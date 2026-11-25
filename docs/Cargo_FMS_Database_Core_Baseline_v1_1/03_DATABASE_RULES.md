# Database Rules & Standards

## Naming and keys
- Use plural snake_case table names and snake_case columns.
- Primary key: `id` (recommended BIGINT UNSIGNED for internal IDs; confirm before schema freeze).
- Business identifier: separate fields such as `shipment_number`, `quotation_number`, `invoice_number`; never expose database IDs as business numbers.
- FK names: `{entity}_id`; index all FK columns used for joins/filtering.
- Timestamps: `created_at`, `updated_at`; append-oriented records may also have `created_by`.
- Soft deletes: use only where restoration is meaningful; for financial/document records, prefer lifecycle state and immutable history rather than blanket soft deletes.

## Money, currency and FX
- Monetary amounts: `DECIMAL`, never FLOAT/DOUBLE. Proposed default `DECIMAL(19,4)` pending currency and precision review.
- FX rates: separate precision (proposed `DECIMAL(19,8)`); preserve source, effective date, rate type.
- Capture transaction currency, exchange rate, base currency, base amount, rounding policy and rate timestamp/source when applicable.
- Avoid recalculating historic transactions using current FX rates.

## Integrity and lifecycle
- Enforce uniqueness on business numbers by scope (company, branch, document type, period, etc.).
- Use database FK constraints for core integrity; use application policies for complex workflow rules.
- State transitions must be validated by domain services. Avoid free-form status updates.
- Use DB transactions for handoff, issue/void, invoicing, allocation, posting, closeout workflows.
- Use idempotency/unique source references to prevent duplicate charges, invoices, receipts, allocations, or posting from retries.
- Posted accounting entries and issued financial/document revisions should not be edited in place. Use reversal, credit, adjustment or new revision.
- Store planned and actual milestones separately; event timeline should not be destructively rewritten. Corrections should reference the original event and record the reason.

## Tenant and security
- Every company-scoped query must apply company authorization, not only request filters.
- Branch visibility must be enforced by policies/services.
- Private files should be stored outside public web root or in private object storage. Database stores metadata and opaque storage keys, not public file paths.
- Audit log should record actor, action, entity, entity ID, timestamp, request context, and before/after data where lawful and useful; avoid storing secrets.

## Query/index guidance
- Index foreign keys and common lookup filters.
- Use compound indexes based on real query patterns (e.g. company + branch + status + created date), not blanket indexing.
- Use unique constraints for partner codes, document numbers, and sequence scopes.
- For high-volume event tables, index shipment + event date and event type.
- Validate query plans and use pagination for operational lists.

## Prohibited shortcuts
- Do not use `MAX(id)+1` to generate document numbers.
- Do not use `paid` boolean as the only AR/AP state.
- Do not store calculated shipment profit as the single source of truth.
- Do not let UI components query Eloquent directly if the project architecture is Livewire → Service/Action → Repository Contract → Repository → Model.
- Do not create giant shipment table with document, billing, accounting, cargo and booking columns mixed together.


## Core-specific integrity additions
- Use composite `(company_id, id)` candidate keys on company-owned parents and composite FKs from children where feasible, preventing cross-company references at the database layer.
- User access is an explicit grant through company and branch access tables; never infer authorization from a submitted `company_id` or `branch_id`.
- Use a global user identity only if the product owner approves globally unique login email; otherwise model identity and company membership separately.
- Avoid nullable-scope unique keys for numbering. Normalize branch scope or split sequence tables.
- Global reference tables (countries, currencies, transport modes) are distinguished from company-configured masters (charge codes, payment terms, tax codes).
