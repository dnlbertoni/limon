# Quickstart — Backend

Instrucciones específicas para el backend.

1) Prerequisitos
- Python 3.11+
- PostgreSQL en ejecución (local o via Docker)

2) Instalación y entorno virtual

```bash
cd backend
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

3) Configurar variables de entorno

- Copiar `backend/.env.example` a `backend/.env` y ajustar valores (DATABASE_URL, SECRET_KEY, etc.).

4) Inicializar base de datos

- Recomendada (dentro del contenedor Docker):

```bash
docker-compose -f docker-compose.yml exec backend python backend/scripts/init_db.py
```

- Alternativa local (si PostgreSQL está accesible desde host):

```bash
python backend/scripts/init_db.py
```

5) Migraciones (Alembic)

```bash
# Crear revisión
alembic revision --autogenerate -m "mensaje"
# Aplicar
alembic upgrade head
```

6) Ejecutar servidor en desarrollo

```bash
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

7) Notas
- No comitear secretos. Usa `backend/.env.example` como plantilla.
- Para pruebas de impresora fiscal, monta `/var/www/fiscal` o usa un simulador.
