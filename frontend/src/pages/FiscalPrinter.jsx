import { useState } from 'react'
import { useMutation } from '@tanstack/react-query'
import { hasarService } from '../services'

export default function FiscalPrinter() {
  const [printerVersion, setPrinterVersion] = useState('2.0')
  const [status, setStatus] = useState(null)
  const [receiptData, setReceiptData] = useState({
    customer: {
      name: 'Consumidor Final',
      tax_id: '',
      vat_condition: 'CF',
      document_type: 'DNI',
      receipt_type: 'T',
      letter: 'B',
    },
    items: [
      { description: '', quantity: 1, price: 0, vat_rate: 21.0, discount: 0 }
    ],
    payments: [{ amount: 0, type: 'efectivo' }]
  })

  const statusMutation = useMutation({
    mutationFn: (config) => hasarService.getStatus(config),
    onSuccess: (data) => setStatus(data),
  })

  const printMutation = useMutation({
    mutationFn: ({ receipt, config }) => hasarService.printReceipt(receipt, config),
  })

  const handleCheckStatus = () => {
    const config = {
      version: printerVersion,
      point_of_sale: 1,
      host: printerVersion === '2.0' ? '192.168.1.100' : undefined,
      password: printerVersion === '2.0' ? 'password' : undefined,
    }
    statusMutation.mutate(config)
  }

  const handlePrintReceipt = () => {
    const config = {
      version: printerVersion,
      point_of_sale: 1,
      host: printerVersion === '2.0' ? '192.168.1.100' : undefined,
      password: printerVersion === '2.0' ? 'password' : undefined,
    }
    printMutation.mutate({ receipt: receiptData, config })
  }

  const addItem = () => {
    setReceiptData({
      ...receiptData,
      items: [...receiptData.items, { description: '', quantity: 1, price: 0, vat_rate: 21.0, discount: 0 }]
    })
  }

  const updateItem = (index, field, value) => {
    const newItems = [...receiptData.items]
    newItems[index][field] = value
    setReceiptData({ ...receiptData, items: newItems })
  }

  return (
    <div className="container">
      <h1>Impresora Fiscal Hasar</h1>
      
      <div className="card">
        <h2>Configuración</h2>
        <div className="form-group">
          <label className="form-label">Versión de Impresora</label>
          <select 
            className="form-control"
            value={printerVersion}
            onChange={(e) => setPrinterVersion(e.target.value)}
          >
            <option value="legacy">Legacy (Comunicación por archivos)</option>
            <option value="2.0">Hasar 2.0 (HTTP/JSON API)</option>
          </select>
        </div>
        <button className="btn btn-primary" onClick={handleCheckStatus} disabled={statusMutation.isPending}>
          {statusMutation.isPending ? 'Verificando...' : 'Verificar Estado'}
        </button>
        
        {status && (
          <div className="alert alert-success" style={{ marginTop: '1rem' }}>
            <strong>Estado de la impresora:</strong>
            <pre>{JSON.stringify(status, null, 2)}</pre>
          </div>
        )}
      </div>

      <div className="card">
        <h2>Datos del Cliente</h2>
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
          <div className="form-group">
            <label className="form-label">Nombre</label>
            <input
              type="text"
              className="form-control"
              value={receiptData.customer.name}
              onChange={(e) => setReceiptData({
                ...receiptData,
                customer: { ...receiptData.customer, name: e.target.value }
              })}
            />
          </div>
          <div className="form-group">
            <label className="form-label">CUIT/DNI</label>
            <input
              type="text"
              className="form-control"
              value={receiptData.customer.tax_id}
              onChange={(e) => setReceiptData({
                ...receiptData,
                customer: { ...receiptData.customer, tax_id: e.target.value }
              })}
            />
          </div>
          <div className="form-group">
            <label className="form-label">Condición IVA</label>
            <select 
              className="form-control"
              value={receiptData.customer.vat_condition}
              onChange={(e) => setReceiptData({
                ...receiptData,
                customer: { ...receiptData.customer, vat_condition: e.target.value }
              })}
            >
              <option value="CF">Consumidor Final</option>
              <option value="RI">Responsable Inscripto</option>
              <option value="MONO">Monotributista</option>
            </select>
          </div>
          <div className="form-group">
            <label className="form-label">Letra</label>
            <select 
              className="form-control"
              value={receiptData.customer.letter}
              onChange={(e) => setReceiptData({
                ...receiptData,
                customer: { ...receiptData.customer, letter: e.target.value }
              })}
            >
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
            </select>
          </div>
        </div>
      </div>

      <div className="card">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
          <h2>Items</h2>
          <button className="btn btn-secondary" onClick={addItem}>
            Agregar Item
          </button>
        </div>
        
        {receiptData.items.map((item, index) => (
          <div key={index} style={{ 
            display: 'grid', 
            gridTemplateColumns: '2fr 1fr 1fr 1fr 1fr', 
            gap: '1rem',
            marginBottom: '1rem',
            padding: '1rem',
            border: '1px solid #ddd',
            borderRadius: '4px'
          }}>
            <div className="form-group">
              <label className="form-label">Descripción</label>
              <input
                type="text"
                className="form-control"
                value={item.description}
                onChange={(e) => updateItem(index, 'description', e.target.value)}
              />
            </div>
            <div className="form-group">
              <label className="form-label">Cantidad</label>
              <input
                type="number"
                className="form-control"
                value={item.quantity}
                onChange={(e) => updateItem(index, 'quantity', parseFloat(e.target.value))}
              />
            </div>
            <div className="form-group">
              <label className="form-label">Precio</label>
              <input
                type="number"
                step="0.01"
                className="form-control"
                value={item.price}
                onChange={(e) => updateItem(index, 'price', parseFloat(e.target.value))}
              />
            </div>
            <div className="form-group">
              <label className="form-label">IVA %</label>
              <input
                type="number"
                step="0.01"
                className="form-control"
                value={item.vat_rate}
                onChange={(e) => updateItem(index, 'vat_rate', parseFloat(e.target.value))}
              />
            </div>
            <div className="form-group">
              <label className="form-label">Descuento %</label>
              <input
                type="number"
                step="0.01"
                className="form-control"
                value={item.discount}
                onChange={(e) => updateItem(index, 'discount', parseFloat(e.target.value))}
              />
            </div>
          </div>
        ))}
      </div>

      <div className="card">
        <button 
          className="btn btn-primary btn-block" 
          onClick={handlePrintReceipt}
          disabled={printMutation.isPending}
        >
          {printMutation.isPending ? 'Imprimiendo...' : 'Imprimir Comprobante'}
        </button>
        
        {printMutation.isSuccess && (
          <div className="alert alert-success" style={{ marginTop: '1rem' }}>
            ✓ Comprobante impreso exitosamente
          </div>
        )}
        
        {printMutation.isError && (
          <div className="alert alert-error" style={{ marginTop: '1rem' }}>
            ✗ Error al imprimir: {printMutation.error?.message}
          </div>
        )}
      </div>
    </div>
  )
}
