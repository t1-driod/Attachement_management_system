import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';

export function AdminLoginPage() {
  const [password, setPassword] = useState('');
  const { login, error, clearError } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    const ok = await login('admin', { password });
    if (ok) navigate('/admin/dashboard');
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-900 via-primary-900 to-slate-900 px-4">
      <Card className="w-full max-w-md" padding="lg">
        <h1 className="mb-2 text-xl font-display font-semibold text-slate-800">Administrator Login</h1>
        <p className="mb-6 text-sm text-slate-500">Enter your admin password</p>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label htmlFor="admin_password" className="block text-sm font-medium text-slate-700">Password</label>
            <input
              id="admin_password"
              type="password"
              value={password}
              onChange={(e) => { setPassword(e.target.value); clearError(); }}
              className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
              placeholder="Enter password"
            />
          </div>
          {error && <p className="text-sm text-red-600">{error}</p>}
          <Button type="submit" className="w-full" disabled={!password}>Login</Button>
        </form>
        <div className="mt-6 text-center">
          <a href="/login" className="text-sm text-primary-600 hover:underline">Back to main login</a>
        </div>
      </Card>
    </div>
  );
}
