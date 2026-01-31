import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { articleService } from '../services'

export default function Articles() {
  const [showForm, setShowForm] = useState(false)
  const [formData, setFormData] = useState({
    code: '',
    name: '',
    description: '',
    price: 0,
    cost: 0,
    stock: 0,
  })
  const queryClient = useQueryClient()

  const { data: articles, isLoading } = useQuery({
    queryKey: ['articles'],
    queryFn: () => articleService.getArticles(),
  })

  const createMutation = useMutation({
    mutationFn: articleService.createArticle,
    onSuccess: () => {
      queryClient.invalidateQueries(['articles'])
      setShowForm(false)
      setFormData({ code: '', name: '', description: '', price: 0, cost: 0, stock: 0 })
    },
  })

  const handleSubmit = (e) => {
    e.preventDefault()
    createMutation.mutate(formData)
  }

  return (
    <div className="container">
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' }}>
        <h1>Artículos</h1>
        <button className="btn btn-primary" onClick={() => setShowForm(!showForm)}>
          {showForm ? 'Cancelar' : 'Nuevo Artículo'}
        </button>
      </div>

      {showForm && (
        <div className="card">
          <h2>Nuevo Artículo</h2>
          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label className="form-label">Código</label>
              <input
                type="text"
                className="form-control"
                value={formData.code}
                onChange={(e) => setFormData({ ...formData, code: e.target.value })}
                required
              />
            </div>
            <div className="form-group">
              <label className="form-label">Nombre</label>
              <input
                type="text"
                className="form-control"
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                required
              />
            </div>
            <div className="form-group">
              <label className="form-label">Descripción</label>
              <textarea
                className="form-control"
                value={formData.description}
                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
              />
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '1rem' }}>
              <div className="form-group">
                <label className="form-label">Precio</label>
                <input
                  type="number"
                  step="0.01"
                  className="form-control"
                  value={formData.price}
                  onChange={(e) => setFormData({ ...formData, price: parseFloat(e.target.value) })}
                />
              </div>
              <div className="form-group">
                <label className="form-label">Costo</label>
                <input
                  type="number"
                  step="0.01"
                  className="form-control"
                  value={formData.cost}
                  onChange={(e) => setFormData({ ...formData, cost: parseFloat(e.target.value) })}
                />
              </div>
              <div className="form-group">
                <label className="form-label">Stock</label>
                <input
                  type="number"
                  className="form-control"
                  value={formData.stock}
                  onChange={(e) => setFormData({ ...formData, stock: parseFloat(e.target.value) })}
                />
              </div>
            </div>
            <button type="submit" className="btn btn-primary" disabled={createMutation.isPending}>
              {createMutation.isPending ? 'Guardando...' : 'Guardar'}
            </button>
          </form>
        </div>
      )}

      <div className="card">
        <h2>Lista de Artículos</h2>
        {isLoading ? (
          <p>Cargando...</p>
        ) : (
          <table className="table">
            <thead>
              <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Costo</th>
                <th>Stock</th>
              </tr>
            </thead>
            <tbody>
              {articles?.map((article) => (
                <tr key={article.id}>
                  <td>{article.code}</td>
                  <td>{article.name}</td>
                  <td>${article.price.toFixed(2)}</td>
                  <td>${article.cost.toFixed(2)}</td>
                  <td>{article.stock}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  )
}
