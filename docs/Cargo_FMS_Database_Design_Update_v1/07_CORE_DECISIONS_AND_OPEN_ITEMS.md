# Core Database — Decision Register

**Purpose:** Prevent unresolved business assumptions from silently becoming database constraints. Status values: `PROPOSED` (recommended, awaiting owner confirmation), `DECIDED` (approved), `OPEN` (requires business input).

| ID | Decision | Recommendation | Status |
|---|---|---|---|
| CORE-001 | Company ownership of partner master | Each legal company owns its own partner records in v1. Introduce explicit group/shared-master mapping only if cross-company sharing is a real requirement. | PROPOSED |
| CORE-002 | User identity tenancy | One global login identity (`users.email` globally unique) with explicit company and branch grants. | PROPOSED |
| CORE-003 | Company and branch access | `user_company_access` plus `user_branch_access`; deny by default; service/policy enforces both grants. | PROPOSED |
| CORE-004 | Department scope | Company-wide department allowed (`branch_id NULL`) or optionally attached to a branch. | PROPOSED |
| CORE-005 | Partner roles | One partner identity per company with multiple role rows (CUSTOMER/VENDOR/AGENT/COLOADER/CARRIER/etc.). | PROPOSED |
| CORE-006 | Partner role history | v1 keeps one row per partner+role; if historical enable/disable periods are needed, move to effective-dated role records before migrations. | PROPOSED |
| CORE-007 | Partner credit profile | Effective-dated records; limit/currency/credit days are policy settings, not AR balance. | PROPOSED |
| CORE-008 | Currency precision | Store currency metadata (`minor_unit`); amounts default DECIMAL(19,4), FX rate DECIMAL(20,10); transaction-specific rounding rules must be snapshotted. | PROPOSED |
| CORE-009 | Company base currency | Exactly one base currency per company; changing it after postings exist requires a controlled accounting migration, not a normal edit. | PROPOSED |
| CORE-010 | Numbering scope | Company + optional branch + document type + period. Use normalized non-null branch scope key or separate sequence scopes to avoid NULL uniqueness ambiguity. | OPEN |
| CORE-011 | Number reset policy | Define whether numbering resets yearly/monthly/never per document type and legal requirement. | OPEN |
| CORE-012 | Location model | Global country/location/port/airport references; local customs/terminal aliases added separately only when required. | PROPOSED |
| CORE-013 | Service taxonomy | Separate transport mode from service type; allow cross-modal services with nullable mode. | PROPOSED |
| CORE-014 | Charge taxonomy | Company-specific charge codes grouped by global category; tax/accounting mapping can be added with Finance phase. | PROPOSED |
| CORE-015 | Tax codes | Company-scoped, effective-dated; verify Vietnam tax behavior and invoice requirements before production. | PROPOSED |
| CORE-016 | Soft delete | No blanket soft deletes for master data. Prefer active/status fields and RESTRICT FKs after use. | PROPOSED |
| CORE-017 | Timestamp policy | Store UTC DATETIME(6); render/derive business date using company/branch IANA timezone. | PROPOSED |
| CORE-018 | RBAC implementation | Prefer Laravel-compatible RBAC (e.g. Spatie schema) with explicit company/team scoping; avoid duplicate custom permission tables. | OPEN |

## Minimum sign-off before migrations

1. Confirm CORE-001 through CORE-005 (company, user, branch, partner model).
2. Confirm CORE-010 and CORE-011 (numbering scope/reset) for at least the first document types.
3. Choose RBAC implementation and its exact schema (CORE-018).
4. Confirm database engine/version, collation, UTC connection/session policy.
5. Confirm whether companies in this product are separate legal entities in one installation or one company per installation.

## Suggested decisions for the project owner

The recommended default is a single installation supporting multiple legal companies, with global user identities, company-owned partner master data, explicit access grants, and independent numbering per company/document type. This is the least surprising model for a forwarding business with branches or subsidiaries while avoiding accidental data sharing.
