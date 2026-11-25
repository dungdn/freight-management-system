# Owner Decision Sign-off — Core Database

**Date:** 2026-11-24  
**Decision status:** Approved by project owner

| Topic | Approved choice | Database consequence |
|---|---|---|
| Partner Master | `company` | `business_partners.company_id NOT NULL`; UQ `(company_id, partner_code)`; no implicit cross-company sharing |
| User Identity | `global` | Global `users`; globally unique normalized email; company and branch grants in access tables |
| Numbering | `yearly` | `period_key CHAR(4)` as calendar year in company timezone; scope by company + optional branch + document type |
| RBAC | `spatie` | Spatie Laravel Permission with Teams enabled; company is team context; branch access separately enforced |
| Additional notes | None | No additional exception or constraint supplied |

## Implementation cautions
1. Spatie's published migration is canonical for package-owned tables. Pin a Laravel-compatible release and inspect its generated schema; do not duplicate those tables manually.
2. Set/reset active team context safely for HTTP requests, jobs, commands and tests.
3. Global identity does not mean global data access. Every company-owned query and command must enforce explicit company grants; branch-scoped data must also enforce branch rules.
4. Yearly reset is approved; document-specific prefix, padding and branch-in-number format still require configuration.
5. Partner records are company-owned. Future shared partner data requires an explicit global identity/profile mapping model and migration plan.

## Sign-off boundary
This approval closes the four decisions presented to the owner. It does not approve every proposed master-data attribute, tax rule, accounting policy, MySQL collation or statutory document format. See `07_CORE_DECISIONS_AND_OPEN_ITEMS.md` for remaining gates.
