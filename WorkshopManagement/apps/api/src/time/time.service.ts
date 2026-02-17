import { Injectable } from '@nestjs/common'
import { PrismaService } from '../prisma/prisma.service'

@Injectable()
export class TimeService {
  constructor(private readonly prisma: PrismaService) {}

  async createTimeLog(data: any) {
    const { repairId, technicianId, start, end, notes } = data
    const log = await this.prisma.timeLog.create({
      data: {
        repairId,
        technicianId,
        start: new Date(start),
        end: end ? new Date(end) : null,
        duration: end && start ? Math.round((new Date(end).getTime() - new Date(start).getTime()) / 1000 / 60) : null,
        notes,
      },
    }).catch(() => null)
    return log
  }
}
