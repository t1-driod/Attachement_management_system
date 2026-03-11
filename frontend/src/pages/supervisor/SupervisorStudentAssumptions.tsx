import { useState, useEffect } from 'react';
import { Card, CardHeader } from '@/components/ui/Card';
import { DataTable, type Column } from '@/components/ui/DataTable';
import { api } from '@/services/api';

interface AssumptionRow {
  index_number: string;
  first_name: string;
  last_name: string;
  programme: string;
  level: string;
  session: string;
  company_name: string;
  company_region: string;
}

const columns: Column<AssumptionRow>[] = [
  {
    key: 'student',
    header: 'Student',
    render: (row) => (
      <div>
        <p className="font-medium text-slate-900">
          {row.first_name} {row.last_name}
        </p>
        <p className="text-xs text-slate-500">{row.index_number}</p>
      </div>
    ),
  },
  { key: 'company_name', header: 'Company', align: 'left' },
  { key: 'company_region', header: 'Region', align: 'left' },
  { key: 'programme', header: 'Programme', align: 'left' },
  { key: 'level', header: 'Level', align: 'center' },
  { key: 'session', header: 'Session', align: 'center' },
];

export function SupervisorStudentAssumptions() {
  const [rows, setRows] = useState<AssumptionRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api
      .get<AssumptionRow[]>('/supervisor/assumptions')
      .then(setRows)
      .catch((e) => setError(e instanceof Error ? e.message : 'Failed to load'))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">Student Assumptions</h1>
        <p className="mt-1 text-slate-500">
          Company and region assumptions for students assigned to you.
        </p>
      </div>
      <Card>
        <CardHeader title="Assumptions" />
        {error && <p className="mb-2 text-sm text-red-600">{error}</p>}
        {loading ? (
          <p className="text-slate-500">Loading...</p>
        ) : (
          <DataTable
            columns={columns}
            data={rows}
            keyField="index_number"
            emptyMessage="No assumption records for your assigned students."
          />
        )}
      </Card>
    </div>
  );
}

