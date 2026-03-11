import { Link } from 'react-router-dom';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';

export function InstructionsPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-display font-bold text-slate-900">Instructions</h1>
        <p className="mt-1 text-slate-500">Please read the following before proceeding</p>
      </div>
      <Card padding="lg">
        <h2 className="text-lg font-semibold text-slate-800">Welcome to IASMS</h2>
        <p className="mt-2 text-slate-600">
          Complete your orientation checklist, submit your contract, maintain your e-logbook weekly, and submit your final report as per the schedule. Use the dashboard links to access each task.
        </p>
        <Link to="/student" className="mt-4 inline-block">
          <Button variant="outline">Back to Dashboard</Button>
        </Link>
      </Card>
    </div>
  );
}
