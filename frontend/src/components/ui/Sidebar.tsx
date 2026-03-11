import { NavLink, useNavigate } from 'react-router-dom';

export interface SidebarItem {
  to: string;
  label: string;
  end?: boolean;
  isLogout?: boolean;
}

interface SidebarProps {
  items: SidebarItem[];
  basePath: string;
  onLogout?: () => void;
}

const navLinkClass = ({ isActive }: { isActive: boolean }) =>
  `block rounded-lg px-3 py-2.5 text-sm font-medium transition-colors ${
    isActive
      ? 'bg-primary-600 text-white'
      : 'text-slate-300 hover:bg-surface-sidebarHover hover:text-white'
  }`;

export function Sidebar({ items, basePath, onLogout }: SidebarProps) {
  const navigate = useNavigate();

  const handleLogoutClick = () => {
    onLogout?.();
    navigate('/login');
  };

  return (
    <aside className="fixed left-0 top-14 z-30 h-[calc(100vh-3.5rem)] w-56 border-r border-slate-700 bg-surface-sidebar">
      <nav className="flex flex-col gap-1 p-3" aria-label="Sidebar">
        {items.map((item) =>
          item.isLogout ? (
            <button
              key={item.to}
              type="button"
              onClick={handleLogoutClick}
              className="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-slate-300 transition-colors hover:bg-surface-sidebarHover hover:text-white"
            >
              {item.label}
            </button>
          ) : (
            <NavLink
              key={item.to}
              to={item.to.startsWith('/') ? item.to : `${basePath}/${item.to}`}
              end={item.end ?? (item.to === '' || item.to === 'dashboard')}
              className={navLinkClass}
            >
              {item.label}
            </NavLink>
          )
        )}
      </nav>
    </aside>
  );
}
