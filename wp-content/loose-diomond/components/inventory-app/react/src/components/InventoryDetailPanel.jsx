// ─────────────────────────────────────────────────────────────
// InventoryDetailPanel.jsx
// The expandable "inventory-detail" row (wishlist, price, cart
// button(s), video/certificate links, and the DIAMOND DETAILS grid)
// was duplicated almost verbatim between InventoryResults.jsx
// (App.jsx) and InventoryResults2.jsx (App2.jsx). The genuinely
// different parts are:
//   - the diamond image path (App: shape-only, App2: shape+color)
//   - the cart button(s) — App conditionally shows a second
//     "Add To Ring" button (isSettingEligible), App2 always shows
//     just one — so that's a render prop, not something worth
//     forcing into one generic config
//   - which fields appear in the DIAMOND DETAILS grid (App has
//     Color + Growth Type + an Additional Details toggle for
//     Girdle/Crown/Pavilion/Fluorescence/Polish/Symmetry; App2 has
//     Intensity instead of Color and no Additional Details toggle)
// Everything else (layout, classes, close button, video/certificate
// links, shipping notes, "already in cart" message) lives here once.
// ─────────────────────────────────────────────────────────────
import React from 'react';
import { openDetailForItem } from '../lsFunction';
import { templateUrl } from './inventoryHelpers';

// JS port of ls_est_delivery_date() (inc/comman-function.php):
// today + delivery_days (falls back to 7, same as diamond-detail-render.php),
// pushed to Monday if it lands on Sat/Sun. Format matches PHP 'l, M j'.
function estDeliveryDate(deliveryDays) {
  const days = parseInt(deliveryDays, 10) > 0 ? parseInt(deliveryDays, 10) : 7;
  const d = new Date();
  d.setDate(d.getDate() + days);
  const dow = d.getDay(); // 0 = Sunday, 6 = Saturday
  if (dow === 6) d.setDate(d.getDate() + 2);
  else if (dow === 0) d.setDate(d.getDate() + 1);
  const dayName = d.toLocaleDateString('en-US', { weekday: 'long' });
  const month = d.toLocaleDateString('en-US', { month: 'short' });
  return `${dayName}, ${month} ${d.getDate()}`;
}

export default function InventoryDetailPanel({
  item,
  items,
  openDetail,
  setOpenDetail,
  imageSrc,
  isInWishlist,
  toggleWishlist,
  cartButtons,
  cartError,
  cartProductIds,
  detailRows,
  additionalDetails, // optional: { open, onToggle, rows }
}) {
  // Ring builder (white: ringbuilder, fancy: ringbuilder_c) — a diamond already
  // in the cart can still be paired with a setting, so skip the "already in cart" note.
  const inventoryType = window.INVENTORY_TYPE ? window.INVENTORY_TYPE : 'default';
  const isRingBuilder = inventoryType === 'ringbuilder' || inventoryType === 'ringbuilder_c';
  console.log("cartProductIds",cartProductIds);

  // pids of diamonds already set in a ring in the cart (from PHP: cartRingProductIds)
  const ringIdsRaw = (window.LD_THEME && window.LD_THEME.cartRingProductIds) || [];
  const productcartIdsRaw = (window.LD_THEME && window.LD_THEME.cartProductIds) || [];
  const ringIds = Array.isArray(ringIdsRaw)
    ? ringIdsRaw
    : (typeof ringIdsRaw === 'string'
        ? ringIdsRaw.split(',').map(s => s.trim()).filter(Boolean)
        : (ringIdsRaw && typeof ringIdsRaw === 'object' ? Object.values(ringIdsRaw) : []));
  const isDiamondInCartRing = (pid) => !!(pid && ringIds.map(Number).includes(Number(pid)));

  const priceNow = window.themeData.WEBSITE_TYPE === 'RETAIL'
    ? ' $' + (Number(item.price)).toFixed(0)
    : ' $' + Number(item.price).toFixed(0);
  const priceWas = window.themeData.WEBSITE_TYPE === 'RETAIL'
    ? ' $' + (Number(item.price) / (100 - 30) * 100).toFixed(0)
    : ' $' + Number(item.org_price).toFixed(0);

  return (
    <div className="py_40 position-relative">
      <div
        className="ls_closedtl"
        role="button"
        tabIndex={0}
        onClick={() => openDetailForItem(null, items, setOpenDetail)}
        onKeyDown={(e) => { if (e.key === 'Enter' || e.key === ' ') openDetailForItem(null, items, setOpenDetail); }}
      >
        <img src={`${templateUrl}/aj_custom/inventory/x.svg`} alt="Close" />
      </div>
      <div className="row m-md-0">
        <div className="col-md-6">
          <div className="ls_wishlist_dmnd text-center">
            <img src={imageSrc} alt={item.shape} />
            <h5 className="price fw-400">
              {priceNow}
              {item.org_price !== '' && item.org_price !== 0 && (
                <del className="ms-1 text-light-gray-secondary font_size_13">{priceWas}</del>
              )}
            </h5>
            <h5 className="fw-600 mb_20">
              {((item.title || item.shape_title) ?? '')
                .toLowerCase()
                .replace(/\b\w/g, c => c.toUpperCase())}{' '}
              Cut Diamond
            </h5>

            <button
              className={`wish-btn btn p-0 ms-2 me-2 ${isInWishlist(item) ? 'active' : ''}`}
              onClick={(e) => { e.preventDefault(); e.stopPropagation(); toggleWishlist(item.id); }}
              aria-label={isInWishlist(item) ? 'Remove from wishlist' : 'Add to wishlist'}
            >
              <i className={`fa-heart ${isInWishlist(item) ? 'fa-solid text-danger' : 'fa-regular'} fa-2x`}></i>
            </button>

            {cartButtons}

            <div class="d-flex align-items-center justify-content-center gap-2">
              {item.video ? (
                <a
                  href={item.video.startsWith('http://') || item.video.startsWith('https://') ? item.video : `https://${item.video.replace(/^\/+/, '')}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="certilink text-decoration-none"
                >
                  <i className="fa-solid fa-circle-play"></i> Play Video
                </a>
              ) : (
                <a
                  href="#"
                  className="certilink text-decoration-none js-video-request-trigger"
                  data-bs-toggle="modal"
                  data-bs-target="#videoRequestModal"
                  data-sku={item.sku}
                  onClick={(e) => e.preventDefault()}
                >
                  <i className="fa-solid fa-circle-play"></i> Request Video
                </a>
              )}
              
              {item.certificate?.trim() && (
                <>
               <span className="mt-3 mx-2">|</span> <a href={item.certificate} className="certilink text-decoration-none" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-eye me-1"></i>View Certificate</a>
               </>
              )}
            </div>

            {cartError && cartError[item.id] ? (
              <div className="mt-2 tbl_already_in_cart">{cartError[item.id]}</div>
            ) : (
            <>
              {
              (isDiamondInCartRing(item.pid) ||
                (productcartIdsRaw && item.pid && productcartIdsRaw.map(Number).includes(Number(item.pid)))) && (
                <div className="mt-2 tbl_already_in_cart">This product is already in your cart</div>
              )
              }
                </>
            )}

            <div className="d-flex align-items-center justify-content-center flex-column gap_5">
              <div className="est_delivery_date mt-2 mb-0">
                <p className="mb-0">
                  {Number(item.price) > 500 ? 'Free Insured ' : ' Insured '} shipping by <strong>{estDeliveryDate(item.delivery_days)}</strong>
                  <i
                    className="fa-regular fa-circle-question ms-1 text-muted"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Estimated delivery date. Actual delivery may vary based on your shipping location and carrier."
                    style={{ cursor: 'pointer' }}
                  ></i>
                </p>
                <p className="mb-0">
                  if you order in the next 15 hours.{' '}
                  <a
                    href="javascript:void(0)"
                    onClick={() => {
                      if (typeof window.openZohoChat === 'function') {
                        window.openZohoChat('Hi, I need this diamond sooner than the estimated delivery date. Product page: ' + window.location.href);
                      }
                    }}
                  >
                    Need it sooner?
                  </a>
                </p>
              </div>
              <h6 className="dmnd_msg mb-4 mb-md-0  mt-2">
                Diamond photo is for visual purposes only.
                {item.video && (<>&nbsp;Play Video for a real diamond.</>)}
              </h6>
            </div>
          </div>
        </div>

        <div className="col-md-6">
          <p className="fw-600 mb_10 tb_d_title">DIAMOND DETAILS</p>

          {detailRows.map((row) => (
            <div className="diamond_detail" key={row.label}>
              <div className="row">
                <div className="col-4 col-md-3">
                  <div className="dttl">
                    <p className="mb-0">{row.label}</p>
                  </div>
                </div>
                <div className="col-8 col-md-9">
                  <div className="odesc">
                    <h6>{row.value}</h6>
                    {row.desc && <span>{row.desc}</span>}
                  </div>
                </div>
              </div>
            </div>
          ))}

          {additionalDetails && (() => {
            const isEmptyValue = (v) =>
              v === 0 ||
              v === '0' ||
              v === '' ||
              v === null ||
              v === undefined ||
              v === '\u2014';

            return (
              <>
                <div className="diamond_detail dd-more-toggle-row">
                 <div className="odesc">
                  <button
                    type="button"
                    className={`dd-more-toggle${additionalDetails.open ? ' open' : ''}`}
                    aria-expanded={!!additionalDetails.open}
                    onClick={additionalDetails.onToggle}
                  >
                    <span>Additional Details</span>
                    <i className="fa-solid fa-chevron-down"></i>
                  </button>
                </div>
                </div>

                {additionalDetails.open && (
                  <div className="dd-more-grid">
                    {additionalDetails.rows.map((row) => (
                      <div className="dd-more-grid-item" key={row.label}>
                        <p className="dd-more-grid-label mb-0">{row.label}</p>
                        <p className="dd-more-grid-value mb-0">{isEmptyValue(row.value) ? '-' : row.value}</p>
                      </div>
                    ))}
                  </div>
                )}
              </>
            );
          })()}
        </div>
      </div>
    </div>
  );
}