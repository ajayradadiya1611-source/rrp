// ─────────────────────────────────────────────────────────────
// InventoryStateBlocks.jsx
// The "no results" / "ask us" / "loading" blocks were previously
// copy-pasted identically across InventoryResults.jsx (App.jsx) and
// InventoryResults2.jsx (App2.jsx) — same diamond-facts array, same
// copy, same logic, just wrapped in <tr>/<td> for the table and
// <div> for the grid. Centralized here so there's one copy of the
// facts + one copy of the copy text, with a small `as` switch for
// the two wrapper shapes list/grid actually need.
// ─────────────────────────────────────────────────────────────
import React from 'react';
import { templateUrl } from './inventoryHelpers';

const DIAMOND_FACTS = [
  "💎 Did you know? Lab-grown diamonds are chemically identical to mined diamonds — but far more ethical and affordable.",
  "💰 Fun fact: 1-carat lab-grown diamonds are typically 30–40% cheaper than mined ones.",
  "🔍 Clarity tip: Tiny inclusions give every diamond a unique fingerprint.",
  "✨ Cut matters most. Better cut = more sparkle.",
  "📚 The 4Cs: Cut, Color, Clarity, Carat. Prioritize Cut for the best brilliance.",
  "🏷 Price Match: Found the same certified diamond cheaper? We'll beat it.",
  "✔ No gimmicks. Just honest pricing on certified lab-grown diamonds.",
  "📜 Certified quality: IGI, GIA and GCAL certified diamonds.",
  "🛡 Peace of mind: Lifetime warranty*, insured shipping, and free resizing, " + (typeof window !== 'undefined' ? window.themeData?.RETURN_DAYS : '') + " days returns.",
  "🌱 Better choice: Conflict-free and environmentally friendly diamonds.",
];

function randomDiamondFact() {
  return DIAMOND_FACTS[Math.floor(Math.random() * DIAMOND_FACTS.length)];
}

const LoadingInner = ({ showFact = true }) => (
  <>
    <div className="loading">
      <img src={`${templateUrl}/aj_custom/inventory/Loading.edc97a1c.gif`} width="40" height="40" alt="Loading" />
    </div>
    {showFact && <div className="dyanamic_text">{randomDiamondFact()}</div>}
  </>
);

const EmptyInner = ({ handleClearFilter, showAskNow = true }) => (
  <>
    <div className="oc_no_result">
      <div className="stext">Zero results 😕</div>
      <div className="reset_text">Sometimes it's because of a filter you forgot to unset. <a href="#" onClick={handleClearFilter}> Reset Filters </a> can help.</div>
      {showAskNow && (
        <div className="ask_now_bx">
          <div className="stext">Looking for something specific but can't find it? Let our team know what you need.</div>
          <div className="dsearch_pup">
            <span className="ls_bbtn" data-bs-toggle="modal" data-bs-target="#askUsModal">Ask us Now</span>
          </div>
        </div>
      )}
    </div>
  </>
);

const AskNowInner = () => (
  <div className="invtr_asknow">
    Looking for something specific but can't find it? Let our team know what you need.<span className="invtr_ask_pup" data-bs-toggle="modal" data-bs-target="#askUsModal"> Ask us Now</span>
  </div>
);

// ── Table (list view) variants — <tr>/<td> wrapped ─────────────
export function TableLoadingRow({ colSpan = 8, showFact = true }) {
  return (
    <tr className="ls_in_loader">
      <td colSpan={colSpan} style={{ textAlign: 'center', background: 'white' }}>
        <LoadingInner showFact={showFact} />
      </td>
    </tr>
  );
}

export function TableEmptyRow({ colSpan = 8, handleClearFilter, showAskNow = true }) {
  return (
    <tr className="contact">
      <td colSpan={colSpan} style={{ textAlign: 'center' }}>
        <EmptyInner handleClearFilter={handleClearFilter} showAskNow={showAskNow} />
      </td>
    </tr>
  );
}

export function TableAskNowRow({ colSpan = 8 }) {
  return (
    <tr className="ask_now_tr text-center">
      <td colSpan={colSpan}>
        <AskNowInner />
      </td>
    </tr>
  );
}

// ── Grid variants — <div> wrapped ──────────────────────────────
export function GridLoadingBlock({ showFact = true }) {
  return (
    <div className="ls_in_loader text-center" style={{ background: 'white' }}>
      <LoadingInner showFact={showFact} />
    </div>
  );
}

export function GridEmptyBlock({ handleClearFilter, showAskNow = true }) {
  return (
    <div className="contact text-center">
      <EmptyInner handleClearFilter={handleClearFilter} showAskNow={showAskNow} />
    </div>
  );
}

export function GridAskNowBlock() {
  return (
    <div className="ask_now_tr text-center">
      <AskNowInner />
    </div>
  );
}
