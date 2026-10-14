import React from 'react'
import InventoryPanel from './InventoryPanel.jsx'
import { fetchInStockNoInventory } from '../../api'

// Lists in-stock color-diamond products with NO row in ls_inventory_c.
// Deletes go through the same /delete-inventory(-bulk) endpoints as the
// out-of-stock panel — these products never had a row, so the DB-delete
// step is just a no-op and only the WooCommerce product is removed.
export default function ColorDiamondInStockNoInventory() {
  return (
    <InventoryPanel
      type="color-diamond"
      label="Color Diamond"
      table="ls_inventory_c"
      fetchFn={fetchInStockNoInventory}
      loadLabel="Load In-Stock Items (No Inventory Record)"
      emptyHint="No items loaded yet, or none in stock without an inventory record."
      deleteVerb="in-stock, no-inventory-record"
      csvPrefix="in-stock-no-inventory"
    />
  )
}
