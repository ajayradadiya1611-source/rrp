import React, { useEffect, useMemo, useRef, useState } from 'react'
import { createPortal } from 'react-dom'
import OptionCard from './OptionCard.jsx'

/**
 * One independent instance of the "Add Extra Feature" button + modal.
 * All selection state lives in this component via useState/useRef — since
 * React gives every mounted instance its own state automatically, there is
 * no possibility of one instance's clicks affecting another's cards, no
 * matter how many instances end up on the same page.
 *
 * Props:
 *   groups         — { group_key: { label, subtitle, selection_type, options: { option_key: { label, note, price } } } }
 *                    (exactly what LGD_Ring_Extra_Features::get_available_groups_for_product()
 *                    returns in PHP, passed through as JSON)
 *   preselectedRaw — raw JSON string in the posted-selection shape, e.g.
 *                    '[{"group_key":"band_width","option_keys":["band_2_00mm"]}]'
 *                    — carried over from the "Select This Setting" step's
 *                    session (see ls_rb_addtobag() / handle_ring_builder_setting()
 *                    in functions.php). Empty string if nothing carried over.
 *   currency       — e.g. "$"
 *   imageBase      — base URL for option images; each option's image is
 *                    {imageBase}/{option_key}.svg
 *   inputName      — the hidden input's `name` (posted with the form) — "extra_features"
 *   mountEl        — the DOM node this instance is mounted into, used to
 *                    check whether this instance lives inside the Ring
 *                    Preview page's own form (to know whether it should
 *                    update that page's separate, page-level price display)
 *                    and to find a variable product's variations_form
 *   productId      — the product ID this instance's prices/upgrades apply
 *                    to (the setting product on the ring builder, or the
 *                    current product on a single-product page) — passed
 *                    straight from PHP's data-product-id, never guessed
 *                    client-side. Used to keep window.LGDExtend's quoted
 *                    plan price in sync with base + upgrades.
 *   simplePrice    — numeric base price for a SIMPLE product (data-simple-price
 *                    from PHP). Ignored for variable products, which get
 *                    their live price from the variations_form's own
 *                    found_variation/reset_data events instead.
 */
export default function RingExtraFeatures({ groups, preselectedRaw, currency, imageBase, inputName, mountEl, productId, simplePrice, cartItemKey }) {
  const [variationPrice, setVariationPrice] = useState(simplePrice || 0)
  const [confirmedSelection, setConfirmedSelection] = useState({}) // { groupKey: [optionKey, ...] }
  const [draftSelection, setDraftSelection] = useState({})
  const [isOpen, setIsOpen] = useState(false)
  const [isAnimatingOpen, setIsAnimatingOpen] = useState(false)
  const modalBodyRef = useRef(null)

  // Parse any carried-over selection once, on mount.
  useEffect(() => {
    if (!preselectedRaw) return
    try {
      const payload = JSON.parse(preselectedRaw)
      const initial = {}
      payload.forEach((g) => {
        initial[g.group_key] = g.option_keys
      })
      setConfirmedSelection(initial)
    } catch (e) {
      // malformed/stale value — ignore rather than break the page
    }
  }, [preselectedRaw])

  const groupEntries = useMemo(() => Object.entries(groups || {}), [groups])

  function isSelected(sel, groupKey, optionKey) {
    return !!(sel[groupKey] && sel[groupKey].indexOf(optionKey) !== -1)
  }

  function toggleOption(groupKey, optionKey, selectionType) {
    setDraftSelection((prev) => {
      const next = { ...prev }
      const current = next[groupKey] || []
      const already = current.indexOf(optionKey) !== -1

      if (selectionType === 'single') {
        // Radio behaviour: clicking the already-selected one deselects it,
        // clicking another replaces the current pick in this group.
        next[groupKey] = already ? [] : [optionKey]
      } else if (already) {
        next[groupKey] = current.filter((k) => k !== optionKey)
      } else {
        next[groupKey] = [...current, optionKey]
      }
      return next
    })
  }

  function priceOf(groupKey, optionKey) {
    return (groups[groupKey] && groups[groupKey].options[optionKey] && groups[groupKey].options[optionKey].price) || 0
  }

  function nameOf(groupKey, optionKey) {
    return (groups[groupKey] && groups[groupKey].options[optionKey] && groups[groupKey].options[optionKey].label) || ''
  }

  function totalAndItems(sel) {
    let total = 0
    const items = []
    Object.entries(sel).forEach(([groupKey, keys]) => {
      keys.forEach((optionKey) => {
        const price = priceOf(groupKey, optionKey)
        total += price
        items.push({ name: nameOf(groupKey, optionKey), price })
      })
    })
    return { total, items }
  }

  const confirmedCalc = useMemo(() => totalAndItems(confirmedSelection), [confirmedSelection, groups])
  const draftCalc = useMemo(() => totalAndItems(draftSelection), [draftSelection, groups])

  const hiddenInputValue = useMemo(() => {
    const payload = Object.entries(confirmedSelection)
      .filter(([, keys]) => keys.length)
      .map(([group_key, option_keys]) => ({ group_key, option_keys }))
    return JSON.stringify(payload)
  }, [confirmedSelection])

  // Keep the Ring Preview page's own page-level price display in sync.
  // That element is outside this component's tree entirely (legacy markup
  // this component doesn't own), so this is a deliberate, narrowly-scoped
  // side effect — only touches it when this instance is actually inside
  // that page's form, never for an instance on a normal product page.
  useEffect(() => {
    if (!mountEl) return
    const insideRingBuilder = mountEl.closest('.ring-builder-form')
    if (!insideRingBuilder) return
    const priceEl = document.getElementById('ring_total_price')
    if (!priceEl) return
    const basePrice = parseFloat(priceEl.getAttribute('data-base-price')) || 0
    priceEl.textContent = currency + (basePrice + confirmedCalc.total).toFixed(2)
  }, [confirmedCalc.total, mountEl, currency])

  // Keep the single-product page's own ".price-display" element in sync.
  // This is the PDP equivalent of the #ring_total_price effect above — that
  // one only fires insideRingBuilder, so without this, selecting an upgrade
  // on a normal product page updates nothing the customer can see (the
  // server-side price/cart total is still correct, but the on-page price
  // never visibly reflects it). variationPrice already holds the right base
  // (live variation price, or simplePrice for a simple product).
  useEffect(() => {
    if (!mountEl) return
    const insideRingBuilder = mountEl.closest('.ring-builder-form')
    if (insideRingBuilder) return // that case is handled by the #ring_total_price effect above
    if (cartItemKey) return // cart page shows price via the item's own "Upgrade" line + badges, not .price-display

    const priceEl = mountEl.closest('form')?.querySelector('.price-display') || document.querySelector('.price-display')
    if (!priceEl) return
    if (variationPrice <= 0) return // no real base price yet (e.g. variable product, no variation picked)

    priceEl.innerHTML = '<span class="price">' + currency + (variationPrice + confirmedCalc.total).toFixed(2) + '</span>'
  }, [confirmedCalc.total, mountEl, currency, variationPrice])

  // Track the current base price for a VARIABLE product's own single-product
  // page (no #ring_total_price element there — that only exists on the ring
  // builder). WooCommerce's own variations_form fires these jQuery events;
  // if this instance isn't inside a variations_form at all (simple product,
  // or ring builder), this is a no-op and variationPrice stays at simplePrice.
  useEffect(() => {
    if (!mountEl) return
    if (mountEl.closest('.ring-builder-form')) return // ring builder has its own price element, handled above

    const formEl = mountEl.closest('form.variations_form') || document.querySelector('form.variations_form')
    const jQ = window.jQuery
    if (!formEl || !jQ) return // not a variable product on this page — keep using simplePrice

    const onFound = (event, variation) => setVariationPrice(parseFloat(variation.display_price) || 0)
    const onReset = () => setVariationPrice(0)

    jQ(formEl).on('found_variation', onFound)
    jQ(formEl).on('reset_data', onReset)

    return () => {
      jQ(formEl).off('found_variation', onFound)
      jQ(formEl).off('reset_data', onReset)
    }
  }, [mountEl])

  // Keep Extend's quoted plan price in sync with base + confirmed upgrades,
  // for all three contexts (ring builder, variable single product, simple
  // single product). Fires on every confirm/remove AND on variation change.
  useEffect(() => {
    if (!mountEl || !window.LGDExtend || !productId || cartItemKey) return

    const insideRingBuilder = mountEl.closest('.ring-builder-form')
    let basePrice = 0

    if (insideRingBuilder) {
      const priceEl = document.getElementById('ring_total_price')
      if (!priceEl) return
      basePrice = parseFloat(priceEl.getAttribute('data-base-price')) || 0
    } else {
      // variationPrice already holds the right value for either case:
      // live variation price if this is a variable product, or the
      // initial simplePrice if there's no variations_form on the page.
      basePrice = variationPrice
    }
    console.log("fff",productId,basePrice + confirmedCalc.total);
    if (basePrice <= 0) return // no real price yet (e.g. no variation picked) — don't quote Extend a bogus total

    const totalCents = Math.round((basePrice + confirmedCalc.total) * 100)
    window.LGDExtend.setActiveProduct(productId, totalCents)
  }, [confirmedCalc.total, mountEl, variationPrice, productId])

  // On page load, if there's a predefined/carried-over selection
  // (preselectedRaw), the effect above can fire before window.LGDExtend
  // exists yet — the SDK script loads/initializes asynchronously, same
  // reason page-ring-preview.php delays its own setActiveProduct call by
  // 1s. Re-assert the correct total once, after that same delay, so Extend
  // ends up with base + upgrades even when nothing ever "changes" after
  // mount to re-trigger the effect above.
  const latestPriceRef = useRef({ basePrice: 0, total: 0 })
  useEffect(() => {
    const insideRingBuilder = mountEl && mountEl.closest('.ring-builder-form')
    let basePrice = 0
    if (insideRingBuilder) {
      const priceEl = document.getElementById('ring_total_price')
      basePrice = priceEl ? (parseFloat(priceEl.getAttribute('data-base-price')) || 0) : 0
    } else {
      basePrice = variationPrice
    }
    latestPriceRef.current = { basePrice, total: confirmedCalc.total }
  })

  useEffect(() => {
    if (!mountEl || !productId || cartItemKey) return

    const timer = setTimeout(() => {
      if (!window.LGDExtend) return
      const { basePrice, total } = latestPriceRef.current
      if (basePrice <= 0) return
      const totalCents = Math.round((basePrice + total) * 100)
      //window.LGDExtend.setActiveProduct(productId, totalCents)
    }, 1000)

    return () => clearTimeout(timer)
    // Intentionally only mountEl/productId/cartItemKey — this should fire
    // once per mount, reading whatever the latest price is at that moment
    // via the ref above, not re-schedule on every selection change.
  }, [mountEl, productId, cartItemKey])

  

  function openModal() {
    setDraftSelection(confirmedSelection) // fresh derivation each open — no leftover state possible
    setIsOpen(true)
    // next tick, so the CSS transition (display:none -> block, then
    // translateX) actually animates instead of jumping straight to open
    requestAnimationFrame(() => requestAnimationFrame(() => setIsAnimatingOpen(true)))
  }

  function closeModal() {
    setIsAnimatingOpen(false)
    setTimeout(() => setIsOpen(false), 250) // match CSS transition duration
  }

  function handleConfirm() {
    // Allow confirming an empty draft — that's how a user removes an
    // upgrade entirely (deselect it in the modal, then confirm).
    setConfirmedSelection(draftSelection)
    closeModal()
    if (cartItemKey) saveCartSelection(draftSelection)
  }

  // Cart-page instances (cartItemKey present, from render_cart_edit_button()
  // in class-ring-extra-features.php) have no <form> submit to ride along
  // with — this is the on-confirm/on-remove save path for those instead.
  // Posts the full selection (server re-verifies and re-prices every
  // option, same as every other path in this component's PHP class) then
  // reloads, matching the existing cart-remove-link UX.
  function saveCartSelection(selection) {
    const jq = window.jQuery
    if (!jq || !window.lgdRefCart) return
    const payload = Object.entries(selection)
      .filter(([, keys]) => keys.length)
      .map(([group_key, option_keys]) => ({ group_key, option_keys }))

    // React cart page (components/cart-page) shows its loader and
    // refreshes in place; otherwise fall back to a full reload.
    const reactCart = !!window.lsCartApp
    if (reactCart) document.dispatchEvent(new CustomEvent('ls:cart-busy'))

    jq.post(window.lgdRefCart.ajaxUrl, {
      action: 'ls_ref_update_upgrades',
      nonce: window.lgdRefCart.updateNonce,
      cart_item_key: cartItemKey,
      extra_features: JSON.stringify(payload),
    }).done((response) => {
      if (response && response.success) {
        if (reactCart) document.dispatchEvent(new CustomEvent('ls:cart-refresh'))
        else window.location.reload()
      } else {
        if (reactCart) document.dispatchEvent(new CustomEvent('ls:cart-idle'))
        alert((response && response.data && response.data.message) || 'Could not update upgrades. Please try again.')
      }
    }).fail(() => {
      if (reactCart) document.dispatchEvent(new CustomEvent('ls:cart-idle'))
      alert('Could not update upgrades. Please try again.')
    })
  }

  function removeConfirmedItem(groupKey, optionKey) {
    setConfirmedSelection((prev) => {
      const next = { ...prev }
      next[groupKey] = (next[groupKey] || []).filter((k) => k !== optionKey)
      if (!next[groupKey].length) delete next[groupKey]
      if (cartItemKey) saveCartSelection(next)
      return next
    })
  }

  // Flat list of { groupKey, optionKey, name, price } for the confirmed
  // selection, used to render removable badges next to the main button.
  const confirmedBadges = useMemo(() => {
    const list = []
    Object.entries(confirmedSelection).forEach(([groupKey, keys]) => {
      keys.forEach((optionKey) => {
        list.push({
          groupKey,
          optionKey,
          name: nameOf(groupKey, optionKey),
          price: priceOf(groupKey, optionKey),
        })
      })
    })
    return list
  }, [confirmedSelection, groups])

  const buttonLabel = confirmedCalc.items.length
    ? `Edit Upgrades (${confirmedCalc.items.length} added, +${currency}${confirmedCalc.total.toFixed(0)})`
    : 'Add Extra Feature'

  return (
    <>
      <div className="row">
        <div className="col-md-12">
          <button
            type="button"
            className={'ls-ref-open-btn' + (confirmedCalc.items.length ? ' has-selection' : '')}
            onClick={openModal}
          >
            <i class="fa fa-sliders" aria-hidden="true"></i> {buttonLabel}
          </button>
          <input type="hidden" name={inputName} value={hiddenInputValue} readOnly />

          {!cartItemKey && confirmedBadges.length > 0 && (
            <div className="ls-ref-badges">
              {confirmedBadges.map((item) => (
                <span key={`${item.groupKey}:${item.optionKey}`} className="ls-ref-badge">
                  {item.name} (+{currency}{Number(item.price).toFixed(0)})
                  <button
                    type="button"
                    className="ls-ref-badge-remove"
                    aria-label={`Remove ${item.name}`}
                    onClick={() => removeConfirmedItem(item.groupKey, item.optionKey)}
                  >
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </button>
                </span>
              ))}
            </div>
          )}
        </div>
      </div>

      {isOpen && createPortal(
        <div
          className={'ls-ref-modal' + (isAnimatingOpen ? ' open' : '')}
          style={{ display: 'block' }}
          onClick={(e) => {
            if (e.target === e.currentTarget) closeModal()
          }}
        >
          <div className="ls-ref-modal-dialog">
            <div className="ls-ref-modal-header">
              <h4>Choose your upgrades</h4>
              <button type="button" className="ls-ref-close" aria-label="Close" onClick={closeModal}>
                &times;
              </button>
            </div>

            <div className="ls-ref-modal-body" ref={modalBodyRef}>
              {groupEntries.map(([groupKey, group]) => (
                <div key={groupKey} className="ls-ref-group">
                  <h5 className="ls-ref-group-title">{group.label}</h5>
                  {group.subtitle && <p className="ls-ref-group-subtitle">{group.subtitle}</p>}
                  <div className="ls-ref-options">
                    {Object.entries(group.options).map(([optionKey, option]) => (
                      <OptionCard
                        key={optionKey}
                        option={option}
                        imageSrc={`${imageBase}/${optionKey}.svg`}
                        currency={currency}
                        selected={isSelected(draftSelection, groupKey, optionKey)}
                        onClick={() => toggleOption(groupKey, optionKey, group.selection_type)}
                      />
                    ))}
                  </div>
                </div>
              ))}
            </div>

            <div className="ls-ref-modal-footer">
              <button type="button" className="ls-ref-cancel-btn" onClick={closeModal}>
                Cancel
              </button>
              <button
                type="button"
                className="ls-ref-confirm-btn"
                disabled={!draftCalc.items.length && !confirmedCalc.items.length}
                onClick={handleConfirm}
              >
                {draftCalc.items.length
                  ? `Upgrade +${currency}${draftCalc.total.toFixed(0)}`
                  : confirmedCalc.items.length
                  ? 'Remove Upgrade'
                  : 'Select Upgrade'}
              </button>
            </div>
          </div>
        </div>,
        document.body
      )}
    </>
  )
}