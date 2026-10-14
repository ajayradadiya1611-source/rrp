/**
 * Sub-shape / "Cut Style" configuration.
 *
 * This is the generalized version of the Round-only Heart & Arrow toggle.
 * Built directly from the shape/L-W-ratio sheet you shared. Each shape
 * that has named cut-styles (Pear Step, Square Emerald, Dutch Marquise,
 * Cushion Modified, etc.) gets an entry here; shapes not listed (Princess,
 * Asscher, Oval-plain, Heart, ...) simply won't show the section.
 *
 * Fields per option:
 *   - value:      what gets sent to the backend as the `subshape` filter
 *                 (only when useShape is true — see below).
 *   - label:       what's shown on the pill button.
 *   - lwratio:     optional [min, max] applied to the lwratio_range slider.
 *   - useShape:    whether this option should ALSO filter by the `shape`
 *                  column (subshape). Per your sheet, only options that
 *                  have their own distinctly-named/tagged shape value
 *                  (Square Emerald, Square Radiant, Square Cushion, plus
 *                  all the non-ratio named styles like Pear Step, Dutch
 *                  Marquise, Cushion Brilliant/Modified/Antique/Old Mine)
 *                  set this. Plain "Elongated (Low/Medium/High)" brackets
 *                  are lwratio-only — the shape column stays the base
 *                  shape (e.g. "emerald"), so useShape is false/omitted
 *                  and no `subshape` param is sent for those.
 *
 * NOTE ON BACKEND: this only wires the frontend (filter state, URL query
 * string, active-filter count). Your ls_inventory-filter endpoint reads
 * `subshape` and, when present, filters `shape = subshape` instead of the
 * base shape IN(...) clause; when absent it falls back to the base shape
 * filter + lwratio_range as usual. Heart & Arrow keeps using its own
 * dedicated `heartarrow` (tblfieldname: `ha`) boolean column, unchanged.
 */

export const CUT_STYLES = {
  pear: [
    { value: 'pear_step', label: 'Pear Step Cut', useShape: true },
  ],

  emerald: [
    { value: 'square_emerald', label: 'Square Emerald', lwratio: [1.0, 1.1], useShape: true },
    { value: 'elongated_emerald_low', label: 'Elongated (Low)', lwratio: [1.1, 1.25] },
    { value: 'elongated_emerald_medium', label: 'Elongated (Medium)', lwratio: [1.26, 1.5] },
    { value: 'elongated_emerald_high', label: 'Elongated (High)', lwratio: [1.5, 2.75] },
  ],

  marquise: [
    { value: 'dutch_marquise', label: 'Dutch Marquise', useShape: true },
  ],

  radiant: [
    { value: 'criss_cut_radiant', label: 'Criss Cut Radiant', useShape: true },
    { value: 'rectangular_radiant', label: 'Rectangular', useShape: true },
    { value: 'radiant_modified', label: 'Radiant Modified', useShape: true },
    { value: 'square_radiant', label: 'Square Radiant', lwratio: [1.0, 1.1], useShape: true },
    { value: 'elongated_radiant_low', label: 'Elongated (Low)', lwratio: [1.1, 1.25] },
    { value: 'elongated_radiant_medium', label: 'Elongated (Medium)', lwratio: [1.26, 1.5] },
    { value: 'elongated_radiant_high', label: 'Elongated (High)', lwratio: [1.5, 2.75] },
  ],
  asscher: [
    { value: 'criss_cut_asscher', label: 'Criss Cut Asscher', useShape: true },
  ],
  cushion: [
    { value: 'criss_cut_cushion', label: 'Criss Cut Cushion', useShape: true },
    { value: 'cushion_brilliant', label: 'Cushion Brilliant', useShape: true },
    { value: 'cushion_modified', label: 'Cushion Modified', useShape: true },
    { value: 'antique_cushion', label: 'Antique Cushion', useShape: true },
    { value: 'old_mine_cushion', label: 'Old Mine Cushion', useShape: true },
    { value: 'square_cushion', label: 'Square Cushion', lwratio: [1.0, 1.1], useShape: true },
    { value: 'elongated_cushion_low', label: 'Elongated (Low)', lwratio: [1.1, 1.25] },
    { value: 'elongated_cushion_medium', label: 'Elongated (Medium)', lwratio: [1.26, 1.5] },
    { value: 'elongated_cushion_high', label: 'Elongated (High)', lwratio: [1.5, 2.75] },
  ],

  oval: [
    { value: 'oval_step', label: 'Oval Step Cut', useShape: true },
  ],

  old_european: [
    { value: 'eurocut', label: 'Eurocut', useShape: true },
  ],

  trapezoid: [
    { value: 'trapez_step', label: 'Trapez Step Cut', useShape: true },
    { value: 'trapez_brilliant', label: 'Trapez Brilliant', useShape: true },
  ],

  criss_cut: [
    { value: 'criss_cut_radiant', label: 'Criss Cut Radiant', useShape: true },
    { value: 'criss_cut_cushion', label: 'Criss Cut Cushion', useShape: true },
    { value: 'criss_cut_asscher', label: 'Criss Cut Asscher', useShape: true },
  ],
};

/**
 * Human-readable label for a base shape value, used to disambiguate
 * options when more than one shape is selected at once (e.g. "Square
 * (Emerald)" vs "Square (Radiant)" both showing together).
 */
const SHAPE_LABELS = {
  pear: 'Pear',
  emerald: 'Emerald',
  marquise: 'Marquise',
  radiant: 'Radiant',
  cushion: 'Cushion',
  oval: 'Oval',
  old_european: 'Old European',
  trapezoid: 'Trapezoid',
  criss_cut: 'Criss Cut',
};

/**
 * Resolve the cut-style option list for the current filter state.
 *
 * Works for any number of selected shapes: it returns the union of
 * configured options across all currently-selected shapes that have an
 * entry in CUT_STYLES (shapes without one, like Princess or plain Oval,
 * just contribute nothing). When more than one shape's options end up
 * in the combined list, each option gets its base shape appended to the
 * label (e.g. "Square Emerald (Emerald)") so it stays clear which shape
 * a style belongs to, and `value` is namespaced with the shape so two
 * shapes' options never collide.
 */
export function getCutStyleOptions(shapeFilter) {
  if (!Array.isArray(shapeFilter) || shapeFilter.length === 0) return null;

  const relevantShapes = shapeFilter.filter((s) => CUT_STYLES[s]);
  if (relevantShapes.length === 0) return null;

  if (relevantShapes.length === 1) {
    return CUT_STYLES[relevantShapes[0]];
  }

  // Multiple shapes selected — merge their option lists, tagging each
  // with its shape so the combined pill group stays unambiguous.
  return relevantShapes.flatMap((shape) =>
    CUT_STYLES[shape].map((opt) => ({
      ...opt,
      value: `${shape}:${opt.value}`,
      label: `${opt.label} (${SHAPE_LABELS[shape] || shape})`,
      // The actual DB shape value to filter on is still opt.value —
      // keep it around so the caller can send the right thing.
      shapeValue: opt.value,
      baseShape: shape,
    }))
  );
}
