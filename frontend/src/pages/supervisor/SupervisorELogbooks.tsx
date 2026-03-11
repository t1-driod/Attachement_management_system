import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Card, CardHeader } from '@/components/ui/Card';
import { DataTable, type Column } from '@/components/ui/DataTable';
import { Button } from '@/components/ui/Button';
import { api } from '@/services/api';

interface LogbookRow {
  index_number: string;
  student_name: string;
  total_weeks: number;
  first_submission: string | null;
  last_updated: string | null;
}

const columns: Column<LogbookRow>[] = [
  {
    key: 'student_name',
    header: 'Student',
    render: (row) => (
      <div>
        <p className="font-medium text-slate-900">{row.student_name || '-'}</p>
        <p className="text-xs text-slate-500">{row.index_number}</p>
      </div>
    ),
  },
  { key: 'total_weeks', header: 'Weeks Completed', align: 'center' },
  {
    key: 'index_number',
    header: 'View logbook',
    align: 'center',
    render: (row) => (
      <Link to={`/supervisor/logbook/${encodeURIComponent(row.index_number)}`}>
        <Button variant="outline" size="sm">
          View logbook
        </Button>
      </Link>
    ),
  },
];

export function SupervisorELogbooks() {
  const [rows, setRows] = useState<LogbookRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api
      .get<LogbookRow[]>('/supervisor/elogbooks')
      .then(setRows)
      .catch((e) => setError(e instanceof Error ? e.message : 'Failed to load'))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">Students&apos; E-Logbooks</h1>
        <p className="mt-1 text-slate-500">
          E-logbook submissions from students assigned to you.
        </p>
      </div>
      <Card>
        <CardHeader title="Logbooks" />
        {error && <p className="mb-2 text-sm text-red-600">{error}</p>}
        {loading ? (
          <p className="text-slate-500">Loading...</p>
        ) : (
          <DataTable
            columns={columns}
            data={rows}
            keyField="index_number"
            emptyMessage="No e-logbook entries yet for your assigned students."
          />
        )}
      </Card>
    </div>
  );
}

