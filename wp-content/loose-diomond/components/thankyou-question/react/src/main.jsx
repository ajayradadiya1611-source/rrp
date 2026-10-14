import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './App.scss'

const mountEl = document.getElementById('ls-thankyou-question-root')

if (mountEl) {
  ReactDOM.createRoot(mountEl).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  )
}
