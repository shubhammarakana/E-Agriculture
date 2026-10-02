import os
from typing import Optional
from pydantic_settings import BaseSettings


class Settings(BaseSettings):
    """Application settings and configuration."""
    
    # OpenAI Configuration
    openai_api_key: str = ""
    
    # Database Configuration
    database_url: str = "sqlite:///./customer_support.db"
    
    # Vector Database Configuration
    chroma_db_path: str = "./chroma_db"
    
    # Application Configuration
    debug: bool = True
    host: str = "0.0.0.0"
    port: int = 8000
    
    # Optional: Anthropic API
    anthropic_api_key: Optional[str] = None
    
    # Model Configuration
    model_name: str = "gpt-3.5-turbo"
    temperature: float = 0.7
    max_tokens: int = 1000
    
    class Config:
        env_file = ".env"
        case_sensitive = False


# Global settings instance
settings = Settings() 