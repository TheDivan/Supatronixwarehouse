import { Controller, Post, Body } from '@nestjs/common'
import { PrismaService } from '../../prisma/prisma.service'

@Controller('api/insurance')
export class InsuranceController {
  constructor(private readonly prisma: PrismaService) {}

  @Post('claims')
  async createClaim(@Body() data: any) {
    const claim = await this.prisma.insuranceClaim.create({ data: data }).catch(() => null)
    return claim
  }
}
