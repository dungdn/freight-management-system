# Core & Master Data — Migration Build Plan

This is the migration order after decision-register sign-off. It is not a command to generate all migrations in one large file.

## Dependency order

1. Global references: `countries`, `currencies`, `transport_modes`, `charge_categories`, `package_types`, `container_types`.
2. Organization: `companies`, `branches`, `departments`.
3. Identity: `users`, RBAC tables (canonical package schema), `user_company_access`, `user_branch_access`.
4. Geography: `locations`, `ports`, `airports`.
5. Commercial partner: `payment_terms`, `tax_codes`, `business_partners`, `partner_roles`, `partner_contacts`, `partner_addresses`, `partner_credit_profiles`, `partner_sales_assignments`.
6. Operations reference: `service_types`, `charge_codes`.
7. FX and numbering: `exchange_rates`, `numbering_sequences`.

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

- [ ] Decision register approved.
- [ ] `05_CORE_MASTER_DATA_DETAILED_DESIGN.md` reviewed.
- [ ] `06_CORE_MASTER_DATA_ERD.md` reviewed.
- [ ] RBAC package and team/company scope fixed.
- [ ] MySQL version and collation fixed.
- [ ] Migrations, factories, seeders, policies, repository contracts and service tests planned.
