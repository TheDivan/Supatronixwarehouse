/* Extra Phase 1 MVP seeds: richer repairs, technicians, and parts usage */
(async function mainExtra() {
  const { PrismaClient } = require('@prisma/client')
  const prisma = new PrismaClient()
  try {
    const alex = await prisma.customer.findFirst({ where: { email: 'alex@example.com' } }).catch(() => null)
    const jamie = await prisma.customer.findFirst({ where: { email: 'jamie@example.com' } }).catch(() => null)
    const tech5 = await prisma.technician.create({ data: { name: 'Nova Singh', phone: '+15550000005', email: 'nova@example.com' } }).catch(() => null)

    // Dr10: new repair for Alex
    const dr10 = await prisma.deviceRepair.create({ data: { customerId: alex?.id, deviceBrand: 'Apple', deviceModel: 'iPhone 12', faultReported: 'Battery not charging', receivedAt: new Date(), estimatedCost: 110, deposit: 0, status: 'Diagnosing' } }).catch(() => null)
    const inv1 = await prisma.inventoryItem.findFirst({ where: { sku: 'SCREEN-IPH12' } }).catch(() => null)
    if (dr10 && inv1) {
      await prisma.repairItem.create({ data: { repairId: dr10.id, inventoryItemId: inv1.id, quantity: 1, unitCost: 45, totalCost: 45 } }).catch(() => null)
    }
    if (tech5 && dr10) {
      await prisma.timeLog.create({ data: { repairId: dr10.id, technicianId: tech5.id, start: new Date().toISOString(), end: new Date().toISOString(), duration: 40, notes: 'Diagnostics' } }).catch(() => null)
    }

    // Dr11: new repair for Jamie
    const dr11 = await prisma.deviceRepair.create({ data: { customerId: jamie?.id, deviceBrand: 'Samsung', deviceModel: 'Galaxy S21', faultReported: 'Charging issues', receivedAt: new Date(), estimatedCost: 100, deposit: 0, status: 'Diagnosing' } }).catch(() => null)
    const inv3 = await prisma.inventoryItem.findFirst({ where: { sku: 'CHG-PORT' } }).catch(() => null)
    if (dr11 && inv3) {
      await prisma.repairItem.create({ data: { repairId: dr11.id, inventoryItemId: inv3.id, quantity: 1, unitCost: 3, totalCost: 3 } }).catch(() => null)
    }
  } catch (e) {
    console.error('Seed-extra error:', e)
  } finally {
    await prisma.$disconnect()
  }
})().catch(e => { console.error(e); process.exit(1) })
