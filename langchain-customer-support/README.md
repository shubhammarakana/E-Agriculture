# Customer Support Chatbot

A powerful, AI-powered customer support chatbot built with Python, LangChain, and FastAPI. This chatbot uses Retrieval-Augmented Generation (RAG) to provide accurate, context-aware responses based on a knowledge base.

## 🚀 Features

- **AI-Powered Responses**: Uses OpenAI's GPT models for intelligent conversation
- **Knowledge Base Integration**: RAG system with ChromaDB vector database
- **Conversation Memory**: Maintains context across chat sessions
- **Web Interface**: Modern, responsive chat UI
- **REST API**: Full API for integration with other systems
- **Session Management**: Persistent conversation history
- **Source Attribution**: Shows sources used for responses
- **Confidence Scoring**: Indicates response confidence levels
- **Demo Mode**: Works without OpenAI API key using pattern matching

## 🏗️ Architecture

```
├── app/
│   ├── __init__.py
│   ├── api.py              # FastAPI endpoints
│   ├── chatbot.py          # Core chatbot logic
│   ├── database.py         # Database models and operations
│   ├── knowledge_base.py   # Knowledge base management
│   └── models.py           # Pydantic models
├── static/
│   └── index.html          # Web interface
├── config.py               # Configuration settings
├── main.py                 # Application entry point
├── demo_chatbot.py         # Demo version (no API key required)
├── requirements.txt        # Python dependencies
└── env.example             # Environment variables template
```

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone <https://github.com/ozgucdlg/langchain-customer-support.git>
   cd langchain-customer-support
   ```

2. **Install dependencies**
   ```bash
   pip install -r requirements.txt --user
   ```

3. **Set up environment variables (optional for demo)**
   ```bash
   cp env.example .env
   # Edit .env and add your OpenAI API key for full functionality
   ```

4. **Initialize the database**
   ```bash
   python init_db.py
   ```

## 🚀 Quick Start

### Demo Mode (No API Key Required)

For testing and demonstration purposes, use the demo version:

```bash
python demo_chatbot.py
```

This will start the chatbot with pattern matching responses for common customer support queries.

### Full Mode (Requires OpenAI API Key)

For full AI-powered responses:

```bash
python main.py
```

## ⚠️ Important Notes

### Virtual Environment Issue

**Problem**: The project includes a `.venv` directory that may not have all dependencies installed.

**Solution**: Use the global Python installation instead of activating the virtual environment:
```bash
# ❌ Don't do this (will cause ModuleNotFoundError)
& .venv/Scripts/Activate.ps1
python demo_chatbot.py

# ✅ Do this instead
python demo_chatbot.py
```

### API Endpoint Configuration

The web interface is configured to call `/api/chat` endpoint. If you encounter "Sorry, I encountered an error" messages, ensure the frontend is calling the correct endpoint.

## ⚙️ Configuration

Create a `.env` file with the following variables:

```env
# OpenAI API Configuration (optional for demo mode)
OPENAI_API_KEY=your_openai_api_key_here

# Database Configuration
DATABASE_URL=sqlite:///./customer_support.db

# Vector Database Configuration
CHROMA_DB_PATH=./chroma_db

# Application Configuration
DEBUG=True
HOST=0.0.0.0
PORT=8000

# Model Configuration
MODEL_NAME=gpt-3.5-turbo
TEMPERATURE=0.7
MAX_TOKENS=1000
```


### Demo Mode Features

The demo version supports these topics:
- **Password Reset**: "How do I reset my password?"
- **Return Policy**: "What is your return policy?"
- **Payment Methods**: "What payment methods do you accept?"
- **Shipping Information**: "How long does shipping take?"

### API Endpoints

- **POST** `/api/chat` - Send a message and get response
- **GET** `/api/conversation/{session_id}` - Get conversation history
- **DELETE** `/api/conversation/{session_id}` - Clear conversation
- **POST** `/api/knowledge` - Add knowledge base item
- **GET** `/api/knowledge` - Get knowledge base items
- **GET** `/api/search` - Search knowledge base
- **GET** `/health` - Health check endpoint



### Testing the API

```bash
# Test health endpoint
curl http://localhost:8000/health

# Test chat endpoint
curl -X POST "http://localhost:8000/api/chat" \
  -H "Content-Type: application/json" \
  -d '{"message": "Hello", "session_id": "test123"}'
```

## 📚 Knowledge Base

The chatbot comes with a pre-loaded knowledge base covering:

- **Account Management**: Password reset, security, account settings
- **Returns & Refunds**: Return policy, refund process
- **Payment Methods**: Accepted payment options
- **Shipping Information**: Delivery times, shipping options
- **Security**: Account protection, data privacy

### Adding Custom Knowledge

```python
# Via API
requests.post("http://localhost:8000/api/knowledge", json={
    "title": "Custom FAQ",
    "content": "Your custom content here...",
    "category": "custom",
    "tags": ["custom", "faq"]
})
```
