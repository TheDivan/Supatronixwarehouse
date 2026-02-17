export type LineItem = {
  description: string
  quantity: number
  unitPrice: number
  total: number
}

export type UserPayload = {
  id: string
  roles: string[]
}
