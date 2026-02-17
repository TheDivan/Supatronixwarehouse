import { Module } from '@nestjs/common'
import { InsuranceController } from './insurance.controller'
import { NotRepairableController } from './not-repairable.controller'
import { InsuranceExportController } from './export.controller'
import { NotRepairablePdfController } from './not-repairable-pdf.controller'
import { NotRepairableController } from './not-repairable.controller'

@Module({
  controllers: [InsuranceController, NotRepairableController, InsuranceExportController, NotRepairablePdfController],
})
export class InsuranceModule {}
