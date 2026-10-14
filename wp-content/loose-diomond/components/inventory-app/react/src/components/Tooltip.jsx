import React, { useState, useRef, useLayoutEffect } from "react";

export default function Tooltip({ text, children, position = "top" }) {
  const [show, setShow] = useState(false);
  const [offsetX, setOffsetX] = useState(0);
  const tooltipRef = useRef(null);

  useLayoutEffect(() => {
    if (!show) {
      setOffsetX(0);
      return;
    }

    const tooltip = tooltipRef.current;
    if (!tooltip) return;

    requestAnimationFrame(() => {
      const rect = tooltip.getBoundingClientRect();
      const viewportWidth = window.innerWidth;

      let adjust = 0;

      if (rect.left < 10) {
        adjust = 10 - rect.left;
      } else if (rect.right > viewportWidth - 10) {
        adjust = viewportWidth - 10 - rect.right;
      }

      setOffsetX(adjust);
    });
  }, [show, text, position]);

  const getBasePosition = () => {
    switch (position) {
      case "bottom":
        return {
          top: "100%",
          left: "50%",
          marginTop: "8px",
        };
      case "left":
        return {
          right: "100%",
          top: "50%",
          transform: "translateY(-50%)",
          marginRight: "8px",
        };
      case "right":
        return {
          left: "100%",
          top: "50%",
          transform: "translateY(-50%)",
          marginLeft: "8px",
        };
      default: // top
        return {
          bottom: "100%",
          left: "50%",
          marginBottom: "8px",
        };
    }
  };

  const base = getBasePosition();

  const finalTransform =
    position === "top" || position === "bottom"
      ? `translateX(calc(-50% + ${offsetX}px))`
      : base.transform;

  return (
    <div
      style={{ position: "relative", display: "inline-block" }}
      onMouseEnter={() => setShow(true)}
      onMouseLeave={() => setShow(false)}
      className="oc_tooltip"
    >
      {children}

      <div
        ref={tooltipRef}
        style={{
          position: "absolute",
          backgroundColor: "#fff",
          color: "#000",
          padding: "6px 10px",
          borderRadius: "4px",
          fontSize: "13px",
          lineHeight: "1.4",
          maxWidth: "min(260px, 90vw)", // mobile safe
          width: "max-content",
          whiteSpace: "normal",
          wordBreak: "break-word",
          zIndex: 999,
          opacity: show ? 0.95 : 0,
          visibility: show ? "visible" : "hidden",
          transition: "opacity 0.15s ease-in-out",
          boxShadow: "0 0.5rem 1rem rgba(0,0,0,.15)",
          pointerEvents: "none",
          fontWeight: 400 ,
          ...base,
          transform: finalTransform,
        }}
      >
        {text}
      </div>
    </div>
  );
}