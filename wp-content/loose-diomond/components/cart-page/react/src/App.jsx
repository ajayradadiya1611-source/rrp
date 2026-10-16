import React, { useCallback, useEffect, useRef, useState } from 'react'
import { ajax, fetchCart, nonce, refreshFragments, setHeaderCount } from './api.js'
import CartItem from './components/CartItem.jsx'
import OrderSummary from './components/OrderSummary.jsx'
import EngravingModal from './components/EngravingModal.jsx'
import VideoModal from './components/VideoModal.jsx'
import Loader from './components/Loader.jsx'
import Html from './components/Html.jsx'

export default function App({ initial }) {
  const [cart, setCart] = useState(initial)
  // Bumped on every server refresh — remounts PHP-hook HTML (Extend offer
  // divs) so third-party widgets always get a fresh, empty element.
  const [refreshSeq, setRefreshSeq] = useState(0)
  // Loader counter — several requests can overlap (refresh + action)
  const [busy, setBusy] = useState(0)
  const loading = busy > 0
  const setLoading = useCallback((on) => setBusy((n) => Math.max(0, n + (on ? 1 : -1))), [])
  const [engravingKey, setEngravingKey] = useState(null) // cart key whose modal is open
  const [videoOpen, setVideoOpen] = useState(false)
  const rootRef = useRef(null)
  // Default (Rank Math) title, used when the cart has no SKUs left
  const defaultTitleRef = useRef(/^Cart - /.test(document.title) ? 'Cart' : document.title)

  // Keep the browser tab title ("Cart - SKU1, SKU2") in sync after
  // remove / any server refresh.
  useEffect(() => {
    if (cart && cart.pageTitle) document.title = cart.pageTitle
    else if (cart && cart.pageTitle === '') document.title = defaultTitleRef.current
  }, [cart && cart.pageTitle])
  const actionsRef = useRef(null)

  /* ---------- helpers ---------- */
  const applyCart = useCallback((data) => {
    if (!data) return
    if (data.empty) {
      window.location.reload() // let WooCommerce render cart-empty.php
      return
    }
    setCart(data)
    setHeaderCount(data.cartCount)
    setRefreshSeq((n) => n + 1)
    refreshFragments()
  }, [])

  /** Re-fetch cart state, always with the loader. */
  const reload = useCallback(async () => {
    setLoading(true)
    try {
      applyCart(await fetchCart())
    } finally {
      setLoading(false)
    }
  }, [applyCart, setLoading])

  /* ---------- events from other widgets  ---------- */
  useEffect(() => {
    window.lsCartApp = { refresh: reload }
    let pending = 0
    const onBusy = () => { pending++; setLoading(true) }
    const onIdle = () => { if (pending > 0) { pending--; setLoading(false) } }
    const onRefresh = async () => {
      try {
        await reload()
      } catch (e) {
        window.location.reload()
      } finally {
        onIdle()
      }
    }
    document.addEventListener('ls:cart-busy', onBusy)
    document.addEventListener('ls:cart-idle', onIdle)
    document.addEventListener('ls:cart-refresh', onRefresh)
    // WooCommerce / other plugins that change the cart
    const jq = window.jQuery
    const onWc = () => reload().catch(() => {})
    if (jq) jq(document.body).on('removed_coupon applied_coupon ls_cart_refresh', onWc)
    return () => {
      delete window.lsCartApp
      document.removeEventListener('ls:cart-busy', onBusy)
      document.removeEventListener('ls:cart-idle', onIdle)
      document.removeEventListener('ls:cart-refresh', onRefresh)
      if (jq) jq(document.body).off('removed_coupon applied_coupon ls_cart_refresh', onWc)
    }
  }, [reload, setLoading])

  /** Run an async action with the full-screen loader. */
  const withLoader = useCallback(async (fn) => {
    setLoading(true)
    try {
      await fn()
    } catch (e) {
      alert(e.message || 'Something went wrong. Please try again.')
    } finally {
      setLoading(false)
    }
  }, [setLoading])

  /* ---------- tell other plugins the cart DOM is ready (Extend protection-plan offers etc.) ---------- */
  useEffect(() => {
    const jq = window.jQuery
    if (!jq) return
    // Next frame: make sure PHP-hook HTML (the .cart-extend-offer divs) is in the DOM.
    const id = requestAnimationFrame(() => jq(document.body).trigger('ls_cart_rendered'))
    return () => cancelAnimationFrame(id)
  }, [cart, refreshSeq])

  /* ---------- bootstrap tooltips injected via PHP html ---------- */
  useEffect(() => {
    const bs = window.bootstrap
    if (!bs || !bs.Tooltip || !rootRef.current) return
    rootRef.current.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => bs.Tooltip.getOrCreateInstance(el))
  }, [cart])

  /* ---------- remove link rendered by PHP hooks ---------- */
  const onRootClick = useCallback((e) => {
    // Remove link from woocommerce_cart_item_remove_link (href has ?remove_item=)
    const rm = e.target.closest('.ls_remove_wrap a[href*="remove_item="]')
    if (rm) {
      e.preventDefault()
      const key = rm.dataset.cartKey || new URL(rm.href, window.location.href).searchParams.get('remove_item')
      if (key) actionsRef.current.removeItem(key)
      return
    }
  }, [withLoader])

  /* ---------- actions ---------- */
  const actions = {
    updateQty: (key, qty) =>
      withLoader(async () => {
        const r = await ajax('custom_update_cart_qty', { cart_item_key: key, qty })
        if (!r.success) throw new Error('Could not update quantity.')
        await reload()
      }),

    removeItem: (key) =>
      withLoader(async () => {
        const r = await ajax('lgd_cart_remove_item', { cart_item_key: key, security: nonce('cart') })
        if (!r.success) throw new Error(r.data?.message || 'Could not remove item.')
        applyCart(r.data)
      }),

    saveEngraving: (key, text, font) =>
      withLoader(async () => {
        const r = await ajax('ls_save_engraving', {
          cart_item_key: key,
          engraving_text: text,
          engraving_font: font,
          security: nonce('engraving'),
        })
        if (!r.success) throw new Error('Could not save engraving. Please try again.')
        setEngravingKey(null)
        setCart((c) => ({
          ...c,
          items: c.items.map((i) => (i.key === key ? { ...i, engraving: { text, font } } : i)),
        }))
      }),

    removeEngraving: (key) => actions.saveEngraving(key, '', ''),

    applyCoupon: async (code) => {
      setLoading(true)
      try {
        const r = await ajax('custom_apply_coupon', { security: nonce('coupon'), coupon_code: code })
        if (!r.success) return { ok: false, message: r.data?.message || 'Could not apply coupon.' }
        await reload()
        return { ok: true, message: r.data?.message }
      } catch (e) {
        return { ok: false, message: 'Could not apply coupon. Please try again.' }
      } finally {
        setLoading(false)
      }
    },

    removeCoupon: (code) =>
      withLoader(async () => {
        const r = await ajax('lgd_cart_remove_coupon', { coupon: code, security: nonce('cart') })
        if (!r.success) throw new Error('Could not remove coupon.')
        applyCart(r.data)
      }),

    openEngraving: (key) => setEngravingKey(key),
    openVideo: () => setVideoOpen(true),
  }

  actionsRef.current = actions

  const engravingItem = cart.items?.find((i) => i.key === engravingKey)

  return (
    <div ref={rootRef} onClick={onRootClick}>
      {cart.noticesHtml ? <Html className="woocommerce-notices-wrapper" html={cart.noticesHtml} /> : null}

      <div className="row">
        {/* LEFT: CART ITEMS */}
        <div className="col-lg-8">
          <div>
            <h3 className="fw-600 mb-4">Bag</h3>
            <div className="cart_inner">
              {(cart.items || []).map((item) => (
                <CartItem key={item.key} refreshSeq={refreshSeq} item={item} fonts={cart.engravingFonts || []} actions={actions} />
              ))}
            </div>
          </div>
        </div>

        {/* RIGHT: ORDER SUMMARY */}
        <div className="col-lg-4">
          <OrderSummary cart={cart} actions={actions} />
        </div>
      </div>

      <Loader show={loading} src={`${cart.themeUri}/aj_custom/inventory/Loading.edc97a1c.gif`} />

      {engravingItem && (
        <EngravingModal
          fonts={cart.engravingFonts || []}
          initialText={engravingItem.engraving.text}
          initialFont={engravingItem.engraving.font}
          onClose={() => setEngravingKey(null)}
          onSave={(text, font) => actions.saveEngraving(engravingItem.key, text, font)}
        />
      )}

      {videoOpen && <VideoModal src={cart.unboxingVideo} onClose={() => setVideoOpen(false)} />}
    </div>
  )
}
