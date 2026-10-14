/**
 * Extra / fancy diamond shapes shown inside the "+N" modal on the Shape
 * filter. These are less common shapes that don't appear in the primary
 * shape grid but are still filterable via ?shape=<value> on the listing
 * page (e.g. /lab-diamonds/?shape=trilliant).
 *
 * img is resolved as `${templateUrl}${img}` — same convention as the main
 * `shapes` array in App.jsx/App2.jsx.
 */
const RAW_MORE_SHAPES = [
  { value: 'trilliant', label: 'Trilliant' },
  { value: 'heptagonal', label: 'Heptagonal' },
  { value: 'pentagonal', label: 'Pentagonal' },
  { value: 'shield', label: 'Shield' },
  { value: 'horsehead', label: 'Horsehead' },
  { value: 'capsule', label: 'Capsule' },
  { value: 'octagonal', label: 'Octagonal' },
  { value: 'star', label: 'Star' },
  { value: 'square', label: 'Square' },
  { value: 'lozenge', label: 'Lozenge' },
  { value: 'briolette', label: 'Briolette' },
  { value: 'calf_head', label: 'Calf Head' },
  { value: 'flanders', label: 'Flanders' },
  { value: 'carre', label: 'Carre' },
  { value: 'bullet', label: 'Bullet' },
  { value: 'tapered_bullet', label: 'Tapered Bullet' },
  { value: 'portuguese', label: 'Portuguese' },
  { value: 'ashoka', label: 'Ashoka' },
  { value: 'cadillac', label: 'Cadillac' },
  { value: 'lily', label: 'Lily' },
  { value: 'cadi_step', label: 'Cadi Step' },
  { value: 'butterfly', label: 'Butterfly' },
  { value: 'buddha', label: 'Buddha' },
  { value: 'moval', label: 'Moval' },
  { value: 'flower', label: 'Flower' },
  { value: 'criss_cut', label: 'Criss Cut' },
];

export const MORE_SHAPES = RAW_MORE_SHAPES.map((s) => ({
  id: `more-shape-${s.value}`,
  value: s.value,
  label: s.label,
  alt: `${s.value}_shape`,
  img: `/aj_custom/diamond/shape/${s.value}.webp`,
}));

export default MORE_SHAPES;
