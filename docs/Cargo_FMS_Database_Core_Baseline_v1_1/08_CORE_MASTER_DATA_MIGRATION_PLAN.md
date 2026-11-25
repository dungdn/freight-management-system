# Core & Master Data — Migration Build Plan

This is the migration order after the owner-approved decisions dated 2026-11-24. It is not a command to generate all migrations in one large file. Remaining technical gates are tracked in `07_CORE_DECISIONS_AND_OPEN_ITEMS.md`.

## Dependency order

1. Global references: `countries`, `currencies`, `transport_modes`, `charge_categories`, `package_types`, `container_types`.
2. Organization: `companies`, `branches`, `departments`.
3. Identity: `users` (global identity, globally unique normalized email), publish/configure Spatie Permission migrations with Teams enabled and company context, then `user_company_access`, `user_branch_access`. Do not create custom duplicate roles/permissions tables.
4. Geography: `locations`, `ports`, `airports`.
5. Commercial partner: `payment_terms`, `tax_codes`, `business_partners`, `partner_roles`, `partner_contacts`, `partner_addresses`, `partner_credit_profiles`, `partner_sales_assignments`.
6. Operations reference: `service_types`, `charge_codes`.
7. FX and numbering: `exchange_rates`, `numbering_sequences` with yearly `period_key=YYYY` and normalized company/branch scope.

## Laravel migration conventions

- One table or tightly coupled pivot per migration; deterministic timestamp ordering.
- Use `$table->unsignedBigInteger()` consistently for PK/FK if using BIGINT UNSIGNED. Laravel `foreignId()` maps to unsigned BIGINT on MySQL; verify generated type.
- Name constraints explicitly for long/composite FK/index names to avoid MySQL identifier-length issues.
- Add composite unique keys needed by composite FKs before adding child constraints.
- Use `restrictOnDelete()` for referenced master/transaction data; do not cascade from company/partner.
- Add `CHECK` constraints only after verifying target MySQL version enforces them; also validate in domain services.
- Avoid MySQL `ENUM`; use constants/lookup data plus validation.
- Avoid relying on nullable-column unique behavior for sequence scope or active-primary constraints.

## Required automated tests

- Cannot create branch under a different company through FK mismatch.
- Cannot attach a partner role/contact/address to a different company than its partner.
- Cannot grant branch access without matching company access (service test).
- Unique company code, branch code, partner code, charge code, ISO codes, currency code.
- Cannot hard-delete company/partner/reference rows after use.
- FX rate must be positive; currency pair cannot be identical unless policy explicitly allows it.
- Concurrent number allocation returns distinct consecutive values under transaction/locking.
- Company A user cannot read/update Company B records, including by guessing IDs.

## Review gate before first migration PR

- [x] Owner decisions approved: company-owned partner master, global user identity, yearly numbering, Spatie Permission.
- [ ] Remaining technical gates closed: MySQL release/collation, package version, email normalization, branch access semantics.
- [ ] `05_CORE_MASTER_DATA_DETAILED_DESIGN.md` reviewed.
- [ ] `06_CORE_MASTER_DATA_ERD.md` reviewed.
- [x] RBAC package direction fixed: Spatie Permission Teams with company as team context; exact compatible version must be pinned in composer.lock.
- [ ] MySQL version and collation fixed.
- [ ] Migrations, factories, seeders, policies, repository contracts and service tests planned.
