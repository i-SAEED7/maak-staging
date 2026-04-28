import { Bar, BarChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts";

type DashboardChartProps = {
  data: Array<{
    name: string;
    value: number;
  }>;
};

export function DashboardChart({ data }: DashboardChartProps) {
  return (
    <div className="tw-card min-h-[280px]">
      <ResponsiveContainer height={260} width="100%">
        <BarChart data={data} margin={{ bottom: 8, left: 8, right: 8, top: 8 }}>
          <CartesianGrid stroke="#efe4d3" strokeDasharray="3 3" vertical={false} />
          <XAxis dataKey="name" tick={{ fill: "#15445a", fontSize: 13 }} />
          <YAxis allowDecimals={false} tick={{ fill: "#607281", fontSize: 12 }} width={40} />
          <Tooltip
            contentStyle={{
              border: "1px solid rgba(123, 97, 58, 0.16)",
              borderRadius: 14,
              direction: "rtl"
            }}
          />
          <Bar dataKey="value" fill="#208CAA" radius={[8, 8, 0, 0]} />
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
}
