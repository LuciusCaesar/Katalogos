---
paths:
  - resources/js/impact-diagram.js
---

# Js

## No Alpine template loops inside SVG — render dynamic SVG via x-html
Alpine <template x-for>/<template x-if> DO NOT WORK inside <svg>: the HTML parser creates the template in the SVG namespace, which has no .content property, so Alpine throws "undefined is not an object (evaluating templateEl.content.children)" and nothing renders (observed on the impact diagram edges).
Pattern used in the impact diagram: static SVG (e.g. <marker> defs) written directly in the Blade view; dynamic parts built as a markup string in an Alpine getter (arcsMarkup) and injected with <g x-html="arcsMarkup"></g> (innerHTML works on SVG elements in modern browsers). Escape user data before interpolating it into the string.
