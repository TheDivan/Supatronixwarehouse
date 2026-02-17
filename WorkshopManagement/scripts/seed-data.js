/* Starter seed script for Phase 1 MVP using Prisma client */
async function main() {
  console.log('Seeding Supatronix MVP (Phase 1) data...');
  const { PrismaClient } = require('@prisma/client')
  const prisma = new PrismaClient()
  try {
    // Roles & Permissions (upsert/get)
    const ownerRole = await prisma.role.upsert({ where: { name: 'OWNER' }, update: {}, create: { name: 'OWNER' } })
    const managerRole = await prisma.role.upsert({ where: { name: 'MANAGER' }, update: {}, create: { name: 'MANAGER' } })

    const permNames = [
      'REPAIR_CREATE','REPAIR_READ','REPAIR_UPDATE','INVENTORY_MANAGE','PARTS_RESERVE','PARTS_CONSUME','TIMELOG','PHOTOS_UPLOAD','RECEIPT_MANAGE','NOTIFICATION_SEND','USER_MANAGE','REPORT_VIEW','SETTINGS_EDIT'
    ]

    const perms = []
    for (const p of permNames) {
      const up = await prisma.permission.upsert({ where: { name: p }, update: {}, create: { name: p } }).catch(() => null)
      if (up) perms.push(up)
    }

    // Assign all permissions to OWNER and MANAGER
    for (const p of perms) {
      await prisma.rolePermission.create({ data: { roleId: ownerRole.id, permissionId: p.id } }).catch(() => null)
      await prisma.rolePermission.create({ data: { roleId: managerRole.id, permissionId: p.id } }).catch(() => null)
    }

    // Admin user
    const adminUser = await prisma.user.upsert({ where: { email: 'admin@supatronix.local' }, update: {}, create: { email: 'admin@supatronix.local', name: 'Admin', password: 'changeme' } }).catch(() => null)
    if (adminUser) {
      await prisma.userRole.create({ data: { userId: adminUser.id, roleId: ownerRole.id } }).catch(() => null)
    }

    // Customers
    const alex = await prisma.customer.upsert({ where: { email: 'alex@example.com' }, update: {}, create: { name: 'Alex Doe', phone: '+15551234567', email: 'alex@example.com' } }).catch(() => null)
    const jamie = await prisma.customer.upsert({ where: { email: 'jamie@example.com' }, update: {}, create: { name: 'Jamie Lee', phone: '+15559876543', email: 'jamie@example.com' } }).catch(() => null)
    const priya = await prisma.customer.upsert({ where: { email: 'priya@example.com' }, update: {}, create: { name: 'Priya Kapoor', phone: '+15557654321', email: 'priya@example.com' } }).catch(() => null)

    // Inventory
    const inv1 = await prisma.inventoryItem.upsert({ where: { sku: 'SCREEN-IPH12' }, update: {}, create: { sku: 'SCREEN-IPH12', name: 'iPhone 12 Display Assembly', category: 'Screen', stockQuantity: 8, reservedQuantity: 0, reorderThreshold: 5, costPrice: 45, sellPrice: 79.99, location: 'Shelf A1' } }).catch(() => null)
    const inv2 = await prisma.inventoryItem.upsert({ where: { sku: 'SCREEN-IPH13' }, update: {}, create: { sku: 'SCREEN-IPH13', name: 'iPhone 13 Display Assembly', category: 'Screen', stockQuantity: 6, reservedQuantity: 0, reorderThreshold: 5, costPrice: 50, sellPrice: 89.99, location: 'Shelf A2' } }).catch(() => null)
    const inv3 = await prisma.inventoryItem.upsert({ where: { sku: 'BAT-IPH12' }, update: {}, create: { sku: 'BAT-IPH12', name: 'Battery 12', category: 'Battery', stockQuantity: 20, reservedQuantity: 0, reorderThreshold: 10, costPrice: 4.5, sellPrice: 9.99, location: 'Shelf B1' } }).catch(() => null)
    const inv4 = await prisma.inventoryItem.upsert({ where: { sku: 'BAT-IPH13' }, update: {}, create: { sku: 'BAT-IPH13', name: 'Battery 13', category: 'Battery', stockQuantity: 12, reservedQuantity: 0, reorderThreshold: 10, costPrice: 5.0, sellPrice: 11.99, location: 'Shelf B2' } }).catch(() => null)
    const inv5 = await prisma.inventoryItem.upsert({ where: { sku: 'CAM-IP13' }, update: {}, create: { sku: 'CAM-IP13', name: 'Camera Module IP13', category: 'Camera', stockQuantity: 5, reservedQuantity: 0, reorderThreshold: 3, costPrice: 25, sellPrice: 49.99, location: 'Shelf C1' } }).catch(() => null)
    const inv6 = await prisma.inventoryItem.upsert({ where: { sku: 'CHG-PORT' }, update: {}, create: { sku: 'CHG-PORT', name: 'Charging Port', category: 'Connector', stockQuantity: 10, reservedQuantity: 0, reorderThreshold: 5, costPrice: 3, sellPrice: 7.99, location: 'Shelf C2' } }).catch(() => null)

    // Repairs and items
    const dr1 = await prisma.deviceRepair.create({ data: { customerId: alex?.id, deviceBrand: 'Apple', deviceModel: 'iPhone 12', faultReported: 'Cracked screen', receivedAt: new Date(), estimatedCost: 180, deposit: 20, status: 'Received' } }).catch(() => null)
    if (dr1 && inv1) {
      await prisma.repairItem.create({ data: { repairId: dr1.id, inventoryItemId: inv1.id, quantity: 1, unitCost: 45, totalCost: 45 } }).catch(() => null)
    }

    const dr2 = await prisma.deviceRepair.create({ data: { customerId: jamie?.id, deviceBrand: 'Samsung', deviceModel: 'Galaxy S21', faultReported: 'Battery drains quickly', receivedAt: new Date(), estimatedCost: 120, deposit: 0, status: 'Diagnosing' } }).catch(() => null)
    if (dr2 && inv3) {
      await prisma.repairItem.create({ data: { repairId: dr2.id, inventoryItemId: inv3.id, quantity: 1, unitCost: 4.5, totalCost: 4.5 } }).catch(() => null)
    }

    const dr3 = await prisma.deviceRepair.create({ data: { customerId: priya?.id, deviceBrand: 'Apple', deviceModel: 'iPhone 13', faultReported: 'Water damage', receivedAt: new Date(), estimatedCost: 300, deposit: 50, status: 'AwaitingParts' } }).catch(() => null)
    if (dr3 && inv2) {
      await prisma.repairItem.create({ data: { repairId: dr3.id, inventoryItemId: inv2.id, quantity: 1, unitCost: 50, totalCost: 50 } }).catch(() => null)
    }

    const dr4 = await prisma.deviceRepair.create({ data: { customerId: alex?.id, deviceBrand: 'Apple', deviceModel: 'iPhone 12', faultReported: 'Battery swelling', receivedAt: new Date(), estimatedCost: 150, deposit: 0, status: 'Repairing' } }).catch(() => null)
    if (dr4 && inv4) {
      await prisma.repairItem.create({ data: { repairId: dr4.id, inventoryItemId: inv4.id, quantity: 1, unitCost: 5, totalCost: 5 } }).catch(() => null)
    }

    const dr5 = await prisma.deviceRepair.create({ data: { customerId: jamie?.id, deviceBrand: 'Apple', deviceModel: 'iPhone 12', faultReported: 'Charger port loose', receivedAt: new Date(), estimatedCost: 100, deposit: 20, status: 'Ready for Pickup' } }).catch(() => null)
    if (dr5 && inv5) {
      await prisma.repairItem.create({ data: { repairId: dr5.id, inventoryItemId: inv5.id, quantity: 1, unitCost: 25, totalCost: 25 } }).catch(() => null)
    }

    const dr6 = await prisma.deviceRepair.create({ data: { customerId: priya?.id, deviceBrand: 'Samsung', deviceModel: 'Galaxy S21', faultReported: 'Screen flicker', receivedAt: new Date(), estimatedCost: 220, deposit: 0, status: 'Released', releasedAt: new Date() } }).catch(() => null)
    if (dr6 && inv6) {
      await prisma.repairItem.create({ data: { repairId: dr6.id, inventoryItemId: inv6.id, quantity: 1, unitCost: 3, totalCost: 3 } }).catch(() => null)
    }

    // Richer phase 1 data: more repairs, more time logs, engineers
    const tech3 = await prisma.technician.create({ data: { name: 'Pat Kim', phone: '+1555000003', email: 'pat@example.com' } }).catch(() => null)
    const tech4 = await prisma.technician.create({ data: { name: 'Lee Chen', phone: '+1555000004', email: 'lee@example.com' } }).catch(() => null)

    const dr7 = await prisma.deviceRepair.create({ data: { customerId: alex?.id, deviceBrand: 'Apple', deviceModel: 'iPhone 12', faultReported: 'Battery drain', receivedAt: new Date(), estimatedCost: 140, deposit: 0, status: 'Diagnosing' } }).catch(() => null)
    if (dr7 && inv3) {
      await prisma.repairItem.create({ data: { repairId: dr7.id, inventoryItemId: inv3.id, quantity: 1, unitCost: 4.5, totalCost: 4.5 } }).catch(() => null)
      await prisma.timeLog.create({ data: { repairId: dr7.id, technicianId: tech3?.id || null, start: new Date().toISOString(), end: new Date().toISOString(), duration: 60, notes: 'Diagnostics' } }).catch(() => null)
    }

    const dr8 = await prisma.deviceRepair.create({ data: { customerId: jamie?.id, deviceBrand: 'Samsung', deviceModel: 'Galaxy S21', faultReported: 'Screen tint', receivedAt: new Date(), estimatedCost: 130, deposit: 0, status: 'Repairing' } }).catch(() => null)
    if (dr8 && inv2) {
      await prisma.repairItem.create({ data: { repairId: dr8.id, inventoryItemId: inv2.id, quantity: 1, unitCost: 50, totalCost: 50 } }).catch(() => null)
      await prisma.timeLog.create({ data: { repairId: dr8.id, technicianId: tech4?.id || null, start: new Date().toISOString(), end: new Date().toISOString(), duration: 75, notes: 'Panel replacement' } }).catch(() => null)
    }

    const dr9 = await prisma.deviceRepair.create({ data: { customerId: priya?.id, deviceBrand: 'Apple', deviceModel: 'iPhone 13', faultReported: 'Camera failure', receivedAt: new Date(), estimatedCost: 230, deposit: 70, status: 'Ready for Pickup' } }).catch(() => null)
    if (dr9 && inv5) {
      await prisma.repairItem.create({ data: { repairId: dr9.id, inventoryItemId: inv5.id, quantity: 1, unitCost: 25, totalCost: 25 } }).catch(() => null)
      await prisma.receipt.create({ data: { repairId: dr9.id, customerId: priya?.id, date: new Date(), lineItems: [{ description: 'Camera repair', quantity: 1, unitPrice: 150, total: 150 }, { description: 'Camera part', quantity: 1, unitPrice: 25, total: 25 }], taxRate: 0.07, subtotal: 175, tax: 12.25, depositApplied: 70, total: 187.25, balanceDue: 0, warrantyTerms: '90 days', issuedBy: 'Admin' } }).catch(() => null)
    }

    // Not-repairable insurance path seed for demonstration
    if (dr6) {
      const nr = await prisma.insuranceClaim.create({ data: {
        repairId: dr6.id,
        insurerName: 'DemoInsurer',
        claimNumber: 'DRNR-2026-0001',
        claimStatus: 'Filed',
        notRepairableReason: 'Irreparable hardware damage',
        salvageValue: 25,
        payoutAmount: 0,
        attachments: []
      } }).catch(() => null)
      if (nr) {
        // link not-repairable report via Not-Repairable PDF (generated on demand) – placeholder
      }
    }
  } catch (e) {
    console.error('Seed error:', e)
  } finally {
    await prisma.$disconnect()
  }
}
\nmain()
  .catch((e) => {
    console.error(e)
    process.exit(1)
  })
  .finally(() => process.exit(0))
