import { Injectable } from '@nestjs/common'
import { PrismaService } from '../prisma/prisma.service'

@Injectable()
export class RBACService {
  constructor(private readonly prisma: PrismaService) {}

  async getUserPermissions(userId: string): Promise<string[]> {
    // gather permissions via roles
    const userRoles = await this.prisma.userRole.findMany({ where: { userId }, include: { role: { include: { permissions: { include: { permission: true } } } } } })
    // Build from role-permission mappings
    const perms = new Set<string>()
    for (const ur of userRoles) {
      const role = ur.role as any
      if (role?.permissions) {
        for (const rp of role.permissions) {
          if (rp?.permission?.name) perms.add(rp.permission.name)
        }
      }
    }
    return Array.from(perms)
  }

  async hasPermission(userId: string, permissionName: string): Promise<boolean> {
    const perms = await this.getUserPermissions(userId)
    return perms.includes(permissionName)
  }

  async assignRoleToUser(userId: string, roleId: string) {
    // create UserRole if not exists
    const existing = await this.prisma.userRole.findFirst({ where: { userId, roleId } })
    if (existing) return existing
    return this.prisma.userRole.create({ data: { userId, roleId } })
  }

  async removeRoleFromUser(userId: string, roleId: string) {
    return this.prisma.userRole.deleteMany({ where: { userId, roleId } })
  }

  async addPermissionToRole(roleId: string, permissionId: string) {
    // create RolePermission if not exists
    const existing = await this.prisma.rolePermission.findFirst({ where: { roleId, permissionId } })
    if (existing) return existing
    return this.prisma.rolePermission.create({ data: { roleId, permissionId } })
  }

  async removePermissionFromRole(roleId: string, permissionId: string) {
    return this.prisma.rolePermission.deleteMany({ where: { roleId, permissionId } })
  }

  // Admin helpers for Phase 1 MVP runtime RBAC editing
  async createRole(name: string) {
    const role = await this.prisma.role.create({ data: { name } })
    // audit
    await this.prisma.auditLog.create({ data: { entity: 'Role', entityId: role.id, action: 'CREATE', changedBy: 'system', timestamp: new Date(), details: `Created role ${name}` } }).catch(() => null)
    return role
  }

  async createPermission(name: string, description?: string) {
    const perm = await this.prisma.permission.create({ data: { name, description } })
    await this.prisma.auditLog.create({ data: { entity: 'Permission', entityId: perm.id, action: 'CREATE', changedBy: 'system', timestamp: new Date(), details: `Created permission ${name}` } }).catch(() => null)
    return perm
  }
}
