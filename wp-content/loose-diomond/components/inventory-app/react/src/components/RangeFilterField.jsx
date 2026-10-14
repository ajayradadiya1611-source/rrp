// ─────────────────────────────────────────────────────────────
// RangeFilterField.jsx
// ONE shared component for a "label + tooltip + min/max number inputs
// + range slider" filter field. Price and Carat were each duplicated
// twice in App.jsx (desktop grid layout + mobile accordion layout) —
// same markup, same clamping logic, same slider config, just
// different ids/bounds/step. This component is the single source of
// truth for that markup so both layouts (and any future numeric range
// filter, e.g. a future "Weight" or "Length" field) render and behave
// identically.
//
// Two input modes are supported:
//   - Uncontrolled (pass minRef/maxRef): the input keeps its own DOM
//     value and only calls back on blur/slide-end. This is what Price
//     uses today, so slider dragging doesn't force a React re-render
//     on every tick.
//   - Controlled (omit minRef/maxRef): the input value is driven by
//     the `value` prop. This is what Carat uses today.
//
// Usage — Price (uncontrolled, with refs + pips):
//   <RangeFilterField
//     idPrefix="price"
//     label="Price"
//     tooltip={TOOLTIP_TEXT.price}
//     className="ls-price-range-field"
//     min={LD_THEME.inventoryRange.min_price}
//     max={LD_THEME.inventoryRange.max_price}
//     step={1}
//     midValue={9500}
//     pips={{ values: [100, 9500, LD_THEME.inventoryRange.max_price], format: (v) => new Intl.NumberFormat('en-US').format(v) }}
//     value={priceInput}
//     minRef={minPriceRef}
//     maxRef={maxPriceRef}
//     onInputChange={setPriceInput}
//     onCommit={handlePriceChange}
//   />
//
// Usage — Carat (controlled, no pips):
//   <RangeFilterField
//     idPrefix="carat"
//     label="Carat"
//     tooltip={TOOLTIP_TEXT.carat}
//     className="ls-carat-range-field"
//     min={LD_THEME.inventoryRange.min_carat}
//     max={LD_THEME.inventoryRange.max_carat}
//     step={0.01}
//     midValue={6}
//     value={caratInput}
//     onInputChange={setCaratInput}
//     onCommit={(values) => handleCaratChange(values.map(parseFloat))}
//   />
// ─────────────────────────────────────────────────────────────

import React from 'react';
import FieldLabel from './FieldLabel';
import RangeSlider from './RangeSlider';

export default function RangeFilterField({
  idPrefix,
  label,
  tooltip,
  min,
  max,
  step = 0.01,
  midValue,
  pips,
  value,
  minRef,
  maxRef,
  onInputChange,
  onCommit,
  className = '',
}) {
  const uncontrolled = !!(minRef && maxRef);
  const minId = `min-${idPrefix}`;
  const maxId = `max-${idPrefix}`;

  const clamp = (v, fallback) => {
    let n = Number(v);
    if (isNaN(n)) n = fallback;
    return Math.min(Math.max(n, min), max);
  };

  return (
    <div className={`d-flex align-items-center mb_70${className ? ` ${className}` : ''}`}>
      <FieldLabel label={label} tooltip={tooltip} />
      <div className="slider-box w-100">
        <div className="input-box">
          <input
            {...(uncontrolled ? { ref: minRef, defaultValue: value[0] } : { value: value[0] })}
            id={minId}
            type="number"
            min={min}
            max={max}
            step={step}
            onChange={(e) => onInputChange([e.target.value, value[1]])}
            onBlur={(e) => {
              const minValue = Number(clamp(e.target.value, min).toFixed(2));
              const maxRawValue = uncontrolled ? (maxRef.current?.value ?? value[1]) : value[1];
              const maxValue = Number(maxRawValue);
              const finalMin = Math.min(minValue, maxValue);
              if (uncontrolled && minRef.current) minRef.current.value = finalMin;
              onInputChange([finalMin, maxValue]);
              onCommit([finalMin, maxValue]);
            }}
          />
          <input
            {...(uncontrolled ? { ref: maxRef, defaultValue: value[1] } : { value: value[1] })}
            id={maxId}
            type="number"
            min={min}
            max={max}
            step={step}
            onChange={(e) => onInputChange([value[0], e.target.value])}
            onBlur={(e) => {
              const maxValue = Number(clamp(e.target.value, max).toFixed(2));
              const minRawValue = uncontrolled ? (minRef.current?.value ?? value[0]) : value[0];
              const minValue = Number(minRawValue);
              const finalMax = Math.max(maxValue, minValue);
              if (uncontrolled && maxRef.current) maxRef.current.value = finalMax;
              onInputChange([minValue, finalMax]);
              onCommit([minValue, finalMax]);
            }}
          />
        </div>
        <RangeSlider
          className={`${idPrefix}-slider-handles w-100`}
          start={value[0]}
          end={value[1]}
          step={step}
          behaviour="drag-tap"
          fireOn={['slide', 'change']}
          range={{ min, '50%': midValue, max }}
          {...(pips
            ? {
                pips: {
                  mode: 'values',
                  values: pips.values,
                  density: -1,
                  format: { to: pips.format },
                },
              }
            : {})}
          onChange={(values, event) => {
            const roundedMin = step < 1 ? Number(parseFloat(values[0]).toFixed(2)) : Math.floor(values[0]);
            const roundedMax = step < 1 ? Number(parseFloat(values[1]).toFixed(2)) : Math.floor(values[1]);

            if (uncontrolled) {
              if (minRef.current) minRef.current.value = roundedMin;
              if (maxRef.current) maxRef.current.value = roundedMax;
            } else {
              const minEl = document.getElementById(minId);
              const maxEl = document.getElementById(maxId);
              if (minEl) minEl.value = roundedMin;
              if (maxEl) maxEl.value = roundedMax;
            }

            if (event === 'change') {
              onInputChange([roundedMin, roundedMax]);
              onCommit([roundedMin, roundedMax]);
            }
          }}
        />
      </div>
    </div>
  );
}