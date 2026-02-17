import { Controller, Post, Body } from '@nestjs/common'
import { TimeService } from './time.service'

@Controller('api/time-logs')
export class TimeController {
  constructor(private readonly service: TimeService) {}

  @Post()
  create(@Body() data: any) {
    return this.service.createTimeLog(data)
  }
}
