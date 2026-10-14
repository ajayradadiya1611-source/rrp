import React, { useEffect } from 'react'
import { createPortal } from 'react-dom'

/**
 * Bootstrap-styled modal without Bootstrap JS (uses the theme's
 * existing .modal CSS). Closes on backdrop click / Escape.
 */
export default function Modal({ onClose, dialogClass = '', contentStyle, children, labelledBy }) {
  useEffect(() => {
    const onKey = (e) => e.key === 'Escape' && onClose()
    document.addEventListener('keydown', onKey)
    document.body.classList.add('modal-open')
    return () => {
      document.removeEventListener('keydown', onKey)
      document.body.classList.remove('modal-open')
    }
  }, [onClose])

  return createPortal(
    <>
      <div
        className="modal fade show"
        style={{ display: 'block' }}
        tabIndex="-1"
        role="dialog"
        aria-modal="true"
        aria-labelledby={labelledBy}
        onMouseDown={(e) => e.target === e.currentTarget && onClose()}
      >
        <div className={`modal-dialog modal-dialog-centered ${dialogClass}`}>
          <div className="modal-content" style={contentStyle}>
            {children}
          </div>
        </div>
      </div>
      <div className="modal-backdrop fade show" />
    </>,
    document.body
  )
}
