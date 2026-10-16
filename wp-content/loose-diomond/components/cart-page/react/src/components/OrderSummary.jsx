import React, { useState } from 'react'
import Html from './Html.jsx'

const chat = (msg) => (e) => {
  e.preventDefault()
  if (typeof window.openZohoChat === 'function') window.openZohoChat(msg + window.location.href)
}

function PromoCode({ hidden, onApply }) {
  const [open, setOpen] = useState(false)
  const [code, setCode] = useState('')
  const [msg, setMsg] = useState(null)

  if (hidden) return null

  const apply = async (e) => {
    e.preventDefault()
    const c = code.trim()
    if (!c) return
    const r = await onApply(c)
    if (r.ok) {
      setCode('')
      setMsg(null)
      setOpen(false)
    } else {
      setMsg(r.message)
    }
  }

  if (!open) {
    return (
      <p className="coupon_form fw-500 mb-2" role="button" onClick={() => setOpen(true)}>
        Apply promo code +
      </p>
    )
  }

  return (
    <form className="promo_code" onSubmit={apply}>
      <div className="promo_code_inner">
        <input type="text" placeholder="Enter Promo Code" value={code} autoFocus onChange={(e) => setCode(e.target.value)} />
        <button type="submit" className="btn apply_btn">Apply</button>
      </div>
      <div className="cart-coupon-message">{msg && <span style={{ color: 'red' }}>{msg}</span>}</div>
    </form>
  )
}

function Row({ label, value, valueClass = '', bold }) {
  const w = bold ? 'fw-700 text-uppercase' : 'fw-500'
  return (
    <div className={`d-flex align-items-center justify-content-between ${bold ? '' : 'pb_10'}`}>
      <p className={`${w} mb-0 text-black`}>{label}</p>
      {typeof value === 'string'
        ? <Html as="p" className={`${w} mb-0 text-black ${valueClass}`} html={value} />
        : <p className={`${w} mb-0 text-black ${valueClass}`}>{value}</p>}
    </div>
  )
}

export default function OrderSummary({ cart, actions }) {
  const icon = (f) => `${cart.themeUri}/aj_custom/cart/${f}`

  return (
    <>
      <div className="cart_collaterals">
        <h6 className="fw-600 mb_20">Order Summary</h6>

        <Row label="Subtotal" value={cart.subtotalHtml} valueClass="cart-subtotal-amount" />

        {cart.coupons.map((c) => (
          <div className="d-flex align-items-center justify-content-between pb_10" key={c.code}>
            <p className="fw-500 mb-0 text-black">
              {c.label ? (
                <span>{c.label}</span>
              ) : (
                <span>
                  Coupon{' '}
                  <span className="coupon_lb">
                    {c.code}{' '}
                    <a href="#" className="remove_coupon text-decoration-none" aria-label={`Remove coupon ${c.code}`}
                      onClick={(e) => { e.preventDefault(); actions.removeCoupon(c.code) }}>
                      <i className="fa fa-times" aria-hidden="true" />
                    </a>
                  </span>
                </span>
              )}
            </p>
            <p className="fw-500 mb-0 text-success">-<span dangerouslySetInnerHTML={{ __html: c.amountHtml }} /></p>
          </div>
        ))}

        <PromoCode hidden={cart.hasRealCoupon} onApply={actions.applyCoupon} />

        <Row label="Shipping" value={cart.shippingHtml} valueClass="text-uppercase" />

       
        <div className="border_top" />

        <Row label="Total" value={cart.totalHtml} valueClass="cart-total-amount" bold />

        <a
          href={cart.canCheckout ? cart.checkoutUrl : cart.cartUrl}
          className={`btn common_black_btn w-100 ${cart.canCheckout ? '' : 'disabled'}`}
          aria-disabled={!cart.canCheckout}
        >
          Secure Checkout
        </a>

        {cart.freeShipLeft && (
          <div className="font_size_12 fw-500 text-left">
            Add ${cart.freeShipLeft} more to your cart to unlock free shipping!
          </div>
        )}

        <div className="trust-block">
          <h6 className="trust-item-head">Your order includes:</h6>
          <div className="trust-item-parent">
            {[`Free ${cart.returnDays}-day returns`, `Free ${cart.returnDays}-day resizing`, 'Manufacturer warranty'].map((t) => (
              <div className="trust-item" key={t}>
                <div className="check-badge"><i className="fa fa-check" aria-hidden="true" /></div>
                <span>{t}</span>
              </div>
            ))}
          </div>

          <h6 className="why-item-head">Why RRP Diamond?</h6>
          <div className="why-item-parent">
            <div className="why-item">
              <div className="why-icon"><img src={icon('money-back.svg')} alt="" /></div>
              <span>100% money-back guarantee</span>
            </div>
            <div className="why-item">
              <div className="why-icon"><img src={icon('inspection.svg')} alt="" /></div>
              <span>Quality inspection</span>
            </div>
            <div className="why-item">
              <div className="why-icon"><img src={icon('quality.svg')} alt="" /></div>
              <span>
                Discreet packaging:
                <br />
                <a href="#" className="play-icon" onClick={(e) => { e.preventDefault(); actions.openVideo() }}>
                  <i className="fa fa-play-circle" aria-hidden="true" /> <span>Watch unboxing video</span>
                </a>
              </span>
            </div>
          </div>
        </div>
      </div>

      <div className="ls_needhelp text-center mt-3">
        Need help?{' '}
        <a href="#" onClick={chat('Hi : ')}>Chat Now</a>
        {cart.supportMobile && (
          <>
            {' '}or <a href={`tel:+${cart.supportMobile}`}>+{cart.supportMobile}</a>
          </>
        )}
      </div>
    </>
  )
}
