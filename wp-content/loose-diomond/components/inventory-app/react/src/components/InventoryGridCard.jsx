// ─────────────────────────────────────────────────────────────
// InventoryGridCard.jsx
// The grid/card markup was identical between InventoryResults.jsx
// (App.jsx) and InventoryResults2.jsx (App2.jsx) — image wrapper,
// badge cluster (shipping icons + wishlist), sale badge, title,
// subtitle, price. Only the image src and the click behavior
// actually differ between the two apps, so those are the only
// things passed in; everything else lives here once.
// ─────────────────────────────────────────────────────────────
import React from 'react';
import { templateUrl } from './inventoryHelpers';

export default function InventoryGridCard({
  item,
  cutLabel,
  colorLabel,
  clarityLabel,
  displayPrice,
  imageSrc,
  imageFallbackSrc,
  href,
  isInWishlist,
  toggleWishlist,
  // Optional: only App2 (colored diamonds) shows an Add To Ring/Bag
  // button on the grid card — App.jsx's grid intentionally has none.
  // Pass all of these together to enable it.
  isLoadingCart,
  inventoryType,
  cartProductIds,
  handleAddToCartClick,
}) {
  const showCartAction = typeof handleAddToCartClick === 'function';
  return (
    <a
      href={href}
      className={`card oc-invt-card h-100 text-center position-relative text-decoration-none ${item.sale == 1 ? 'issaleitem' : ''}`}
    >
      <div className="card-body d-flex flex-column align-items-start">
        <div className="mb-2 position-relative bg_light_blue  card-body-img-wrap">
          <div className="position-relative w-100">
            {imageSrc && (
              <img
                src={imageSrc}
                alt={item.shape_title}
                style={{ objectFit: 'contain' }}
                className="oc-grid-img"
                onError={(e) => { e.currentTarget.src = imageFallbackSrc; }}
              />
            )}
          </div>

          <div className="oc-invt-card-badge position-absolute top-0 end-0 m-2 d-flex align-items-center gap-2" >
            {item.shipping == 1 && (
              <span className="oc-invt-card-badge-con"> <img src={`${templateUrl}/aj_custom/inventory/delivery.svg`} alt="Ships within 3 days" title="Ship Within 3 Days" /></span>
            )}
            {item.shipping == 2 && !(item.vendor_country == "0" && item.sale == 1) && (
              <span  className="oc-invt-card-badge-con"> <img src={`${templateUrl}/aj_custom/inventory/space-rocket.svg`} alt="Overnight" title="Overnight Delivery Available" /></span>
            )}
            <span  className="oc-invt-card-badge-con"  onClick={(e) => { e.preventDefault(); e.stopPropagation(); toggleWishlist(item.id); }}> <i className={`fa-heart ${isInWishlist(item) ? 'fa-solid text-danger' : 'fa-regular'} fa-lg`}></i> </span>
          </div>
          <div className="position-absolute top-0 start-0 m-2 d-flex align-items-center gap-2" style={{ zIndex: 2 }}>
            {item.sale == 1 && <span className="oc-grid-sale"><img src={`${templateUrl}/assets/image/header/fire.gif`}/>
              Sale</span>}
          </div>
        </div>

        <div className="fw-medium mb-1 oc-grid-title">
          {item.carat} Carat · {colorLabel} · {clarityLabel}
        </div>
        <div className="fw-400 mb-1 oc-grid-sub-title">
          {cutLabel}  · {item.certificate_type == 0 && 'Non Certified'}
                        {item.certificate_type == 1 && 'IGI'}
                        {item.certificate_type == 2 && 'GIA'}
                        {item.certificate_type == 3 && 'GCAL'}
                        {item.certificate_type == 4 && 'Matching Pair'}
        </div>

        <div className="d-flex align-items-center justify-content-center gap-1 mb-3 mt-auto  oc-grid-price">
          <span className="fw-500">{displayPrice}</span>
          {item.org_price !== '' && item.org_price !== 0 && (
            <del className="text-light-gray-secondary fw-500 ">
              was {
                window.themeData.WEBSITE_TYPE === 'RETAIL'
                  ? '$' + (Number(item.price) / (100 - 30) * 100).toFixed(0)
                  : '$' + Number(item.org_price).toFixed(0)
              }
            </del>
          )}
        </div>

        
      </div>
    </a>
  );
}
