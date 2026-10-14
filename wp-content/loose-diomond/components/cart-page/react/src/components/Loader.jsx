import React, { useEffect } from 'react'

export default function Loader({ show, src }) {
  useEffect(() => {
    document.body.classList.toggle('overflow-hidden', !!show)
  }, [show])

  if (!show) return null
  return (
    <div
      id="ls-cart-loader"
      className="position-fixed top-0 start-0 w-100 vh-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75"
      style={{ zIndex: 1055 }}
    >
      <img src={src} width="40" height="40" alt="Loading..." />
    </div>
  )
}
