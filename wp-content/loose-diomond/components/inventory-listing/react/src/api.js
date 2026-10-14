// This module does NOT define its own REST routes — the product-grid
// listing calls the theme's existing shared inventory endpoints
// directly (see api/routes.php for details on where those live):
//
//   GET /wp-json/custom/v1/get-products
//   GET /wp-json/custom/v1/get-attribute-terms
//
// InventoryListing/index.jsx currently calls fetch() on those URLs
// inline (unchanged from the original App3.jsx behavior). These
// thin wrappers are provided so future edits can go through one
// place instead of scattering raw fetch() calls, but nothing else
// in this module uses them yet.

export function fetchProducts(queryString) {
  return fetch(`/wp-json/custom/v1/get-products?${queryString}`).then((res) => res.json())
}

export function fetchAttributeTerms(queryString) {
  return fetch(`/wp-json/custom/v1/get-attribute-terms?${queryString}`).then((res) => res.json())
}
