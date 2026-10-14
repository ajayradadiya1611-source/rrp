import React from 'react';
import { createRoot } from 'react-dom/client';
import InventoryApp from './App.jsx';
import InventoryApp2 from './App2.jsx';
import SimilarDiamonds from './SimilarDiamonds.jsx';

// InventoryApp3 (product grid) and InventoryApp4 (ring builder
// breadcrumb) moved out of this bundle into their own standalone
// modules — see components/inventory-listing/ and
// components/ring-builder-breadcrumb/ at the theme root. Their mount
// points (#inventory-listing-root, #ring-builder-breadcrumb-root) are
// now rendered by lgd_render_inventory_listing() and
// lgd_render_ring_builder_breadcrumb() respectively, not from here.

const rootElement = document.getElementById('ls-inventory-root');
if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<InventoryApp />);
}

const rootElement2 = document.getElementById('ls-inventory-root2');
if (rootElement2) {
  const root = createRoot(rootElement2);
  root.render(<InventoryApp2 />);
}

const rootElementSimilar = document.getElementById('ls-similar-diamonds-root');
if (rootElementSimilar) {
  const root = createRoot(rootElementSimilar);
  const shape = rootElementSimilar.getAttribute('data-shape') || '';
  const excludeSku = rootElementSimilar.getAttribute('data-exclude-sku') || '';
  const isColored = rootElementSimilar.getAttribute('data-colored') === '1';
  const isSale = rootElementSimilar.getAttribute('data-is-sale') === '1';  
  const cut = rootElementSimilar.getAttribute('data-cut') || '';
  const color = rootElementSimilar.getAttribute('data-color') || '';
  const clarity = rootElementSimilar.getAttribute('data-clarity') || '';
  const carat = rootElementSimilar.getAttribute('data-carat') || '';
  const certificateType = rootElementSimilar.getAttribute('data-certificate-type') || '';
  const table = rootElementSimilar.getAttribute('data-table') || '';
  const depth = rootElementSimilar.getAttribute('data-depth') || '';
  const lwratio = rootElementSimilar.getAttribute('data-lwratio') || '';
  root.render(
    <SimilarDiamonds
      shape={shape}
      excludeSku={excludeSku}
      isColored={isColored}
      isSale={isSale}                                                     
      cut={cut}
      color={color}
      clarity={clarity}
      carat={carat}
      certificateType={certificateType}
      table={table}
      depth={depth}
      lwratio={lwratio}
    />
  );
}