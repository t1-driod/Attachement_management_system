import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Card, CardHeader } from '@/components/ui/Card';
import { StatCard } from '@/components/ui/StatCard';
import { BarChartCard } from '@/components/charts/BarChartCard';
import { DataTable, type Column } from '@/components/ui/DataTable';
import { Button } from '@/components/ui/Button';
import { mockVisitScoresData } from '@/services/mockData';
import { SupervisorScoreForm } from '@/pages/supervisor/SupervisorScoreForm';
import type { StudentSummary, SupervisorDashboardStats } from '@/types';
import { api } from '@/services/api';

const columns: Column<StudentSummary & { onGrade?: (student: StudentSummary) => void }>[] = [
  {
    key: 'student',
    header: 'Student',
    align: 'left',
    render: (row) => (
      <div className="text-left">
        <p className="font-medium text-slate-900">{`${row.first_name} ${row.last_name}`.trim()}</p>
        <p className="text-xs text-slate-500">{row.student_index}</p>
      </div>
    ),
  },
  { key: 'company_name', header: 'Company', align: 'center' },
  { key: 'company_region', header: 'Region', align: 'center' },
  {
    key: 'actions',
    header: 'Actions',
    align: 'center',
    render: (row) => (
      <div className="flex items-center justify-center gap-2">
        <Link to={`/supervisor/logbook/${encodeURIComponent(row.student_index)}`}>
          <Button variant="outline" size="sm">
            View logbook
          </Button>
        </Link>
        <Button
          variant="primary"
          size="sm"
          onClick={() => (row as any).onGrade?.(row)}
        >
          Enter score
        </Button>
      </div>
    ),
  },
];

export function SupervisorDashboard() {
  const [stats, setStats] = useState<SupervisorDashboardStats | null>(null);
  const [students, setStudents] = useState<StudentSummary[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [gradingStudent, setGradingStudent] = useState<StudentSummary | null>(null);

  useEffect(() => {
    Promise.all([
      api.get<SupervisorDashboardStats>('/supervisor/stats'),
      api.get<StudentSummary[]>('/supervisor/students'),
    ])
      .then(([s, st]) => {
        setStats(s);
        setStudents(st);
      })
      .catch((e) => setError(e instanceof Error ? e.message : 'Failed to load'))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p className="text-slate-500">Loading...</p>;
  if (error) return <p className="text-red-600">{error}</p>;

  const s = stats ?? {
    totalStudents: 0,
    firstVisits: 0,
    secondVisits: 0,
    firstVisitWithScoresheet: 0,
    secondVisitWithScoresheet: 0,
  };

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">Institutional Supervisor Dashboard</h1>
        <p className="mt-1 text-slate-500">Your assigned students and visit summary</p>
      </div>

      <section>
        <CardHeader title="Summary" />
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard title="Total Assigned Students" value={s.totalStudents} variant="primary" />
          <StatCard title="First Visits" value={s.firstVisits} variant="success" />
          <StatCard title="Second Visits" value={s.secondVisits} variant="info" />
          <StatCard
            title="Scoresheets (1st / 2nd)"
            value={`${s.firstVisitWithScoresheet} / ${s.secondVisitWithScoresheet}`}
            variant="warning"
          />
        </div>
      </section>

      <section>
        <BarChartCard
          title="Visit scoresheets submitted"
          data={mockVisitScoresData}
          barColor="#10b981"
          height={240}
        />
      </section>

      <section>
        <Card>
          <CardHeader title="Assigned Students" />
          <DataTable
            columns={columns}
            data={students.map((s) => ({
              ...s,
              onGrade: (student: StudentSummary) => setGradingStudent(student),
            })) as any}
            keyField="student_index"
            emptyMessage="No students have been assigned to you yet."
          />
        </Card>
      </section>

      {gradingStudent && (
        <div
          className="fixed inset-0 z-40 flex justify-end bg-slate-900/50 backdrop-blur-sm"
          onClick={(e) => e.target === e.currentTarget && setGradingStudent(null)}
        >
          <div className="flex h-full w-full max-w-3xl flex-col bg-white shadow-2xl animate-slide-up">
            <header className="flex items-start justify-between border-b border-slate-200 px-6 py-4">
              <div>
                <p className="text-xs font-semibold uppercase tracking-wide text-primary-600/80">
                  Visiting score
                </p>
                <h2 className="mt-1 text-lg font-display font-semibold text-slate-900">
                  {gradingStudent.first_name} {gradingStudent.last_name}{' '}
                  <span className="text-sm font-normal text-slate-500">
                    ({gradingStudent.student_index})
                  </span>
                </h2>
                <p className="mt-1 text-xs text-slate-500">
                  Enter first or second visit scores using the same rubric as the student portal.
                </p>
              </div>
              <button
                type="button"
                onClick={() => setGradingStudent(null)}
                className="ml-3 rounded-full border border-slate-300 bg-slate-50 p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
              >
                <span className="block leading-none text-lg">×</span>
              </button>
            </header>
            <div className="flex-1 overflow-y-auto px-6 py-4 bg-slate-50">
              <SupervisorScoreForm
                indexNumber={gradingStudent.student_index}
                onClose={() => setGradingStudent(null)}
              />
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
