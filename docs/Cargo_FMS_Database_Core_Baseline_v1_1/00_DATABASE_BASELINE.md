# Database Baseline

## 1. Product and data architecture
Cargo FMS is a Freight Forwarding & Logistics Operations Management Platform. It is an operational product with financial workflows, not a generic CRUD app.

- **Modular monolith** for the initial product; modules own their tables and lifecycle.
- **Shipment is the operational center**, not the universal record for every workflow.
- Sales, quotation, booking, shipment, document revision, charge, invoice, receipt/payment, and journal each have independent states.
- Modules communicate through application/service contracts and domain events; avoid cross-module direct model manipulation.
- Analytics begin with query/report services and selective read models; no premature warehouse.

## 2. Database target
Recommended baseline: MySQL 8.x, InnoDB, utf8mb4. Confirm exact server release and Laravel support before implementation.

## 3. Tenant/company structure
Initial model: company and branch are first-class organizational dimensions.
- `company_id` is required on company-owned business records.
- `branch_id` is required where operational responsibility or reporting is branch-specific; otherwise nullable/omitted by table design.
- Authorization must enforce company/branch scope server-side. UI filtering is not a security boundary.
- **Approved:** each Company owns its own Partner Master in v1. `business_partners.company_id` is mandatory; cross-company sharing is not allowed. A future shared-master capability requires an explicit global identity + company profile/mapping design.

## 4. Partner model
Use `business_partners` for shared identity (customer, vendor, agent, co-loader, carrier, etc.) and `partner_roles` for role capabilities. Avoid separate customer/vendor tables unless later domain requirements justify a distinct aggregate.

## 5. Operational record boundaries
- Inquiry/Quotation/Sales Order: commercial lifecycle.
- Booking: carrier/agent booking lifecycle; may link to one or multiple shipments depending on consolidation/business rules.
- Shipment: operational job and shipment lifecycle.
- Shipment event: timeline fact, with planned and actual timestamps distinct.
- Document: document identity plus revision snapshots; issued revisions immutable.
- Charge: estimated/accrued/actual revenue or cost entries; independent of invoice.
- Invoice: receivable/payable document; does not replace charge records.
- Open item: outstanding balance record; receipts/payments and allocations update it transactionally.
- Journal: accounting posting; financial accounting is a later module.

## 6. Direction and transport mode
Store direction and transport mode as separate dimensions, e.g. `direction=EXPORT`, `transport_mode=OCEAN`, `service_type=FCL`. Avoid overloaded shipment-type values such as `EXPORT_FCL`.

## 7. Shipment lifecycle (database implication)
Suggested high-level statuses: `DRAFT`, `CONFIRMED`, `OPERATIONAL`, `COMPLETED`, `CLOSED`, `CANCELLED`. Status changes must occur through validated transitions. Timeline milestones are separate event rows and should not be collapsed into one status field. Store planned and actual values separately.

## 8. Immutability and correction
- Issued document revisions are immutable; amendments create a new revision.
- Issued/posting financial documents are immutable; corrections use credit, adjustment, reversal or replacement workflows.
- Historical snapshots must preserve the values used at the time (partner/address, rate, currency, terms, document template data).
- Master data referenced by transactions should be deactivated, not hard-deleted.

## 9. Core & Master Data baseline
The detailed Core & Master Data design is defined in `05_CORE_MASTER_DATA_DETAILED_DESIGN.md`, with its logical ERD in `06_CORE_MASTER_DATA_ERD.md`. Project-owner decisions are recorded in `07_CORE_DECISIONS_AND_OPEN_ITEMS.md`. Partner ownership, global user identity, yearly numbering and Spatie RBAC have been approved; remaining implementation gates are listed separately.

## 10. Decisions and remaining design work
- Partner ownership is decided: each Company owns its own Partner Master in v1.
- Shipment-specific number format and whether shipment numbers are company-wide or branch-specific must be specified per document type within the approved yearly numbering scope.
- Whether a shipment may be linked to multiple sales orders/quotations.
- Consolidation allocation policy for shared charges and how allocation bases are selected.
- Financial base currency per company and FX source/precision policy.
- Whether shipment accounting is operational profitability only or formal subledger posting.
- Retention and privacy policy for files and audit logs.
