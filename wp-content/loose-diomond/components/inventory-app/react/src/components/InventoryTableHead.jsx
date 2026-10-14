// ─────────────────────────────────────────────────────────────
// InventoryTableHead.jsx
// The <thead> markup (sortable column headers with the darrow/
// crnt_sort classes) was identical between InventoryResults.jsx
// (App.jsx — Shape/Carat/Certificate/Cut/Color/Clarity/Price) and
// InventoryResults2.jsx (App2.jsx — same but Intensity instead of
// Color). Column list differs, so it's passed in as `columns`;
// everything else (sort-state classes, click wiring) lives here once.
//
// columns: [{ key, label, sortField?, className? }]
//   - sortField present  -> sortable <th> (darrow/crnt_sort + onClick)
//   - sortField omitted  -> plain <th> (e.g. "Certificate")
//   - label can be a string or JSX (e.g. Price's "(US$)" unit span)
// A trailing blank <th></th> (for the action column) is always added.
// ─────────────────────────────────────────────────────────────
import React from 'react';

export default function InventoryTableHead({ filter, toggleSorting, columns }) {
  return (
    <thead>
      <tr>
        {columns.map((col) => (
          col.sortField ? (
            <th
              key={col.key}
              className={`darrow ${filter.ls_sortby === col.sortField ? `crnt_sort ${filter.ls_sort === "DESC" ? "desc" : "asc"}` : ""}${col.className ? ` ${col.className}` : ''}`}
              onClick={() => toggleSorting(col.sortField)}
            >
              {col.label}
            </th>
          ) : (
            <th key={col.key} className={col.className || ''}>{col.label}</th>
          )
        ))}
        <th></th>
      </tr>
    </thead>
  );
}
