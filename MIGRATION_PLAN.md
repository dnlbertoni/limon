# Plan de migración: PHP (CodeIgniter) → FastAPI + React/Vite + PostgreSQL

Fecha: 2026-01-30
Responsable: Equipo de migración
Repositorio: limon

## Resumen
Documento de trabajo que describe las tareas necesarias para migrar la aplicación monolítica PHP/CodeIgniter a una arquitectura separada: backend en FastAPI (Python) y frontend en React/Vite. Incluye fases, prioridades, mapeo inicial de módulos y checklist de entregables.

## Alcance
- Migrar lógica de negocio y datos para las funcionalidades principales: POS, facturación, artículos, stock, cuentas/ctacte, autenticación.
- Preservar integraciones críticas: impresora fiscal Hasar (legacy y 2g), generación de PDFs/remitos, ficheros existentes.
- Desplegar con Docker / docker-compose y proveer scripts de migración de datos.
- Internacionalización en español.

## Fases y tareas (alto nivel)
1. Auditoría (completada)
   - Listado de módulos, controladores, modelos y vistas (ya realizado).
2. Diseño de arquitectura (en progreso)
   - Definir estructura de paquetes del backend (`app/`), routers, servicios y modelos (SQLAlchemy/Alambic opcional).
   - Definir esquema REST y contratos (JSON) para cada recurso.
   - Políticas de autenticación (JWT), roles y permisos.
3. Scaffold backend (en progreso)
   - Crear proyecto FastAPI mínimo: `app/main.py`, `app/api/`, `app/core/config.py`, `requirements.txt`.
   - Configurar CORS y carga de `.env` (útil: `DATABASE_URL`, `SECRET_KEY`, `HASAR_*`).
   - Añadir pruebas básicas y script de arranque `uvicorn`.
4. Migración de base de datos
   - Revisar esquemas SQL existentes y normalizar para PostgreSQL.
   - Crear modelos SQLAlchemy (o Pydantic + ORM) y migraciones (Alembic / SQL scripts).
   - Implementar scripts de ETL para portar datos (validaciones, mapeos de tipos).
5. Implementar autenticación y seguridad
   - JWT con `SECRET_KEY`, refresco de tokens según `ACCESS_TOKEN_EXPIRE_MINUTES`.
   - Rutas protegidas y middleware (roles si aplica).
6. Migrar módulos por prioridad (iterativo)
   - Prioridad 1 (Core POS/Facturación): `pos/factura`, `facturas`, impresiones Hasar, `Facencab_model`, `Tmpmovim_model`.
   - Prioridad 2 (Artículos y Catálogo): `articulos` (búsquedas, CRUD, import CSV).
   - Prioridad 3 (Cuentas / CTActe / Auth): `auth` (tank_auth → JWT), `cuenta`, `ctacte`.
   - Prioridad 4 (Stock, version, reportes, utilidades): `stock`, `version`, export PDF.
   - Para cada módulo: definir endpoints REST, DTOs, services y tests.
7. Scaffold frontend React
   - Crear app Vite + React, definir rutas, stores (estado), servicios HTTP a la API.
   - Migrar vistas críticas: POS (pantalla de caja), pantallas de facturas, listado de artículos, login.
   - Integrar i18n (react-i18next) con español por defecto.
8. Integración y funcionalidades especiales
   - Impresora fiscal Hasar: encapsular cliente Hasar en backend (`services/hasar.py`) y exponer endpoints para imprimir/consultar estado.
   - PDFs / Remitos: mantener generación en backend (migrar lógica PHP a Python o seguir generando con herramientas existentes si conveniente).
9. Docker, CI/CD y despliegue
   - Actualizar `docker-compose.yml` con servicios `backend`, `frontend`, `db` (Postgres).
   - Añadir pipeline CI (tests, lint, build de imágenes).
10. Pruebas, QA y despliegue
   - Pruebas unitarias y de integración (endpoints críticos).
   - Plan de rollback y validación en staging.

## Mapeo inicial de recursos / endpoints sugeridos
(Recursos y endpoints REST recomendados; ajustar a conveniencia)
- Auth
  - POST `/api/auth/login` → recibe `username/email` + `password` → JWT
  - POST `/api/auth/refresh` → refresh token
  - POST `/api/auth/register` (opcional)
- Usuarios / Cuentas
  - GET `/api/cuentas` (filtros)
  - GET `/api/cuentas/{id}`
  - POST `/api/cuentas`
  - PUT `/api/cuentas/{id}`
- Artículos
  - GET `/api/articulos` (filtros, búsqueda)
  - GET `/api/articulos/{codigo}`
  - POST `/api/articulos`
  - PUT `/api/articulos/{id}`
  - POST `/api/articulos/import` (CSV)
- POS / Facturación
  - POST `/api/pos/presupuesto` → crear/actualizar presupuesto temporal
  - POST `/api/pos/{id}/articulos` → añadir artículo a comprobante
  - DELETE `/api/pos/{id}/articulos/{mov_id}`
  - POST `/api/pos/{id}/print` → imprime ticket/factura (invoca Hasar)
  - POST `/api/facturas` → grabar comprobante definitivo
  - GET `/api/facturas/{id}`
  - GET `/api/facturas?cuenta=&desde=&hasta=`
- Stock / Inventario
  - GET `/api/stock`
  - GET `/api/stock/listado-pdf` → generar PDF
- Utilities
  - GET `/api/hasar/status`
  - POST `/api/hasar/print` (payload: tipo, items)

## Prioridades y entregables por sprint (ejemplo)
- Sprint 0 (1 semana): Scaffold backend + conexión DB, JWT básico, CORS, README con pasos de arranque.
- Sprint 1 (2 semanas): Endpoints POS minimal (presupuesto, addArticulo, list), scaffold frontend POS minimal.
- Sprint 2 (2 semanas): Grabado facturas, impresión (Hasar) integration, pruebas end-to-end.
- Sprint 3 (2 semanas): CRUD Artículos + import CSV + búsquedas.
- Sprint 4 (2 semanas): Auth completa, cuentas/ctacte, stock, reportes, Docker final.
- Sprint 5: QA, ajustes, deploy en staging, documentación final.

## Riesgos y mitigaciones
- Impresora fiscal (Hasar): mantener librería PHP vs reimplementación en Python — mitigación: crear servicio wrapper que pueda llamar al binario/daemon existente o exponer un adaptador REST temporal.
- Migración de datos: inconsistencias en datos históricos — mitigación: scripts ETL con validaciones y dumps intermedios.
- Regresión funcional: cobertura de tests insuficiente — mitigación: priorizar tests para facturación y POS.

## Checklist mínimo antes de desplegar a staging
- [ ] Backend: endpoints críticos implementados y cubiertos por tests.
- [ ] Migración de esquema y datos validada en Postgres staging.
- [ ] Auth JWT funcionando; permisos básicos verificados.
- [ ] Frontend POS integrado y autenticado.
- [ ] Impresora Hasar integrada y probada con hardware o simulador.
- [ ] `docker-compose` con `backend`, `frontend`, `db` y variables en `.env`.
- [ ] Documentación de pasos de rollback.

## Recursos y archivos en repositorio a revisar
- `www/application/modules/*` → controladores y modelos ya auditados.
- `backend/.env` → variables útiles ya existentes (DB, SECRET_KEY, HASAR_*).
- `migrations/` y `database/migrations/` → revisar SQL existentes para migración.

## Primera tarea inmediata (próximo paso)
- Crear scaffold mínimo de backend en `backend/` con FastAPI: `requirements.txt`, `app/main.py`, `app/core/config.py`, `app/api/v1/health.py` y setup de CORS. (Si confirmas, lo creo ahora.)

---

Archivo creado por el agente como guía. Mantendremos este documento vivo y lo actualizaremos con subtareas y progresos.
