---
paths:
  - resources/js/impact-diagram.js
---

# Js

## No Alpine template loops inside SVG — render dynamic SVG via x-html
Alpine <template x-for>/<template x-if> DO NOT WORK inside <svg>: the HTML parser creates the template in the SVG namespace, which has no .content property, so Alpine throws "undefined is not an object (evaluating templateEl.content.children)" and nothing renders (observed on the impact diagram edges).
Pattern used in the impact diagram: static SVG (e.g. <marker> defs) written directly in the Blade view; dynamic parts built as a markup string in an Alpine getter (arcsMarkup) and injected with <g x-html="arcsMarkup"></g> (innerHTML works on SVG elements in modern browsers). Escape user data before interpolating it into the string.

## Impact diagram: zoom gestures and controls
The impact diagram canvas supports zoom: ctrl/cmd+wheel (and trackpad pinch, which browsers report as ctrl+wheel) zooms toward the cursor (zoomAt); plain wheel/trackpad pans. This is the standard canvas-app gesture set (Figma/Miro/Excalidraw) — keep it. @wheel.prevent on the area intercepts the browser page zoom while over the canvas.
Zoom range 0.25–3 (minZoom/maxZoom state). The wrapper transform is translate(pan) scale(zoom) with transform-origin 0 0; screen = pan + zoom * canvas, so pointerPos divides by zoom to get canvas coordinates — any new pointer math must go through pointerPos.
Zoom controls: floating − / % / + buttons bottom-right of the viewport; the percentage (click) resets zoom AND pan to the initial view (resetView).
