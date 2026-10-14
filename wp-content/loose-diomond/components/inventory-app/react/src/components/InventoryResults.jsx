// ─────────────────────────────────────────────────────────────
// InventoryResults.jsx
// Combines InventoryTableView.jsx (list) and InventoryGridView.jsx
// (grid) into one component that switches on `viewMode`. Both JSX
// bodies are unchanged from their original files — this file just
// lets App.jsx render <InventoryResults viewMode={viewMode} ... />
// once instead of managing two separate components/imports.
// ─────────────────────────────────────────────────────────────
import React, { useState } from 'react';
import { TableLoadingRow, TableEmptyRow, TableAskNowRow, GridLoadingBlock, GridEmptyBlock, GridAskNowBlock } from './InventoryStateBlocks';
import InventoryGridCard from './InventoryGridCard';
import InventoryTableHead from './InventoryTableHead';
import InventoryRowActions from './InventoryRowActions';
import InventoryDetailPanel from './InventoryDetailPanel';
import { cutLabels, colorLabels, clarityLabels, lsFunction, openDetailForItem, isSettingEligible, isCustomSettingShape, buildCustomSettingMsg } from '../lsFunction';
import { fluorescenceLabels, polishSymmetryLabels, girdleLabels, culetLabels } from './FilterSlider';
import { templateUrl } from './inventoryHelpers';

export default function InventoryResults({
  items,
  filter,
  viewMode,
  openDetail,
  setOpenDetail,
  isLoading,
  skuInput,
  isLoadingCart,
  inventoryType,
  cartProductIds,
  isInCartRing,
  cartError,
  widgetRef,
  toggleSorting,
  isInWishlist,
  toggleWishlist,
  handleAddToCartClick,
  handleClearFilter,
  showAskNow = true,
}) {
  // Per-row "Additional Details" reveal (Girdle Thickness, Crown Angle,
  // Pavilion Angle, Fluorescence, Polish, Symmetry) — list view only.
  const [showMoreDetails, setShowMoreDetails] = useState({});
  const toggleMoreDetails = (id) => setShowMoreDetails(prev => ({ ...prev, [id]: !prev[id] }));

  if (viewMode === 'grid') {
    return (
          <div className="oc-inventory-grid mb-3">
            <div className="row g-1 g-md-2">
              {items.map((item) => {
                const cutLabel = cutLabels[item.cut - 1] || item.cut;
                const colorLabel = colorLabels[item.color - 1] || item.color;
                const clarityLabel = clarityLabels[item.clarity - 1] || item.clarity;
                const shapeName = item.shape_title
                  ?.toLowerCase()
                  ?.trim()
                  ?.replace(/\s+/g, '_') || '';
                const displayPrice = window.themeData.WEBSITE_TYPE === 'RETAIL'
                  ? '$' + Number(item.price).toFixed(0)
                  : '$' + Number(item.price).toFixed(0);
                const shapeImageSrc = shapeName ? `${templateUrl}/assets/image/ls-inventory/shape-diamond/png/${shapeName}.png` : '';
                const shapeImageFallback = `${templateUrl}/assets/image/ls-inventory/shape-diamond/svg/${shapeName}.svg`;
                return (
                  <div className="col-6 col-md-4 col-lg-3" key={item.id} data-sku={item.sku}>
                    <InventoryGridCard
                      item={item}
                      cutLabel={cutLabel}
                      colorLabel={colorLabel}
                      clarityLabel={clarityLabel}
                      displayPrice={displayPrice}
                      imageSrc={shapeImageSrc}
                      imageFallbackSrc={shapeImageFallback}
                      href={`/diamond/${item.sku}/`}
                      isInWishlist={isInWishlist}
                      toggleWishlist={toggleWishlist}
                    />
                  </div>
                );
              })}
            </div>

            {items.length === 0 && !isLoading && (
              <GridEmptyBlock handleClearFilter={handleClearFilter} showAskNow={showAskNow} />
            )}

            {(showAskNow && !isLoading && !skuInput && items.length > 0) && (
              <GridAskNowBlock />
            )}

            {isLoading && <GridLoadingBlock />}
          </div>
  
    );
  }

  return (
        <div className={`table-responsive oc-inventory-table ${viewMode === 'grid' ? 'd-none' : ''}`}>
          <table className="table table-striped prdctlist">
            <InventoryTableHead
              filter={filter}
              toggleSorting={toggleSorting}
              columns={[
                { key: 'shape', label: 'Shape', sortField: 'shape' },
                { key: 'carat', label: 'Carat', sortField: 'carat' },
                { key: 'certificate', label: 'Certificate', className: 'oc-cefrticate-th' },
                { key: 'cut', label: 'Cut', sortField: 'cut' },
                { key: 'color', label: 'Color', sortField: 'color' },
                { key: 'clarity', label: 'Clarity', sortField: 'clarity' },
                { key: 'price', label: <>Price <span className="ls-price-th-unit">(US$)</span></>, sortField: 'price' },
              ]}
            />
            <tbody>
              {items.map((item, index) => {
                const cutLabel = cutLabels[item.cut - 1] || item.cut;
                const colorLabel = colorLabels[item.color - 1] || item.color;
                const clarityLabel = clarityLabels[item.clarity - 1] || item.clarity;
                const desc = lsFunction(cutLabel, colorLabel, clarityLabel);
                const shapeName = item.shape_title
  ?.toLowerCase()
  ?.trim()
  ?.replace(/\s+/g, '_') || '';
  const shapeNameImage = item.shape_title
  ?.toLowerCase()
  ?.trim()
  ?.replace(/\s+/g, '_') || '';
  //console.log(shapeNameImage);
                  return (
                  <React.Fragment key={item.id}>
                    <tr
                      data-sku={item.sku}
                      className={`inventory-row ${openDetail === item.id ? 'open' : ''} ${item.sale == 1 ? 'issaleitem' : ''}`}
                      onClick={(e) => {
                        e.preventDefault();
                        openDetailForItem(openDetail === item.id ? null : item, items, setOpenDetail);
                      }}
                    >
                      <td className="oc-td-shape-name">
                        <div className="d-flex flex-column flex-md-row align-items-center text-center justify-content-center justify-content-md-start gap-2">
                          {shapeName && (
                            <img className="oc-shape_ci" src={`${templateUrl}/assets/image/ls-inventory/shape-diamond/svg/${shapeName}.svg`}  />
                          )}
                          <span className='tb_ls_shape_name'>{item.shape_title.toLowerCase()}</span>
                          {(item.sale == 1 || item.shipping == 1 || (item.shipping == 2 && !(item.vendor_country == "0" && item.sale == 1))) && (
                            <>
                              {item.sale == 1 && (
                                <span className='tb_ls_sale'>Sale</span>
                              )}
                              {item.shipping == 1 && (
                                <img src={`${templateUrl}/aj_custom/inventory/delivery.svg`}  data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-original-title="Ship Within 3 Days" className='tbl_express_shipping'  />
                              )}
                              {item.shipping == 2 && !(item.vendor_country == "0" && item.sale == 1) && (
                                <img src={`${templateUrl}/aj_custom/inventory/space-rocket.svg`} className='tbl_overnight_shipping' data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-original-title="Overnight Delivery Available — Order Before 11:00 AM EDT" />
                              )}
                            </>
                          )}
                        </div>
                      </td>

                      <td className="oc-carat-td">{item.carat}</td>
                      <td  className="oc-cefrticate-td">{item.certificate_type == 0 && (
                                          <>
                                            Non Certified
                                            
                                          </>
                                        )}
                                        {item.certificate_type == 1 && 'IGI'}
                                        {item.certificate_type == 2 && 'GIA'}
                                        {item.certificate_type == 3 && 'GCAL'}
                                        {item.certificate_type == 4 && 'Matching Pair'}</td>
                      <td className="oc-cut-td">{cutLabel}</td>
                      <td>{colorLabel}</td>
                      <td className="oc-clarity-td">{clarityLabel}</td>
                      <td className="oc-price-td">
                        <div className="d-flex flex-column flex-md-row gap-1">
                        <span className="item-rg-price">
                        {
                            window.themeData.WEBSITE_TYPE === 'RETAIL'
                              ? ' $' + (Number(item.price)).toFixed(0)
                              : ' $' + Number(item.price).toFixed(0)
                          }
                         
                        </span>
                        {item.org_price !== '' && item.org_price !== 0 && (
                          <del className="text-light-gray-secondary font_size_11">
                          {
                            window.themeData.WEBSITE_TYPE === 'RETAIL'
                              ? ' $' + (Number(item.price)/ (100-30) * 100).toFixed(0)
                              : ' $' + Number(item.org_price).toFixed(0)
                          }
                          </del>
                        )}
                        
                        </div>
                      </td>
                      <td>
                        <InventoryRowActions
                          item={item}
                          items={items}
                          openDetail={openDetail}
                          setOpenDetail={setOpenDetail}
                          isInWishlist={isInWishlist}
                          toggleWishlist={toggleWishlist}
                          showMobileArrow
                        />
                      </td>
                    </tr>
                    <tr className={`inventory-detail ${openDetail === item.id ? '' : 'd-none'}`} proid={item.pid}>
                      <td colSpan={8}>
                        <InventoryDetailPanel
                          item={item}
                          items={items}
                          openDetail={openDetail}
                          setOpenDetail={setOpenDetail}
                          imageSrc={`${templateUrl}/assets/image/ls-inventory/shape-diamond/png/${shapeNameImage.toLowerCase()}.png`}
                          isInWishlist={isInWishlist}
                          toggleWishlist={toggleWishlist}
                          cartError={cartError}
                          cartProductIds={cartProductIds}
                          cartButtons={(() => {
                           const showRingBtn =
                            item.certificate_type != 4 &&
                            inventoryType !== 'ringbuilder' &&
                            isSettingEligible(item) &&
                            !(typeof isInCartRing === 'function' && isInCartRing(item.pid)); // mirrors PHP's $is_in_cart_ring
                          const showCustomBtn =
                            item.certificate_type != 4 &&
                            inventoryType !== 'ringbuilder' &&
                            isCustomSettingShape(item) &&
                            !(cartProductIds && item.pid && cartProductIds.map(Number).includes(Number(item.pid)));

                            const hasMultipleButtons = showRingBtn || showCustomBtn;
                            const isAlreadyInCart = !!(cartProductIds && item.pid && cartProductIds.map(Number).includes(Number(item.pid)));

                            return (
                              <div class="text-center d-flex flex-row align-items-center justify-content-center gap-2">
                             
                                {showRingBtn ? (
                                  <a href="#" className="lscartbtn ls_add_to_cart "
                                    onClick={(e) => { e.preventDefault(); handleAddToCartClick(item.pid, item.id, 'ringbuilder'); }}
                                  >
                                    {isLoadingCart === `${item.id}:ringbuilder` ? (
                                      <>Processing &nbsp;<span className="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></>
                                    ) : (
                                      "Add To Ring"
                                    )}
                                  </a>
                                ) : (
                                  <>
                                    {showCustomBtn && (
                                      <a  href="#" data-bs-toggle="modal" data-bs-target="#customSettingModal" className="lscartbtn ls_add_to_cart"
                                        
                                      >
                                        Ask Custom Setting
                                      </a>
                                    )}
                                  </>
                                )}
                             
                                <a href="#" className={`lscartbtn ls_add_to_cart${hasMultipleButtons ? ' aj-white-btn-color' : ''}`}
                                  onClick={(e) => {
                                    e.preventDefault();
                                    // Already in cart (incl. ring builder) → go straight to the cart.
                                    if (isAlreadyInCart) { window.location.href = '/cart'; return; }
                                    handleAddToCartClick(item.pid, item.id);
                                  }}
                                >
                                  {isLoadingCart === `${item.id}:${inventoryType}` ? (
                                    <>Processing &nbsp;<span className="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></>
                                  ) : isAlreadyInCart ? (
                                    "Go To Cart"
                                  ) : inventoryType === 'ringbuilder' ? (
                                    "Add To Setting"
                                  ) : (
                                    "Add To Bag"
                                  )}
                                </a>
                              </div>
                            );
                          })()}
                          detailRows={[
                            { label: 'CARAT', value: item.carat },
                            { label: 'SIZE (MM)', value: item.mm },
                            { label: 'CUT', value: cutLabel, desc: desc.cut },
                            { label: 'COLOR', value: colorLabel, desc: desc.color },
                            { label: 'CLARITY', value: clarityLabel, desc: desc.clarity },
                            { label: 'IN THE BOX', value: (
                              <>
                                {item.certificate_type == 0 && (
                                  <>
                                    Diamond (Non Certified)
                                    <i
                                      className="fa-regular fa-circle-question text-decoration-none ms-2"
                                      data-bs-toggle="tooltip"
                                      data-bs-placement="top"
                                      title="The diamond doesn't include a GIA or IGI grading report. The quality is graded by the manufacturer."
                                      aria-label="Non Certified Info"
                                    ></i>
                                  </>
                                )}
                                {item.certificate_type == 1 && 'Diamond, IGI Certificate'}
                                {item.certificate_type == 2 && 'Diamond, GIA Certificate'}
                                {item.certificate_type == 3 && 'Diamond, GCAL Certificate'}
                                {item.certificate_type == 4 && 'Diamond, Matching Pair'}
                                {item.certificate?.trim() && item.certificate_type == 2  && (
                                  <i
                                      className="fa-regular fa-circle-question text-decoration-none ms-2"
                                      data-bs-toggle="tooltip"
                                      data-bs-placement="top"
                                      title="GIA lab-grown diamonds include a digital grading report. Printed copies may be available upon request from GIA at no additional cost."
                                      aria-label="Certified Info"
                                    ></i>
                                )}
                              </>
                            ) },
                            { label: 'TABLE & DEPTH', value: item.av_table + '% , ' + item.depth + '%' },
                            { label: 'L/W RATIO', value: item.lwratio },
                            { label: 'SKU', value: item.sku },
                            { label: 'GROWTH TYPE', value: item.dtype },
                          ]}
                          additionalDetails={{
                            open: !!showMoreDetails[item.id],
                            onToggle: () => toggleMoreDetails(item.id),
                            rows: [
                              { label: 'GIRDLE THICKNESS', value: girdleLabels[Math.round(item.girdle_thickness) - 1] ?? (item.girdle_thickness ?? '\u2014') },
                              { label: 'CROWN ANGLE', value: item.crown_angle != null && item.crown_angle !== '' ? `${item.crown_angle}\u00b0` : '\u2014' },
                              { label: 'PAVILION ANGLE', value: item.pavilion_angle != null && item.pavilion_angle !== '' ? `${item.pavilion_angle}\u00b0` : '\u2014' },
                              { label: 'FLUORESCENCE', value: fluorescenceLabels[Math.round(item.fluorescence) - 1] ?? (item.fluorescence ?? '\u2014') },
                              { label: 'POLISH', value: polishSymmetryLabels[Math.round(item.polish) - 1] ?? (item.polish ?? '\u2014') },
                              { label: 'SYMMETRY', value: polishSymmetryLabels[Math.round(item.symmetry) - 1] ?? (item.symmetry ?? '\u2014') },
                              { label: 'CULET', value: culetLabels[Math.round(item.culet) - 1] ?? (item.culet ?? '\u2014') },
                            ],
                          }}
                        />
                      </td>
                    </tr>

                    {index === 19 && window.themeData.WEBSITE_TYPE === 'RETAIL' && (
                      <>
                        <tr className="trustpilot_review">
                          <td colSpan="8" style={{ textAlign: "center" }}>
                          
                            <div
                              ref={widgetRef}
                              className="trustpilot-widget"
                              data-locale="en-US"
                              data-template-id="5419b6ffb0d04a076446a9af"
                              data-businessunit-id="5f6c3ba7296f83000100bdf0"
                              data-style-height="20px"
                              data-style-width="100%"
                            
                              data-token="f24cfa9d-6ac7-4b9b-a381-8fcf03556d14"
                            >
                              <a href="https://www.trustpilot.com/review/www.loosegrowndiamond.com" target="_blank" rel="noopener noreferrer">
                                Trustpilot
                              </a>
                            </div>
                          </td>
                        </tr>
                        <tr className="d-none"></tr>
                      </>
                    )}
                  </React.Fragment>
                );
              })}

              {items.length === 0 && !isLoading && (
                <TableEmptyRow colSpan={8} handleClearFilter={handleClearFilter} />
              )}

              {(!isLoading && !skuInput && items.length > 0) && (
                <TableAskNowRow colSpan={8} />
              )}

              {isLoading && <TableLoadingRow colSpan={8} />}

              
            </tbody>
          </table>
        </div>
  
  );
}