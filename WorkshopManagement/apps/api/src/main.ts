import { NestFactory } from '@nestjs/core'
import { AppModule } from './app.module'
import { PermissionsGuard } from './common/guards/permissions.guard'
import { JwtGuard } from './auth/jwt.guard'
import { AuthModule } from './auth/auth.module'

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  app.enableCors();
  // Simple runtime auth middleware (Phase 2: portal uses token-based login)
  app.use((req, res, next) => {
    const header = req.headers['authorization'] as string | undefined
    if (header && header.startsWith('Bearer ')) {
      const token = header.slice(7)
      if (token === 'token-admin') {
        req.user = { id: 'USER-ADMIN', name: 'Admin', email: 'admin@supatronix.local', roles: ['OWNER'] }
      } else if (token === 'token-client') {
        req.user = { id: 'USER-CUSTOMER', name: 'PortalUser', email: 'portal@example.com', roles: ['CUSTOMER'] }
      }
    }
    if (!req.user) {
      req.user = { id: 'guest', name: 'Guest', roles: ['CUSTOMER'] }
    }
    next()
  })
  // Apply global RBAC guard if defined in module
  try {
    app.useGlobalGuards(app.get(JwtGuard), app.get(PermissionsGuard))
  } catch {
    // guard may not be registered in all setups during scaffolding
  }
  const port = process.env.PORT ? Number(process.env.PORT) : 3001
  await app.listen(port);
}
bootstrap();
