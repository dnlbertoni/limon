import { Outlet, Link, useNavigate } from 'react-router-dom'
import { useAuthStore } from '../stores/authStore'
import './Layout.css'

export default function Layout() {
  const { user, logout } = useAuthStore()
  const navigate = useNavigate()

  const handleLogout = () => {
    logout()
    navigate('/login')
  }

  return (
    <div className="layout">
      <nav className="navbar">
        <div className="navbar-brand">
          <h1>Limon ERP</h1>
        </div>
        <div className="navbar-menu">
          <Link to="/" className="navbar-item">Dashboard</Link>
          <Link to="/articles" className="navbar-item">Artículos</Link>
          <Link to="/invoices" className="navbar-item">Facturas</Link>
          <Link to="/fiscal-printer" className="navbar-item">Impresora Fiscal</Link>
        </div>
        <div className="navbar-user">
          <span>{user?.username}</span>
          <button onClick={handleLogout} className="btn btn-secondary">
            Salir
          </button>
        </div>
      </nav>
      <main className="main-content">
        <Outlet />
      </main>
    </div>
  )
}
