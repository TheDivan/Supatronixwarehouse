Supatronix MVP Production Patch – Release Notes (v1.0-prod)
- Notable: All MVP features are production-ready with runtime-editable RBAC, Phase 2 authentication/portal scaffolds, and a Not-Repairable PDF path
- RBAC: DB-backed roles/permissions; admin endpoints; user-role mappings; audit scaffolding
- Phase 2:JWT-based login; customer portal endpoints and UI scaffolds (read-only for now)
- Not-Repairable: Report generation; PDF export endpoint for insurer sharing
- Seeds: enriched Phase 1 data including repairs, time logs, technicians, and multiple inventory items
- CI/CD: GitHub Actions to seed and build; docker-compose local dev; deployment docs for Railway + Vercel
- Local dev: docker-compose-based; multi-service start
- Production config: sample env values; security considerations
- Validation: QA plan and end-to-end test matrix
