# Core & Master Data — Detailed Database Design

**Version:** 1.0 · **Review status:** Proposed baseline for sign-off · **Target:** MySQL 8.x / InnoDB / utf8mb4

This document defines the first implementable database boundary. It is intentionally more precise than the domain catalog, but remains subject to the explicit business decisions in `07_CORE_DECISIONS_AND_OPEN_ITEMS.md`.

## 1. Conventions

- Internal PK: `BIGINT UNSIGNED AUTO_INCREMENT`; all corresponding FKs use exactly the same type.
- IDs are surrogate keys. Human-readable codes are separate and unique within their declared scope.
- Strings: `VARCHAR`; use `TEXT` only for genuinely long free text. Codes use uppercase ASCII where practical.
- Boolean flags: `TINYINT(1)`; lifecycle values use constrained `VARCHAR` codes or lookup tables, not MySQL `ENUM`, to keep application evolution manageable.
- Dates: `DATE`; timestamps: `DATETIME(6)` stored in UTC. Company timezone is an IANA timezone name and is used for display/business-date interpretation.
- Every company-owned row carries `company_id NOT NULL`, except global reference tables. `branch_id` is nullable only when the business record is legitimately company-wide.
- All FK columns must have indexes. Composite indexes are chosen for actual access patterns and do not replace authorization checks.
- `created_at`, `updated_at` are present on mutable master records. Add `created_by`, `updated_by` when actor attribution is required; do not assume Laravel timestamps provide audit history.
- Referenced master data is deactivated, not hard-deleted. Do not apply `ON DELETE CASCADE` from company/partner to transactional or historical records.

## 2. Ownership and scope matrix

| Aggregate / tables | Owner | Scope | Notes |
|---|---|---|---|
| companies | Organization | Root tenant/legal entity | No parent company assumption in v1 |
| branches, departments | Organization | Company | Branch may optionally own a department |
| users, roles, permissions, pivots | Identity & Access | Platform / company membership decision pending | Keep auth identity separate from operational employee profile |
| user_company_access, user_branch_access | Identity & Access | Company / branch | Explicit grants; deny by default |
| business_partners, partner_roles, contacts, addresses | Partner Master | Company | v1 chooses company-owned partner master, not implicit cross-company sharing |
| partner_credit_profiles | Partner Master / Credit Policy | Company + partner | Effective-dated commercial/credit settings; not a GL balance |
| partner_sales_assignments | Sales Ownership | Company + partner + user | Effective-dated assignment history |
| countries, currencies, transport_modes, service_types, charge_categories, package_types, container_types | Global Reference Data | Global | Seeded/maintained by controlled admin; no company_id unless tenant-specific override is later required |
| locations, ports, airports | Network Reference Data | Global baseline | Company-specific aliases/customs codes belong in extension tables if required |
| exchange_rates | Treasury / FX Reference | Company | Rate source/type and effective date must be explicit |
| payment_terms, tax_codes, charge_codes | Commercial Master | Company | Company-specific accounting/tax behavior |
| numbering_sequences | Document Numbering | Company | Optional branch/document type scope represented in unique key |

## 3. Detailed table specifications

Notation: `PK` primary key; `FK` foreign key; `UQ` unique constraint. Unless stated otherwise, columns `id`, `created_at`, `updated_at` are required. Use `DATETIME(6)` for timestamps.

### 3.1 Organization

#### `companies`
- PK `id BIGINT UNSIGNED`.
- `code VARCHAR(20) NOT NULL`; `legal_name VARCHAR(200) NOT NULL`; `display_name VARCHAR(200) NULL`.
- `tax_code VARCHAR(40) NULL`; `base_currency_id BIGINT UNSIGNED NOT NULL FK currencies.id`; `timezone VARCHAR(64) NOT NULL DEFAULT 'Asia/Ho_Chi_Minh'`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(code)`; optional UQ `(tax_code)` only if policy guarantees one company per tax code and null handling is understood.
- Index `(is_active, code)`.
- Owner: Organization. Company is root scope; do not physically delete after business activity exists.

#### `branches`
- PK `id`; FK `company_id BIGINT UNSIGNED NOT NULL -> companies.id`.
- `code VARCHAR(20) NOT NULL`; `name VARCHAR(160) NOT NULL`; `timezone VARCHAR(64) NULL`; `address_id BIGINT UNSIGNED NULL` only if a dedicated company-address aggregate is introduced; `is_head_office TINYINT(1) NOT NULL DEFAULT 0`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(company_id, code)`; UQ `(company_id, id)` to support composite company-consistency FKs from child tables.
- Index `(company_id, is_active, name)`.
- A company should have at most one active head office: enforce in application transaction or a generated-column strategy after MySQL behavior is validated.

#### `departments`
- PK `id`; FK `company_id NOT NULL`; FK `branch_id NULL -> branches.id`.
- `code VARCHAR(20) NOT NULL`; `name VARCHAR(120) NOT NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(company_id, code)`; composite FK `(company_id, branch_id) -> branches(company_id, id)` where branch is present.
- Index `(company_id, branch_id, is_active)`.
- Department can be company-wide (`branch_id IS NULL`) or branch-attached. If the organization requires every department to belong to one branch, change to NOT NULL before migrations.

### 3.2 Identity and access

#### `users`
- PK `id`; `name VARCHAR(160) NOT NULL`; `email VARCHAR(190) NOT NULL`; `email_verified_at DATETIME(6) NULL`; `password VARCHAR(255) NOT NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`; `last_login_at DATETIME(6) NULL`.
- UQ `(email)` if login identity is global. If same email may be used by multiple company accounts, use a separate global identity plus company memberships; do not silently change to `(company_id,email)` while retaining single-company user assumptions.
- Index `(is_active, email)` only if query plans justify it; unique email already indexes email.
- Owner: Identity & Access. Avoid putting operational branch ownership directly on users.

#### `roles`, `permissions`, `role_permissions`, `user_roles`
- Recommended v1 approach: use Laravel-compatible RBAC tables. `roles`: PK, `name VARCHAR(100)`, `guard_name VARCHAR(50)`, optional `company_id NULL` for platform/global vs company-specific role; UQ `(company_id, name, guard_name)` with explicit handling for NULL scope.
- `permissions`: PK, `name VARCHAR(120)`, `guard_name VARCHAR(50)`, UQ `(name, guard_name)`.
- `role_permissions`: PK or composite PK `(role_id, permission_id)`; both FKs NOT NULL; UQ pair.
- `user_roles`: PK or composite PK `(user_id, role_id, company_id)` if role assignment is company-contextual; user and role FKs NOT NULL.
- If using Spatie Laravel Permission, adopt its canonical schema and configure teams/company scope deliberately; do not create a parallel custom RBAC model.

#### `user_company_access`
- PK `id`; FK `user_id NOT NULL`; FK `company_id NOT NULL`; `access_level VARCHAR(20) NOT NULL` (e.g. MEMBER, ADMIN); `is_default TINYINT(1) NOT NULL DEFAULT 0`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(user_id, company_id)`; index `(company_id, is_active, user_id)`.

#### `user_branch_access`
- PK `id`; FK `user_id NOT NULL`; FK `company_id NOT NULL`; FK `branch_id NOT NULL`.
- UQ `(user_id, branch_id)`; composite FK `(company_id, branch_id) -> branches(company_id, id)`; index `(company_id, branch_id, user_id)`.
- Service/policy must also verify that user has active `user_company_access` for the same company. This cross-table condition is not expressible as a simple FK.

### 3.3 Partner Master

#### `business_partners`
- PK `id`; FK `company_id NOT NULL`.
- `partner_code VARCHAR(30) NOT NULL`; `legal_name VARCHAR(200) NOT NULL`; `display_name VARCHAR(200) NULL`; `partner_kind VARCHAR(20) NOT NULL` (`ORGANIZATION` or `INDIVIDUAL`); `tax_code VARCHAR(40) NULL`; `registration_no VARCHAR(60) NULL`; `website VARCHAR(255) NULL`; `default_currency_id BIGINT UNSIGNED NULL FK currencies.id`; `payment_term_id BIGINT UNSIGNED NULL`; `status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE'`; `notes TEXT NULL`.
- UQ `(company_id, partner_code)`; index `(company_id, status, legal_name)`; optional index `(company_id, tax_code)` (not unique until tax-code policy is confirmed).
- Owner: Partner Master. This is identity only; customer/vendor/agent capability is in `partner_roles`.

#### `partner_roles`
- PK `id`; FK `company_id NOT NULL`; FK `business_partner_id NOT NULL`.
- `role_code VARCHAR(30) NOT NULL` (`CUSTOMER`, `VENDOR`, `AGENT`, `COLOADER`, `CARRIER`, `SHIPPER`, `CONSIGNEE`, etc.); `is_active TINYINT(1) NOT NULL DEFAULT 1`; `effective_from DATE NULL`; `effective_to DATE NULL`.
- UQ `(business_partner_id, role_code)` for one current capability row. If role history is required, replace with effective-dated versions and enforce non-overlap in service logic.
- Composite FK `(company_id, business_partner_id) -> business_partners(company_id, id)`; index `(company_id, role_code, is_active)`.

#### `partner_contacts`
- PK `id`; FK `company_id NOT NULL`; FK `business_partner_id NOT NULL`.
- `full_name VARCHAR(160) NOT NULL`; `job_title VARCHAR(100) NULL`; `email VARCHAR(190) NULL`; `phone VARCHAR(40) NULL`; `mobile VARCHAR(40) NULL`; `is_primary TINYINT(1) NOT NULL DEFAULT 0`; `is_active TINYINT(1) NOT NULL DEFAULT 1`; `notes VARCHAR(500) NULL`.
- Index `(business_partner_id, is_active)` and `(company_id, email)`; no unique email because contacts may share addresses.
- Primary-contact uniqueness should be enforced transactionally or via a generated active-primary key strategy.

#### `partner_addresses`
- PK `id`; FK `company_id NOT NULL`; FK `business_partner_id NOT NULL`.
- `address_type VARCHAR(30) NOT NULL` (`REGISTERED`, `BILLING`, `OFFICE`, `WAREHOUSE`, `PICKUP`, `DELIVERY`, `OTHER`); `label VARCHAR(100) NULL`; `contact_name VARCHAR(160) NULL`; `phone VARCHAR(40) NULL`; `address_line1 VARCHAR(200) NOT NULL`; `address_line2 VARCHAR(200) NULL`; `ward VARCHAR(100) NULL`; `district VARCHAR(100) NULL`; `city VARCHAR(100) NULL`; `region VARCHAR(100) NULL`; `postal_code VARCHAR(20) NULL`; `country_code CHAR(2) NOT NULL`; `is_default TINYINT(1) NOT NULL DEFAULT 0`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- FK `country_id -> countries.id`; composite FK `(company_id,business_partner_id)`.
- Index `(business_partner_id, address_type, is_active)`; `(company_id, country_code, city)`.
- Shipment/document must snapshot the address values used at transaction time; never rely on a mutable address row to reproduce historical paperwork.

#### `partner_credit_profiles`
- PK `id`; FK `company_id NOT NULL`; FK `business_partner_id NOT NULL`.
- `credit_limit DECIMAL(19,4) NULL`; `currency_code CHAR(3) NOT NULL`; `credit_days SMALLINT UNSIGNED NOT NULL DEFAULT 0`; `effective_from DATE NOT NULL`; `effective_to DATE NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`; `approved_by BIGINT UNSIGNED NULL`; `approved_at DATETIME(6) NULL`.
- Index `(company_id, business_partner_id, effective_from, effective_to)`; FK approver -> users.
- Validate effective-date overlap in service layer; credit limit is not an AR balance.

#### `partner_sales_assignments`
- PK `id`; FK `company_id NOT NULL`; FK `business_partner_id NOT NULL`; FK `sales_user_id NOT NULL -> users.id`; `effective_from DATE NOT NULL`; `effective_to DATE NULL`; `assignment_type VARCHAR(20) NOT NULL DEFAULT 'PRIMARY'`; `created_by BIGINT UNSIGNED NULL`.
- Index `(company_id, business_partner_id, effective_from)` and `(company_id, sales_user_id, effective_to)`.
- Multiple historical assignments allowed. Prevent overlapping PRIMARY assignments in service logic or a generated-column uniqueness design after requirements are settled.

### 3.4 Global reference data

#### `countries`
- PK `id`; `iso2 CHAR(2) NOT NULL`; `iso3 CHAR(3) NOT NULL`; `name VARCHAR(120) NOT NULL`; `phone_code VARCHAR(8) NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `iso2`; UQ `iso3`; index `(name)`.

#### `locations`
- PK `id`; FK `country_id NOT NULL`; `location_type VARCHAR(20) NOT NULL` (`COUNTRY_REGION`, `REGION`, `CITY`, `DISTRICT`, `OTHER`); `code VARCHAR(30) NULL`; `name VARCHAR(160) NOT NULL`; `parent_location_id BIGINT UNSIGNED NULL`; `timezone VARCHAR(64) NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- FK parent -> locations.id; UQ `(country_id, location_type, code)` only when code is non-null (MySQL allows multiple NULL values); index `(country_id, location_type, name)` and `(parent_location_id)`.
- Do not make city names globally unique.

#### `ports`
- PK `id`; FK `country_id NOT NULL`; optional FK `location_id NULL`; `unlocode CHAR(5) NOT NULL`; `name VARCHAR(160) NOT NULL`; `port_type VARCHAR(20) NULL`; `timezone VARCHAR(64) NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(unlocode)`; index `(country_id, name)`.

#### `airports`
- PK `id`; FK `country_id NOT NULL`; optional FK `location_id NULL`; `iata_code CHAR(3) NULL`; `icao_code CHAR(4) NULL`; `name VARCHAR(160) NOT NULL`; `timezone VARCHAR(64) NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `iata_code` and UQ `icao_code` (multiple NULL allowed); index `(country_id, name)`.

#### `currencies`
- PK `id`; `code CHAR(3) NOT NULL` (ISO 4217); `name VARCHAR(80) NOT NULL`; `minor_unit TINYINT UNSIGNED NOT NULL`; `symbol VARCHAR(8) NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(code)`.

#### `exchange_rates`
- PK `id`; FK `company_id NOT NULL`; `from_currency_code CHAR(3) NOT NULL`; `to_currency_code CHAR(3) NOT NULL`; `rate_type VARCHAR(20) NOT NULL` (`BUY`, `SELL`, `ACCOUNTING`, `CUSTOMER`, etc.); `rate DECIMAL(20,10) NOT NULL`; `effective_at DATETIME(6) NOT NULL`; `source VARCHAR(80) NULL`; `created_by BIGINT UNSIGNED NULL`.
- UQ `(company_id, from_currency_code, to_currency_code, rate_type, effective_at)`; index `(company_id, from_currency_code, to_currency_code, effective_at)`.
- Check `rate > 0` and `from_currency_code <> to_currency_code`; self-conversion should be handled as 1 in service or explicit row, but choose one policy.
- Historical transactions snapshot applied rate and source; never recalculate from latest rate.

### 3.5 Commercial / operational reference masters

#### `transport_modes`
- PK `id`; `code VARCHAR(20) NOT NULL`; `name VARCHAR(80) NOT NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`; UQ `(code)`.
- Seed values may include OCEAN, AIR, ROAD, RAIL, MULTIMODAL. Do not use mode to encode direction or service subtype.

#### `service_types`
- PK `id`; `code VARCHAR(30) NOT NULL`; `name VARCHAR(100) NOT NULL`; `transport_mode_id BIGINT UNSIGNED NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`; `sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0`.
- UQ `(code)`; index `(transport_mode_id, is_active)`.
- Examples: FCL, LCL, AIR_FREIGHT, CUSTOMS, TRUCKING, WAREHOUSING. Null mode is allowed for cross-modal services.

#### `charge_categories`
- PK `id`; `code VARCHAR(30) NOT NULL`; `name VARCHAR(100) NOT NULL`; `financial_side VARCHAR(10) NOT NULL` (`REVENUE`, `COST`, `BOTH`); `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(code)`.

#### `charge_codes`
- PK `id`; FK `company_id NOT NULL`; FK `charge_category_id NOT NULL`; `code VARCHAR(30) NOT NULL`; `name VARCHAR(120) NOT NULL`; `default_unit VARCHAR(20) NULL`; `tax_code_id BIGINT UNSIGNED NULL`; `is_taxable TINYINT(1) NOT NULL DEFAULT 0`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(company_id, code)`; index `(company_id, charge_category_id, is_active)`.
- Charge code is a company-owned commercial master. A transaction charge snapshots code/name/tax/rate details at creation/issue.

#### `package_types`, `container_types`
- Global masters, PK `id`, UQ `code VARCHAR(20)`, `name VARCHAR(80)`, `is_active TINYINT(1)`.
- `container_types` may add `iso_size_type_code VARCHAR(10) NULL`, `nominal_length_ft DECIMAL(5,2) NULL`, `is_reefer TINYINT(1) NOT NULL DEFAULT 0`, `is_open_top TINYINT(1) NOT NULL DEFAULT 0`; unique ISO code when present.

#### `payment_terms`
- PK `id`; FK `company_id NOT NULL`; `code VARCHAR(20) NOT NULL`; `name VARCHAR(100) NOT NULL`; `due_days SMALLINT UNSIGNED NOT NULL DEFAULT 0`; `description VARCHAR(255) NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(company_id, code)`; index `(company_id, is_active)`.

#### `tax_codes`
- PK `id`; FK `company_id NOT NULL`; `code VARCHAR(20) NOT NULL`; `name VARCHAR(100) NOT NULL`; `rate DECIMAL(7,4) NOT NULL`; `tax_type VARCHAR(20) NOT NULL`; `effective_from DATE NOT NULL`; `effective_to DATE NULL`; `is_active TINYINT(1) NOT NULL DEFAULT 1`.
- UQ `(company_id, code, effective_from)`; index `(company_id, is_active, effective_from)`.
- Tax rules must be reviewed against applicable Vietnam requirements before production; historic invoice lines snapshot rate and tax basis.

### 3.6 Numbering

#### `numbering_sequences`
- PK `id`; FK `company_id NOT NULL`; `branch_id BIGINT UNSIGNED NULL`; `document_type VARCHAR(40) NOT NULL`; `period_key VARCHAR(10) NOT NULL DEFAULT 'ALL'`; `prefix VARCHAR(30) NOT NULL DEFAULT ''`; `next_number BIGINT UNSIGNED NOT NULL DEFAULT 1`; `padding SMALLINT UNSIGNED NOT NULL DEFAULT 6`; `reset_policy VARCHAR(20) NOT NULL DEFAULT 'NEVER'`; `updated_at DATETIME(6) NOT NULL`.
- UQ `(company_id, branch_id, document_type, period_key)` requires careful NULL semantics. Recommended robust implementation: normalize scope using `branch_scope_id BIGINT UNSIGNED NOT NULL DEFAULT 0` where 0 means company-wide, and unique `(company_id, branch_scope_id, document_type, period_key)`; do not FK 0. Alternatively separate company-wide and branch-specific sequence tables.
- Allocate number inside a DB transaction using `SELECT ... FOR UPDATE` or atomic `UPDATE ... LAST_INSERT_ID`; never `MAX(number)+1`. Persist issued business number on the document row with a matching unique constraint.

## 4. Composite company integrity

For company-owned child tables, add `UNIQUE (company_id, id)` on the parent and composite FK `(company_id, parent_id)` on the child where practical. This prevents accidentally linking a row in Company A to a parent row in Company B. Apply to branches, business_partners, partner child tables, and company-scoped masters. It complements, but does not replace, authorization.

## 5. Delete/update policy

- Organization and partner masters: `RESTRICT` deletion once referenced; set `is_active=0` / `status=INACTIVE`.
- Pure global lookup rows: restrict deletion; controlled updates only.
- Pivot/access rows: may be revoked/deactivated; preserve audit trail for access changes.
- Historical transaction snapshots: no FK required for every copied display field; keep source FK nullable where deletion is prohibited and snapshot values immutable.
- FK updates: primary keys are immutable; use `ON UPDATE RESTRICT`.

## 6. Index policy

Create indexes for every FK. Add composite indexes only for known list/filter paths, typically `(company_id, status, created_at)`, `(company_id, branch_id, status, created_at)`, and partner lookup `(company_id, status, legal_name)`. Verify with `EXPLAIN`; avoid indexing every low-cardinality boolean independently.

## 7. Implementation gate

Do not write Laravel migrations until the open decisions in `07_CORE_DECISIONS_AND_OPEN_ITEMS.md` are answered, especially identity tenancy, partner sharing, company/branch scope, and numbering reset policy. Then build migrations in dependency order and test fresh install, rollback, cross-company FK rejection, uniqueness, and concurrent number allocation.
