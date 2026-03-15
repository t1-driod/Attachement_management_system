import { Link } from 'react-router-dom';

interface TopBarProps {
  title?: string;
  displayName: string;
  logoUrl?: string;
  onLogout?: () => void;
  searchPlaceholder?: string;
  /** Link for avatar/name (e.g. /student/profile). When set, avatar and name are clickable. */
  profileLink?: string;
  /** URL for profile photo. When set, avatar shows image with fallback to initial. */
  profilePhotoUrl?: string;
}

export function TopBar({
  displayName,
  logoUrl = '/img/header_log.png',
  onLogout,
  searchPlaceholder = 'Search logbook, forms, and more',
  profileLink,
  profilePhotoUrl,
}: TopBarProps) {
  const initial = displayName.trim() ? displayName.trim().charAt(0).toUpperCase() : '?';

  const avatarContent = profilePhotoUrl ? (
    <img
      src={profilePhotoUrl}
      alt=""
      className="h-8 w-8 rounded-full object-cover ring-1 ring-slate-200"
      onError={(e) => {
        const el = e.currentTarget;
        el.style.display = 'none';
        const fallback = el.nextElementSibling as HTMLElement;
        if (fallback) fallback.style.display = 'flex';
      }}
    />
  ) : null;
  const avatarFallback = (
    <div
      className="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm font-medium text-white"
      style={profilePhotoUrl ? { display: 'none' } : undefined}
    >
      {initial}
    </div>
  );

  const userBlock = (
    <>
      <div className="relative flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full">
        {avatarContent}
        {avatarFallback}
      </div>
      <span className="max-w-[120px] truncate text-sm font-medium text-slate-700">
        {displayName}
      </span>
      {onLogout && (
        <button
          type="button"
          onClick={onLogout}
          className="rounded-lg px-2 py-1 text-sm text-slate-500 hover:bg-slate-100 hover:text-slate-700"
        >
          Logout
        </button>
      )}
    </>
  );

  return (
    <header className="sticky top-0 z-40 flex h-14 items-center gap-4 border-b border-slate-200 bg-white px-4 shadow-card">
      <div className="flex shrink-0 items-center">
        {logoUrl && (
          <img src={logoUrl} alt="Logo" className="h-8 w-auto object-contain" />
        )}
      </div>
      <div className="flex flex-1 items-center justify-center">
        <div className="relative w-full max-w-md">
          <span className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden>
            &#8981;
          </span>
          <input
            type="text"
            placeholder={searchPlaceholder}
            className="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:ring-1 focus:ring-primary-500"
          />
        </div>
      </div>
      <div className="flex items-center gap-2">
        <button
          type="button"
          className="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
          title="Notifications"
          aria-label="Notifications"
        >
          &#128276;
        </button>
        <button
          type="button"
          className="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
          title="Help"
          aria-label="Help"
        >
          ?
        </button>
        <div className="flex items-center gap-2 pl-2">
          {profileLink ? (
            <Link
              to={profileLink}
              className="flex items-center gap-2 rounded-lg py-1 pr-1 transition hover:bg-slate-100"
              title="Edit profile"
            >
              {userBlock}
            </Link>
          ) : (
            <div className="flex items-center gap-2">{userBlock}</div>
          )}
        </div>
      </div>
    </header>
  );
}
