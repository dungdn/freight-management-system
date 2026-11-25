# Data Model Principles — Cargo FMS

## 1. Identity strategy
Recommended default: numeric internal PKs plus unique business identifiers (Shipment No., Quotation No., Booking No., Invoice No.). Public identifiers can use ULID/UUID if exposure or distributed creation later requires it. Adopt one policy consistently.

## 2. Entity ownership (conceptual)
- Core: company, branch, department, user, partner, partner roles, contacts, locations, currency, charge codes, service catalogue, numbering, audit
- Sales: inquiry, quotation, revision, quotation lines, sales order/shipping order
- Rates: rate sheet/revision/line/condition
- Freight: shipment, parties, cargo, legs, booking, equipment, events, milestones, consol associations
- Documentation: templates, shipment docs, revisions, snapshots, attachments
- Logistics: transport orders, assignments, POD
- Billing: charges, charge allocations, debit/credit/commission notes
- AR/AP: invoices/open items, receipts, payments, allocations
- Accounting: accounts, periods, journals, lines, posting rules
- Analytics: metric projections/read models

## 3. Important relationship rules
- Business Partner can have multiple roles.
- Quotation has one or more revisions; sent/accepted revisions are preserved.
- Shipping Order references accepted quotation revision.
- Shipment references source order and may have multiple cargo lines, parties, legs, bookings, documents, charges and events.
- Consol ↔ house shipments use an explicit association entity; don't assume a one-to-one relationship.
- Booking/shipment cardinality should be validated against actual operational practice before final ERD.
- Charges can be split/allocated across multiple shipments or consol jobs where necessary.
- Charge-to-billing linkage must support controlled partial billing if the business requires it and prevent accidental duplicate billing.
- Invoice/open item balances derive from allocations and approved adjustments.
- Receipts/payments can be partially allocated and can leave unapplied balances.
- Posted journals are immutable and traceable to source documents.

## 4. Money and quantities
Use DECIMAL, not floating point, for money, rates, quantities and exchange rates where precision matters. Store:
- Currency code
- Original amount
- Quantity/unit/basis
- Unit rate
- Tax treatment/rate snapshot where relevant
- FX rate, effective date and source
- Functional/reporting currency and converted amount when accounting rules require
- Rounding policy

Do not assume one currency per shipment. A shipment can have revenue and costs in multiple currencies.

## 5. Snapshots
Snapshot at issuance/confirmation for:
- Quotation revision
- Shipping instruction handoff where audit requirements demand
- House/master documents
- Debit/credit/commission documents
- Invoices
- Posted journals and FX context
- Partner names/address/tax identifiers as printed

## 6. Temporal history
Use revisions/history for:
- Customer ownership assignment
- Rate changes
- Schedule updates
- Document amendments
- Booking changes
- Actual milestone correction
- Financial adjustment
- Status transition audit

## 7. Soft delete versus archival
Soft delete only for records whose business semantics permit. Issued/posted financial or transport documents should be voided/reversed/superseded, not deleted. Master data deactivation is usually preferable to deletion if referenced.

## 8. Index and integrity
Unique constraints for business identifiers, FKs for core relationships, transaction boundaries for issue/post/allocation, indexes based on observed access patterns. Evaluate query plans before broad indexing.

## 9. Initial entity inventory (not final ERD)
Core: companies, branches, departments, users, roles, permissions, business_partners, partner_roles, contacts, addresses, locations, ports, airports, currencies, exchange_rates, charge_codes, service_types, numbering_sequences, audit_events.

Sales/Rates: inquiries, quotations, quotation_revisions, quotation_lines, sales_orders, sales_order_lines, rate_sheets, rate_revisions, rate_lines, rate_conditions.

Freight: shipments, shipment_parties, cargo_items, shipment_legs, bookings, booking_shipment_links (if many-to-many), containers, container_assignments, shipment_events, shipment_milestones, consol_master, consol_house_links, cargo_receipts.

Documents: document_types, templates, shipment_documents, document_revisions, document_snapshots, document_files.

Billing/AR/AP: charges, charge_allocations, debit_notes, debit_note_lines, credit_notes, credit_note_lines, commission_notes, invoices, invoice_lines, open_items, receipts, payments, receipt_allocations, payment_allocations.

Accounting: chart_of_accounts, accounting_periods, journals, journal_lines, bank_accounts, bank_transactions, reconciliations.

Analytics: report projections/snapshots introduced only when justified.
