import { useState, useEffect } from 'react';
import { Card, CardHeader } from '@/components/ui/Card';
import { DataTable, type Column } from '@/components/ui/DataTable';
import { api } from '@/services/api';

interface ReportRow {
  name: string;
  size: number;
  modified: string;
}

const columns: Column<ReportRow>[] = [
  { key: 'name', header: 'File Name' },
  { key: 'size', header: 'Size (bytes)' },
  { key: 'modified', header: 'Modified' },
  {
    key: 'name',
    header: 'Download',
    align: 'center',
    render: (row) => (
      <a
        href={`/iasms/submit_report/uploads/${encodeURIComponent(row.name)}`}
        target="_blank"
        rel="noopener noreferrer"
        className="text-primary-600 hover:underline"
      >
        Download
      </a>
    ),
  },
];

export function SubmittedReports() {
  const [rows, setRows] = useState<ReportRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api
      .get<ReportRow[]>('/admin/reports')
      .then(setRows)
      .catch((e) => setError(e instanceof Error ? e.message : 'Failed to load'))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">View Submitted Reports</h1>
        <p className="mt-1 text-slate-500">View and download student reports</p>
      </div>
      <Card>
        <CardHeader title="Reports" />
        {error && <p className="mb-2 text-sm text-red-600">{error}</p>}
        {loading ? (
          <p className="text-slate-500">Loading...</p>
        ) : (
          <DataTable columns={columns} data={rows} keyField="name" emptyMessage="No reports submitted yet." />
        )}
      </Card>
    </div>
  );
}
