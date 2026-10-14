import React from 'react'
import ReactDOM from 'react-dom/client'
import RingExtraFeatures from './components/RingExtraFeatures'
import './index.scss'

/**
 * Every "Add Extra Feature" button + its modal is a separate mount point
 * (.ls-ref-mount, rendered by LGD_Ring_Extra_Features::render_button_and_modal()
 * in PHP). A page can legitimately contain more than one — a quick-view
 * popup, an upsell/related-product widget, the Ring Preview page, a normal
 * single-product page — and each becomes its own independent React root
 * with its own component state. Unlike the old jQuery version, there is no
 * shared/global selector anywhere, so one instance's selection can never
 * leak into another's no matter how many end up on the same page.
 */
function mountAll() {
  document.querySelectorAll('.ls-ref-mount').forEach((el) => {
    if (el.dataset.lsRefMounted) return // guard against double-mounting on re-run
    el.dataset.lsRefMounted = '1'

    let groups = {}
    try {
      groups = JSON.parse(el.dataset.groups || '{}')
    } catch (e) {
      groups = {}
    }

    const props = {
      groups,
      preselectedRaw: el.dataset.preselected || '',
      currency: el.dataset.currency || '$',
      imageBase: el.dataset.imageBase || '',
      inputName: el.dataset.inputName || 'extra_features',
      mountEl: el,
      productId: el.dataset.productId ? Number(el.dataset.productId) : null,
      simplePrice: el.dataset.simplePrice ? parseFloat(el.dataset.simplePrice) : 0,
      cartItemKey: el.dataset.cartItemKey || null,
    }

    ReactDOM.createRoot(el).render(
      <React.StrictMode>
        <RingExtraFeatures {...props} />
      </React.StrictMode>
    )
  })
}

// Cart page is rendered by React (components/cart-page), so its
// .ls-ref-mount divs can appear after DOMContentLoaded. Expose a
// re-scan hook + listen for the cart app's event. Double-mount is
// already guarded by data-ls-ref-mounted.
window.lsRefMountAll = mountAll
document.addEventListener('ls:ref-mount', mountAll)

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountAll)
} else {
  mountAll()
}
