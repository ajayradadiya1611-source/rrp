import React, { useEffect, useState, useRef } from 'react';
import RangeSlider from '../RangeSlider';
import FilterDropdown from '../FilterDropdown';
import ProductGridLoop from './ProductGridLoop';
import '../../App.scss';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import Modal from '../Modal';
import { readWishlist, toggleWishlistIdWithType } from '../../wishlist';

export default function InventoryApp3() {
  const {
    SITE_URL = '',
    API_BASE_URL = '',
    ajaxUrl = '',
    templateUrl = '',
    filters = {},
    searchQuery = '',
  } = window.LD_THEME || {};

  const [products, setProducts] = useState([]);
  const categorySlug = window?.LD_THEME?.categorySlug;
  const with_ordering = window?.LD_THEME?.with_ordering;
  const categoryId = window?.LD_THEME?.categoryId;
  const taxonomy = window?.LD_THEME?.taxonomy;
  const parentCategorySlug = window?.LD_THEME?.parentCategorySlug;
  const parentCategoryId = window?.LD_THEME?.parentCategoryId;
  console.log("InventoryApp3 initialized with categorySlug:", categorySlug, "categoryId:", categoryId, "taxonomy:", taxonomy);
  // Only show filter on engagement-rings category (not on search pages)
  const showFilter = categorySlug === 'engagement-rings' || categoryId;

  const styleSlugs = [
    'engagement-rings',
    'round-shape-engagement-ring',
    'oval-shape-engagement-ring',
    'cushion-shape-engagement-ring',
    'princess-shape-engagement-ring',
    'emerald-shape-engagement-ring',
    'radiant-shape-engagement-ring',
    'pear-shape-engagement-ring',
    'marquise-shape-engagement-ring',
    'asscher-shape-engagement-ring',
    'heart-shape-engagement-ring'
  ];

  const shapeSlugs = [
    'round-shape-engagement-ring',
    'oval-shape-engagement-ring',
    'cushion-shape-engagement-ring',
    'princess-shape-engagement-ring',
    'emerald-shape-engagement-ring',
    'radiant-shape-engagement-ring',
    'pear-shape-engagement-ring',
    'marquise-shape-engagement-ring',
    'asscher-shape-engagement-ring',
    'heart-shape-engagement-ring'
  ];

  let isshow_style = styleSlugs.includes(categorySlug);
  let isshow_shape = !shapeSlugs.includes(categorySlug);
  console.log("isshow_style",isshow_style,"isshow_shape",isshow_shape);

  // Extra filter keys (from a preset page's ACF "Filter query", e.g.
  // metal=..., or any future filter) — every array-valued key in
  // LD_THEME.filters other than the built-in ones.
  const BUILTIN_FILTER_KEYS = ['style', 'shape', 'price_range', 'price', 'ls_sortby', 'ls_sort'];
  const extraFilters = Object.fromEntries(
    Object.entries(LD_THEME.filters || {}).filter(
      ([k, v]) => !BUILTIN_FILTER_KEYS.includes(k) && Array.isArray(v) && v.length
    )
  );
  const defaultFilters = {
    ...extraFilters,
    style: LD_THEME.filters.style,
    shape: LD_THEME.filters.shape,
    price_range: [LD_THEME.filters.price_range[0], LD_THEME.filters.price_range[1]],
  };

  console.log("defaultFilters",defaultFilters);
  const resetdefaultFilter = {
    ...Object.fromEntries(Object.keys(extraFilters).map((k) => [k, []])),
    style: LD_THEME.filtersDefault.style,
    shape: LD_THEME.filtersDefault.shape,
    price_range: [LD_THEME.filtersDefault.price_range[0], LD_THEME.filtersDefault.price_range[1]],
  };

  const [selectedFilters, setSelectedFilters] = useState(defaultFilters);
  const [loading, setLoading] = useState(false);
  const [page, setPage] = useState(1);
  const [perPage] = useState(window?.LD_THEME?.perPage);
  const [totalPages, setTotalPages] = useState(1);
  const [loadingMore, setLoadingMore] = useState(false);
  const [isFiltering, setIsFiltering] = useState(false);
  const [activeDropdown, setActiveDropdown] = useState(null);
  const [sortOrder, setSortOrder] = useState('best_seller'); // 'best_seller' | 'asc' | 'desc'
  const [priceRange, setPriceRange] = useState([LD_THEME.inventoryRange.min_price, LD_THEME.inventoryRange.max_price]);
  const [total, setTotal] = useState(null);
  const [disabledFilter, setDisabledFilter] = useState(false);

  const sliderRefs = useRef({});

  const [terms, setTerms] = useState({});
  const extraAttributeFilters = window?.LD_THEME?.extraAttributeFilters || [];
  // Static terms list for "ready-to-ship" category
  const readyToShipTerms = [
    { term_id: 1936, name: 'Engagement Rings', slug: 'ready-to-ship-engagement-ring', count: 23 },
    { term_id: 1939, name: 'Earrings', slug: 'ready-to-ship-diamond-earrings', count: 17 },
    { term_id: 1938, name: 'Wedding Band', slug: 'ready-to-ship-wedding-band', count: 17 },
    { term_id: 1937, name: 'Pendants', slug: 'ready-to-ship-pendants', count: 17 },
    { term_id: 1940, name: 'Bracelets', slug: 'ready-to-ship-bracelets', count: 17 },
    { term_id: 1940, name: 'Necklaces', slug: 'necklaces', count: 17 },
    { term_id: 1938, name: 'Lab Diamonds', slug: 'ready-to-ship-lab-diamonds', count: 17 },
                                
  ];
  const [wishlistIds, setWishlistIds] = useState([]);
  const keyFor = (id, variationId) => {
    if (variationId === undefined || variationId === null || variationId === '') return String(id);
    return `${id}:${String(variationId)}`;
  };
console.log("Wishlist IDs loaded:", wishlistIds);
  useEffect(() => {
    if (typeof document === 'undefined') return;
    const items = readWishlist();
    setWishlistIds(Array.isArray(items) ? items.map(entry => keyFor(entry && entry.id ? entry.id : entry, entry && entry.variationId)) : []);
    console.log("Wishlist IDs loaded:", wishlistIds);
  }, []);

  const toggleWishlist = (id) => {
    const variationId = undefined;
    const meta = { time: Date.now() };
    const next = toggleWishlistIdWithType(id, 'productdiomon', variationId, meta);
    setWishlistIds(Array.isArray(next) ? next.map(entry => keyFor(entry.id, entry.variationId)) : []);
  };

  useEffect(() => {
    let mounted = true;
    const fetchTerms = async () => {
      try {
        // extra attribute filters are defined per category in PHP (LD_THEME.extraAttributeFilters)
        const attributes = ["style", "shape", ...extraAttributeFilters.map((f) => f.attribute)];
        const exclude = [
          "bespoke", "antique-loose-black-diamonds", 
          "hoops-and-drops", "tennis", "4-prong", "4-prong-lgdfsp-01", "bezel-lgdfsp-11", "solitaire-lgdfp-09",
          "trillion-cut-loose-black-diamond", "baguette-cut-loose-natural-diamonds",
        ];

        const params = new URLSearchParams({
          attribute: attributes.join(","),
          exclude: exclude.join(","),
        });
        // Optional per-attribute include lists, set in PHP (LD_THEME.termInclude)
        const include = window?.LD_THEME?.termInclude;
        if (include && include.shape && include.shape.length) {
          params.set("include_shape", include.shape.join(","));
        }

        const res = await fetch(`/wp-json/custom/v1/get-attribute-terms?${params}`);
        const json = await res.json();
        if (!mounted) return;
        setTerms(json.terms || {});

      } catch (err) {
        console.error('Error fetching attribute terms:', err);
      }
    };

    fetchTerms();
    return () => { mounted = false; };
  }, []);

  // Reset page/products when filters or sort change
  useEffect(() => {
    setPage(1);
    setProducts([]);
    setTotalPages(1);
  }, [selectedFilters, sortOrder]);

 const firstLoad = useRef(true);
  const prevFilterRef = useRef(null);
  const filterDebounceRef = useRef(null);

  // Fetch products when filters/sort/page change
  useEffect(() => {
    // Determine if on search page or category page
    const isSearch = !categoryId && searchQuery;
    const isCategory = categoryId && taxonomy;

    // /shop/ (flag set in PHP): no category or search, list all products
    const isShopAll = !!window?.LD_THEME?.isShop && !isSearch && !isCategory;

    if (!isSearch && !isCategory && !isShopAll) return;

    const controller = new AbortController();
    const signal = controller.signal;

    const fetchProducts = async () => {
      if (firstLoad.current || (page === 1 && isFiltering)) setLoading(true); else setLoadingMore(true);
      try {
        let url = sortOrder === 'best_seller'
          ? `/wp-json/custom/v1/get-products?orderby=best_seller&page=${page}&per_page=${perPage}`
          : `/wp-json/custom/v1/get-products?orderby=price&order=${encodeURIComponent(sortOrder)}&page=${page}&per_page=${perPage}`;
        
        // Add category and taxonomy for category pages
        if (isCategory) {
          url += `&category_id=${categoryId}&taxonomy=${taxonomy}`;
        }
        
        if (isShopAll) {
          url += `&all=1`;
        }

        // Add search query for search pages
        if (isSearch) {
          url += `&search=${encodeURIComponent(searchQuery)}`;
        }

        Object.entries(selectedFilters).forEach(([attr, values]) => {
          if (attr == 'price' && Array.isArray(values) && values.length === 2) {
            url += `&price_min=${encodeURIComponent(values[0])}&price_max=${encodeURIComponent(values[1])}`;
          } else if (Array.isArray(values) && values.length > 0) {
            url += `&${encodeURIComponent(attr)}=${encodeURIComponent(values.join(','))}`;
          }
        });
        // include price range when set
        // if (Array.isArray(priceRange) && priceRange.length === 2) {
        //   const [minP, maxP] = priceRange;
        //   url += `&price_min=${encodeURIComponent(minP)}&price_max=${encodeURIComponent(maxP)}`;
        // }

        const response = await fetch(url, { signal });
        const data = await response.json();
        const items = data.products || [];
        const pagination = data.pagination || {};
        setTotalPages(pagination.total_pages || 1);
        setTotal(pagination.total || 0);
        // Always replace products for the selected page (do not append)
        setProducts(items);
      } catch (error) {
        if (error.name === 'AbortError') return; // fetch was canceled
        console.error('Error fetching products:', error);
      } finally {
        if (firstLoad.current || (page === 1 && isFiltering)) setLoading(false); else setLoadingMore(false);
        firstLoad.current = false;
        // any in-flight filtering request is finished
        setIsFiltering(false);
      }
    };

    fetchProducts();

    return () => controller.abort();
  }, [selectedFilters, sortOrder, page, perPage]);

  // Debounced URL update when selectedFilters change (mirrors App2 behavior)
  useEffect(() => {
     if (document.body.classList.contains("single-product") || document.body.classList.contains("search-results")) {
        return;
      }
    const filterString = JSON.stringify(selectedFilters);
    if (prevFilterRef.current === filterString) return;
    prevFilterRef.current = filterString;

    if (filterDebounceRef.current) {
      clearTimeout(filterDebounceRef.current);
      filterDebounceRef.current = null;
    }

    filterDebounceRef.current = setTimeout(() => {
      try {
        const qs = buildQueryString(selectedFilters, resetdefaultFilter);
        const newUrl = `${window.location.pathname}${qs}`;
        window.history.replaceState(null, '', newUrl);
      } catch (err) {
        // ignore
      }
      filterDebounceRef.current = null;
    }, 250);

    return () => {
      if (filterDebounceRef.current) {
        clearTimeout(filterDebounceRef.current);
        filterDebounceRef.current = null;
      }
    };
  }, [selectedFilters]);

  // style terms are fetched inside FilterDropdown when attribute prop is passed

  const toggleDropdown = (id) => {
    setActiveDropdown((prev) => (prev === id ? null : id));
  };

  // close dropdowns when clicking outside
  useEffect(() => {
    const onDocClick = (e) => {
      if (!e.target.closest || e.target.closest('.filter-dropdown')) return;
      setActiveDropdown(null);
    };

    document.addEventListener('click', onDocClick);
    return () => document.removeEventListener('click', onDocClick);
  }, []);

  // Infinite scroll removed — pagination will be used instead

  // listen for filter changes dispatched by FilterDropdown
  useEffect(() => {
    const handler = (e) => {
      const { id, attribute, values } = e.detail || {};
      const key = attribute || id;
      if (!key) return;
      // If the shape filter was auto-selected and locked, ignore any
      // external attempts to change the shape filter so it remains
      // the single, permanent selection.
      if (key === 'shape' && disabledFilter) return;

      setIsFiltering(true);
      setSelectedFilters((prev) => ({ ...prev, [key]: values }));
    };
    document.addEventListener('filter:change', handler);
    return () => document.removeEventListener('filter:change', handler);
  }, [disabledFilter]);

  const sliderSettings = {
    arrows: true,
    dots: true,
    autoplay: true,
    infinite: true, // Needed for autoplay
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplaySpeed: 1500, // ms each image stays on screen while hovering (slick default: 3000)
    speed: 400,          // ms for the slide transition animation (slick default: 500)
    pauseOnHover: false, // hover is what starts playback, so don't let slick pause it
  };

  // Banner images to cycle through for the custom card inserted after every 7th product
  const bannerImages = ['1.png', '2.png', '3.png'];

  // Disable pagination while loading/filtering
  const paginationDisabled = loading || loadingMore || isFiltering;

  // Pagination clicks should scroll to top; filtering/sorting should not.
  const goToPage = (p) => {
    setPage(p);
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  // Build page items including ellipses for large page sets
  const buildPageItems = (total, current) => {
    if (!total || total <= 1) return [];

    // Show first page, last page, and a small window around the current page.
    // Use ellipses ('dots') when there's a gap.
    const delta = 1; // number of pages to show on each side of current
    const start = Math.max(2, current - delta);
    const end = Math.min(total - 1, current + delta);

    const range = [];
    for (let i = start; i <= end; i++) range.push(i);

    const items = [1];
    if (start > 2) items.push('dots');
    items.push(...range);
    if (end < total - 1) items.push('dots');
    if (total > 1) items.push(total);

    return items;
  };

  function isEqual(a, b) {
    if (a === b) return true;
    if (Array.isArray(a) && Array.isArray(b)) {
      if (a.length !== b.length) return false;
      return a.every((v, i) => v === b[i]);
    }
    if (a && b && typeof a === 'object' && typeof b === 'object') {
      const ak = Object.keys(a), bk = Object.keys(b);
      if (ak.length !== bk.length) return false;
      return ak.every(k => isEqual(a[k], b[k]));
    }
    return false;
  }

  function getActiveFilters(selectedFilters, defaultFilters) {
    console.log("getActiveFilters",selectedFilters,defaultFilters);
    const active = {};
    for (const key in selectedFilters) {
      const val = selectedFilters[key];
      const def = defaultFilters[key];
      if (val == null) continue;
      if (Array.isArray(val) && val.length === 0) continue;
      if (!isEqual(val, def)) active[key] = val;
    }
    return active;
  }

  // Build URL query string from filter values (always emit filter fields)
  const buildQueryString = (cur, def) => {
    try {
      const params = new URLSearchParams();

      const add = (k, v) => {
        if (v === undefined || v === null) return;
        params.append(k, String(v));
      };

      const isDefaultValue = (value, defaultValue) => {
        if (Array.isArray(value) && Array.isArray(defaultValue)) {
          return JSON.stringify([...value].sort()) === JSON.stringify([...defaultValue].sort());
        }
        return value === defaultValue;
      };

      const get = (k) => (cur && cur[k] !== undefined ? cur[k] : (def && def[k] !== undefined ? def[k] : undefined));

      // Only include style, shape and price in query string
      const style = get('style');
      if (style && !isDefaultValue(style, (def && def.style) || LD_THEME.filtersDefault.style)) {
        if (Array.isArray(style)) add('style', style.join(','));
        else add('style', style);
      }

      const shape = get('shape');
      if (shape && !isDefaultValue(shape, (def && def.shape) || LD_THEME.filtersDefault.shape)) {
        if (Array.isArray(shape)) add('shape', shape.join(','));
        else add('shape', shape);
      }

      // Any extra filter keys (preset pages / future filters)
      Object.keys(cur || {}).forEach((k) => {
        if (BUILTIN_FILTER_KEYS.includes(k)) return;
        const v = cur[k];
        if (Array.isArray(v) && v.length) add(k, v.join(','));
      });

      // support both `price` and `price_range` keys
      const price = get('price') !== undefined ? get('price') : get('price_range');
      if (price && Array.isArray(price) && !isDefaultValue(price, (def && (def.price || def.price_range)) || LD_THEME.filtersDefault.price_range)) {
        add('price', price.map(v => Number(v).toFixed(2)).join(','));
      }

      let qs = params.toString();
      qs = qs.replace(/%2C/gi, ',');
      return qs ? `?${qs}` : '';
    } catch (err) {
      console.error('buildQueryString error', err);
      return '';
    }
  };

  // usage
  const activeFilters = getActiveFilters(selectedFilters, resetdefaultFilter);
  console.log("activeFilters",activeFilters);
  const hasActiveFilters = Object.keys(activeFilters).length > 0;
  const isSearch = !categoryId && searchQuery;

  return (
    <div>
      {/* Banner Section */}
      <div className="container-fluid">
        {/* Breadcrumb Section */}
        {showFilter && with_ordering && (
          <>
            <div class="ls_rbfilter">
              <div class="filters d-flex align-items-center">
                <span>Filter By :</span>

                  {(( isshow_style==true)) && (
                    <FilterDropdown
                      id="style"
                      menuWidth={180}
                      menuWidthMobile={280}
                      title="Style"
                      activeId={activeDropdown}
                      toggle={toggleDropdown}
                      terms={terms.style || []}
                      attribute="style"
                      selectedValues={selectedFilters.style || []}
                    />
                  )}

                {categorySlug === 'ready-to-ship' && (
                  <FilterDropdown id="ready-to-ship" menuWidth="match-button" menuWidthMobile={280} title="Product Type" activeId={activeDropdown} toggle={toggleDropdown} terms={readyToShipTerms} attribute="ready_to_ship" selectedValues={selectedFilters.ready_to_ship || []} />
                )}

                {((  isshow_shape==true  ) && categorySlug !== 'chains'  && categorySlug !== 'ready-to-ship-lab-diamonds' )&& (
                  <FilterDropdown id="diamond-shape" menuWidth="match-button" menuWidthMobile={280} title="Diamond Shape" activeId={activeDropdown} toggle={toggleDropdown} terms={terms.shape || []} attribute="shape" selectedValues={selectedFilters.shape || []} disabledFilter={disabledFilter} />
                )}

                {extraAttributeFilters.map((f) => (
                  <FilterDropdown
                    key={f.attribute}
                    id={f.attribute}
                    menuWidth="match-button"
                    menuWidthMobile={280}
                    title={f.title}
                    activeId={activeDropdown}
                    toggle={toggleDropdown}
                    terms={terms[f.attribute] || []}
                    attribute={f.attribute}
                    selectedValues={selectedFilters[f.attribute] || []}
                  />
                ))}

                <FilterDropdown
                  id="price"
                  title="Price"
                  menuWidth={250}
                  menuWidthMobile={280}
                  activeId={activeDropdown}
                  toggle={toggleDropdown}
                >
                  <div class="slider-box  px-3 w-100">
                      <div className="input-box">
                        <input
                          type="text"
                          id="min-lwratio"
                          value={priceRange[0]}
                          onChange={(e) => {
                            setPriceRange([Number(e.target.value), priceRange[1]]);
                          }}
                          onBlur={(e) => {
                            let value = Number(priceRange[0]);
                            if (isNaN(value)) value = LD_THEME.inventoryRange.min_price;

                            // Clamp between min_price–max_price
                            value = Math.min(Math.max(value, LD_THEME.inventoryRange.min_price), LD_THEME.inventoryRange.max_price);
                            // Fix to 2 decimals
                            value = Number(value.toFixed(2));

                            // make sure min <= max
                            let maxValue = Number(priceRange[1]);
                            let minValue = Math.min(value, maxValue);

                            setPriceRange([minValue, maxValue]);

                            setSelectedFilters((prev) => ({
                              ...prev,
                              price: [
                                minValue,
                                maxValue,
                              ]
                            }));
                          }}
                        />
                        <input
                          type="text"
                          id="max-lwratio"
                          value={priceRange[1]}
                          onChange={(e) => {
                            setPriceRange([priceRange[0], e.target.value]);
                          }}
                          onBlur={(e) => {
                            let value = Number(priceRange[1]);
                            if (isNaN(value)) value = LD_THEME.inventoryRange.max_price;

                            // Clamp between min_price–max_price
                            value = Math.min(Math.max(value, LD_THEME.inventoryRange.min_price), LD_THEME.inventoryRange.max_price);
                            // Fix to 2 decimals
                            value = Number(value.toFixed(2));

                            // make sure min <= max
                            let minValue = Number(priceRange[0]);
                            let maxValue = Math.max(value, minValue);

                            setPriceRange([minValue, maxValue]);

                            setSelectedFilters((prev) => ({
                              ...prev,
                              price: [
                                minValue,
                                maxValue,
                              ]
                            }));
                          }}
                        />
                      </div>
                      <RangeSlider
                        className="lwratio-slider-handles w-100"
                        start={priceRange[0]}
                        end={priceRange[1]}
                        min={LD_THEME.inventoryRange.min_price}
                        max={LD_THEME.inventoryRange.max_price}
                        step={1}
                        fireOn={['slide', 'change']}
                        onChange={(values, event) => {
                          const parsed = [Number(values[0]), Number(values[1])];

                          const minEl = document.getElementById("min-lwratio");
                          const maxEl = document.getElementById("max-lwratio");
                          if (minEl) minEl.value = Number(values[0]);
                          if (maxEl) maxEl.value = Number(values[1]);
                          
                          if (event === 'change') {
                            setPriceRange(parsed);
                            setSelectedFilters((prev) => ({
                              ...prev,
                              price: [
                                parseFloat(parsed[0]),
                                parseFloat(parsed[1])
                              ]
                            }));
                          }
                        }}
                      />
                  </div>
                </FilterDropdown>

                {(() => {
                  const readyShipCategories = [
                    'studs', 'hoops', 'drops', 'halo-lab-diamond-earrings', 'cluster',
                    'diamond-bands', 'anniversary-rings', 'eternity-rings', 'stackable-rings',
                    'curved-wedding-band', 'solitaire', 'halo', 'lab-diamond-earrings',
                    'chains', 'diamond-tennis-bracelets', 'diamond-tennis-necklaces',
                  ];

                  if (!readyShipCategories.includes(categorySlug)) return null;

                  const isReadyShip = (selectedFilters.ready_ship || []).includes('ready-to-ship');

                  return (
                   <div className="filter-dropdown">
                      <button
                        type="button"
                        className={`filter-btn gap-1 ${isReadyShip ? 'active' : ''}`}
                        style={{
                          border: `1px solid ${isReadyShip ? '#000000' : '#dee2e6'}`,
                        }}
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-original-title="Shipped in 2 business days"
                        onClick={() => {
                          setPage(1);
                          setSelectedFilters((prev) => {
                            const next = { ...prev };
                            if (isReadyShip) delete next.ready_ship;
                            else next.ready_ship = ['ready-to-ship'];
                            return next;
                          });
                        }}
                      >
                        <img src={`${templateUrl}/aj_custom/inventory/delivery.svg`} alt="" />
                        Ready to Ship
                      </button>
                    </div>
                  );
                })()}

                <div class="woocommerce_ordering_1 ms-auto">
                  <span>Sort by :</span>
                  <FilterDropdown
                    id="sort"
                    menuWidth="match-button"
                    menuWidthMobile={280}
                    activeId={activeDropdown}
                    toggle={toggleDropdown}
                    label={sortOrder === 'best_seller' ? 'Best Seller' : sortOrder === 'desc' ? 'Price: High to Low' : 'Price: Low to High'}
                  >
                    <div
                      role="button"
                      tabIndex={0}
                      className={sortOrder === 'best_seller' ? 'active' : ''}
                      onClick={() => {
                        setSortOrder('best_seller');
                        toggleDropdown(null);
                      }}
                    >
                      Best Seller
                    </div>
                    <div
                      role="button"
                      tabIndex={0}
                      className={sortOrder === 'asc' ? 'active' : ''}
                      onClick={() => {
                        setSortOrder('asc');
                        toggleDropdown(null);
                      }}
                    >
                      Price: Low to High
                    </div>
                    <div
                      role="button"
                      tabIndex={0}
                      className={sortOrder === 'desc' ? 'active' : ''}
                      onClick={() => {
                        setSortOrder('desc');
                        toggleDropdown(null);
                      }}
                    >
                      Price: High to Low
                    </div>
                  </FilterDropdown>
                </div>
              </div>
            </div>

            <div className="filter_item d-flex align-items-center flex-wrap">
             {Number(total ?? 0) !== 0 && (
              <p className="mb-0">
                Found {Number(total ?? 0).toLocaleString()} Items
              </p>
            )}
              <div className="d-flex align-items-center gap-2 flex-wrap">
                {!hasActiveFilters ? (
                  <span className="text-muted no_filter">No filters applied</span>
                ) : (
                  <>
                    {/* Price (explicit) */}
                    {selectedFilters.price && selectedFilters.price.length === 2 && (
                      <span key="price">
                        Price: ${selectedFilters.price[0]} - ${selectedFilters.price[1]}
                        <a
                          href="#"
                          onClick={(e) => {
                            e.preventDefault();
                            setSelectedFilters((prev) => {
                              const next = { ...prev };
                              delete next.price;
                              return next;
                            });
                            setPriceRange([LD_THEME.inventoryRange.min_price, LD_THEME.inventoryRange.max_price]); // reset slider
                          }}
                          className="text-decoration-none mx-2"
                        >
                          x
                        </a>
                      </span>
                    )}
                
                    {/* Only render style, shape and ready_to_ship (hard-coded) */}
                    {['style', 'shape', 'ready_to_ship'].map((attr) => {
                      const values = selectedFilters[attr] || [];
                      if (!values || values.length === 0) return null;

                      const displayName = attr === 'ready_to_ship' ? 'Ready To Ship' : attr.charAt(0).toUpperCase() + attr.slice(1);
                      const sourceTerms = attr === 'ready_to_ship' ? readyToShipTerms : terms[attr];

                      return (
                        <span key={attr}>
                          {displayName}:{' '}
                          {values.map((val) => {
                            const label = sourceTerms?.find((t) => t.slug === val)?.name || val;

                            return (
                              <React.Fragment key={`${attr}-${val}`}>
                                {label}
                                {!(attr === 'shape' && disabledFilter) && (
                                  <a
                                    href="#"
                                    onClick={(e) => {
                                      e.preventDefault();
                                      setSelectedFilters((prev) => {
                                        const next = { ...prev };
                                        next[attr] = (next[attr] || []).filter((v) => String(v) !== String(val));
                                        if ((next[attr] || []).length === 0) delete next[attr];
                                        return next;
                                      });
                                    }}
                                    className="text-decoration-none mx-2"
                                  >
                                    x
                                  </a>
                                )}
                              </React.Fragment>
                            );
                          })}
                        </span>
                      );
                    })}

                    <a
                      href="#"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedFilters((prev) => {
                          if (disabledFilter && prev && prev.shape && prev.shape.length) {
                            return { shape: prev.shape };
                          }
                          return {};
                        });
                        setPriceRange([LD_THEME.inventoryRange.min_price, LD_THEME.inventoryRange.max_price]); // reset slider when clearing non-locked filters
                      }}
                      className="text-decoration-none ps-2 fw-semibold"
                    >
                      Clear All
                    </a>
                  </>
                )}
              </div>
            </div>

          </>
        )}

        {loading ? (
          <div className="d-flex justify-content-center my-5">
            <img src={`${templateUrl}/aj_custom/inventory/Loading.edc97a1c.gif`} width="40" height="40" alt="Loading" />
          </div>
        ) : total === 0 ? (
          <div className="d-flex justify-content-center my-5">
            <div className="cat_ask_now_bx">
              {isSearch ? (
                <>
                  <div className="cat_heading">No Products were found</div>
                  <div className="cat_dsearch_pup">
                    <a href="/lab-diamonds/" className="cat_ls_bbtn">Return to Shop</a>
                  </div>
                </>
              ) : (
                <>
                  <div className="cat_heading">No Record Found</div>
                  <div className="cat_stext">But you can still get your custom setting</div>
                  <div className="cat_dsearch_pup">
                    <a href="#" className="cat_ls_bbtn text-decoration-none mt-4" data-bs-toggle="modal" data-bs-target="#customSettingModal">Ask us Now</a>
                  </div>
                </>
              )}
            </div>
          </div>
        ) : (
          <ProductGridLoop
            products={products}
            templateUrl={templateUrl}
            categorySlug={categorySlug}
            withOrdering={with_ordering}
            sliderSettings={sliderSettings}
            sliderRefs={sliderRefs}
            wishlistIds={wishlistIds}
            keyFor={keyFor}
            toggleWishlist={toggleWishlist}
            bannerImages={bannerImages}
          />
        )}
        {/* Pagination (replaces infinite scroll) */}
        {totalPages > 1 && !isFiltering && with_ordering && (
          <nav className="woocommerce-pagination pagination-cubic">
            <ul className="page-numbers">
                {/* Prev button */}
                <li className="prevItem">
                  {page > 1 && !paginationDisabled && (
                    <a
                      aria-label={`Page ${page - 1}`}
                      className="prev page-numbers"
                      href="#"
                      onClick={(e) => { e.preventDefault(); goToPage(page - 1); }}
                    >
                      <i className="fa fa-angle-double-left"></i>
                    </a>
                  )}
                </li>
              {/* First item (page 1) */}
              <li className="firstItem">
                {page === 1 ? (
                  <span aria-label={`Page 1`} aria-current="page" className="page-numbers current">1</span>
                ) : paginationDisabled ? (
                  <span className="page-numbers disabled" aria-disabled="true">1</span>
                ) : (
                  <a
                    aria-label={`Page 1`}
                    className="page-numbers"
                    href="#"
                    onClick={(e) => { e.preventDefault(); goToPage(1); }}
                  >1</a>
                )}
              </li>

              {buildPageItems(totalPages, page).map((item, idx) => {
                if (item === 'dots') {
                  return (
                    <li key={`dots-${idx}`}><span className="page-numbers dots">…</span></li>
                  );
                }

                // Skip rendering page 1 again since we rendered it as firstItem
                if (item === 1) return null;

                return (
                  <li key={`page-${item}`}>
                    {item === page ? (
                      <span aria-label={`Page ${item}`} aria-current="page" className="page-numbers current">{item}</span>
                    ) : paginationDisabled ? (
                      <span className="page-numbers disabled" aria-disabled="true">{item}</span>
                    ) : (
                      <a
                        aria-label={`Page ${item}`}
                        className="page-numbers"
                        href="#"
                        onClick={(e) => { e.preventDefault(); goToPage(item); }}
                      >{item}</a>
                    )}
                  </li>
                );
              })}

              <li className="lastItem">
                {page < totalPages && !paginationDisabled && (
                  <a
                    className="next page-numbers"
                    href="#"
                    onClick={(e) => { e.preventDefault(); goToPage(page + 1); }}
                    aria-label={`Page ${page + 1}`}
                  >
                    <i className="fa fa-angle-double-right"></i>
                  </a>
                )}
              </li>
            </ul>
          </nav>
        )}
        {loadingMore && (
          <div className="d-flex justify-content-center my-4">
            <img src={`${templateUrl}/aj_custom/inventory/Loading.edc97a1c.gif`} width="40" height="40" alt="Loading" />
          </div>
        )}
      </div>
    </div>
  );
}