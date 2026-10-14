import axios from 'axios'

// window.lgdDeleteInventoryData is expected to be localized from PHP via
// wp_localize_script(), providing { apiUrl, nonce }.
// apiUrl should point to: /wp-json/lgd/v1
const cfg = typeof window !== 'undefined' ? (window.lgdDeleteInventoryData || {}) : {}

const client = axios.create({
  baseURL: cfg.apiUrl || '/wp-json/lgd/v1',
  headers: {
    'X-WP-Nonce': cfg.nonce || '',
  },
})

export function fetchOutOfStock(type) {
  return client.get('/out-of-stock-inventory', { params: { type } }).then((res) => res.data)
}

export function fetchInStockNoInventory(type) {
  return client.get('/in-stock-no-inventory', { params: { type } }).then((res) => res.data)
}

export function deleteInventoryRecord(type, productId) {
  return client
    .post('/delete-inventory', { type, product_id: productId })
    .then((res) => res.data)
}

export function bulkDeleteInventoryRecords(type, productIds) {
  return client
    .post('/delete-inventory-bulk', { type, product_ids: productIds })
    .then((res) => res.data)
}
