import uvicorn
from fastapi import FastAPI
from fastapi.staticfiles import StaticFiles
from fastapi.responses import FileResponse
import os

from app.api import app as api_app
from config import settings

# Create main app
app = FastAPI(
    title="Customer Support Chatbot",
    description="A complete customer support chatbot with web interface and API",
    version="1.0.0"
)

# Mount the API
app.mount("/api", api_app)

# Mount static files
app.mount("/static", StaticFiles(directory="static"), name="static")


@app.get("/")
async def read_index():
    """Serve the main chat interface."""
    return FileResponse("static/index.html")


@app.get("/health")
async def health_check():
    """Health check endpoint."""
    return {"status": "healthy", "version": "1.0.0"}


if __name__ == "__main__":
    print("🚀 Starting Customer Support Chatbot...")
    print(f"📱 Web Interface: http://{settings.host}:{settings.port}")
    print(f"🔌 API Documentation: http://{settings.host}:{settings.port}/api/docs")
    print(f"📊 API Health Check: http://{settings.host}:{settings.port}/api/health")
    print("=" * 50)
    
    uvicorn.run(
        "main:app",
        host=settings.host,
        port=settings.port,
        reload=settings.debug,
        log_level="info"
    ) 