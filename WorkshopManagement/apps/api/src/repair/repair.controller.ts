import { Controller, Post, Get, Body, Param, Put } from '@nestjs/common'
import { RepairService } from './repair.service'

@Controller('api/repair-jobs')
export class RepairController {
  constructor(private readonly repairService: RepairService) {}

  @Post()
  create(@Body() data: any) {
    return this.repairService.createRepair(data)
  }

  @Get(':id')
  get(@Param('id') id: string) {
    return this.repairService.getRepair(id)
  }

  @Put(':id')
  update(@Param('id') id: string, @Body() data: any) {
    return this.repairService.updateRepair(id, data)
  }
}
