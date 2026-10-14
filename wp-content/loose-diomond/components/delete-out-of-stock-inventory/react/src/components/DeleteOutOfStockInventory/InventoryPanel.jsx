import React, { useState } from 'react'
import { fetchOutOfStock, deleteInventoryRecord } from '../../api'

// `fetchFn` defaults to the out-of-stock listing; pass fetchInStockNoInventory
// (or any GET-list function with the same { type } -> { items } shape) to
// reuse this panel for a different listing. `loadLabel`, `emptyHint`,
// `deleteVerb` and `csvPrefix` let each mode use its own wording without
// forking the component.
export default function InventoryPanel({
  type,
  label,
  table,
  fetchFn = fetchOutOfStock,
  loadLabel = 'Load Out of Stock Items',
  emptyHint = 'No items loaded yet, or none out of stock.',
  deleteVerb = 'out of stock',
  csvPrefix = 'out-of-stock',
}) {
  const [items, setItems] = useState([])
  const [selected, setSelected] = useState(() => new Set())
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [busyId, setBusyId] = useState(null)
  const [message, setMessage] = useState('')

  // Sequential bulk-delete progress: one request at a time, no single
  // giant server call — avoids a server timeout on large selections.
  const [bulkProgress, setBulkProgress] = useState(null) // { current, total, currentId } | null
  const [bulkCancelled, setBulkCancelled] = useState(false)

  const loadItems = async () => {
    setLoading(true)
    setError('')
    setMessage('')
    try {
      const data = await fetchFn(type)
      setItems(data.items || [])
      setSelected(new Set())
    } catch (err) {
      setError(err?.response?.data?.message || `Failed to load ${deleteVerb} inventory.`)
    } finally {
      setLoading(false)
    }
  }

  const toggleSelected = (productId) => {
    setSelected((prev) => {
      const next = new Set(prev)
      if (next.has(productId)) {
        next.delete(productId)
      } else {
        next.add(productId)
      }
      return next
    })
  }

  const toggleSelectAll = () => {
    setSelected((prev) =>
      prev.size === items.length ? new Set() : new Set(items.map((i) => i.product_id))
    )
  }

  const handleDelete = async (productId) => {
    const confirmed = window.confirm(
      `Permanently delete product #${productId} from ${table} (if a record exists) and WooCommerce?\n\nThis is a full delete — not moved to Trash. This cannot be undone.`
    )
    if (!confirmed) return

    setBusyId(productId)
    setError('')
    setMessage('')
    try {
      const res = await deleteInventoryRecord(type, productId)
      setMessage(
        `Deleted product #${res.product_id} — ${res.inventory_rows_deleted} row(s) removed from ${res.table}, product deleted: ${res.product_deleted ? 'yes' : 'no'}.`
      )
      setItems((prev) => prev.filter((i) => i.product_id !== productId))
      setSelected((prev) => {
        const next = new Set(prev)
        next.delete(productId)
        return next
      })
    } catch (err) {
      setError(err?.response?.data?.message || 'Delete failed.')
    } finally {
      setBusyId(null)
    }
  }

  const handleBulkDelete = async () => {
    if (selected.size === 0) return

    const ids = Array.from(selected)
    const confirmed = window.confirm(
      `Permanently delete ${ids.length} product(s) from ${table} (where a record exists) and WooCommerce?\n\nDeletes are sent one at a time. This is a full delete — not moved to Trash. This cannot be undone.`
    )
    if (!confirmed) return

    setError('')
    setMessage('')
    setBulkCancelled(false)
    setBulkProgress({ current: 0, total: ids.length, currentId: ids[0] })

    const succeeded = []
    const failed = []
    let wasCancelled = false

    for (let i = 0; i < ids.length; i++) {
      // Check cancellation flag between requests (set via the Cancel button)
      let cancelledNow = false
      setBulkCancelled((c) => {
        cancelledNow = c
        return c
      })
      if (cancelledNow) {
        wasCancelled = true
        break
      }

      const productId = ids[i]
      setBulkProgress({ current: i, total: ids.length, currentId: productId })

      try {
        const res = await deleteInventoryRecord(type, productId)
        succeeded.push(productId)
        setItems((prev) => prev.filter((it) => it.product_id !== productId))
        setSelected((prev) => {
          const next = new Set(prev)
          next.delete(productId)
          return next
        })
      } catch (err) {
        failed.push(productId)
      }

      setBulkProgress({ current: i + 1, total: ids.length, currentId: productId })
    }

    setMessage(
      `Bulk delete finished: ${succeeded.length} succeeded${failed.length ? `, ${failed.length} failed` : ''}${wasCancelled ? ' (cancelled)' : ''}.`
    )
    if (failed.length) {
      setError(`Failed product IDs: ${failed.join(', ')}`)
    }
    setBulkProgress(null)
    setBulkCancelled(false)
  }

  const cancelBulkDelete = () => {
    setBulkCancelled(true)
  }

  const csvEscape = (value) => {
    const str = value === null || value === undefined ? '' : String(value)
    if (/[",\n]/.test(str)) {
      return `"${str.replace(/"/g, '""')}"`
    }
    return str
  }

  const exportCsv = (rows, filenameSuffix) => {
    if (!rows.length) return
    const headers = ['product_id', 'sku', 'shape', 'carat', 'stock_status']
    const lines = [
      headers.join(','),
      ...rows.map((item) =>
        headers.map((h) => csvEscape(item[h])).join(',')
      ),
    ]
    const csvContent = lines.join('\n')
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    const stamp = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-')
    a.href = url
    a.download = `${table}-${csvPrefix}-${filenameSuffix}-${stamp}.csv`
    document.body.appendChild(a)
    a.click()
    a.remove()
    URL.revokeObjectURL(url)
  }

  const handleExportAll = () => exportCsv(items, 'all')
  const handleExportSelected = () =>
    exportCsv(items.filter((i) => selected.has(i.product_id)), 'selected')

  const allSelected = items.length > 0 && selected.size === items.length
  const isBulkRunning = bulkProgress !== null
  const bulkPercent = isBulkRunning
    ? Math.round((bulkProgress.current / bulkProgress.total) * 100)
    : 0

  return (
    <section className="panel">
      <div className="panel-header">
        <h2>{label}</h2>
        <span className="table-name">{table}</span>
      </div>

      <div className="panel-actions">
        <button className="btn btn-load" onClick={loadItems} disabled={loading || isBulkRunning}>
          {loading ? 'Loading…' : loadLabel}
        </button>

        <button
          className="btn btn-export"
          onClick={handleExportAll}
          disabled={items.length === 0 || isBulkRunning}
          title="Export all loaded items as CSV"
        >
          Export CSV (All)
        </button>

        <button
          className="btn btn-export"
          onClick={handleExportSelected}
          disabled={selected.size === 0 || isBulkRunning}
          title="Export only selected items as CSV"
        >
          Export CSV (Selected)
        </button>

        <button
          className="btn btn-bulk-delete"
          onClick={handleBulkDelete}
          disabled={isBulkRunning || selected.size === 0}
        >
          {isBulkRunning ? 'Deleting…' : `Delete Selected (${selected.size})`}
        </button>

        {isBulkRunning && (
          <button className="btn btn-cancel" onClick={cancelBulkDelete} disabled={bulkCancelled}>
            {bulkCancelled ? 'Cancelling…' : 'Cancel'}
          </button>
        )}
      </div>

      {isBulkRunning && (
        <div className="bulk-progress">
          <div className="bulk-progress-label">
            Deleting {bulkProgress.current} / {bulkProgress.total}
            {bulkProgress.currentId ? ` — product #${bulkProgress.currentId}` : ''}
          </div>
          <div className="progress-bar">
            <div className="progress-bar-fill" style={{ width: `${bulkPercent}%` }} />
          </div>
        </div>
      )}

      {error && <p className="error">{error}</p>}
      {message && <p className="success">{message}</p>}

      {items.length > 0 && (
        <table className="items-table">
          <thead>
            <tr>
              <th>
                <input
                  type="checkbox"
                  checked={allSelected}
                  onChange={toggleSelectAll}
                  disabled={isBulkRunning}
                  aria-label="Select all"
                />
              </th>
              <th>Product ID</th>
              <th>SKU</th>
              <th>Shape</th>
              <th>Carat</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {items.map((item) => (
              <tr
                key={item.product_id}
                className={
                  isBulkRunning && bulkProgress.currentId === item.product_id ? 'row-active' : ''
                }
              >
                <td>
                  <input
                    type="checkbox"
                    checked={selected.has(item.product_id)}
                    onChange={() => toggleSelected(item.product_id)}
                    disabled={isBulkRunning}
                  />
                </td>
                <td>{item.product_id}</td>
                <td>{item.sku}</td>
                <td>{item.shape}</td>
                <td>{item.carat}</td>
                <td>
                  {item.stock_status === 'no-inventory-record' ? (
                    <span className="badge badge-warn" title="No matching row in the inventory table">
                      no inventory record
                    </span>
                  ) : item.stock_status === 'in-stock-no-inventory' ? (
                    <span className="badge badge-warn" title="In stock in WooCommerce, but no matching row in the inventory table">
                      in stock, no inventory record
                    </span>
                  ) : item.stock_status === 'missing-product' ? (
                    <span className="badge badge-warn" title="WooCommerce product no longer exists">
                      missing product
                    </span>
                  ) : (
                    item.stock_status
                  )}
                </td>
                <td>
                  <button
                    className="btn-icon btn-icon-delete"
                    disabled={busyId === item.product_id || isBulkRunning}
                    onClick={() => handleDelete(item.product_id)}
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

      {!loading && items.length === 0 && (
        <p className="empty-hint">{emptyHint}</p>
      )}
    </section>
  )
}
