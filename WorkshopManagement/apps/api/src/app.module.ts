import { Module } from '@nestjs/common'
import { ConfigModule } from '@nestjs/config'
import { PrismaModule } from '../prisma/prisma.module'
import { HealthController } from './health/health.controller'
import { PortalController } from './portal/portal.controller'
import { AuthModule } from '../auth/auth.module'
import { TimeModule } from '../time/time.module'
import { InsuranceModule } from '../insurance/insurance.module'
import { RepairModule } from '../repair/repair.module'
import { AdminModule } from '../admin/admin.module'
import { RBACModule } from '../rbac/rbac.module'
import { PermissionsGuard } from './common/guards/permissions.guard'
 

@Module({
  imports: [ConfigModule.forRoot({ isGlobal: true }), PrismaModule, RepairModule, AdminModule, RBACModule, AuthModule, TimeModule, InsuranceModule],
  controllers: [HealthController, PortalController],
  providers: [PermissionsGuard],
})
export class AppModule {}
