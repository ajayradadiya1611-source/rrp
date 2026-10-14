import axios from 'axios'

// window.lgdCategoryProductPositionData is localized from PHP: { apiUrl, nonce, adminUrl }
const cfg = typeof window !== 'undefined' ? (window.lgdCategoryProductPositionData || {}) : {}

export const adminUrl = cfg.adminUrl || '/wp-admin/'

const client = axios.create({
  baseURL: cfg.apiUrl || '/wp-json/lgd/v1',
  headers: { 'X-WP-Nonce': cfg.nonce || '' },
})

export function fetchCategories() {
  return client.get('/product-position/categories').then((res) => res.data)
}

export function fetchCategoryProducts(termId) {
  return client.get('/product-position/products', { params: { term_id: termId } }).then((res) => res.data)
}

export function savePositions(termId, productIds) {
  return client
    .post('/product-position/save', { term_id: termId, product_ids: productIds })
    .then((res) => res.data)
}

export function resetPositions(termId) {
  return client.post('/product-position/reset', { term_id: termId }).then((res) => res.data)
}
