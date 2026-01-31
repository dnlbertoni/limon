from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.core.config import settings
from app.middleware.security import SecurityHeadersMiddleware, LoggingMiddleware, RateLimitMiddleware
from app.api.endpoints import auth, articles, invoices, hasar
from fastapi import APIRouter

# Create FastAPI app
app = FastAPI(
    title=settings.APP_NAME,
    description="Modern ERP system with FastAPI, React, and PostgreSQL",
    version="2.0.0",
    docs_url="/api/docs",
    redoc_url="/api/redoc"
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=[str(origin).rstrip("/") for origin in settings.BACKEND_CORS_ORIGINS],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Add security middleware
app.add_middleware(SecurityHeadersMiddleware)
app.add_middleware(LoggingMiddleware)
app.add_middleware(RateLimitMiddleware, calls=100, period=60)

# Create API router
api_router = APIRouter(prefix="/api")

# Include routers
api_router.include_router(auth.router, prefix="/auth", tags=["Authentication"])
api_router.include_router(articles.router, prefix="/articles", tags=["Articles"])
api_router.include_router(invoices.router, prefix="/invoices", tags=["Invoices"])
api_router.include_router(hasar.router, prefix="/hasar", tags=["Hasar Fiscal Printer"])

app.include_router(api_router)


@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": "Limon ERP API",
        "version": "2.0.0",
        "docs": "/api/docs"
    }


@app.get("/health")
async def health():
    """Health check endpoint"""
    return {"status": "healthy"}


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
