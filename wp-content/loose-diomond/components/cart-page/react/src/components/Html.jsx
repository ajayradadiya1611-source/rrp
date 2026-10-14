import React, { memo } from 'react'

/**
 * Renders server-built HTML (already escaped / kses'd in PHP: prices,
 * thumbnails, hook output). Memoised so an unchanged string never
 * resets the DOM (important for widgets mounted inside it).
 */
function Html({ html, as: Tag = 'div', ...rest }) {
  if (!html) return null
  return <Tag {...rest} dangerouslySetInnerHTML={{ __html: html }} />
}

export default memo(Html)
