import React from 'react'
import InventoryPanel from './InventoryPanel.jsx'
import { fetchInStockNoInventory } from '../../api'

// Lists in-stock white-diamond products with NO row in ls_inventory.
// Deletes go through the same /delete-inventory(-bulk) endpoints as the
// out-of-stock panel — these products never had a row, so the DB-delete
// step is just a no-op and only the WooCommerce product is removed.
export default function WhiteDiamondInStockNoInventory() {
  return (
    <InventoryPanel
      type="white-diamond"
      label="White Diamond"
      table="ls_inventory"
      fetchFn={fetchInStockNoInventory}
      loadLabel="Load In-Stock Items (No Inventory Record)"
      emptyHint="No items loaded yet, or none in stock without an inventory record."
      deleteVerb="in-stock, no-inventory-record"
      csvPrefix="in-stock-no-inventory"
    />
  )
}
