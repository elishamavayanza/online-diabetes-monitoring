import React from 'react';
import { useDataTable, UseDataTableProps } from '../../hook-components/DataTable';
import { Pagination } from '../Pagination';

export interface DataTableProps<T> extends UseDataTableProps<T> {}

export function DataTable<T>({
                                 columns,
                                 data,
                                 pageSize,
                                 initialSortKey,
                                 initialSortDirection,
                                 className,
                             }: DataTableProps<T>) {
    const {
        classes,
        paginatedData,
        totalPages,
        currentPage,
        setCurrentPage,
        sortKey,
        sortDirection,
        toggleSort,
    } = useDataTable<T>({
        columns,
        data,
        pageSize,
        initialSortKey,
        initialSortDirection,
        className,
    });

    return (
        <div className={classes}>
            <div className="datatable__wrapper">
                <table className="datatable__table">
                    <thead>
                    <tr>
                        {columns.map((col) => (
                            <th
                                key={String(col.key)}
                                className={col.sortable ? 'datatable__th--sortable' : ''}
                                onClick={col.sortable ? () => toggleSort(String(col.key)) : undefined}
                                aria-sort={
                                    col.sortable && sortKey === String(col.key)
                                        ? sortDirection === 'asc' ? 'ascending' : 'descending'
                                        : undefined
                                }
                            >
                  <span className="datatable__th-content">
                    {col.title}
                      {col.sortable && (
                          <span className="datatable__sort-icon">
                        {sortKey === String(col.key)
                            ? sortDirection === 'asc' ? ' ▲' : ' ▼'
                            : ' ⇅'}
                      </span>
                      )}
                  </span>
                            </th>
                        ))}
                    </tr>
                    </thead>
                    <tbody>
                    {paginatedData.length > 0 ? (
                        paginatedData.map((row, rowIndex) => (
                            <tr key={rowIndex}>
                                {columns.map((col) => (
                                    <td key={String(col.key)}>
                                        {col.render
                                            ? col.render(row, rowIndex + (currentPage - 1) * (pageSize || 10))
                                            : (row[col.key as keyof T] as React.ReactNode)}
                                    </td>
                                ))}
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td colSpan={columns.length} className="datatable__empty">
                                Aucune donnée disponible.
                            </td>
                        </tr>
                    )}
                    </tbody>
                </table>
            </div>

            {totalPages > 1 && (
                <div className="datatable__pagination">
                    <Pagination
                        totalItems={data.length}
                        pageSize={pageSize}
                        initialPage={currentPage}
                        onPageChange={setCurrentPage}
                    />
                </div>
            )}
        </div>
    );
}
