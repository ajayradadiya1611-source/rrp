import React, { useState } from 'react'
import Modal from './Modal.jsx'
import styleString from './styleString.js'

const DEFAULT_TEXT = 'I Love You'
const MAX = 15

export default function EngravingModal({ fonts, initialText, initialFont, onClose, onSave }) {
  const [text, setText] = useState(initialText || DEFAULT_TEXT)
  const [font, setFont] = useState(initialFont || fonts[0]?.value || '')
  const preview = text || DEFAULT_TEXT

  const submit = () => {
    const t = text.trim()
    if (!t) {
      alert('Please enter engraving text.')
      return
    }
    onSave(t, font)
  }

  return (
    <Modal onClose={onClose} contentStyle={{ borderRadius: 4 }} labelledBy="engravingModalLabel">
      <div className="modal-header border-0 pb-0">
        <button type="button" className="btn-close" aria-label="Close" onClick={onClose} />
      </div>
      <div className="modal-body px-4 pt-0 pb-4">
        <h5 className="text-center fw-600 mb-3 ls-2" id="engravingModalLabel">ENGRAVING DETAILS</h5>
        <p className="text-center text-muted font_size_12 mb-4">
          If no engraving is selected, your ring will only have the metal purity stamp and the RRP Diamond logo.
        </p>

        <div className="engraving-fonts mb-4">
          {fonts.map((f) => (
            <div className="form-check mb-2" key={f.value}>
              <input
                className="form-check-input engraving-font-radio"
                type="radio"
                name="engraving_font"
                id={`font_${f.value}`}
                value={f.value}
                checked={font === f.value}
                onChange={() => setFont(f.value)}
              />
              <label className="form-check-label" htmlFor={`font_${f.value}`}>
                {f.label} - <span className="engraving-preview" style={styleString(f.style)}>{preview}</span>
              </label>
            </div>
          ))}
        </div>

        <label className="font_size_12 fw-500 mb-1" htmlFor="engraving_text">
          Engraving text (Limit of {MAX} characters)
        </label>
        <input
          type="text"
          id="engraving_text"
          className="form-control mb-4"
          maxLength={MAX}
          placeholder="Enter engraving text"
          value={text}
          onChange={(e) => setText(e.target.value)}
          onKeyDown={(e) => e.key === 'Enter' && submit()}
        />

        <div className="text-end">
          <button type="button" className="btn common_black_btn px-4" onClick={submit}>
            {initialText ? 'Update Engraving' : 'Add Engraving'}
          </button>
        </div>
      </div>
    </Modal>
  )
}
