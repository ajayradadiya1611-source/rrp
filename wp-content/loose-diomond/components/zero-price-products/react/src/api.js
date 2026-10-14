import axios from 'axios'

// window.lgdZeroPriceProductsData is localized from PHP: { apiUrl, nonce, adminUrl }
const cfg = typeof window !== 'undefined' ? (window.lgdZeroPriceProductsData || {}) : {}

export const adminUrl = cfg.adminUrl || '/wp-admin/'

const client = axios.create({
  baseURL: cfg.apiUrl || '/wp-json/lgd/v1',
  headers: {
    'X-WP-Nonce': cfg.nonce || '',
  },
})

export function fetchZeroPriceProducts() {
  return client.get('/zero-price-products').then((res) => res.data)
}

export function deleteZeroPriceProduct(productId) {
  return client
    .post('/zero-price-products/delete', { product_id: productId })
    .then((res) => res.data)
}

export function bulkDeleteZeroPriceProducts(productIds) {
  return client
    .post('/zero-price-products/delete-bulk', { product_ids: productIds })
    .then((res) => res.data)
}
