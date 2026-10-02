from sqlalchemy import create_engine, Column, String, DateTime, Text, Integer
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, Session
from datetime import datetime
import uuid
from typing import List, Optional

from config import settings

# Create database engine
engine = create_engine(settings.database_url, echo=settings.debug)

# Create session factory
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# Create base class for models
Base = declarative_base()


class Conversation(Base):
    """Database model for conversations."""
    __tablename__ = "conversations"
    
    id = Column(String, primary_key=True, default=lambda: str(uuid.uuid4()))
    session_id = Column(String, nullable=False, index=True)
    user_id = Column(String, nullable=True, index=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)


class Message(Base):
    """Database model for messages."""
    __tablename__ = "messages"
    
    id = Column(String, primary_key=True, default=lambda: str(uuid.uuid4()))
    conversation_id = Column(String, nullable=False, index=True)
    role = Column(String, nullable=False)  # 'user' or 'assistant'
    content = Column(Text, nullable=False)
    timestamp = Column(DateTime, default=datetime.utcnow)


class KnowledgeBase(Base):
    """Database model for knowledge base items."""
    __tablename__ = "knowledge_base"
    
    id = Column(String, primary_key=True, default=lambda: str(uuid.uuid4()))
    title = Column(String, nullable=False)
    content = Column(Text, nullable=False)
    category = Column(String, nullable=False)
    tags = Column(Text, nullable=True)  # JSON string of tags
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)


def get_db() -> Session:
    """Get database session."""
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()


def init_db():
    """Initialize database tables."""
    Base.metadata.create_all(bind=engine)


class DatabaseManager:
    """Database manager for CRUD operations."""
    
    def __init__(self, db: Session):
        self.db = db
    
    def create_conversation(self, session_id: str, user_id: Optional[str] = None) -> Conversation:
        """Create a new conversation."""
        conversation = Conversation(session_id=session_id, user_id=user_id)
        self.db.add(conversation)
        self.db.commit()
        self.db.refresh(conversation)
        return conversation
    
    def get_conversation(self, session_id: str) -> Optional[Conversation]:
        """Get conversation by session ID."""
        return self.db.query(Conversation).filter(Conversation.session_id == session_id).first()
    
    def add_message(self, conversation_id: str, role: str, content: str) -> Message:
        """Add a message to a conversation."""
        message = Message(conversation_id=conversation_id, role=role, content=content)
        self.db.add(message)
        self.db.commit()
        self.db.refresh(message)
        return message
    
    def get_conversation_messages(self, conversation_id: str) -> List[Message]:
        """Get all messages for a conversation."""
        return self.db.query(Message).filter(Message.conversation_id == conversation_id).order_by(Message.timestamp).all()
    
    def add_knowledge_item(self, title: str, content: str, category: str, tags: List[str] = None) -> KnowledgeBase:
        """Add a knowledge base item."""
        tags_json = ",".join(tags) if tags else ""
        item = KnowledgeBase(title=title, content=content, category=category, tags=tags_json)
        self.db.add(item)
        self.db.commit()
        self.db.refresh(item)
        return item
    
    def get_knowledge_items(self, category: Optional[str] = None) -> List[KnowledgeBase]:
        """Get knowledge base items, optionally filtered by category."""
        query = self.db.query(KnowledgeBase)
        if category:
            query = query.filter(KnowledgeBase.category == category)
        return query.all() 