export function lsFunction(cut, color, clarity) {
  const desc = { cut: '', color: '', clarity: '' };

  switch (cut) {
    case 'Good':
      desc.cut = 'Quality cut with good proportions to return most light that enters.';
      break;
    case 'Excellent':
      desc.cut = 'High-quality cut with near-perfect proportions that return nearly all light that enters.';
      break;
    case 'Very Good':
      desc.cut = 'High-quality cut with good proportions that return almost all light that enters.';
      break;
    case 'Ideal':
      desc.cut = 'Perfectly-proportioned to return all light that enters for maximum fire. Only 5% of mined diamonds meet this standard.';
      break;
    default:
      desc.cut = '';
  }

  switch (color) {
    case 'M':
      desc.color = "Have a definite yellow tint that's visible to the naked eye. Like K and L diamonds, M color diamonds offer fantastic value for money.";
      break;
    case 'L':
      desc.color = 'Color is most noticeable when viewed from the side, have a slightly yellow tone.';
      break;
    case 'K':
      desc.color = 'Color noticeable with the naked eye. There are several color grades that are lower than K.';
      break;
    case 'J':
      desc.color = 'Color noticeable when seen side-by-side with diamonds of better grades. Good value.';
      break;
    case 'I':
      desc.color = 'Slight color noticeable when seen side-by-side with diamonds of better grades. Great value.';
      break;
    case 'H':
      desc.color = 'Nearly colorless. Slight color may be noticeable when seen side-by-side with diamonds of better grades. Excellent value.';
      break;
    case 'G':
      desc.color = 'Nearly colorless. Gemologists will be able to detect some color, but appears colorless to non-experts. Excellent value.';
      break;
    case 'F':
      desc.color = 'Almost colorless, with slight color visible to expert gemologists. Still considered a very high quality grade.';
      break;
    case 'E':
      desc.color = 'Almost completely colorless. So close to perfectly colorless, only expert gemologists may be able to detect any color.';
      break;
    case 'D':
      desc.color = 'Completely colorless, displaying the most desirable icy white brilliance. Very rare in nature and the most expensive color.';
      break;
    default:
      desc.color = '';
  }

  switch (clarity) {
    case 'I2':
      desc.clarity = 'Obvious inclusions which can be easily seen using a 10x loupe (and even with the naked eyes).';
      break;
    case 'I1':
      desc.clarity = 'Included. Inclusions are very easy to see under 10x magnification and may be seen with the naked eye.';
      break;
    case 'SI3':
      desc.clarity = 'Refer to diamonds that cannot be graded SI2 but do not have as many visible flaws as the stones graded I.';
      break;
    case 'SI2':
      desc.clarity = 'Slightly included with more inclusions than SI1. Easily seen by an expert under 10x magnification but difficult to see with the naked eye.';
      break;
    case 'SI1':
      desc.clarity = 'Slightly included. Easily seen by an expert under 10x magnification but difficult to see with the naked eye.';
      break;
    case 'VS2':
      desc.clarity = 'Very slightly included. Easier for an expert to see inclusions under 10x magnification, and generally not visible to the naked eye; slightly more inclusions than VS1.';
      break;
    case 'VS1':
      desc.clarity = 'Very slightly included. Easier for an expert to see inclusions under 10x magnification, and generally not visible to the naked eye.';
      break;
    case 'VVS2':
      desc.clarity = 'Very, very slightly included. Extremely small inclusions are difficult for an expert to see under 10x magnification and invisible to the naked eye; slightly more inclusions than VVS1.';
      break;
    case 'VVS1':
      desc.clarity = 'Very, very slightly included. Extremely small inclusions are difficult for an expert to see under 10x magnification and invisible to the naked eye.';
      break;
    case 'IF':
      desc.clarity = 'Internally flawless. The highest clarity grade, with no flaws or inclusions. Very rare and expensive.';
      break;
    case 'FL':
      desc.clarity = 'Flawless Diamonds. The highest grade of diamond clarity, with no visible inclusions or blemishes. Very rare and expensive.';
      break;
    default:
      desc.clarity = '';
  }

  return desc;
}

export default lsFunction;

export const cutLabels = ["Good", "Very Good", "Excellent", "Ideal", "All"];

export const colorLabels = [
  "M",
  "L",
  "K",
  "J",
  "I",
  "H",
  "G",
  "F",
  "E",
  "D",
  "All"
];

export const clarityLabels = [
  "I2",
  "I1",
  "SI3",
  "SI2",
  "SI1",
  "VS2",
  "VS1",
  "VVS2",
  "VVS1",
  "IF",
  "FL",
  "All"
];

export function updateSkuQuery(sku) {
  if (typeof window === 'undefined') return;
  try {
    const url = new URL(window.location.href);

    if (sku) {
      url.searchParams.set('viewsku', sku);
    } else {
      url.searchParams.delete('viewsku');
    }

    let finalUrl = url.toString().replace(/%2C/gi, ',');
    window.history.replaceState({}, '', finalUrl);
  } catch (err) {
    // ignore
  }
}

export function openDetailForItem(itemOrId, items, setOpenDetail) {
  if (!setOpenDetail) return;
  if (!itemOrId) {
    setOpenDetail(null);
    updateSkuQuery(null);
    return;
  }
  if (typeof itemOrId === 'object') {
    setOpenDetail(itemOrId.id);
    updateSkuQuery(itemOrId.sku || '');
    return;
  }
  const id = itemOrId;
  const found = Array.isArray(items) ? items.find(it => it.id === id) : null;
  setOpenDetail(id);
  updateSkuQuery(found && found.sku ? found.sku : '');
}

// Shapes eligible for "Add To Setting" — mirrors $first_10_shapes in the
// single diamond detail PHP template (single-diomond-page.php).
export const SETTING_ELIGIBLE_SHAPES = [
  'round',
  'princess',
  'cushion',
  'oval',
  'emerald',
  'pear',
  'asscher',
  'radiant',
  'marquise',
  'heart',
  'cushion_brilliant',
  'cushion_modified',
  'antique_cushion',
  'old_mine_cushion',
  'square_cushion',
  'oval_step',
  'square_emerald',
  'pear_step',
  'rectangular_radiant',
  'radiant_modified',
  'square_radiant',
  'dutch_marquise',
  // Named fancy cuts that use a standard base-shape setting
  'criss_cut_radiant',
  'criss_cut_cushion',
  'criss_cut_asscher',
  'portuguese',  // -> Round
  'square',      // -> Princess
  'carre',       // -> Princess
  'flanders',    // -> Cushion
  'ashoka',      // -> Cushion
  'capsule',     // -> Cushion
  'moval',       // -> Oval
  'crisscut',    // -> Radiant
  'criss_cut'    // -> Radiant
];

// "Carré" -> "carre", "Criss-Cut" -> "criss_cut", "Portuguese Cut" -> "portuguese"
export function normalizeShapeKey(value) {
  let key = (value || '')
    .toString()
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
    .replace(/[\s-]+/g, '_')
    .replace(/^_+|_+$/g, '');
  if (key.endsWith('_cut') && !SETTING_ELIGIBLE_SHAPES.includes(key)) {
    key = key.slice(0, -4);
  }
  return key;
}

// Ring builder: a setting of the base shape also accepts these named cuts,
// so they are pre-selected together in the diamond list shape filter.
// Values must match the `value` in App.jsx `shapes` (= ls_inventory.shape).
export const RING_SHAPE_GROUPS = {
  round:    ['round', 'portuguese'],
  princess: ['princess', 'square', 'carre'],
  cushion:  ['cushion', 'flanders', 'ashoka', 'capsule'],
  oval:     ['oval', 'moval'],
  radiant:  ['radiant', 'criss_cut'],
};

// Any shape in a group -> the whole group; otherwise just [shape].
export function getRingShapeGroup(shape) {
  if (!shape) return [];
  const key = normalizeShapeKey(shape);
  for (const base of Object.keys(RING_SHAPE_GROUPS)) {
    if (RING_SHAPE_GROUPS[base].includes(key)) return [...RING_SHAPE_GROUPS[base]];
  }
  return [key];
}

export function isSettingEligible(item) {
  if (!item) return false;
  const shapeKey = normalizeShapeKey(item.shape || item.shape_title || '');
  return SETTING_ELIGIBLE_SHAPES.includes(shapeKey);
}

// Shapes that offer "Ask About a Custom Setting" instead of "Add To Ring" —
// mirrors $custom_setting_shapes in the single diamond detail PHP template
// (diamond-detail.php).
export const CUSTOM_SETTING_SHAPES = [
  'rose_cut',
  'old_european',
  'hexagon',
  'kite',
  'old_mine',
  'triangular',
];

export function isCustomSettingShape(item) {
  if (!item) return false;
  const shapeKey = normalizeShapeKey(item.shape || item.shape_title || '');
  return !SETTING_ELIGIBLE_SHAPES.includes(shapeKey);
}

// Mirrors $custom_setting_msg in diamond-detail.php — builds the Zoho chat
// opener message for a given diamond item.
export function buildCustomSettingMsg(item) {
  if (!item) return '';
  const shapeName = (item.shape_title || item.shape || '').toString().trim();
  const carat = item.carat ? `${item.carat}ct ` : '';
  const name = `${carat}${shapeName}`.trim();
  return `Hi! I'm interested in this diamond — ${name} Lab Grown Diamond (${item.sku || ''}). I'd like to discuss a custom setting for it. Can someone help me with that?`;
}

export async function handleAddToCartClick({ pid, did, items, inventoryType, type, setConfirmItem, setConfirmModalOpen, performAddToCart }) {
  const item = items && items.find(i => String(i.id) === String(did));
  const resolvedType = type || inventoryType;
  console.log("Checking if item is non-certified for ring builder: inventoryType", { item, inventoryType, resolvedType });
  if (item) {
    const isNonCertified = (item.certificate_type == 0);
    console.log("Checking if item is non-certified for ring builder:", { item, isNonCertified });
    if (isNonCertified) {
      if (typeof setConfirmItem === 'function') setConfirmItem({ ...item, __addType: resolvedType });
      if (typeof setConfirmModalOpen === 'function') setConfirmModalOpen(true);
      return;
    }
  }

  if (typeof performAddToCart === 'function') {
    await performAddToCart(pid, did, resolvedType);
  }
}