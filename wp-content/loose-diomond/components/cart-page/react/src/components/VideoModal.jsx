import React, { useEffect, useRef } from 'react'
import Modal from './Modal.jsx'

export default function VideoModal({ src, onClose }) {
  const ref = useRef(null)

  useEffect(() => {
    const v = ref.current
    if (v) v.play().catch(() => {})
    return () => v && v.pause()
  }, [])

  return (
    <Modal onClose={onClose} dialogClass="modal-lg" contentStyle={{ borderRadius: 4, background: '#000' }}>
      <button
        type="button"
        className="btn-close btn-close-white position-absolute top-0 end-0 m-2"
        style={{ zIndex: 10 }}
        aria-label="Close"
        onClick={onClose}
      />
      <div className="modal-body p-0">
        <div className="ratio ratio-16x9">
          <video ref={ref} src={src} controls playsInline style={{ width: '100%', height: '100%' }} />
        </div>
      </div>
    </Modal>
  )
}
