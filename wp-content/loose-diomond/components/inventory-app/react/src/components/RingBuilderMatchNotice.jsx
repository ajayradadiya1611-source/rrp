// ─────────────────────────────────────────────────────────────
// RingBuilderMatchNotice.jsx
// Shows "Only displaying results that match your diamond/setting"
// with a Remove link for every ring-builder item that is selected
// (window.LS_RING_BUILDER) — including when BOTH are selected.
// Remove calls the existing ls_rb_clear admin-ajax action.
// ─────────────────────────────────────────────────────────────
import React, { useEffect, useState } from 'react';

const readRb = () => (typeof window !== 'undefined' && window.LS_RING_BUILDER) || null;

// is_selected can arrive as true / 1 / "1" / "yes" from wp_localize_script
// (which stringifies scalars). Fall back to real item data when the flag is
// missing/false but an item clearly exists in the session.
const isOn = (v) => {
  if (v === true || v === 1) return true;
  if (typeof v === 'string') return ['1', 'true', 'yes'].includes(v.trim().toLowerCase());
  return false;
};
const hasItem = (obj) => {
  if (!obj || typeof obj !== 'object') return false;
  if (isOn(obj.is_selected)) return true;
  return !!(obj.product_id || obj.d_pid || obj.pid || obj.id || obj.sku || obj.title);
};

export default function RingBuilderMatchNotice({ page = '', afterRemove = 'reload' }) {
  const [rb, setRb] = useState(readRb);
  const [busy, setBusy] = useState(null);

  // LS_RING_BUILDER may be localized after this bundle mounts — re-read once ready.
  useEffect(() => {
    if (rb) return;
    const t = setTimeout(() => setRb(readRb()), 0);
    const onLoad = () => setRb(readRb());
    window.addEventListener('load', onLoad);
    return () => { clearTimeout(t); window.removeEventListener('load', onLoad); };
  }, [rb]);

  if (!rb) return null;

  const items = [];
  const diamondSel = hasItem(rb.diamond);
  const settingSel = hasItem(rb.setting);
  // Both selected (complete ring) -> no notice at all.
  if (diamondSel && settingSel) return null;
  // Show only on the SECOND step: the diamond notice belongs on the
  // settings listing, the setting notice on the diamonds listing.
  // (page = 'diamonds' | 'settings'; empty = old behaviour)
  if (diamondSel && page !== 'diamonds') items.push({ type: 'diamond', label: 'Diamond' });
  if (settingSel && page !== 'settings') items.push({ type: 'setting', label: 'Setting' });
  if (!items.length) return null;

  const ajax = rb.admin_ajax_url || '/wp-admin/admin-ajax.php';

  const onRemove = (e, type) => {
    e.preventDefault();
    if (busy) return;
    setBusy(type);
    const body = new URLSearchParams();
    body.append('action', 'ls_rb_clear');
    body.append('type', type);
    fetch(ajax, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body, credentials: 'same-origin' })
      .then((r) => r.json())
      .then(() => {
        if (afterRemove === 'reload') window.location.reload();
        else window.location.href = afterRemove;
      })
      .catch((err) => { console.error('ls_rb_clear error', err); setBusy(null); });
  };

  return (
    <div className="ls-rb-match-notice">
      {items.map((it) => (
        <div key={it.type} className="ls-rb-match-notice__row">
          <span>Only displaying results that match your {it.label.toLowerCase()}.</span>{' '}
          <a href="#" className="ls-rb-match-notice__remove" onClick={(e) => onRemove(e, it.type)}>
            {busy === it.type ? 'Removing…' : `Remove ${it.label}`}
          </a>
        </div>
      ))}
    </div>
  );
}
