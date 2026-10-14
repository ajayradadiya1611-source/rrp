import React, { useState } from 'react'
import { fetchSaleInventory, createSaleProduct } from '../../api'

export default function SaleProductPanel({ type, label, table }) {
  const [items, setItems] = useState([])
  const [selected, setSelected] = useState(() => new Set())
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [busyId, setBusyId] = useState(null)
  const [message, setMessage] = useState('')

  // Sequential bulk-create progress: one request at a time, no single
  // giant server call — avoids a server timeout on large selections.
  const [bulkProgress, setBulkProgress] = useState(null) // { current, total, currentId } | null
  const [bulkCancelled, setBulkCancelled] = useState(false)

  const loadItems = async () => {
    setLoading(true)
    setError('')
    setMessage('')
    try {
      const data = await fetchSaleInventory(type)
      setItems(data.items || [])
      setSelected(new Set())
    } catch (err) {
      setError(err?.response?.data?.message || 'Failed to load sale inventory.')
    } finally {
      setLoading(false)
    }
  }

  const notCreatedIds = items.filter((i) => i.pid_status !== 'created').map((i) => i.inventory_id)

  const toggleSelected = (inventoryId) => {
    setSelected((prev) => {
      const next = new Set(prev)
      if (next.has(inventoryId)) {
        next.delete(inventoryId)
      } else {
        next.add(inventoryId)
      }
      return next
    })
  }

  const toggleSelectAll = () => {
    setSelected((prev) =>
      prev.size === notCreatedIds.length ? new Set() : new Set(notCreatedIds)
    )
  }

  const applyResultToItems = (res) => {
    setItems((prev) =>
      prev.map((i) =>
        i.inventory_id === res.inventory_id
          ? { ...i, product_id: res.product_id, pid_status: res.product_id ? 'created' : i.pid_status }
          : i
      )
    )
  }

  const handleCreate = async (inventoryId) => {
    setBusyId(inventoryId)
    setError('')
    setMessage('')
    try {
      const res = await createSaleProduct(type, inventoryId)
      applyResultToItems(res)
      setSelected((prev) => {
        const next = new Set(prev)
        next.delete(inventoryId)
        return next
      })
      setMessage(
        res.already_existed
          ? `Row #${res.inventory_id} already had product #${res.product_id}.`
          : `Created product #${res.product_id} for row #${res.inventory_id}.`
      )
    } catch (err) {
      setError(err?.response?.data?.message || 'Create failed.')
    } finally {
      setBusyId(null)
    }
  }

  const handleBulkCreate = async () => {
    if (selected.size === 0) return

    const ids = Array.from(selected)
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

      const inventoryId = ids[i]
      setBulkProgress({ current: i, total: ids.length, currentId: inventoryId })

      try {
        const res = await createSaleProduct(type, inventoryId)
        applyResultToItems(res)
        succeeded.push(inventoryId)
        setSelected((prev) => {
          const next = new Set(prev)
          next.delete(inventoryId)
          return next
        })
      } catch (err) {
        failed.push(inventoryId)
      }

      setBulkProgress({ current: i + 1, total: ids.length, currentId: inventoryId })
    }

    setMessage(
      `Bulk create finished: ${succeeded.length} succeeded${failed.length ? `, ${failed.length} failed` : ''}${wasCancelled ? ' (cancelled)' : ''}.`
    )
    if (failed.length) {
      setError(`Failed inventory IDs: ${failed.join(', ')}`)
    }
    setBulkProgress(null)
    setBulkCancelled(false)
  }

  const cancelBulkCreate = () => {
    setBulkCancelled(true)
  }

  const allSelected = notCreatedIds.length > 0 && selected.size === notCreatedIds.length
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
          {loading ? 'Loading…' : 'Load Sale Items'}
        </button>

        <button
          className="btn btn-bulk-create"
          onClick={handleBulkCreate}
          disabled={isBulkRunning || selected.size === 0}
        >
          {isBulkRunning ? 'Creating…' : `Create Selected (${selected.size})`}
        </button>

        {isBulkRunning && (
          <button className="btn btn-cancel" onClick={cancelBulkCreate} disabled={bulkCancelled}>
            {bulkCancelled ? 'Cancelling…' : 'Cancel'}
          </button>
        )}
      </div>

      {isBulkRunning && (
        <div className="bulk-progress">
          <div className="bulk-progress-label">
            Creating {bulkProgress.current} / {bulkProgress.total}
            {bulkProgress.currentId ? ` — row #${bulkProgress.currentId}` : ''}
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
                  disabled={isBulkRunning || notCreatedIds.length === 0}
                  aria-label="Select all not-created"
                />
              </th>
              <th>Inventory ID</th>
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
                key={item.inventory_id}
                className={
                  isBulkRunning && bulkProgress.currentId === item.inventory_id ? 'row-active' : ''
                }
              >
                <td>
                  {item.pid_status !== 'created' && (
                    <input
                      type="checkbox"
                      checked={selected.has(item.inventory_id)}
                      onChange={() => toggleSelected(item.inventory_id)}
                      disabled={isBulkRunning}
                    />
                  )}
                </td>
                <td>{item.inventory_id}</td>
                <td>{item.product_id ?? '—'}</td>
                <td>{item.sku}</td>
                <td>{item.shape}</td>
                <td>{item.carat}</td>
                <td>
                  {item.pid_status === 'created' ? (
                    <span className="badge badge-ok">created</span>
                  ) : (
                    <span className="badge badge-warn" title="No live WooCommerce product yet">
                      not created
                    </span>
                  )}
                </td>
                <td>
                  {item.pid_status !== 'created' && (
                    <button
                      className="btn-icon btn-icon-create"
                      disabled={busyId === item.inventory_id || isBulkRunning}
                      onClick={() => handleCreate(item.inventory_id)}
                      title="Create product"
                      aria-label={`Create product for row ${item.inventory_id}`}
                    >
                      {busyId === item.inventory_id ? '…' : '＋'}
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}

      {!loading && items.length === 0 && (
        <p className="empty-hint">No items loaded yet, or none marked on sale.</p>
      )}
    </section>
  )
}
