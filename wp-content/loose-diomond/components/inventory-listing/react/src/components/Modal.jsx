import React, { useEffect } from "react";

export default function Modal({
  isOpen,
  onClose,
  children,
  size = "md",
  centered = true,
  closeOnOverlay = true,
  fullscreen = false,
  bodyClass = "ls-modal-open"
}) {
  // Prevent body scroll when modal open + add a class to <body> so page-level
  // CSS can react to a modal being open (e.g. hide floating widgets).
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = "hidden";
      if (bodyClass) document.body.classList.add(bodyClass);
    } else {
      document.body.style.overflow = "auto";
      if (bodyClass) document.body.classList.remove(bodyClass);
    }

    return () => {
      document.body.style.overflow = "auto";
      if (bodyClass) document.body.classList.remove(bodyClass);
    };
  }, [isOpen, bodyClass]);

  if (!isOpen) return null;

  const sizeMap = {
    sm: "300px",
    md: "500px",
    lg: "800px",
    xl: "1000px"
  };

  return (
    <div
      style={overlayStyle}
      onClick={closeOnOverlay ? onClose : undefined}
    >
      <div
        style={{
          ...modalStyle,
          ...(fullscreen
            ? fullscreenStyle
            : {
                maxWidth: sizeMap[size],
                margin: centered ? "auto" : "100px auto",
                overflowY: "auto",
                maxHeight: "90vh"
              })
        }}
        onClick={(e) => e.stopPropagation()}
      >
        {!fullscreen && (
          <button style={closeStyle} onClick={onClose}>
            <i class="fa-solid fa-xmark fs-4"></i>
          </button>
        )}

        {children}
      </div>
    </div>
  );
}

const overlayStyle = {
  position: "fixed",
  inset: 0,
  background: "rgba(0,0,0,0.5)",
  display: "flex",
  justifyContent: "center",
  alignItems: "center",
  zIndex: 9999999999999,
  animation: "fadeIn 0.2s ease"
};

const modalStyle = {
  background: "#fff",
  borderRadius: "8px",
  width: "90%",
  position: "relative",
  boxShadow: "0 10px 30px rgba(0,0,0,0.2)",
  animation: "slideUp 0.2s ease"
};

const fullscreenStyle = {
  width: "100%",
  height: "100%",
  maxWidth: "100%",
  borderRadius: "0",
  margin: "0",
  display: "flex",
  flexDirection: "column",
  overflowY: "auto",
};

const closeStyle = {
  position: "absolute",
  top: "4px",
  right: "4px",
  border: "none",
  background: "transparent",
  fontSize: "18px",
  cursor: "pointer",
  zIndex: 1
};

const closeStyleFullscreen = {
  position: "absolute",
  top: "16px",
  right: "16px",
  border: "none",
  background: "transparent",
  fontSize: "16px",
  color: "#0B131A",
  cursor: "pointer",
  zIndex: 1,
  width: "28px",
  height: "28px",
  display: "flex",
  alignItems: "center",
  justifyContent: "center"
};