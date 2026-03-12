import { Link } from 'react-router-dom';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { useAuth } from '@/hooks/useAuth';

const LEGACY_BASE = import.meta.env.DEV ? 'http://localhost/iasms' : '';

const quickLinks = [
  { to: '/student/instructions', label: 'Instructions', description: 'Read post-login instructions', external: false },
  { to: '/student/elogbook', label: 'E-Logbook', description: 'Submit weekly logbook entries', external: false },
  { to: '/student/orientation', label: 'Orientation Checklist', description: 'Complete orientation checklist', external: false },
  { to: '/student/contract', label: 'Submit Contract', description: 'Upload attachment contract', external: false },
  { to: '/student/report', label: 'Submit Report', description: 'Upload final report', external: false },
  { to: `${LEGACY_BASE}/student_assumption/student_assumption.php`, label: 'Student Assumption', description: 'Set company and region', external: true },
];

const supervisorAssessmentLinks = [
  {
    to: '/student/supervisor/visiting',
    label: 'Visiting Supervisor Assessment',
    description: 'Your visiting supervisor can log in here to assess you and enter marks.',
    external: false,
  },
  {
    to: '/student/supervisor/company',
    label: 'Company Supervisor Assessment',
    description: 'Your company supervisor can log in here to assess you and enter marks.',
    external: false,
  },
];

export function StudentDashboard() {
  const { user } = useAuth();

  return (
    <div className="space-y-10">
      {/* Welcome header */}
      <div className="rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 px-6 py-8 text-white shadow-lg">
        <h1 className="text-2xl font-display font-bold tracking-tight md:text-3xl">
          Welcome back, {user?.name ?? 'Student'}
        </h1>
        <p className="mt-2 text-primary-100">
          Industrial attachment tasks and links
        </p>
      </div>

      {/* Supervisor assessments – where supervisors enter marks */}
      <section>
        <h2 className="mb-4 text-lg font-semibold text-slate-800">Supervisor assessments</h2>
        <p className="mb-6 text-sm text-slate-500">
          Open an assessment so your supervisor can log in with their password and enter your marks using the same competency form.
        </p>
        <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
          {supervisorAssessmentLinks.map((link) => (
            <Card
              key={link.to}
              className="flex flex-col border border-slate-200 bg-white transition-all hover:border-primary-200 hover:shadow-md"
            >
              <div className="flex-1">
                <h3 className="font-semibold text-slate-800">{link.label}</h3>
                <p className="mt-1.5 text-sm text-slate-500">{link.description}</p>
              </div>
              <div className="mt-4">
                {link.external ? (
                  <a href={link.to} target="_blank" rel="noopener noreferrer" className="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Open <span className="text-slate-400" aria-hidden>↗</span>
                  </a>
                ) : (
                  <Link to={link.to}>
                    <Button variant="outline" size="sm">Open assessment</Button>
                  </Link>
                )}
              </div>
            </Card>
          ))}
        </div>
      </section>

      {/* Tasks & links */}
      <section>
        <h2 className="mb-4 text-lg font-semibold text-slate-800">Tasks & links</h2>
        <p className="mb-6 text-sm text-slate-500">
          Complete your attachment requirements and access forms below.
        </p>
        <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {quickLinks.map((link) => (
            <Card
              key={link.to}
              className="flex flex-col border border-slate-200 bg-white transition-all hover:border-primary-200 hover:shadow-md"
            >
              <div className="flex-1">
                <h3 className="font-semibold text-slate-800">{link.label}</h3>
                <p className="mt-1.5 text-sm text-slate-500">{link.description}</p>
              </div>
              <div className="mt-4">
                {link.external ? (
                  <a
                    href={link.to}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                  >
                    Open
                    <span className="text-slate-400" aria-hidden>↗</span>
                  </a>
                ) : (
                  <Link to={link.to}>
                    <Button variant="outline" size="sm">Open</Button>
                  </Link>
                )}
              </div>
            </Card>
          ))}
        </div>
      </section>
    </div>
  );
}
