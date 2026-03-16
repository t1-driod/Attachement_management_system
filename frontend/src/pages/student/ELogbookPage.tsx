import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { useAuth } from '@/hooks/useAuth';
import { api } from '@/services/api';

interface LogbookEntry {
  id: number;
  week_number: number;
  monday_job_assigned: string;
  monday_skill_acquired: string;
  tuesday_job_assigned: string;
  tuesday_skill_acquired: string;
  wednesday_job_assigned: string;
  wednesday_skill_acquired: string;
  thursday_job_assigned: string;
  thursday_skill_acquired: string;
  friday_job_assigned: string;
  friday_skill_acquired: string;
}

interface ElogbookResponse {
  index_number: string;
  entries: LogbookEntry[];
}

const DAYS = [
  { day: 'Monday', jobKey: 'monday_job_assigned', skillKey: 'monday_skill_acquired' },
  { day: 'Tuesday', jobKey: 'tuesday_job_assigned', skillKey: 'tuesday_skill_acquired' },
  { day: 'Wednesday', jobKey: 'wednesday_job_assigned', skillKey: 'wednesday_skill_acquired' },
  { day: 'Thursday', jobKey: 'thursday_job_assigned', skillKey: 'thursday_skill_acquired' },
  { day: 'Friday', jobKey: 'friday_job_assigned', skillKey: 'friday_skill_acquired' },
] as const;

const emptyWeek = () => ({
  monday_job_assigned: '',
  monday_skill_acquired: '',
  tuesday_job_assigned: '',
  tuesday_skill_acquired: '',
  wednesday_job_assigned: '',
  wednesday_skill_acquired: '',
  thursday_job_assigned: '',
  thursday_skill_acquired: '',
  friday_job_assigned: '',
  friday_skill_acquired: '',
});

export function ELogbookPage() {
  const { user } = useAuth();
  const indexNumber = user?.indexNumber ?? '';

  const [entries, setEntries] = useState<LogbookEntry[]>([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

  const [currentWeek, setCurrentWeek] = useState(1);
  const [form, setForm] = useState(emptyWeek());

  useEffect(() => {
    if (!indexNumber) return;
    api
      .get<ElogbookResponse>(`/elogbook/${encodeURIComponent(indexNumber)}`)
      .then((res) => {
        setEntries(res.entries ?? []);
        const maxWeek = res.entries?.length
          ? Math.max(...res.entries.map((e) => e.week_number))
          : 0;
        if (maxWeek >= 1) setCurrentWeek(maxWeek);
      })
      .catch(() => setEntries([]))
      .finally(() => setLoading(false));
  }, [indexNumber]);

  const currentEntry = entries.find((e) => e.week_number === currentWeek);

  useEffect(() => {
    if (currentEntry) {
      setForm({
        monday_job_assigned: currentEntry.monday_job_assigned ?? '',
        monday_skill_acquired: currentEntry.monday_skill_acquired ?? '',
        tuesday_job_assigned: currentEntry.tuesday_job_assigned ?? '',
        tuesday_skill_acquired: currentEntry.tuesday_skill_acquired ?? '',
        wednesday_job_assigned: currentEntry.wednesday_job_assigned ?? '',
        wednesday_skill_acquired: currentEntry.wednesday_skill_acquired ?? '',
        thursday_job_assigned: currentEntry.thursday_job_assigned ?? '',
        thursday_skill_acquired: currentEntry.thursday_skill_acquired ?? '',
        friday_job_assigned: currentEntry.friday_job_assigned ?? '',
        friday_skill_acquired: currentEntry.friday_skill_acquired ?? '',
      });
    } else {
      setForm(emptyWeek());
    }
  }, [currentWeek, currentEntry?.id]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    const filled = DAYS.every(
      (d) => (form[d.jobKey as keyof typeof form] as string)?.trim() && (form[d.skillKey as keyof typeof form] as string)?.trim()
    );
    if (!filled) {
      setMessage({ type: 'error', text: 'All fields are required.' });
      return;
    }
    setSubmitting(true);
    setMessage(null);
    try {
      const res = await api.post<{ success: boolean; error?: string }>('/student/elogbook', {
        week_number: currentWeek,
        ...form,
      });
      if (res.success) {
        setMessage({ type: 'success', text: `Week ${currentWeek} ${(res as { updated?: boolean }).updated ? 'updated' : 'saved'} successfully!` });
        const updated = {
          id: 0,
          week_number: currentWeek,
          ...form,
        };
        setEntries((prev) => {
          const idx = prev.findIndex((e) => e.week_number === currentWeek);
          if (idx >= 0) {
            const next = [...prev];
            next[idx] = { ...next[idx], ...form };
            return next;
          }
          return [...prev, updated];
        });
      } else {
        setMessage({ type: 'error', text: res.error ?? 'Save failed' });
      }
    } catch (err) {
      setMessage({ type: 'error', text: err instanceof Error ? err.message : 'Save failed' });
    } finally {
      setSubmitting(false);
    }
  };

  const addNewWeek = () => {
    const maxWeek = entries.length ? Math.max(...entries.map((e) => e.week_number)) : 0;
    setCurrentWeek(maxWeek + 1);
    setForm(emptyWeek());
    setMessage(null);
  };

  if (!indexNumber) {
    return (
      <p className="text-slate-500">You must be logged in as a student to use the e-logbook.</p>
    );
  }

  if (loading) {
    return <p className="text-slate-500">Loading e-logbook...</p>;
  }

  const weekNumbersFromEntries = entries.length
    ? Array.from(new Set(entries.map((e) => e.week_number))).sort((a, b) => a - b)
    : [];
  const weekNumbers = Array.from(new Set([...weekNumbersFromEntries, currentWeek])).sort((a, b) => a - b);
  const hasCurrentWeekData = !!currentEntry;

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <h1 className="text-2xl font-display font-bold text-slate-900">E-Logbook</h1>
        <Link to="/student">
          <Button variant="outline" size="sm">Back to Dashboard</Button>
        </Link>
      </div>

      {message && (
        <div
          className={`rounded-lg p-4 ${
            message.type === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'
          }`}
        >
          {message.text}
        </div>
      )}

      <div className="flex flex-wrap items-center gap-2 rounded-lg bg-slate-100 p-4 shadow-md">
        <span className="font-medium text-slate-700">Weeks:</span>
        {weekNumbers.length === 0 && <span className="text-slate-500">No weeks yet. Start with Week 1 below.</span>}
        {weekNumbers.map((w) => (
          <button
            key={w}
            type="button"
            onClick={() => setCurrentWeek(w)}
            className={`rounded px-3 py-1.5 text-sm font-medium ${
              w === currentWeek
                ? 'bg-primary-600 text-white'
                : 'bg-slate-200 text-slate-700 hover:bg-slate-300'
            }`}
          >
            Week {w}
          </button>
        ))}
        <Button variant="primary" size="sm" onClick={addNewWeek} className="ml-2">
          + Add new week
        </Button>
      </div>

      <Card className="p-6 shadow-lg">
        <h2 className="mb-4 text-lg font-semibold text-slate-800">Week {currentWeek}</h2>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="overflow-x-auto">
            <table className="min-w-full border border-slate-200 text-sm">
              <thead>
                <tr className="bg-slate-50">
                  <th className="border border-slate-200 px-4 py-2 text-center font-medium text-slate-700">Day</th>
                  <th className="border border-slate-200 px-4 py-2 text-center font-medium text-slate-700">Job assigned</th>
                  <th className="border border-slate-200 px-4 py-2 text-center font-medium text-slate-700">Skill acquired</th>
                </tr>
              </thead>
              <tbody>
                {DAYS.map(({ day, jobKey, skillKey }) => (
                  <tr key={day} className="border-b border-slate-200">
                    <td className="border border-slate-200 px-4 py-2 text-center font-medium text-slate-700">
                      {day}
                    </td>
                    <td className="border border-slate-200 p-2">
                      <textarea
                        value={form[jobKey as keyof typeof form] as string}
                        onChange={(e) => setForm((f) => ({ ...f, [jobKey]: e.target.value }))}
                        className="min-h-[80px] w-full rounded border border-slate-300 px-2 py-1.5 text-slate-800"
                        rows={3}
                      />
                    </td>
                    <td className="border border-slate-200 p-2">
                      <textarea
                        value={form[skillKey as keyof typeof form] as string}
                        onChange={(e) => setForm((f) => ({ ...f, [skillKey]: e.target.value }))}
                        className="min-h-[80px] w-full rounded border border-slate-300 px-2 py-1.5 text-slate-800"
                        rows={3}
                      />
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          <div className="flex gap-2">
            <Button type="submit" disabled={submitting}>
              {hasCurrentWeekData ? (submitting ? 'Updating...' : 'Update') : (submitting ? 'Saving...' : 'Save')}
            </Button>
          </div>
        </form>
      </Card>
    </div>
  );
}
