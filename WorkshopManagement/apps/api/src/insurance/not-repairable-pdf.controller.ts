import { Controller, Get, Param, Res } from '@nestjs/common'
import { PrismaService } from './../prisma/prisma.service'
import { Response } from 'express'
const PDFDocument = require('pdfkit')

@Controller('api/insurance/not-repairable')
export class NotRepairablePdfController {
  constructor(private readonly prisma: PrismaService) {}

  @Get('report/:claimId/pdf')
  async pdf(@Param('claimId') claimId: string, @Res() res: Response) {
    const claim = await this.prisma.insuranceClaim.findUnique({ where: { id: claimId }, include: { repair: true } }).catch(() => null)
    if (!claim) {
      res.status(404).send('Not found')
      return
    }
    const repair = (claim as any).repair
    const doc = new PDFDocument()
    res.setHeader('Content-Type', 'application/pdf')
    res.setHeader('Content-Disposition', `attachment; filename=not-repairable-${claimId}.pdf`)
    doc.pipe(res)
    doc.fontSize(20).text('Not Repairable Report', { align: 'center' })
    doc.moveDown()
    doc.fontSize(12).text(`Repair ID: ${claim.repairId}`)
    doc.text(`Device: ${repair?.deviceBrand ?? ''} ${repair?.deviceModel ?? ''} (IMEI: ${repair?.imei ?? ''}, Serial: ${repair?.serial ?? ''})`)
    doc.text(`Fault Reported: ${repair?.faultReported ?? ''}`)
    doc.text(`Not Repairable Reason: ${claim.notRepairableReason ?? ''}`)
    doc.text(`Salvage Value: ${claim.salvageValue ?? ''}`)
    doc.text(`Insurer: ${claim.insurerName ?? ''}`)
    doc.text(`Claim Number: ${claim.claimNumber ?? ''}`)
    doc.text(`Claim Status: ${claim.claimStatus ?? ''}`)
    if (claim.attachments?.length) {
      doc.moveDown().text('Attachments:')
      claim.attachments.forEach((a: string) => doc.text(`- ${a}`))
    }
    // Signature placeholder box
    doc.moveDown(2)
    const sigY = doc.y
    doc.text('Customer Signature:')
    doc.moveTo(72, sigY + 15).lineTo(520, sigY + 15).stroke()
    doc.text('Signature confirmed on receipt of report', 72, sigY + 40)
    doc.end()
  }
}
