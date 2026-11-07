---
paths:
  - 'resources/views/**'
---

# Views

## Catalog UI design guidelines: shared component library and page patterns
All catalog pages are built from the reusable components in resources/views/components/catalog/ (score, responsibilities, section, table, row-actions, actions, breadcrumbs, empty-state, metric-card, people, detail-card, timestamps, show-layout, form-layout, flash, delete-modal). Show pages use x-catalog.show-layout and order content: name (H1) + type badge, definition, responsibilities, metrics, then sections with counts and exact empty messages. Index pages share one skeleton (primary New button, flash, empty-state, table, row-actions, pagination). Create/edit pages use x-catalog.form-layout. Every sub-page carries x-catalog.breadcrumbs (index > name > current) and ends with a flux ghost back link (icon arrow-left). Scores render via x-catalog.score (0-1 value; variant "quality" thresholds 0.9/0.7, "governance" 0.8/0.5; "-" when null). Prefer flux: components over raw HTML buttons/links. When adding or restyling a page, reuse these components instead of hand-rolling markup.
