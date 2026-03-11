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
  { key: 'index_number', header: 'Index Number' },
  { key: 'first_name', header: 'First Name' },
  { key: 'last_name', header: 'Last Name' },
  { key: 'company_name', header: 'Company Name' },
  { key: 'company_region', header: 'Region' },
  { key: 'programme', header: 'Programme' },
  { key: 'level', header: 'Level' },
  { key: 'session', header: 'Session' },
];

export function StudentAssumptions() {
  const [rows, setRows] = useState<AssumptionRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api
      .get<AssumptionRow[]>('/admin/assumptions')
      .then(setRows)
      .catch((e) => setError(e instanceof Error ? e.message : 'Failed to load'))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">Student Assumptions</h1>
        <p className="mt-1 text-slate-500">Company and region assumptions per student</p>
      </div>
      <Card>
        <CardHeader title="Assumptions" />
        {error && <p className="mb-2 text-sm text-red-600">{error}</p>}
        {loading ? (
          <p className="text-slate-500">Loading...</p>
        ) : (
          <DataTable columns={columns} data={rows} keyField="index_number" emptyMessage="No assumption records." />
        )}
      </Card>
    </div>
  );
}
