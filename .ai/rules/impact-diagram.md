---
paths:
  - 'app/Services/ImpactDiagram/**'
---

# Impact Diagram

## Extending the impact diagram with new entity types
The impact diagram (GET /impact-diagram/{entityType}/{entity}, route web.impact-diagram.show) is registry-driven: ImpactDiagramBuilder::TYPES maps route slugs to diagram type keys, RELATIONS declares the relations to traverse (both directions) with edge orientation/label/hidden flags, SHOW_ROUTES gives the per-type show route, TYPE_ORDER the column order, and attributes() the side-panel characteristics.
To add a new entity type: add a TYPES entry, a RELATIONS entry per relation to traverse (belongsTo results are normalized to single-model arrays), a SHOW_ROUTES entry, add the key to TYPE_ORDER, extend attributes(), then mirror the type (name, plural, colors, Tailwind classes) in resources/js/impact-diagram.js TYPES and RELS, and add tests in tests/Feature/ImpactDiagramTest.php.
Hidden edges (hidden: true) are deployable via the ⚡ badge in the frontend; the frontend shows hidden-type blocks only when deployed.
The frontend is a dependency-free Alpine port of the prototype in .tmp/ (no React/graph library): keep it that way for new features.

## Impact diagram: full entity coverage, progressive display and layered layout
The impact diagram (GET /impact-diagram/{entityType}/{entity}, route web.impact-diagram.show) covers ALL 11 entity types (business-asset, business-rule, data-source, data-quality-check, data-initiative, business-objective, domain, data-issue, root-cause, solution, business-process) — every type is a valid entry point. ImpactDiagramBuilder is registry-driven: TYPES (slug/key/model), RELATIONS (relations to traverse both directions, with fixed edge orientation), SHOW_ROUTES, TYPE_ORDER, attributes(), entityLabel(). To add a type: one entry in each registry + the frontend TYPES/RELS in resources/js/impact-diagram.js + tests.
Payload: { rootId, nodes[{id, type, label, url, depth, attributes}], edges[{from, to, rel, label}] } — NO hidden flag: each node carries its BFS depth from the root; the FRONTEND implements progressive display (initially only depth <= 1, then per-node extension via the ⚡ badge / expanded set) and derives visibility dynamically.
Frontend layout: the blocks view is NOT one column per type anymore — columns are impact LEVELS (BFS over visible edges), with barycenter ordering (3 passes) inside each level to minimize edge crossings; manual drags are kept via manualRank. Non-adjacent-level edges route orthogonally through a bottom lane; free-graph view keeps the same layered defaults as draggable nodes.
Data initiatives are labelled by their "label" column (entityLabel), every other entity by "name". belongsTo relations may be null (skip); BackedEnum attribute values go through attributeValue().

## Impact diagram: type blocks, metamodel block order, progressive display
Blocks view of the impact diagram (GET /impact-diagram/{entityType}/{entity}) shows ONE BLOCK PER ENTITY TYPE (spec F2) — NOT per BFS level. Only types with visible elements are displayed, packed left-to-right in BLOCK_ORDER = process, objective, initiative, domain, asset, source, dq, rule, issue, rootcause, solution — an optimal linear arrangement of the metamodel type graph (cost 21) that keeps related types adjacent to minimize edge crossings. This order exists in BOTH resources/js/impact-diagram.js (BLOCK_ORDER, drives the layout) and ImpactDiagramBuilder::TYPE_ORDER (payload node sort) — keep them in sync.
Links connect BLOCKS, not individual elements (spec F4): one aggregated arc per (from-type, to-type, relation) with a "label · count" text; the fine connectivity is revealed by selection highlighting. Adjacent blocks get Bézier arcs; non-adjacent blocks route orthogonally through a lane below the blocks.
Payload: { rootId, nodes[{id, type, label, url, depth, attributes}], edges[{from, to, rel, label}] } — no hidden flag. Each node carries its BFS depth from the root; the FRONTEND implements progressive display (initially depth <= 1, per-node extension via the ⚡ badge / expanded set), derived dynamically.
Registry-driven backend (ImpactDiagramBuilder: TYPES/RELATIONS/SHOW_ROUTES/TYPE_ORDER/attributes/entityLabel). Data initiatives are labelled by "label", others by "name"; belongsTo relations may be null (skip); BackedEnum attribute values go through attributeValue().

## Impact diagram: floating type blocks in star layout around the root
Blocks view of the impact diagram: blocks are FLOATING containers (draggable as whole nodes), NOT columns. Star layout: the root's type block sits at the CANVAS CENTER containing the root element; every other visible type block floats on a ring — its ring index is the distance from the root's type in the type graph DERIVED FROM THE ACTUAL PAYLOAD EDGES (data-driven, no static metamodel constant), its angle deterministic (members sorted by BLOCK_ORDER, rings staggered by half a step). Manual block drags are kept in the blockPos state and override the radial defaults. Free graph view uses the same star idea at element level: root node centered, nodes on rings by their BFS depth.
Links connect BLOCKS (spec F4): one aggregated arc per (from-type, to-type, relation) with "label · count"; arcs exit/enter at the rectangle borders along the center-to-center direction (borderExit) with a short Bézier; selection highlights the arcs whose constituent edges touch the selected element.
Coordinates are star-centered (root at 0,0); the origin getter translates everything into the positive quadrant and sizes the canvas from the bounding box — keep new rendering code going through origin/renderBlockRect/geomOf.
Payload: { rootId, nodes[{id, type, label, url, depth, attributes}], edges[{from, to, rel, label}] } — no hidden flag; progressive display (initially depth <= 1, per-node ⚡ extension) is pure frontend state.

## Impact diagram: snowflake radial-tree layout and canvas pan
The impact diagram uses a SNOWFLAKE (radial BFS tree) layout: the root element (blocks view: its type block) is at the canvas center; every other block/node is placed inside its PARENT's angular sector at a radius growing with BFS depth (radialTree() in resources/js/impact-diagram.js, sector widths proportional to subtree leaf counts). Positions are computed at init from the FULL payload — so elements revealed later by progressive extension always appear next to the element they were extended from. Manual drags override defaults (blockPos / graphPos).
The canvas is larger than the viewport (fixed h-[70vh] diagram column): drag the background or scroll (wheel/trackpad, @wheel.prevent) to PAN in all directions; panning after a drag suppresses the background click-deselect (suppressClick).
Card slots inside a type block are indices among the VISIBLE elements only (visibleSlot/visibleIdsOf) — the block height only counts visible elements, so newly revealed items always sit inside their block (never below it).
Payload contract unchanged: { rootId, nodes[{id, type, label, url, depth, attributes}], edges[{from, to, rel, label}] }; progressive display (depth <= 1 initially, per-node ⚡ extension) is pure frontend state.
