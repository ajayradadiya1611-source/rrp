import React, { useEffect, useState } from 'react';
import { fetchMeta, fetchOne, createDiamond, updateDiamond } from '../../api.js';

const BLANK = {
  lot_number: '', sku: '', shape: '', shape_title: '', price: '', org_price: '', sale_price: '', rap: '',
  carat: '', cut: 0, color: '', intensity: '', clarity: '', video: '', certificate: '', certificate_type: '',
  mm: '', note: '', status: 0, sale: 0, dtype: 'HPHT', av_table: '', depth: '',
  lwratio: '', shipping: 0, ha: 0,
  sheet_provider: '', vendor_id: '', vendor_name: '', vendor_city: '', vendor_country: '', stock_num: '',
  polish: 0, symmetry: 0, fluorescence: 0, culet: 0, girdle_thickness: 0,
  crown_angle: '', pavilion_angle: '', length: '', width: '', height: '',
  image: '', delivery_days: '', is_media: 0,
};

export default function InventoryForm({ type, mode, id, onDone, onCancel }) {
  const [meta, setMeta] = useState(null);
  const [form, setForm] = useState(BLANK);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState('');
  const [priceError, setPriceError] = useState('');

  useEffect(() => {
    let cancelled = false;
    Promise.all([
      fetchMeta(type),
      mode === 'edit' ? fetchOne(type, id) : Promise.resolve(null),
    ])
      .then(([metaData, row]) => {
        if (cancelled) return;
        setMeta(metaData);
        if (row) {
          setForm({ ...BLANK, ...row });
        }
      })
      .catch((err) => setError(err.message))
      .finally(() => !cancelled && setLoading(false));
    return () => { cancelled = true; };
  }, [type, mode, id]);

  const set = (key) => (e) => {
    const value = e.target.type === 'checkbox' ? (e.target.checked ? 1 : 0) : e.target.value;
    setForm((f) => ({ ...f, [key]: value }));
  };

  const validatePrice = () => {
    const main = parseInt(form.price, 10);
    const org = parseInt(form.org_price, 10);
    if (form.org_price !== '' && !Number.isNaN(org) && !Number.isNaN(main) && org <= main) {
      setPriceError('Regular Price must be greater than Main Price');
      return false;
    }
    setPriceError('');
    return true;
  };

  const onSubmit = async (e) => {
    e.preventDefault();
    if (!validatePrice()) return;
    setSaving(true);
    setError('');
    try {
      if (mode === 'add') {
        await createDiamond(type, form);
      } else {
        await updateDiamond(type, id, form);
      }
      onDone();
    } catch (err) {
      setError(err.message);
    } finally {
      setSaving(false);
    }
  };

  if (loading || !meta) {
    return (
      <div className="ls-admin-page wrap">
        <div className="ls-card ls-card-loading">Loading…</div>
      </div>
    );
  }

  const opts = meta.options;

  return (
    <div className="ls-admin-page wrap">
      <header className="ls-page-header">
        <div>
          <h1>{mode === 'add' ? 'Add Diamond' : 'Edit Diamond'}</h1>
          <p className="ls-page-subtitle">{type === 'color' ? 'Colored Diamonds' : 'Diamonds'} inventory</p>
        </div>
        <button type="button" className="ls-btn ls-btn-ghost" onClick={onCancel}>← Back to List</button>
      </header>

      {error && <div className="ls-notice ls-notice-error">{error}</div>}

      <form onSubmit={onSubmit} className="ls-card ls-form">
        <div className="ls-form-section">
          <h3 className="ls-form-section-title">Identity</h3>
          <div className="ls-form-grid">
            {mode === 'add' && (
              <Field label="Lot Number" required>
                <select value={form.lot_number} onChange={set('lot_number')} required>
                  <option value="">- Select -</option>
                  {meta.available_lots.map((lot) => (
                    <option key={lot} value={lot}>{lot}</option>
                  ))}
                  <option value="all">All</option>
                </select>
              </Field>
            )}

            <Field label="SKU" required>
              <input type="text" value={form.sku} onChange={set('sku')} />
            </Field>

            <Field label="Shape" required>
              <input type="text" value={form.shape} onChange={set('shape')} />
            </Field>

            {type === 'color' && (
              <Field label="Shape Title" required>
                <input type="text" value={form.shape_title} onChange={set('shape_title')} />
              </Field>
            )}

            <Field label="Stock #" hint="Vendor/sheet stock number, distinct from your internal SKU.">
              <input type="text" value={form.stock_num} onChange={set('stock_num')} />
            </Field>
          </div>
        </div>

        <div className="ls-form-section">
          <h3 className="ls-form-section-title">Pricing</h3>
          <div className="ls-form-grid">
            <Field label="Price" required>
              <input type="text" value={form.price} onChange={set('price')} />
            </Field>

            <Field
              label="Regular Price"
              hint="Optional — if blank it will consider Price + 5%. Must be greater than Main Price."
            >
              <input
                type="text"
                value={form.org_price}
                onChange={set('org_price')}
                onBlur={validatePrice}
                className={priceError ? 'ls-input-error' : ''}
              />
              {priceError && <div className="ls-field-error">{priceError}</div>}
            </Field>

            <Field label="Sale Price" hint="Defaults to Price if left blank.">
              <input type="text" value={form.sale_price} onChange={set('sale_price')} />
            </Field>

            <Field label="Rap" hint="Rapaport price reference.">
              <input type="text" value={form.rap} onChange={set('rap')} />
            </Field>

            <Field label="Sale" required>
              <Select value={form.sale} onChange={set('sale')} options={opts.sale} />
            </Field>
          </div>
        </div>

        <div className="ls-form-section">
          <h3 className="ls-form-section-title">4Cs &amp; Grading</h3>
          <div className="ls-form-grid">
            <Field label="Carat" required>
              <input type="text" value={form.carat} onChange={set('carat')} />
            </Field>

            <Field label="Cut" required>
              <Select value={form.cut} onChange={set('cut')} options={opts.cut} />
            </Field>

            <Field label="Color" required>
              <Select value={form.color} onChange={set('color')} options={opts.color} />
            </Field>

            {type === 'color' && (
              <Field label="Intensity" required>
                <Select value={form.intensity} onChange={set('intensity')} options={opts.intensity} />
              </Field>
            )}

            <Field label="Clarity" required>
              <Select value={form.clarity} onChange={set('clarity')} options={opts.clarity} />
            </Field>

            <Field label="Polish">
              <Select value={form.polish} onChange={set('polish')} options={opts.polish} />
            </Field>

            <Field label="Symmetry">
              <Select value={form.symmetry} onChange={set('symmetry')} options={opts.symmetry} />
            </Field>

            <Field label="Fluorescence">
              <Select value={form.fluorescence} onChange={set('fluorescence')} options={opts.fluorescence} />
            </Field>

            <Field label="Culet">
              <Select value={form.culet} onChange={set('culet')} options={opts.culet} />
            </Field>

            <Field label="Girdle Thickness">
              <Select value={form.girdle_thickness} onChange={set('girdle_thickness')} options={opts.girdle_thickness} />
            </Field>

            {type === 'white' && (
              <>
                <Field label="Diamond Type" required>
                  <Select value={form.dtype} onChange={set('dtype')} options={opts.dtype} />
                </Field>
                <Field label="Heart &amp; Arrow" required>
                  <Select value={form.ha} onChange={set('ha')} options={opts.ha} />
                </Field>
              </>
            )}
          </div>
        </div>

        <div className="ls-form-section">
          <h3 className="ls-form-section-title">Measurements</h3>
          <div className="ls-form-grid">
            <Field label="Measurement (mm)" required>
              <input type="text" value={form.mm} onChange={set('mm')} />
            </Field>

            <Field label="Table %" required>
              <input type="text" value={form.av_table} onChange={set('av_table')} />
            </Field>

            <Field label="Depth %" required>
              <input type="text" value={form.depth} onChange={set('depth')} />
            </Field>

            <Field label="L/W Ratio" required>
              <input type="text" value={form.lwratio} onChange={set('lwratio')} />
            </Field>

            <Field label="Crown Angle">
              <input type="text" value={form.crown_angle} onChange={set('crown_angle')} />
            </Field>

            <Field label="Pavilion Angle">
              <input type="text" value={form.pavilion_angle} onChange={set('pavilion_angle')} />
            </Field>

            <Field label="Length">
              <input type="text" value={form.length} onChange={set('length')} />
            </Field>

            <Field label="Width">
              <input type="text" value={form.width} onChange={set('width')} />
            </Field>

            <Field label="Height">
              <input type="text" value={form.height} onChange={set('height')} />
            </Field>
          </div>
        </div>

        <div className="ls-form-section">
          <h3 className="ls-form-section-title">Vendor &amp; Sourcing</h3>
          <div className="ls-form-grid">
            <Field label="Sheet Provider">
              <input type="text" value={form.sheet_provider} onChange={set('sheet_provider')} />
            </Field>

            <Field label="Vendor ID">
              <input type="text" value={form.vendor_id} onChange={set('vendor_id')} />
            </Field>

            <Field label="Vendor Name">
              <input type="text" value={form.vendor_name} onChange={set('vendor_name')} />
            </Field>

            <Field label="Vendor City">
              <input type="text" value={form.vendor_city} onChange={set('vendor_city')} />
            </Field>

            <Field label="Vendor Country">
              <input type="text" value={form.vendor_country} onChange={set('vendor_country')} />
            </Field>
          </div>
        </div>

        <div className="ls-form-section">
          <h3 className="ls-form-section-title">Media &amp; Documentation</h3>
          <div className="ls-form-grid">
            <Field label="Video" required>
              <input type="text" value={form.video} onChange={set('video')} />
            </Field>

            <Field label="Certificate" required>
              <input type="text" value={form.certificate} onChange={set('certificate')} />
            </Field>

            <Field label="Certificate Type" hint="e.g. IGI, GIA, GCAL">
              <input type="text" value={form.certificate_type} onChange={set('certificate_type')} />
            </Field>

            <Field label="Image URL">
              <input type="text" value={form.image} onChange={set('image')} />
            </Field>

            <Field label="Has Media">
              <Select value={form.is_media} onChange={set('is_media')} options={opts.is_media} />
            </Field>

            <Field label="Note" required className="ls-field-wide">
              <textarea rows="3" value={form.note} onChange={set('note')} />
            </Field>
          </div>
        </div>

        <div className="ls-form-section">
          <h3 className="ls-form-section-title">Fulfillment</h3>
          <div className="ls-form-grid">
            <Field label="Stock Status" required>
              <Select value={form.status} onChange={set('status')} options={opts.status} />
            </Field>

            <Field label="Shipping" required>
              <Select value={form.shipping} onChange={set('shipping')} options={opts.shipping} />
            </Field>

            <Field label="Delivery Days">
              <input type="text" value={form.delivery_days} onChange={set('delivery_days')} />
            </Field>
          </div>
        </div>

        <div className="ls-form-actions">
          <button type="button" className="ls-btn ls-btn-ghost" onClick={onCancel}>Cancel</button>
          <button type="submit" className="ls-btn ls-btn-primary" disabled={saving}>
            {saving ? 'Saving…' : mode === 'add' ? 'Add Diamond' : 'Update Diamond'}
          </button>
        </div>
      </form>
    </div>
  );
}

function Field({ label, required, hint, className = '', children }) {
  return (
    <div className={`ls-field ${className}`}>
      <label>{label} {required && <span className="ls-required">*</span>}</label>
      {children}
      {hint && <span className="ls-field-hint">{hint}</span>}
    </div>
  );
}

function Select({ value, onChange, options }) {
  return (
    <select value={value} onChange={onChange}>
      {Object.entries(options || {}).map(([name, num]) => (
        <option key={name} value={num}>{name}</option>
      ))}
    </select>
  );
}
