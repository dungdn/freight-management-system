# Cargo FMS Architecture Pack — v2 (Domain & Boundaries Baseline)

This pack is the proposed decision baseline for Cargo FMS. It consolidates and **makes explicit** four major design decisions:
1. Business Domain and terminology
2. Feature Specification and release scope
3. Shipment lifecycle and state-transition rules
4. Architecture boundaries and module ownership

These are architecture proposals to review and accept—not legal, customs, tax, or operational certification. Vietnam-specific forms, e-manifest, port/airport releases, tax/accounting rules, and actual forwarder SOPs must be validated with domain experts and current official specifications.

## Included documents
- `00_DECISION_SUMMARY.md` — decisions at a glance, what is frozen for the baseline
- `01_BUSINESS_DOMAIN.md` — domain glossary, actors, concepts, operating model
- `02_FEATURE_SPECIFICATION.md` — detailed module inventory and MVP / later scope
- `03_SHIPMENT_LIFECYCLE.md` — lifecycle model, statuses, events, transitions, exceptions
- `04_ARCHITECTURE_BOUNDARIES.md` — module ownership, dependencies, contracts, rules
- `05_SYSTEM_ARCHITECTURE.md` — architecture style, layers, deployment topology
- `06_DATA_MODEL_PRINCIPLES.md` — entity design principles and key relationships
- `07_ADR.md` — architecture decision records
- `08_IMPLEMENTATION_ROADMAP.md` — delivery stages and exit criteria
- `09_OPEN_QUESTIONS.md` — decisions requiring domain validation

## Suggested review order
1. Read `00_DECISION_SUMMARY.md`
2. Review/accept `01_BUSINESS_DOMAIN.md`
3. Review scope and prioritization in `02_FEATURE_SPECIFICATION.md`
4. Validate lifecycle in `03_SHIPMENT_LIFECYCLE.md`
5. Confirm boundaries in `04_ARCHITECTURE_BOUNDARIES.md`
6. Use the rest as implementation guardrails

## Status
Version 2.0 — Architecture baseline proposal. Change any decision by recording the rationale in `07_ADR.md` rather than silently drifting.
