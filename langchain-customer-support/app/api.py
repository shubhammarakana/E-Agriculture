import uuid
from typing import List, Optional
from fastapi import FastAPI, Depends, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware
from sqlalchemy.orm import Session

from app.models import (
    ChatRequest, ChatResponse, ConversationHistory, 
    KnowledgeBaseItem, HealthCheck
)
from app.database import get_db, DatabaseManager, init_db
from app.chatbot import chatbot
from app.knowledge_base import initialize_knowledge_base
from config import settings

# Create FastAPI app
app = FastAPI(
    title="Customer Support Chatbot API",
    description="A LangChain-powered customer support chatbot with RAG capabilities",
    version="1.0.0"
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure appropriately for production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.on_event("startup")
async def startup_event():
    """Initialize database and knowledge base on startup."""
    print("Initializing application...")
    init_db()
    initialize_knowledge_base()
    print("Application initialized successfully!")


@app.get("/", response_model=HealthCheck)
async def root():
    """Health check endpoint."""
    return HealthCheck()


@app.get("/health", response_model=HealthCheck)
async def health_check():
    """Health check endpoint."""
    return HealthCheck()


@app.post("/chat", response_model=ChatResponse)
async def chat(
    request: ChatRequest,
    db: Session = Depends(get_db)
):
    """Main chat endpoint."""
    try:
        # Generate session ID if not provided
        session_id = request.session_id or str(uuid.uuid4())
        
        # Create database manager
        db_manager = DatabaseManager(db)
        
        # Get response from chatbot
        result = chatbot.get_response(
            user_message=request.message,
            session_id=session_id,
            db_manager=db_manager
        )
        
        return ChatResponse(
            response=result["response"],
            session_id=result["session_id"],
            conversation_id=result["conversation_id"],
            sources=result["sources"],
            confidence=result["confidence"]
        )
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error processing chat request: {str(e)}"
        )


@app.get("/conversation/{session_id}", response_model=ConversationHistory)
async def get_conversation_history(
    session_id: str,
    db: Session = Depends(get_db)
):
    """Get conversation history for a session."""
    try:
        db_manager = DatabaseManager(db)
        conversation = db_manager.get_conversation(session_id)
        
        if not conversation:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Conversation not found"
            )
        
        messages = db_manager.get_conversation_messages(conversation.id)
        
        # Convert to ChatMessage format
        chat_messages = []
        for message in messages:
            chat_messages.append({
                "role": message.role,
                "content": message.content,
                "timestamp": message.timestamp
            })
        
        return ConversationHistory(
            conversation_id=conversation.id,
            session_id=conversation.session_id,
            user_id=conversation.user_id,
            messages=chat_messages,
            created_at=conversation.created_at,
            updated_at=conversation.updated_at
        )
        
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error retrieving conversation: {str(e)}"
        )


@app.delete("/conversation/{session_id}")
async def clear_conversation(session_id: str):
    """Clear conversation memory for a session."""
    try:
        chatbot.clear_conversation(session_id)
        return {"message": "Conversation cleared successfully"}
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error clearing conversation: {str(e)}"
        )


@app.post("/knowledge", response_model=KnowledgeBaseItem)
async def add_knowledge_item(
    title: str,
    content: str,
    category: str,
    tags: Optional[List[str]] = None,
    db: Session = Depends(get_db)
):
    """Add a new item to the knowledge base."""
    try:
        # Add to vector database
        doc_id = chatbot.add_knowledge_item(title, content, category, tags or [])
        
        # Add to SQL database
        db_manager = DatabaseManager(db)
        kb_item = db_manager.add_knowledge_item(title, content, category, tags or [])
        
        return KnowledgeBaseItem(
            id=kb_item.id,
            title=kb_item.title,
            content=kb_item.content,
            category=kb_item.category,
            tags=kb_item.tags.split(",") if kb_item.tags else [],
            created_at=kb_item.created_at,
            updated_at=kb_item.updated_at
        )
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error adding knowledge item: {str(e)}"
        )


@app.get("/knowledge", response_model=List[KnowledgeBaseItem])
async def get_knowledge_items(
    category: Optional[str] = None,
    db: Session = Depends(get_db)
):
    """Get knowledge base items."""
    try:
        db_manager = DatabaseManager(db)
        items = db_manager.get_knowledge_items(category)
        
        result = []
        for item in items:
            result.append(KnowledgeBaseItem(
                id=item.id,
                title=item.title,
                content=item.content,
                category=item.category,
                tags=item.tags.split(",") if item.tags else [],
                created_at=item.created_at,
                updated_at=item.updated_at
            ))
        
        return result
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error retrieving knowledge items: {str(e)}"
        )


@app.get("/search")
async def search_knowledge_base(
    query: str,
    k: int = 5,
    category: Optional[str] = None
):
    """Search the knowledge base."""
    try:
        results = chatbot.search_knowledge_base(query, k, category)
        return {
            "query": query,
            "results": results,
            "count": len(results)
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error searching knowledge base: {str(e)}"
        )


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.api:app",
        host=settings.host,
        port=settings.port,
        reload=settings.debug
    ) 