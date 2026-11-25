# Business Domain — Cargo FMS

## 1. Product definition
Cargo FMS is an operational and commercial management system for freight-forwarding, NVOCC and logistics service businesses. It coordinates sales, pricing, shipment execution, shipping documents, charges, customer billing, vendor costs, settlement and reporting.

It is **not initially** a full customs-declaration platform, carrier network, warehouse management system, own-fleet TMS, or statutory accounting replacement.

## 2. Business operating model
Typical business flow:
1. Sales receives inquiry and confirms cargo/service requirements.
2. Sales prepares pricing based on sell rates and estimated buy costs.
3. Customer accepts a quotation or sends shipping instruction/order.
4. Sales hands over a structured Shipping Order to Operations/Documentation.
5. Operations opens and manages a Shipment/Job, booking, route, cargo, milestones and exceptions.
6. Documentation prepares and issues applicable shipping documents.
7. Charges and vendor costs are captured, checked, and billed.
8. Accounting tracks invoices, receipts/payments, open items, allocations and settlement.
9. Shipment is reviewed and closed operationally and/or financially under separate criteria.
10. Management analyzes volume, revenue, costs, profit, aging, service performance and sales performance.

## 3. Primary actors
- Sales Executive / Sales Representative
- Sales Supervisor / Manager
- Operations staff
- Documentation staff
- Logistics coordinator
- Billing staff
- Shipment accountant
- AR/AP accountant
- Financial accountant
- Finance manager
- Branch/department manager
- Management
- System administrator

External parties include customers, shippers, consignees, notify parties, shipping lines, airlines, overseas agents, co-loaders, vendors, trucking providers, warehouses/CFS, ports and airports.

## 4. Business Partner model
A single partner identity may have multiple roles. Do not create separate companies merely because an organization is both customer and vendor, or agent and co-loader.

### Partner roles
Customer, Shipper, Consignee, Notify Party, Agent, Co-loader, Carrier, Airline, Vendor, Trucking Provider, Warehouse/CFS Operator, Customs Service Partner.

Partner data:
- Legal/operating names and identifiers
- Contacts
- Addresses
- Role/profile information
- Payment terms and credit settings (where permitted)
- Tax and invoice attributes (subject to local validation)
- Active/inactive and risk/hold flags
- Ownership/assignment to sales team

## 5. Domain glossary
| Term | Meaning in Cargo FMS |
|---|---|
| Inquiry | Customer/prospect request for freight or logistics service |
| Quotation | Commercial offer with revision, validity, charges, terms and approval |
| Rate Sheet | Reusable buy/sell rates with applicability and effective dates |
| Shipping Order | Sales-to-operations handoff with confirmed service/cargo instructions |
| Shipment / Job | Operational file coordinating one cargo/service engagement |
| Booking | Reservation/request for transport capacity with provider |
| Transport Leg | A segment in route with mode/provider/locations/times |
| FCL | Full Container Load |
| LCL | Less-than-Container Load; consolidated handling |
| Consol | Consolidation/master movement grouping multiple house shipments |
| House B/L | House-level transport document issued by forwarder/NVOCC |
| Master B/L | Master-level document issued by carrier or master provider |
| HAWB / MAWB | House / Master Air Waybill |
| POL / POD | Port of Loading / Port of Discharge |
| ETD / ETA | Estimated departure / arrival |
| ATD / ATA | Actual departure / arrival |
| SI | Shipping Instruction |
| Cargo Manifest | Cargo summary for a movement/document |
| CFS | Container Freight Station |
| Arrival Notice | Notice of expected/actual cargo arrival |
| Delivery Order | Release/delivery instruction under applicable process |
| POD | Proof of Delivery |
| Charge | Revenue or cost item (freight, handling, docs, THC, etc.) |
| Debit Note | Document/record of charge billed to a customer/party |
| Credit Note | Credit/adjustment document; exact semantics depend on company workflow |
| Commission Note | Commission payable/receivable record under business rules |
| AR | Accounts Receivable |
| AP | Accounts Payable |
| Open Item | Invoice/financial balance not yet fully settled |
| Allocation | Matching receipt/payment amount to one or more open items |
| Job Profit | Defined revenue less defined costs for a shipment/job |
| Accrual | Estimated cost/revenue recorded pending final document/amount |
| Operational Closure | Operations declares execution complete with exceptions reviewed |
| Financial Closure | Finance declares billing/cost/settlement review complete, subject to policy |

## 6. Shipment dimensions (must not collapse into one field)
Shipment attributes include:
- Direction: Import/Inbound, Export/Outbound, Domestic/Other
- Mode: Ocean, Air, Road, Rail (initial supported modes are phased)
- Service: FCL, LCL, Air, Logistics, etc.
- Parties: customer, shipper, consignee, notify, agent, carrier, vendor
- Cargo: package, commodity, weight, volume, dimensions, container/equipment
- Routing: locations and ordered transport legs
- Commercial: quotation/order/rate references
- Booking: booking(s), providers, cutoffs
- Documents: house/master relationships, issued revisions
- Execution: milestones and events
- Financial: revenue/cost charges, billing, invoices, open items
- Ownership: company, branch, department, sales, operations owner

## 7. Service taxonomy
- Ocean FCL Import
- Ocean FCL Export
- Ocean LCL Import
- Ocean LCL Export
- Consol / Co-load (inbound or outbound)
- Air Import
- Air Export
- Logistics / local transport coordination
- Additional services to be added through configurable service catalogue

Direction, mode, and service type are distinct fields, not one combined free-text label.

## 8. Operational records versus financial records
Shipment is an operational job. Charges, billing documents, invoices, receipts/payments, open items and journal entries are separate financial records linked back to the shipment/job.

A shipment may be operationally completed while still having outstanding receivables or payables. Operational and financial closure therefore have separate states/checklists.

## 9. Vietnam localization
Vietnam customs, port, airport, CFS, e-manifest, invoice, tax, and accounting formats are requirements that need current official documentation and partner testing. Implement a versioned export/import mapping layer. Do not encode an unverified format as an unchangeable core domain rule.
