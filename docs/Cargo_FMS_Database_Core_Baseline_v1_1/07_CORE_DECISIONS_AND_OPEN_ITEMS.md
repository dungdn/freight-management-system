# Core Database — Decision Register

**Version:** 1.1 · **Review status:** Core decisions approved; technical implementation gates remain · **Approved:** 2026-11-24

Status: `DECIDED` = explicitly approved by project owner; `PROPOSED` = recommended but not yet confirmed; `OPEN` = requires further business/technical input.

## 1. Approved decisions

| ID | Decision | Approved baseline | Status |
|---|---|---|---|
| CORE-001 | Partner master ownership | Each Company owns its own `business_partners` records. No implicit sharing across companies in v1. | DECIDED |
| CORE-002 | User identity tenancy | Global user identity. A single `users` row/login may be granted access to multiple companies. `users.email` is globally unique under the selected login policy. | DECIDED |
| CORE-003 | Company and branch access | Explicit `user_company_access` and `user_branch_access`; deny by default. Branch access requires active company access. Enforce in policies/services, not UI alone. | DECIDED |
| CORE-004 | Department scope | Departments may be company-wide (`branch_id IS NULL`) or branch-attached. | PROPOSED |
| CORE-005 | Partner roles | One partner identity per company, with multiple role rows such as CUSTOMER, VENDOR, AGENT, COLOADER, CARRIER. | DECIDED |
| CORE-006 | Partner role history | One role row per partner+role in v1; effective-dated role history is deferred unless business requires it before launch. | PROPOSED |
| CORE-007 | Partner credit profile | Effective-dated records; credit limit/currency/days are commercial policy, not an AR balance. | PROPOSED |
| CORE-008 | Currency precision | Currency metadata includes `minor_unit`; amount baseline DECIMAL(19,4), FX rate DECIMAL(20,10); transaction rounding context is snapshotted. | PROPOSED |
| CORE-009 | Company base currency | Exactly one base currency per company; changing after financial postings requires controlled migration. | PROPOSED |
| CORE-010 | Numbering scope | Company + optional branch + document type + calendar year period. Company-wide scope is represented by a normalized non-null branch scope key (or equivalent separate scope table), avoiding nullable unique-key ambiguity. | DECIDED |
| CORE-011 | Number reset policy | Yearly reset. Period key is calendar year (`YYYY`) in the owning company/branch business timezone. | DECIDED |
| CORE-012 | Location model | Global country/location/port/airport references; company-specific customs/terminal aliases use extension tables if required. | PROPOSED |
| CORE-013 | Service taxonomy | Transport mode is separate from service type; service type may have no single mode for cross-modal services. | PROPOSED |
| CORE-014 | Charge taxonomy | Company-specific charge codes grouped by global charge category. | PROPOSED |
| CORE-015 | Tax codes | Company-scoped, effective-dated; Vietnam tax behavior and invoice requirements must be verified before production. | PROPOSED |
| CORE-016 | Soft delete | No blanket soft deletes for master data. Prefer active/status fields and RESTRICT FKs after use. | PROPOSED |
| CORE-017 | Timestamp policy | Store UTC DATETIME(6); interpret business dates using company/branch IANA timezone. | PROPOSED |
| CORE-018 | RBAC implementation | Use Spatie Laravel Permission with its Teams feature configured for company context. Do not create a parallel custom roles/permissions schema. Branch grants remain a separate application authorization concern. | DECIDED |

## 2. Implementation interpretation

### Partner ownership
- `business_partners.company_id` is mandatory.
- Partner code uniqueness is `(company_id, partner_code)`.
- Partner children carry `company_id` and use composite FKs where supported to ensure parent and child belong to the same company.
- If cross-company sharing is required in a later release, introduce an explicit shared/global partner identity and company-specific profiles/mappings. Do not remove `company_id` or silently share rows.

### Global user identity and access
- `users` is platform-global; do not add `company_id` to `users`.
- `users.email` is globally unique for the selected email-login flow. Normalize email consistently before persistence; decide case-insensitive collation explicitly.
- `user_company_access` grants membership in a company; `user_branch_access` narrows branch access.
- A user with company access but no branch grant must follow an explicit company-wide access policy; do not interpret missing branch rows inconsistently. Recommended default: company-wide access is controlled by `access_level`/permission and branch-specific resources still require branch authorization.
- Revoke/deactivate grants rather than deleting audit-relevant access history.

### Spatie teams/company context
- Enable Spatie Permission `teams` mode with `team_foreign_key` representing the active `company_id` context.
- Configure team context before role/permission checks and assignments; reset it between requests/jobs to prevent context leakage.
- Roles are company-contextual. Global platform administration, if needed, should be a separately named platform guard/role strategy rather than a role with an ambiguous NULL company scope.
- Use the package's published migrations as the schema authority for `roles`, `model_has_roles`, `model_has_permissions`, and `role_has_permissions`; do not hand-maintain a duplicate schema. Confirm the installed package version and generated migration before freezing exact indexes/FKs.
- `user_branch_access` and branch-aware policies are custom application authorization; Spatie company teams do not replace them.

### Yearly numbering
- Numbering scope: `(company_id, branch_scope_id, document_type, period_key)`.
- `branch_scope_id` is non-null; reserve `0` for company-wide numbering and use a real branch ID for branch-specific numbering. Do not create an FK from sentinel `0`; validate non-zero branch IDs against the same company in the numbering service or use a normalized scope table if stronger FK enforcement is required.
- `period_key` is four-digit calendar year, e.g. `2026`, calculated in the company's business timezone.
- `reset_policy` is not a per-row user-editable toggle in v1; yearly is the approved policy. Future document types may require a different policy only through a documented decision.
- Allocate under a DB transaction/row lock (or atomic update), never `MAX(number)+1`. Persist the generated business number on the issued document with a unique constraint matching its scope.

## 3. Remaining open items before production migrations

| ID | Item | Required action |
|---|---|---|
| OPEN-001 | Exact MySQL release | Select supported MySQL 8.x patch and verify CHECK, collation, FK behavior. |
| OPEN-002 | Laravel/PHP/package lock | Confirm Laravel 13, PHP 8.4 and compatible Spatie Permission release from package metadata/lockfile at implementation time. |
| OPEN-003 | Email collation and normalization | Decide case-insensitive uniqueness strategy (`*_0900_ai_ci` or normalized `email_canonical`) and test it. |
| OPEN-004 | Branch access semantics | Confirm whether company membership grants all branches by default or requires explicit branch grants. Recommended deny-by-default for branch-scoped records. |
| OPEN-005 | Number formats | Approve per-document-type prefix, padding, branch inclusion, and sample output; yearly reset itself is decided. |
| OPEN-006 | Company legal/accounting policy | Confirm whether each Company represents a legal entity and whether base currency can be changed before first posting only. |
| OPEN-007 | Global reference maintenance | Define who can create/update global countries, currencies, ports, airports, package/container types. |
| OPEN-008 | Vietnam tax/document rules | Validate tax codes, invoice requirements and local statutory formats with current authoritative requirements. |

## 4. Sign-off summary

**Approved by project owner on 2026-11-24:** CORE-001, CORE-002, CORE-003, CORE-005, CORE-010, CORE-011, CORE-018.

These decisions are sufficient to proceed to a final technical schema review and then implement the first migration batch, provided OPEN-001 through OPEN-004 are resolved as implementation configuration decisions. They are not a claim that the full Cargo FMS schema or statutory accounting design is production-certified.
