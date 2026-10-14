import React, { useMemo, useRef, useState } from 'react'
import { fetchZeroPriceProducts, deleteZeroPriceProduct, adminUrl } from '../../api'

const TYPE_FILTERS = [
  { value: 'all', label: 'All' },
  { value: 'white-diamond', label: 'White Diamond' },
  { value: 'color-diamond', label: 'Color Diamond' },
  { value: 'other', label: 'Other' },
]

export default function ZeroPriceProducts() {
  const [items, setItems] = useState([])
  const [loaded, setLoaded] = useState(false)
  const [selected, setSelected] = useState(() => new Set())
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [message, setMessage] = useState('')
  const [busyId, setBusyId] = useState(null)
  const [typeFilter, setTypeFilter] = useState('all')
  const [search, setSearch] = useState('')

  // Sequential bulk delete — one request at a time to avoid server timeouts.
  const [bulkProgress, setBulkProgress] = useState(null) // { current, total, currentId }
  const cancelRef = useRef(false)
  const [cancelling, setCancelling] = useState(false)

  const loadItems = async () => {
    setLoading(true)
    setError('')
    setMessage('')
    try {
      const data = await fetchZeroPriceProducts()
      setItems(data.items || [])
      setSelected(new Set())
      setLoaded(true)
    } catch (err) {
      setError(err?.response?.data?.message || 'Failed to load zero price products.')
    } finally {
      setLoading(false)
    }
  }

  const visible = useMemo(() => {
    const q = search.trim().toLowerCase()
    return items.filter((i) => {
      if (typeFilter === 'other' && i.type) return false
      if (typeFilter !== 'all' && typeFilter !== 'other' && i.type !== typeFilter) return false
      if (!q) return true
      return (
        String(i.product_id).includes(q) ||
        (i.sku || '').toLowerCase().includes(q) ||
        (i.title || '').toLowerCase().includes(q)
      )
    })
  }, [items, typeFilter, search])

  const visibleIds = visible.map((i) => i.product_id)
  const allSelected = visibleIds.length > 0 && visibleIds.every((id) => selected.has(id))
  const isBulkRunning = bulkProgress !== null

  const toggleSelected = (id) => {
    setSelected((prev) => {
      const next = new Set(prev)
      next.has(id) ? next.delete(id) : next.add(id)
      return next
    })
  }

  const toggleSelectAll = () => {
    setSelected((prev) => {
      const next = new Set(prev)
      if (allSelected) visibleIds.forEach((id) => next.delete(id))
      else visibleIds.forEach((id) => next.add(id))
      return next
    })
  }

  const removeItem = (id) => {
    setItems((prev) => prev.filter((i) => i.product_id !== id))
    setSelected((prev) => {
      const next = new Set(prev)
      next.delete(id)
      return next
    })
  }

  const handleDelete = async (item) => {
    if (!window.confirm(`Permanently delete product #${item.product_id} "${item.title}"? This cannot be undone.`)) {
      return
    }
    setBusyId(item.product_id)
    setError('')
    setMessage('')
    try {
      await deleteZeroPriceProduct(item.product_id)
      removeItem(item.product_id)
      setMessage(`Deleted product #${item.product_id}.`)
    } catch (err) {
      setError(err?.response?.data?.message || 'Delete failed.')
    } finally {
      setBusyId(null)
    }
  }

  const handleBulkDelete = async () => {
    const ids = Array.from(selected)
    if (ids.length === 0) return
    if (!window.confirm(`Permanently delete ${ids.length} product(s)? This cannot be undone.`)) {
      return
    }

    setError('')
    setMessage('')
    cancelRef.current = false
    setCancelling(false)

    const succeeded = []
    const failed = []
    let wasCancelled = false

    for (let i = 0; i < ids.length; i++) {
      if (cancelRef.current) {
        wasCancelled = true
        break
      }
      const id = ids[i]
      setBulkProgress({ current: i, total: ids.length, currentId: id })
      try {
        await deleteZeroPriceProduct(id)
        removeItem(id)
        succeeded.push(id)
      } catch (err) {
        failed.push(`#${id} (${err?.response?.data?.message || 'error'})`)
      }
      setBulkProgress({ current: i + 1, total: ids.length, currentId: id })
    }

    setMessage(
      `Bulk delete finished: ${succeeded.length} deleted${failed.length ? `, ${failed.length} failed` : ''}${wasCancelled ? ' (cancelled)' : ''}.`
    )
    if (failed.length) setError(`Failed: ${failed.join(', ')}`)
    setBulkProgress(null)
    setCancelling(false)
  }

  const cancelBulk = () => {
    cancelRef.current = true
    setCancelling(true)
  }

  const bulkPercent = isBulkRunning ? Math.round((bulkProgress.current / bulkProgress.total) * 100) : 0

  return (
    <div className="app-root">
      <h1>Zero Price Products</h1>
      <p className="app-subtitle">
        Lists every WooCommerce product whose price is 0 or empty. Delete is permanent
        (skips Trash). The price is re-checked on the server before each delete, so a product
        that has been given a price since loading will be skipped.
      </p>

      <section className="panel">
        <div className="panel-actions">
          <button className="btn btn-load" onClick={loadItems} disabled={loading || isBulkRunning}>
            {loading ? 'Loading…' : loaded ? 'Reload' : 'Load Zero Price Products'}
          </button>

          <button
            className="btn btn-bulk-delete"
            onClick={handleBulkDelete}
            disabled={isBulkRunning || selected.size === 0}
          >
            {isBulkRunning ? 'Deleting…' : `Delete Selected (${selected.size})`}
          </button>

          {isBulkRunning && (
            <button className="btn-cancel" onClick={cancelBulk} disabled={cancelling}>
              {cancelling ? 'Cancelling…' : 'Cancel'}
            </button>
          )}
        </div>

        {loaded && (
          <div className="filters">
            <select value={typeFilter} onChange={(e) => setTypeFilter(e.target.value)} disabled={isBulkRunning}>
              {TYPE_FILTERS.map((f) => (
                <option key={f.value} value={f.value}>
                  {f.label}
                </option>
              ))}
            </select>
            <input
              type="search"
              placeholder="Search ID, SKU or title…"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
            <span className="count">
              Showing {visible.length} of {items.length}
            </span>
          </div>
        )}

        {isBulkRunning && (
          <div className="bulk-progress">
            <div className="bulk-progress-label">
              Deleting {bulkProgress.current} / {bulkProgress.total} — product #{bulkProgress.currentId}
            </div>
            <div className="progress-bar">
              <div className="progress-bar-fill" style={{ width: `${bulkPercent}%` }} />
            </div>
          </div>
        )}

        {error && <p className="error">{error}</p>}
        {message && <p className="success">{message}</p>}

        {visible.length > 0 && (
          <table className="items-table">
            <thead>
              <tr>
                <th>
                  <input
                    type="checkbox"
                    checked={allSelected}
                    onChange={toggleSelectAll}
                    disabled={isBulkRunning}
                    aria-label="Select all visible"
                  />
                </th>
                <th>Product ID</th>
                <th>Title</th>
                <th>SKU</th>
                <th>Type</th>
                <th>Price</th>
                <th>Status</th>
                <th>Stock</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {visible.map((item) => (
                <tr
                  key={item.product_id}
                  className={isBulkRunning && bulkProgress.currentId === item.product_id ? 'row-active' : ''}
                >
                  <td>
                    <input
                      type="checkbox"
                      checked={selected.has(item.product_id)}
                      onChange={() => toggleSelected(item.product_id)}
                      disabled={isBulkRunning}
                    />
                  </td>
                  <td>
                    <a href={`${adminUrl}post.php?post=${item.product_id}&action=edit`} target="_blank" rel="noreferrer">
                      {item.product_id}
                    </a>
                  </td>
                  <td className="title-cell">{item.title}</td>
                  <td>{item.sku || '—'}</td>
                  <td>{item.type ? item.type.replace('-diamond', '') : '—'}</td>
                  <td>
                    {item.price === null || item.price === '' ? (
                      <span className="badge badge-warn">empty</span>
                    ) : (
                      <span className="badge badge-danger">{item.price}</span>
                    )}
                  </td>
                  <td>{item.status}</td>
                  <td>{item.stock_status || '—'}</td>
                  <td>
                    <button
                      className="btn-icon btn-icon-delete"
                      disabled={busyId === item.product_id || isBulkRunning}
                      onClick={() => handleDelete(item)}
                      title="Delete permanently"
                      aria-label={`Delete product ${item.product_id}`}
                    >
                      {busyId === item.product_id ? '…' : '🗑'}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}

        {!loading && visible.length === 0 && (
          <p className="empty-hint">
            {loaded ? 'No zero price products found.' : 'Click "Load Zero Price Products" to start.'}
          </p>
        )}
      </section>
    </div>
  )
}
