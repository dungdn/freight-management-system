# Feature Specification — Cargo FMS

Legend: **P0 Foundation**, **P1 MVP core**, **P2 expansion**, **P3 advanced/integration**. Priorities indicate recommended sequence, not a claim that all features are required for the first usable release.

## 0. Foundation & Administration (P0)
- Company profile, branches, departments, teams
- Users, roles, permissions, data-scope rules
- Business partner master and role profiles
- Contact/address management
- Countries, ports, airports, locations, routes
- Carriers, airlines, agents, co-loaders, vendors, trucking providers
- Service/mode, container/package types, commodity codes (reference data)
- Charge catalogue, units/basis, currencies, exchange rates, payment terms, tax codes
- Business-numbering configuration
- Document storage metadata, audit trail, notifications
- Approval configuration (simple role-based first)
- Import/export framework and template downloads

## 1. Sales / CRM (P1)
### Customer ownership
- Assign customer to salesperson/team
- Effective-date assignment history
- Manager access to team/customer portfolio
- Reassignment audit

### Inquiry
- Inquiry source, customer/prospect, service, direction, route, cargo summary, requested dates
- Owner, follow-up, notes, status, loss reason
- Convert to quotation

### Quotation
- Header and itemized charges
- Sell price; estimated buy cost/margin visibility permission-gated
- Currency, validity, payment terms, scope/conditions, exclusions
- Rate lookup and manual overrides with reason
- Approval thresholds (initially configurable/simple)
- Revision numbering and immutable sent snapshots
- Sent, accepted, rejected, expired, withdrawn lifecycle
- Customer acceptance evidence/notes
- Convert accepted quote into Shipping Order

### Sales Order / Shipping Order
- Structured handoff to operations/docs
- Source quotation/revision, customer, service, cargo, route, requested dates, instructions
- Missing-data checklist
- Handoff acceptance/return for clarification
- Idempotent shipment creation
- Sales activity history

### Sales reports
- Inquiry/quotation pipeline
- Win/loss/conversion
- Shipment count/volume by salesperson/customer/service
- Revenue and profit measures with clear definitions and permission checks

## 2. Rate Management (P1/P2)
- Rate sheet and revision
- Buy/sell side, charge, currency, basis, validity period
- Origin/destination and route scope
- Carrier/agent/co-loader scope
- Customer/contract/spot scope
- Equipment/service/mode scope
- Conditions, surcharges, exclusions
- Rate import/export and validation
- Rate comparison and expiry reminders
- Rate snapshot into quotation/accepted order
- Approval/history for rate changes

## 3. Freight Operations – Common (P1)
- Create Shipment from Shipping Order or manually
- Unique Job/Shipment number
- Direction, mode, service, branch/department ownership
- Customer and party roles with snapshots where document issuance needs them
- Cargo line items: packages, kind, marks, description, weight, volume, dimensions
- Container/equipment and seal tracking as applicable
- Route and transport legs; transshipment support
- Booking(s), provider, reference, cutoffs
- Milestone timeline with planned/actual dates, location, source and notes
- Exceptions/holds, owner, due date, resolution
- Operational notes and references
- Attachments/document checklist
- Shipment search/filter and list/detail views
- Operational close checklist and closure/reopen permissions

## 4. Inbound Ocean – P1/P2
- FCL inbound core workflow (P1)
- LCL/consol inbound cargo association (P2)
- Master/house B/L reference and document data (P1/P2)
- Arrival details and arrival notice (P1)
- Delivery Order workflow (P1; configurable checks)
- Authorization letters/receiver authorization documents (P2)
- Cargo release/delivery coordination events (P1)
- POD capture (P1)
- CFS list export and customs/e-manifest exports (P2/P3 after format validation)
- Inbound charges/costs, profit, AR/AP views (P1)

## 5. Outbound Ocean – P1/P2
- Booking request, confirmation, amendment, cancellation/rollover (P1)
- Carrier/co-loader and routing/schedule (P1)
- Cargo pickup/receiving and cutoffs (P1)
- Shipping Instruction capture, review and submission tracking (P1)
- HBL draft/issue/amend (P1)
- MBL reference/capture and master document handling (P1/P2)
- Cargo manifest generation (P1/P2)
- Shipping Advice generation (P1)
- Departure and milestone tracking (P1)
- FCL first; LCL/consol outbound expanded in P2
- Charges, billing, costs, profit and report linkage (P1)

## 6. Consolidation / LCL / Co-load (P2)
- Consol master record and planned movement
- House shipment association
- CFS receiving and cargo receipt
- Cargo measurement/weight verification and discrepancy record
- Loading/manifest
- Master-house document links
- Shared cost/revenue allocation basis
- Consol closing and arrival/deconsol records
- Co-loader settlement

## 7. Air Freight (P2)
- Air import/export jobs
- Airline/agent booking
- MAWB/HAWB
- Flight legs, cutoffs, routing
- Cargo weight, volume and chargeable-weight rule
- Manifest/doc generation
- Milestones/tracking
- Air-specific charges and profitability

## 8. Schedule & Routing (P1)
- Carrier/vessel/voyage/route master
- Monthly schedule entry/import
- Schedule revisions (retain prior version)
- Multiple legs/transshipment
- ETD/ETA and actual ATD/ATA
- Search and calendar/table views
- Print/export schedule
- Delay notifications (P2)

## 9. Logistics & Transport Coordination (P1/P2)
- Transport order linked to shipment or standalone service
- Pickup/delivery locations and windows
- Transport vendor/driver/vehicle reference (not own-fleet dispatch initially)
- Assignment, instructions, status and exceptions
- Estimated/actual buy cost and customer charge
- POD upload
- Transport service billing
- Later: fleet, dispatch optimization, GPS, driver app

## 10. Documentation (P1)
- Document templates and type configuration
- Data field mappings
- Draft preview, review, approval, issue
- Versioning and amendment/cancellation/superseding
- Snapshot of issued values and file checksum
- PDF generation and private file storage
- Attachment checklist
- HBL, Arrival Notice, DO, Shipping Advice, SI/Manifest progressively
- Export mapping/versioning for official formats, after validation

## 11. Charges, Billing & Shipment Accounting (P1)
- Charge codes and categories
- Revenue (sell) and cost (buy) lines
- Party, source, basis, quantity, rate, currency, tax
- Estimated/accrued/confirmed/actual and billed/paid distinctions
- Debit Note and Credit Note lifecycle
- Commission Note
- Partial billing rules if required
- Billing approval/issue
- Duplicate-source prevention
- Charge allocation across shipments/consol
- Shipment profitability: estimated, accrued, actual; disclose missing costs
- Job closing review

## 12. AR/AP (P1/P2)
- Customer/vendor invoice/open items
- Due dates and payment terms
- Receipt and payment entry
- Partial settlement and multiple allocations
- Unallocated receipts/payments
- Aging reports
- Statement/reconciliation
- Write-off and reversal controls (P2)
- Currency/exchange difference handling (P2)

## 13. Financial Accounting (P3)
- Chart of Accounts
- Fiscal years and accounting periods
- Journal entries/lines
- Posting rules and source mapping
- Double-entry validation
- General ledger and trial balance
- Cash/bank accounts
- Bank reconciliation
- VAT reports and tax mapping
- Fixed assets, depreciation
- Prepaid expense amortization
- Cost centers and dimensions
- Period close/lock
- Financial statements
- Statutory accounting/tax validations with qualified domain expertise

## 14. Management / Analytics (P1/P2)
- Shipment count and volume
- Shipment by mode/service/direction/route/branch
- Revenue/cost/profit and margin
- Profit by shipment, customer, salesperson, agent, carrier, route, department, branch
- Sales pipeline and conversion
- Operations milestones/delay/exception report
- AR/AP aging
- Agent/co-loader settlement
- Filters, exports, role scope
- Document metric definitions and reconciliation
- Later: reporting data mart/BI and predictive analytics

## 15. Integration & External Access (P3)
- Versioned API
- Carrier/airline/agent adapters
- EDI exchange
- Customs/port/CFS format adapters and certification
- Email integration
- Webhooks
- Customer/agent portal
- Retry queue, dead-letter review, idempotency, monitoring, reconciliation

## 16. Non-functional requirements
- Security and record-level scope
- Auditability and traceability
- Availability, backup, restore drills
- Performance budgets and query plans
- Accessibility and responsive UX
- Localization (Vietnamese/English), timezone and currency
- Maintainability, testability, deployment/rollback
- Observability (logs, metrics, queue failures)
- Data retention and privacy policies
