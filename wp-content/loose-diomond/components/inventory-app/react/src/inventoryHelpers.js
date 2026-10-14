// ─────────────────────────────────────────────────────────────
// inventoryHelpers.js
// Shared, logic-preserving helpers extracted from App.jsx / App2.jsx
// (and reused by App3.jsx). Nothing here changes existing behavior:
// the two places where App.jsx and App2.jsx had DRIFTED are exposed
// as options, so each caller keeps its exact current output.
//
//   App.jsx  -> buildQueryString(cur, def)                       // defaults
//   App2.jsx -> buildQueryString(cur, def, {
//                 colorDefaultKey: 'color',
//                 heartarrowParam: 'hearts-and-arrows',
//                 debug: true,
//               })
// ─────────────────────────────────────────────────────────────

import * as React from 'react';
import { readWishlist, toggleWishlistIdWithType } from '../wishlist.js';

// Module-level constants (identical in App.jsx and App2.jsx)
export const templateUrl =
  (typeof window !== 'undefined' && window.themeData && window.themeData.templateUrl)
    ? window.themeData.templateUrl : '';

export const ajaxUrl =
  (typeof window !== 'undefined' && window.themeData && window.themeData.ajaxUrl)
    ? window.themeData.ajaxUrl : '';

export const globalRb =
  (typeof window !== 'undefined' && window.LS_RING_BUILDER) ? window.LS_RING_BUILDER : null;

// ─────────────────────────────────────────────────────────────
// useWishlist()
// Same wishlist read/toggle logic App.jsx has inline (keyFor +
// toggleWishlist + isInWishlist), pulled out so new components (e.g.
// SimilarDiamonds) can reuse it instead of re-implementing it.
// ─────────────────────────────────────────────────────────────
export function useWishlist() {
  const [wishlistIds, setWishlistIds] = React.useState([]);

  const keyFor = (id, variationId) => `${id}:${variationId === undefined || variationId === null ? '' : String(variationId)}`;

  React.useEffect(() => {
    if (typeof document === 'undefined') return;
    const items = readWishlist();
    setWishlistIds(Array.isArray(items) ? items.map(entry => keyFor(entry && entry.id ? entry.id : entry, entry && entry.variationId)) : []);
  }, []);

  const toggleWishlist = (id) => {
    const variationId = undefined;
    const meta = {};
    const params = (typeof window !== 'undefined' && window.location && window.location.search)
      ? new URLSearchParams(window.location.search)
      : new URLSearchParams();
    try {
      meta.query = params.toString();
      meta.currentUrl = (typeof window !== 'undefined' && window.location) ? window.location.href : '';
    } catch (e) {
      // ignore if window isn't available or URL parsing fails
    }
    const next = toggleWishlistIdWithType(id, 'rawdiomon', variationId, meta);
    setWishlistIds(Array.isArray(next) ? next.map(entry => keyFor(entry.id, entry.variationId)) : []);
  };

  const isInWishlist = (item) => {
    if (!wishlistIds || !Array.isArray(wishlistIds)) return false;
    const id = item?.id ?? item?.pid ?? '';
    const variationId = item?.variation_id ?? item?.vid ?? item?.variationId ?? undefined;
    if (!id) return false;
    return wishlistIds.includes(keyFor(id, variationId));
  };

  return { isInWishlist, toggleWishlist };
}

// Pure helper: was defined inline inside buildQueryString in every App file.
export const isDefaultValue = (value, defaultValue) => {
  if (Array.isArray(value) && Array.isArray(defaultValue)) {
    return JSON.stringify([...value].sort()) === JSON.stringify([...defaultValue].sort());
  }
  return value === defaultValue;
};

/**
 * Build the inventory query string from the current + default filters.
 * Faithful merge of App.jsx and App2.jsx. The only behavioral differences
 * between those two files are parameterized below.
 *
 * @param {object} cur  current filter state
 * @param {object} def  default filter state
 * @param {object} [options]
 * @param {string} [options.colorDefaultKey='color_range']  key on LD_THEME.filtersDefault
 *        used to compare the color filter. App.jsx used 'color_range', App2.jsx used 'color'.
 * @param {string} [options.heartarrowParam='heartarrow']  URL param name for hearts & arrows.
 *        App.jsx emitted 'heartarrow', App2.jsx emitted 'hearts-and-arrows'.
 * @param {boolean} [options.debug=false]  when true, logs viewsku (App2.jsx behavior).
 * @returns {string} e.g. "?shape=round,oval&price=1000.00,5000.00" or ""
 */
export const buildQueryString = (cur, def, options = {}) => {
  const {
    colorDefaultKey = 'color_range',
    heartarrowParam = 'heartarrow',
    debug = false,
  } = options;

  try {
    const params = new URLSearchParams();

    const add = (k, v) => {
      if (v === undefined || v === null) return;
      params.append(k, String(v));
    };

    const get = (k) => (cur && cur[k] !== undefined ? cur[k] : (def && def[k] !== undefined ? def[k] : undefined));

    const ringbuiding = get('type') || (typeof window !== 'undefined' ? new URLSearchParams(window.location.search).get('type') : undefined);
    if (ringbuiding) {
      add('type', ringbuiding);
    }

    const shape = get('shape');
    if (!isDefaultValue(shape, LD_THEME.filtersDefault.shape)) {
      if (Array.isArray(shape)) add('shape', shape.join(','));
      else if (shape) add('shape', shape);
    }

    const price_range = get('price_range');
    if (!isDefaultValue(price_range, LD_THEME.filtersDefault.price_range) && Array.isArray(price_range)) add('price', price_range.map(v => Number(v).toFixed(2)).join(','));

    const carat_range = get('carat_range');
    if (!isDefaultValue(carat_range, LD_THEME.filtersDefault.carat_range) && Array.isArray(carat_range)) add('carat', carat_range.map(v => Number(v).toFixed(2)).join(','));

    const certificate_type = get('certificate_type');
    if (!isDefaultValue(certificate_type, LD_THEME.filtersDefault.certificate_type)) {
      if (Array.isArray(certificate_type)) add('certificate', certificate_type.join(','));
      else if (certificate_type) add('certificate', certificate_type);
    }

    const method = get('method');
    if (!isDefaultValue(method, LD_THEME.filtersDefault.method)) {
      if (Array.isArray(method)) add('method', method.join(','));
      else if (method) add('method', method);
    }

    const media = get('media');
    if (!isDefaultValue(media, [])) {
      // Only one option (360° View) remains, and the backend filters on
      // a single is_media=1 flag — no need to also expose the specific
      // option value in the URL.
      add('is_media', 1);
    }

    const table_range = get('table_range');
    if (!isDefaultValue(table_range, LD_THEME.filtersDefault.table_range) && Array.isArray(table_range)) add('table', table_range.map(v => Number(v).toFixed(2)).join(','));

    const intensity = get('intensity');
    if (!isDefaultValue(intensity, LD_THEME.filtersDefault.intensity) && Array.isArray(intensity)) add('intensity', intensity.map(v => Number(v)).join(','));

    const shipping = get('shipping');
    if (!isDefaultValue(shipping, LD_THEME.filtersDefault.shipping)) add('shipping', shipping);

    const is_sale = get('is_sale');
    if (!isDefaultValue(is_sale, LD_THEME.filtersDefault.is_sale)) add('is_sale', is_sale);

    const cut = get('cut');
    if (!isDefaultValue(cut, LD_THEME.filtersDefault.cut_range) && Array.isArray(cut)) add('cut', cut.map(v => Number(v).toFixed(2)).join(','));

    // NOTE: App.jsx compared against filtersDefault.color_range, App2.jsx against
    // filtersDefault.color. Controlled by `colorDefaultKey` to preserve both.
    const color = get('color');
    if (!isDefaultValue(color, LD_THEME.filtersDefault[colorDefaultKey]) && Array.isArray(color)) add('color', color.map(v => Number(v).toFixed(2)).join(','));
    else if (!isDefaultValue(color, LD_THEME.filtersDefault[colorDefaultKey]) && color) add('color', color);

    const clarity = get('clarity');
    if (!isDefaultValue(clarity, LD_THEME.filtersDefault.clarity_range) && Array.isArray(clarity)) add('clarity', clarity.map(v => Number(v).toFixed(2)).join(','));

    const depth_range = get('depth_range');
    if (!isDefaultValue(depth_range, LD_THEME.filtersDefault.depth_range) && Array.isArray(depth_range)) add('depth', depth_range.map(v => Number(v).toFixed(2)).join(','));

    const lwratio_range = get('lwratio_range');
    if (!isDefaultValue(lwratio_range, LD_THEME.filtersDefault.lwratio_range) && Array.isArray(lwratio_range)) add('lwratio', lwratio_range.map(v => Number(v).toFixed(2)).join(','));

    // Advanced Filters (sidebar): Length / Width / Height / Fluorescence /
    // Polish / Symmetry / Crown Angle / Pavilion Angle / Girdle Thickness.
    const length_range = get('length_range');
    if (!isDefaultValue(length_range, LD_THEME.filtersDefault.length_range) && Array.isArray(length_range)) add('length', length_range.map(v => Number(v).toFixed(2)).join(','));

    const width_range = get('width_range');
    if (!isDefaultValue(width_range, LD_THEME.filtersDefault.width_range) && Array.isArray(width_range)) add('width', width_range.map(v => Number(v).toFixed(2)).join(','));

    const height_range = get('height_range');
    if (!isDefaultValue(height_range, LD_THEME.filtersDefault.height_range) && Array.isArray(height_range)) add('height', height_range.map(v => Number(v).toFixed(2)).join(','));

    const fluorescence_range = get('fluorescence_range');
    if (!isDefaultValue(fluorescence_range, LD_THEME.filtersDefault.fluorescence_range) && Array.isArray(fluorescence_range)) add('fluorescence', fluorescence_range.map(v => Number(v).toFixed(2)).join(','));

    const polish_range = get('polish_range');
    if (!isDefaultValue(polish_range, LD_THEME.filtersDefault.polish_range) && Array.isArray(polish_range)) add('polish', polish_range.map(v => Number(v).toFixed(2)).join(','));

    const symmetry_range = get('symmetry_range');
    if (!isDefaultValue(symmetry_range, LD_THEME.filtersDefault.symmetry_range) && Array.isArray(symmetry_range)) add('symmetry', symmetry_range.map(v => Number(v).toFixed(2)).join(','));

    const crown_angle_range = get('crown_angle_range');
    if (!isDefaultValue(crown_angle_range, LD_THEME.filtersDefault.crown_angle_range) && Array.isArray(crown_angle_range)) add('crown_angle', crown_angle_range.map(v => Number(v).toFixed(2)).join(','));

    const pavilion_angle_range = get('pavilion_angle_range');
    if (!isDefaultValue(pavilion_angle_range, LD_THEME.filtersDefault.pavilion_angle_range) && Array.isArray(pavilion_angle_range)) add('pavilion_angle', pavilion_angle_range.map(v => Number(v).toFixed(2)).join(','));

    const girdle_range = get('girdle_range');
    if (!isDefaultValue(girdle_range, LD_THEME.filtersDefault.girdle_range) && Array.isArray(girdle_range)) add('girdle', girdle_range.map(v => Number(v).toFixed(2)).join(','));

    // NOTE: App.jsx emitted param name 'heartarrow', App2.jsx 'hearts-and-arrows'.
    // Controlled by `heartarrowParam` to preserve both.
    const heartarrow = get('heartarrow');
    if (!isDefaultValue(heartarrow, LD_THEME.filtersDefault.heartarrow)) add(heartarrowParam, heartarrow);

    const ls_sku = get('ls_sku');
    if (!isDefaultValue(ls_sku, LD_THEME.filtersDefault.sku)) add('sku', ls_sku);

    const params_real = new URLSearchParams(window.location.search);
    if (debug) console.log('viewsku', params_real);
    if (params_real.has('viewsku')) {
      const viewsku = params_real.get('viewsku');
      if (debug) console.log('viewsku', viewsku, LD_THEME.filtersDefault.sku);
      add('viewsku', viewsku);
    }

    const ls_sortby = get('ls_sortby');
    if (!isDefaultValue(ls_sortby, LD_THEME.filtersDefault.ls_sortby)) add('ls_sortby', ls_sortby);

    const ls_sort = get('ls_sort');
    if (!isDefaultValue(ls_sort, LD_THEME.filtersDefault.ls_sort)) add('ls_sort', ls_sort);

    let qs = params.toString();
    qs = qs.replace(/%2C/gi, ',');
    return qs ? `?${qs}` : '';
  } catch (err) {
    console.error('buildQueryString error', err);
    return '';
  }
};
