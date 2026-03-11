import { useEffect, useState } from 'react';
import { CardHeader } from '@/components/ui/Card';
import { StatCard } from '@/components/ui/StatCard';
import { BarChartCard } from '@/components/charts/BarChartCard';
import { LineChartCard } from '@/components/charts/LineChartCard';
import { PieChartCard } from '@/components/charts/PieChartCard';
import type { AdminDashboardStats, ChartDataPoint } from '@/types';
import { api } from '@/services/api';
import {
  mockFacultyDistribution,
  mockRegionDistribution,
  mockRegistrationsByMonth,
  mockSubmissionsTrend,
} from '@/services/mockData';

export function AdminDashboard() {
  const [stats, setStats] = useState<AdminDashboardStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    api
      .get<AdminDashboardStats>('/admin/stats')
      .then((data) => {
        if (!cancelled) setStats(data);
      })
      .catch((e) => {
        if (!cancelled) setError(e instanceof Error ? e.message : 'Failed to load stats');
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });
    return () => { cancelled = true; };
  }, []);

  const facultyChartData = mockFacultyDistribution.map((f) => ({ name: f.faculty, value: f.count }));
  const regionChartData = mockRegionDistribution.map((r) => ({ name: r.region, value: r.count }));

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <p className="text-slate-500">Loading dashboard...</p>
      </div>
    );
  }
  if (error) {
    return (
      <div className="rounded-lg bg-red-50 p-4 text-red-700">
        {error}
      </div>
    );
  }

  const s = stats ?? {
    registeredStudents: 0,
    orientationChecklists: 0,
    elogbooksSubmitted: 0,
    contractsPending: 0,
    contractsApproved: 0,
    reportsSubmitted: 0,
    assumptionsCount: 0,
    visitingScoresCount: 0,
    companyScoresCount: 0,
  };

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">Admin Dashboard</h1>
        <p className="mt-1 text-slate-500">Overview of registrations, submissions, and scores</p>
      </div>

      <section>
        <CardHeader title="Overview" />
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
          <StatCard title="Registered Students" value={s.registeredStudents} variant="primary" />
          <StatCard title="Orientation Checklists" value={s.orientationChecklists} variant="success" />
          <StatCard title="E-Logbooks Submitted" value={s.elogbooksSubmitted} variant="info" />
          <StatCard
            title="Contracts"
            value={`${s.contractsApproved} / ${s.contractsPending} pending`}
            variant="warning"
          />
          <StatCard title="Reports Submitted" value={s.reportsSubmitted} variant="slate" />
        </div>
        <div className="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
          <StatCard title="Student Assumptions" value={s.assumptionsCount} variant="primary" />
          <StatCard
            title="Scores (Visiting / Company)"
            value={`${s.visitingScoresCount} / ${s.companyScoresCount}`}
            variant="success"
          />
        </div>
      </section>

      <section className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <LineChartCard title="Registrations by month" data={mockRegistrationsByMonth as ChartDataPoint[]} />
        <LineChartCard title="Submissions trend" data={mockSubmissionsTrend as ChartDataPoint[]} strokeColor="#10b981" />
      </section>

      <section className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <BarChartCard title="Students by faculty" data={facultyChartData} barColor="#0c8ee6" />
        <PieChartCard title="Students by region" data={regionChartData} />
      </section>
    </div>
  );
}
