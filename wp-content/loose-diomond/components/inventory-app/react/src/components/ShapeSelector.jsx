import { useState } from 'react';
import Modal from './Modal';

/**
 * Reusable diamond-shape picker grid.
 * Shared between App.jsx / App2.jsx and usable standalone (top toolbar
 * scroll row) or embedded inside FilterSidebar's Shape section.
 *
 * Props:
 *  - shapes: array of { id, value, label, alt, img }
 *  - templateUrl: string, prefix for shape image src
 *  - selected: array of currently selected shape values (filter.shape)
 *  - onChange: (value) => void — toggles a shape on/off
 *  - isRingBuilder: boolean — ring builder locks selection to a single shape
 *  - initialVisible: number of shapes to show before the "+N" toggle
 *    (default 10). Pass Infinity to always show all shapes (e.g. horizontal
 *    scroll row) with no toggle at all.
 *  - moreShapes: array of extra "fancy" shapes ({ id, value, label, alt, img })
 *    to offer in the "+N" modal, on top of any base shapes beyond
 *    initialVisible. Defaults to []. Each app must pass its own list (and
 *    its own img path convention) — not every app supports every fancy
 *    shape, so this is never assumed/hardcoded here.
 *
 * Clicking "+N" opens a modal listing the extra fancy shapes (moreShapes,
 * plus any base shapes beyond initialVisible). Picking a shape in the modal
 * selects it and closes the modal immediately. If there's nothing to show
 * beyond the first row (no extra base shapes and no moreShapes passed), the
 * "+N" trigger doesn't render at all.
 */
export default function ShapeSelector({
  shapes,
  templateUrl,
  selected = [],
  onChange,
  isRingBuilder = false,
  initialVisible = 10,
  moreShapes = [],
}) {
  const [showMoreModal, setShowMoreModal] = useState(false);

  const isSelectedShape = (value) =>
    isRingBuilder ? selected?.[0] === value : selected?.includes(value);

  const renderShape = (s, { inModal = false } = {}) => {
    const isSelected = isSelectedShape(s.value);
    const inputId = inModal ? `modal-${s.id}` : s.id;

    const handlePick = () => {
      // Ring builder: shape is locked to the setting — nothing is pickable
      // (selected one stays, all others are disabled), in grid and modal.
      if (isRingBuilder) return;
      onChange(s.value);
      if (inModal) setShowMoreModal(false);
    };

    return (
      <label
        key={inputId}
        htmlFor={inputId}
        className={`shape-box ${isSelected ? 'active' : ''} ${isRingBuilder && !isSelected ? 'disabled' : ''}`}
        aria-disabled={isRingBuilder && !isSelected ? 'true' : undefined}
        onClick={inModal || isRingBuilder ? (e) => { e.preventDefault(); handlePick(); } : undefined}
      >
        {isRingBuilder ? (
          isSelected && (
            <input
              type="checkbox"
              name="shape[]"
              value={s.value}
              id={inputId}
              className="checkbox"
              checked
              readOnly
            />
          )
        ) : (
          <input
            type="checkbox"
            name="shape[]"
            value={s.value}
            id={inputId}
            className="checkbox"
            checked={isSelected}
            onChange={inModal ? undefined : () => onChange(s.value)}
            readOnly={inModal}
          />
        )}
        <span className="shape-box-inner">
          <img src={`${templateUrl}${s.img}`} alt={s.alt} className="shape-image" />
          <p className="text-capitalize">{s.label}</p>
        </span>
      </label>
    );
  };

  const firstRow = shapes.slice(0, initialVisible);
  const restBaseShapes = shapes.slice(initialVisible);

  // moreShapes may repeat some values that already exist further down the
  // base `shapes` array (both lists are maintained independently). Dedupe
  // by value so the "+N" modal never shows the same shape twice.
  const seenValues = new Set(restBaseShapes.map((s) => s.value));
  const extraShapes = moreShapes.filter((s) => !seenValues.has(s.value));
  const modalShapes = [...restBaseShapes, ...extraShapes];

  // If the current selection is one of the "more" shapes (not shown in the
  // main grid), surface it as its own active box next to the "+" trigger so
  // the user can see/undo it without reopening the modal.
  const selectedMoreShape = modalShapes.find((s) =>
    isRingBuilder ? selected?.[0] === s.value : selected?.includes(s.value)
  );

  return (
    <div className="shape">
      <div className="d-flex align-items-center">
        <div className="d-flex align-items-center flex-column gap-4 w-100">
          <div className="d-flex align-items-center justify-content-between flex-wrap flex-lg-nowrap w-100 row row-cols-5 g-4">
            {firstRow.map((s) => renderShape(s))}
            {selectedMoreShape && renderShape(selectedMoreShape)}
            {modalShapes.length > 0 && (
              <div className="more" onClick={() => setShowMoreModal(true)} role="button">
                <p className="mb-0">+{modalShapes.length}</p>
              </div>
            )}
          </div>
        </div>
        
      </div>

      <Modal
        isOpen={showMoreModal}
        onClose={() => setShowMoreModal(false)}
        size="lg"
        bodyClass="ls-modal-open"
      >
        <div class="p-4">
          <h5 class="mb-4">All Shapes</h5>
          <div className="d-flex align-items-center justify-content-start flex-wrap w-100 row row-cols-5 g-2">
            {modalShapes.map((s) => renderShape(s, { inModal: true }))}
          </div>
        </div>
      </Modal>
    </div>
  );
}
