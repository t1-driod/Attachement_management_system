import type { ReactNode } from 'react';

export interface Column<T> {
  key: keyof T | string;
  header: string;
  render?: (row: T) => ReactNode;
  align?: 'left' | 'center' | 'right';
}

interface DataTableProps<T> {
  columns: Column<T>[];
  data: T[];
  keyField: keyof T | string;
  emptyMessage?: string;
  className?: string;
}

export function DataTable<T extends object>({
  columns,
  data,
  keyField,
  emptyMessage = 'No data to display.',
  className = '',
}: DataTableProps<T>) {
  const getValue = (row: T, key: keyof T | string): unknown =>
    (row as Record<string, unknown>)[key as string];

  if (data.length === 0) {
    return (
      <div className={`rounded-lg border border-slate-200 bg-slate-50 py-12 text-center text-slate-500 ${className}`}>
        {emptyMessage}
      </div>
    );
  }

  return (
    <div className={`overflow-hidden rounded-lg border border-slate-200 ${className}`}>
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-slate-200 text-sm">
          <thead className="bg-slate-50">
            <tr>
              {columns.map((col) => (
                <th
                  key={String(col.key)}
                  className={`px-4 py-3 font-medium text-slate-700 ${col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left'}`}
                >
                  {col.header}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-200 bg-white">
            {data.map((row, idx) => {
              const rawKey = getValue(row, keyField);
              const safeKey = rawKey != null && rawKey !== '' ? String(rawKey) : `row-${idx}`;
              return (
              <tr key={safeKey} className="hover:bg-slate-50">
                {columns.map((col) => (
                  <td
                    key={String(col.key)}
                    className={`px-4 py-3 text-slate-800 ${col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left'}`}
                  >
                    {col.render
                      ? col.render(row)
                      : String(getValue(row, col.key) ?? '')}
                  </td>
                ))}
              </tr>
            )})}
          </tbody>
        </table>
      </div>
    </div>
  );
}
