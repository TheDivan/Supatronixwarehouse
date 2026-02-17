import { Controller, Post, Body, Param, Get } from '@nestjs/common'
import { Permissions } from '../common/decorators/permissions.decorator'
import { RBACService } from '../rbac/rbac.service'

@Controller('api/admin')
export class AdminController {
  constructor(private readonly rbac: RBACService) {}

  @Post('roles')
  @Permissions('USER_MANAGE')
  async createRole(@Body() body: any) {
    const name = body?.name
    if (!name) return { ok: false, error: 'Missing role name' }
    const role = await this.rbac.createRole(name)
    return { ok: true, role }
  }

  @Post('permissions')
  @Permissions('USER_MANAGE')
  async createPermission(@Body() body: any) {
    const name = body?.name
    const description = body?.description
    if (!name) return { ok: false, error: 'Missing permission name' }
    const perm = await this.rbac.createPermission(name, description)
    return { ok: true, permission: perm }
  }

  @Post('roles/:roleId/permissions')
  
  @Permissions('USER_MANAGE')
  async addPermissionToRole(@Param('roleId') roleId: string, @Body() body: any) {
    const permissionId = body?.permissionId
    const result = await this.rbac.addPermissionToRole(roleId, permissionId)
    return { ok: true, result }
  }

  @Post('users/:userId/roles')
  @Permissions('USER_MANAGE')
  async assignRoleToUser(@Param('userId') userId: string, @Body() body: any) {
    const roleId = body?.roleId
    await this.rbac.assignRoleToUser(userId, roleId)
    return { ok: true, userId, roleId }
  }
}
