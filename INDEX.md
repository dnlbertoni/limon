# Limon ERP v2.0 - Índice Completo

## 📁 Estructura del Proyecto

```
limon/
├── backend/                        # Backend FastAPI
│   ├── app/
│   │   ├── api/endpoints/         # Rutas API
│   │   │   ├── auth.py           # Endpoints de autenticación
│   │   │   ├── articles.py       # Gestión de artículos
│   │   │   ├── invoices.py       # Facturas y clientes
│   │   │   └── hasar.py          # Endpoints impresora fiscal
│   │   ├── core/                 # Funcionalidad principal
│   │   │   ├── config.py         # Configuración
│   │   │   ├── database.py       # Configuración de base de datos
│   │   │   └── security.py       # Utilidades de seguridad
│   │   ├── models/               # Modelos SQLAlchemy
│   │   │   ├── user.py           # Modelo de usuario
│   │   │   ├── article.py        # Modelos de artículos
│   │   │   └── invoice.py        # Modelos de facturas
│   │   ├── schemas/              # Esquemas Pydantic
│   │   │   ├── user.py           # Esquemas de usuario
│   │   │   ├── article.py        # Esquemas de artículos
│   │   │   └── invoice.py        # Esquemas de facturas
│   │   ├── services/             # Lógica de negocio
│   │   │   ├── hasar.py          # Servicio Hasar unificado
│   │   │   ├── hasar_legacy.py   # Impresora legacy
│   │   │   └── hasar2.py         # Impresora moderna
│   │   ├── middleware/           # Middleware
│   │   │   └── security.py       # Middleware de seguridad
│   │   └── main.py              # Aplicación FastAPI
│   ├── requirements.txt          # Dependencias Python
│   ├── Dockerfile               # Configuración Docker
│   └── .env.example             # Plantilla de entorno
│
├── frontend/                      # Frontend React
│   ├── src/
│   │   ├── components/           # Componentes reutilizables
│   │   │   ├── Layout.jsx       # Diseño principal
│   │   │   └── Layout.css       # Estilos del diseño
│   │   ├── pages/               # Componentes de página
│   │   │   ├── Login.jsx        # Página de login
│   │   │   ├── Dashboard.jsx    # Panel de control
│   │   │   ├── Articles.jsx     # Gestión de artículos
│   │   │   ├── Invoices.jsx     # Lista de facturas
│   │   │   └── FiscalPrinter.jsx # Interfaz impresora
│   │   ├── services/            # Servicios API
│   │   │   ├── api.js          # Configuración Axios
│   │   │   └── index.js        # Funciones de servicio
│   │   ├── stores/              # Gestión de estado
│   │   │   └── authStore.js    # Estado de autenticación
│   │   ├── App.jsx             # Componente principal
│   │   ├── main.jsx            # Punto de entrada
│   │   └── index.css           # Estilos globales
│   ├── package.json             # Dependencias Node
│   ├── vite.config.js           # Configuración Vite
│   ├── Dockerfile              # Configuración Docker
│   └── index.html              # Plantilla HTML
│
├── database/                      # Archivos de base de datos
│   ├── migrations/               # Migraciones SQL
│   │   └── 001_initial_schema.sql
│   └── scripts/                 # Scripts de migración
│       └── migrate_mysql_to_postgres.py
│
├── docs/                         # Documentación
│   ├── QUICKSTART.md            # Guía de inicio rápido
│   ├── API.md                   # Documentación API
│   ├── SECURITY.md              # Guía de seguridad
│   ├── DEPLOYMENT.md            # Guía de despliegue
│   └── MIGRATION.md             # Guía de migración
│
├── scripts/                      # Scripts de utilidad
│   ├── setup.sh                 # Automatización de configuración
│   └── test.sh                  # Script de pruebas
│
├── docker-compose.new.yml        # Configuración Docker Compose
├── .env.example.new              # Plantilla de entorno
├── .gitignore.new               # Reglas de Git ignore
├── README.new.md                # README principal
└── SUMMARY.md                   # Resumen del proyecto
```

## 📚 Índice de Documentación

### Primeros Pasos
1. **[Guía de Inicio Rápido](docs/QUICKSTART.md)** - Ponte en marcha en minutos
2. **[README Principal](README.new.md)** - Visión general completa del proyecto
3. **[Resumen del Proyecto](SUMMARY.md)** - Lo que se logró

### Desarrollo
4. **[Documentación API](docs/API.md)** - Referencia completa de API
5. **[Guía de Seguridad](docs/SECURITY.md)** - Detalles de implementación de seguridad
6. **Código Backend** - Ubicado en `backend/app/`
7. **Código Frontend** - Ubicado en `frontend/src/`

### Despliegue y Migración
8. **[Guía de Despliegue](docs/DEPLOYMENT.md)** - Despliegue en producción
9. **[Guía de Migración](docs/MIGRATION.md)** - Migrar desde el sistema legacy
10. **Configuración Docker** - `docker-compose.new.yml`

## 🚀 Enlaces Rápidos

### Para Usuarios
- **Login**: http://localhost:3000 (por defecto: admin/admin123)
- **Panel**: http://localhost:3000
- **Artículos**: http://localhost:3000/articles
- **Facturas**: http://localhost:3000/invoices
- **Impresora Fiscal**: http://localhost:3000/fiscal-printer

### Para Desarrolladores
- **Documentación API**: http://localhost:8000/api/docs
- **ReDoc**: http://localhost:8000/api/redoc
- **Health Check**: http://localhost:8000/health
- **PgAdmin**: http://localhost:5050

### Para Administradores
- **Configuración de Entorno**: `.env`
- **Logs Docker**: `docker-compose -f docker-compose.new.yml logs -f`
- **Base de Datos**: `docker-compose -f docker-compose.new.yml exec postgres psql -U limon_user -d limon_db`

## 🔑 Características Clave

### Características del Backend
- ✅ FastAPI con auto-documentación
- ✅ Autenticación JWT
- ✅ ORM SQLAlchemy
- ✅ Base de datos PostgreSQL
- ✅ Validación Pydantic
- ✅ Middleware de seguridad
- ✅ Limitación de tasa
- ✅ Configuración CORS

### Características del Frontend
- ✅ React 18
- ✅ React Router
- ✅ Gestión de estado con Zustand
- ✅ React Query
- ✅ Rutas protegidas
- ✅ Interfaz moderna

### Integración Hasar
- ✅ Soporte legacy (basado en archivos)
- ✅ Soporte Hasar 2.0 (HTTP API)
- ✅ Interfaz de servicio unificada
- ✅ Operación de versión dual

### Características de Seguridad
- ✅ Tokens JWT
- ✅ Hash de contraseñas (bcrypt)
- ✅ Limitación de tasa (100/min)
- ✅ CORS
- ✅ Headers de seguridad
- ✅ Validación de entrada
- ✅ Prevención de inyección SQL

## 📊 Estadísticas

- **Backend**: 20 archivos Python, ~1,034 líneas de código
- **Frontend**: 15+ archivos JavaScript/JSX
- **Documentación**: 6 guías completas
- **Endpoints API**: 20+ endpoints
- **Tablas de Base de Datos**: 9 tablas
- **Capas de Seguridad**: 5+ medidas de seguridad

## 🛠️ Stack Tecnológico

### Backend
- Python 3.11
- FastAPI 0.110.0
- SQLAlchemy 2.0
- PostgreSQL 15
- Pydantic 2.5
- JWT (python-jose)
- bcrypt

### Frontend
- React 18.2
- Vite 5.0
- React Router 6.21
- Zustand 4.4
- React Query 5.17
- Axios 1.6

### Infraestructura
- Docker & Docker Compose
- PostgreSQL 15
- PgAdmin 4
- Nginx (producción)

## 🎯 Casos de Uso

### Para Dueños de Negocio
1. Gestionar inventario (artículos, marcas, categorías)
2. Crear y rastrear facturas
3. Gestionar información de clientes
4. Imprimir recibos fiscales (impresoras legacy y modernas)
5. Ejecutar reportes y análisis

### Para Desarrolladores
1. API RESTful para integración
2. Documentación API auto-generada
3. Esquemas con type-safety
4. Stack tecnológico moderno
5. Fácil de extender

### Para Administradores de Sistema
1. Despliegue Docker
2. Migraciones de base de datos
3. Configuración de seguridad
4. Monitoreo y logging
5. Backup y restauración

## 📋 Listas de Verificación

### Lista de Inicio Rápido
- [ ] Clonar repositorio
- [ ] Copiar `.env.example.new` a `.env`
- [ ] Ejecutar `docker-compose -f docker-compose.new.yml up -d`
- [ ] Inicializar base de datos
- [ ] Acceder a http://localhost:3000
- [ ] Login con admin/admin123
- [ ] Cambiar contraseña

### Lista de Despliegue
- [ ] Configurar variables de entorno de producción
- [ ] Configurar certificados SSL
- [ ] Configurar firewall
- [ ] Configurar backups de base de datos
- [ ] Configurar monitoreo
- [ ] Probar todos los endpoints
- [ ] Configurar impresoras Hasar
- [ ] Capacitar usuarios
- [ ] Realizar auditoría de seguridad

### Lista de Migración
- [ ] Backup de base de datos MySQL
- [ ] Backup de archivos de impresora fiscal
- [ ] Instalar nuevo sistema en paralelo
- [ ] Ejecutar migración de datos
- [ ] Probar impresoras fiscales
- [ ] Capacitar usuarios
- [ ] Cutover
- [ ] Monitorear

## 🔗 Archivos Importantes

### Configuración
- `.env` - Variables de entorno
- `docker-compose.new.yml` - Configuración Docker
- `backend/app/core/config.py` - Configuración de aplicación

### Base de Datos
- `database/migrations/001_initial_schema.sql` - Esquema de base de datos
- `database/scripts/migrate_mysql_to_postgres.py` - Script de migración

### Servicios
- `backend/app/services/hasar.py` - Servicio Hasar unificado
- `backend/app/services/hasar_legacy.py` - Impresora legacy
- `backend/app/services/hasar2.py` - Impresora moderna

### API
- `backend/app/api/endpoints/auth.py` - Autenticación
- `backend/app/api/endpoints/articles.py` - Artículos
- `backend/app/api/endpoints/invoices.py` - Facturas
- `backend/app/api/endpoints/hasar.py` - Impresora fiscal

### Frontend
- `frontend/src/App.jsx` - Aplicación principal
- `frontend/src/pages/FiscalPrinter.jsx` - Interfaz de impresora
- `frontend/src/stores/authStore.js` - Estado de autenticación

## 🆘 Soporte

### Documentación
1. Consultar [Guía de Inicio Rápido](docs/QUICKSTART.md)
2. Revisar [Documentación API](docs/API.md)
3. Leer [Guía de Despliegue](docs/DEPLOYMENT.md)
4. Consultar [Guía de Migración](docs/MIGRATION.md)

### Solución de Problemas
- **Servicios no inician**: Verificar logs de Docker
- **Errores de base de datos**: Verificar cadena de conexión
- **Problemas de login**: Verificar configuración JWT
- **Problemas de impresora**: Verificar configuración de impresora

### Obtener Ayuda
- Issues de GitHub: Reportar bugs o solicitar características
- Documentación API: http://localhost:8000/api/docs
- Email: support@example.com (configurar esto)

## 📝 Licencia

Ver archivo [LICENSE](LICENSE) para detalles.

---

**Versión**: 2.0.0  
**Última Actualización**: 2024-01-30  
**Estado**: Listo para Producción  
**Mantenedor**: Equipo Limon ERP
