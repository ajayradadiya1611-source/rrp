import RingBuilderMatchNotice from './components/RingBuilderMatchNotice';
import React, { useEffect, useState, useRef, useCallback ,useMemo} from 'react';
import './styles.scss';
import RangeSlider from './components/RangeSlider';
import Modal from "./components/Modal";
import ConfirmAddModal from './components/ConfirmAddModal';
import axios from "./api.js";
import { readWishlist, toggleWishlistIdWithType, readCookieValue, writeCookieValue } from './wishlist';
import { lsFunction, cutLabels, colorLabels, clarityLabels, updateSkuQuery, openDetailForItem } from './lsFunction';
import { templateUrl, ajaxUrl, globalRb, buildQueryString, countActiveFilters } from './components/inventoryHelpers';
import FilterSidebar, { SidebarSection } from './components/FilterSidebar';
import FieldLabel from './components/FieldLabel';
import RangeFilterField from './components/RangeFilterField';
import CaratQuickFilter from './components/CaratQuickFilter';
import FilterSlider from './components/FilterSlider';
import InventoryResults2 from './components/InventoryResults2';
import { TOOLTIP_TEXT } from './constants/tooltipTexts';
import ResetFiltersButton from './components/ResetFiltersButton';
import FilterDropdown from './components/FilterDropdown';

// query-string updates and form submit handling removed — behaviour handled server-side or elsewhere

export default function InventoryApp2() {
  const [showMoreShapes, setShowMoreShapes] = useState(true);
   const containerRef = useRef(null);
  // const API_BASE_URL = window.API_BASE_URL || 'https://stage2.loosegrowndiamond.com/wp-json/ls/v1'; 

  const {
    SITE_URL = '',
    API_BASE_URL = '',
    ajaxUrl = '',
    templateUrl = '',
    filters = {},
    cartProductIds: cartProductIdsRaw = [],
    cartRingProductIds: cartRingProductIdsRaw = [],
  } = window.LD_THEME || {};
const cartProductIds = Array.isArray(cartProductIdsRaw)
    ? cartProductIdsRaw
    : (typeof cartProductIdsRaw === 'string'
        ? cartProductIdsRaw.split(',').map(s => s.trim()).filter(Boolean)
        : (cartProductIdsRaw && typeof cartProductIdsRaw === 'object'
            ? Object.keys(cartProductIdsRaw)
            : []));

const cartRingProductIds = Array.isArray(cartRingProductIdsRaw)
    ? cartRingProductIdsRaw
    : (typeof cartRingProductIdsRaw === 'string'
        ? cartRingProductIdsRaw.split(',').map(s => s.trim()).filter(Boolean)
        : (cartRingProductIdsRaw && typeof cartRingProductIdsRaw === 'object'
            ? Object.keys(cartRingProductIdsRaw)
            : []));

  // Mirrors PHP's $is_in_cart_ring: true when this pid was already added via the ring builder
  const isInCartRing = (pid) =>
    !!(pid && cartRingProductIds.map(Number).includes(Number(pid)));

  const [dropdownOpen, setDropdownOpen] = useState({ certificate: false, method: false, table: false, depth: false, lwratio: false });

  // Only toggle the dropdown state for the given key, do not reset or affect other state (especially not RangeSlider values)
  const toggleDropdown = (key) => {
    setDropdownOpen(prev => (prev[key] ? {} : { [key]: true }));
  };

  // FilterDropdown (components/FilterDropdown.jsx) is the shared dropdown
  // shell used across the app — this adapts our object-keyed
  // dropdownOpen/toggleDropdown state to the activeId + toggle(id|null)
  // interface FilterDropdown expects, so we don't need a second dropdown
  // implementation just for this state shape.
  const activeDropdownId = Object.keys(dropdownOpen).find(k => dropdownOpen[k]) || null;
  const setActiveDropdownId = (id) => setDropdownOpen(id ? { [id]: true } : {});

  const SORT_FIELDS = [
    { value: 'price', label: 'Price' },
    { value: 'carat', label: 'Carat' },
    { value: 'intensity', label: 'Intensity' },
    { value: 'clarity', label: 'Clarity' },
  ];

  useEffect(() => {
      const handleClickOutside = (e) => {
        // only act when at least one dropdown is open
        if (!Object.values(dropdownOpen).some(Boolean)) return;
  
        // if the click happened inside any .dropdown element, do nothing
        const clickedDropdown = e.target && e.target.closest && e.target.closest('.dropdown');
        if (clickedDropdown) return;
  
        // otherwise close all dropdowns
        setDropdownOpen({});
      };
  
      // use 'click' so it runs after React onClick handlers
      document.addEventListener('click', handleClickOutside);
      return () => document.removeEventListener('click', handleClickOutside);
    }, [dropdownOpen]);

  // products data and detail open state (dynamic generation)
  const [openDetail, setOpenDetail] = useState(null);
  const [wishlistIds, setWishlistIds] = useState([]);

  

  const keyFor = (id, variationId) => `${id}:${variationId === undefined || variationId === null ? '' : String(variationId)}`;

  useEffect(() => {
    if (typeof document === 'undefined') return;
    const items = readWishlist();
    setWishlistIds(Array.isArray(items) ? items.map(entry => keyFor(entry && entry.id ? entry.id : entry, entry && entry.variationId)) : []);
  }, []);

  const toggleWishlist = (id) => {
    const variationId = undefined;
    const meta = { price: undefined };
    const params = (typeof window !== 'undefined' && window.location && window.location.search)
        ? new URLSearchParams(window.location.search)
        : new URLSearchParams();
    try {
      // include the current query string and full URL in the meta so
      // downstream handlers can access the page context
      meta.query = params.toString();
      meta.currentUrl = (typeof window !== 'undefined' && window.location) ? window.location.href : '';
    } catch (e) {
      // ignore if window isn't available or URL parsing fails
    }
   // console.log("Toggling wishlist for id:", id, "variationId:", variationId, "with meta:", meta);
    const next = toggleWishlistIdWithType(id, 'colordiomon', variationId, meta);
    setWishlistIds(Array.isArray(next) ? next.map(entry => keyFor(entry.id, entry.variationId)) : []);
  };

  const isInWishlist = (item) => {
    if (!wishlistIds || !Array.isArray(wishlistIds)) return false;
    const id = item?.id ?? item?.pid ?? '';
    const variationId = item?.variation_id ?? item?.vid ?? item?.variationId ?? undefined;
    if (!id) return false;
    return wishlistIds.includes(keyFor(id, variationId));
  };

  const shapes = [
    { id: 1, value: 'round', label: 'Round', img: '/assets/image/diamond/round_shape_lab_grown_diamond.svg', alt: 'round_shape_lab_grown_diamond' },
    { id: 2, value: 'princess', label: 'Princess', img: '/assets/image/diamond/buy_princess_cut_lab_created_diamond.svg', alt: 'buy_princess_cut_lab_created_diamond' },
    { id: 3, value: 'cushion', label: 'Cushion', img: '/assets/image/diamond/radient_shape_lab_created_diamond.svg', alt: 'radient_shape_lab_created_diamond' },
    { id: 4, value: 'oval', label: 'Oval', img: '/assets/image/diamond/oval.svg', alt: 'oval' },
    { id: 5, value: 'emerald', label: 'Emerald', img: '/assets/image/diamond/emerald.svg', alt: 'emerald' },
    { id: 6, value: 'pear', label: 'Pear', img: '/assets/image/diamond/pear_shape_lab_created_diamond.svg', alt: 'pear_shape_lab_created_diamond' },
    { id: 7, value: 'asscher', label: 'Asscher', img: '/assets/image/diamond/asscher.svg', alt: 'asscher' },
    { id: 8, value: 'radiant', label: 'Radiant', img: '/assets/image/diamond/radiant.svg', alt: 'radiant' },
    { id: 9, value: 'marquise', label: 'Marquise', img: '/assets/image/diamond/buy_marquise_shape_agape_diamonds.svg', alt: 'buy_marquise_shape_agape_diamonds' },
    { id: 10, value: 'heart', label: 'Heart', img: '/assets/image/diamond/buy_heart_shape_lab_created_diamond.svg', alt: 'buy_heart_shape_lab_created_diamond' },
  ];

  const COLOR_OPTIONS = [
    { key: 'yellow',key_val: "1", label: 'Yellow', img: 'yellow_diamond.png' },
    { key: 'pink',key_val: "2", label: 'Pink', img: 'pink_diamond.png' },
    { key: 'purple',key_val: "3", label: 'Purple', img: 'purple_diamond.png' },
    { key: 'red', key_val: "4",label: 'Red', img: 'red_diamond.png' },
    { key: 'blue', key_val: "5",label: 'Blue', img: 'blue_diamond.png' },
    { key: 'green',key_val: "6", label: 'Green', img: 'green_diamond.png' },
    { key: 'orange', key_val: "7",label: 'Orange', img: 'orange_diamond.png' },
    { key: 'brown',key_val: "8", label: 'Brown', img: 'brown_diamond.png' },
    { key: 'black', key_val: "9",label: 'Black', img: 'black_diamond.png' },
    { key: 'gray',key_val: "10", label: 'Gray', img: 'gray_diamond.png' },
  ];

  

  const intensityLabels = [
    "Fancy Light",
    "Fancy",
    "Fancy Intense",
    "Fancy Vivid",
    "Fancy Deep",
    "Fancy Dark"
  ];

  

  const ls_get_intensitydesc = (intensity) => {
    const desc = {};

    switch (intensity) {
      case "1":
        desc["intensity"] = "A light form of intensity that can be seen with the naked eye. Fancy Light diamonds allow for maximum light performance, while also showing significant color.";
        break;
      case "2":
        desc["intensity"] = "The “Fancy” intensity indicates the diamond has significant color. The shade of the color is neither light nor too intense.";
        break;
      case "3":
        desc["intensity"] = "Fancy Intense indicates very significant color. The color intensity can be clearer seen, as the color saturation is much more intense than the “Fancy” intensity grade.";
        break;
      case "4":
        desc["intensity"] = "As the name indicated Fancy Vivid color diamonds have very strong color saturation. The Fancy Vivid is often the most expensive intensity grade.";
        break;
      case "5":
        desc["intensity"] = "On of the darkest color tones available. Fancy Deep diamonds have almost full color saturation.";
        break;
      case "6":
        desc["intensity"] = "The most intense color tone available. Fancy Dark diamond have full color saturation with minimal room for additional light properties. ";
        break;
      default:
        desc["intensity"] = "";
    }

    return desc;
  }

  const [isLoading, setLoading] = useState(true);
  const [isLoadingCart, setLoadingCart] = useState(null);
  const [confirmModalOpen, setConfirmModalOpen] = useState(false);
  const [confirmItem, setConfirmItem] = useState(null);

  const [filtersModalOpen, setFiltersModalOpen] = useState(false);
  const [activeAccordion, setActiveAccordion] = useState({ color: false, shape: false, price: false, carat: false, cut: false, intensity: false, clarity: false, type: false, advanced: false});


  const inventoryType = window.INVENTORY_TYPE ? window.INVENTORY_TYPE : 'default';

  const ringBuilderShape = shapes.find(
    shape => shape.id === Number(globalRb?.setting?.shape)
  )?.value;

  // Filter states
  const defaultFilter = {
     shape: ringBuilderShape ? [ringBuilderShape] : LD_THEME.filters.shape,
    price_range: [LD_THEME.filters.price_range[0], LD_THEME.filters.price_range[1]],
    carat_range: [LD_THEME.filters.carat_range[0], LD_THEME.filters.carat_range[1]],
    cut: [LD_THEME.filters.cut_range[0], LD_THEME.filters.cut_range[1]],
    intensity: LD_THEME.filters.intensity,
    color: LD_THEME.filters.color,
    clarity: [LD_THEME.filters.clarity_range[0], LD_THEME.filters.clarity_range[1]],
    ls_sku: LD_THEME.filters.sku,
    certificate_type: LD_THEME.filters.certificate_type,
    method: LD_THEME.filters.method,
    media: LD_THEME.filters.media || [],
    table_range: [LD_THEME.filters.table_range[0], LD_THEME.filters.table_range[1]],
    depth_range: [LD_THEME.filters.depth_range[0], LD_THEME.filters.depth_range[1]],
    lwratio_range: [LD_THEME.filters.lwratio_range[0], LD_THEME.filters.lwratio_range[1]],
    // Advanced Filters (sidebar) — real min/max from ls_inventory_c, same
    // as table_range/depth_range/lwratio_range above. See script-load.php.
    length_range: [LD_THEME.filters.length_range[0], LD_THEME.filters.length_range[1]],
    width_range: [LD_THEME.filters.width_range[0], LD_THEME.filters.width_range[1]],
    height_range: [LD_THEME.filters.height_range[0], LD_THEME.filters.height_range[1]],
    fluorescence_range: [LD_THEME.filters.fluorescence_range[0], LD_THEME.filters.fluorescence_range[1]],
    culet_range: [LD_THEME.filters.culet_range[0], LD_THEME.filters.culet_range[1]],
    polish_range: [LD_THEME.filters.polish_range[0], LD_THEME.filters.polish_range[1]],
    symmetry_range: [LD_THEME.filters.symmetry_range[0], LD_THEME.filters.symmetry_range[1]],
    crown_angle_range: [LD_THEME.filters.crown_angle_range[0], LD_THEME.filters.crown_angle_range[1]],
    pavilion_angle_range: [LD_THEME.filters.pavilion_angle_range[0], LD_THEME.filters.pavilion_angle_range[1]],
    girdle_range: [LD_THEME.filters.girdle_range[0], LD_THEME.filters.girdle_range[1]],
    ls_sortby: LD_THEME.filters.ls_sortby || '',
    ls_sort: LD_THEME.filters.ls_sort || '',
    shipping: LD_THEME.filters.shipping,
    is_sale: LD_THEME.filters.is_sale,
    
  };
  const resetdefaultFilter  = {
     shape:  LD_THEME.filtersDefault.shape,
    price_range: [LD_THEME.filtersDefault.price_range[0], LD_THEME.filtersDefault.price_range[1]],
    carat_range: [LD_THEME.filtersDefault.carat_range[0], LD_THEME.filtersDefault.carat_range[1]],
    cut: [LD_THEME.filtersDefault.cut_range[0], LD_THEME.filtersDefault.cut_range[1]],
    intensity: LD_THEME.filtersDefault.intensity,
    color: LD_THEME.filtersDefault.color,
    clarity: [LD_THEME.filtersDefault.clarity_range[0], LD_THEME.filtersDefault.clarity_range[1]],
    ls_sku: LD_THEME.filtersDefault.sku,
    certificate_type: LD_THEME.filtersDefault.certificate_type,
    method: LD_THEME.filtersDefault.method,
    media: LD_THEME.filtersDefault.media || [],
    table_range: [LD_THEME.filtersDefault.table_range[0], LD_THEME.filtersDefault.table_range[1]],
    depth_range: [LD_THEME.filtersDefault.depth_range[0], LD_THEME.filtersDefault.depth_range[1]],
    lwratio_range: [LD_THEME.filtersDefault.lwratio_range[0], LD_THEME.filtersDefault.lwratio_range[1]],
    length_range: [LD_THEME.filtersDefault.length_range[0], LD_THEME.filtersDefault.length_range[1]],
    width_range: [LD_THEME.filtersDefault.width_range[0], LD_THEME.filtersDefault.width_range[1]],
    height_range: [LD_THEME.filtersDefault.height_range[0], LD_THEME.filtersDefault.height_range[1]],
    fluorescence_range: [LD_THEME.filtersDefault.fluorescence_range[0], LD_THEME.filtersDefault.fluorescence_range[1]],
    culet_range: [LD_THEME.filtersDefault.culet_range[0], LD_THEME.filtersDefault.culet_range[1]],
    polish_range: [LD_THEME.filtersDefault.polish_range[0], LD_THEME.filtersDefault.polish_range[1]],
    symmetry_range: [LD_THEME.filtersDefault.symmetry_range[0], LD_THEME.filtersDefault.symmetry_range[1]],
    crown_angle_range: [LD_THEME.filtersDefault.crown_angle_range[0], LD_THEME.filtersDefault.crown_angle_range[1]],
    pavilion_angle_range: [LD_THEME.filtersDefault.pavilion_angle_range[0], LD_THEME.filtersDefault.pavilion_angle_range[1]],
    girdle_range: [LD_THEME.filtersDefault.girdle_range[0], LD_THEME.filtersDefault.girdle_range[1]],
    ls_sortby: '',
    ls_sort: '',
    shipping: LD_THEME.filtersDefault.shipping,
    is_sale: LD_THEME.filtersDefault.is_sale,
  };
  const resetfilters_blank   = {
    shape:  LD_THEME.filters_blank.shape,
    price_range: [LD_THEME.filters_blank.price_range[0], LD_THEME.filters_blank.price_range[1]],
    carat_range: [LD_THEME.filters_blank.carat_range[0], LD_THEME.filters_blank.carat_range[1]],
    cut: [LD_THEME.filters_blank.cut_range[0], LD_THEME.filters_blank.cut_range[1]],
    intensity: LD_THEME.filters_blank.intensity,
    color: LD_THEME.filters_blank.color,
    clarity: [LD_THEME.filters_blank.clarity_range[0], LD_THEME.filters_blank.clarity_range[1]],
    ls_sku: LD_THEME.filters_blank.sku,
    certificate_type: LD_THEME.filters_blank.certificate_type,
    method: LD_THEME.filters_blank.method,
    media: LD_THEME.filters_blank.media || [],
    table_range: [LD_THEME.filters_blank.table_range[0], LD_THEME.filters_blank.table_range[1]],
    depth_range: [LD_THEME.filters_blank.depth_range[0], LD_THEME.filters_blank.depth_range[1]],
    lwratio_range: [LD_THEME.filters_blank.lwratio_range[0], LD_THEME.filters_blank.lwratio_range[1]],
    length_range: [LD_THEME.filters_blank.length_range[0], LD_THEME.filters_blank.length_range[1]],
    width_range: [LD_THEME.filters_blank.width_range[0], LD_THEME.filters_blank.width_range[1]],
    height_range: [LD_THEME.filters_blank.height_range[0], LD_THEME.filters_blank.height_range[1]],
    fluorescence_range: [LD_THEME.filters_blank.fluorescence_range[0], LD_THEME.filters_blank.fluorescence_range[1]],
    culet_range: [LD_THEME.filters_blank.culet_range[0], LD_THEME.filters_blank.culet_range[1]],
    polish_range: [LD_THEME.filters_blank.polish_range[0], LD_THEME.filters_blank.polish_range[1]],
    symmetry_range: [LD_THEME.filters_blank.symmetry_range[0], LD_THEME.filters_blank.symmetry_range[1]],
    crown_angle_range: [LD_THEME.filters_blank.crown_angle_range[0], LD_THEME.filters_blank.crown_angle_range[1]],
    pavilion_angle_range: [LD_THEME.filters_blank.pavilion_angle_range[0], LD_THEME.filters_blank.pavilion_angle_range[1]],
    girdle_range: [LD_THEME.filters_blank.girdle_range[0], LD_THEME.filters_blank.girdle_range[1]],
    ls_sortby: '',
    ls_sort: '',
    shipping: LD_THEME.filters_blank.shipping,
    is_sale: LD_THEME.filters_blank.is_sale,
  };

  
  const [filter, setFilter] = useState(() => ({ ...defaultFilter }));
  
  // Local input state for SKU search so typing doesn't trigger API calls
  // until the user commits via blur or Enter.
  const [skuInput, setSkuInput] = useState(defaultFilter.ls_sku || '');
   const [viewSkuParam, setViewSkuParam] = useState(null);
  const viewSkuScrolledRef = useRef(false);
  const scrollToSku = (sku) => {
    if (typeof window === 'undefined' || !sku) return;
    let tries = 0;
    const attempt = () => {
      const esc = (window.CSS && CSS.escape) ? CSS.escape(String(sku)) : String(sku);
      const el = document.querySelector(`[data-sku="${esc}"]`);
      if (el && el.offsetParent !== null) {
        const header = document.querySelector('header, .site-header');
        const offset = (header && getComputedStyle(header).position.match(/fixed|sticky/) ? header.offsetHeight : 0) + 20;
        const top = el.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      } else if (tries++ < 20) {
        setTimeout(attempt, 150);
      }
    };
    setTimeout(attempt, 300);
  };
    const [priceInput, setPriceInput] = useState(
    defaultFilter.price_range?.map(v => parseInt(v)) || []
  );
  const [caratInput, setCaratInput] = useState(defaultFilter.carat_range?.map(v => v.toFixed(2)) || []);
  const [tableInput, setTableInput] = useState(defaultFilter.table_range?.map(v => v.toFixed(2)) || []);
  const [depthInput, setDepthInput] = useState(defaultFilter.depth_range?.map(v => v.toFixed(2)) || []);
  const [lwratioInput, setLwratioInput] = useState(defaultFilter.lwratio_range?.map(v => v.toFixed(2)) || []);

  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);

  const [items, setItems] = useState([]);
  const [viewMode, setViewModeState] = useState(() => {
    const saved = readCookieValue('ld_view_mode');
    return saved === 'grid' || saved === 'list' ? saved : 'list';
  }); // 'list' (table) or 'grid' (cards)
  const setViewMode = (mode) => {
    setViewModeState(mode);
    writeCookieValue('ld_view_mode', mode, 365);
  };
  const [total, setTotal] = useState(0);
  const [cartError, setCartError] = useState({});
  
  const [isMobile, setIsMobile] = useState(window.innerWidth <= 768);

  const minPriceRef = useRef(null);
  const maxPriceRef = useRef(null);

  useEffect(() => {
    const handleResize = () => {
      setIsMobile(window.innerWidth <= 768);
    };

    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, []);

  const toggleSorting = (field) => {
    setFilter(prev => {
      if (prev.ls_sortby === field) {
        return {
          ...prev,
          ls_sort: prev.ls_sort === 'ASC' ? 'DESC' : 'ASC'
        };
      } else {
        return {
          ...prev,
          ls_sortby: field,
          ls_sort: 'ASC'
        };
      }
    });
  };

  const hasLoadedMobileRef = useRef(null);
  const cancelSourceRef = useRef(null);
  const priceDebounceRef = useRef(null);
  const prevFilterRef = useRef(null);
  const filterDebounceRef = useRef(null);

  const hasFilterChanged = JSON.stringify(filter) !== JSON.stringify(resetdefaultFilter);

  // Build URL query string from filter values (always emit filter fields)
  // buildQueryString now imported from ./components/inventoryHelpers

  
  
  // ─────────────────────────────────────────────
  // HELPER: reset all range input states to default
  // ─────────────────────────────────────────────
  const resetRangeInputs = () => {
    setPriceInput(LD_THEME.filtersDefault.price_range?.map(v => v.toFixed(2)) || []);
    setCaratInput(LD_THEME.filtersDefault.carat_range?.map(v => v.toFixed(2)) || []);
    setTableInput(LD_THEME.filtersDefault.table_range?.map(v => v.toFixed(2)) || []);
    setDepthInput(LD_THEME.filtersDefault.depth_range?.map(v => v.toFixed(2)) || []);
    setLwratioInput(LD_THEME.filtersDefault.lwratio_range?.map(v => v.toFixed(2)) || []);
  };

  // ─────────────────────────────────────────────
  // HELPER: clear SKU input & filter when any
  //         non-SKU filter changes
  // ─────────────────────────────────────────────
  const clearSku = () => {
    setSkuInput('');
  };

  const handleClearFilter = (e) => {
    e.preventDefault();
    
    minPriceRef.current.value = LD_THEME.inventoryRange.min_price;
    maxPriceRef.current.value = LD_THEME.inventoryRange.max_price;
    setFilter(resetdefaultFilter);
    setSkuInput(LD_THEME.filtersDefault.sku || '');
    setViewSkuParam(null);
    resetRangeInputs();

    if (isMobile) {
      hasLoadedMobileRef.current = false;
      resetAndFetch();
    }
  };

  // ─────────────────────────────────────────────
  // FILTER HANDLERS — each clears SKU
  // ─────────────────────────────────────────────

  // Handlers for filter changes
  // ─────────────────────────────────────────────
  // FILTER HANDLERS — each clears SKU
  // ─────────────────────────────────────────────
  const handleShapeChange = (value) => {
    clearSku();
    setFilter(prev => {
      const shapes = prev.shape.includes(value)
        ? prev.shape.filter(s => s !== value)
        : [...prev.shape, value];
      return { ...prev, shape: shapes, ls_sku: '' };
    });
  };

  const handlePriceChange = (values) => {
    clearSku();
    setFilter(prev => ({ ...prev, price_range: values, ls_sku: '' }));
  };

  const handleCaratChange = (values) => {
    clearSku();
    setFilter(prev => ({ ...prev, carat_range: values, ls_sku: '' }));
  };

  const handleCutChange = (values) => {
    console.log("values", values);
    clearSku();
    setFilter(prev => ({ ...prev, cut: values, ls_sku: '' }));
  };

  const handleColorChange = (values) => {
    clearSku();
    setFilter(prev => ({ ...prev, color: values, ls_sku: '' }));
  };

  const handleClarityChange = (values) => {
    clearSku();
    setFilter(prev => ({ ...prev, clarity: values, ls_sku: '' }));
  };

  // Handlers for filter changes
  const handleIntensityChange = (value) => {
    clearSku();
    setFilter(prev => {
      const intensities = prev.intensity.includes(value)
        ? prev.intensity.filter(i => i !== value)
        : [...prev.intensity, value];
      return { ...prev, intensity: intensities, ls_sku: ''  };
    });
  };

  // ─────────────────────────────────────────────
  // QUICK FILTER PILLS (above the grid/table) — same one-tap
  // sort/quality shortcuts as App.jsx. "Best Color" is intentionally
  // not included here: App2's `color` field is a single fancy-color
  // key (e.g. 'yellow'), not a D-Z range like App.jsx's — there's no
  // objective "best" fancy color to threshold against, and reusing
  // App.jsx's [7,9] range logic would overwrite `filter.color` with
  // an array the color-swatch UI/backend don't understand.
  // ─────────────────────────────────────────────
  const sameRange = (a, b) => Array.isArray(a) && Array.isArray(b) && Number(a[0]) === Number(b[0]) && Number(a[1]) === Number(b[1]);

  const handleQuickLargest = () => {
    clearSku();
    setFilter(prev => ({ ...prev, ls_sortby: 'carat', ls_sort: 'DESC' }));
  };
  const handleRemoveQuickLargest = () => {
    clearSku();
    setFilter(prev => ({ ...prev, ls_sortby: resetdefaultFilter.ls_sortby, ls_sort: resetdefaultFilter.ls_sort }));
  };

  const handleQuickLowestPrice = () => {
    clearSku();
    setFilter(prev => ({ ...prev, ls_sortby: 'price', ls_sort: 'ASC' }));
  };
  const handleRemoveQuickLowestPrice = () => {
    clearSku();
    setFilter(prev => ({ ...prev, ls_sortby: resetdefaultFilter.ls_sortby, ls_sort: resetdefaultFilter.ls_sort }));
  };

  const handleQuickHighestPrice = () => {
    clearSku();
    setFilter(prev => ({ ...prev, ls_sortby: 'price', ls_sort: 'DESC' }));
  };
  const handleRemoveQuickHighestPrice = () => {
    clearSku();
    setFilter(prev => ({ ...prev, ls_sortby: resetdefaultFilter.ls_sortby, ls_sort: resetdefaultFilter.ls_sort }));
  };

  // Top Quality uses cut + clarity only (no color threshold — see note above).
  const handleQuickTopQuality = () => {
    clearSku();
    setFilter(prev => ({ ...prev, cut: [2, 4], color: [7, 10], clarity: [6, 11], ls_sku: '' }));
  };
  const handleRemoveQuickTopQuality = () => {
    clearSku();
    setFilter(prev => ({ ...prev, cut: resetdefaultFilter.cut, clarity: resetdefaultFilter.clarity, ls_sku: '' }));
  };

  const handleQuickFLVS2 = () => {
    clearSku();
    setFilter(prev => ({ ...prev, clarity: [5, 10], ls_sku: '' }));
  };
  const handleRemoveQuickFLVS2 = () => {
    clearSku();
    setFilter(prev => ({ ...prev, clarity: resetdefaultFilter.clarity, ls_sku: '' }));
  };

  const handleQuickIGI = () => {
    clearSku();
    setFilter(prev => ({ ...prev, certificate_type: ['1'], ls_sku: '' }));
  };
  const handleRemoveQuickIGI = () => {
    clearSku();
    setFilter(prev => ({ ...prev, certificate_type: resetdefaultFilter.certificate_type, ls_sku: '' }));
  };

  const handleQuickGIA = () => {
    clearSku();
    setFilter(prev => ({ ...prev, certificate_type: ['2'], ls_sku: '' }));
  };
  const handleRemoveQuickGIA = () => {
    clearSku();
    setFilter(prev => ({ ...prev, certificate_type: resetdefaultFilter.certificate_type, ls_sku: '' }));
  };

  const isLargestActive = filter.ls_sortby === 'carat' && filter.ls_sort === 'DESC';
  const isLowestPriceActive = filter.ls_sortby === 'price' && filter.ls_sort === 'ASC';
  const isHighestPriceActive = filter.ls_sortby === 'price' && filter.ls_sort === 'DESC';
  const isTopQualityActive = sameRange(filter.cut, [2, 4]) && sameRange(filter.clarity, [6, 11]);
  const isFLVS2Active = sameRange(filter.clarity, [5, 10]);
  const isIGIActive = Array.isArray(filter.certificate_type) && filter.certificate_type.length === 1 && filter.certificate_type[0] === '1';
  const isGIAActive = Array.isArray(filter.certificate_type) && filter.certificate_type.length === 1 && filter.certificate_type[0] === '2';

  // "Clear all" (.ls-carat-pill-clear) on the carat-pills row clears every
  // quick pill in that row: carat range, sort-order (Largest, Lowest
  // Price, Highest Price), and quality/certificate (Top Quality, Clarity,
  // IGI, GIA) — it must not touch shape, method, color, intensity, or any
  // other sidebar filter.
  const isCaratQuickRangeActive = !sameRange(filter.carat_range, resetdefaultFilter.carat_range);
  const isSortOrderQuickActive = isLargestActive || isLowestPriceActive || isHighestPriceActive;
  const isQualityQuickActive = isTopQualityActive || isFLVS2Active || isIGIActive || isGIAActive;
  const hasQuickPillActive = isCaratQuickRangeActive || isSortOrderQuickActive || isQualityQuickActive;
  const handleClearQuickPills = () => {
    clearSku();
    setFilter(prev => ({
      ...prev,
      carat_range: resetdefaultFilter.carat_range,
      ls_sortby: resetdefaultFilter.ls_sortby,
      ls_sort: resetdefaultFilter.ls_sort,
      cut: resetdefaultFilter.cut,
      clarity: resetdefaultFilter.clarity,
      certificate_type: resetdefaultFilter.certificate_type,
      ls_sku: '',
    }));
    setCaratInput((resetdefaultFilter.carat_range || []).map(v => Number(v).toFixed(2)));
  };

  const handleCertificateChange = (value) => {
    clearSku();
    setFilter(prev => {
      const current = prev.certificate_type || [];
      const certificate = current.includes(value)
        ? current.filter(v => v !== value)
        : [...current, value];
      return { ...prev, certificate_type: certificate, ls_sku: '' };
    });
  };

  const handleMediaChange = (value) => {
    clearSku();
    setFilter(prev => {
      const current = prev.media || [];
      const media = current.includes(value) ? [] : [value];
      return { ...prev, media: media, ls_sku: '' };
    });
  };

  const handleMethodChange = (value) => {
    clearSku();
    setFilter(prev => {
      const current = prev.method || [];
      const method = current.includes(value)
        ? current.filter(v => v !== value)
        : [...current, value];
      return { ...prev, method: method, ls_sku: '' };
    });
  };

  const handleTableChange = (values) => {
    clearSku();
    setFilter(prev => ({ ...prev, table_range: values, ls_sku: '' }));
  };

  const isTableModified =
    Number(filter.table_range[0].toFixed(2)) !== Number(LD_THEME.filtersDefault.table_range[0].toFixed(2)) ||
    Number(filter.table_range[1].toFixed(2)) !== Number(LD_THEME.filtersDefault.table_range[1].toFixed(2));

  const handleDepthChange = (values) => {
    clearSku();
    setFilter(prev => ({ ...prev, depth_range: values, ls_sku: '' }));
  };

  const isDepthModified =
    Number(filter.depth_range[0].toFixed(2)) !== Number(LD_THEME.filtersDefault.depth_range[0].toFixed(2)) ||
    Number(filter.depth_range[1].toFixed(2)) !== Number(LD_THEME.filtersDefault.depth_range[1].toFixed(2));

  const handleLwratioChange = (values) => {
    clearSku();
    setFilter(prev => ({ ...prev, lwratio_range: values, ls_sku: '' }));
  };

  const isLwratioModified =
    Number(filter.lwratio_range[0].toFixed(2)) !== Number(LD_THEME.filtersDefault.lwratio_range[0].toFixed(2)) ||
    Number(filter.lwratio_range[1].toFixed(2)) !== Number(LD_THEME.filtersDefault.lwratio_range[1].toFixed(2));
    console.log("isLwratioModified", isLwratioModified);
    console.log("isLwratioModifiedss", filter.lwratio_range, LD_THEME.filtersDefault.lwratio_range);

  // ─────────────────────────────────────────────
  // Advanced Filters (sidebar): Length / Width / Height / Fluorescence /
  // Polish / Symmetry / Crown Angle / Pavilion Angle / Girdle Thickness.
  // These live directly on `filter` (length_range, width_range, etc. —
  // see defaultFilter/resetdefaultFilter/resetfilters_blank above)
  // instead of a separate advancedFilters state, so they're reset,
  // diffed, and sent to the API the exact same way every other filter
  // is. Mirrors App.jsx's consolidation.
  // ─────────────────────────────────────────────
  const setAdvancedInput = (key, values) => {
    setFilter(prev => ({ ...prev, [key]: values }));
  };

  const commitAdvanced = (key, values) => {
    clearSku();
    setFilter(prev => ({ ...prev, [key]: values }));
  };

  const fetchInventory = async (pageNumber = 1, replace = false, overrideFilter = null) => {
    // ✅ allow page 1 / replace even if hasMore was false
    if (!hasMore && pageNumber !== 1 && !replace) return;

    // allow starting a fresh/replace fetch for page 1 even if a previous request
    // is still marked loading (we will cancel the previous request below).
    if (isLoading && !(replace || pageNumber === 1)) return;

    // cancel any in-flight request before starting a new one
    if (cancelSourceRef.current) {
      try { cancelSourceRef.current.cancel('New request starting'); } catch (e) { }
      cancelSourceRef.current = null;
    }
    console.log("Starting API call with filter:",filter);

    setLoading(true);
    let source;
    try {

      setOpenDetail(null);
      updateSkuQuery(null);


      source = axios.CancelToken.source();
      cancelSourceRef.current = source;

      const requestFilter = overrideFilter || filter;


      // Build payload: always include pagination, otherwise only include
      // filter fields that differ from the defaults to avoid sending
      // redundant values.
      const payload = { ls_start: pageNumber, ls_per_page: 52 };
      const addIfDifferent = (key, val, defVal, transform) => {
        const transformedVal = transform ? transform(val) : val;
        const transformedDef = transform ? transform(defVal) : defVal;
        const isObj = val && typeof val === 'object';
        if (isObj) {
          if (JSON.stringify(val) !== JSON.stringify(defVal)) payload[key] = transformedVal;
        } else {
          if (transformedVal !== undefined && transformedVal !== null && transformedVal !== '' && transformedVal !== transformedDef) {
            payload[key] = transformedVal;
          }
        }
      };

      // requestFilter may be overrideFilter or current filter
      const rf = requestFilter;
      const df = resetdefaultFilter || {};
      console.log("rf", rf, "df", df);
      addIfDifferent('price_range', rf.price_range, df.price_range, v => Array.isArray(v) ? v.join(',') : v);
      
      addIfDifferent('carat_range', rf.carat_range, df.carat_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('cut_range', rf.cut, df.cut, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('colored', COLOR_OPTIONS.find(c => c.key === String(rf.color))?.key_val || '', COLOR_OPTIONS.find(c => c.key === String(df.color))?.key_val || '');
      addIfDifferent('clarity_range', rf.clarity, df.clarity, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('intensity', rf.intensity, df.intensity);
      addIfDifferent('certificate_type', rf.certificate_type, df.certificate_type);
      addIfDifferent('dtype', rf.method, df.method);
      if (Array.isArray(rf.media) && rf.media.length > 0) {
        payload.is_media = 1;
      }
      addIfDifferent('table_range', rf.table_range, df.table_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('depth_range', rf.depth_range, df.depth_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('lwratio_range', rf.lwratio_range, df.lwratio_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('ls_sku', rf.ls_sku || '', df.ls_sku || '');
      addIfDifferent('ls_sortby', rf.ls_sortby || '', df.ls_sortby || '');
      addIfDifferent('ls_sort', rf.ls_sort || '', df.ls_sort || '');
      addIfDifferent('shipping', rf.shipping, df.shipping);
      addIfDifferent('is_sale', rf.is_sale, df.is_sale);

      // Advanced Filters (sidebar) — these now live directly on filter/resetdefaultFilter.
      addIfDifferent('length_range', rf.length_range, df.length_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('width_range', rf.width_range, df.width_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('height_range', rf.height_range, df.height_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('fluorescence_range', rf.fluorescence_range, df.fluorescence_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('culet_range', rf.culet_range, df.culet_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('polish_range', rf.polish_range, df.polish_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('symmetry_range', rf.symmetry_range, df.symmetry_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('crown_angle_range', rf.crown_angle_range, df.crown_angle_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('pavilion_angle_range', rf.pavilion_angle_range, df.pavilion_angle_range, v => Array.isArray(v) ? v.join(',') : v);
      addIfDifferent('girdle_range', rf.girdle_range, df.girdle_range, v => Array.isArray(v) ? v.join(',') : v);
      
      //addIfDifferent('shape', rf.shape, df.shape);
      payload.shape = rf.shape ;
      // Ring builder: tell the API so it excludes Matching Pair (certificate_type 4) diamonds.
      payload.inventory_type = inventoryType;
      const response = await axios.post(
        `${API_BASE_URL}/inventory-colored`,
        payload,
        {
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true, // IMPORTANT if API relies on cookies/session  
          cancelToken: source.token,
        }
      );

      const newItems = response.data?.data || [];
      const itemTotal = response.data?.total || 0;

      setTotal(itemTotal);

      setItems(prev => {
        const updated = replace ? newItems : [...prev, ...newItems];

        if (updated.length >= itemTotal) {
          setHasMore(false);
        }
        setLoading(false);
        return updated;
      });
    } catch (error) {
      if (axios.isCancel && axios.isCancel(error)) {
        // request was cancelled — ignore
      } else if (error && (error.code === 'ECONNABORTED' || error.code === 'ETIMEDOUT')) {
        console.error('API Error: request timed out', error);
        // stop infinite retry loop if API fails
        setHasMore(false);
      } else {
        console.error("API Error:", error);
        // stop infinite retry loop if API fails
        setHasMore(false);
      }
    } finally {
      // Only clear the shared ref if it still points at THIS request's
      // own cancel source. If a newer fetchInventory call has already
      // taken over cancelSourceRef.current (e.g. filter/sort changed
      // again while this request was in flight), clearing it here would
      // wipe out the newer request's cancel token — a stray older
      // response could then never be cancelled and race the newer one,
      // overwriting `total` with a stale count.
      if (cancelSourceRef.current === source) {
        cancelSourceRef.current = null;
      }
      //setLoading(false);
      console.log("API call completed");
    }
  };
 const performAddToCart = async (pid, did, typeOverride) => {
    const cartType = typeOverride || inventoryType;

    if (cartType !== 'ringbuilder_c' && cartProductIds && pid && cartProductIds.map(Number).includes(Number(pid))) {
      window.location.href = '/cart';
      return;
    }

    setLoadingCart(`${did}:${cartType}`);
    setCartError({}); // reset previous error

    try {
      const ajaxUrl = (typeof window !== 'undefined' && window.ls_ajax_object && window.ls_ajax_object.ajax_url) ? window.ls_ajax_object.ajax_url : ajaxUrl;
      const nonce = (typeof window !== 'undefined' && window.ls_ajax_object && window.ls_ajax_object.nonce) ? window.ls_ajax_object.nonce : '';

      const form = new FormData();
      form.append('action', 'ls_add_to_cart_c');
      form.append('product_id', pid);
      form.append('iid', did);
      form.append('quantity', 1);
      form.append('type', cartType);
      if (nonce) form.append('nonce', nonce);

      const response = await fetch(ajaxUrl, {
        method: 'POST',
        body: form,
        credentials: 'same-origin'
      });

      const json = await response.json();

      if (!response.ok || (json && json.success === false)) {
        const msg = (json && (json.data || json.message)) || 'Failed to add to cart. Please try again.';
        console.error('Add to cart failed', msg);
        setCartError({ [did]: msg });
        return;
      }

      const data = json.success ? json.data : json;

      if (data.status) {
        console.log('✅ Added to cart');
        console.log('Cart count:', data.cart_count);
        if (data.redirect) window.location.href = data.redirect;
      } else {
        console.error('Add to cart failed', data);
        setCartError({ [did]: data.content || 'Failed to add to cart. Please try again.' });
      }
    } catch (err) {
      console.error('❌ API error', err);
      setCartError({ [did]: 'Failed to add to cart due to a network error. Please try again.' });
    } finally {
      setLoadingCart(null);
      setConfirmModalOpen(false);
      setConfirmItem(null);
    }
  };
  const handleAddToCartClick = async (pid, did, typeOverride) => {
    const item = items && items.find(i => String(i.id) === String(did));
    const cartType = typeOverride || inventoryType;

    if (cartType !== 'ringbuilder_c' && cartProductIds && pid && cartProductIds.map(Number).includes(Number(pid))) {
      window.location.href = '/cart';
      return;
    }

    // If this is a ringbuilder flow and the item is non-certified, show confirmation
    if (item) {
      const isNonCertified = (item.certificate_type === 0) || !(item.certificate && item.certificate.trim());
      if (isNonCertified) {
        setConfirmItem({ ...item, __addType: cartType });
        setConfirmModalOpen(true);
        return;
      }
    }

    await performAddToCart(pid, did, typeOverride);


 
    
  };

  const resetAndFetch = (pageNumber = 1, replace = true, overrideFilter = null) => {
    if (cancelSourceRef.current) {
      try { cancelSourceRef.current.cancel('Reset'); } catch (e) { }
      cancelSourceRef.current = null;
    }
    setPage(1);
    setItems([]);
    setTotal(0);
    setHasMore(true);
    fetchInventory(pageNumber, replace, overrideFilter);
  };
  
  const handleSkuKeyDown = (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      handleSkuCommit(true);
      try { e.currentTarget.blur(); } catch (err) { }
    }
  };

  // ─────────────────────────────────────────────
  // SKU COMMIT — resets ALL other filters to default
  // ─────────────────────────────────────────────
  const handleSkuCommit = (forceFetch = false) => {
    const val = (skuInput || '').trim();
    if (forceFetch) {
      // Use the BLANK filter set (not defaultFilter) so filters seeded from the
      // page's ACF `filter_url` (shape/carat/color/...) are reset on SKU search.
      const nf = { ...resetfilters_blank, ls_sku: val };
      // Keep side effects OUT of the setFilter updater (React can invoke
      // updater functions more than once, e.g. Strict Mode, which would
      // call resetAndFetch twice and the second call would cancel the
      // request the first call just started) and sync prevFilterRef so
      // the filter-watching effect doesn't schedule a duplicate fetch.
      prevFilterRef.current = JSON.stringify(nf);
      setFilter(nf);
      // Skipping the debounced effect above also skips the URL update it
      // normally does for every other filter (carat, color, etc.), so do
      // it here explicitly to keep SKU search consistent with the rest.
      try {
        const qs = buildQueryString(nf, defaultFilter);
        window.history.replaceState(null, '', `${window.location.pathname}${qs}`);
      } catch (err) { /* ignore */ }
      resetAndFetch(1, true, nf);
      resetRangeInputs();
    } else {
      if (!isMobile) {
        const nf = { ...resetfilters_blank, ls_sku: val };
        prevFilterRef.current = JSON.stringify(nf);
        setFilter(nf);
        // Same URL sync as the forceFetch branch above.
        try {
          const qs = buildQueryString(nf, defaultFilter);
          window.history.replaceState(null, '', `${window.location.pathname}${qs}`);
        } catch (err) { /* ignore */ }
        resetAndFetch(1, true, nf);
        resetRangeInputs();
      } else {
        // mobile: just update filter and wait for "View Results" click.
        // No manual URL sync needed — this goes through the normal
        // (non-bypassed) debounced filter effect, which already updates
        // the URL the same way it does for every other filter.
        setFilter(prev => ({ ...resetfilters_blank, ls_sku: val }));
        resetRangeInputs();
      }
    }
  };

  useEffect(() => {

    if (items.length === 1 && !viewSkuParam) {
      const single = items[0];
      if (single) {
        try { setViewSkuParam(single.sku); } catch (e) { /* ignore */ }
        openDetailForItem(single, items, setOpenDetail);
      }
      return;
    }

    
    if (!viewSkuParam || items.length === 0) return;
    const matchedItem = items.find((item) => item.sku === viewSkuParam);
    if (matchedItem) {
      openDetailForItem(matchedItem, items, setOpenDetail);
      // Scroll to the diamond once on page load (viewsku from URL)
      if (!viewSkuScrolledRef.current) {
        viewSkuScrolledRef.current = true;
        scrollToSku(matchedItem.sku);
      }
    }
  }, [items, viewSkuParam]);

  useEffect(() => {
    if (typeof window === 'undefined') return;
    try {
      const params = new URLSearchParams(window.location.search);
      if (params.has('viewsku')) {
        const viewsku = params.get('viewsku');
        if (viewsku) setViewSkuParam(viewsku);
      }
    } catch (err) {
      // ignore
    }
  }, []);

  const openSkuInNewTab = (e) => {
    // Only open when Ctrl (or Meta) is held during click
    if (!e || !(e.ctrlKey || e.metaKey)) return;
    const val = (skuInput || '').trim();
    if (!val) return;
    try {
      const qs = buildQueryString({ ls_sku: val }, defaultFilter, { colorDefaultKey: 'color', heartarrowParam: 'hearts-and-arrows', debug: true });
      const url = `${SITE_URL}/lab-created-colored-diamonds/${qs}`;
      window.open(url, '_blank');
    } catch (err) {
      console.error('openSkuInNewTab error', err);
    }
  };


  // Debounced fetch when filters actually change. Cancels previous axios requests.
  useEffect(() => {
    // ❌ On mobile → DO NOTHING (wait for button click)
 //   if (isMobile && hasLoadedMobileRef.current) return;

    const filterString = JSON.stringify(filter);
    if (prevFilterRef.current === filterString && hasLoadedMobileRef.current) return; // no real change
    prevFilterRef.current = filterString;
    hasLoadedMobileRef.current = true;

    // cancel any pending debounce
    if (filterDebounceRef.current) {
      clearTimeout(filterDebounceRef.current);
      filterDebounceRef.current = null;
    }

    // start debounce
    filterDebounceRef.current = setTimeout(() => {
      // cancel any in-flight axios request
      if (cancelSourceRef.current) {
        try { cancelSourceRef.current.cancel('Filter changed'); } catch (e) { }
        cancelSourceRef.current = null;
      }

      // update URL to reflect only active (non-default) filters
      try {
        const qs = buildQueryString(filter, defaultFilter, { colorDefaultKey: 'color', heartarrowParam: 'hearts-and-arrows', debug: true });
       // if (qs) {
          const newUrl = `${window.location.pathname}${qs}`;
          window.history.replaceState(null, '', newUrl);
        //}
      } catch (err) {
        // ignore
      }

      setPage(1);
      setItems([]);
      setTotal(0);
      setHasMore(true);
      fetchInventory(1, true);
      filterDebounceRef.current = null;
    }, 250);

    return () => {
      if (filterDebounceRef.current) {
        clearTimeout(filterDebounceRef.current);
        filterDebounceRef.current = null;
      }
      // NOTE: intentionally NOT cancelling cancelSourceRef.current here.
      // This cleanup runs on every filter/isMobile change, including
      // setFilter() calls made directly by handleSkuCommit (which starts
      // its own request via resetAndFetch). Cancelling here would kill
      // that just-started request even though this effect's own debounced
      // fetch never fired. fetchInventory() already cancels any stale
      // in-flight request itself right before starting a new one.
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [filter, isMobile]);

  // ===============================
  // LOAD NEXT PAGE
  // ===============================
  useEffect(() => {
    if (page > 1) {
      fetchInventory(page);
    }
  }, [page]);

  // Previously used IntersectionObserver to auto-load next pages.
  // Replaced by an explicit "Load more" button to trigger pagination.

  // checkbox counts are initialized on mount; update them from explicit handlers if needed

  // cleanup debounced timers on unmount
  useEffect(() => {
    return () => {
      if (priceDebounceRef.current) clearTimeout(priceDebounceRef.current);
    };
  }, []);

  // fallback color
  const svgFill = useMemo(() => {
   // console.log("COLOR_OPTIONSA",COLOR_OPTIONS,filter);
    return (
      COLOR_OPTIONS.find(c => c.key === filter.color)
        ?.label.toLowerCase() || "rgb(49, 65, 133)"
    );
  }, [filter.color]);

  const renderShapes = (shapeList) => shapeList.map((s) => {
    const isRingBuilder = inventoryType === "ringbuilder_c";
    const isSelected = isRingBuilder ? filter.shape?.[0] === s.value : filter.shape?.includes(s.value);

    return (
      <label
        key={s.id}
        htmlFor={s.id}
        className={`shape-box ${isSelected ? "active" : ""} ${isRingBuilder && !isSelected ? "disabled" : ""
          }`}
      >
        {isRingBuilder ? (
          isSelected && (
            <input
              type="checkbox"
              name="shape[]"
              value={s.value}
              id={s.id}
              className="checkbox"
              checked
            />
          )
        ) : (
          <input
            type="checkbox"
            name="shape[]"
            value={s.value}
            id={s.id}
            className="checkbox"
            checked={isSelected}
            onChange={() => handleShapeChange(s.value)}
          />
        )}

        <span className="shape-box-inner">
          <img
            src={`${templateUrl}${s.img}`}
            alt={s.alt}
            className="shape-image"
          />
          <p className="text-capitalize">{s.label}</p>
        </span>
      </label>
    );
  });
  
  // Replace the widgetRef + useEffect with this callback ref:
  const widgetRef = useCallback((node) => {
    if (!node) return;
    const tryLoad = () => {
      if (window.Trustpilot) {
        window.Trustpilot.loadFromElement(node, true);
      } else {
        setTimeout(tryLoad, 300);
      }
    };
    tryLoad();
  }, []);

   const certificateLabelsMap = { '1': 'igi', '2': 'gia', '3': 'gcal', '0': 'non certified', '4': 'matching pair' };
  const selectedCertificateLabels = (filter.certificate_type || []).map(v => certificateLabelsMap[v] || v).join(', ');
  const methodLabelsMap = { 'hpht': 'hpht', 'cvd': 'cvd' };
  const selectedMethodLabels = (filter.method || []).map(v => methodLabelsMap[v] || v).join(', ');

  return (
    <div className="ls_inventorypg" ref={containerRef}>
      <div>
        <div className="container-fluid">

          <div className="d-flex align-items-center justify-content-between mb_20 d-lg-none">
          {Number(total) > 0 ? (
              <p className="mb-0 text-black diamond-result text-nowrap">
                <span className="item-result-count">
                  {Number(total).toLocaleString()}
                </span>
                &nbsp;diamonds found
              </p>
            ) : (
              <p className="mb-0 text-black diamond-result text-nowrap">
                Loading diamonds...
              </p>
            )}
            <button
              type="button"
              className="fil_common_btn fil_common_btn_black fw-normal d-inline-flex align-items-center gap-1"
              onClick={() => setFiltersModalOpen(true)}
            >
              Filter &amp; Sort
              {(() => {
                const activeCount = countActiveFilters(filter, resetdefaultFilter, { colorDefaultKey: 'color' });
                return activeCount > 0 ? ` (${activeCount})` : '';
              })()}
            </button>
          </div>

          <div className="ls-mobile-shape-scroll d-lg-none mb_20">
            {renderShapes(shapes)}
          </div>

          <div className="d-none d-lg-block">
            <div className="row align-items-center">
              <div className="col-lg-12">
                <div className="d-flex align-items-center mb_70 shape">
                  <FieldLabel label="Color" tooltip={TOOLTIP_TEXT.fancyColor} />

                  <div className="d-flex flex-wrap gap_10 ms_15">
                    {COLOR_OPTIONS.map((color) => (
                      <div
                        key={color.key}
                        className={`color_diamond text-center ${filter.color == color.key ? 'active' : ''}`}
                        onClick={() => handleColorChange(color.key)}
                        role="button"
                      >
                        <div>
                          <img
                            src={`${templateUrl}/assets/image/diamond/${color.img}`}
                            alt={color.label}
                          />
                          <p className="mb-0">{color.label}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>

              {/* Shape form selection start */}
              <div className="col-lg-6">
                <div className="d-flex align-items-center mb_70 shape">
                  <FieldLabel label="Shape" tooltip={TOOLTIP_TEXT.shape} />
                  <div className="d-flex align-items-center w-100 diamond-filter">
                    <div className="d-flex align-items-center flex-column gap-4 w-100">
                      {/* Shape Box */}
                      <div className="d-flex align-items-center justify-content-between flex-wrap flex-lg-nowrap w-100">
                        {renderShapes(shapes)}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {/* price form selection section */}
              <div className="col-lg-6">
                <RangeFilterField
                  idPrefix="price"
                  className="ls-price-range-field"
                  label="Price"
                  tooltip={TOOLTIP_TEXT.price}
                  min={LD_THEME.inventoryRange.min_price}
                  max={LD_THEME.inventoryRange.max_price}
                  step={1}
                  midValue={9500}
                  pips={{ values: [100, 9500, LD_THEME.inventoryRange.max_price], format: (v) => new Intl.NumberFormat('en-US').format(v) }}
                  value={priceInput}
                  minRef={minPriceRef}
                  maxRef={maxPriceRef}
                  onInputChange={setPriceInput}
                  onCommit={handlePriceChange}
                />
              </div>

              {/* carat form selection section */}
              <div className="col-lg-6">
                <RangeFilterField
                  idPrefix="carat"
                  className="ls-carat-range-field"
                  label="Carat"
                  tooltip={TOOLTIP_TEXT.carat}
                  min={LD_THEME.inventoryRange.min_carat}
                  max={LD_THEME.inventoryRange.max_carat}
                  step={0.01}
                  midValue={6}
                  value={caratInput}
                  onInputChange={setCaratInput}
                  onCommit={handleCaratChange}
                />
              </div>

              {/* cut form selection section */}
              <div className="col-lg-6">
                <div className="d-flex align-items-center mb_70">
                  <FieldLabel label="Cut" tooltip={TOOLTIP_TEXT.cut} />
                  <FilterSlider field="cut" start={filter.cut[0]} end={filter.cut[1]} onChange={handleCutChange} />
                  <div>
                  </div>
                </div>
              </div>

              {/* intensity box selection section */}
              <div className="col-lg-6">
                <div className="d-flex align-items-center mb_70">
                  <FieldLabel label="Intensity" tooltip={TOOLTIP_TEXT.intensity} />
                  <div className="d-flex flex-wrap ms_15 gap_10">
                    {Array.from({ length: 6 }, (_, i) => i + 1).map((idx) => (
                      <div className="position-relative" key={idx}>
                        <div className={"intensity_box " + (filter.intensity.includes(idx) ? 'active' : '')} onClick={() => handleIntensityChange(idx)}>
                          <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="48.000000pt" height="53.000000pt" viewBox="0 0 48.000000 53.000000" preserveAspectRatio="xMidYMid meet">
                            <g transform="translate(0.000000,53.000000) scale(0.100000,-0.100000)" fill="rgb(49, 65, 133)" stroke="none" style={{ fill: svgFill, opacity: 0.2 + ((idx - 1) / 5) * 0.8 }}>
                              <path d="M127 471 l-98 -58 36 -22 36 -21 65 36 c64 36 64 36 64 80 0 24 -1 44 -2 43 -2 0 -47 -26 -101 -58z"></path>
                              <path d="M262 483 c3 -43 5 -46 63 -79 l61 -33 36 22 36 22 -100 57 -99 57 3 -46z"></path>
                              <path d="M178 374 l-58 -35 0 -69 0 -70 41 -25 c79 -50 81 -51 145 -13 l59 34 3 71 3 72 -64 35 c-35 20 -66 36 -68 35 -2 0 -30 -16 -61 -35z"></path>
                              <path d="M10 270 c0 -60 3 -110 6 -110 4 0 22 9 40 21 33 20 34 22 34 89 0 67 0 68 -36 89 -19 12 -37 21 -40 21 -2 0 -4 -49 -4 -110z"></path>
                              <path d="M433 358 c-32 -19 -33 -22 -33 -89 0 -67 1 -70 33 -89 17 -11 35 -20 40 -20 4 0 7 50 7 110 0 61 -3 110 -7 109 -5 0 -23 -10 -40 -21z"></path>
                              <path d="M65 145 l-37 -22 58 -33 c33 -18 78 -44 102 -57 l42 -25 0 44 c0 42 -1 43 -64 79 l-64 37 -37 -23z"></path>
                              <path d="M322 132 c-61 -36 -62 -37 -62 -80 0 -44 0 -44 23 -30 12 8 57 34 99 58 43 24 77 44 75 45 -1 1 -18 11 -37 23 l-36 21 -62 -37z"></path>
                            </g>
                          </svg>
                        </div>
                        <span className={"intensity-name position-absolute top-100 " + (filter.intensity.includes(idx) ? '' : 'd-none')}>{intensityLabels[idx - 1]}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>

              {/* clarity form selection section */}
              <div className="col-lg-6">
                <div className="d-flex align-items-center mb_70">
                  <FieldLabel label="Clarity" tooltip={TOOLTIP_TEXT.clarity} />
                  <FilterSlider field="clarity" start={filter.clarity[0]} end={filter.clarity[1]} onChange={handleClarityChange} />
                  <div>
                  </div>
                </div>
              </div>

            </div>
          </div>
          {window.themeData.WEBSITE_TYPE=='RETAIL' && (
            <div className="mb-2">

              {/* Desktop Image */}
              <img
                src={`${templateUrl}/aj_custom/inventory/festival.webp`}
                alt="festival"
                className="img-fluid d-none d-md-block"
              />

              {/* Mobile Image */}
              <img
                src={`${templateUrl}/aj_custom/inventory/festival_mob.webp`}
                alt="festival"
                className="img-fluid d-block d-md-none"
              />

            </div>
          )}
          <div className="filter_resultsec">
            <div className="d-flex align-items-center justify-content-between">
              <div className="d-lg-flex align-items-center d-none gap-2 shipingicons">
                <button
                  type="button"
                  className={`fil_common_btn ${filter.shipping === 2 ? 'active' : 'black_border_btn'} fw-normal d-flex  gap-1 gap-sm-2`}
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  data-bs-original-title="Overnight Delivery Available — Order Before 11:00 AM EDT"
                  onClick={() => {
                    clearSku();
                    setFilter(prev => {
                      const nextShipping = prev.shipping === 2 ? 0 : 2;
                      return { ...prev, shipping: nextShipping, ls_sku: '' };
                    });
                  }}
                  
                >
                  <img src={`${templateUrl}/aj_custom/inventory/space-rocket.svg`}/>
                  Overnight
                </button>
                <button
                  type="button"
                  className={`fil_common_btn ${filter.shipping === 1 ? 'active' : 'black_border_btn'} fw-normal d-flex  gap-1 gap-sm-2`}
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  data-bs-original-title="Ship Within 3 Days"
                  onClick={() => {
                    clearSku();
                    setFilter(prev => {
                      const nextShipping = prev.shipping === 1 ? 0 : 1;
                      return { ...prev, shipping: nextShipping, ls_sku: '' };
                    });
                  }}
                  
                >
                  <img src={`${templateUrl}/aj_custom/inventory/delivery.svg`}/>
                  Express 
                </button>
                <button
                  type="button"
                  className={`fil_common_btn ${filter.is_sale === 1 ? 'active' : 'black_border_btn'} fw-normal d-flex  gap-1 gap-sm-2 oc_sale_icon`}
                  style={{ color: 'red' }}
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  data-bs-original-title="Sale"
                  onClick={() => {
                    clearSku();
                    setFilter(prev => {
                      const nextSale = prev.is_sale === 1 ? 0 : 1;
                      return { ...prev, is_sale: nextSale, ls_sku: '' };
                    });
                  }}
                  
                >
                  <img src={`${templateUrl}/assets/image/header/fire.gif`}/>
                  Sale
                </button>
              </div>

              <div  className="d-lg-none d-flex gap-2 align-items-center ms-2 shipingicons">
                  <button
                    type="button"
                    className={`fil_common_btn ${filter.shipping === 2 ? 'active' : 'black_border_btn'} fw-medium d-flex  gap-1 gap-sm-2`}
                    onClick={() => {
                      clearSku();
                      setFilter(prev => {
                        const nextShipping = prev.shipping === 2 ? 0 : 2;
                        return { ...prev, shipping: nextShipping, ls_sku: '' };
                      });
                    }}
                    
                  >
                    <img src={`${templateUrl}/aj_custom/inventory/space-rocket.svg`}/>
                    Overnight
                  </button>

                  <button
                    type="button"
                    className={`fil_common_btn ${filter.shipping === 1 ? 'active' : 'black_border_btn'} fw-medium d-flex  gap-1 gap-sm-2`}
                    onClick={() => {
                      clearSku();
                      setFilter(prev => {
                        const nextShipping = prev.shipping === 1 ? 0 : 1;
                        return { ...prev, shipping: nextShipping, ls_sku: '' };
                      });
                    }}
                  
                  >
                    <img src={`${templateUrl}/aj_custom/inventory/delivery.svg`}/>
                    Express 
                  </button>

                  <button
                    type="button"
                    className={`fil_common_btn ${filter.is_sale === 1 ? 'active' : 'black_border_btn'} fw-normal d-flex  gap-1 gap-sm-2 oc_sale_icon`}
                    style={{ color: 'red' }}
                    onClick={() => {
                      clearSku();
                      setFilter(prev => {
                        const nextSale = prev.is_sale === 1 ? 0 : 1;
                        return { ...prev, is_sale: nextSale, ls_sku: '' };
                      });
                    }}
                    
                  >
                    <img src={`${templateUrl}/assets/image/header/fire.gif`}/>
                    Sale
                  </button>
                
              </div>

              {Number(total) > 0 && (
                <p className="mb-0 text-black diamond-result text-nowrap d-none d-lg-block">
                  <span className="item-result-count">{Number(total).toLocaleString()}</span>
                  <span className="d-none d-md-inline-block">&nbsp;lab grown diamonds</span>&nbsp;found
                </p>
              )}

              <div className="d-flex gap-2 align-items-center">
                <div className="view-button-group">
                   <button type="button" className={viewMode === 'list' ? 'active' : ''} style={{ color: viewMode === 'list' ? '#fff' : '#0B131A' }} onClick={() => setViewMode('list')} aria-label="List view">
                     <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path d="M16.6668 4.61543H3.3335V3.33337H16.6668V4.61543Z" fill="currentColor"></path>
                       <path d="M16.6668 15.8334H3.3335V14.5513H16.6668V15.8334Z" fill="currentColor"></path>
                       <path d="M3.3335 10.2244H16.6668V8.94235H3.3335V10.2244Z" fill="currentColor"></path>
                     </svg>
                   </button>
                   <button type="button" className={viewMode === 'grid' ? 'active' : ''} style={{ color: viewMode === 'grid' ? '#fff' : '#0B131A' }} onClick={() => setViewMode('grid')} aria-label="Grid view">
                     <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path d="M3.91321 3.33337C3.59304 3.33337 3.3335 3.59292 3.3335 3.91308V7.97105C3.3335 8.29122 3.59304 8.55076 3.91321 8.55076H7.97117C8.29134 8.55076 8.55088 8.29122 8.55088 7.97105V3.91308C8.55088 3.59292 8.29134 3.33337 7.97117 3.33337H3.91321ZM4.49292 7.39134V4.49279H7.39146V7.39134H4.49292Z" fill="currentColor"></path>
                       <path d="M12.0291 3.33337C11.709 3.33337 11.4494 3.59292 11.4494 3.91308V7.97105C11.4494 8.29122 11.709 8.55076 12.0291 8.55076H16.0871C16.4073 8.55076 16.6668 8.29122 16.6668 7.97105V3.91308C16.6668 3.59292 16.4073 3.33337 16.0871 3.33337H12.0291ZM12.6089 7.39134V4.49279H15.5074V7.39134H12.6089Z" fill="currentColor"></path>
                       <path d="M11.4494 12.029C11.4494 11.7088 11.709 11.4493 12.0291 11.4493H16.0871C16.4073 11.4493 16.6668 11.7088 16.6668 12.029V16.0869C16.6668 16.4071 16.4073 16.6667 16.0871 16.6667H12.0291C11.709 16.6667 11.4494 16.4071 11.4494 16.0869V12.029ZM12.6089 12.6087V15.5072H15.5074V12.6087H12.6089Z" fill="currentColor"></path>
                       <path d="M3.91321 11.4493C3.59304 11.4493 3.3335 11.7088 3.3335 12.029V16.0869C3.3335 16.4071 3.59304 16.6667 3.91321 16.6667H7.97117C8.29134 16.6667 8.55088 16.4071 8.55088 16.0869V12.029C8.55088 11.7088 8.29134 11.4493 7.97117 11.4493H3.91321ZM4.49292 15.5072V12.6087H7.39146V15.5072H4.49292Z" fill="currentColor"></path>
                     </svg>
                   </button>
                 </div>
                <FilterDropdown
                  id="sort"
                  className="ls-sort-dropdown d-lg-block d-none"
                  menuWidth="match-button"
                  menuWidthMobile={280}
                  activeId={activeDropdownId}
                  toggle={setActiveDropdownId}
                  label={(() => {
                    const active = SORT_FIELDS.find(f => f.value === filter.ls_sortby);
                    return active ? `${active.label}: ${filter.ls_sort === 'DESC' ? 'High to Low' : 'Low to High'}` : 'Price: Low to High';
                  })()}
                >
                  {SORT_FIELDS.map((f) => {
                    const isActiveField = filter.ls_sortby === f.value;
                    return (
                      <div
                        key={f.value}
                        role="button"
                        tabIndex={0}
                        className={isActiveField ? 'active' : ''}
                        onClick={() => { toggleSorting(f.value); setActiveDropdownId(null); }}
                      >
                        {f.label}
                        {isActiveField && (
                          <i className={`fa-solid ${filter.ls_sort === 'DESC' ? 'fa-arrow-down-wide-short' : 'fa-arrow-up-short-wide'} ms-2`}></i>
                        )}
                      </div>
                    );
                  })}
                </FilterDropdown>
                <ResetFiltersButton show={hasFilterChanged} onClick={handleClearFilter} />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="container-fluid">
        <div className="ls-inv-layout">
          <FilterSidebar
            idSuffix="desktop"
            skuValue={skuInput}
            onSkuChange={(e) => setSkuInput(e.target.value)}
            onSkuBlur={() => {}}
            onSkuKeyDown={handleSkuKeyDown}
            onSkuSubmit={() => handleSkuCommit(true)}
            typeOptions={[
              { value: 'white', label: 'White Diamonds', href: inventoryType === "ringbuilder_c" ? `${SITE_URL}/lab-diamonds/?type=ringbuilder` : `${SITE_URL}/lab-diamonds/` },
              { value: 'fancy', label: 'Fancy Color Diamonds', href: inventoryType === "ringbuilder_c" ? `${SITE_URL}/lab-created-colored-diamonds/?type=ringbuilder_c` : `${SITE_URL}/lab-created-colored-diamonds/` },
            ]}
            activeType="fancy"
            certificateOptions={[
              { value: '1', label: 'IGI' },
              { value: '2', label: 'GIA' },
              { value: '3', label: 'GCAL' },
              { value: '0', label: 'Non Certified' },
              ...(inventoryType !== 'ringbuilder_c' ? [{ value: '4', label: 'Matching Pair' }] : []),
            ]}
            certificateSelected={filter.certificate_type}
            onCertificateChange={handleCertificateChange}
            methodOptions={[
              { value: 'hpht', label: 'HPHT', tooltip: 'HPHT: grown using High Pressure High Temperature technology.' },
              { value: 'cvd', label: 'CVD', tooltip: 'CVD: grown using Chemical Vapor Deposition technology.' },
            ]}
            methodSelected={filter.method}
            onMethodChange={handleMethodChange}
            mediaOptions={[
              { value: '360', label: 'Images OR 360° View' },
            ]}
            mediaSelected={filter.media}
            onMediaChange={handleMediaChange}
            tableInput={tableInput}
            onTableInputChange={setTableInput}
            onTableCommit={(values) => { clearSku(); setTableInput(values); handleTableChange(values); }}
            depthInput={depthInput}
            onDepthInputChange={setDepthInput}
            onDepthCommit={(values) => { clearSku(); setDepthInput(values); handleDepthChange(values); }}
            lwratioInput={lwratioInput}
            onLwratioInputChange={setLwratioInput}
            onLwratioCommit={(values) => { clearSku(); setLwratioInput(values); handleLwratioChange(values); }}
            advanced={{
              length: { value: filter.length_range, onInputChange: (v) => setAdvancedInput('length_range', v), onCommit: (v) => commitAdvanced('length_range', v) },
              width: { value: filter.width_range, onInputChange: (v) => setAdvancedInput('width_range', v), onCommit: (v) => commitAdvanced('width_range', v) },
              height: { value: filter.height_range, onInputChange: (v) => setAdvancedInput('height_range', v), onCommit: (v) => commitAdvanced('height_range', v) },
              fluorescence: { value: filter.fluorescence_range, onInputChange: (v) => setAdvancedInput('fluorescence_range', v), onCommit: (v) => commitAdvanced('fluorescence_range', v) },
              culet: { value: filter.culet_range, onInputChange: (v) => setAdvancedInput('culet_range', v), onCommit: (v) => commitAdvanced('culet_range', v) },
              polish: { value: filter.polish_range, onInputChange: (v) => setAdvancedInput('polish_range', v), onCommit: (v) => commitAdvanced('polish_range', v) },
              symmetry: { value: filter.symmetry_range, onInputChange: (v) => setAdvancedInput('symmetry_range', v), onCommit: (v) => commitAdvanced('symmetry_range', v) },
              crownAngle: { value: filter.crown_angle_range, onInputChange: (v) => setAdvancedInput('crown_angle_range', v), onCommit: (v) => commitAdvanced('crown_angle_range', v) },
              pavilionAngle: { value: filter.pavilion_angle_range, onInputChange: (v) => setAdvancedInput('pavilion_angle_range', v), onCommit: (v) => commitAdvanced('pavilion_angle_range', v) },
              girdle: { value: filter.girdle_range, onInputChange: (v) => setAdvancedInput('girdle_range', v), onCommit: (v) => commitAdvanced('girdle_range', v) },
            }}
          />

          <div className="ls-inv-main">
            <RingBuilderMatchNotice page="diamonds" />
        <div className="d-flex align-items-start gap-2">
          <button
            type="button"
            className="ls-mobile-filter-icon-btn d-lg-none"
            onClick={() => setFiltersModalOpen(true)}
            aria-label="Filter & Sort"
          >
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2.5 5H17.5M5.83333 10H14.1667M8.33333 15H11.6667" stroke="#0B131A" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
            {(() => {
              const activeCount = countActiveFilters(filter, resetdefaultFilter, { colorDefaultKey: 'color' });
              return activeCount > 0 ? `(${activeCount})` : '';
            })()}
          </button>
          <CaratQuickFilter
            minCarat={LD_THEME.inventoryRange.min_carat}
            maxCarat={LD_THEME.inventoryRange.max_carat}
            value={filter.carat_range}
            onSelect={(range) => {
              const [min, max] = range.map((v) => Number(v));
              clearSku();
              setCaratInput([min.toFixed(2), max.toFixed(2)]);
              setFilter(prev => ({ ...prev, carat_range: [min, max], ls_sku: '' }));
            }}
            onLargest={handleQuickLargest}
            largestActive={isLargestActive}
            onRemoveLargest={handleRemoveQuickLargest}
            onLowestPrice={handleQuickLowestPrice}
            lowestPriceActive={isLowestPriceActive}
            onRemoveLowestPrice={handleRemoveQuickLowestPrice}
            onHighestPrice={handleQuickHighestPrice}
            highestPriceActive={isHighestPriceActive}
            onRemoveHighestPrice={handleRemoveQuickHighestPrice}
            onTopQuality={handleQuickTopQuality}
            topQualityActive={isTopQualityActive}
            onRemoveTopQuality={handleRemoveQuickTopQuality}
            onIGI={handleQuickIGI}
            igiActive={isIGIActive}
            onRemoveIGI={handleRemoveQuickIGI}
            onGIA={handleQuickGIA}
            giaActive={isGIAActive}
            onRemoveGIA={handleRemoveQuickGIA}
            onClearAll={hasQuickPillActive ? handleClearQuickPills : undefined}
          />
        </div>

        <div className="ls-sidebar-search d-flex d-lg-none">
          <input
            type="text"
            className="form-control"
            placeholder="Search by SKU/GIA/IGI/GCAL number"
            value={skuInput}
            onChange={(e) => setSkuInput(e.target.value)}
            onBlur={() => {}}
            onKeyDown={handleSkuKeyDown}
          />
          <button type="button" className="ls-sidebar-search-submit" onClick={() => handleSkuCommit(true)}>Submit</button>
        </div>

        <InventoryResults2
          items={items}
          filter={filter}
          viewMode={viewMode}
          openDetail={openDetail}
          setOpenDetail={setOpenDetail}
          isLoading={isLoading}
          skuInput={skuInput}
          isLoadingCart={isLoadingCart}
          inventoryType={inventoryType}
          cartProductIds={inventoryType === 'ringbuilder_c' ? cartRingProductIds : cartProductIds} /* ring builder: only diamonds already set in a ring count as in-cart */
          isInCartRing={isInCartRing}
          cartError={cartError}
          widgetRef={widgetRef}
          toggleSorting={toggleSorting}
          isInWishlist={isInWishlist}
          toggleWishlist={toggleWishlist}
          handleAddToCartClick={handleAddToCartClick}
          handleClearFilter={handleClearFilter}
          COLOR_OPTIONS={COLOR_OPTIONS}
          intensityLabels={intensityLabels}
          ls_get_intensitydesc={ls_get_intensitydesc}
        />

        {!isLoading && hasMore && items.length > 0 && (
          <div className="text-center">
            <button
              className="ls_invtrldmr ls-button"
              onClick={() => setPage(prev => prev + 1)}
              disabled={isLoading}
            >
              LOAD MORE
            </button>
          </div>
        )}
          </div>
        </div>
      </div>

      <Modal isOpen={filtersModalOpen} onClose={() => setFiltersModalOpen(false)} fullscreen bodyClass="ls-filter-modal-open">
        <div className="inventory_filter_modal">
          <div className="position-relative d-flex flex-column w-100 bg-white">
            {/* Header */}
            <div className="ls-modal-header flex-shrink-0 p-3 border-bottom d-flex align-items-center justify-content-between">
              <h3 className="ls-modal-title fw-600 mb-0 text-start">
                Filters
              </h3>
              <button
                type="button"
                className="ls-modal-close-btn"
                onClick={() => setFiltersModalOpen(false)}
                aria-label="Close"
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            </div>

            <div className="ls-modal-body pt-0 min-vh-100 flex-grow-1 flex-shrink-1 flex-basis-auto px-3 sm:px-4">

              <div className="mb-3 mt-3">
                <div className="ls-sidebar-subfield-label mb-2">Sort by</div>
                <div className="d-flex align-items-center gap-2 flex-wrap">
                  <button type="button" className={`ls-sidebar-pill ${isTopQualityActive ? 'active' : ''}`} onClick={handleQuickTopQuality}>Top Quality</button>
                  <button type="button" className={`ls-sidebar-pill ${isLowestPriceActive ? 'active' : ''}`} onClick={handleQuickLowestPrice}>Price: Low to High</button>
                  <button type="button" className={`ls-sidebar-pill ${isHighestPriceActive ? 'active' : ''}`} onClick={handleQuickHighestPrice}>Price: High to Low</button>
                  <button type="button" className={`ls-sidebar-pill ${isLargestActive ? 'active' : ''}`} onClick={handleQuickLargest}>Carat: Biggest</button>
                </div>
              </div>
              <div className="ls-sidebar-section-divider"></div>

              <FilterSidebar
                idSuffix="mobile"
                className="ls-filter-sidebar-modal"
                showDividers
                shapeOptions={shapes}
                templateUrl={templateUrl}
                shapeSelected={filter.shape}
                onShapeChange={handleShapeChange}
                isRingBuilder={inventoryType === "ringbuilder_c"}
                priceInput={priceInput}
                onPriceInputChange={setPriceInput}
                onPriceCommit={handlePriceChange}
                priceMin={LD_THEME.inventoryRange.min_price}
                priceMax={LD_THEME.inventoryRange.max_price}
                caratInput={caratInput}
                onCaratInputChange={setCaratInput}
                onCaratCommit={handleCaratChange}
                caratMin={LD_THEME.inventoryRange.min_carat}
                caratMax={LD_THEME.inventoryRange.max_carat}
                cutValue={filter.cut}
                onCutChange={handleCutChange}
                colorSwatchOptions={COLOR_OPTIONS}
                colorSwatchValue={filter.color}
                onColorSwatchChange={handleColorChange}
                intensityValue={filter.intensity}
                onIntensityChange={handleIntensityChange}
                intensityLabels={intensityLabels}
                intensitySvgFill={svgFill}
                clarityValue={filter.clarity}
                onClarityChange={handleClarityChange}
                typeOptions={[
                  { value: 'white', label: 'White Diamonds', href: inventoryType === "ringbuilder_c" ? `${SITE_URL}/lab-diamonds/?type=ringbuilder` : `${SITE_URL}/lab-diamonds/` },
                  { value: 'fancy', label: 'Fancy Color Diamonds', href: inventoryType === "ringbuilder_c" ? `${SITE_URL}/lab-created-colored-diamonds/?type=ringbuilder_c` : `${SITE_URL}/lab-created-colored-diamonds/` },
                ]}
                activeType="fancy"
                certificateOptions={[
                  { value: '1', label: 'IGI' },
                  { value: '2', label: 'GIA' },
                  { value: '3', label: 'GCAL' },
                  { value: '0', label: 'Non Certified' },
                  ...(inventoryType !== 'ringbuilder_c' ? [{ value: '4', label: 'Matching Pair' }] : []),
                ]}
                certificateSelected={filter.certificate_type}
                onCertificateChange={handleCertificateChange}
                methodOptions={[
                  { value: 'hpht', label: 'HPHT', tooltip: 'HPHT: grown using High Pressure High Temperature technology.' },
                  { value: 'cvd', label: 'CVD', tooltip: 'CVD: grown using Chemical Vapor Deposition technology.' },
                ]}
                methodSelected={filter.method}
                onMethodChange={handleMethodChange}
                mediaOptions={[
                  { value: '360', label: 'Images OR 360° View' },
                ]}
                mediaSelected={filter.media}
                onMediaChange={handleMediaChange}
                tableInput={tableInput}
                onTableInputChange={setTableInput}
                onTableCommit={(values) => { clearSku(); setTableInput(values); handleTableChange(values); }}
                depthInput={depthInput}
                onDepthInputChange={setDepthInput}
                onDepthCommit={(values) => { clearSku(); setDepthInput(values); handleDepthChange(values); }}
                lwratioInput={lwratioInput}
                onLwratioInputChange={setLwratioInput}
                onLwratioCommit={(values) => { clearSku(); setLwratioInput(values); handleLwratioChange(values); }}
                advanced={{
                  length: { value: filter.length_range, onInputChange: (v) => setAdvancedInput('length_range', v), onCommit: (v) => commitAdvanced('length_range', v) },
                  width: { value: filter.width_range, onInputChange: (v) => setAdvancedInput('width_range', v), onCommit: (v) => commitAdvanced('width_range', v) },
                  height: { value: filter.height_range, onInputChange: (v) => setAdvancedInput('height_range', v), onCommit: (v) => commitAdvanced('height_range', v) },
                  fluorescence: { value: filter.fluorescence_range, onInputChange: (v) => setAdvancedInput('fluorescence_range', v), onCommit: (v) => commitAdvanced('fluorescence_range', v) },
                  culet: { value: filter.culet_range, onInputChange: (v) => setAdvancedInput('culet_range', v), onCommit: (v) => commitAdvanced('culet_range', v) },
                  polish: { value: filter.polish_range, onInputChange: (v) => setAdvancedInput('polish_range', v), onCommit: (v) => commitAdvanced('polish_range', v) },
                  symmetry: { value: filter.symmetry_range, onInputChange: (v) => setAdvancedInput('symmetry_range', v), onCommit: (v) => commitAdvanced('symmetry_range', v) },
                  crownAngle: { value: filter.crown_angle_range, onInputChange: (v) => setAdvancedInput('crown_angle_range', v), onCommit: (v) => commitAdvanced('crown_angle_range', v) },
                  pavilionAngle: { value: filter.pavilion_angle_range, onInputChange: (v) => setAdvancedInput('pavilion_angle_range', v), onCommit: (v) => commitAdvanced('pavilion_angle_range', v) },
                  girdle: { value: filter.girdle_range, onInputChange: (v) => setAdvancedInput('girdle_range', v), onCommit: (v) => commitAdvanced('girdle_range', v) },
                }}
              />
            </div>
            
            <div className="ls-modal-footer flex-shrink-0">
            <div className="d-flex align-items-center justify-content-between gap-3">
              {(hasFilterChanged) ? (
                <a href="#" onClick={handleClearFilter} className="ls-sidebar-pill text-nowrap fw-medium">
                  Clear all
                </a>
              ) : <span />}
              <button
                type="button"
                className="btn dark_btn flex-grow-1"
                onClick={() => {
                  setPage(1);
                  setItems([]);
                  setTotal(0);
                  setHasMore(true);
                  fetchInventory(1, true);
                  setFiltersModalOpen(false);
                }}
              >
                {isLoading ? 'Loading...' : `Show ${Number(total).toLocaleString()} diamonds`}
              </button>
              </div>
            </div>
          </div>

        </div>
      </Modal>
      <ConfirmAddModal
        isOpen={confirmModalOpen}
        onClose={() => { setConfirmModalOpen(false); setConfirmItem(null); }}
        item={confirmItem}
        onConfirm={async (it) => { if (it) await performAddToCart(it.pid, it.id, it.__addType); }}
      />

    </div>
  );
}