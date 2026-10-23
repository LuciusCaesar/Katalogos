---
paths:
  - app/Models/BusinessEvent.php
---

# Models

## business_asset_business_event pivot is evolvable toward typed relations
The business_asset_business_event pivot carries a nullable relation_type column reserved for the future "subject of" / "context of" distinction; today businessAssets()/businessEvents() are undifferentiated and cover every pivot row. When the split happens: filter with wherePivot('relation_type', ...), backfill existing rows, and replace the (business_asset_id, business_event_id) unique index with one including relation_type.
