import os
import json
from typing import List, Dict, Any, Optional
from langchain.text_splitter import RecursiveCharacterTextSplitter
from langchain_openai import OpenAIEmbeddings
from langchain.schema import Document

from config import settings


class SimpleKnowledgeBase:
    """Simple knowledge base for testing without vector database."""
    
    def __init__(self):
        self.knowledge_items = []
        self.initialize_from_json()
    
    def initialize_from_json(self):
        """Initialize knowledge base from kb_data.json."""
        kb_path = os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(__file__))), 'kb_data.json')
        
        if not os.path.exists(kb_path):
            print(f"Warning: Knowledge base file not found at {kb_path}")
            return

        try:
            with open(kb_path, 'r', encoding='utf-8') as f:
                data = json.load(f)
                for item in data:
                    self.knowledge_items.append({
                        "title": item.get("question", ""),
                        "content": item.get("answer", ""),
                        "category": item.get("category", "General"),
                        "tags": item.get("keywords", [])
                    })
            print(f"Loaded {len(self.knowledge_items)} knowledge items from {kb_path}")
        except Exception as e:
            print(f"Error loading knowledge base: {e}")
    
    def search(self, query: str, k: int = 5, category: Optional[str] = None) -> List[Dict[str, Any]]:
        """Simple keyword-based search."""
        query_lower = query.lower()
        results = []
        
        for item in self.knowledge_items:
            if category and item["category"] != category:
                continue
                
            # Simple keyword matching
            content_lower = item["content"].lower()
            title_lower = item["title"].lower()
            
            # Check if query keywords are in content or title
            query_words = query_lower.split()
            matches = 0
            
            for word in query_words:
                if word in content_lower or word in title_lower:
                    matches += 1
            
            if matches > 0:
                # Calculate simple relevance score
                relevance = matches / len(query_words)
                results.append({
                    "content": item["content"][:500] + "..." if len(item["content"]) > 500 else item["content"],
                    "metadata": {
                        "title": item["title"],
                        "category": item["category"],
                        "tags": item["tags"]
                    },
                    "score": relevance
                })
        
        # Sort by relevance and return top k
        results.sort(key=lambda x: x["score"], reverse=True)
        return results[:k]
    
    def add_document(self, title: str, content: str, category: str, tags: List[str] = None) -> str:
        """Add a document to the knowledge base."""
        doc_id = f"doc_{len(self.knowledge_items)}"
        self.knowledge_items.append({
            "id": doc_id,
            "title": title,
            "content": content,
            "category": category,
            "tags": tags or []
        })
        return doc_id
    
    def get_all_documents(self) -> List[Dict[str, Any]]:
        """Get all documents from the knowledge base."""
        return self.knowledge_items


class KnowledgeBaseManager:
    """Knowledge base manager that uses simple search for now."""
    
    def __init__(self):
        self.simple_kb = SimpleKnowledgeBase()
        # Create a mock vectorstore for compatibility
        self.vectorstore = MockVectorStore(self.simple_kb)
    
    def add_document(self, title: str, content: str, category: str, tags: List[str] = None) -> str:
        """Add a document to the knowledge base."""
        return self.simple_kb.add_document(title, content, category, tags)
    
    def search(self, query: str, k: int = 5, category: Optional[str] = None) -> List[Dict[str, Any]]:
        """Search the knowledge base."""
        return self.simple_kb.search(query, k, category)
    
    def get_all_documents(self) -> List[Dict[str, Any]]:
        """Get all documents from the knowledge base."""
        return self.simple_kb.get_all_documents()


class MockVectorStore:
    """Mock vector store for compatibility with LangChain."""
    
    def __init__(self, knowledge_base):
        self.knowledge_base = knowledge_base
    
    def as_retriever(self, search_type="similarity", search_kwargs=None):
        return MockRetriever(self.knowledge_base)


class MockRetriever:
    """Mock retriever that uses simple search."""
    
    def __init__(self, knowledge_base):
        self.knowledge_base = knowledge_base
    
    def get_relevant_documents(self, query: str):
        results = self.knowledge_base.search(query, k=3)
        documents = []
        
        for result in results:
            doc = Document(
                page_content=result["content"],
                metadata=result["metadata"]
            )
            documents.append(doc)
        
        return documents


def initialize_knowledge_base():
    """Initialize the knowledge base."""
    kb_manager = KnowledgeBaseManager()
    print("Knowledge base initialized successfully!")
    return kb_manager 