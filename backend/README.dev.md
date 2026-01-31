# Development README — Backend

Requisitos mínimos:
- Python 3.11+
- PostgreSQL (local o via Docker)
- pip / entorno virtual

Instalación rápida (entorno virtual):

```bash
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

Variables de entorno:
- Copiar `backend/.env.example` a `backend/.env` y ajustar valores.

Ejecutar la aplicación localmente:

```bash
# desde el directorio backend
uvicorn app.main:app --host 0.0.0.0 --port 8000 --reload
```

Con Docker Compose (modo desarrollo):

```bash
docker compose up --build
```

Migraciones (Alembic):

```bash
alembic revision --autogenerate -m "mensaje"
alembic upgrade head
```

Tests y linting:

```bash
pytest
# recomendar: ruff check . && ruff format .
```

Notas:
- No incluir secretos reales en el repositorio. Usa `backend/.env.example` como plantilla.
- Para desarrollo con impresora fiscal, usar montaje de /var/www/fiscal o un simulador.
