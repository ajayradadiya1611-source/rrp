export const WISHLIST_COOKIE = 'ld_wishlist';

const readCookie = (name) => {
  if (typeof document === 'undefined') return null;
  const match = document.cookie.split('; ').find(c => c.startsWith(name + '='));
  if (!match) return null;
  return decodeURIComponent(match.split('=')[1] || '');
};

const writeCookie = (name, value, days = 365) => {
  if (typeof document === 'undefined') return;
  try {
    const d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${encodeURIComponent(value)};expires=${d.toUTCString()};path=/`;
  } catch (e) {
    // ignore
  }
};

// Exposed so other modules (e.g. the grid/list view toggle) can persist
// simple string values in a cookie without duplicating this logic.
export const readCookieValue = readCookie;
export const writeCookieValue = writeCookie;

export function readWishlist() {
  try {
    const raw = readCookie(WISHLIST_COOKIE);
    if (!raw) return [];
    const parsed = JSON.parse(raw);
    // normalize to array of objects { id, type }
    if (!Array.isArray(parsed)) return [];
    return parsed.map(item => {
      if (item && typeof item === 'object' && 'id' in item) return item;
      // legacy numeric id
      return { id: item, type: undefined };
    });
  } catch (e) {
    return [];
  }
}

export function writeWishlist(ids) {
  try {
    // ensure we write array of objects
    const out = Array.isArray(ids) ? ids.map(i => (typeof i === 'object' ? i : { id: i })) : [];
    writeCookie(WISHLIST_COOKIE, JSON.stringify(out), 365);
  } catch (e) {
    // ignore
  }
}

export function toggleWishlistId(id) {
  // deprecated fallback: toggle without type
  const current = readWishlist();
  const existsIndex = current.findIndex(x => x.id === id || String(x.id) === String(id));
  let next;
  if (existsIndex > -1) {
    next = current.filter((_, i) => i !== existsIndex);
  } else {
    next = [...current, { id }];
  }
  writeWishlist(next);
  return next;
}

export function isInWishlist(id, variationId) {
  const items = readWishlist();
  console.log('Checking wishlist for id:', id, 'variationId:', variationId, 'current items:', items);
  return items.some(x => {
    const sameId = String(x.id) === String(id);
    if (variationId === undefined || variationId === null || variationId === '') {
      // if no variationId provided, match items without a variationId
      console.log('No variationId provided, checking for items without variationId. Item variationId:', x.variationId);
      console.log('is-true-or-false:', sameId && (x.variationId === undefined || x.variationId === null || x.variationId === ''));
      return sameId && (x.variationId === undefined || x.variationId === null || x.variationId === '');
    }
    console.log('is-true-or-false-var:', sameId && String(x.variationId) === String(variationId));
    return sameId && String(x.variationId) === String(variationId);
  });
}

export function toggleWishlistIdWithType(id, type, variationId, meta) {
  const current = readWishlist();
  const existsIndex = current.findIndex(x => {
    const sameId = String(x.id) === String(id);
    if (variationId === undefined || variationId === null || variationId === '') {
      return sameId && (x.variationId === undefined || x.variationId === null || x.variationId === '');
    }
    return sameId && String(x.variationId) === String(variationId);
  });
  let next;
  if (existsIndex > -1) {
    next = current.filter((_, i) => i !== existsIndex);
  } else {
    // include optional meta (keep as-is if provided)
    const item = { id, type, variationId };
    if (meta !== undefined) item.meta = meta;
    next = [...current, item];
  }
  writeWishlist(next);
  return next;
}