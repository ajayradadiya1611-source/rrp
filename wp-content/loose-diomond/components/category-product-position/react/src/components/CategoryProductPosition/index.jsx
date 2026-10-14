import React, { useEffect, useMemo, useRef, useState } from 'react'
import { fetchCategories, fetchCategoryProducts, savePositions, resetPositions } from '../../api'

/** Flatten product_cat terms into a tree-ordered list with depth. */
function buildTree(terms) {
  const byParent = {}
  terms.forEach((t) => {
    ;(byParent[t.parent] = byParent[t.parent] || []).push(t)
  })
  const out = []
  const walk = (parent, depth) => {
    ;(byParent[parent] || []).forEach((t) => {
      out.push({ ...t, depth })
      walk(t.id, depth + 1)
    })
  }
  walk(0, 0)
  // orphans (parent missing)
  const seen = new Set(out.map((t) => t.id))
  terms.forEach((t) => !seen.has(t.id) && out.push({ ...t, depth: 0 }))
  return out
}

export default function CategoryProductPosition() {
  const [categories, setCategories] = useState([])
  const [termId, setTermId] = useState('')
  const [items, setItems] = useState([])
  const [savedCount, setSavedCount] = useState(0)
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [dirty, setDirty] = useState(false)
  const [error, setError] = useState('')
  const [message, setMessage] = useState('')
  const [search, setSearch] = useState('')
  const dragIndex = useRef(null)
  const [overIndex, setOverIndex] = useState(null)

  const tree = useMemo(() => buildTree(categories), [categories])

  useEffect(() => {
    fetchCategories()
      .then((d) => setCategories(d.items || []))
      .catch((e) => setError(e?.response?.data?.message || 'Failed to load categories.'))
  }, [])

  // Warn before leaving with unsaved changes
  useEffect(() => {
    const h = (e) => {
      if (dirty) {
        e.preventDefault()
        e.returnValue = ''
      }
    }
    window.addEventListener('beforeunload', h)
    return () => window.removeEventListener('beforeunload', h)
  }, [dirty])

  const loadProducts = async (id) => {
    setLoading(true)
    setError('')
    setMessage('')
    try {
      const d = await fetchCategoryProducts(id)
      setItems(d.items || [])
      setSavedCount(d.saved_count || 0)
      setDirty(false)
    } catch (e) {
      setError(e?.response?.data?.message || 'Failed to load products.')
      setItems([])
    } finally {
      setLoading(false)
    }
  }

  const onCategoryChange = (id) => {
    if (dirty && !window.confirm('You have unsaved changes. Switch category anyway?')) return
    setTermId(id)
    setSearch('')
    if (id) loadProducts(id)
    else setItems([])
  }

  const move = (from, to) => {
    if (from === to || from == null || to == null) return
    setItems((prev) => {
      const next = [...prev]
      const [it] = next.splice(from, 1)
      next.splice(Math.max(0, Math.min(to, next.length)), 0, it)
      return next
    })
    setDirty(true)
    setMessage('')
  }

  const setPosition = (from, value) => {
    const pos = parseInt(value, 10)
    if (!Number.isFinite(pos) || pos < 1) return
    move(from, Math.min(pos, items.length) - 1)
  }

  const sortBySales = () => {
    setItems((prev) => [...prev].sort((a, b) => b.total_sales - a.total_sales))
    setDirty(true)
  }

  const handleSave = async () => {
    setSaving(true)
    setError('')
    try {
      const ids = items.map((i) => i.id)
      const d = await savePositions(termId, ids)
      setSavedCount(d.count)
      setItems((prev) => prev.map((i) => ({ ...i, saved: true })))
      setDirty(false)
      setMessage(`Saved position of ${d.count} products.`)
      setCategories((prev) => prev.map((c) => (c.id === Number(termId) ? { ...c, has_order: true } : c)))
    } catch (e) {
      setError(e?.response?.data?.message || 'Save failed.')
    } finally {
      setSaving(false)
    }
  }

  const handleReset = async () => {
    if (!window.confirm('Remove the saved position for this category? Best Seller will fall back to WooCommerce sales count.')) return
    setSaving(true)
    try {
      await resetPositions(termId)
      setCategories((prev) => prev.map((c) => (c.id === Number(termId) ? { ...c, has_order: false } : c)))
      await loadProducts(termId)
      setMessage('Position reset.')
    } catch (e) {
      setError(e?.response?.data?.message || 'Reset failed.')
    } finally {
      setSaving(false)
    }
  }

  const q = search.trim().toLowerCase()
  const matches = (i) =>
    !q || String(i.id).includes(q) || (i.sku || '').toLowerCase().includes(q) || (i.title || '').toLowerCase().includes(q)

  return (
    <div className="ppos-root">
      <h1>Category Product Position</h1>
      <p className="ppos-subtitle">
        Choose a category, drag products into the order you want, then click <strong>Save</strong>. This order is
        used by <strong>Sort by : Best Seller</strong> on the website. Products without a saved position appear after,
        sorted by sales.
      </p>

      <div className="ppos-toolbar">
        <select value={termId} onChange={(e) => onCategoryChange(e.target.value)} disabled={saving}>
          <option value="">— Select category —</option>
          {tree.map((c) => (
            <option key={c.id} value={c.id}>
              {'— '.repeat(c.depth)}
              {c.name} ({c.count}){c.has_order ? ' ✓' : ''}
            </option>
          ))}
        </select>

        {termId && (
          <>
            <input
              type="search"
              placeholder="Highlight by ID / SKU / title"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
            <button className="btn btn-secondary" onClick={sortBySales} disabled={loading || saving || !items.length}>
              Auto-sort by sales
            </button>
            <button className="btn btn-secondary" onClick={() => loadProducts(termId)} disabled={loading || saving}>
              {dirty ? 'Discard changes' : 'Reload'}
            </button>
            {savedCount > 0 && (
              <button className="btn btn-danger" onClick={handleReset} disabled={saving}>
                Reset
              </button>
            )}
            <button className="btn btn-primary" onClick={handleSave} disabled={saving || loading || !items.length}>
              {saving ? 'Saving…' : dirty ? 'Save *' : 'Save'}
            </button>
          </>
        )}
      </div>

      {error && <div className="ppos-notice ppos-error">{error}</div>}
      {message && <div className="ppos-notice ppos-success">{message}</div>}

      {termId && !loading && (
        <div className="ppos-meta">
          {items.length} products · {savedCount ? `${savedCount} with saved position` : 'no saved position yet'}
          {dirty && <span className="ppos-dirty"> · unsaved changes</span>}
        </div>
      )}

      {loading && <div className="ppos-loading">Loading products…</div>}

      {!loading && termId && items.length === 0 && <div className="ppos-empty">No published products in this category.</div>}

      {!loading && items.length > 0 && (
        <ul className="ppos-list">
          {items.map((item, index) => (
            <li
              key={item.id}
              className={[
                'ppos-row',
                overIndex === index ? 'is-over' : '',
                q && !matches(item) ? 'is-dim' : '',
                q && matches(item) ? 'is-match' : '',
              ].join(' ')}
              draggable={!saving}
              onDragStart={(e) => {
                dragIndex.current = index
                e.dataTransfer.effectAllowed = 'move'
              }}
              onDragOver={(e) => {
                e.preventDefault()
                if (overIndex !== index) setOverIndex(index)
              }}
              onDragLeave={() => setOverIndex(null)}
              onDrop={(e) => {
                e.preventDefault()
                move(dragIndex.current, index)
                dragIndex.current = null
                setOverIndex(null)
              }}
              onDragEnd={() => {
                dragIndex.current = null
                setOverIndex(null)
              }}
            >
              <span className="ppos-handle" title="Drag to reorder">⋮⋮</span>
              <input
                key={`${item.id}-${index}`}
                className="ppos-pos"
                type="number"
                min="1"
                max={items.length}
                defaultValue={index + 1}
                title="Type a position and press Enter"
                onKeyDown={(e) => e.key === 'Enter' && setPosition(index, e.currentTarget.value)}
                onBlur={(e) => Number(e.currentTarget.value) !== index + 1 && setPosition(index, e.currentTarget.value)}
              />
              {item.image ? <img src={item.image} alt="" /> : <span className="ppos-noimg" />}
              <div className="ppos-info">
                <a href={item.edit_url} target="_blank" rel="noreferrer">
                  {item.title}
                </a>
                <small>
                  #{item.id}
                  {item.sku && ` · SKU ${item.sku}`}
                  {item.price !== '' && ` · $${item.price}`}
                  {` · ${item.total_sales} sold`}
                  {item.stock_status !== 'instock' && <span className="ppos-oos"> · {item.stock_status}</span>}
                  {!item.saved && <span className="ppos-new"> · new</span>}
                </small>
              </div>
              <div className="ppos-actions">
                <button title="Move to top" onClick={() => move(index, 0)} disabled={index === 0}>⤒</button>
                <button title="Up" onClick={() => move(index, index - 1)} disabled={index === 0}>↑</button>
                <button title="Down" onClick={() => move(index, index + 1)} disabled={index === items.length - 1}>↓</button>
                <button title="Move to bottom" onClick={() => move(index, items.length - 1)} disabled={index === items.length - 1}>⤓</button>
              </div>
            </li>
          ))}
        </ul>
      )}
    </div>
  )
}
