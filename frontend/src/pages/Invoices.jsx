import { useQuery } from '@tanstack/react-query'
import { invoiceService } from '../services'

export default function Invoices() {
  const { data: invoices, isLoading } = useQuery({
    queryKey: ['invoices'],
    queryFn: () => invoiceService.getInvoices(),
  })

  return (
    <div className="container">
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' }}>
        <h1>Facturas</h1>
        <button className="btn btn-primary">
          Nueva Factura
        </button>
      </div>

      <div className="card">
        <h2>Lista de Facturas</h2>
        {isLoading ? (
          <p>Cargando...</p>
        ) : (
          <table className="table">
            <thead>
              <tr>
                <th>Número</th>
                <th>Letra</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              {invoices?.length === 0 ? (
                <tr>
                  <td colSpan="6" style={{ textAlign: 'center' }}>
                    No hay facturas registradas
                  </td>
                </tr>
              ) : (
                invoices?.map((invoice) => (
                  <tr key={invoice.id}>
                    <td>{invoice.point_of_sale}-{invoice.number}</td>
                    <td>{invoice.letter}</td>
                    <td>{new Date(invoice.date).toLocaleDateString()}</td>
                    <td>{invoice.customer_id}</td>
                    <td>${invoice.total_amount.toFixed(2)}</td>
                    <td>{invoice.status === 1 ? 'Activa' : 'Anulada'}</td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        )}
      </div>
    </div>
  )
}
