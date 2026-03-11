import { Outlet } from 'react-router-dom';
import { TopBar } from '@/components/ui/TopBar';
import { useAuth } from '@/hooks/useAuth';

export function StudentLayout() {
  const { user, logout } = useAuth();

  return (
    <div className="min-h-screen bg-surface">
      <TopBar displayName={user?.name ?? 'Student'} onLogout={logout} />
      <main className="pt-14 min-h-screen">
        <div className="p-6">
          <Outlet />
        </div>
      </main>
    </div>
  );
}
