import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './App.scss'

const mountEl = document.getElementById('ls-cart-root')

if (mountEl) {
  ReactDOM.createRoot(mountEl).render(<App initial={window.LS_CART_INITIAL || {}} />)
}
