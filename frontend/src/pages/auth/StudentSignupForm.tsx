import { useState } from 'react';
import { Button } from '@/components/ui/Button';
import { api } from '@/services/api';

const inputClass =
  'mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500';
const labelClass = 'block text-sm font-medium text-slate-700';

export function StudentSignupForm({ onSuccess, onCancel }: { onSuccess?: () => void; onCancel: () => void }) {
  const [first_name, setFirst_name] = useState('');
  const [last_name, setLast_name] = useState('');
  const [index_number, setIndex_number] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    if (!first_name.trim() || !last_name.trim() || !index_number.trim() || !password) {
      setError('Provide details for all fields.');
      return;
    }
    setLoading(true);
    try {
      const data = await api.post<{ success: boolean; error?: string; message?: string }>('/auth/register', {
        first_name: first_name.trim(),
        last_name: last_name.trim(),
        index_number: index_number.trim(),
        password,
      });
      if (data.success) {
        setSuccess(true);
        onSuccess?.();
      } else {
        setError(data.error ?? 'Registration failed.');
      }
    } catch (err: unknown) {
      const msg = err && typeof err === 'object' && 'message' in err ? String((err as Error).message) : 'Registration failed.';
      setError(msg);
    } finally {
      setLoading(false);
    }
  };

  if (success) {
    return (
      <div className="rounded-2xl border border-slate-700 bg-slate-900/95 p-6 text-center">
        <p className="text-primary-300 font-medium">Registration successful.</p>
        <p className="mt-1 text-sm text-slate-400">You can now sign in with your index number and password.</p>
        <Button type="button" onClick={onCancel} className="mt-4" variant="outline">
          Close
        </Button>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div>
        <label htmlFor="signup-firstname" className={labelClass}>First Name</label>
        <input
          id="signup-firstname"
          type="text"
          value={first_name}
          onChange={(e) => setFirst_name(e.target.value)}
          className={inputClass}
          placeholder="Enter first name"
          autoComplete="given-name"
        />
      </div>
      <div>
        <label htmlFor="signup-lastname" className={labelClass}>Last Name</label>
        <input
          id="signup-lastname"
          type="text"
          value={last_name}
          onChange={(e) => setLast_name(e.target.value)}
          className={inputClass}
          placeholder="Enter last name"
          autoComplete="family-name"
        />
      </div>
      <div>
        <label htmlFor="signup-index" className={labelClass}>Index / Reg Number</label>
        <input
          id="signup-index"
          type="text"
          value={index_number}
          onChange={(e) => setIndex_number(e.target.value)}
          className={inputClass}
          placeholder="e.g. 04/2014/0001D"
          autoComplete="username"
        />
      </div>
      <div>
        <label htmlFor="signup-password" className={labelClass}>Password</label>
        <input
          id="signup-password"
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          className={inputClass}
          placeholder="Choose a password"
          autoComplete="new-password"
        />
      </div>
      {error && <p className="text-sm text-red-400">{error}</p>}
      <div className="flex flex-wrap items-center justify-between gap-3 pt-2">
        <Button type="submit" disabled={loading}>
          {loading ? 'Signing up…' : 'Sign Up'}
        </Button>
        <button type="button" onClick={onCancel} className="text-sm text-slate-400 hover:text-slate-200 hover:underline">
          Cancel
        </button>
      </div>
    </form>
  );
}
