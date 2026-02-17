import { Controller, Post, Body } from '@nestjs/common'
import * as jwt from 'jsonwebtoken'

@Controller('api/auth')
export class AuthController {
  @Post('login')
  login(@Body() body: any) {
    const email = body?.email
    const secret = process.env.JWT_SECRET || 'devsecret'
    if (email === 'admin@supatronix.local') {
      const payload = { id: 'USER-ADMIN', name: 'Admin', email, roles: ['OWNER'] }
      const token = jwt.sign(payload, secret, { expiresIn: '1h' })
      return { accessToken: token, user: payload }
    }
    if (email) {
      const payload = { id: 'USER-CUSTOMER', name: email.split('@')[0], email, roles: ['CUSTOMER'] }
      const token = jwt.sign(payload, secret, { expiresIn: '1h' })
      return { accessToken: token, user: payload }
    }
    const payload = { id: 'guest', name: 'Guest', roles: ['CUSTOMER'] }
    const token = jwt.sign(payload, secret, { expiresIn: '1h' })
    return { accessToken: token, user: payload }
  }
}
