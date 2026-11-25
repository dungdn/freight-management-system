# System Architecture — Cargo FMS

## 1. Style
**Modular monolith**: single Laravel application, one deployment unit, one primary MySQL database initially, internal module ownership and contracts.

## 2. Layered model
```text
Presentation: Blade / Livewire / Alpine / Tailwind
    ↓
Application: Actions / Use Cases / Application Services / DTOs
    ↓
Domain: Rules / Policies / Value Objects / State transitions
    ↓
Contracts: Repository interfaces, module service interfaces, events
    ↓
Infrastructure: Eloquent repositories, queue, mail, storage, adapters
    ↓
MySQL / Redis / filesystem or object storage / external systems
```

The diagram represents responsibility, not a requirement that every CRUD path contain five classes.

## 3. Layer rules
### Presentation
- UI state, validation feedback, authorization boundary and use-case invocation.
- No direct Eloquent/SQL/repository calls.
- No financial or lifecycle business logic embedded in component methods or Blade.

### Application
- Use case orchestration
- Authorization and policy checks
- Transaction boundaries
- Cross-module calls through contracts
- Idempotency
- Domain event/outbox handling
- DTO/result mapping

### Domain
- Invariants, calculations, transitions, policies, money/quantity rules
- Framework-independent where useful, pragmatic Laravel integration acceptable

### Contracts
- Interfaces and DTOs between layers/modules
- Avoid passing Eloquent models as public module contracts
- Contracts should express intent, not leak persistence details

### Infrastructure
- Eloquent and query implementations
- Queue jobs, storage adapters, mail, integration clients
- Dependency injection bindings

## 4. Recommended stack
- PHP: current supported PHP release compatible with selected Laravel
- Laravel: current stable major after package compatibility review; project baseline target may be Laravel 13 if supported at build start
- UI: Blade + Livewire + Alpine.js
- CSS: Tailwind
- DB: MySQL 8.x
- Queue/cache/session: Redis
- Queue monitoring: Horizon if Redis queues
- Assets: Vite + Node LTS
- PDF: select renderer after document layout proof-of-concept
- Excel: PhpSpreadsheet/Laravel Excel, queue large exports
- Tests: PHPUnit; Playwright for browser E2E
- Static analysis: Larastan/PHPStan; Pint formatter
- Containers: Docker Compose
- CI: GitLab CI or GitHub Actions
- Mail testing: Mailpit local
- Error tracking/metrics: Sentry-compatible service plus structured logs

Version pinning is deferred until project creation; confirm official compatibility and package maintenance status.

## 5. Local development topology
```text
Developer browser
   │
Apache/Nginx (container)
   │
Laravel/PHP app
   ├── MySQL
   ├── Redis
   ├── Queue worker
   ├── Scheduler
   ├── Private file storage
   └── Mailpit
Node/Vite serves or builds frontend assets.
```

## 6. Deployment topology (initial)
- Web process: stateless where practical
- Worker process: separate scaling and restart policy
- Scheduler: single controlled scheduler or coordinated scheduling
- MySQL: managed or protected database with backups
- Redis: queue/cache as appropriate
- Private object storage: production documents
- TLS reverse proxy
- Secret/environment management
- Monitoring/logging
- Backup and restore testing

## 7. Database recommendation
MySQL is a good fit:
- transactional relational workflows and many linked entities
- ACID transactions and foreign keys
- Laravel ecosystem support
- DECIMAL for money
- JSON columns only for flexible supplemental payloads, not core relational fields
- indexes designed around filters and report patterns

Consider PostgreSQL only if there are clear requirements for its particular features or the team strongly prefers it. MySQL is not a blocker for this product.

## 8. Scalability strategy
1. Optimize query patterns, indexes and pagination.
2. Queue heavy PDF/Excel/export/integration tasks.
3. Scale worker and web processes separately.
4. Use read projections for heavy dashboards.
5. Cache stable reference data.
6. Profile before denormalizing.
7. Consider a separate analytics store only when workload justifies it.
8. Consider service extraction only after module boundaries and scaling pressure are proven.

## 9. Non-goals initially
Microservices, Kubernetes, event streaming platform, multiple transactional databases, SPA rewrite, GraphQL, autonomous integration engine and full BI platform.
