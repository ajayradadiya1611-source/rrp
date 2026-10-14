// ─────────────────────────────────────────────────────────────
// FilterSidebar.jsx
// Left-hand filter sidebar (Type / Certificate / Method / Table(%) /
// Depth(%) / L-W Ratio / Advanced Filters) matching the reference
// screenshot layout. Fully driven by props so the SAME component can
// be dropped into App.jsx and App2.jsx — it reuses the exact filter
// state, *Input state, and handlers those files already have; it adds
// nothing new to the data layer except (optionally) the Advanced
// Filters block, which is wired up by the parent.
// ─────────────────────────────────────────────────────────────

import React, { useState, useEffect, useRef } from 'react';
import FilterSlider, { getSliderPreset } from './FilterSlider';
import CheckboxGroup from './CheckboxGroup';
import RangeSlider from './RangeSlider';
import ShapeSelector from './ShapeSelector';
import { TOOLTIP_TEXT } from '../constants/tooltipTexts';
import FieldLabel from './FieldLabel';

export function SidebarSection({ id, title, tooltip, open, onToggle, controlType, divider = false, children }) {
  // Keep the body mounted for a moment after `open` goes false so the
  // fade-out animation can actually play, then remove it from the DOM.
  const [mounted, setMounted] = useState(open);
  const [closing, setClosing] = useState(false);
  const closeTimerRef = useRef(null);

  useEffect(() => {
    if (open) {
      if (closeTimerRef.current) {
        clearTimeout(closeTimerRef.current);
        closeTimerRef.current = null;
      }
      setClosing(false);
      setMounted(true);
    } else if (mounted) {
      setClosing(true);
      closeTimerRef.current = setTimeout(() => {
        setMounted(false);
        setClosing(false);
        closeTimerRef.current = null;
      }, 180);
    }
    return () => {
      if (closeTimerRef.current) clearTimeout(closeTimerRef.current);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open]);

  return (
    <>
      <div className="ls-sidebar-section">
        <button
          type="button"
          className="ls-sidebar-section-header"
          onClick={() => onToggle(id)}
          aria-expanded={open}
        >
          <span className="ls-sidebar-section-title">
            {title}
            {tooltip && (
              <i
                className="fa-solid fa-question question_icon ms-1"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-original-title={tooltip}
                onClick={(e) => e.stopPropagation()}
              ></i>
            )}
          </span>
          <i
            className="fa-solid fa-chevron-down ls-sidebar-chevron"
            style={{ transform: open ? 'rotate(180deg)' : 'rotate(0deg)' }}
          ></i>
        </button>
        {mounted && (
          <div
            className={`ls-sidebar-section-body${controlType ? ` ls-sidebar-section-body-${controlType}` : ''}${controlType === 'number-pip' ? ' ls-pips' : ''}${closing ? ' is-closing' : ''}`}
          >
            {children}
          </div>
        )}
      </div>
      {divider && <div className="ls-sidebar-section-divider"></div>}
    </>
  );
}

function PillGroup({ name, options, selected, onChange, multi = true }) {
  return (
    <div className={`ls-sidebar-pill-group${name ? ` ls-sidebar-pill-group-${name}` : ''}`}>
      {options.map(({ value, label, tooltip }) => {
        const isActive = multi ? selected.includes(value) : selected === value;
        return (
          <button
            key={value}
            type="button"
            className={`ls-sidebar-pill${isActive ? ' active' : ''}`}
            onClick={() => onChange(value)}
            {...(tooltip ? {
              'data-bs-toggle': 'tooltip',
              'data-bs-placement': 'top',
              'data-bs-original-title': tooltip,
            } : {})}
          >
            {label}
          </button>
        );
      })}
    </div>
  );
}

function NumericRangeControl({ field, min, max, step = 0.01, unit = '', value, onInputChange, onCommit, hideInputs = false, idSuffix = '' }) {
  const hasPips = !!getSliderPreset(field).pips;
  const minId = idSuffix ? `min-${field}-${idSuffix}` : `min-${field}`;
  const maxId = idSuffix ? `max-${field}-${idSuffix}` : `max-${field}`;

  return (
    <div className={`slider-box w-100 ls-sidebar-slider-box ls-sidebar-slider-box-${field}${hideInputs ? ' ls-sidebar-slider-box-no-inputs' : ''}${hasPips ? ' ls-sidebar-slider-box-pips ls-pips' : ''}`}>
      <FilterSlider
        field={field}
        start={value[0]}
        end={value[1]}
        onChange={(values, event) => {
          const roundedMin = step < 1 ? Number(parseFloat(values[0]).toFixed(2)) : Math.floor(values[0]);
          const roundedMax = step < 1 ? Number(parseFloat(values[1]).toFixed(2)) : Math.floor(values[1]);

          const minEl = document.getElementById(minId);
          const maxEl = document.getElementById(maxId);
          if (minEl) minEl.value = roundedMin;
          if (maxEl) maxEl.value = roundedMax;

          if (event === 'change') {
            onInputChange([roundedMin, roundedMax]);
            onCommit([roundedMin, roundedMax]);
          }
        }}
      />
      {!hideInputs && (
      <div className="input-box ls-sidebar-input-box">
        <div>
          <input
            id={minId}
            type="number" min={min} max={max} step={step}
            value={value[0]}
            onChange={(e) => onInputChange([e.target.value, value[1]])}
            onBlur={(e) => {
              let v = Number(e.target.value);
              if (isNaN(v)) v = min;
              v = Math.min(Math.max(v, min), max);
              const maxValue = Number(value[1]);
              const minValue = Math.min(v, maxValue);
              onInputChange([minValue, maxValue]);
              onCommit([minValue, maxValue]);
            }}
          />
        </div>
        <div>
          <input
            id={maxId}
            type="number" min={min} max={max} step={step}
            value={value[1]}
            onChange={(e) => onInputChange([value[0], e.target.value])}
            onBlur={(e) => {
              let v = Number(e.target.value);
              if (isNaN(v)) v = max;
              v = Math.min(Math.max(v, min), max);
              const minValue = Number(value[0]);
              const maxValue = Math.max(v, minValue);
              onInputChange([minValue, maxValue]);
              onCommit([minValue, maxValue]);
            }}
          />
        </div>
      </div>
      )}
    </div>
  );
}

export default function FilterSidebar({
  className = '',
  // SKU / GIA / IGI / GCAL search (plain input, no "open in new tab" icon)
  skuValue,
  onSkuChange,
  onSkuBlur,
  onSkuKeyDown,
  onSkuSubmit,
  // Shape (optional — off by default, pass shapeOptions to enable)
  shapeOptions,
  templateUrl,
  shapeSelected = [],
  onShapeChange,
  isRingBuilder = false,
  moreShapes = [],
  // Type
  typeOptions = [],           // [{value, label, href}]
  activeType,
  // Certificate
  certificateOptions = [],    // [{value, label}]
  certificateSelected = [],
  onCertificateChange,
  // Price (optional — off by default, pass priceInput to enable)
  priceInput,
  onPriceInputChange,
  onPriceCommit,
  priceMin,
  priceMax,
  // Carat (optional — off by default, pass caratInput to enable)
  caratInput,
  onCaratInputChange,
  onCaratCommit,
  caratMin,
  caratMax,
  // Cut (optional — off by default, pass cutValue to enable)
  cutValue,
  onCutChange,
  // Color (optional — off by default, pass colorValue to enable)
  colorValue,
  onColorChange,
  // Color as fancy-color swatches instead of a D-Z slider (App2 —
  // colored diamonds). Mutually exclusive with colorValue/onColorChange:
  // pass colorSwatchOptions instead to render this variant under the
  // same "Color" section.
  colorSwatchOptions,          // [{key, label, img}]
  colorSwatchValue,            // currently selected key
  onColorSwatchChange,
  // Intensity (App2 only — no white-diamond equivalent, so this section
  // only renders when intensityValue is passed)
  intensityValue,              // array of selected indices (1-6)
  onIntensityChange,
  intensityLabels = [],
  intensitySvgFill,
  // Clarity (optional — off by default, pass clarityValue to enable)
  clarityValue,
  onClarityChange,
  // Advanced Options (H&A) — only relevant when a single round shape is selected
  showHeartArrow = false,
  heartArrowActive = false,
  onHeartArrowToggle,
  // Cut Style — generalized version of Heart & Arrow for other shapes
  // (Pear Step, Square Emerald, Cushion Modified, etc). Only relevant
  // when a single shape with configured styles is selected — see
  // constants/cutStyles.js. `cutStyleOptions` is `[{value, label}]`,
  // `cutStyleValue` is the currently-selected value (string) or ''.
  cutStyleOptions = null,
  cutStyleValue = '',
  onCutStyleChange,
  cutStyleTitle = 'Cut Style',
  // Method
  methodOptions = [],         // [{value, label}]
  methodSelected = [],
  onMethodChange,
  // Media
  mediaOptions = [],          // [{value, label}]
  mediaSelected = [],
  onMediaChange,
  // Table (%)
  tableInput,
  onTableInputChange,
  onTableCommit,
  // Depth (%)
  depthInput,
  onDepthInputChange,
  onDepthCommit,
  // L/W Ratio
  lwratioInput,
  onLwratioInputChange,
  onLwratioCommit,
  // Advanced Filters (all optional — parent decides whether to pass these in)
  advanced,
  // Show a divider line after each section — used when this component is
  // reused inside the mobile filter modal (kept off for the desktop sidebar).
  showDividers = false,
  // Unique suffix appended to every field's input ids (min-price, max-price,
  // etc). FilterSidebar is mounted twice at once (desktop sidebar + mobile
  // filters modal), so without this the two copies collide on the same
  // DOM ids and getElementById() picks the wrong instance. Pass a distinct
  // value per mount point, e.g. idSuffix="desktop" / idSuffix="mobile".
  idSuffix = '',
}) {
  const [open, setOpen] = useState({
    shape: true,
    type: true,
    cutPattern: true,
    certificate: true,
    price: true,
    carat: true,
    cut: true,
    color: true,
    intensity: true,
    clarity: true,
    method: true,
    table: true,
    depth: true,
    lwratio: true,
    media: true,
    advanced: true,
  });

  const toggle = (id) => setOpen((prev) => ({ ...prev, [id]: !prev[id] }));

  return (
    <aside className={`ls-filter-sidebar${className ? ` ${className}` : ''}`}>
      {onSkuChange && (
        <div className="ls-sidebar-search d-flex align-items-center">
          <input
            type="text"
            className="form-control"
            placeholder="Search by SKU/GIA/IGI/GCAL number"
            value={skuValue}
            onChange={onSkuChange}
            onBlur={onSkuBlur}
            onKeyDown={onSkuKeyDown}
          />
          {onSkuSubmit && (
            <button type="button" className="ls-sidebar-search-submit" onClick={onSkuSubmit}>
              Submit
            </button>
          )}
        </div>
      )}
       {colorSwatchOptions && colorSwatchOptions.length > 0 && (
        <SidebarSection id="color" title="Color" tooltip={TOOLTIP_TEXT.fancyColor} open={open.color} onToggle={toggle} divider={showDividers}>
          <div className="d-flex flex-wrap gap_10 justify-content-center">
            {colorSwatchOptions.map((c) => (
              <div
                key={c.key}
                className={`color_diamond text-center ${colorSwatchValue == c.key ? 'active' : ''}`}
                onClick={() => onColorSwatchChange(c.key)}
                role="button"
              >
                <div>
                  <img src={`${templateUrl}/assets/image/diamond/${c.img}`} alt={c.label} />
                  <p className="mb-0">{c.label}</p>
                </div>
              </div>
            ))}
          </div>
        </SidebarSection>
      )}
      {shapeOptions && shapeOptions.length > 0 && (
        <SidebarSection id="shape" title="Shape" tooltip={TOOLTIP_TEXT.shape} open={open.shape} onToggle={toggle} divider={showDividers}>
          <ShapeSelector
            shapes={shapeOptions}
            templateUrl={templateUrl}
            selected={shapeSelected}
            onChange={onShapeChange}
            isRingBuilder={isRingBuilder}
            initialVisible={9}
            moreShapes={moreShapes}
          />
        </SidebarSection>
      )}

      {typeOptions.length > 0 && (
        <SidebarSection id="type" title="Type" tooltip={TOOLTIP_TEXT.type} controlType="pill" open={open.type} onToggle={toggle} divider={showDividers}>
          <div className="ls-sidebar-pill-group">
            {typeOptions.map((opt) => (
              <a
                key={opt.value}
                href={opt.href}
                className={`ls-sidebar-pill${activeType === opt.value ? ' active' : ''}`}
              >
                {opt.label}
              </a>
            ))}
          </div>
        </SidebarSection>
      )}

      {(showHeartArrow || (cutStyleOptions && cutStyleOptions.length > 0)) && (
        <SidebarSection id="cutPattern" title="Cut Pattern" tooltip={TOOLTIP_TEXT.heartArrow || TOOLTIP_TEXT.cutStyle} controlType="pill" open={open.cutPattern} onToggle={toggle} divider={showDividers}>
          <div className="ls-sidebar-pill-group">
            {showHeartArrow && (
              <button
                className={`ls-sidebar-pill ${heartArrowActive ? 'show' : ''} ${heartArrowActive ? 'active' : ''}`}
                type="button"
                onClick={onHeartArrowToggle}
                aria-expanded='false'
              >
                Heart & Arrow
              </button>
            )}
            {cutStyleOptions && cutStyleOptions.map(({ value, label }) => (
              <button
                key={value}
                type="button"
                className={`ls-sidebar-pill ${cutStyleValue === value ? 'active' : ''}`}
                onClick={() => onCutStyleChange && onCutStyleChange(value)}
              >
                {label}
              </button>
            ))}
          </div>
        </SidebarSection>
      )}

      {certificateOptions.length > 0 && (
        <SidebarSection id="certificate" title="Certificate" tooltip={TOOLTIP_TEXT.certificate} controlType="pill" open={open.certificate} onToggle={toggle} divider={showDividers}>
          <PillGroup name="certificate" options={certificateOptions} selected={certificateSelected} onChange={onCertificateChange} />
        </SidebarSection>
      )}

      {priceInput && (
        <SidebarSection id="price" title="Price" tooltip={TOOLTIP_TEXT.price} controlType="number" open={open.price} onToggle={toggle} divider={showDividers}>
          <NumericRangeControl
            field="price" min={priceMin} max={priceMax} step={1}
            value={priceInput}
            onInputChange={onPriceInputChange}
            onCommit={onPriceCommit}
          idSuffix={idSuffix}
          />
        </SidebarSection>
      )}

      {caratInput && (
        <SidebarSection id="carat" title="Carat" tooltip={TOOLTIP_TEXT.carat} controlType="number" open={open.carat} onToggle={toggle} divider={showDividers}>
          <NumericRangeControl
            field="carat" min={caratMin} max={caratMax} step={0.01}
            value={caratInput}
            onInputChange={onCaratInputChange}
            onCommit={onCaratCommit}
          idSuffix={idSuffix}
          />
        </SidebarSection>
      )}

      {cutValue && (
        <SidebarSection id="cut" title="Cut" tooltip={TOOLTIP_TEXT.cut} controlType="number-pip" open={open.cut} onToggle={toggle} divider={showDividers}>
          <FilterSlider field="cut" start={cutValue[0]} end={cutValue[1]} onChange={onCutChange} />
        </SidebarSection>
      )}

      {colorValue && (
        <SidebarSection id="color" title="Color" tooltip={TOOLTIP_TEXT.color} controlType="number-pip" open={open.color} onToggle={toggle} divider={showDividers}>
          <FilterSlider field="color" start={colorValue[0]} end={colorValue[1]} onChange={onColorChange} />
        </SidebarSection>
      )}

     

      {intensityValue && (
        <SidebarSection id="intensity" title="Intensity"  tooltip={TOOLTIP_TEXT.intensity} open={open.intensity} onToggle={toggle} divider={showDividers}>
          <div className="d-flex flex-wrap gap-4 justify-content-center mb-intensity">
            {Array.from({ length: 6 }, (_, i) => i + 1).map((idx) => (
              <div className="position-relative" key={idx}>
                <div
                  className={`intensity_box ${intensityValue.includes(idx) ? 'active' : ''}`}
                  onClick={() => onIntensityChange(idx)}
                >
                  <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="48.000000pt" height="53.000000pt" viewBox="0 0 48.000000 53.000000" preserveAspectRatio="xMidYMid meet">
                    <g transform="translate(0.000000,53.000000) scale(0.100000,-0.100000)" fill="rgb(49, 65, 133)" stroke="none" style={{ fill: intensitySvgFill, opacity: 0.2 + ((idx - 1) / 5) * 0.8 }}>
                      <path d="M127 471 l-98 -58 36 -22 36 -21 65 36 c64 36 64 36 64 80 0 24 -1 44 -2 43 -2 0 -47 -26 -101 -58z"></path>
                      <path d="M262 483 c3 -43 5 -46 63 -79 l61 -33 36 22 36 22 -100 57 -99 57 3 -46z"></path>
                      <path d="M178 374 l-58 -35 0 -69 0 -70 41 -25 c79 -50 81 -51 145 -13 l59 34 3 71 3 72 -64 35 c-35 20 -66 36 -68 35 -2 0 -30 -16 -61 -35z"></path>
                      <path d="M10 270 c0 -60 3 -110 6 -110 4 0 22 9 40 21 33 20 34 22 34 89 0 67 0 68 -36 89 -19 12 -37 21 -40 21 -2 0 -4 -49 -4 -110z"></path>
                      <path d="M433 358 c-32 -19 -33 -22 -33 -89 0 -67 1 -70 33 -89 17 -11 35 -20 40 -20 4 0 7 50 7 110 0 61 -3 110 -7 109 -5 0 -23 -10 -40 -21z"></path>
                      <path d="M65 145 l-37 -22 58 -33 c33 -18 78 -44 102 -57 l42 -25 0 44 c0 42 -1 43 -64 79 l-64 37 -37 -23z"></path>
                      <path d="M322 132 c-61 -36 -62 -37 -62 -80 0 -44 0 -44 23 -30 12 8 57 34 99 58 43 24 77 44 75 45 -1 1 -18 11 -37 23 l-36 21 -62 -37z"></path>
                    </g>
                  </svg>
                </div>
                <span className="intensity-name position-absolute top-100">{intensityLabels[idx - 1]}</span>
              </div>
            ))}
          </div>
        </SidebarSection>
      )}

      {clarityValue && (
        <SidebarSection id="clarity" title="Clarity" tooltip={TOOLTIP_TEXT.clarity} controlType="number-pip" open={open.clarity} onToggle={toggle} divider={showDividers}>
          <FilterSlider field="clarity" start={clarityValue[0]} end={clarityValue[1]} onChange={onClarityChange} />
        </SidebarSection>
      )}

      {methodOptions.length > 0 && (
        <SidebarSection id="method" title="Growth Type" tooltip={TOOLTIP_TEXT.method} controlType="pill" open={open.method} onToggle={toggle} divider={showDividers}>
          <PillGroup name="method" options={methodOptions} selected={methodSelected} onChange={onMethodChange} />
        </SidebarSection>
      )}

      {tableInput && (
        <SidebarSection id="table" title="Table (%)" tooltip={TOOLTIP_TEXT.table} controlType="number" open={open.table} onToggle={toggle} divider={showDividers}>
          <NumericRangeControl
            field="table" min={50} max={80} step={0.01}
            value={tableInput}
            onInputChange={onTableInputChange}
            onCommit={onTableCommit}
          idSuffix={idSuffix}
          />
        </SidebarSection>
      )}

      {depthInput && (
        <SidebarSection id="depth" title="Depth (%)" tooltip={TOOLTIP_TEXT.depth} controlType="number" open={open.depth} onToggle={toggle} divider={showDividers}>
          <NumericRangeControl
            field="depth" min={46} max={78} step={0.01}
            value={depthInput}
            onInputChange={onDepthInputChange}
            onCommit={onDepthCommit}
          idSuffix={idSuffix}
          />
        </SidebarSection>
      )}

      {lwratioInput && (
        <SidebarSection id="lwratio" title="L/W Ratio" tooltip={TOOLTIP_TEXT.lwratio} controlType="number" open={open.lwratio} onToggle={toggle} divider={showDividers}>
          <NumericRangeControl
            field="lwratio" min={1} max={2.75} step={0.01}
            value={lwratioInput}
            onInputChange={onLwratioInputChange}
            onCommit={onLwratioCommit}
          idSuffix={idSuffix}
          />
        </SidebarSection>
      )}

      {mediaOptions.length > 0 && (
        <SidebarSection id="media" title="Media" tooltip={TOOLTIP_TEXT.media} controlType="checkbox" open={open.media} onToggle={toggle} divider={showDividers}>
          <CheckboxGroup name="media" options={mediaOptions} selected={mediaSelected} onChange={onMediaChange} />
        </SidebarSection>
      )}

      {advanced && (
        <SidebarSection id="advanced" title="Advanced Filters" tooltip={TOOLTIP_TEXT.advanced} controlType="number" open={open.advanced} onToggle={toggle} divider={showDividers}>
          <div className="ls-sidebar-advanced-body">
            {advanced.length && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Length (mm)" tooltip={TOOLTIP_TEXT.length} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="length" min={3} max={20} step={0.01}
                  value={advanced.length.value}
                  onInputChange={advanced.length.onInputChange}
                  onCommit={advanced.length.onCommit}
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.width && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Width (mm)" tooltip={TOOLTIP_TEXT.width} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="width" min={3} max={15} step={0.01}
                  value={advanced.width.value}
                  onInputChange={advanced.width.onInputChange}
                  onCommit={advanced.width.onCommit}
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.height && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Height (mm)" tooltip={TOOLTIP_TEXT.height} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="height" min={2} max={12} step={0.01}
                  value={advanced.height.value}
                  onInputChange={advanced.height.onInputChange}
                  onCommit={advanced.height.onCommit}
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.fluorescence && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Fluorescence" tooltip={TOOLTIP_TEXT.fluorescence} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="fluorescence" min={0} max={4} step={1}
                  value={advanced.fluorescence.value}
                  onInputChange={advanced.fluorescence.onInputChange}
                  onCommit={advanced.fluorescence.onCommit}
                  hideInputs
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.polish && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Polish" tooltip={TOOLTIP_TEXT.polish} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="polish" min={0} max={2} step={1}
                  value={advanced.polish.value}
                  onInputChange={advanced.polish.onInputChange}
                  onCommit={advanced.polish.onCommit}
                  hideInputs
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.symmetry && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Symmetry" tooltip={TOOLTIP_TEXT.symmetry} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="symmetry" min={0} max={2} step={1}
                  value={advanced.symmetry.value}
                  onInputChange={advanced.symmetry.onInputChange}
                  onCommit={advanced.symmetry.onCommit}
                  hideInputs
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.crownAngle && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Crown Angle" tooltip={TOOLTIP_TEXT.crownAngle} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="crown_angle" min={23} max={40} step={0.1} unit="°"
                  value={advanced.crownAngle.value}
                  onInputChange={advanced.crownAngle.onInputChange}
                  onCommit={advanced.crownAngle.onCommit}
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.pavilionAngle && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Pavilion Angle" tooltip={TOOLTIP_TEXT.pavilionAngle} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="pavilion_angle" min={38} max={43} step={0.1} unit="°"
                  value={advanced.pavilionAngle.value}
                  onInputChange={advanced.pavilionAngle.onInputChange}
                  onCommit={advanced.pavilionAngle.onCommit}
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.girdle && (
              <>
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Girdle Thickness" tooltip={TOOLTIP_TEXT.girdle} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="girdle" min={0} max={7} step={1}
                  value={advanced.girdle.value}
                  onInputChange={advanced.girdle.onInputChange}
                  onCommit={advanced.girdle.onCommit}
                  hideInputs
                idSuffix={idSuffix}
                />
              </div>
              {showDividers && <div className="ls-sidebar-section-divider"></div>}
              </>
            )}
            {advanced.culet && (
              <div className="ls-sidebar-subfield">
                <FieldLabel as="span" label="Culet" tooltip={TOOLTIP_TEXT.culet} className="ls-sidebar-subfield-label d-flex align-items-center" />
                <NumericRangeControl
                  field="culet" min={0} max={4} step={1}
                  value={advanced.culet.value}
                  onInputChange={advanced.culet.onInputChange}
                  onCommit={advanced.culet.onCommit}
                  hideInputs
                idSuffix={idSuffix}
                />
              </div>
            )}
          </div>
        </SidebarSection>
      )}
    </aside>
  );
}