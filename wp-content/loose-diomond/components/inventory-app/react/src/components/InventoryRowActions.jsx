// ─────────────────────────────────────────────────────────────
// InventoryRowActions.jsx
// The wishlist heart + "View" link cell was duplicated between
// InventoryResults.jsx and InventoryResults2.jsx's table rows.
// App.jsx's version also has a separate mobile-only chevron-toggle
// variant that App2.jsx's doesn't have, so that part is optional via
// `showMobileArrow`. `arrowOnly` goes further — no wishlist/View at
// all, just the chevron toggle, at every screen size.
// ─────────────────────────────────────────────────────────────
import React from 'react';
import { openDetailForItem } from '../lsFunction';

export default function InventoryRowActions({
  item,
  items,
  openDetail,
  setOpenDetail,
  isInWishlist,
  toggleWishlist,
  showMobileArrow = false,
  arrowOnly = false,
}) {
  const arrowButton = (
    <button
      className={`btn p-0 border-0 bg-transparent oc_view_arrow${openDetail === item.id ? ' open' : ''}`}
      onClick={(e) => {
        e.preventDefault();
        openDetailForItem(openDetail === item.id ? null : item, items, setOpenDetail);
      }}
      aria-label={openDetail === item.id ? 'Hide Details' : 'View Details'}
      aria-expanded={openDetail === item.id}
    >
      <i className={`fa-solid fa-chevron-${openDetail === item.id ? 'up' : 'down'}`}></i>
    </button>
  );

  if (arrowOnly) {
    return (
      <div className="d-flex justify-content-center">
        {arrowButton}
      </div>
    );
  }

  const wishlistButton = (
    <button
      className={`wish-btn btn p-0 ms-2 me-2 ${isInWishlist(item) ? 'active' : ''}`}
      onClick={(e) => { e.preventDefault(); e.stopPropagation(); toggleWishlist(item.id); }}
      aria-label={isInWishlist(item) ? 'Remove from wishlist' : 'Add to wishlist'}
    >
      <i className={`fa-heart ${isInWishlist(item) ? 'fa-solid text-danger' : 'fa-regular'} fa-2x`}></i>
    </button>
  );

  const viewLink = (
    <a
      href="#"
      className="view_btn text-decoration-none"
      onClick={(e) => {
        e.preventDefault();
        openDetailForItem(openDetail === item.id ? null : item, items, setOpenDetail);
      }}
    >
      View
    </a>
  );

  if (!showMobileArrow) {
    return (
      <>
        {wishlistButton}
        {viewLink}
      </>
    );
  }

  return (
    <>
      {/* Desktop */}
      <div className="d-none d-md-flex align-items-center  justify-content-end">
        {wishlistButton}
        {viewLink}
      </div>

      {/* Mobile */}
      <div className="d-flex d-md-none justify-content-center">
        {arrowButton}
      </div>
    </>
  );
}
