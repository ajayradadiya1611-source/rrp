import React, { useEffect, useState, useRef, useCallback } from 'react';

export default function FilterDropdown({
  id,
  title,
  label,
  activeId,
  toggle,
  className = '',
  children,
  terms = [],
  attribute,
  selectedValues = [],
  disabledFilter = false,
  isSortDropdown = false,
  // Explicit menu width control. Accepts a number (px), any CSS width
  // string (e.g. '90vw'), or the special value 'match-button' to size
  // the menu to exactly match the toggle button's rendered width
  // (useful since the menu is position:fixed, so CSS '100%' would
  // resolve against the viewport, not the button). `menuWidth` applies
  // to desktop; `menuWidthMobile` optionally overrides it below
  // `mobileBreakpoint`. If neither is passed, falls back to the
  // previous class-name-based defaults (price-dropdown → 250,
  // sort-dropdown → match-button, else 200) so old usages still work.
  menuWidth,
  menuWidthMobile,
  mobileBreakpoint = 768,
}) {
  const [selected, setSelected] = useState(selectedValues);
  const [menuStyle, setMenuStyle] = useState({});
  const [sortLabel, setSortLabel] = useState(title);
  const isActive = activeId === id;
  const btnRef = useRef(null);
  const parentRef = useRef(null);

  // Sync external selectedValues
  useEffect(() => {
    setSelected(selectedValues || []);
  }, [selectedValues]);

  // Compute and apply fixed menu position whenever dropdown opens
  useEffect(() => {
    if (!isActive || !btnRef.current || !parentRef.current) return;

    const rect = btnRef.current.getBoundingClientRect();
    const isSortClass = parentRef.current.classList.contains('sort-dropdown');
    const isPriceClass = parentRef.current.classList.contains('price-dropdown');
    const isMobile = typeof window !== 'undefined' && window.innerWidth < mobileBreakpoint;

    let width;
    if (isMobile && menuWidthMobile != null) {
      width = menuWidthMobile === 'match-button' ? rect.width : menuWidthMobile;
    } else if (menuWidth != null) {
      width = menuWidth === 'match-button' ? rect.width : menuWidth;
    } else {
      width = isPriceClass ? 250 : isSortClass ? rect.width : 200;
    }

    const viewportWidth = typeof window !== 'undefined' ? window.innerWidth : 0;
    const edgeMargin = 12; // keep a small gap from the screen edge

    // Clamp numeric widths so the menu never exceeds the viewport (with margins)
    if (typeof width === 'number' && viewportWidth) {
      width = Math.min(width, viewportWidth - edgeMargin * 2);
    }

    // Shift left so the menu's right edge stays on-screen, but never go
    // past the left edge either
    let left = rect.left;
    if (typeof width === 'number' && viewportWidth) {
      const maxLeft = viewportWidth - width - edgeMargin;
      left = Math.min(left, maxLeft);
      left = Math.max(left, edgeMargin);
    }

    setMenuStyle({
      position: 'fixed',
      top: rect.bottom + 8,
      left,
      zIndex: 9,
      width,
    });
  }, [isActive, menuWidth, menuWidthMobile, mobileBreakpoint]);

  // Close on outside click, scroll, and resize
  const closeDropdown = useCallback(() => {
    if (isActive) toggle(null);
  }, [isActive, toggle]);

  useEffect(() => {
    document.addEventListener('click', closeDropdown);
    window.addEventListener('scroll', closeDropdown);
    window.addEventListener('resize', closeDropdown);

    return () => {
      document.removeEventListener('click', closeDropdown);
      window.removeEventListener('scroll', closeDropdown);
      window.removeEventListener('resize', closeDropdown);
    };
  }, [closeDropdown]);

  const handleBtnClick = (e) => {
    e.stopPropagation(); // prevent document click from immediately closing
    toggle(isActive ? null : id);
  };

  const onCheckboxChange = (slug, checked) => {
    setSelected((prev) => {
      const next = checked ? [...prev, slug] : prev.filter((s) => s !== slug);
      document.dispatchEvent(
        new CustomEvent('filter:change', { detail: { id, attribute, values: next } })
      );
      return next;
    });
    toggle(null);
  };

  const onSortSelect = (e) => {
    setSortLabel(e.target.textContent.trim());
    toggle(null);
  };

  return (
    <div
      ref={parentRef}
      className={`filter-dropdown ${className}${isActive ? ' active' : ''}`}
    >
      <button ref={btnRef} type="button" className="filter-btn" onClick={handleBtnClick}>
        {label != null ? label : isSortDropdown ? sortLabel : title}{' '}
        <i className={`fa-solid ${isActive ? 'fa-angle-up' : 'fa-angle-down'}`} />
      </button>

      {isActive && (
        <div className="filter-menu" style={menuStyle}>
          <div className="filter_content">
            {children ? (
              // Only auto-scrape textContent for the label when the parent
              // hasn't supplied a controlled `label` — lets children with
              // icons/markup (e.g. an active-direction indicator) opt out.
              isSortDropdown && label == null ? (
                <div onClick={onSortSelect}>{children}</div>
              ) : (
                children
              )
            ) : attribute ? (
              terms.length === 0 ? (
                <div className="px-2 py-2">No options</div>
              ) : (
                terms.map((t) => (
  <label
    className="checkbox"
    key={t.term_id}
    onClick={(e) => e.stopPropagation()}
  >
    <input
      type="checkbox"
      value={t.slug}
      checked={selected.includes(t.slug)}
      onChange={(e) => onCheckboxChange(t.slug, e.target.checked)}
      disabled={disabledFilter}
    />{' '}
    <span>{t.name}</span>
  </label>
))
              )
            ) : null}
          </div>
        </div>
      )}
    </div>
  );
}