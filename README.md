# Katalogos

Katalogos is an open-source **data governance platform** designed to help organizations manage their data assets, initiatives, and stewardship responsibilities. Built on Laravel, Katalogos provides a structured approach to data cataloging, role assignments, and governance workflows.

## About

Katalogos addresses the critical need for **business value-oriented data governance** by:

- **Centralizing data asset inventory** - Track and document all your data assets in one place
- **Managing data initiatives** - Organize data projects and programs with clear ownership
- **Defining roles and responsibilities** - Assign Data Stewards, Data Owners, Data Custodians, and Domain Owners to ensure accountability
- **Enabling traceability** - Maintain clear lineage between business assets and data initiatives

As an open-source solution, Katalogos can be customized to fit your organization's specific governance requirements while maintaining industry best practices.

## Data Model

```mermaid
classDiagram
    %% ========== ENTITY MODELS ========== %%

    class User {
        +id
        +name
        +email
        +password
        +email_verified_at
        +two_factor_secret
        +two_factor_recovery_codes
        +two_factor_confirmed_at
        +remember_token
        +initials()
        +roleAssignments()
        +roles()
        +dataInitiatives()
        +businessAssets()
        +dataSources()
        +domains()
        +hasRoleOn()
        +isDataStewardFor()
        +isDataOwnerFor()
        +isDataCustodianFor()
        +isDomainOwnerFor()
    }

    class Role {
        +id
        +name
        +description
        +users()
        +dataInitiatives()
        +businessAssets()
        +dataSources()
        +domains()
    }

    class DataInitiative {
        +id
        +code
        +label
        +description
        +average_governance_score
        +businessAssets()
        +roleAssignments()
        +users()
        +roles()
        +governanceScoreHistory()
        +businessObjectives()
        +dataSteward()
        +dataOwner()
        +assignRoleToUser()
        +removeRoleFromUser()
        +getAvgDataQualityCheckScoreAttribute()
    }

    class BusinessAsset {
        +id
        +name
        +definition
        +data_initiative_id
        +domain_id
        +consistency
        +integrity
        +availability
        +dataInitiative()
        +domain()
        +roleAssignments()
        +users()
        +roles()
        +dataIssues()
        +dataSources()
        +businessRules()
        +businessProcesses()
        +regulations()
        +governanceScores()
        +governanceScore()
        +dataSteward()
        +dataOwner()
        +assignRoleToUser()
        +removeRoleFromUser()
        +calculateGovernanceScore()
        +getMinDataQualityCheckScoreAttribute()
        +getMaxDataQualityCheckScoreAttribute()
        +getAvgDataQualityCheckScoreAttribute()
    }

    class Domain {
        +id
        +name
        +description
        +businessAssets()
        +roleAssignments()
        +users()
        +roles()
        +domainOwner()
        +assignRoleToUser()
        +removeRoleFromUser()
    }

    class BusinessProcess {
        +id
        +name
        +description
        +businessAssets()
    }

    class DataSource {
        +id
        +name
        +description
        +businessAssets()
        +dataQualityChecks()
        +roleAssignments()
        +users()
        +roles()
        +dataCustodian()
        +assignRoleToUser()
        +removeRoleFromUser()
    }

    class BusinessRule {
        +id
        +name
        +description
        +dimension
        +businessAssets()
        +dataIssues()
        +dataQualityChecks()
        +getMinDataQualityCheckScoreAttribute()
        +getMaxDataQualityCheckScoreAttribute()
        +getAvgDataQualityCheckScoreAttribute()
    }

    class DataQualityCheck {
        +id
        +name
        +description
        +business_rule_id
        +businessRule()
        +dataSources()
        +scores()
        +latestScore()
    }

    class DataQualityCheckScore {
        +id
        +data_quality_check_id
        +rows_passed
        +rows_failed
        +total_rows
        +score
        +origin_type
        +origin_id
        +origin_name
        +notes
        +dataQualityCheck()
        +origin()
        +getScorePercentageAttribute()
    }

    class DataIssue {
        +id
        +name
        +description
        +businessAssets()
        +rootCauses()
        +businessRules()
    }

    class RootCause {
        +id
        +name
        +description
        +dimension
        +dataIssues()
        +solutions()
    }

    class Solution {
        +id
        +name
        +description
        +dimension
        +implementation_status
        +rootCauses()
    }

    class Regulation {
        +id
        +name
        +description
        +documentation_url
        +businessAssets()
    }

    class BusinessObjective {
        +id
        +name
        +description
        +dataInitiatives()
        +kpis()
    }

    class KPI {
        +id
        +name
        +description
        +business_objective_id
        +businessObjective()
    }

    class GovernanceCriterion {
        +id
        +key
        +name
        +description
        +weight
        +category
        +is_active
        +order
    }

    class GovernanceScore {
        +id
        +business_asset_id
        +score
        +max_possible_score
        +criteria_results
        +criteria_weights
        +changes
        +calculated_at
        +businessAsset()
    }

    class DataInitiativeGovernanceScoreHistory {
        +id
        +data_initiative_id
        +score
        +event
        +calculated_at
        +dataInitiative()
    }

    %% ========== PIVOT MODEL ========== %%

    class RoleAssignment {
        +id
        +user_id
        +role_id
        +roleable_id
        +roleable_type
        +user()
        +role()
        +roleable()
    }

    %% ========== RELATIONSHIPS ========== %%

    %% User Relationships
    User "1" -- "0..*" RoleAssignment : assigns
    User "1" -- "0..*" DataInitiative : via RoleAssignment
    User "1" -- "0..*" BusinessAsset : via RoleAssignment
    User "1" -- "0..*" DataSource : via RoleAssignment
    User "1" -- "0..*" Domain : via RoleAssignment

    %% Role Relationships
    Role "1" -- "0..*" RoleAssignment : defines
    Role "1" -- "0..*" DataInitiative : via RoleAssignment
    Role "1" -- "0..*" BusinessAsset : via RoleAssignment
    Role "1" -- "0..*" DataSource : via RoleAssignment
    Role "1" -- "0..*" Domain : via RoleAssignment

    %% Polymorphic: RoleAssignment to Roleable
    RoleAssignment "1" -- "1" User : belongs to
    RoleAssignment "1" -- "1" Role : belongs to
    RoleAssignment "1" -- "1" DataInitiative : roleable (morph)
    RoleAssignment "1" -- "1" BusinessAsset : roleable (morph)
    RoleAssignment "1" -- "1" DataSource : roleable (morph)
    RoleAssignment "1" -- "1" Domain : roleable (morph)

    %% DataInitiative Relationships
    DataInitiative "1" -- "0..*" BusinessAsset : owns
    DataInitiative "1" -- "0..*" GovernanceScoreHistory : tracks
    DataInitiative "1" -- "0..*" BusinessObjective : linked via pivot
    DataInitiative "1" -- "0..*" RoleAssignment : has roles

    %% BusinessAsset Relationships
    BusinessAsset "1" -- "0..*" BusinessProcess : related via pivot
    BusinessAsset "1" -- "0..*" DataIssue : linked via pivot
    BusinessAsset "1" -- "0..*" DataSource : linked via pivot
    BusinessAsset "1" -- "0..*" BusinessRule : linked via pivot
    BusinessAsset "1" -- "0..*" Regulation : linked via pivot
    BusinessAsset "1" -- "0..*" GovernanceScore : has scores
    BusinessAsset "1" -- "1" Domain : belongs to
    BusinessAsset "1" -- "1" DataInitiative : belongs to
    BusinessAsset "1" -- "0..*" RoleAssignment : has roles

    %% Domain Relationships
    Domain "1" -- "0..*" BusinessAsset : contains
    Domain "1" -- "0..*" RoleAssignment : has roles

    %% BusinessProcess Relationships
    BusinessProcess "1" -- "0..*" BusinessAsset : associated via pivot

    %% DataSource Relationships
    DataSource "1" -- "0..*" BusinessAsset : associated via pivot
    DataSource "1" -- "0..*" DataQualityCheck : linked via pivot
    DataSource "1" -- "0..*" RoleAssignment : has roles

    %% BusinessRule Relationships
    BusinessRule "1" -- "0..*" BusinessAsset : associated via pivot
    BusinessRule "1" -- "0..*" DataIssue : linked via pivot
    BusinessRule "1" -- "0..*" DataQualityCheck : owns

    %% DataQualityCheck Relationships
    DataQualityCheck "1" -- "0..*" DataQualityCheckScore : has scores
    DataQualityCheck "1" -- "1" BusinessRule : belongs to
    DataQualityCheck "1" -- "0..*" DataSource : linked via pivot

    %% DataQualityCheckScore Relationships
    DataQualityCheckScore "1" -- "1" DataQualityCheck : belongs to
    DataQualityCheckScore "1" -- "0..1" User : origin

    %% DataIssue Relationships
    DataIssue "1" -- "0..*" BusinessAsset : linked via pivot
    DataIssue "1" -- "0..*" RootCause : linked via pivot
    DataIssue "1" -- "0..*" BusinessRule : linked via pivot

    %% RootCause Relationships
    RootCause "1" -- "0..*" DataIssue : linked via pivot
    RootCause "1" -- "0..*" Solution : linked via pivot

    %% Solution Relationships
    Solution "1" -- "0..*" RootCause : linked via pivot

    %% Regulation Relationships
    Regulation "1" -- "0..*" BusinessAsset : linked via pivot

    %% BusinessObjective Relationships
    BusinessObjective "1" -- "0..*" DataInitiative : linked via pivot
    BusinessObjective "1" -- "0..*" KPI : owns

    %% KPI Relationships
    KPI "1" -- "1" BusinessObjective : belongs to

    %% GovernanceScore Relationships
    GovernanceScore "1" -- "1" BusinessAsset : belongs to

    %% DataInitiativeGovernanceScoreHistory Relationships
    DataInitiativeGovernanceScoreHistory "1" -- "1" DataInitiative : belongs to
```

### Key Relationships

- **User ↔ RoleAssignment ↔ Roleable (polymorphic)**: Users are assigned roles on entities via the polymorphic `role_assignments` pivot table. Roleable entities include: DataInitiative, BusinessAsset, DataSource, and Domain.
- **DataInitiative → BusinessAsset**: A DataInitiative contains many BusinessAssets (one-to-many)
- **Polymorphic Roles**: The same Role model can be assigned to any entity type (DataInitiative, BusinessAsset, DataSource, Domain)
- **BusinessProcess ↔ BusinessAsset**: Many-to-many relationship for process-asset associations
- **BusinessAsset ↔ DataIssue**: Many-to-many relationship tracking data issues per asset
- **BusinessAsset ↔ BusinessRule**: Many-to-many relationship for rule-asset associations
- **BusinessAsset ↔ Regulation**: Many-to-many relationship for compliance tracking
- **BusinessAsset ↔ DataSource**: Many-to-many relationship for source-asset associations
- **BusinessRule → DataQualityCheck**: A BusinessRule owns many DataQualityChecks (one-to-many)
- **DataQualityCheck → DataQualityCheckScore**: A check has many score records over time (one-to-many)
- **DataIssue ↔ RootCause**: Many-to-many relationship for issue analysis
- **RootCause ↔ Solution**: Many-to-many relationship for remediation tracking
- **BusinessObjective ↔ DataInitiative**: Many-to-many relationship for objective-initiative alignment
- **BusinessObjective → KPI**: A BusinessObjective owns many KPIs (one-to-many)
- **BusinessAsset → GovernanceScore**: A BusinessAsset has many governance score records over time (one-to-many)
- **Domain → BusinessAsset**: A Domain contains many BusinessAssets (one-to-many)

## Getting Started

### Prerequisites

- PHP 8.4+
- Composer 2.x
- Node.js 18+
- SQLite (default) or MySQL/PostgreSQL

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-org/katalogos.git
   cd katalogos
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

5. Build frontend assets:
   ```bash
   npm run build
   ```

### Running the Application

#### Development
Start the development server with all services:
```bash
composer run dev
```

This starts:
- Laravel development server
- Queue listener
- Log viewer (Pail)
- Vite dev server

Access the application at `http://localhost:8000`

#### Production
For production, ensure you have:
- Proper `.env` configuration
- Database connection configured
- Queue worker running
- Frontend assets built with `npm run build`

## Running Tests

Katalogos uses Pest for testing with PHPUnit compatibility:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter RoleAssignmentTest

# Run with coverage
php artisan test --coverage
```

The test suite includes:
- Feature tests for API endpoints
- Feature tests for role assignments
- Authentication tests
- Authorization tests

## Code Quality

```bash
# Run linter
composer run lint

# Check lint without fixing
composer run lint:check

# Run static analysis
composer run types:check

# Run full CI check
composer run ci:check
```

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests and linting (`composer run ci:check`)
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Development Workflow

- Follow PSR-12 coding standards
- Write tests for new features
- Keep commits atomic and well-described
- Update documentation as needed

## License

Katalogos is open-sourced software licensed under the [MIT license](LICENSE).
