import { Controller, Get, Query } from '@nestjs/common'
import { PrismaService } from './../prisma/prisma.service'

@Controller('api/insurance/export')
export class InsuranceExportController {
  constructor(private readonly prisma: PrismaService) {}

  @Get('claims')
  async exportClaims(@Query() _q: any) {
    const claims = await this.prisma.insuranceClaim.findMany().catch(() => [])
    const header = 'claimId,repairId,insurerName,claimNumber,claimStatus,notRepairableReason,salvageValue,payoutAmount,createdAt\n'
    const rows = claims.map((c: any) => `${c.id},${c.repairId},${c.insurerName},${c.claimNumber},${c.claimStatus},${c.notRepairableReason ?? ''},${c.salvageValue ?? ''},${c.payoutAmount ?? ''},${c.createdAt ?? ''}\n`)
    const csv = header + rows.join('')
    return { csv }
  }
}
