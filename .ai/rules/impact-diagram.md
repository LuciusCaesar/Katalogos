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
