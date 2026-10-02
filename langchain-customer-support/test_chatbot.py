#!/usr/bin/env python3
"""
Simple test script for the customer support chatbot.
This will test the basic functionality without requiring all dependencies.
"""

import os
import sys
from datetime import datetime

# Add the current directory to Python path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

def test_knowledge_base():
    """Test the knowledge base functionality."""
    print("🧪 Testing Knowledge Base...")
    
    try:
        from app.knowledge_base import KnowledgeBaseManager
        
        kb = KnowledgeBaseManager()
        
        # Test search functionality
        results = kb.search("password reset", k=3)
        print(f"✅ Knowledge base search returned {len(results)} results")
        
        for i, result in enumerate(results):
            print(f"  {i+1}. {result['metadata']['title']} (Score: {result['score']:.2f})")
        
        return True
        
    except Exception as e:
        print(f"❌ Knowledge base test failed: {e}")
        return False

def test_models():
    """Test the Pydantic models."""
    print("\n🧪 Testing Models...")
    
    try:
        from app.models import ChatRequest, ChatResponse
        
        # Test ChatRequest
        request = ChatRequest(
            message="How do I reset my password?",
            session_id="test123"
        )
        print(f"✅ ChatRequest created: {request.message}")
        
        # Test ChatResponse
        response = ChatResponse(
            response="Here's how to reset your password...",
            session_id="test123",
            conversation_id="conv123",
            confidence=0.85
        )
        print(f"✅ ChatResponse created: {response.response[:50]}...")
        
        return True
        
    except Exception as e:
        print(f"❌ Models test failed: {e}")
        return False

def test_database():
    """Test the database functionality."""
    print("\n🧪 Testing Database...")
    
    try:
        from app.database import DatabaseManager, SessionLocal
        
        # Create a database session
        db = SessionLocal()
        db_manager = DatabaseManager(db)
        
        # Test conversation creation
        conversation = db_manager.create_conversation("test_session", "test_user")
        print(f"✅ Conversation created: {conversation.id}")
        
        # Test message addition
        message = db_manager.add_message(conversation.id, "user", "Hello")
        print(f"✅ Message added: {message.id}")
        
        # Clean up
        db.close()
        
        return True
        
    except Exception as e:
        print(f"❌ Database test failed: {e}")
        return False

def test_config():
    """Test the configuration."""
    print("\n🧪 Testing Configuration...")
    
    try:
        from config import settings
        
        print(f"✅ Configuration loaded:")
        print(f"  - Host: {settings.host}")
        print(f"  - Port: {settings.port}")
        print(f"  - Debug: {settings.debug}")
        print(f"  - Model: {settings.model_name}")
        
        return True
        
    except Exception as e:
        print(f"❌ Configuration test failed: {e}")
        return False

def main():
    """Run all tests."""
    print("🚀 Starting Customer Support Chatbot Tests")
    print("=" * 50)
    
    tests = [
        ("Configuration", test_config),
        ("Models", test_models),
        ("Knowledge Base", test_knowledge_base),
        ("Database", test_database),
    ]
    
    passed = 0
    total = len(tests)
    
    for test_name, test_func in tests:
        try:
            if test_func():
                passed += 1
        except Exception as e:
            print(f"❌ {test_name} test crashed: {e}")
    
    print("\n" + "=" * 50)
    print(f"📊 Test Results: {passed}/{total} tests passed")
    
    if passed == total:
        print("🎉 All tests passed! The chatbot is ready to run.")
        print("\nTo start the chatbot:")
        print("1. Set your OpenAI API key in a .env file")
        print("2. Run: python main.py")
        print("3. Open: http://localhost:8000")
    else:
        print("⚠️  Some tests failed. Please check the errors above.")
    
    return passed == total

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1) 