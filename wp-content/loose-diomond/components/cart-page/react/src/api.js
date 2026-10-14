const cfg = () => window.LS_CART || {}

/** POST to admin-ajax as form data, returns parsed JSON. */
export async function ajax(action, data = {}) {
  const body = new URLSearchParams({ action, ...data })
  const res = await fetch(cfg().ajaxUrl || '/wp-admin/admin-ajax.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
    body,
  })
  if (!res.ok && res.status !== 400 && res.status !== 403 && res.status !== 404) {
    throw new Error('Request failed')
  }
  const json = await res.json()
  // Server messages come HTML-escaped (esc_html → &quot; &#039; &amp; …).
  // React prints text literally, so decode them to plain text here once.
  if (json && json.data && typeof json.data.message === 'string') {
    json.data.message = decodeEntities(json.data.message)
  }
  return json
}

/** "Coupon &quot;love30&quot; has expired." → 'Coupon "love30" has expired.' (text only, no HTML executed) */
export function decodeEntities(str = '') {
  if (!str || str.indexOf('&') === -1) return str
  const doc = new DOMParser().parseFromString(`<!doctype html><body>${str}`, 'text/html')
  return doc.body.textContent || ''
}

export const nonce = (name) => (cfg().nonces || {})[name] || ''

/** Fetch fresh cart state from PHP. */
export async function fetchCart() {
  const r = await ajax('lgd_cart_page_data', { security: nonce('cart') })
  if (!r || !r.success) throw new Error('Could not load cart')
  return r.data
}

/** Tell mini-cart / header count (WooCommerce fragments) to refresh. */
export function refreshFragments() {
  if (window.jQuery) window.jQuery(document.body).trigger('wc_fragment_refresh')
}
