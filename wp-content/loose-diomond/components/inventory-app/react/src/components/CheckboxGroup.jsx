// ─────────────────────────────────────────────────────────────
// CheckboxGroup.jsx
// Reusable multi-select checkbox list for sidebar filters — same role
// as PillGroup (in FilterSidebar.jsx) but rendered as checkboxes
// instead of pill buttons. Fully controlled: the parent owns
// `selected` and is told what changed via `onChange(value)`.
//
// Usage:
//   <CheckboxGroup
//     name="media"
//     options={[{ value: 'square', label: 'Square Images' }, { value: '360', label: '360° View' }]}
//     selected={filter.media}
//     onChange={handleMediaChange}
//   />
// ─────────────────────────────────────────────────────────────

import React from 'react';

export default function CheckboxGroup({ name, options = [], selected = [], onChange }) {
  return (
    <div className={`ls-sidebar-checkbox-group${name ? ` ls-sidebar-checkbox-group-${name}` : ''}`}>
      {options.map(({ value, label }) => {
        const id = `${name}-${value}`;
        const isChecked = selected.includes(value);
        return (
          <label key={value} htmlFor={id} className={`ls-sidebar-checkbox-option${isChecked ? ' active' : ''}`}>
            <input
              type="checkbox"
              id={id}
              name={name}
              value={value}
              checked={isChecked}
              onChange={() => onChange(value)}
            />
            <span className="ls-sidebar-checkbox-box" aria-hidden="true">
              <i className="fa-solid fa-check"></i>
            </span>
            <span className="ls-sidebar-checkbox-label">{label}</span>
          </label>
        );
      })}
    </div>
  );
}
