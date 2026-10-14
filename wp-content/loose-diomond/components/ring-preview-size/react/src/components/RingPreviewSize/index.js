const SIZES = [0.2, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0];

const SCALE_MAP = {
  0.2: 0.2,
  1.0: 0.25,
  1.5: 0.28,
  2.0: 0.31,
  2.5: 0.35,
  3.0: 0.38,
  3.5: 0.43,
  4.0: 0.47,
  4.5: 0.53,
  5.0: 0.55,
};

export default function initRingPreviewSize() {
  document.querySelectorAll('.dcsg-container').forEach((container) => {
    // Already built (e.g. re-init after a variation change) — skip.
    if (container.dataset.dcsgBuilt) return;

    const bgSrc = container.getAttribute('data-bg') || container.dataset.bg;
    const ringSrc = container.getAttribute('data-ring') || container.dataset.ring;
    const crt = parseFloat(container.getAttribute('crt')) || 1.0;

    if (!ringSrc) return;

    // ── Build the inner markup (background, ring, controls) ──────────
    const background = document.createElement('img');
    background.className = 'dcsg-background';
    background.src = bgSrc;

    const ring = document.createElement('img');
    ring.className = 'dcsg-ring';
    ring.id = 'dcsg-ring';
    ring.src = ringSrc;

    const controls = document.createElement('div');
    controls.className = 'dcsg-controls';
    controls.id = 'dcsg-controls';

    container.innerHTML = '';
    container.appendChild(background);
    container.appendChild(ring);
    container.appendChild(controls);

    // ── Carat size buttons ─────────────────────────────────────────
    // If carat is 6 or more, only show the size 5 button
    // If carat is 6 or more, only show the size 5 button
    const sizesToRender = crt >= 6 ? [5.0] : SIZES;
    controls.classList.toggle('dcsg-controls--single', crt >= 6);
    sizesToRender.forEach((size) => {
      const btn = document.createElement('button');
      btn.className = 'dcsg-btn';
      btn.innerText = size.toFixed(1);
      btn.type = 'button';
      btn.dataset.size = size;
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        controls.querySelectorAll('.dcsg-btn').forEach((b) => b.classList.remove('dcsg-active'));
        btn.classList.add('dcsg-active');
        ring.style.transform = `translate(-50%, -50%) scale(${SCALE_MAP[size]})`;
      });
      controls.appendChild(btn);
    });

    // find nearest size to the diamond's actual carat (within the rendered set)
    let nearestIndex = 0;
    let nearestDiff = Infinity;
    sizesToRender.forEach((size, i) => {
      const diff = Math.abs(size - crt);
      if (diff < nearestDiff) {
        nearestDiff = diff;
        nearestIndex = i;
      }
    });

    if (controls.children[nearestIndex]) controls.children[nearestIndex].click();

    container.dataset.dcsgBuilt = 'true';
  });
}
