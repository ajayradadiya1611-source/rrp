const { root, nonce } = window.lsInventoryAdmin || {};

async function request(path, options = {}) {
  const res = await fetch(`${root}${path}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': nonce,
      ...(options.headers || {}),
    },
    credentials: 'same-origin',
  });

  const data = await res.json().catch(() => null);

  if (!res.ok) {
    const message = (data && (data.message || data.msg)) || `Request failed (${res.status})`;
    throw new Error(message);
  }

  return data;
}

export function fetchMeta(type) {
  return request(`inventory-admin/${type}/meta`);
}

export function fetchList(type, params) {
  const qs = new URLSearchParams(
    Object.entries(params).filter(([, v]) => v !== undefined && v !== '')
  ).toString();
  return request(`inventory-admin/${type}?${qs}`);
}

export function fetchOne(type, id) {
  return request(`inventory-admin/${type}/${id}`);
}

export function createDiamond(type, payload) {
  return request(`inventory-admin/${type}`, {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export function updateDiamond(type, id, payload) {
  return request(`inventory-admin/${type}/${id}`, {
    method: 'PUT',
    body: JSON.stringify(payload),
  });
}

export function deleteDiamond(type, id) {
  return request(`inventory-admin/${type}/${id}`, { method: 'DELETE' });
}

export function changeStatus(type, id, status) {
  return request(`inventory-admin/${type}/${id}/status`, {
    method: 'POST',
    body: JSON.stringify({ status }),
  });
}
