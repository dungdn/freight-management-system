# Architecture Decision Records

Status values: Proposed / Accepted / Rejected / Superseded. Decisions become accepted only after review.

## ADR-001 — Modular Monolith
**Status:** Proposed  
**Decision:** Begin as a modular monolith, not microservices.  
**Why:** Solo/small-team delivery, transactional workflows, operational domain still evolving.  
**Trade-off:** Requires discipline to maintain boundaries in one codebase.

## ADR-002 — Laravel + Livewire UI
**Status:** Proposed  
**Decision:** Laravel with Blade, Livewire, Alpine and Tailwind.  
**Why:** Data-entry, tables, workflow, approval and document screens dominate.  
**Trade-off:** Highly interactive/complex visualizations may need JS components later.

## ADR-003 — MySQL
**Status:** Proposed  
**Decision:** MySQL 8.x as primary transactional DB.  
**Why:** Relational business data, integrity, transaction support, Laravel familiarity.  
**Trade-off:** Reporting queries require deliberate indexing and possibly projections.

## ADR-004 — Shipment is Operational Core, not Universal Aggregate
**Status:** Proposed  
**Decision:** Shipment anchors operations but charges, billing, AR/AP and GL have their own modules.  
**Why:** Prevent a monolithic Shipment record and avoid finance lifecycle coupling.

## ADR-005 — Repository Contracts
**Status:** Proposed  
**Decision:** Application code depends on module repository contracts; Eloquent is an infrastructure implementation. Livewire cannot access repositories or Eloquent directly.  
**Why:** Testability, consistent boundaries, controlled query behavior.  
**Trade-off:** More files and interfaces; keep abstractions purposeful.

## ADR-006 — Issued/Posted Record Protection
**Status:** Proposed  
**Decision:** Issued documents and posted journals are immutable except through controlled amendment, void or reversal.  
**Why:** Auditability and financial integrity.

## ADR-007 — Phase Accounting
**Status:** Proposed  
**Decision:** Shipment charges, billing documents and profit report precede full GL/statutory accounting.  
**Why:** Deliver core business value before the large accounting scope.

## ADR-008 — Separate Operational and Financial Closure
**Status:** Proposed  
**Decision:** Operational closure and financial closure are separate workflows/states.  
**Why:** Shipment execution may finish before invoices and settlements do.

## ADR-009 — Shipment Lifecycle is Coarse State + Events
**Status:** Proposed  
**Decision:** Keep shipment coarse lifecycle and record actual execution as milestones/events.  
**Why:** Different service types do not follow a single exact linear workflow.

## ADR-010 — No External Format Assumptions
**Status:** Proposed  
**Decision:** Customs, e-manifest, port/airport, invoice/tax format mappings are versioned adapters subject to current validation.  
**Why:** Requirements can change and vary by authority/provider.
