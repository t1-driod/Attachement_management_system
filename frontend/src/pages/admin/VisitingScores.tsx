import { useState, useEffect } from 'react';
import { Card, CardHeader } from '@/components/ui/Card';
import { DataTable, type Column } from '@/components/ui/DataTable';
import { api } from '@/services/api';

interface ScoreRow {
  index_number: string;
  first_name: string;
  last_name: string;
  programme: string;
  level: string;
  session: string;
  first_visit_grade: number | null;
  second_visit_grade: number | null;
}

const columns: Column<ScoreRow>[] = [
  { key: 'index_number', header: 'Index Number' },
  { key: 'first_name', header: 'First Name' },
  { key: 'last_name', header: 'Last Name' },
  { key: 'programme', header: 'Programme' },
  { key: 'level', header: 'Level' },
  { key: 'session', header: 'Session' },
  {
    key: 'first_visit_grade',
    header: 'First visit',
    align: 'center',
    render: (row) =>
      row.first_visit_grade != null ? (
        <span className="font-medium text-slate-800">{row.first_visit_grade}</span>
      ) : (
        <span className="text-slate-400">—</span>
      ),
  },
  {
    key: 'second_visit_grade',
    header: 'Second visit',
    align: 'center',
    render: (row) =>
      row.second_visit_grade != null ? (
        <span className="font-medium text-slate-800">{row.second_visit_grade}</span>
      ) : (
        <span className="text-slate-400">—</span>
      ),
  },
];

export function VisitingScores() {
  const [rows, setRows] = useState<ScoreRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api
      .get<ScoreRow[]>('/admin/visiting-scores')
      .then(setRows)
      .catch((e) => setError(e instanceof Error ? e.message : 'Failed to load'))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">Visiting Supervisors Score</h1>
        <p className="mt-1 text-slate-500">First and second visit scores from visiting supervisors</p>
      </div>
      <Card>
        <CardHeader title="Scores" />
        {error && <p className="mb-2 text-sm text-red-600">{error}</p>}
        {loading ? (
          <p className="text-slate-500">Loading...</p>
        ) : (
          <DataTable columns={columns} data={rows} keyField="index_number" emptyMessage="No visiting scores yet." />
        )}
      </Card>
    </div>
  );
}
