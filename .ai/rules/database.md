---
paths:
  - 'database/**'
---

# Database

## Rebuild local SQLite DB after switching branches with divergent migrations
database/database.sqlite is local-only (gitignored). Branches like metamodel-business-data-terms rename core tables (data_initiatives -> business_initiatives), so a DB migrated on one branch will not match main's schema.
After switching between branches whose migrations diverge, run `php artisan migrate:fresh --seed` before using the app.
If an existing DB holds data worth keeping, copy it first (e.g. database/database.sqlite.backup-<branch>).
App code must never assume table existence fixes empty-state bugs: DashboardComponent-style count() queries return 0 on empty tables by design.
