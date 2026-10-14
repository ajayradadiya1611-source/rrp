import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './App.scss'

const mountEl = document.getElementById('ring-builder-breadcrumb-root')

if (mountEl) {
  const globalRb = (typeof window !== 'undefined' && window.LS_RING_BUILDER) ? window.LS_RING_BUILDER : null
  const rbData = globalRb
    ? { setting: globalRb.setting || {}, diamond: globalRb.diamond || {}, started_with: globalRb.started_with || '' }
    : { setting: {}, diamond: {}, started_with: '' }
  const urls = globalRb ? (globalRb.urls || {}) : {}
  const currentPage = globalRb ? (globalRb.current_page || 'setting') : 'setting'

  ReactDOM.createRoot(mountEl).render(
    <React.StrictMode>
      <App currentPage={currentPage} rbData={rbData} urls={urls} />
    </React.StrictMode>
  )
}
