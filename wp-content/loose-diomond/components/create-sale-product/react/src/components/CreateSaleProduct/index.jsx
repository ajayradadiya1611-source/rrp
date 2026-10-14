import React from 'react'
import ColorSaleProduct from './ColorSaleProduct.jsx'
import WhiteSaleProduct from './WhiteSaleProduct.jsx'

export default function CreateSaleProduct() {
  return (
    <div className="app-root">
      <h1>Create Sale Product</h1>
      <p className="app-subtitle">
        Lists every inventory row flagged as "on sale" — for each, shows whether a
        WooCommerce product already exists (by pid) and lets you create the missing one.
        Creation reuses the same logic as the storefront Add To Cart flow.
      </p>
      <div className="panels">
        <ColorSaleProduct />
        <WhiteSaleProduct />
      </div>
    </div>
  )
}

export { ColorSaleProduct, WhiteSaleProduct }
