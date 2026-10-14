/** "font-family:Arial; font-size:13px" → { fontFamily: 'Arial', fontSize: '13px' } */
export default function styleString(str = '') {
  const out = {}
  str.split(';').forEach((rule) => {
    const i = rule.indexOf(':')
    if (i < 1) return
    const prop = rule.slice(0, i).trim().replace(/-([a-z])/g, (_, c) => c.toUpperCase())
    const val = rule.slice(i + 1).trim()
    if (prop && val) out[prop] = val
  })
  return out
}
