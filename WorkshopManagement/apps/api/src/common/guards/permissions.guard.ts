import { Injectable, CanActivate, ExecutionContext } from '@nestjs/common'
import { Reflector } from '@nestjs/core'
import { RBACService } from '../../rbac/rbac.service'

@Injectable()
export class PermissionsGuard implements CanActivate {
  constructor(private readonly reflector: Reflector, private readonly rbac: RBACService) {}

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const required = this.reflector.get<string[]>('permissions', context.getHandler()) || []
    if (!required.length) return true
    const req = context.switchToHttp().getRequest()
    const user = req.user
    if (!user?.id) return false
    // check if user has any of the required permissions
    for (const perm of required) {
      if (await this.rbac.hasPermission(user.id, perm)) return true
    }
    return false
  }
}
