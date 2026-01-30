# Limon ERP - Version 2.0

Sistema de gestión ERP moderno con arquitectura backend-frontend-database.

## 🚀 Nueva Arquitectura

### Stack Tecnológico

- **Backend**: Python + FastAPI
- **Frontend**: React + Vite
- **Base de Datos**: PostgreSQL
- **Contenedores**: Docker + Docker Compose

### Características Principales

✅ **Backend FastAPI**
- API RESTful moderna y rápida
- Autenticación JWT segura
- Documentación automática con Swagger/OpenAPI
- Validación de datos con Pydantic
- ORM con SQLAlchemy

✅ **Frontend React**
- Interfaz de usuario moderna y responsiva
- Gestión de estado con Zustand
- React Query para manejo de datos
- React Router para navegación

✅ **Base de Datos PostgreSQL**
- Base de datos relacional robusta
- Migraciones de datos desde MySQL
- Respaldo y recuperación de datos

✅ **Seguridad**
- Autenticación JWT con tokens
- HTTPS/SSL (configurable)
- CORS configurado
- Validación de entrada
- Prevención de SQL Injection
- Rate limiting
- Headers de seguridad

✅ **Integración Hasar Fiscal**
- **Hasar Legacy**: Comunicación por archivos (compatible con versiones antiguas)
- **Hasar 2.0**: Comunicación HTTP/JSON API (versiones modernas)
- Servicio unificado que mantiene la relación entre ambos controladores
- Soporte para ambas versiones simultáneamente

## 📋 Requisitos Previos

- Docker y Docker Compose
- Git
- (Opcional) Python 3.11+ y Node.js 18+ para desarrollo local

## 🛠️ Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/dnlbertoni/limon.git
cd limon
```

### 2. Configurar variables de entorno

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Editar el archivo .env con tus configuraciones
nano .env
```

Variables importantes:
```env
# PostgreSQL
POSTGRES_DATABASE=limon_db
POSTGRES_USER=limon_user
POSTGRES_PASSWORD=your_secure_password

# Backend
SECRET_KEY=your-secret-key-here-change-in-production
BACKEND_CORS_ORIGINS=["http://localhost:3000"]

# Hasar Fiscal Printers
HASAR_LEGACY_PATH=/var/www/fiscal
HASAR_2_HOST=192.168.1.100
HASAR_2_PASSWORD=your_fiscal_printer_password
```

### 3. Iniciar con Docker Compose

```bash
# Construir e iniciar todos los servicios
docker-compose -f docker-compose.new.yml up -d

# Ver logs
docker-compose -f docker-compose.new.yml logs -f
```

### 4. Acceder a la aplicación

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **API Docs**: http://localhost:8000/api/docs
- **PgAdmin**: http://localhost:5050

## 📊 Migración de Datos

Para migrar datos desde la base MySQL existente a PostgreSQL:

```bash
# 1. Configurar variables de MySQL en .env
MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_USER=root
MYSQL_PASSWORD=password
MYSQL_DATABASE=facturador

# 2. Ejecutar script de migración
cd database/scripts
python migrate_mysql_to_postgres.py
```

## 🔐 Seguridad Implementada

### Autenticación y Autorización
- JWT (JSON Web Tokens) para autenticación
- Tokens con expiración configurable
- Refresh tokens para sesiones prolongadas
- Verificación de usuario en cada request

### Protección de API
- Rate limiting (100 requests/minuto por IP)
- CORS configurado para orígenes permitidos
- Validación de entrada con Pydantic
- Headers de seguridad (X-Frame-Options, CSP, etc.)

### Base de Datos
- Conexiones seguras con PostgreSQL
- Prevención de SQL Injection con ORM
- Passwords hasheados con bcrypt
- Migraciones controladas con Alembic

## 📱 Impresoras Fiscales Hasar

### Relación entre Hasar Legacy y Hasar 2.0

El sistema mantiene compatibilidad con ambas versiones de controladores fiscales:

#### Hasar Legacy (Comunicación por Archivos)
```python
# Configuración
config = {
    "version": "legacy",
    "point_of_sale": 1
}

# El servicio usa archivos en /var/www/fiscal/{puesto}/
# - mandar/: Comandos a enviar
# - recibir/: Respuestas de la impresora
# - log/: Logs de operaciones
```

#### Hasar 2.0 (HTTP/JSON API)
```python
# Configuración
config = {
    "version": "2.0",
    "host": "192.168.1.100",
    "password": "fiscal_password"
}

# El servicio usa HTTP POST a http://{host}/fiscal.json
# Con autenticación básica y comandos en JSON
```

#### Servicio Unificado

```python
from app.services.hasar import HasarService

# El servicio se adapta automáticamente según la configuración
printer = HasarService.get_printer_instance(config)

# Los mismos métodos funcionan para ambas versiones
await printer.get_status()
await printer.open_fiscal_receipt(customer_data)
await printer.print_item(description, quantity, price, vat_rate)
await printer.close_fiscal_receipt()
await printer.daily_close("Z")
```

## 🔧 Desarrollo Local

### Backend

```bash
cd backend

# Crear entorno virtual
python -m venv venv
source venv/bin/activate  # En Windows: venv\Scripts\activate

# Instalar dependencias
pip install -r requirements.txt

# Ejecutar servidor de desarrollo
uvicorn app.main:app --reload
```

### Frontend

```bash
cd frontend

# Instalar dependencias
npm install

# Ejecutar servidor de desarrollo
npm run dev
```

## 📚 Estructura del Proyecto

```
limon/
├── backend/                 # Backend FastAPI
│   ├── app/
│   │   ├── api/            # Endpoints de la API
│   │   ├── core/           # Configuración y seguridad
│   │   ├── models/         # Modelos de base de datos
│   │   ├── schemas/        # Esquemas Pydantic
│   │   ├── services/       # Lógica de negocio
│   │   │   ├── hasar.py           # Servicio unificado
│   │   │   ├── hasar_legacy.py   # Controlador legacy
│   │   │   └── hasar2.py          # Controlador 2.0
│   │   └── middleware/     # Middleware de seguridad
│   ├── requirements.txt
│   └── Dockerfile
├── frontend/               # Frontend React
│   ├── src/
│   │   ├── components/    # Componentes reutilizables
│   │   ├── pages/         # Páginas de la aplicación
│   │   ├── services/      # Servicios API
│   │   └── stores/        # Gestión de estado
│   ├── package.json
│   └── Dockerfile
├── database/              # Scripts de base de datos
│   ├── migrations/        # Migraciones SQL
│   └── scripts/          # Scripts de migración
├── docker-compose.new.yml # Configuración Docker
└── README.md
```

## 🧪 Testing

```bash
# Backend tests
cd backend
pytest

# Frontend tests
cd frontend
npm test
```

## 📖 API Documentation

Una vez iniciado el backend, la documentación interactiva está disponible en:

- Swagger UI: http://localhost:8000/api/docs
- ReDoc: http://localhost:8000/api/redoc

## 🤝 Contribuir

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Licencia

Ver archivo `LICENSE` para más detalles.

## 👥 Autores

- Sistema Legacy: PHP/CodeIgniter
- Sistema v2.0: Python FastAPI + React

## 🆘 Soporte

Para reportar problemas o solicitar ayuda, abrir un issue en GitHub.

## 🔄 Changelog

### Version 2.0.0 (2024)
- ✅ Migración completa a FastAPI + React + PostgreSQL
- ✅ Implementación de seguridad completa (JWT, HTTPS, rate limiting)
- ✅ Servicio unificado para Hasar Legacy y Hasar 2.0
- ✅ Dockerización de toda la aplicación
- ✅ Documentación automática de API
- ✅ Migración de datos desde MySQL
