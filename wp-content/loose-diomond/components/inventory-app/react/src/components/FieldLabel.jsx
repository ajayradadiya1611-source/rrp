// ─────────────────────────────────────────────────────────────
// FieldLabel.jsx
// ONE shared component for a filter field's heading + question-mark
// tooltip. Price, Carat, Table, and Depth (desktop grid, mobile
// accordion, and the sidebar) were each rendering this same markup by
// hand, and previously each one wrapped the icon in the custom
// components/Tooltip.jsx (a hover-positioned React tooltip).
//
// That custom Tooltip has been dropped from this component. The icon
// now just carries plain Bootstrap tooltip attributes:
//
//   <i data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="...">
//
// This relies on Bootstrap's tooltip JS already being initialized on
// the page (the same convention already used by the shipping icons in
// InventoryTableView.jsx: data-bs-toggle="tooltip"). No extra JS is
// added here — if the parent theme re-scans for
// [data-bs-toggle="tooltip"] elements after each render (as it must
// already do for the shipping icons, which are also re-rendered on
// every filter change), these will pick up the same behavior for free.
//
// Usage:
//   import FieldLabel from './FieldLabel';
//   import { TOOLTIP_TEXT } from '../constants/tooltipTexts';
//
//   <FieldLabel label="Price" tooltip={TOOLTIP_TEXT.price} />
//   <FieldLabel label="Carat" tooltip={TOOLTIP_TEXT.carat} />
//   <FieldLabel label="Table (%)" tooltip={TOOLTIP_TEXT.table} />
//   <FieldLabel label="Depth (%)" tooltip={TOOLTIP_TEXT.depth} />
//
// Props:
//   label      - string, the field name shown to the user
//   tooltip    - string, the tooltip copy (pull from constants/tooltipTexts.js)
//   as         - 'p' (default) — set to 'span' when nested inside a
//                <span> (e.g. SidebarSection titles use a <span> title)
//   className  - extra classes for the outer wrapper (default matches
//                the desktop heading wrapper used across App.jsx)
// ─────────────────────────────────────────────────────────────

import React from 'react';

export default function FieldLabel({ label, tooltip, as: As = 'p', className = 'heading d-flex align-items-center' }) {
  return (
    <div className={className}>
      <As className="field-label">{label}{tooltip && (
        <i
          className="fa-solid fa-question question_icon ms-1"
          data-bs-toggle="tooltip"
          data-bs-placement="top"
          data-bs-original-title={tooltip}
        ></i>
      )}</As>
      
    </div>
  );
}
