import React from 'react'

export default function OptionCard({ option, imageSrc, currency, selected, onClick }) {
  return (
    <div
      className={'ls-ref-option' + (selected ? ' selected' : '')}
     
      data-price={option.price}
      data-name={option.label}
    >
      <div className="ls-ref-option-img">
        <img
          src={imageSrc}
          alt={option.label}
          onError={(e) => {
            e.currentTarget.style.visibility = 'hidden'
          }}
        />
      </div>
      <div className="ls-ref-option-name">
        {option.label}
        {option.note && (
          <i
            className="fa-solid fa-question question_icon ms-1"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            data-bs-original-title={option.note}
            title={option.note}
          ></i>
        )}
      </div>
      <button type="button" className="ls-ref-add-btn"  onClick={onClick}>
        {selected ? `Remove -${currency}${Number(option.price).toFixed(0)}` : `Add +${currency}${Number(option.price).toFixed(0)}`}
      </button>
    </div>
  )
}
