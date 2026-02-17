Production PR: Supatronix MVP v1.0

Title
- Supatronix MVP v1.0: RBAC Admin UI, Phase 2 Auth/Portal, Not-Repairable PDF, Rich Seeds, Docker Compose, CI/CD, Deployment Docs

Overview
- Production-ready final patch that completes MVP capabilities and readiness for deployment:
  - Runtime-editable RBAC (Role/Permission/UserRole/RolePermission) with admin endpoints and audit scaffolding
  - Phase 2 authentication and customer portal scaffold (JWT-based)
  - Not-Repairable Insurance path with PDF export for Not Repairable Reports
  - Rich seed data to showcase real-world usage
  - End-to-end local dev using docker-compose; CI/CD hooks for Railway (backend) + Vercel (frontend)
  - Polished RBAC admin UI screens and lightweight admin README

What changed (high level)
- RBAC: Added DB models (User, Role, Permission, UserRole, RolePermission); RBACService; PermissionsGuard; @Permissions; admin endpoints
- Admin UI: polished RBAC admin UI scaffold and README for admin testers
- Phase 2: JWT auth endpoints; portal endpoints; portal UI scaffolds (login/dashboard/repairs)
- Not-Repairable: PDF export path and report creation endpoint; PDF generator implementation
- Insurance export (CSV) scaffold
- Time logs: time-tracking endpoints and services
- Seeds: enriched Phase 1 data (seed-data.js + seed-extra.js)
- CI/CD: workflows to seed data, build, and deploy
- Deployment docs: Railway + Vercel guide and production config samples
- QA: MVP test plan and edge-case coverage

Notable data-model changes
- RBAC: User, Role, Permission, UserRole, RolePermission
- Insurance: InsuranceClaim, NotRepair (phase-2)
- Time: TimeLog
- Receipts: linked to repairs and customers

How to test (summary)
- RBAC runtime edits: create role/permission, assign role to user, attach permissions, verify access control
- Repairs: intake, reserve/consume parts, time logs, photos, receipts
- Phase 2: portal login and customer data access
- Not-Repairable PDF: generate and download, content validated
- CI/CD: seed runs on deploy; build passes

Deployment plan
- Backend (Railway): migrations, seed, health checks; env: DATABASE_URL, JWT_SECRET, API_BASE_URL
- Frontend (Vercel): API_BASE_URL to backend; deploy; server-side rendering
- Production config: sample envs included in docs

Release notes (production)
- RBAC runtime edit enabled
- Phase 2 auth/portal scaffolds added
- Not-Repairable path plus PDF export
- Notable: rich seeds; docker-compose local dev
- CI/CD in place; deployment docs

Rollout plan
- Phase 1: Core RBAC, repairs, inventory, PDF exports, seeds, docker-compose
- Phase 2: Phase 2 portal, JWT auth, customer data scoping, Not-Repairable report workflow
- Phase 3: Analytics, multi-store, offline resilience, PCI considerations as applicable

Testing and QA
- See docs/QA.md for end-to-end tests and edge cases

Appendix
- Architecture notes; code pointers; promo materials
