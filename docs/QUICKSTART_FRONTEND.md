# Quickstart — Frontend

Instrucciones específicas para el frontend (React + Vite).

1) Requisitos
- Node 18+ y npm o yarn

2) Instalación

```bash
cd frontend
npm install
# o: yarn
```

3) Configurar URL de la API

```bash
export VITE_API_URL=http://localhost:8000
```

4) Ejecutar servidor de desarrollo

```bash
npm run dev
# o: yarn dev
```

5) Acceder a la aplicación
- http://localhost:3000

6) Notas
- Frontend espera la API en `VITE_API_URL`.
- Para desarrollo junto al backend en Docker, usar el compose indicado en `docs/QUICKSTART.md`.
