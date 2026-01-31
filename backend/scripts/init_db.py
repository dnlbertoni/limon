#!/usr/bin/env python3
"""Inicializa la base de datos y crea el usuario admin por defecto.

Ejecución recomendada dentro del contenedor:
  docker-compose -f docker-compose.yml exec backend python backend/scripts/init_db.py
"""
from app.core.database import engine, Base, SessionLocal
from app.models.user import User
from app.models.article import Article, Brand, Category
from app.models.invoice import Invoice, InvoiceType, InvoiceItem, Customer
from app.core.security import get_password_hash


def init_db():
    # Crear tablas según modelos
    Base.metadata.create_all(bind=engine)

    # Crear usuario admin si no existe
    db = SessionLocal()
    try:
        existing = db.query(User).filter_by(username="admin").first()
        if not existing:
            admin = User(
                username="admin",
                email="admin@limon.com",
                hashed_password=get_password_hash("admin123"),
                full_name="Administrator",
                is_superuser=True,
            )
            db.add(admin)
            db.commit()
            print("✓ Admin user created: username=admin password=admin123")
        else:
            print("✓ Admin user already exists")
    finally:
        db.close()

    print("✓ Database initialized")


if __name__ == "__main__":
    init_db()
