import uuid
from typing import List, Dict, Any, Optional
from langchain_openai import ChatOpenAI
from langchain.prompts import ChatPromptTemplate, MessagesPlaceholder
from langchain.schema import HumanMessage, AIMessage, SystemMessage
from langchain.memory import ConversationBufferWindowMemory
from langchain.chains import ConversationalRetrievalChain
from langchain.retrievers import ContextualCompressionRetriever
from langchain.retrievers.document_compressors import LLMChainExtractor

from config import settings
from app.knowledge_base import KnowledgeBaseManager
from app.database import DatabaseManager


class CustomerSupportChatbot:
    """Main chatbot class with RAG capabilities."""
    
    def __init__(self):
        # Initialize LLM
        self.llm = ChatOpenAI(
            openai_api_key=settings.openai_api_key,
            model_name=settings.model_name,
            temperature=settings.temperature,
            max_tokens=settings.max_tokens
        )
        
        # Initialize knowledge base
        self.kb_manager = KnowledgeBaseManager()
        
        # Initialize conversation memory
        self.memory = ConversationBufferWindowMemory(
            k=10,  # Keep last 10 exchanges
            return_messages=True,
            memory_key="chat_history"
        )
        
        # Create system prompt
        self.system_prompt = """You are the AgriAI Agriculture Expert assistant. 
        Your role is to help farmers and agricultural traders with their questions about crop management, soil health, pest control, irrigation, government schemes, and market prices.
        
        Guidelines:
        1. Always be helpful, expert-level, and supportive of farmers' needs.
        2. Use the provided knowledge base to give accurate agricultural advice.
        3. If you don't know something, say so and suggest consulting a local agriculture officer or KVK (Krishi Vigyan Kendra).
        4. Keep responses informative, encouraging sustainable and modern farming practices.
        5. Provide step-by-step guidance for complex farming tasks.
        
        Use the following context to answer the user's question:"""
        
        # Create prompt template
        self.prompt_template = ChatPromptTemplate.from_messages([
            ("system", self.system_prompt + "\n\nContext:\n{context}\n\n"),
            MessagesPlaceholder(variable_name="chat_history"),
            ("human", "{question}")
        ])
        
        # Create retrieval chain
        self.retrieval_chain = ConversationalRetrievalChain.from_llm(
            llm=self.llm,
            retriever=self.kb_manager.vectorstore.as_retriever(
                search_type="similarity",
                search_kwargs={"k": 3}
            ),
            memory=self.memory,
            return_source_documents=True,
            verbose=settings.debug
        )
    
    def get_response(self, user_message: str, session_id: str, db_manager: DatabaseManager) -> Dict[str, Any]:
        """Get response from the chatbot."""
        try:
            # Get or create conversation
            conversation = db_manager.get_conversation(session_id)
            if not conversation:
                conversation = db_manager.create_conversation(session_id)
            
            # Add user message to database
            db_manager.add_message(conversation.id, "user", user_message)
            
            # Get response from LLM
            result = self.retrieval_chain({"question": user_message})
            
            response_text = result["answer"]
            source_documents = result.get("source_documents", [])
            
            # Add assistant response to database
            db_manager.add_message(conversation.id, "assistant", response_text)
            
            # Format sources
            sources = []
            for doc in source_documents:
                sources.append({
                    "title": doc.metadata.get("title", "Unknown"),
                    "category": doc.metadata.get("category", "Unknown"),
                    "content": doc.page_content[:200] + "..." if len(doc.page_content) > 200 else doc.page_content
                })
            
            # Calculate confidence based on source relevance
            confidence = self._calculate_confidence(source_documents, user_message)
            
            return {
                "response": response_text,
                "session_id": session_id,
                "conversation_id": conversation.id,
                "sources": sources,
                "confidence": confidence
            }
            
        except Exception as e:
            print(f"Error in chatbot response: {e}")
            return {
                "response": "I apologize, but I'm experiencing technical difficulties. Please try again or contact our support team.",
                "session_id": session_id,
                "conversation_id": "",
                "sources": [],
                "confidence": 0.0
            }
    
    def _calculate_confidence(self, source_documents: List, user_message: str) -> float:
        """Calculate confidence score based on source relevance."""
        if not source_documents:
            return 0.3  # Low confidence if no sources found
        
        # Simple confidence calculation based on number and relevance of sources
        base_confidence = 0.6
        
        # Increase confidence with more relevant sources
        source_bonus = min(len(source_documents) * 0.1, 0.3)
        
        # Check if sources contain relevant keywords
        user_keywords = set(user_message.lower().split())
        relevant_sources = 0
        
        for doc in source_documents:
            doc_content = doc.page_content.lower()
            doc_keywords = set(doc_content.split())
            if user_keywords.intersection(doc_keywords):
                relevant_sources += 1
        
        relevance_bonus = min(relevant_sources * 0.05, 0.1)
        
        return min(base_confidence + source_bonus + relevance_bonus, 1.0)
    
    def get_conversation_history(self, session_id: str, db_manager: DatabaseManager) -> List[Dict[str, Any]]:
        """Get conversation history for a session."""
        conversation = db_manager.get_conversation(session_id)
        if not conversation:
            return []
        
        messages = db_manager.get_conversation_messages(conversation.id)
        
        history = []
        for message in messages:
            history.append({
                "role": message.role,
                "content": message.content,
                "timestamp": message.timestamp.isoformat()
            })
        
        return history
    
    def clear_conversation(self, session_id: str):
        """Clear conversation memory for a session."""
        self.memory.clear()
    
    def add_knowledge_item(self, title: str, content: str, category: str, tags: List[str] = None) -> str:
        """Add a new item to the knowledge base."""
        return self.kb_manager.add_document(title, content, category, tags)
    
    def search_knowledge_base(self, query: str, k: int = 5, category: Optional[str] = None) -> List[Dict[str, Any]]:
        """Search the knowledge base."""
        return self.kb_manager.search(query, k, category)


# Global chatbot instance
chatbot = CustomerSupportChatbot() 