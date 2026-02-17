import React from 'react'

export default function AdminRoles() {
  const [name, setName] = React.useState('')
  const [perm, setPerm] = React.useState('')
  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    // Placeholder: call backend admin role creation if authenticated
    alert('RBAC admin: role creation would be performed here. Name: ' + name)
  }
  return (
    <div style={{ padding: 20 }}>
      <h1>RBAC Admin</h1>
      <form onSubmit={onSubmit}>
        <div>
          <label>Role Name</label>
          <input value={name} onChange={(e) => setName(e.target.value)} placeholder="e.g. STORE_MANAGER" />
        </div>
        <div>
          <label>First Permission (optional)</label>
          <input value={perm} onChange={(e) => setPerm(e.target.value)} placeholder="e.g. INVENTORY_MANAGE" />
        </div>
        <button type="submit">Create Role</button>
      </form>
      <p style={{ marginTop: 12, color: '#555' }}>Note: This is a scaffold. The real admin UI wires to /api/admin endpoints with proper auth in a future patch.</p>
    </div>
  )
}
