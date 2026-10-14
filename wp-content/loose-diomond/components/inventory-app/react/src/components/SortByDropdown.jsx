// ─────────────────────────────────────────────────────────────
// SortByDropdown.jsx
// Compact "Sort by" dropdown shown beside the Reset Filters button.
// Fixed option order: Price - Carat - Color - Clarity. Clicking an
// option that isn't the active sort field selects it (ASC by
// default); clicking the already-active option flips ASC/DESC —
// same behavior as clicking a sortable table header.
//
// The parent owns the actual sort state; this component is just UI.
// It relies on the same `.dropdown` / click-outside-to-close wiring
// already used elsewhere (App.jsx's toggleDropdown/dropdownOpen).
// ─────────────────────────────────────────────────────────────

import React from 'react';

const SORT_FIELDS = [
  { value: 'price', label: 'Price' },
  { value: 'carat', label: 'Carat' },
  { value: 'color', label: 'Color' },
  { value: 'clarity', label: 'Clarity' },
];

export default function SortByDropdown({ ls_sortby, ls_sort, open, onToggle, onSelect }) {
  const activeField = SORT_FIELDS.find(f => f.value === ls_sortby);
  const label = activeField
    ? `${activeField.label}: ${ls_sort === 'DESC' ? 'High to Low' : 'Low to High'}`
    : 'Sort by';

  return (
    <div className={`dropdown ls-sort-dropdown${open ? ' active' : ''}`}>
      <button
        type="button"
        className="filter-btn"
        onClick={(e) => { e.stopPropagation(); onToggle(); }}
        aria-expanded={open}
      >
        {label}
        <i className={`fa-solid ${open ? 'fa-angle-up' : 'fa-angle-down'} ms-1`}></i>
      </button>

      {open && (
        <div className="filter-menu">
          <div className="filter_content">
            {SORT_FIELDS.map((f) => {
              const isActiveField = ls_sortby === f.value;
              return (
                <div
                  key={f.value}
                  role="button"
                  tabIndex={0}
                  className={isActiveField ? 'active' : ''}
                  onClick={() => onSelect(f.value)}
                >
                  {f.label}
                  {isActiveField && (
                    <i className={`fa-solid ${ls_sort === 'DESC' ? 'fa-arrow-down-wide-short' : 'fa-arrow-up-short-wide'} ms-2`}></i>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      )}
    </div>
  );
}
