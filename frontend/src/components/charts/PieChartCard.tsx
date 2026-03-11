import { PieChart, Pie, Cell, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import { Card } from '@/components/ui/Card';
import type { ChartDataPoint } from '@/types';

const DEFAULT_COLORS = ['#0c8ee6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16', '#f97316'];

interface PieChartCardProps {
  title: string;
  data: ChartDataPoint[];
  dataKey?: string;
  nameKey?: string;
  colors?: string[];
  height?: number;
}

export function PieChartCard({
  title,
  data,
  dataKey = 'value',
  nameKey = 'name',
  colors = DEFAULT_COLORS,
  height = 280,
}: PieChartCardProps) {
  return (
    <Card>
      <h3 className="mb-4 text-sm font-semibold text-slate-800 font-display">{title}</h3>
      <ResponsiveContainer width="100%" height={height}>
        <PieChart>
          <Pie
            data={data}
            dataKey={dataKey}
            nameKey={nameKey}
            cx="50%"
            cy="50%"
            outerRadius="80%"
            label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
          >
            {data.map((_, index) => (
              <Cell key={index} fill={colors[index % colors.length]} />
            ))}
          </Pie>
          <Tooltip
            contentStyle={{ borderRadius: '8px', border: '1px solid #e2e8f0' }}
            formatter={(value: number) => [value, 'Count']}
          />
          <Legend />
        </PieChart>
      </ResponsiveContainer>
    </Card>
  );
}
