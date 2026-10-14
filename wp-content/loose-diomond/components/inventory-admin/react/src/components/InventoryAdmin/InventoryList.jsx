import React, { useEffect, useState, useCallback } from 'react';
import { fetchList, fetchMeta, deleteDiamond, changeStatus } from '../../api.js';

const COLUMNS = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'pid', label: 'Product', sortable: false },
  { key: 'sku', label: 'SKU', sortable: true },
  { key: 'shape', label: 'Shape', sortable: true },
  { key: 'price', label: 'Price', sortable: true },
  { key: 'carat', label: 'Carat', sortable: true },
  { key: 'video', label: 'Video', sortable: true },
  { key: 'certificate', label: 'Certificate', sortable: true },
  { key: 'certificate_type', label: 'Cert Type', sortable: true },
  { key: 'lot_number', label: 'Lot No.', sortable: false },
  { key: 'vendor_name', label: 'Vendor', sortable: true },
  { key: 'stock_num', label: 'Stock #', sortable: true },
  { key: 'note', label: 'Note', sortable: false },
  { key: 'sale', label: 'In Sale', sortable: true },
  { key: 'status', label: 'Stock', sortable: true },
];

export default function InventoryList({ type, onAdd, onEdit }) {
  const [rows, setRows] = useState([]);
  const [meta, setMeta] = useState({ lot_numbers: [] });
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(1);
  const [perPage] = useState(50);
  const [search, setSearch] = useState('');
  const [searchInput, setSearchInput] = useState('');
  const [lotNumber, setLotNumber] = useState('all');
  const [orderby, setOrderby] = useState('id');
  const [order, setOrder] = useState('asc');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [pendingStatus, setPendingStatus] = useState({});

  const load = useCallback(() => {
    setLoading(true);
    setError('');
    fetchList(type, { page, per_page: perPage, search, lot_number: lotNumber, orderby, order })
      .then((data) => {
        setRows(data.items || []);
        setTotal(data.total || 0);
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [type, page, perPage, search, lotNumber, orderby, order]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { fetchMeta(type).then(setMeta).catch(() => {}); }, [type]);

  const totalPages = Math.max(1, Math.ceil(total / perPage));

  const onSort = (col) => {
    if (!col.sortable) return;
    if (orderby === col.key) {
      setOrder(order === 'asc' ? 'desc' : 'asc');
    } else {
      setOrderby(col.key);
      setOrder('asc');
    }
    setPage(1);
  };

  const onSearchSubmit = (e) => {
    e.preventDefault();
    setSearch(searchInput);
    setPage(1);
  };

  const onDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this diamond?')) return;
    try {
      await deleteDiamond(type, id);
      load();
    } catch (err) {
      alert(err.message);
    }
  };

  const onStatusChange = async (row, value) => {
    setPendingStatus((s) => ({ ...s, [row.id]: true }));
    try {
      await changeStatus(type, row.id, Number(value));
      setRows((rs) => rs.map((r) => (r.id === row.id ? { ...r, status: value } : r)));
    } catch (err) {
      alert(err.message);
    } finally {
      setPendingStatus((s) => ({ ...s, [row.id]: false }));
    }
  };

  return (
    <div className="ls-admin-page wrap">
      <header className="ls-page-header">
        <div>
          <h1>{type === 'color' ? 'Colored Diamonds' : 'Diamonds'}</h1>
          <p className="ls-page-subtitle">
            {loading ? 'Loading inventory…' : `${total} diamond${total === 1 ? '' : 's'} in inventory`}
          </p>
        </div>
        <button className="ls-btn ls-btn-primary" onClick={onAdd}>
          <span className="ls-btn-icon">＋</span> Add Diamond
        </button>
      </header>

      <div className="ls-card">
        <div className="ls-toolbar">
          <div className="ls-field">
            <label>Lot Number</label>
            <select value={lotNumber} onChange={(e) => { setLotNumber(e.target.value); setPage(1); }}>
              <option value="all">All Lots</option>
              {meta.lot_numbers.map((lot) => (
                <option key={lot} value={lot}>{lot}</option>
              ))}
            </select>
          </div>

          <form onSubmit={onSearchSubmit} className="ls-search">
            <input
              type="text"
              placeholder="Search by SKU or Shape…"
              value={searchInput}
              onChange={(e) => setSearchInput(e.target.value)}
            />
            <button type="submit" className="ls-btn ls-btn-primary">Search</button>
          </form>
        </div>

        {error && <div className="ls-notice ls-notice-error">{error}</div>}

        <div className="ls-table-scroll">
          <table className="ls-table">
            <thead>
              <tr>
                {COLUMNS.map((col) => (
                  <th
                    key={col.key}
                    onClick={() => onSort(col)}
                    className={col.sortable ? 'is-sortable' : ''}
                  >
                    <span>
                      {col.label}
                      {orderby === col.key && (
                        <span className="ls-sort-arrow">{order === 'asc' ? ' ↑' : ' ↓'}</span>
                      )}
                    </span>
                  </th>
                ))}
                <th className="ls-col-actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              {loading && (
                <tr className="ls-row-empty"><td colSpan={COLUMNS.length + 1}>Loading…</td></tr>
              )}
              {!loading && rows.length === 0 && (
                <tr className="ls-row-empty"><td colSpan={COLUMNS.length + 1}>No diamonds found.</td></tr>
              )}
              {!loading && rows.map((row) => (
                <tr key={row.id}>
                  <td className="ls-cell-muted">{row.id}</td>
                  <td>
                    {row.pid && Number(row.pid) !== 0 ? (
                      <a className="ls-link" href={`/wp-admin/post.php?post=${row.pid}&action=edit`} target="_blank" rel="noreferrer">View</a>
                    ) : <span className="ls-cell-muted">—</span>}
                  </td>
                  <td className="ls-cell-strong">{row.sku}</td>
                  <td>
                    {row.shape}
                    {[1, 3].includes(Number(row.shipping)) && <span className="ls-tag ls-tag-shipping" title="Express Shipping">⚡</span>}
                    {[2, 3].includes(Number(row.shipping)) && <span className="ls-tag ls-tag-overnight" title="Overnight">🚀</span>}
                  </td>
                  <td>
                    <span className="ls-price">{row.price}</span>
                    {row.org_price && Number(row.org_price) !== 0 && (
                      <span className="ls-price-original">{row.org_price}</span>
                    )}
                  </td>
                  <td>{row.carat}</td>
                  <td>{row.video ? <a className="ls-link" href={row.video} target="_blank" rel="noreferrer">Video</a> : <span className="ls-cell-muted">—</span>}</td>
                  <td>{row.certificate ? <a className="ls-link" href={row.certificate} target="_blank" rel="noreferrer">View</a> : <span className="ls-cell-muted">—</span>}</td>
                  <td>{row.certificate_type || <span className="ls-cell-muted">—</span>}</td>
                  <td>{row.lot_number}</td>
                  <td>{row.vendor_name || <span className="ls-cell-muted">—</span>}</td>
                  <td>{row.stock_num || <span className="ls-cell-muted">—</span>}</td>
                  <td className="ls-cell-note" title={row.note}>{row.note}</td>
                  <td>
                    {Number(row.sale) === 1
                      ? <span className="ls-pill ls-pill-sale">Sale</span>
                      : <span className="ls-cell-muted">—</span>}
                  </td>
                  <td>
                    <select
                      className={`ls-status-select ${Number(row.status) === 1 ? 'is-out' : 'is-in'}`}
                      value={row.status}
                      disabled={!!pendingStatus[row.id]}
                      onChange={(e) => onStatusChange(row, e.target.value)}
                    >
                      <option value={0}>In Stock</option>
                      <option value={1}>Out Of Stock</option>
                    </select>
                  </td>
                  <td className="ls-col-actions">
                    <a className="ls-action" href="#" onClick={(e) => { e.preventDefault(); onEdit(row.id); }}>Edit</a>
                    <a className="ls-action ls-action-danger" href="#" onClick={(e) => { e.preventDefault(); onDelete(row.id); }}>Delete</a>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <div className="ls-pagination">
          <button className="ls-btn ls-btn-ghost" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>‹ Prev</button>
          <span className="ls-pagination-info">Page {page} of {totalPages}</span>
          <button className="ls-btn ls-btn-ghost" disabled={page >= totalPages} onClick={() => setPage((p) => p + 1)}>Next ›</button>
        </div>
      </div>
    </div>
  );
}
