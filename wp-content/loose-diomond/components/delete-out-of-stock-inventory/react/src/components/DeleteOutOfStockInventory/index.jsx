import React, { useState } from 'react'
import ColorDiamondDelete from './ColorDiamondDelete.jsx'
import WhiteDiamondDelete from './WhiteDiamondDelete.jsx'
import ColorDiamondInStockNoInventory from './ColorDiamondInStockNoInventory.jsx'
import WhiteDiamondInStockNoInventory from './WhiteDiamondInStockNoInventory.jsx'

const TABS = [
  { id: 'out-of-stock', label: 'Out of Stock' },
  { id: 'in-stock-no-inventory', label: 'In Stock — No Inventory Record' },
]

export default function DeleteOutOfStockInventory() {
  const [activeTab, setActiveTab] = useState('out-of-stock')

  return (
    <div className="app-root">
      <h1>Delete Out of Stock Inventory</h1>
      <p className="app-subtitle">
        Each panel permanently deletes the matching diamond inventory record (by product ID, if
        one exists) and force-deletes the WooCommerce product. Not moved to Trash.
      </p>

      <div className="tabs" role="tablist">
        {TABS.map((tab) => (
          <button
            key={tab.id}
            role="tab"
            aria-selected={activeTab === tab.id}
            className={`tab-button${activeTab === tab.id ? ' tab-button-active' : ''}`}
            onClick={() => setActiveTab(tab.id)}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {activeTab === 'out-of-stock' && (
        <>
          <p className="app-subtitle">
            Products tagged white/color diamond with stock_status = outofstock for at least 45
            days. Includes products with no matching inventory row at all.
          </p>
          <div className="panels">
            <ColorDiamondDelete />
            <WhiteDiamondDelete />
          </div>
        </>
      )}

      {activeTab === 'in-stock-no-inventory' && (
        <>
          <p className="app-subtitle">
            Products tagged white/color diamond that ARE in stock, unchanged for at least 45
            days, but have no matching row in the inventory table at all.
          </p>
          <div className="panels">
            <ColorDiamondInStockNoInventory />
            <WhiteDiamondInStockNoInventory />
          </div>
        </>
      )}
    </div>
  )
}

export {
  ColorDiamondDelete,
  WhiteDiamondDelete,
  ColorDiamondInStockNoInventory,
  WhiteDiamondInStockNoInventory,
}
