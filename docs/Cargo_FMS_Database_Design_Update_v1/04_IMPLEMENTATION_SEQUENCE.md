# Database Implementation Sequence

This sequence prioritizes stable foundations and vertical slices. Each phase should be reviewed and tested before moving on.


## Phase 0 — Decisions and design review
- [ ] Confirm MySQL version, charset/collation, timezone policy.
- [ ] Confirm company/branch scope and shared partner ownership.
- [ ] Confirm business-number scopes and sequence behavior.
- [ ] Confirm currencies, rounding and FX policy.
- [ ] Confirm shipment directions, transport modes and service-type taxonomy.
- [ ] Confirm soft-delete and audit retention policy.
- [ ] Review proposed ERD and table catalog.

## Phase 1 — Core foundation
- **Required review artifacts:** `05_CORE_MASTER_DATA_DETAILED_DESIGN.md`, `06_CORE_MASTER_DATA_ERD.md`, `07_CORE_DECISIONS_AND_OPEN_ITEMS.md`, `08_CORE_MASTER_DATA_MIGRATION_PLAN.md`.
- Do not start migrations until the decision register minimum sign-off is complete.
- [ ] Companies, branches, departments, users, roles/permissions, branch access.
- [ ] Business partners, roles, contacts, addresses, credit profiles, sales assignments.
- [ ] Countries, locations, ports, airports, currencies, exchange rates.
- [ ] Transport modes, service types, charge categories/codes, payment terms, tax codes.
- [ ] Numbering sequence service (transaction-safe; concurrency test).
- [ ] Seed data and master-data validation.

## Phase 2 — Sales
- [ ] Inquiry and follow-up activities.
- [ ] Quotation header, revision, lines, approvals.
- [ ] Quote acceptance and sales-order conversion in transaction.
- [ ] Sales-order handoff to operations with idempotency.
- [ ] Tests for duplicate handoff, revision history and permission scope.

## Phase 3 — Freight Operations
- [ ] Shipment header and shipment number service.
- [ ] Shipment parties, cargo items, legs.
- [ ] Booking and booking-to-shipment link.
- [ ] Event/milestone timeline, exception workflow.
- [ ] Shipment lifecycle transitions.
- [ ] Index and branch-scope tests.

## Phase 4 — Consolidation and Documentation
- [ ] Consol header/link/receipt/loading/allocation tables.
- [ ] Document type/template/version management.
- [ ] Shipment document + revision snapshots.
- [ ] File metadata and private storage integration.
- [ ] Issued revision immutability and amendment tests.
- [ ] Vietnam customs/port/airport output specs verified against current official requirements before coding.

## Phase 5 — Billing & Shipment Accounting
- [ ] Charges and charge source links.
- [ ] Revenue/cost state and FX snapshot rules.
- [ ] Charge allocation and shared consol cost allocation.
- [ ] Debit/credit/commission note workflows.
- [ ] Shipment profitability query with explicit basis (estimated, accrued, actual, invoiced).
- [ ] Closing checks: missing cost, unbilled revenue, unresolved charge sources.

## Phase 6 — AR/AP
- [ ] Invoice header/lines and issuing workflow.
- [ ] Open items.
- [ ] Receipts/payments and allocations.
- [ ] Partial, multi-invoice and unapplied allocation cases.
- [ ] Aging and reconciliation tests.

## Phase 7 — Financial Accounting
- [ ] COA and accounting periods.
- [ ] Posting rules and journal header/lines.
- [ ] Double-entry validation, posting and reversal.
- [ ] Bank records/reconciliation.
- [ ] Assets, depreciation, prepaid/amortization.
- [ ] VAT and financial reports; verify local accounting requirements with qualified review.

## Migration quality gate (each phase)
- [ ] Migration up/down tested on fresh DB and upgrade path.
- [ ] FK/index/unique constraints reviewed.
- [ ] Factories/seeders prepared.
- [ ] Repository contracts and domain services implemented; no cross-module model leakage.
- [ ] Unit/feature tests for lifecycle, concurrency, authorization, and idempotency.
- [ ] Document changes reflected in ERD and catalog.
