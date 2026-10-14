import React from 'react';
import Modal from './Modal';

export default function ConfirmAddModal({
  isOpen,
  onClose,
  item,
  onConfirm,
  title = 'Quality control check',
}) {

  const templateUrl = (typeof window !== 'undefined' && window.themeData && window.themeData.templateUrl) ? window.themeData.templateUrl : '';

  const certificateIcon =
  templateUrl
    ? `${templateUrl}/aj_custom/inventory/certificate.png`
    : '/aj_custom/inventory/certificate.png';

  return (
    <Modal isOpen={!!isOpen} onClose={onClose} size="md">
      <div className="p-4">

        {/* Header */}
        <div className=" justify-content-between align-items-center mb-3">
          <h5 className="mb-1 fw-semibold">{title}</h5>
          <div className="mb-1">
              Please note and confirm the following important information about this item before adding it to your cart.
            </div>
        </div>

        {/* Body */}
        <div className="d-flex gap-3 align-items-start">
          
          {/* Icon */}
          <div style={{ fontSize: 22 }}>
            <img src={certificateIcon} alt="Certificate" className="quality_img" />
          </div>

          {/* Text */}
          <div>
            

            <div className="text-muted small oc-qc-check">
              <p className="mb-0">This diamond does not come with a certificate. </p>
              <p  className="mb-0"> It is listed as a non-certified stone and is sold without any certification. Please confirm if you wish to proceed with this purchase.</p>
            </div>

           
          </div>
        </div>

        {/* Footer buttons */}
        <div className="d-flex justify-content-end gap-2 mt-4">

          <button
            className="btn btn-light oc-white"
            onClick={onClose}
          >
            Don't add
          </button>

          <button
            className="btn btn-dark oc-dark"
            onClick={async () => {
              if (onConfirm) await onConfirm(item);
            }}
          >
            Confirm and add to cart
          </button>

        </div>
      </div>
    </Modal>
  );
}