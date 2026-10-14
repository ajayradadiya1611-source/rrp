// ─────────────────────────────────────────────────────────────
// FilterSlider.jsx
// ONE source of truth for the RangeSlider config used by the inventory
// filters. The desktop (Bootstrap grid) and mobile (accordion) layouts
// were rendering the SAME slider config twice — this centralizes it.
//
// What is shared here: the layout-independent slider settings
//   (className, min/max/step/range/pips/margin/behaviour/snap).
// What stays in each layout: the surrounding wrapper markup (grid vs
//   accordion) and the tooltip/label text, plus `start`, `end` and
//   `onChange` which are passed in as props (they come from state/refs
//   and, for the numeric sliders, differ per layout).
//
// Usage (identical in desktop AND mobile for grade sliders):
//   <FilterSlider field="cut"     start={filter.cut[0]}     end={filter.cut[1]}     onChange={handleCutChange} />
//   <FilterSlider field="color"   start={filter.color[0]}   end={filter.color[1]}   onChange={handleColorChange} />
//   <FilterSlider field="clarity" start={filter.clarity[0]} end={filter.clarity[1]} onChange={handleClarityChange} />
//
// Numeric sliders keep their existing onChange (it touches refs/state):
//   <FilterSlider field="price"   start={priceInput[0]} end={priceInput[1]} onChange={onPriceChange} />
//   <FilterSlider field="carat"   start={caratInput[0]} end={caratInput[1]} onChange={onCaratChange} />
//   <FilterSlider field="table"   start={tableInput[0]} end={tableInput[1]} onChange={onTableChange} />
//   <FilterSlider field="depth"   start={depthInput[0]} end={depthInput[1]} onChange={onDepthChange} />
//   <FilterSlider field="lwratio" start={lwratioInput[0]} end={lwratioInput[1]} onChange={onLwratioChange} />
// ─────────────────────────────────────────────────────────────

import React from 'react';
import RangeSlider from './RangeSlider';
import { cutLabels, colorLabels, clarityLabels } from '../lsFunction';

// Grade label maps for the new Advanced Filters sliders (Fluorescence, Polish,
// Symmetry, Girdle Thickness). Kept local to this file since they aren't
// used anywhere else yet.
export const fluorescenceLabels = ['None', 'Faint', 'Medium', 'Strong', 'Very Strong', 'All'];
export const polishSymmetryLabels = ['Good', 'Very Good', 'Excellent', 'All'];
export const girdleLabels = ['Ext. Thin', 'Very Thin', 'Thin', 'Medium', 'Sli. Thick', 'Thick', 'Very Thick', 'Ext. Thick', 'All'];
export const culetLabels = ['None', 'Very Small', 'Medium', 'Slightly Large', 'Large', 'All'];

const invRange = () =>
  (typeof window !== 'undefined' && window.LD_THEME && window.LD_THEME.inventoryRange)
    ? window.LD_THEME.inventoryRange
    : {};

// Layout-independent config per field. `range`/`pips` for price & carat read
// LD_THEME.inventoryRange at call time (it is a runtime global).
export function getSliderPreset(field) {
  const inv = invRange();

  switch (field) {
    // ── Grade sliders: static config, identical in mobile & desktop ──
    case 'cut':
      return {
        className: 'cut-slider-handles w-100',
        min: 0, max: 4, step: 1, margin: 1,
        pips: {
          mode: 'values', values: [0, 1, 2, 3, 4], density: -1,
          format: { to: (v) => cutLabels[Math.round(v)] || v },
        },
      };
    case 'color':
      return {
        className: 'color-slider-handles w-100',
        min: 0, max: 10, step: 1, snap: false, behaviour: 'drag-tap', margin: 1,
        pips: {
          mode: 'values', values: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], density: 10,
          format: { to: (v) => colorLabels[Math.round(v)] || v },
        },
      };
    case 'clarity':
      return {
        className: 'clarity-slider-handles w-100',
        min: 0, max: 11, step: 1, snap: false, behaviour: 'drag-tap', margin: 1,
        pips: {
          mode: 'values', values: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], density: 11,
          format: { to: (v) => clarityLabels[Math.round(v)] || v },
        },
      };

    // ── Numeric sliders: shared config, onChange supplied by caller ──
    case 'price':
      return {
        className: 'price-slider-handles w-100',
        step: 1, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
        range: { min: inv.min_price, '50%': 9500, max: inv.max_price },
      };
    case 'carat':
      return {
        className: 'carat-slider-handles w-100',
        step: 0.01, fireOn: ['slide', 'change'],
        range: { min: inv.min_carat, '50%': 6, max: inv.max_carat },
      };
    case 'table':
      return {
        className: 'table-slider-handles w-100',
        min: 50, max: 80, step: 0.01, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
      };
    case 'depth':
      return {
        className: 'depth-slider-handles w-100',
        min: 46, max: 78, step: 0.01, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
      };
    case 'lwratio':
      return {
        className: 'lwratio-slider-handles w-100',
        min: 1, max: 2.75, step: 0.01, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
      };

    // ── Advanced Filters: numeric mm/degree ranges ──
    case 'length':
      return {
        className: 'length-slider-handles w-100',
        min: 3, max: 20, step: 0.01, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
      };
    case 'width':
      return {
        className: 'width-slider-handles w-100',
        min: 3, max: 15, step: 0.01, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
      };
    case 'height':
      return {
        className: 'height-slider-handles w-100',
        min: 2, max: 12, step: 0.01, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
      };
    case 'crown_angle':
      return {
        className: 'crown-angle-slider-handles w-100',
        min: 23, max: 40, step: 0.1, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
      };
    case 'pavilion_angle':
      return {
        className: 'pavilion-angle-slider-handles w-100',
        min: 38, max: 43, step: 0.1, behaviour: 'drag-tap', fireOn: ['slide', 'change'],
      };

    // ── Advanced Filters: ordinal grade sliders (same pattern as cut/color/clarity) ──
    case 'fluorescence':
      return {
        className: 'fluorescence-slider-handles w-100',
        min: 0, max: 5, step: 1, margin: 1,
        pips: {
          mode: 'values', values: [0, 1, 2, 3, 4, 5], density: -1,
          format: { to: (v) => fluorescenceLabels[Math.round(v)] || v },
        },
      };
    case 'polish':
      return {
        className: 'polish-slider-handles w-100',
        min: 0, max: 3, step: 1, margin: 1,
        pips: {
          mode: 'values', values: [0, 1, 2, 3], density: -1,
          format: { to: (v) => polishSymmetryLabels[Math.round(v)] || v },
        },
      };
    case 'symmetry':
      return {
        className: 'symmetry-slider-handles w-100',
        min: 0, max: 3, step: 1, margin: 1,
        pips: {
          mode: 'values', values: [0, 1, 2, 3], density: -1,
          format: { to: (v) => polishSymmetryLabels[Math.round(v)] || v },
        },
      };
    case 'girdle':
      return {
        className: 'girdle-slider-handles w-100',
        min: 0, max: 8, step: 1, margin: 1,
        pips: {
          mode: 'values', values: [0, 1, 2, 3, 4, 5, 6, 7, 8], density: -1,
          format: { to: (v) => girdleLabels[Math.round(v)] || v },
        },
      };

    case 'culet':
      return {
        className: 'culet-slider-handles w-100',
        min: 0, max: 5, step: 1, margin: 1,
        pips: {
          mode: 'values', values: [0, 1, 2, 3, 4, 5], density: -1,
          format: { to: (v) => culetLabels[Math.round(v)] || v },
        },
      };

    default:
      return {};
  }
}

/**
 * Renders the shared RangeSlider for a given filter field.
 * @param {string}   field     one of: cut|color|clarity|price|carat|table|depth|lwratio
 * @param {number}   start     lower handle value (from filter state / *Input state)
 * @param {number}   end       upper handle value
 * @param {function} onChange  slider onChange handler (identical for grade fields,
 *                             layout-specific for numeric fields)
 * @param {string}  [className] optional override of the default className
 * plus any extra RangeSlider props via ...rest
 */
export default function FilterSlider({ field, start, end, onChange, className, ...rest }) {
  const preset = getSliderPreset(field);
  return (
    <RangeSlider
      {...preset}
      className={className || preset.className}
      start={start}
      end={end}
      onChange={onChange}
      {...rest}
    />
  );
}
