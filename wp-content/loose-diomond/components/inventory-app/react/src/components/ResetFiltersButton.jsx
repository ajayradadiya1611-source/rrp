// ─────────────────────────────────────────────────────────────
// ResetFiltersButton.jsx
// The small "↻ Reset Filters" link shown beside the view-toggle /
// filter-dropdown row once any filter has been changed. Pulled out
// into its own component so App.jsx, App2.jsx, and App3.jsx all use
// the exact same markup/classes (fil_small_btn text-nowrap fw-medium
// d-lg-block d-none) instead of drifting out of sync — it renders
// nothing when `show` is false, so it's safe to drop in unconditionally.
// ─────────────────────────────────────────────────────────────

import React from 'react';

export default function ResetFiltersButton({ show, onClick, className = '' }) {
  if (!show) return null;

  return (
    <a
      href="#"
      onClick={(e) => { e.preventDefault(); onClick(e); }}
      className={`fil_small_btn text-nowrap fw-medium d-lg-block d-none${className ? ` ${className}` : ''}`}
    >
      <i className="fa-solid fa-arrow-rotate-right me-2"></i>
      Reset Filters
    </a>
  );
}
