import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { useAuthStore } from './stores/authStore'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import Articles from './pages/Articles'
import Invoices from './pages/Invoices'
import FiscalPrinter from './pages/FiscalPrinter'
import Layout from './components/Layout'

const queryClient = new QueryClient()

function PrivateRoute({ children }) {
  const isAuthenticated = useAuthStore(state => state.isAuthenticated)
  return isAuthenticated ? children : <Navigate to="/login" />
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <Router>
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route path="/" element={
            <PrivateRoute>
              <Layout />
            </PrivateRoute>
          }>
            <Route index element={<Dashboard />} />
            <Route path="articles" element={<Articles />} />
            <Route path="invoices" element={<Invoices />} />
            <Route path="fiscal-printer" element={<FiscalPrinter />} />
          </Route>
        </Routes>
      </Router>
    </QueryClientProvider>
  )
}

export default App
