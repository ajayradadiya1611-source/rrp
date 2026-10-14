import React from 'react'
import InventoryPanel from './InventoryPanel.jsx'

// Deletes from ls_inventory_c WHERE pid = product_id (fully delete, not trash)
export default function ColorDiamondDelete() {
  return <InventoryPanel type="color-diamond" label="Color Diamond" table="ls_inventory_c" />
}
