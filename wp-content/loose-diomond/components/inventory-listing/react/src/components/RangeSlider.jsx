import React, { useEffect, useRef } from 'react';
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

export default function RangeSlider({
  className = '',
  start = 20,
  end = 80,
  min = 0,
  max = 100,
  step = 1,
  behaviour = 'tap-drag',
  snap = false,
  connect = true,
  range = null,
  pips = null,
  margin = 0,
  onChange = null,
  fireOn = 'change',
  debounce = 0,
}) {
  const ref = useRef(null);
  const debounceRef = useRef(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    if (el.noUiSlider) return;

    const opts = {
      start: [start, end],
      step,
      connect,
     // behaviour,
      range: range ? range : { min, max },
    };
    if (margin) opts.margin = margin;
    if (snap) opts.snap = snap;
    if (pips) opts.pips = pips;

    noUiSlider.create(el, opts);

    try {
      el.dataset.min = opts.start[0];
      el.dataset.max = opts.start[1];
    } catch (e) {}

    // ── Click-to-set: move nearest handle to clicked position ───────────────
    const handleClick = (e) => {
      const slider = el.noUiSlider;
      if (!slider) return;
      if (e.target.closest('.noUi-handle')) return;

      const rect = el.getBoundingClientRect();
      const isVertical = el.classList.contains('noUi-vertical');

      let ratio = isVertical
        ? (e.clientY - rect.top) / rect.height
        : (e.clientX - rect.left) / rect.width;

      ratio = Math.max(0, Math.min(1, ratio));

      const resolvedRange = range ? range : { min, max };
      const clickedValue = resolvedRange.min + ratio * (resolvedRange.max - resolvedRange.min);
      const snapped = Math.round(clickedValue / step) * step;
      const clamped = Math.max(resolvedRange.min, Math.min(resolvedRange.max, snapped));

      const [v0, v1] = slider.get(true);
      const handleIndex = Math.abs(clamped - v0) <= Math.abs(clamped - v1) ? 0 : 1;
      slider.setHandle(handleIndex, clamped);
    };

    el.addEventListener('click', handleClick);

    // ── onChange wiring ─────────────────────────────────────────────────────
    if (typeof onChange === 'function') {
      const updateDataset = (values) => {
        try {
          el.dataset.min = values[0];
          el.dataset.max = values[1];
        } catch (e) {}
      };

      const events = Array.isArray(fireOn) ? fireOn : [fireOn];
      const makeHandler = (evt) => (values) => {
        updateDataset(values);
        if (debounce > 0) {
          if (debounceRef.current) clearTimeout(debounceRef.current);
          debounceRef.current = setTimeout(() => {
            onChange(values, evt);
            debounceRef.current = null;
          }, debounce);
        } else {
          onChange(values, evt);
        }
      };

      el.noUiSlider.on('update', updateDataset);
      events.forEach((ev) => {
        if (['update', 'slide', 'change'].includes(ev)) {
          el.noUiSlider.on(ev, makeHandler(ev));
        }
      });
    }

    return () => {
      try {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        el.removeEventListener('click', handleClick);
        if (el.noUiSlider) el.noUiSlider.destroy();
      } catch (e) {}
    };
  }, [start, end, min, max, step, behaviour, snap, connect, pips, onChange, fireOn, debounce]);

  return <div ref={ref} className={className}></div>;
}