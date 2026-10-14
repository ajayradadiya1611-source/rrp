// ─────────────────────────────────────────────────────────────
// CaratQuickFilter.jsx
// The row of quick carat pills shown above the diamond grid/table
// (<1ct, 1ct+, 1.5ct+, 2ct+ ... 10ct+). Clicking a pill sets the
// carat range filter to [minCarat, breakpoint) for "<1ct" or
// [breakpoint, maxCarat] for "Nct+". Once a carat pill is active, the
// other carat pills are hidden and the active one shows a "×" remove
// mark; clicking it clears the carat filter and brings all the carat
// pills back.
//
// It also renders a set of one-tap quality/sort shortcut pills after
// the carat pills — Largest, Lowest price, Highest price, Top
// Quality, Best Color, Clarity, IGI Certified, GIA Certified, Clear
// all. Each shortcut pill is driven by an `xActive` flag + `onX`
// (apply) + `onRemoveX` (clear) trio passed in by the parent, and
// behaves the same way as the carat pill: active → highlighted with
// a "×" remove mark.
//
// "Clear all" only resets these quick pills (sort, carat range,
// cut/color/clarity, certificate) — it does not touch shape, price,
// or other sidebar filters; that scoping is the parent's
// responsibility via what it passes as `onClearAll`.
//
// This is a self-contained, reusable piece: the parent owns the
// actual filter state and just tells this component what's active
// and what to call when a pill (or its remove mark) is clicked.
//
// Usage:
//   <CaratQuickFilter
//     minCarat={LD_THEME.inventoryRange.min_carat}
//     maxCarat={LD_THEME.inventoryRange.max_carat}
//     value={filter.carat_range}
//     onSelect={(range) => { clearSku(); setCaratInput(range.map(v => v.toFixed(2))); handleCaratChange(range); }}
//     onLargest={...} largestActive={...} onRemoveLargest={...}
//     onLowestPrice={...} lowestPriceActive={...} onRemoveLowestPrice={...}
//     onHighestPrice={...} highestPriceActive={...} onRemoveHighestPrice={...}
//     onTopQuality={...} topQualityActive={...} onRemoveTopQuality={...}
//     onDEF={...} defActive={...} onRemoveDEF={...}         (label: "Best Color")
//     onFLVS2={...} flVs2Active={...} onRemoveFLVS2={...}   (label: "Clarity")
//     onIGI={...} igiActive={...} onRemoveIGI={...}
//     onGIA={...} giaActive={...} onRemoveGIA={...}
//     onClearAll={...}
//   />
// ─────────────────────────────────────────────────────────────

import React, { useRef } from 'react';
import { CARAT_PILL_BREAKPOINTS } from '../constants/caratPills';
import { templateUrl } from './inventoryHelpers';

// Enables click-and-drag horizontal scrolling on the pills strip
// (desktop mouse users), while leaving native touch scrolling on
// mobile untouched. A small drag threshold prevents accidental
// drags from swallowing genuine pill clicks.
function useDragScroll() {
  const ref = useRef(null);
  const state = useRef({ isDown: false, startX: 0, scrollLeft: 0, moved: false });

  const onMouseDown = (e) => {
    const el = ref.current;
    if (!el) return;
    state.current.isDown = true;
    state.current.moved = false;
    state.current.startX = e.pageX - el.offsetLeft;
    state.current.scrollLeft = el.scrollLeft;
  };

  const endDrag = () => {
    const el = ref.current;
    state.current.isDown = false;
    if (el) el.classList.remove('is-dragging');
  };

  const onMouseMove = (e) => {
    const el = ref.current;
    if (!el || !state.current.isDown) return;
    const x = e.pageX - el.offsetLeft;
    const walk = x - state.current.startX;
    if (Math.abs(walk) > 5) {
      state.current.moved = true;
      el.classList.add('is-dragging');
    }
    if (state.current.moved) {
      e.preventDefault();
      el.scrollLeft = state.current.scrollLeft - walk;
    }
  };

  // Swallow the click that immediately follows a drag so pills
  // don't fire onSelect when the user was just scrolling.
  const onClickCapture = (e) => {
    if (state.current.moved) {
      e.stopPropagation();
      e.preventDefault();
      state.current.moved = false;
    }
  };

  return {
    ref,
    onMouseDown,
    onMouseMove,
    onMouseUp: endDrag,
    onMouseLeave: endDrag,
    onClickCapture,
  };
}

function buildPills(minCarat, maxCarat) {
  const pills = [{ label: `<${CARAT_PILL_BREAKPOINTS[0]}ct`, range: [minCarat, CARAT_PILL_BREAKPOINTS[0]] }];
  CARAT_PILL_BREAKPOINTS.forEach((breakpoint) => {
    pills.push({ label: `${breakpoint}ct+`, range: [breakpoint, maxCarat] });
  });
  return pills;
}

function RemoveMark({ label, onRemove }) {
  if (!onRemove) return null;
  return (
    <span
      className="ls-carat-pill-remove"
      role="button"
      aria-label={`Remove ${label} filter`}
      onClick={(e) => {
        e.stopPropagation();
        onRemove();
      }}
    >
      {' '}
      <img
        src={`${templateUrl}/aj_custom/inventory/close-pills.svg`}
        alt=""
        className="ls-carat-pill-remove-icon"
      />
    </span>
  );
}

function ActionPill({ label, active, onClick, onRemove }) {
  if (!onClick) return null;
  return (
    <button
      type="button"
      className={`ls-carat-pill${active ? ' active' : ''}`}
      onClick={onClick}
    >
      {label}
      {active && <RemoveMark label={label} onRemove={onRemove} />}
    </button>
  );
}

export default function CaratQuickFilter({
  minCarat,
  maxCarat,
  value = [],
  onSelect,
  className = 'ls-carat-pills',
  onLargest,
  largestActive = false,
  onRemoveLargest,
  onLowestPrice,
  lowestPriceActive = false,
  onRemoveLowestPrice,
  onHighestPrice,
  highestPriceActive = false,
  onRemoveHighestPrice,
  onTopQuality,
  topQualityActive = false,
  onRemoveTopQuality,
  onDEF,
  defActive = false,
  onRemoveDEF,
  onFLVS2,
  flVs2Active = false,
  onRemoveFLVS2,
  onIGI,
  igiActive = false,
  onRemoveIGI,
  onGIA,
  giaActive = false,
  onRemoveGIA,
  onClearAll,
}) {
  const drag = useDragScroll();
  const pills = buildPills(minCarat, maxCarat);
  const [activeMin, activeMax] = value.map(Number);

  const decoratedPills = pills.map((p) => ({
    ...p,
    isActive:
      Math.abs(Number(p.range[0]) - activeMin) < 0.001 &&
      Math.abs(Number(p.range[1]) - activeMax) < 0.001,
  }));
  const activeCaratPill = decoratedPills.find((p) => p.isActive);
  const visibleCaratPills = activeCaratPill ? [activeCaratPill] : decoratedPills;

  return (
    <div
      className={className}
      ref={drag.ref}
      onMouseDown={drag.onMouseDown}
      onMouseMove={drag.onMouseMove}
      onMouseUp={drag.onMouseUp}
      onMouseLeave={drag.onMouseLeave}
      onClickCapture={drag.onClickCapture}
    >
      {visibleCaratPills.map(({ label, range, isActive }) => (
        <button
          key={label}
          type="button"
          className={`ls-carat-pill${isActive ? ' active' : ''}`}
          onClick={() => onSelect(range)}
        >
          {label}
          {isActive && (
            <RemoveMark label={label} onRemove={() => onSelect([minCarat, maxCarat])} />
          )}
        </button>
      ))}

      {onLargest && <ActionPill label="Largest" active={largestActive} onClick={onLargest} onRemove={onRemoveLargest} />}
      {onLowestPrice && <ActionPill label="Lowest price" active={lowestPriceActive} onClick={onLowestPrice} onRemove={onRemoveLowestPrice} />}
      {onHighestPrice && <ActionPill label="Highest price" active={highestPriceActive} onClick={onHighestPrice} onRemove={onRemoveHighestPrice} />}
      {onTopQuality && <ActionPill label="Top Quality" active={topQualityActive} onClick={onTopQuality} onRemove={onRemoveTopQuality} />}
      {onDEF && <ActionPill label="Best Color" active={defActive} onClick={onDEF} onRemove={onRemoveDEF} />}
      {onFLVS2 && <ActionPill label="Clarity" active={flVs2Active} onClick={onFLVS2} onRemove={onRemoveFLVS2} />}
      {onIGI && <ActionPill label="IGI Certified" active={igiActive} onClick={onIGI} onRemove={onRemoveIGI} />}
      {onGIA && <ActionPill label="GIA Certified" active={giaActive} onClick={onGIA} onRemove={onRemoveGIA} />}

      {onClearAll && (
        <button type="button" className="ls-carat-pill ls-carat-pill-clear aj-white-btn-color" onClick={onClearAll}>
          Clear all
        </button>
      )}
    </div>
  );
}
