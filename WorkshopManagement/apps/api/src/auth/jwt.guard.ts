import { Injectable, CanActivate, ExecutionContext } from '@nestjs/common'
import * as jwt from 'jsonwebtoken'

@Injectable()
export class JwtGuard implements CanActivate {
  canActivate(context: ExecutionContext): boolean {
    const req = context.switchToHttp().getRequest()
    const auth = req.headers['authorization'] as string | undefined
    if (!auth) {
      // No token; allow guest access for MVP or require login for protected routes via PermissionsGuard later
      req.user = { id: 'guest', name: 'Guest', roles: ['CUSTOMER'] }
      return true
    }
    const match = auth.match(/^Bearer (.*)$/)
    if (!match) return false
    const token = match[1]
    try {
      const secret = process.env.JWT_SECRET || 'devsecret'
      const payload = jwt.verify(token, secret) as any
      req.user = payload
      return true
    } catch {
      return false
    }
  }
}
