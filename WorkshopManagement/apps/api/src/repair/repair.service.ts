import { Injectable } from '@nestjs/common'

@Injectable()
export class RepairService {
  private repairs: any[] = []

  createRepair(data: any) {
    const repair = {
      id: 'DR-' + String(this.repairs.length + 1).padStart(4, '0'),
      ...data,
      receivedAt: data.receivedAt || new Date().toISOString(),
      status: data.status || 'Received',
    }
    this.repairs.push(repair)
    return repair
  }

  getRepair(id: string) {
    return this.repairs.find((r) => r.id === id)
  }

  updateRepair(id: string, data: any) {
    const idx = this.repairs.findIndex((r) => r.id === id)
    if (idx < 0) return null
    this.repairs[idx] = { ...this.repairs[idx], ...data, updatedAt: new Date().toISOString() }
    return this.repairs[idx]
  }
}
