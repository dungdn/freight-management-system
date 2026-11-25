# Implementation Roadmap

## Stage 0 — Domain confirmation
Confirm customer type, initial service, terminology, shipment lifecycle, booking/document practices, billing policy, and reports. Gather anonymized sample documents/forms and validate official formats separately.

**Exit:** domain decisions and MVP scope reviewed.

## Stage 1 — Foundation
- Laravel/Docker setup, CI, code quality
- Authentication, users, roles, permissions, data scopes
- Organization/branch/department
- Partner master/roles/contacts
- Reference data and numbering
- Audit, notifications, file storage abstraction
- Reusable UI components
**Exit:** foundation feature tests and authorization/data-scope tests pass.

## Stage 2 — Sales to Shipment
- Inquiry, customer assignment, sales pipeline
- Rate sheet basics
- Quotation revisions/approval/send/accept
- Shipping Order/handoff
- Shipment creation idempotently
**Exit:** accepted quotation produces one shipment with complete source traceability.

## Stage 3 — Core Ocean Export FCL (recommended first workflow)
- Shipment detail, parties, cargo, routing
- Booking request/confirmation/amendment
- Container/equipment references
- Cut-offs, SI, B/L draft data
- Events/milestones
- Document framework baseline
**Exit:** one export job traceable from booking through departure/documentation.

## Stage 4 — Core Ocean Import FCL
- Arrival/ETA
- Arrival Notice
- DO/release process
- Delivery coordination and POD
- Import charges/costs
**Exit:** import workflow completes with traceable document and event history.

## Stage 5 — Charges & Profit
- Charge catalogue
- Revenue/cost lines
- Debit/credit/commission notes
- Approval/issue
- Multi-currency baseline
- Profit projection and missing-cost warnings
**Exit:** shipment-level profitability reconciles with source charge records.

## Stage 6 — AR/AP Core
- Customer/vendor invoice or open-item creation
- Receipts/payments
- Partial/multiple allocation
- Unallocated balances
- Aging and statement
**Exit:** balances reconcile to invoice/open-item allocation model.

## Stage 7 — LCL/Consol & Logistics
- Consol/master association
- CFS cargo receipt and discrepancy
- Charge allocation
- Transport order and POD
**Exit:** multi-house consol operation and transport coordination tested.

## Stage 8 — Air Freight
- MAWB/HAWB, airline, flight legs
- Weight/chargeable weight rules
- Air milestones, docs, charges
**Exit:** air-specific workflow and billing calculations verified.

## Stage 9 — Full Financial Accounting
COA, periods, journals, posting, GL, trial balance, bank reconciliation, VAT reports, assets/prepaid, period close, financial statements—subject to accountant review.
**Exit:** balanced posting, reconciliation and close controls tested.

## Stage 10 — External integrations & production hardening
Versioned formats, carrier/agent/port/CFS/customs integrations, portals, security, restore drills, performance, monitoring, deployment and acceptance.

## MVP recommendation
Treat Stage 1 through a slice of Stage 5 as the first demonstrable MVP. Prioritize a single workflow end-to-end rather than superficial CRUD for every department.
