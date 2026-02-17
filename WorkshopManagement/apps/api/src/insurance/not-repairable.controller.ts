import { Controller, Post, Body, Get } from '@nestjs/common'
import { PrismaService } from '../../prisma/prisma.service'
import { v4 as uuidv4 } from 'uuid'

@Controller('api/insurance/not-repairable')
export class NotRepairableController {
  constructor(private readonly prisma: PrismaService) {}

  @Post('report')
  async generateReport(@Body() data: any) {
    const {
      repairId,
      insurerName,
      claimNumber,
      notRepairableReason,
      salvageValue,
      attachments
    } = data
    const claim = await this.prisma.insuranceClaim.create({
      data: {
        repairId,
        insurerName: insurerName ?? 'Unknown',
        claimNumber: claimNumber ?? uuidv4(),
        claimStatus: 'Filed',
        notRepairableReason,
        salvageValue,
        attachments: attachments ?? [],
      },
    }).catch(() => null)
    // Build a simple not-repairable report payload
    const repair = await this.prisma.deviceRepair.findUnique({ where: { id: repairId } }).catch(() => null)
    return {
      notRepairableReport: {
        repairId,
        deviceBrand: repair?.deviceBrand,
        deviceModel: repair?.deviceModel,
        faultReported: repair?.faultReported,
        notRepairableReason: notRepairableReason,
        salvageValue,
        insurerName: insurerName ?? 'Unknown',
        claimNumber: claimNumber ?? (claim?.id ?? 'N/A'),
        claimStatus: 'Filed',
        attachments: attachments ?? [],
        createdAt: claim?.createdAt,
      }
    }
  }
}
