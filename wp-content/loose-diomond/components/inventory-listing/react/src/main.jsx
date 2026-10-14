import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'

const mountEl = document.getElementById('inventory-listing-root')

if (mountEl) {
  ReactDOM.createRoot(mountEl).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  )
}
