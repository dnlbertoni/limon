import { useAuthStore } from '../stores/authStore'

export default function Dashboard() {
  const { user } = useAuthStore()

  return (
    <div className="container">
      <h1>Dashboard</h1>
      <div className="card">
        <h2>Bienvenido, {user?.full_name || user?.username}</h2>
        <p>Sistema de gestión ERP - Limon v2.0</p>
        <div style={{ marginTop: '2rem' }}>
          <h3>Características principales:</h3>
          <ul style={{ marginTop: '1rem', paddingLeft: '2rem' }}>
            <li>✓ Backend FastAPI con Python</li>
            <li>✓ Frontend React moderno</li>
            <li>✓ Base de datos PostgreSQL</li>
            <li>✓ Autenticación JWT segura</li>
            <li>✓ Integración con impresoras fiscales Hasar (Legacy y 2.0)</li>
            <li>✓ Gestión de artículos, facturas y clientes</li>
            <li>✓ Arquitectura de seguridad completa</li>
          </ul>
        </div>
      </div>
      
      <div className="card" style={{ marginTop: '2rem' }}>
        <h3>Impresoras Fiscales Hasar</h3>
        <p>
          Este sistema mantiene la relación entre los controladores Hasar Legacy (comunicación por archivos)
          y Hasar 2.0 (comunicación HTTP/JSON API), permitiendo trabajar con ambas versiones de manera unificada.
        </p>
      </div>
    </div>
  )
}
