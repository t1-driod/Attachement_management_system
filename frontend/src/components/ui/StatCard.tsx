import React from 'react';

type StatCardVariant = 'primary' | 'success' | 'info' | 'warning' | 'slate';

const variantStyles: Record<StatCardVariant, string> = {
  primary: 'bg-primary-600 text-white shadow-primary-600/20',
  success: 'bg-emerald-600 text-white shadow-emerald-600/20',
  info: 'bg-cyan-600 text-white shadow-cyan-600/20',
  warning: 'bg-amber-500 text-slate-900 shadow-amber-500/20',
  slate: 'bg-slate-700 text-white shadow-slate-700/20',
};

interface StatCardProps {
  title: string;
  value: string | number;
  variant?: StatCardVariant;
  subtitle?: string;
  icon?: React.ReactNode;
  className?: string;
}

export function StatCard({
  title,
  value,
  variant = 'primary',
  subtitle,
  icon,
  className = '',
}: StatCardProps) {
  return (
    <div
      className={`rounded-xl p-5 shadow-card transition-shadow hover:shadow-cardHover ${variantStyles[variant]} ${className}`}
    >
      <div className="flex items-start justify-between">
        <div>
          <p className="text-sm font-medium opacity-90">{title}</p>
          <p className="mt-1 text-2xl font-bold tracking-tight font-display">{value}</p>
          {subtitle && <p className="mt-0.5 text-xs opacity-85">{subtitle}</p>}
        </div>
        {icon && <div className="opacity-90">{icon}</div>}
      </div>
    </div>
  );
}
