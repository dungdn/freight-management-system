# Domain Table Catalog

Table names are proposed, not frozen. Each domain owns its tables and APIs. Shared reference data should be accessed through contracts, not direct cross-domain ORM joins in application logic.


## A. Core / Organization / Master Data

| Table | Purpose | Notes |
|---|---|---|
| companies | Legal/tenant company | Base currency, timezone, active flag |
| branches | Branches/offices | Belongs to company |
| departments | Organizational department | Company/branch scope as decided |
| users | User accounts | Authentication identity |
| roles, permissions, role_permissions, user_roles | Access control | Use policy checks in application layer |
| user_branch_access | Explicit branch access | Enforce scope server-side |
| business_partners | Shared partner identity | Customer/vendor/agent/co-loader/carrier |
| partner_roles | Role capabilities | Unique partner + role |
| partner_contacts | Contact people | Optional partner contact |
| partner_addresses | Partner addresses | Address types and country code |
| partner_credit_profiles | Credit terms/limit | Financial policy, effective-dated if needed |
| partner_sales_assignments | Sales ownership | Effective date range, assignment history |
| countries | Country codes | ISO country code |
| locations | Generic geographic locations | Country/region/city; use typed port/airport references as needed |
| ports, airports | Port/airport reference data | Codes and timezone |
| shipping_routes, shipping_route_legs | Route definitions | Optional planning/master reference |
| currencies, exchange_rates | Currency and FX | Effective-dated rate records |
| transport_modes | Ocean/Air/Road/etc. | Reference values |
| service_types | FCL/LCL/AIR/LOGISTICS etc. | Avoid conflating with direction |
| charge_categories, charge_codes | Charge taxonomy | Revenue/cost category and behavior |
| package_types, container_types | Cargo packaging references | Reference/master data |
| payment_terms, tax_codes | Payment/tax references | Version/effective dates as needed |
| numbering_sequences | Business number generation | Transaction-safe sequence per scope |

For Core & Master Data, table-level PK/FK/nullability/unique/index/ownership details are governed by `05_CORE_MASTER_DATA_DETAILED_DESIGN.md`; the diagram is in `06_CORE_MASTER_DATA_ERD.md`.

## B. Sales & CRM

| Table | Purpose | Notes |
|---|---|---|
| sales_inquiries | Lead/request for quote | Owner, partner, service, route, status |
| sales_inquiry_activities | Follow-up history | Append-oriented activity log |
| quotations | Quotation aggregate | Header and current revision pointer |
| quotation_revisions | Versioned quote | Snapshot commercial conditions |
| quotation_lines | Price/service detail | Each revision owns its lines |
| quotation_approvals | Approval decisions | Actor, decision, timestamp, note |
| sales_orders | Accepted commercial work | Created/accepted from quote |
| sales_order_lines | Order detail | Snapshot agreed services/charges |
| sales_order_handoffs | Sales-to-operations handoff | Handoff status, receiving user/team |

## C. Rate Management

| Table | Purpose | Notes |
|---|---|---|
| rate_sheets | Rate collection | Customer/carrier/agent rates |
| rate_sheet_revisions | Version/effective period | Avoid retroactively changing quoted rates |
| rate_lines | Rate values | Route, mode, service, charge code, unit |
| rate_conditions | Rules/conditions | Container, minimum, validity, exclusions |
| rate_approvals | Approval workflow | Optional based on policy |

## D. Freight Operations

| Table | Purpose | Notes |
|---|---|---|
| shipments | Shipment/job header | Company, branch, number, direction, mode, service, lifecycle |
| shipment_parties | Shipper/consignee/notify/etc. | Snapshot identity and address per shipment |
| shipment_cargo_items | Cargo/package/weight/volume lines | Avoid a single cargo blob on header |
| shipment_legs | Route segments | Planned/actual departure and arrival data |
| bookings | Carrier/agent booking | Booking reference, carrier, route, dates, status |
| booking_shipment_links | Booking-to-shipment relation | Supports shared/consolidated booking if required |
| booking_containers | Container details under booking | Container number/type/seal, etc. |
| containers | Container master/identity | Only if container reuse/history is required |
| shipment_container_assignments | Shipment/container relationship | Time/history and quantities |
| shipment_events | Shipment milestone timeline | Event type, planned/actual, source, correction link |
| shipment_exceptions | Operational exceptions | Severity, owner, resolution lifecycle |
| consols | Consolidation header | Master-level group |
| consol_shipments | Consol ↔ shipment links | Link table |
| consol_cargo_receipts | Cargo receiving records | CFS/warehouse receipt context |
| consol_loading_records | Load/manifest planning | Container allocation |
| consol_cost_allocations | Shared cost allocation | Auditable basis and amount |

## E. Documentation

| Table | Purpose | Notes |
|---|---|---|
| document_types | Document definitions | HBL/MBL/HAWB/MAWB, SI, Manifest, AN, DO, POD etc. |
| document_templates | Template identity | Company/branch/service scope |
| document_template_versions | Versioned template | Active dates and format metadata |
| shipment_documents | Document identity per shipment | Document number, type, lifecycle |
| shipment_document_revisions | Revision snapshots | `document_data` + `snapshot_data`; issued revision immutable |
| document_files | Stored file metadata | Storage key, mime, checksum, size; private storage |
| document_approvals | Review/approval history | Append-oriented |

## F. Billing / Shipment Accounting

| Table | Purpose | Notes |
|---|---|---|
| charges | Revenue/cost entries | Estimated/accrued/actual; currency and FX snapshot |
| charge_allocations | Charge allocation | Shipment/consol or other supported target |
| charge_source_links | Trace charge to origin | Quote, rate, booking, vendor source |
| charge_adjustments | Adjustments | Reason and source reference |
| debit_notes, debit_note_lines | Customer billing request/document | Issue/void/adjust lifecycle |
| credit_notes, credit_note_lines | Customer credits | Correction trail |
| commission_notes, commission_note_lines | Agent commission | Explicit calculation basis |

Profit should be calculated from defined charge categories and states. Do not keep an ungoverned `shipment_profit` column as the source of truth.

## G. AR/AP

| Table | Purpose | Notes |
|---|---|---|
| invoices, invoice_lines | Issued billing documents | Invoice header/line snapshots |
| receipts | Customer money received | May be unapplied or partly applied |
| receipt_allocations | Receipt-to-invoice/open-item allocation | Supports partial and multiple allocations |
| payments | Vendor/agent payments | Payment document lifecycle |
| payment_allocations | Payment-to-payable allocation | Partial allocation supported |
| open_items | Outstanding AR/AP | Balance and currency; created from invoice/posting policy |
| settlement_adjustments | Write-off/FX/rounding adjustments | Approval and reason required |

Do not model receivables/payables as a simple boolean `paid` flag.

## H. Financial Accounting (later phase)

| Table | Purpose | Notes |
|---|---|---|
| chart_of_accounts | GL account master | Company-scoped and controlled |
| accounting_periods | Period controls | Open/closed state |
| journal_entries, journal_lines | Double-entry journal | Balanced before posting; posted immutable |
| posting_rules | Mapping from source transactions | Versioned rules |
| bank_accounts, bank_transactions | Bank records | Import and reconciliation |
| bank_reconciliations | Reconciliation workflow | Immutable reconciliation results/history |
| fixed_assets, asset_depreciation | Asset register/depreciation | Later feature |
| prepaid_expenses, prepaid_amortizations | Prepaid schedule | Later feature |
