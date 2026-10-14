import axios from 'axios'

// window.lgdCreateSaleProductData is expected to be localized from PHP via
// wp_localize_script(), providing { apiUrl, nonce }.
// apiUrl should point to: /wp-json/lgd/v1
const cfg = typeof window !== 'undefined' ? (window.lgdCreateSaleProductData || {}) : {}

const client = axios.create({
  baseURL: cfg.apiUrl || '/wp-json/lgd/v1',
  headers: {
    'X-WP-Nonce': cfg.nonce || '',
  },
})

export function fetchSaleInventory(type) {
  return client.get('/sale-inventory', { params: { type } }).then((res) => res.data)
}

export function createSaleProduct(type, inventoryId) {
  return client
    .post('/create-sale-product', { type, inventory_id: inventoryId })
    .then((res) => res.data)
}

export function bulkCreateSaleProduct(type, inventoryIds) {
  return client
    .post('/create-sale-product-bulk', { type, inventory_ids: inventoryIds })
    .then((res) => res.data)
}
