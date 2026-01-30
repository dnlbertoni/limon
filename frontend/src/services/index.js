import api from './api'

export const authService = {
  async login(username, password) {
    const formData = new FormData()
    formData.append('username', username)
    formData.append('password', password)
    
    const response = await api.post('/auth/login', formData, {
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    return response.data
  },

  async register(userData) {
    const response = await api.post('/auth/register', userData)
    return response.data
  },

  async getCurrentUser() {
    const response = await api.get('/auth/me')
    return response.data
  },
}

export const articleService = {
  async getArticles(params = {}) {
    const response = await api.get('/articles/', { params })
    return response.data
  },

  async createArticle(data) {
    const response = await api.post('/articles/', data)
    return response.data
  },

  async updateArticle(id, data) {
    const response = await api.put(`/articles/${id}`, data)
    return response.data
  },

  async deleteArticle(id) {
    const response = await api.delete(`/articles/${id}`)
    return response.data
  },
}

export const invoiceService = {
  async getInvoices(params = {}) {
    const response = await api.get('/invoices/', { params })
    return response.data
  },

  async createInvoice(data) {
    const response = await api.post('/invoices/', data)
    return response.data
  },

  async getCustomers(params = {}) {
    const response = await api.get('/invoices/customers', { params })
    return response.data
  },

  async createCustomer(data) {
    const response = await api.post('/invoices/customers', data)
    return response.data
  },
}

export const hasarService = {
  async getStatus(config) {
    const response = await api.post('/hasar/status', config)
    return response.data
  },

  async printReceipt(receiptData, config) {
    const response = await api.post('/hasar/print-receipt', receiptData, {
      params: config
    })
    return response.data
  },

  async dailyClose(config, closeType = 'Z') {
    const response = await api.post('/hasar/daily-close', null, {
      params: { ...config, close_type: closeType }
    })
    return response.data
  },
}
