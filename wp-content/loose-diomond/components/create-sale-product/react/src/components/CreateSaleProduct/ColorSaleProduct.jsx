import React from 'react'
import SaleProductPanel from './SaleProductPanel.jsx'

// Sale rows from ls_inventory_c — creates missing products via add_product_to_wc_c()
export default function ColorSaleProduct() {
  return <SaleProductPanel type="color-diamond" label="Color Diamond" table="ls_inventory_c" />
}
