import React, { useEffect, useState } from 'react'

type Role = { id: string; name: string }
type Perm = { id: string; name: string }

export default function RolesCrud() {
  const [roles, setRoles] = useState<Role[]>([])
  const [permissions, setPermissions] = useState<Perm[]>([])
  const [newRole, setNewRole] = useState('')
  const [newPerm, setNewPerm] = useState('')

  useEffect(() => {
    fetch('/api/admin/roles')
      .then(res => res.json())
      .then(setRoles)
      .catch(() => setRoles([]))
    fetch('/api/admin/permissions')
      .then(res => res.json())
      .then(setPermissions)
      .catch(() => setPermissions([]))
  }, [])

  async function createRole() {
    if (!newRole) return
    const r = await fetch('/api/admin/roles', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: newRole }),
    }).then(res => res.json())
    if (r?.role) {
      setRoles(prev => [...prev, r.role])
      setNewRole('')
    }
  }

  async function assignPermToRole(roleId: string) {
    if (!newPerm) return
    await fetch(`/api/admin/roles/${roleId}/permissions`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ permissionId: newPerm }),
    })
  }

  return (
    <div style={{ padding: 20 }}>
      <h1>RBAC Admin — Roles & Permissions</h1>
      <section style={{ marginBottom: 20 }}>
        <h2>Create Role</h2>
        <input value={newRole} onChange={(e) => setNewRole(e.target.value)} placeholder="New role name" />
        <button onClick={createRole} style={{ marginLeft: 8 }}>Create</button>
      </section>
      <section>
        <h2>Existing Roles</h2>
        <ul>
          {roles.map((r) => (
            <li key={r.id}>{r.name} <span style={{ color: '#888' }}>(ID: {r.id})</span></li>
          ))}
        </ul>
      </section>
      <section style={{ marginTop: 20 }}>
        <h2>Permissions</h2>
        <select value={newPerm} onChange={(e) => setNewPerm(e.target.value)}>
          <option value="">Select permission</option>
          {permissions.map((p) => (
            <option key={p.id} value={p.id}>{p.name}</option>
          ))}
        </select>
        <button onClick={() => /* no-op; placeholder */ null} style={{ marginLeft: 8 }}>Attach to Role</button>
      </section>
    </div>
  )
}
