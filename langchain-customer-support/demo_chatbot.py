#!/usr/bin/env python3
"""
Demo version of the customer support chatbot that works without OpenAI API key.
This version uses simple pattern matching for responses.
"""

import uuid
from typing import List, Dict, Any, Optional
from fastapi import FastAPI, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from fastapi.responses import FileResponse
import uvicorn

from app.models import ChatRequest, ChatResponse, HealthCheck
from app.database import get_db, DatabaseManager, init_db
from fastapi import Depends
from app.knowledge_base import KnowledgeBaseManager
from config import settings

# Create FastAPI app
app = FastAPI(
    title="Customer Support Chatbot Demo",
    description="A demo version of the customer support chatbot",
    version="1.0.0"
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Mount static files
app.mount("/static", StaticFiles(directory="static"), name="static")


class DemoChatbot:
    """Demo chatbot that uses pattern matching instead of OpenAI."""
    
    def __init__(self):
        self.kb_manager = KnowledgeBaseManager()
        
        # Simple response patterns
        self.patterns = {
            "password": [
                "To reset your password, go to the login page and click 'Forgot Password'. Enter your email and check for a reset link.",
                "You can reset your password by visiting our login page and clicking the 'Forgot Password' link."
            ],
            "return": [
                "Our return policy allows returns within 30 days. Items must be in original condition with packaging.",
                "You can return items within 30 days of purchase. Please ensure items are in original condition."
            ],
            "shipping": [
                "Standard shipping takes 3-5 business days and is free for orders over $50.",
                "We offer standard shipping (3-5 days), express shipping (1-2 days), and overnight options."
            ],
            "payment": [
                "We accept Visa, Mastercard, American Express, PayPal, Apple Pay, and Google Pay.",
                "You can pay with major credit cards, digital wallets like PayPal and Apple Pay, or bank transfers for business accounts."
            ],
            "hello": [
                "Hello! How can I help you today? I can assist with password resets, returns, shipping, and payment questions.",
                "Hi there! I'm here to help with your customer service needs. What can I assist you with?"
            ],
            "help": [
                "I can help you with password resets, return policies, shipping information, payment methods, and account security.",
                "I'm here to assist with various customer service topics. Just ask me about passwords, returns, shipping, or payments!"
            ]
        }
    
    def get_response(self, user_message: str, session_id: str, db_manager: DatabaseManager) -> Dict[str, Any]:
        """Get response using pattern matching."""
        try:
            # Get or create conversation
            conversation = db_manager.get_conversation(session_id)
            if not conversation:
                conversation = db_manager.create_conversation(session_id)
            
            # Add user message to database
            db_manager.add_message(conversation.id, "user", user_message)
            
            # Search knowledge base
            kb_results = self.kb_manager.search(user_message, k=2)
            
            # Find matching pattern
            user_lower = user_message.lower()
            response = None
            confidence = 0.5
            
            for pattern, responses in self.patterns.items():
                if pattern in user_lower:
                    import random
                    response = random.choice(responses)
                    confidence = 0.8
                    break
            
            # If no pattern match, use knowledge base
            if not response and kb_results:
                response = kb_results[0]["content"][:200] + "..."
                confidence = kb_results[0]["score"]
            elif not response:
                response = "I'm sorry, I don't have specific information about that. Please contact our support team for assistance."
                confidence = 0.3
            
            # Add assistant response to database
            db_manager.add_message(conversation.id, "assistant", response)
            
            # Format sources
            sources = []
            for result in kb_results[:2]:
                sources.append({
                    "title": result["metadata"]["title"],
                    "category": result["metadata"]["category"],
                    "content": result["content"][:200] + "..." if len(result["content"]) > 200 else result["content"]
                })
            
            return {
                "response": response,
                "session_id": session_id,
                "conversation_id": conversation.id,
                "sources": sources,
                "confidence": confidence
            }
            
        except Exception as e:
            print(f"Error in demo chatbot: {e}")
            return {
                "response": "I apologize, but I'm experiencing technical difficulties. Please try again.",
                "session_id": session_id,
                "conversation_id": "",
                "sources": [],
                "confidence": 0.0
            }


# Global demo chatbot instance
demo_chatbot = DemoChatbot()


@app.on_event("startup")
async def startup_event():
    """Initialize database on startup."""
    print("🚀 Starting Demo Customer Support Chatbot...")
    init_db()
    print("✅ Demo chatbot initialized successfully!")


@app.get("/", response_model=HealthCheck)
async def root():
    """Health check endpoint."""
    return HealthCheck()


@app.get("/health", response_model=HealthCheck)
async def health_check():
    """Health check endpoint."""
    return HealthCheck()


@app.post("/api/chat", response_model=ChatResponse)
async def chat(request: ChatRequest, db=Depends(get_db)):
    """Main chat endpoint."""
    try:
        # Generate session ID if not provided
        session_id = request.session_id or str(uuid.uuid4())
        
        # Create database manager
        db_manager = DatabaseManager(db)
        
        # Get response from demo chatbot
        result = demo_chatbot.get_response(
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


@app.get("/demo")
async def read_demo():
    """Serve the demo chat interface."""
    return FileResponse("static/index.html")


if __name__ == "__main__":
    print("🎮 Starting Demo Customer Support Chatbot...")
    print(f"📱 Demo Interface: http://{settings.host}:{settings.port}/demo")
    print(f"🔌 API Endpoint: http://{settings.host}:{settings.port}/api/chat")
    print(f"📊 Health Check: http://{settings.host}:{settings.port}/health")
    print("=" * 50)
    print("💡 Try asking about: password reset, returns, shipping, payment methods")
    print("=" * 50)
    
    uvicorn.run(
        "demo_chatbot:app",
        host=settings.host,
        port=settings.port,
        reload=settings.debug,
        log_level="info"
    ) 