import React from 'react'
import SaleProductPanel from './SaleProductPanel.jsx'

// Sale rows from ls_inventory — creates missing products via add_product_to_wc()
export default function WhiteSaleProduct() {
  return <SaleProductPanel type="white-diamond" label="White Diamond" table="ls_inventory" />
}
