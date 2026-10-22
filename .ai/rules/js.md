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

## Impact diagram snowflake: expansion-driven parenting (rebuildLayout)
The snowflake positions are NOT computed once from the full graph: rebuildLayout() recomputes them on every extension/retraction (toggleExpand/selectRelated call it), so newly revealed blocks/nodes always appear next to the element they were extended FROM.
revealParent(id): the most recently expanded visible neighbour of id (scan this.expanded in reverse), or the root for depth<=1 elements. Node-level tree: visible elements hang under their revealParent. Block-level: each visible type hangs under the type of the revealer of its most recently revealed element (highest expanded.indexOf(parent) recency) — NOT any element, so a block whose type also relates to the root still relocates next to the block it was just extended from.
Manual positions (blockPos, manualNodePos) always override the defaults (defaultBlockPos, defaultNodePos); defaults reflow on extension, manual ones stay.
Never reintroduce positions computed from the static metamodel/type-adjacency graph at init — that is exactly the bug where new blocks appeared far from their related box.

## Impact diagram: layout tree MUST be acyclic (expanded elements parent only under earlier expansions)
Regression fixed: with naive "most recent expanded neighbour" parenting, two mutually-related EXPANDED elements can parent each other (e.g. extend a rule to reveal issues, then extend an issue to reveal another rule). The cycle makes both unreachable from the root in the radial tree, so they fall back to position (0,0) and stack on top of the center block.
Guarantees in revealParent(): (1) an EXPANDED element only ever hangs under an expansion with a strictly SMALLER expanded-index (skip i >= ownIndex with continue, do not break — earlier expansions must still be considered); parent pointers strictly descend in expansion order, so node-level cycles are impossible. (2) At the type level, typeParent chains are validated after construction: any type whose parent chain does not reach the root type (mutual type-level cycles are still possible) is re-attached to the root type.
Verify any parenting change with a simulation: instantiate window.impactDiagram(payload) in node, run expansions, and assert all block/node positions are pairwise distinct and every revealParent chain reaches rootId (see /tmp/diagram-sim.js pattern from the fix session).

## Impact diagram layout: force-balanced relaxation (no ring radii)
The block/node layout is a deterministic FORCE RELAXATION (relax() in rebuildLayout), NOT radial ring radii — ring-based placement measured distances from the canvas center, which made deep chains (objective → initiative → asset → rule → issue…) produce multi-thousand-pixel arrows.
Model: bodies = visible blocks (blocks view) or elements (graph view); springs along visible links pull pairs to a rest length (BLOCK_REST 440 / NODE_REST 220); pairwise repulsion with RECTANGLE-PROJECTION separation (half-extents a/b, projection = a·|ux| + b·|uy| — a circle model under-separates tall rectangles on diagonals); light gravity to the origin; pinned = root + manual drags. Freshly revealed bodies seed AT their revealer position, previously placed bodies keep their positions (continuity), then everything relaxes together.
relax() is followed by resolveOverlaps(): a positional pass pushing overlapping pairs apart along their center axis until rectangle projections no longer intersect — pure forces can leave small overlaps; this pass guarantees none (30 passes max).
Validate layout changes with simulations in node (instantiate window.impactDiagram with a synthetic payload, expand progressively, assert zero blockRect intersections, bounded link lengths, acyclic revealParent chains) — see /tmp/diagram-sim.js and /tmp/diagram-sim2.js patterns from the fix sessions.
