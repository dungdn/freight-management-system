> **Core ERD supersession:** For Core & Master Data cardinalities and constraints, use `06_CORE_MASTER_DATA_ERD.md`. This file remains the conceptual cross-domain overview.

# Conceptual ERD

These diagrams express high-level relationships only; cardinalities and exact FK choices must be reviewed before migrations.


## Core + Sales + Shipment
```mermaid
erDiagram
  COMPANY ||--o{ BRANCH : has
  COMPANY ||--o{ BUSINESS_PARTNER : owns_or_shares
  BUSINESS_PARTNER ||--o{ PARTNER_ROLE : has
  BUSINESS_PARTNER ||--o{ PARTNER_CONTACT : has
  BUSINESS_PARTNER ||--o{ PARTNER_ADDRESS : has
  BRANCH ||--o{ SALES_INQUIRY : handles
  BUSINESS_PARTNER ||--o{ SALES_INQUIRY : requests
  SALES_INQUIRY ||--o{ QUOTATION : receives
  QUOTATION ||--o{ QUOTATION_REVISION : versions
  QUOTATION_REVISION ||--o{ QUOTATION_LINE : contains
  QUOTATION ||--o| SALES_ORDER : accepted_as
  SALES_ORDER ||--o{ SALES_ORDER_LINE : contains
  SALES_ORDER ||--o{ HANDOFF : sends_to_ops
  SALES_ORDER ||--o{ SHIPMENT : may_create
  COMPANY ||--o{ SHIPMENT : owns
  SHIPMENT ||--o{ SHIPMENT_PARTY : has
  SHIPMENT ||--o{ CARGO_ITEM : contains
  SHIPMENT ||--o{ SHIPMENT_LEG : routes
  SHIPMENT ||--o{ SHIPMENT_EVENT : timeline
  SHIPMENT ||--o{ BOOKING_LINK : booking_relation
  BOOKING ||--o{ BOOKING_LINK : links
  SHIPMENT ||--o{ SHIPMENT_DOCUMENT : documents
  SHIPMENT ||--o{ CHARGE : accrues
```

## Billing / AR/AP (conceptual)
```mermaid
erDiagram
  SHIPMENT ||--o{ CHARGE : incurs
  CHARGE ||--o{ CHARGE_ALLOCATION : allocated
  DEBIT_NOTE ||--o{ DEBIT_NOTE_LINE : contains
  CREDIT_NOTE ||--o{ CREDIT_NOTE_LINE : contains
  INVOICE ||--o{ INVOICE_LINE : contains
  INVOICE ||--o{ OPEN_ITEM : creates
  RECEIPT ||--o{ RECEIPT_ALLOCATION : applies
  OPEN_ITEM ||--o{ RECEIPT_ALLOCATION : settled_by
  PAYMENT ||--o{ PAYMENT_ALLOCATION : applies
  OPEN_ITEM ||--o{ PAYMENT_ALLOCATION : settled_by
  JOURNAL_ENTRY ||--o{ JOURNAL_LINE : contains
```

## Relationship guidance
- Prefer explicit link tables for many-to-many relations and historical assignments.
- `shipment_parties` should preserve role and snapshot fields; a partner FK alone is insufficient to preserve what appeared on a document.
- Booking may link multiple shipments only if the operational use case requires shared booking; otherwise simplify to a direct FK initially.
- Consolidation is not the same thing as shipment; link shipments and retain allocation audit records.
