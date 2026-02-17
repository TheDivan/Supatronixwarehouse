import React, { useState } from 'react'

export default function PortalLogin() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const handleSubmit = (e: any) => {
    e.preventDefault()
    // Simple placeholder: in real app, call /api/auth/login and store token
    alert('Login is a placeholder in MVP Phase 2 scaffold.')
  }
  return (
    <div style={{ padding: 20 }}>
      <h2>Customer Portal Login</h2>
      <form onSubmit={handleSubmit}>
        <div>
          <label>Email</label>
          <input value={email} onChange={(e) => setEmail(e.target.value)} />
        </div>
        <div>
          <label>Password</label>
          <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
        </div>
        <button type="submit">Login</button>
      </form>
    </div>
  )
}
