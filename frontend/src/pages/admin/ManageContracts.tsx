import { useState, useEffect } from 'react';
import { Card, CardHeader } from '@/components/ui/Card';
import { DataTable, type Column } from '@/components/ui/DataTable';
import { Button } from '@/components/ui/Button';
import { api } from '@/services/api';

interface ContractRow {
  id: number;
  index_number: string;
  student_name: string;
  status: string;
  submission_date: string | null;
  admin_comment: string;
  original_filename: string;
}

const columns: Column<ContractRow>[] = [
  { key: 'index_number', header: 'Index Number' },
  { key: 'student_name', header: 'Student Name' },
  { key: 'status', header: 'Status' },
  { key: 'submission_date', header: 'Submitted At' },
  { key: 'original_filename', header: 'File' },
];

export function ManageContracts() {
  const [rows, setRows] = useState<ContractRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [statusFilter, setStatusFilter] = useState('');
  const [actionLoading, setActionLoading] = useState<number | null>(null);

  const fetchContracts = () => {
    setLoading(true);
    const params = statusFilter ? `?status=${encodeURIComponent(statusFilter)}` : '';
    api
      .get<ContractRow[]>(`/admin/contracts${params}`)
      .then(setRows)
      .catch((e) => setError(e instanceof Error ? e.message : 'Failed to load'))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    fetchContracts();
  }, [statusFilter]);

  const handleAction = async (contractId: number, action: 'approve' | 'reject') => {
    setActionLoading(contractId);
    try {
      await api.post('/admin/contracts', { action, contract_id: contractId, admin_comment: '' });
      fetchContracts();
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Action failed');
    } finally {
      setActionLoading(null);
    }
  };

  const columnsWithActions: Column<ContractRow>[] = [
    ...columns,
    {
      key: 'id',
      header: 'Actions',
      align: 'center',
      render: (row) => (
        row.status === 'pending' ? (
          <span className="flex justify-center gap-2">
            <Button
              variant="primary"
              size="sm"
              disabled={actionLoading !== null}
              onClick={() => handleAction(row.id, 'approve')}
            >
              Approve
            </Button>
            <Button
              variant="outline"
              size="sm"
              disabled={actionLoading !== null}
              onClick={() => handleAction(row.id, 'reject')}
            >
              Reject
            </Button>
          </span>
        ) : (
          <span className="text-slate-500">{row.status}</span>
        )
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">View Contracts</h1>
        <p className="mt-1 text-slate-500">Approve or reject student contracts</p>
      </div>
      <Card>
        <CardHeader title="Contracts" />
        <div className="mb-4 flex items-center gap-3">
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
          >
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
          <Button variant="outline" size="sm" onClick={fetchContracts}>Refresh</Button>
        </div>
        {error && <p className="mb-2 text-sm text-red-600">{error}</p>}
        {loading ? (
          <p className="text-slate-500">Loading...</p>
        ) : (
          <DataTable columns={columnsWithActions} data={rows} keyField="id" emptyMessage="No contracts to review." />
        )}
      </Card>
    </div>
  );
}
