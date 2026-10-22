---
paths:
  - app/Http/Controllers/GlobalSearchController.php
---

# Controllers

## Global search: adding a new searchable entity
Global search (GET /search, route name "search") uses Laravel Scout with the database driver (SCOUT_DRIVER=database). Per-entity filtering/sorting was deliberately removed from index pages in favor of this global search.
To make a new entity searchable: add the Scout Searchable trait + toSearchableArray() (only REAL table columns — the database engine LIKE-queries them), add it as a group in GlobalSearchController (title, web.*.show routeName, take(5)), keep groups ordered like the sidebar, and add cases to tests/Feature/GlobalSearchTest.php.
LIKE wildcards (% and _) are stripped in __invoke() — keep that. Associated-info matching (e.g. business asset by steward/owner name) goes through Scout's ->query() callback with orWhereHas.
Do not re-introduce per-entity search/filter UI on index pages without user request.
