import React from 'react'
import InventoryPanel from './InventoryPanel.jsx'

// Deletes from ls_inventory WHERE pid = product_id (fully delete, not trash)
export default function WhiteDiamondDelete() {
  return <InventoryPanel type="white-diamond" label="White Diamond" table="ls_inventory" />
}
