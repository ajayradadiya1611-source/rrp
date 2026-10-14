import React, { useEffect, useRef, useState } from 'react'
import Html from './Html.jsx'
import styleString from './styleString.js'

/* ---------------- Ring / Setting size select ---------------- */
function SizeSelect({ label, value, options, onSave, className }) {
  const [current, setCurrent] = useState(value)
  const [busy, setBusy] = useState(false)
  const [saved, setSaved] = useState(false)
  const timer = useRef(null)

  useEffect(() => setCurrent(value), [value])
  useEffect(() => () => clearTimeout(timer.current), [])

  const change = async (e) => {
    const next = e.target.value
    const prev = current
    setCurrent(next)
    setBusy(true)
    try {
      await onSave(next)
      setSaved(true)
      clearTimeout(timer.current)
      timer.current = setTimeout(() => setSaved(false), 1500)
    } catch (err) {
      setCurrent(prev)
      alert(err.message || `Could not update ${label.toLowerCase()}. Please try again.`)
    } finally {
      setBusy(false)
    }
  }

  return (
    <div className={`oc_variation_sub ${className} d-flex align-items-center gap_8`}>
      <strong>{label}:</strong>
      <select className={`${className}_select`} value={current} disabled={busy} onChange={change}>
        {options.map((o) => (
          <option key={o.value} value={o.value}>{o.label}</option>
        ))}
      </select>
      {saved && <span className="text-success" style={{ fontSize: 12 }}>Saved</span>}
    </div>
  )
}

/* ---------------- Quantity ---------------- */
function Qty({ item, onChange }) {
  const [val, setVal] = useState(item.qty)
  useEffect(() => setVal(item.qty), [item.qty])
  const timer = useRef(null)
  useEffect(() => () => clearTimeout(timer.current), [])

  if (!item.qtyBox) {
    return (
      <p className="product_quantity px-0 px-md-4 fw-500 mb-0 text-black">
        {item.qty} qty.
        {/* same hidden field as old cart.php — Extend reads input.qty */}
        <input type="hidden" name={`cart[${item.key}][qty]`} value={item.qty} className="qty" readOnly />
      </p>
    )
  }

  const max = item.maxQty > 0 ? item.maxQty : undefined
  const commit = (n) => {
    let q = parseInt(n, 10)
    if (isNaN(q) || q <= 0) q = 1
    if (max && q > max) q = max
    setVal(q)
    if (q !== item.qty) onChange(q)
  }

  return (
    <div className="product_quantity px-0 px-md-4 fw-500 mb-0 text-black">
      <div className="quantity">
        <label className="screen-reader-text" htmlFor={`qty_${item.key}`}>{item.nameText} quantity</label>
        <input
          type="number"
          id={`qty_${item.key}`}
          className="input-text qty text"
          min="1"
          max={max}
          step="1"
          inputMode="numeric"
          value={val}
          onChange={(e) => {
            const v = e.target.value
            setVal(v)
            // Call the API on change (arrows or typing). Short debounce so
            // typing "12" sends one request, not "1" then "12".
            clearTimeout(timer.current)
            if (v === '') return // wait until a number is typed
            timer.current = setTimeout(() => commit(v), 400)
          }}
          onKeyDown={(e) => {
            if (e.key === 'Enter') {
              clearTimeout(timer.current)
              commit(e.currentTarget.value)
            }
          }}
        />
      </div>
    </div>
  )
}

/* ---------------- Cart item ---------------- */
export default function CartItem({ item, ringSizes, fonts, actions, refreshSeq = 0 }) {
  const fontStyle = styleString(fonts.find((f) => f.value === item.engraving.font)?.style || '')
  const hasEngraving = !!item.engraving.text

  const rowClass = [
    'cart_item d-flex flex-wrap w-100',
    item.rbType === 'setting_prdct' ? 'ls-ring-builder-product' : '',
    item.rbType === 'diamond_prdct' ? 'ls-ring-builder-diamond-product' : '',
  ].join(' ')

  const Thumb = item.permalink
    ? <a href={item.permalink} dangerouslySetInnerHTML={{ __html: item.thumbnailHtml }} />
    : <Html as="span" html={item.thumbnailHtml} />

  return (
    <div className={rowClass} data-engraving-key={item.key} data-qty={item.qty}>
      {/* Thumbnail + title/meta */}
      <div className="product_name">
        <div>
          {Thumb}
          {item.sampleImage && <p className="ls_img_instruction mb-0">Sample Image Only</p>}
        </div>

        <div>
          {item.permalink
            ? <a href={item.permalink} dangerouslySetInnerHTML={{ __html: item.nameHtml }} />
            : <Html as="span" html={item.nameHtml} />}

          {/* key → fresh DOM each refresh (Extend offer re-renders cleanly) */}
          <Html key={`after-${refreshSeq}`} html={item.afterNameHtml} />

          {item.meta.length > 0 && (
            <div className="cart-item-meta">
              <div className="oc_variation">
                {item.meta.map((m, i) => {
                  if (m.type === 'ringsize') {
                    return (
                      <SizeSelect key={i} label="Ring Size" className="ls_ringsize" value={item.ringSize}
                        options={ringSizes} onSave={(v) => actions.updateRingSize(item.key, v)} />
                    )
                  }
                  if (m.type === 'settingsize') {
                    return (
                      <SizeSelect key={i} label="Setting Size" className="ls_settingsize" value={item.settingSize}
                        options={ringSizes} onSave={(v) => actions.updateSettingSize(item.key, v)} />
                    )
                  }
                  return (
                    <div className="oc_variation_sub" key={i}>
                      <strong>{m.key}:</strong> <span dangerouslySetInnerHTML={{ __html: m.html }} />
                    </div>
                  )
                })}
              </div>
            </div>
          )}

          {item.outOfStock && <p style={{ color: 'red' }} className="oc-cart-out-stock">This item has been sold out.</p>}

          {item.noCertificate && (
            <div className="custom-certi-text">This diamond is sold without a certificate (no certificate included).</div>
          )}

          {item.isEngraving && (
            <div className="ls_engraving_display_area">
              {hasEngraving ? (
                <div className="ls_engraving_display" style={{ fontSize: 13, marginTop: 4 }}>
                  <strong>Engraving:</strong>{' '}
                  <span
                    className="ls_engraving_text"
                    role="button"
                    tabIndex={0}
                    title="Edit engraving"
                    style={{ textDecoration: 'underline', cursor: 'pointer', ...fontStyle }}
                    onClick={() => actions.openEngraving(item.key)}
                    onKeyDown={(e) => e.key === 'Enter' && actions.openEngraving(item.key)}
                  >
                    {item.engraving.text}
                  </span>
                  <a
                    href="#"
                    className="ls_remove_engraving text-decoration-none text-dark fw-700 ms-1"
                    aria-label="Remove engraving"
                    onClick={(e) => { e.preventDefault(); actions.removeEngraving(item.key) }}
                  >
                    &#10005;
                  </a>
                </div>
              ) : (
                <a href="#" className="add_engraving_btn" onClick={(e) => { e.preventDefault(); actions.openEngraving(item.key) }}>
                  + Add engraving
                </a>
              )}
            </div>
          )}
        </div>
      </div>

      {/* Quantity & price */}
      <div className="product_quantity_total">
        <Qty item={item} onChange={(q) => actions.updateQty(item.key, q)} />

        <div className="d-flex flex-column align-items-end text-end occarttotal">
          <Html as="p" className="fw-500 mb-0" html={item.priceHtml} />
          <Html html={item.wasPriceHtml} />
          {/* PHP-built (woocommerce_cart_item_remove_link): no Remove on diamond
              line, Remove + "Rebuild Ring" on setting line. Clicks handled in App. */}
          <Html className="ls_remove_wrap" html={item.removeHtml} />
        </div>
      </div>

      {/* Full width: upgrades + actions */}
      <div className="ls_item_fullrow">
        {item.upgrades.length > 0 && (
          <div className="ls_upgrade_list">
            {item.upgrades.map((u) => (
              <div className="ls_upgrade_card" key={u.optionKey || u.name}>
                <div className="ls_upgrade_thumb">{u.img && <img src={u.img} alt={u.name} />}</div>
                <div className="ls_upgrade_info">
                  <span className="ls_upgrade_label">Setting upgrade</span>
                  <span className="ls_upgrade_name">{u.name}</span>
                </div>
                <div className="ls_upgrade_side">
                  <Html className="ls_upgrade_price" html={u.priceHtml} />
                  <a
                    href="#"
                    className="ls_upgrade_remove"
                    aria-label={`Remove ${u.name}`}
                    onClick={(e) => { e.preventDefault(); actions.removeUpgrade(item.key, u.optionKey) }}
                  >
                    Remove
                  </a>
                </div>
              </div>
            ))}
          </div>
        )}

        {item.upgradeEditHtml && <Html className="ls_item_actions" html={item.upgradeEditHtml} />}
      </div>
    </div>
  )
}
