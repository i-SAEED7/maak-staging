import type { ReactNode } from "react";

export type DataColumn<T> = {
  key: string;
  label: string;
  render: (row: T) => ReactNode;
};

type DataTableProps<T> = {
  columns: DataColumn<T>[];
  rows: T[];
  emptyMessage?: string;
};

export function DataTable<T>({ columns, rows, emptyMessage = "لا توجد بيانات." }: DataTableProps<T>) {
  return (
    <div className="table-card">
      <table className="data-table">
        <thead>
          <tr>
            {columns.map((column) => (
              <th key={column.key}>{column.label}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rows.length ? (
            rows.map((row, rowIndex) => (
              <tr key={rowIndex}>
                {columns.map((column) => (
                  <td key={`${column.key}-${rowIndex}`}>{column.render(row)}</td>
                ))}
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan={columns.length} className="table-empty">
                {emptyMessage}
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
