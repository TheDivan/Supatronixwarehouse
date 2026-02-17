import { Module } from '@nestjs/common'
import { RBACService } from './rbac.service'
import { AdminModule } from '../admin/admin.module'

@Module({
  imports: [AdminModule],
  providers: [RBACService],
  exports: [RBACService],
})
export class RBACModule {}
