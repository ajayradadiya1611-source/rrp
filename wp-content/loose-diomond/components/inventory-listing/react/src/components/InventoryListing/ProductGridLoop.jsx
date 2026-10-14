// ─────────────────────────────────────────────────────────────
// ProductGridLoop.jsx
// Extracted from App3.jsx: renders the products.map() grid — each
// product card (image slider, wishlist button, price) plus the
// "Looking For Custom Setting?" banner card that's inserted after
// every 7th product. Pulled out so App3.jsx's render body stays
// readable; behavior/markup is unchanged from the original inline
// JSX.
// ─────────────────────────────────────────────────────────────
import React, { useState } from 'react';
import Slider from 'react-slick';

export default function ProductGridLoop({
  products,
  templateUrl,
  categorySlug,
  withOrdering,
  sliderSettings,
  sliderRefs,
  wishlistIds,
  keyFor,
  toggleWishlist,
  bannerImages,
}) {
  // Selected metal per product card: { [productId]: swatch }
  const [activeMetal, setActiveMetal] = useState({});

  // Same colour mapping as .dr-swatches on the static product cards
  const swatchClass = (label = '') => {
    const lc = label.toLowerCase();
    if (lc.includes('rose')) return 'dr-swatch--rg';
    if (lc.includes('yellow')) return 'dr-swatch--yg';
    if (lc.includes('platinum')) return 'dr-swatch--pl';
    return 'dr-swatch--w';
  };

  return (
    <>
    <div className="row collection-row">
      {products.map((product, index) => (
        <React.Fragment key={product.id}>
          <div className="col-md-3 col-6">
            <div
              className={`product_image_wrapper  ${product.is_sale_diamond == 1 ? ' is_sale_diamond' : ''}`}
              onMouseEnter={() => sliderRefs.current[product.id]?.slickPlay()}
              onMouseLeave={() => sliderRefs.current[product.id]?.slickPause()}
            >
              {product.product_badge && (
                <div className="product-badge-wrapper">
                  {product.product_badge}
                </div>
              )}
              <a className="product-content-image" href={activeMetal[product.id]?.link || product.link}>
                <Slider
                  {...sliderSettings}
                  key={activeMetal[product.id]?.slug || 'default'} /* remount on metal change so slick resets cleanly */
                  className="ring_slick_slider"
                  autoplay={false}
                  ref={(el) => (sliderRefs.current[product.id] = el)}
                >
                  {(activeMetal[product.id]?.images?.length ? activeMetal[product.id].images : product.gallery_images || []).map((image, imgIndex) => (
                    <div key={image || imgIndex}>
                      <div className="product-content-image-inner">
                        <img src={image} alt={product.title} className="img-fluid" />
                      </div>
                    </div>
                  ))}
                </Slider>
                <div className="oc-product_image_delivery_left">
                  {product.delivery == 1 && (
                      <>
                        <span 
                               className="d-flex gap-1"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-original-title="Shipped in 2 business days">
                          <img
                              className="ready-to-ship-icon"
                              src={`${templateUrl}/aj_custom/category/ready-to-ship-blue.svg`}
                              alt="Ready to Ship"
                          />
                          <span className="ready-to-ship-text">
                              Ready to Ship
                          </span>
                        </span>
                      </>
                  )}
              </div>
              </a>
              <div className="product_details mt-0">
                {product.metal_swatches?.length > 0 && (
                  <div className="dr-swatches mt-0 mb-2">
                    {product.metal_swatches.map((sw, i) => {
                      const current = activeMetal[product.id];
                      const isActive = current ? current.slug === sw.slug : i === 0;
                      return (
                        <span
                          key={sw.slug}
                          role="button"
                          tabIndex={0}
                          title={sw.label}
                          aria-label={sw.label}
                          className={`dr-swatch ${swatchClass(sw.label)}${isActive ? ' is-active' : ''}`}
                          onClick={() => setActiveMetal((prev) => ({ ...prev, [product.id]: sw }))}
                          onKeyDown={(e) => {
                            if (e.key === 'Enter' || e.key === ' ') {
                              e.preventDefault();
                              setActiveMetal((prev) => ({ ...prev, [product.id]: sw }));
                            }
                          }}
                        />
                      );
                    })}
                  </div>
                )}
                <div className="d-flex align-items-start justify-content-between">
                  <a href={activeMetal[product.id]?.link || product.link}>{product.title}</a>
                  <button
                    className={`wish-btn btn p-0 ms-2 ${wishlistIds.includes(keyFor(product.id)) ? 'active' : ''}`}
                    onClick={(e) => { e.preventDefault(); e.stopPropagation(); toggleWishlist(product.id); }}
                    aria-label={wishlistIds.includes(keyFor(product.id)) ? 'Remove from wishlist' : 'Add to wishlist'}
                  >
                    <i className={`fa-heart ${wishlistIds.includes(keyFor(product.id)) ? 'fa-solid text-danger' : 'fa-regular'} fa-lg`}></i>
                  </button>
                </div>
                <div className="ls_stng_price">
                  {(() => {
                    const m = activeMetal[product.id];
                    const price = m?.price ?? product.price;
                    const org = m ? m.org_price : product.org_price;
                    return (
                      <>
                        <span className="ls_sale_price  me-1">${price}</span>
                        {org && org !== price && (
                          <span className="ls_rglr_price">${org}</span>
                        )}
                      </>
                    );
                  })()}
                </div>
                {categorySlug === 'engagement-rings' && (
                  <div className="ls_stng_txt">(Setting Price)</div>
                )}
              </div>
            </div>
          </div>
          {/* Custom Card after every 7th product */}
          {((index + 1) % 7 === 0) && withOrdering && (
            <div className="col-md-3 col-6" key={`custom-${index}`}>
              {(() => {
                // Count which banner occurrence this is
                const bannerIndex = Math.floor((index + 1) / 7) - 1;
                const bannerImage = bannerImages[bannerIndex % bannerImages.length];

                console.log('Banner:', {
                  productIndex: index,
                  bannerIndex,
                  bannerImage,
                });

                return (
                  <div
                    className="pcat_bg bg-dark"
                    style={{
                      backgroundImage: `url(${templateUrl}/aj_custom/category-banner/${categorySlug}/${bannerImage})`,
                    }}
                  >
                    <h4 className="text-center">
                      Looking For Custom Setting?
                    </h4>
                    <button
                      className="btn"
                      data-bs-toggle="modal"
                      data-bs-target="#customSettingModal"
                    >
                      Yes, Please
                    </button>
                  </div>
                );
              })()}
            </div>
          )}
        </React.Fragment>
      ))}
    </div>
    </>
  );
}
