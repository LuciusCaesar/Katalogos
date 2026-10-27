---
paths:
  - 'app/Services/Governance*.php, app/Services/DataInitiativeGovernanceProjection.php, app/Models/GovernanceScore.php, app/Observers/BusinessAssetObserver.php, app/Listeners/CalculateGovernanceScore.php'
---

# Listeners

## Governance score = asset-level fact log; initiative metrics projected on read
Governance criteria are defined in code (App\Services\GovernanceCriteria, versioned via DEFINITION_VERSION) — never reintroduce a governance_criteria DB table. governance_scores is an append-only fact log: GovernanceScoreService::calculateAndSave is idempotent (skips appending when definition version, achieved points, criteria results AND the data_initiative_id snapshot are unchanged), so replayed events/double submissions never duplicate rows. Each row stores achieved_score (absolute points), score (ratio), max_possible_score and a data_initiative_id membership snapshot (not a FK). Data Initiative governance (average ratio + total points + history timeline) is NEVER written: DataInitiativeGovernanceProjection computes it on read by sweeping the score log chronologically. Deleting an asset cascade-deletes its score rows (accepted; soft-delete later). When the criteria definition evolves, bump DEFINITION_VERSION and let old rows keep their snapshot — the history stays interpretable.
