// ─────────────────────────────────────────────────────────────
// tooltipTexts.js
// ONE source of truth for the question-mark tooltip copy shown beside
// each filter label (Price, Carat, Table, Depth, etc). Previously this
// text was hardcoded as a string literal at every place a label was
// rendered (desktop grid, mobile accordion, sidebar) — which meant the
// same sentence was duplicated 3-4x per field and could drift out of
// sync. Now every usage pulls from here, so updating copy only needs
// a change in one place.
//
// Usage:
//   import { TOOLTIP_TEXT } from './tooltipTexts';
//   <FieldLabel label="Price" tooltip={TOOLTIP_TEXT.price} />
// ─────────────────────────────────────────────────────────────

export const TOOLTIP_TEXT = {
  price: 'The total cost of the diamond based on its carat weight, cut, color, and clarity.',
  carat: 'Carat is a measurement of a diamonds weight. More carats = more money.',
  table: "A diamond's table is its top surface, usually measuring 53–64% of its average diameter. Different cuts and shapes may have varying table sizes.",
  depth: "The pavilion is the lower portion of the diamond and typically accounts for 40–60% of its total weight.",
  shape: "A diamond's shape refers to the general silhouette of the stone when viewed face up, not to be confused with the cut.",
  cut: "A diamond's cut refers to how well it returns light; Excellent being ideal standards, Poor being the lowest possible standards.",
  color: 'Diamond Color measures the degree of colorlessness. Stones are graded on a scale that ranges from D (colorless) to Z (yellow).',
  fancyColor: "A diamond's color refers to the presence of any color in a diamond, with the most valuable diamonds being completely colorless.",
  clarity: 'Clarity refers to the number, size, and position of inclusions and blemishes. Diamonds are graded on a scale from Flawless (no inclusions) to I3 (obvious inclusions).',
  intensity: 'A light form of intensity that can be seen with the naked eye.',
  lwratio: 'Length divided by width. Higher ratio = slender diamond, lower ratio = broader diamond. Each shape has its ideal range',
  certificate: 'Filter by certificate type. Each certified diamond includes a unique grading report number for easy identification and verification.',
  method: 'Filter by Growth Type',
  media: 'Filter diamonds by available media, including square images or an interactive 360° view.',
  type: 'Filter by white diamonds or fancy-colored diamonds.',
  heartArrow: 'Special features such as Hearts & Arrows precision-cut diamonds.',
  cutStyle: "Named cut styles available for this shape (e.g. Pear Step, Square Emerald, Cushion Modified). Each shape has its own set of styles.",
  advanced: 'Filter diamonds using additional grading details like fluorescence, polish, symmetry, and more.',
  length: "Measures the diamond from end to end at its longest point.",
  width: "Measures the diamond across its widest point.",
  height: "Measures the diamond from the table to the culet (its total depth).",
  fluorescence: 'Shows how much a diamond glows under UV light. None to Faint is generally preferred.',
  polish: "Measures the smoothness of a diamond's surface. Better polish enhances brilliance and sparkle.",
  symmetry: "Measures how precisely a diamond's facets are aligned. Better symmetry improves light performance.",
  crownAngle: 'The angle between the table and bezel facets, affecting sparkle and fire.',
  pavilionAngle: 'The pavilion angle affects how efficiently light reflects inside the diamond.',
  girdle: "The girdle is the diamond's outer edge. Its thickness impacts durability and proportions.",
  culet: "The culet is the small facet at the diamond's pointed bottom tip. A smaller culet generally reflects light better.",
  intensity:"A light form of intensity that can be seen with the naked eye."
};

export default TOOLTIP_TEXT;
