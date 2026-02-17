import { Controller, Get, Param, Req } from '@nestjs/common'

@Controller('api/customers')
export class PortalController {
  @Get('me')
  me(@Req() req: any) {
    // Placeholder: in Phase 2, return the authenticated customer's profile
    return { id: req.user?.id || 'guest', name: req.user?.name || 'Guest' }
  }

  @Get('me/repairs')
  repairs(@Req() req: any) {
    // Placeholder: return a small sample of repairs for the logged-in customer
    const id = req.user?.id || 'guest'
    return [
      {
        repairId: 'DR-0001',
        deviceBrand: 'Apple',
        deviceModel: 'iPhone 12',
        status: 'Released',
        releasedAt: new Date().toISOString(),
        customerId: id,
      },
      {
        repairId: 'DR-0002',
        deviceBrand: 'Samsung',
        deviceModel: 'Galaxy S21',
        status: 'Ready for Pickup',
        releasedAt: null,
        customerId: id,
      }
    ]
  }

  @Get(':customerId/receipts')
  receipts(@Param('customerId') customerId: string) {
    // Placeholder: return a simple receipt list for the customer
    return [
      {
        receiptId: 'RCPT-0001',
        date: new Date().toISOString(),
        total: 140,
        balanceDue: 0,
        repairId: 'DR-0001',
      }
    ]
  }
}
