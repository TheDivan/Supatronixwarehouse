# Supatronix MVP Deployment Guide (Railway + Vercel)

- Prereqs: Node.js 18+, PostgreSQL, Git, access to Railway and Vercel.
- DB: Provide a DATABASE_URL in Railway Postgres or local DB.
- Secrets: Set JWT_SECRET for production, and API_BASE_URL (for frontend to point to backend).

1) Backend (Railway)
- Create a new Railway project; attach a Postgres plugin.
- Environment:
  - DATABASE_URL: your Postgres connection string
  - JWT_SECRET: a secret for signing tokens (optional; default in code)
  - PORT: 3000 (Railway will map to its own port)
- Deploy steps:
  - Ensure migrations are in place (use Prisma db push during deploy or a migrations step).
  - Run seed if needed: npm run seed or npm run seed:extra
  - Start backend via npm run start (or have Railway run npm start).

2) Frontend (Vercel)
- Create a Vercel project; connect to repo; configure environment:
  - API_BASE_URL: https://<your-railway-backend>.up.railway.app (or your deployed endpoint)
- Deploy; Vercel handles build and SSR.

3) Verification steps
- After deployment, test health endpoint: GET /health
- Test login (admin): POST /api/auth/login with { email: "admin@supatronix.local" }
- Retrieve RBAC admin endpoints only when logged in as OWNER/MANAGER.
- Test Phase 2 portal by logging in as customer and calling /api/customers/me endpoints.

4) Rollout plan
- Start with MVP on a test store; validate RBAC runtime edits, basic repairs, inventory flows, and basic portal.
- After MVP, iterate on insurance path, not-repairable report generation, and reports.

End of guide
