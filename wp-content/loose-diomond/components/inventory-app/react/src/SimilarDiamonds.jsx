// ─────────────────────────────────────────────────────────────
// SimilarDiamonds.jsx
// Replaces the old server-rendered "Similar diamonds" PHP loop on
// the single diamond detail page (diamond-detail.php). Renders the
// same shared grid components InventoryResults.jsx's grid view uses
// (InventoryGridCard + GridLoadingBlock/GridEmptyBlock) instead of
// its own separate card markup, so there's one card implementation
// for the whole app, not two.
//
// Mounted at #ls-similar-diamonds-root by index.jsx, which reads the
// current diamond's shape/sku/is-colored off the root element's
// data-* attrs. `isColored` decides which backend endpoint and which
// label/image logic to use — a colored diamond's "similar" results
// need to come from ls_inventory_c (via /inventory-colored), not the
// white-diamond table.
// ─────────────────────────────────────────────────────────────

import React, { useEffect, useState } from 'react';
import axios from './api.js';
import InventoryGridCard from './components/InventoryGridCard';
import { GridLoadingBlock } from './components/InventoryStateBlocks';
import { cutLabels, colorLabels, clarityLabels } from './lsFunction';
import { useWishlist, templateUrl } from './components/inventoryHelpers';

const LIMIT = 4;

// Same fancy-color list App2.jsx defines locally — kept in sync by hand
// since it's small and static (not currently exported from a shared file).
const COLOR_OPTIONS = [
  { key: 'yellow', key_val: '1', label: 'Yellow' },
  { key: 'pink', key_val: '2', label: 'Pink' },
  { key: 'purple', key_val: '3', label: 'Purple' },
  { key: 'red', key_val: '4', label: 'Red' },
  { key: 'blue', key_val: '5', label: 'Blue' },
  { key: 'green', key_val: '6', label: 'Green' },
  { key: 'orange', key_val: '7', label: 'Orange' },
  { key: 'brown', key_val: '8', label: 'Brown' },
  { key: 'black', key_val: '9', label: 'Black' },
  { key: 'gray', key_val: '10', label: 'Gray' },
];

// Carat has no "grade" to match exactly like cut/color/clarity do, so
// "similar" carat means "nearby" — build a tolerance window around the
// current diamond's carat instead of an exact match. Wider stones get a
// wider window since buyers comparing a 3ct diamond aren't expecting
// options within 0.01ct of it the way a 0.3ct shopper might.
function getCaratRange(carat) {
  const c = parseFloat(carat);
  if (!c || Number.isNaN(c)) return null;

  let tolerance;
  if (c < 1) tolerance = 0.15;
  else if (c < 2) tolerance = 0.25;
  else if (c < 3) tolerance = 0.4;
  else tolerance = 0.6;

  const min = Math.max(0.01, c - tolerance);
  const max = c + tolerance;
  return `${min.toFixed(2)},${max.toFixed(2)}`;
}

// Generic "nearby" range builder for the finer proportion fields
// (table %, depth %, L/W ratio) — same idea as getCaratRange, just a
// flat tolerance since these don't scale with stone size the way carat does.
function getToleranceRange(value, tolerance, decimals = 2) {
  const v = parseFloat(value);
  if (!v || Number.isNaN(v)) return null;

  const min = Math.max(0, v - tolerance);
  const max = v + tolerance;
  return `${min.toFixed(decimals)},${max.toFixed(decimals)}`;
}

export default function SimilarDiamonds({
  shape,
  excludeSku,
  isColored = false,
  isSale = false,
  cut,
  color,
  clarity,
  carat,
  certificateType,
  table,
  depth,
  lwratio,
}) {
  const { API_BASE_URL = '' } = window.LD_THEME || {};
  const [items, setItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const { isInWishlist, toggleWishlist } = useWishlist();

  useEffect(() => {
    if (!shape) {
      setIsLoading(false);
      return;
    }

    const source = axios.CancelToken.source();
    setIsLoading(true);

    const endpoint = `${API_BASE_URL}/${isColored ? 'inventory-colored' : 'inventory-filter'}`;

    // Base payload shared by every tier: shape + the 4Cs (Cut/Color/
    // Clarity exact grade match, Carat as a nearby window) plus
    // Certificate type.
    const basePayload = {
      shape: [shape],
      exclude_sku: excludeSku || '',
      ls_per_page: LIMIT,
      ls_start: 1,
    };

    if (cut) basePayload.cut_range = `${cut},${cut}`;
    if (clarity) basePayload.clarity_range = `${clarity},${clarity}`;
    if (color) {
      // Fancy-colored diamonds match color as an exact category (there's
      // no "near this color" for a colored stone), white diamonds match
      // it as a one-grade range like cut/clarity above. NOTE: the two
      // endpoints use different param names here — inventory-colored
      // reads `colored`, inventory-filter reads `color` (not
      // `color_range`, unlike cut/clarity/carat/table/depth/lwratio).
      if (isColored) {
        basePayload.colored = color;
      } else {
        basePayload.color = `${color},${color}`;
      }
    }
    if (certificateType) basePayload.certificate_type = [certificateType];

    // On a sale-tag page, restrict "similar" results to other diamonds
    // also flagged on sale — same `is_sale` param the inventory-filter /
    // inventory-colored endpoints already read (see ls-inventory-api.php).
    if (isSale) basePayload.is_sale = 1;

    // Tier 1 (most specific): 4Cs + Certificate + Table % + Depth % +
    // L/W Ratio, all within a close tolerance — as close to "the same
    // diamond" as the current inventory can offer.
    const tier1Payload = { ...basePayload };
    const caratRange = getCaratRange(carat);
    if (caratRange) tier1Payload.carat_range = caratRange;

    const tableRange = getToleranceRange(table, 2);
    if (tableRange) tier1Payload.table_range = tableRange;

    const depthRange = getToleranceRange(depth, 2);
    if (depthRange) tier1Payload.depth_range = depthRange;

    const lwratioRange = getToleranceRange(lwratio, 0.05);
    if (lwratioRange) tier1Payload.lwratio_range = lwratioRange;

    // Tier 2 (fallback): drop Carat/Table/Depth/L-W Ratio, keep just
    // 4Cs + Certificate — used when nothing matches the tighter tier 1
    // criteria.
    const tier2Payload = { ...basePayload };

    // Tier 3 (last resort): shape only, so the section still shows
    // something rather than going empty. Still honors is_sale, if set,
    // so a sale-tag page's fallback doesn't suddenly surface non-sale items.
    const tier3Payload = {
      shape: [shape],
      exclude_sku: excludeSku || '',
      ls_per_page: LIMIT,
      ls_start: 1,
    };
    if (isSale) tier3Payload.is_sale = 1;

    const fetchSimilar = (payload) =>
      axios.post(endpoint, payload, {
        headers: { 'Content-Type': 'application/json' },
        withCredentials: true,
        cancelToken: source.token,
      });

    fetchSimilar(tier1Payload)
      .then((response) => {
        const tier1Results = response.data?.data || [];
        if (tier1Results.length > 0) {
          setItems(tier1Results);
          return null;
        }

        return fetchSimilar(tier2Payload).then((tier2Response) => {
          const tier2Results = tier2Response.data?.data || [];
          if (tier2Results.length > 0) {
            setItems(tier2Results);
            return null;
          }

          return fetchSimilar(tier3Payload).then((tier3Response) => {
            setItems(tier3Response.data?.data || []);
          });
        });
      })
      .catch((error) => {
        if (!(axios.isCancel && axios.isCancel(error))) {
          console.error('Failed to load similar diamonds', error);
        }
      })
      .finally(() => setIsLoading(false));

    return () => source.cancel();
  }, [shape, excludeSku, isColored, isSale, cut, color, clarity, carat, certificateType, table, depth, lwratio, API_BASE_URL]);

  if (isLoading) {
    return <GridLoadingBlock showFact={false} />;
  }

  if (items.length === 0) return null;

  return (
    <div className="oc-inventory-grid mb-3">
      <div className="row g-1 g-md-2">
        {items.map((item) => {
          const cutLabel = cutLabels[item.cut - 1] || item.cut;
          const clarityLabel = clarityLabels[item.clarity - 1] || item.clarity;
          const shapeName = item.shape_title
            ?.toLowerCase()
            ?.trim()
            ?.replace(/\s+/g, '_') || '';
          const displayPrice = '$' + Number(item.price).toFixed(0);

          let colorLabel;
          let shapeImageSrc;
          if (isColored) {
            colorLabel = COLOR_OPTIONS.find(c => c.key_val === String(item.color))?.label || item.color;
            const colorName = colorLabel.toLowerCase();
            shapeImageSrc = shapeName ? `${templateUrl}/assets/image/ls-inventory-color/diamond/${shapeName}/${colorName}.png` : '';
          } else {
            colorLabel = colorLabels[item.color - 1] || item.color;
            shapeImageSrc = shapeName ? `${templateUrl}/assets/image/ls-inventory/shape-diamond/png/${shapeName}.png` : '';
          }
          const shapeImageFallback = `${templateUrl}/assets/image/ls-inventory/shape-diamond/svg/${shapeName}.svg`;

          return (
            <div className="col-6 col-md-4 col-lg-3" key={item.id}>
              <InventoryGridCard
                item={item}
                cutLabel={cutLabel}
                colorLabel={colorLabel}
                clarityLabel={clarityLabel}
                displayPrice={displayPrice}
                imageSrc={shapeImageSrc}
                imageFallbackSrc={shapeImageFallback}
                href={`/diamond/${item.sku}/`}
                isInWishlist={isInWishlist}
                toggleWishlist={toggleWishlist}
              />
            </div>
          );
        })}
      </div>
    </div>
  );
}