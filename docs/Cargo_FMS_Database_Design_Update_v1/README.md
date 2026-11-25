# Cargo FMS — Database Design Pack (Core Review Update)

**Pack version:** Core & Master Data Review v1.0  
**Status:** Proposed baseline pending business sign-off; not production-ready.  
**Target:** Laravel + MySQL 8.x / InnoDB / utf8mb4.

## Files
- `00_DATABASE_BASELINE.md` — overall database architecture baseline.
- `01_DOMAIN_TABLE_CATALOG.md` — table catalog across all Cargo FMS domains.
- `02_RELATIONSHIPS_ERD.md` — conceptual cross-domain ERD.
- `03_DATABASE_RULES.md` — shared database standards.
- `04_IMPLEMENTATION_SEQUENCE.md` — phased project implementation sequence.
- `05_CORE_MASTER_DATA_DETAILED_DESIGN.md` — detailed Core table specs: PK, FK, types, nullable, unique/indexes, ownership, deletion policy.
- `06_CORE_MASTER_DATA_ERD.md` — detailed logical Core ERD and cardinalities.
- `07_CORE_DECISIONS_AND_OPEN_ITEMS.md` — decisions awaiting owner approval and sign-off checklist.
- `08_CORE_MASTER_DATA_MIGRATION_PLAN.md` — migration dependency order and test gates.
- `09_CORE_MASTER_DATA_SCHEMA_REFERENCE.sql` — compact reference DDL for selected foundational tables; review draft only.
- `schema_draft.sql` — earlier broad starter DDL for Core/Sales/Freight; retain for historical reference, not canonical Core schema.

## Recommended reading order
1. `00_DATABASE_BASELINE.md` and `03_DATABASE_RULES.md`
2. `05_CORE_MASTER_DATA_DETAILED_DESIGN.md`
3. `06_CORE_MASTER_DATA_ERD.md`
4. Resolve `07_CORE_DECISIONS_AND_OPEN_ITEMS.md`
5. Follow `08_CORE_MASTER_DATA_MIGRATION_PLAN.md`

## Important limitations
The SQL files are design references, not production migrations. The compact Core reference DDL intentionally omits some tables whose specifications are in the detailed design. It has not been executed against a live MySQL server in this environment. Confirm exact MySQL release, collation, RBAC package/schema, business decisions, and Laravel compatibility before implementation.
