import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App.jsx';
import './App.scss';

const rootEl = document.getElementById('ls-inventory-admin-root');

if (rootEl) {
  const type = rootEl.dataset.type === 'color' ? 'color' : 'white';
  createRoot(rootEl).render(<App type={type} />);
}
