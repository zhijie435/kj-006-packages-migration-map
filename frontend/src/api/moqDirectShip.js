import request from '@/utils/request'

const API_PREFIX = '/api/shearerline'

export const supplierApi = {
  list: (params) => request.get(`${API_PREFIX}/suppliers`, { params }),
  all: () => request.get(`${API_PREFIX}/suppliers/all`),
  create: (data) => request.post(`${API_PREFIX}/suppliers`, data),
  update: (id, data) => request.put(`${API_PREFIX}/suppliers/${id}`, data),
  delete: (id) => request.delete(`${API_PREFIX}/suppliers/${id}`),
  detail: (id) => request.get(`${API_PREFIX}/suppliers/${id}`),
}

export const productApi = {
  list: (params) => request.get(`${API_PREFIX}/products`, { params }),
  bySupplier: (supplierId) => request.get(`${API_PREFIX}/products/by-supplier/${supplierId}`),
  create: (data) => request.post(`${API_PREFIX}/products`, data),
  update: (id, data) => request.put(`${API_PREFIX}/products/${id}`, data),
  delete: (id) => request.delete(`${API_PREFIX}/products/${id}`),
  detail: (id) => request.get(`${API_PREFIX}/products/${id}`),
}

export const moqOrderApi = {
  list: (params) => request.get(`${API_PREFIX}/moq-orders`, { params }),
  statistics: () => request.get(`${API_PREFIX}/moq-orders/statistics`),
  create: (data) => request.post(`${API_PREFIX}/moq-orders`, data),
  detail: (id) => request.get(`${API_PREFIX}/moq-orders/${id}`),
  confirm: (id) => request.post(`${API_PREFIX}/moq-orders/${id}/confirm`),
  process: (id) => request.post(`${API_PREFIX}/moq-orders/${id}/process`),
  ship: (id, data) => request.post(`${API_PREFIX}/moq-orders/${id}/ship`, data),
  complete: (id) => request.post(`${API_PREFIX}/moq-orders/${id}/complete`),
  cancel: (id, data) => request.post(`${API_PREFIX}/moq-orders/${id}/cancel`, data),
  refund: (id, data) => request.post(`${API_PREFIX}/moq-orders/${id}/refund`, data),
  pay: (id, data) => request.post(`${API_PREFIX}/moq-orders/${id}/pay`, data),
}

export const shipmentApi = {
  list: (params) => request.get(`${API_PREFIX}/shipments`, { params }),
  byOrder: (orderId) => request.get(`${API_PREFIX}/shipments/by-order/${orderId}`),
  detail: (id) => request.get(`${API_PREFIX}/shipments/${id}`),
  updateTracking: (id, data) => request.post(`${API_PREFIX}/shipments/${id}/update-tracking`, data),
}

export const statusMap = {
  pending: { text: '待确认', type: 'warning' },
  confirmed: { text: '已确认', type: 'primary' },
  processing: { text: '处理中', type: 'info' },
  shipped: { text: '已发货', type: 'success' },
  completed: { text: '已完成', type: 'success' },
  cancelled: { text: '已取消', type: 'danger' },
  refunded: { text: '已退款', type: 'danger' },
}

export const shipmentStatusMap = {
  pending: { text: '待发货', type: 'info' },
  shipped: { text: '已发货', type: 'primary' },
  in_transit: { text: '运输中', type: 'warning' },
  delivered: { text: '已签收', type: 'success' },
  returned: { text: '已退回', type: 'danger' },
  failed: { text: '发货失败', type: 'danger' },
}
