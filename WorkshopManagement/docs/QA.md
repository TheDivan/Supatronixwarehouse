# QA & Acceptance Test Plan (Supatronix MVP)

- Core flows to test manually:
  - Intake -> Diagnosing -> AwaitingParts/Repairing -> Ready for Pickup -> Released
  - Part reservations reduce available stock immediately; consumption reduces stock on release
  - Time logs capture durations and associate with repairs
  - Photos upload and retrieval per repair
  - Receipts: verify labor/parts lines, taxes, deposits, total, balance
  - RBAC: create role/permission, assign to user, verify access control on protected endpoints
  - Insurance path: create a not-repairable report, associated insurance claim, export a PDF
  - Phase 2 portal: login as customer, read repair history, read receipts

- Edge cases:
  - Multiple repairs reserve same final stock item; ensure reservation vs stock logic holds
  - Cancelled repairs release reservations
  - Partial consumption of multi-unit parts updates remaining stock accordingly

- Automated checks to add in CI:
  - Simple integration test stubs for REST endpoints using supertest (optional)
  - Verification of audit logs on RBAC changes
  - PDF generation check for Not Repairable report
