import React from 'react';
import { usePagination, UsePaginationProps } from '../../hook-components/Pagination';

export interface PaginationProps extends UsePaginationProps {}

export function Pagination({ totalItems, pageSize, initialPage, siblingCount, onPageChange, className }: PaginationProps) {
    const { currentPage, totalPages, pages, goToPage, goToNext, goToPrev, classes } = usePagination({
        totalItems,
        pageSize,
        initialPage,
        siblingCount,
        onPageChange,
        className,
    });

    return (
        <nav className={classes} aria-label="Pagination">
            <button className="pagination__button" onClick={goToPrev} disabled={currentPage === 1}>
                Précédent
            </button>
            {pages.map((page, index) => {
                if (page === 'DOTS') {
                    return <span key={`dots-${index}`} className="pagination__dots">...</span>;
                }
                return (
                    <button
                        key={page}
                        className={`pagination__button ${page === currentPage ? 'pagination__button--active' : ''}`}
                        onClick={() => goToPage(page as number)}
                        aria-current={page === currentPage ? 'page' : undefined}
                    >
                        {page}
                    </button>
                );
            })}
            <button className="pagination__button" onClick={goToNext} disabled={currentPage === totalPages}>
                Suivant
            </button>
        </nav>
    );
}
