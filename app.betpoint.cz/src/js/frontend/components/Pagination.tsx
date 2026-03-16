import React from 'react';

interface Props {
	currentPage: number;
	totalPages: number;
	onPageChange: (page: number) => void;
}

export default function Pagination({ currentPage, totalPages, onPageChange }: Props) {
	const handleFirstPage = () => onPageChange(1);
	const handlePreviousPage = () => onPageChange(Math.max(currentPage - 1, 1));
	const handleNextPage = () => onPageChange(Math.min(currentPage + 1, totalPages));
	const handleLastPage = () => onPageChange(totalPages);
	const handlePageNumberClick = (page: number) => onPageChange(page);

	const pageNumbers = [];
	for (let i = 1; i <= totalPages; i++) {
		pageNumbers.push(i);
	}

	return (
		<div className="pagination">
			<button className="pagination-button pagination-button-first" onClick={handleFirstPage} disabled={currentPage === 1} title="First page">
				<i className="sbc-icon-caret-double-left"></i>
			</button>
			<button className="pagination-button pagination-button-previous" onClick={handlePreviousPage} disabled={currentPage === 1} title="Previous page">
				<i className="sbc-icon-caret-left"></i>
			</button>
			{pageNumbers.map((page) => (
				<button
					className={[
						'pagination-button pagination-button-number',
						(page === currentPage ? 'active' : '')
					].join(' ')}
					key={page}
					onClick={() => handlePageNumberClick(page)}
					disabled={page === currentPage}
					title={`Page ${page}`}
				>
					{page}
				</button>
			))}
			<button className="pagination-button pagination-button-next" onClick={handleNextPage} disabled={currentPage === totalPages} title="Next page">
				<i className="sbc-icon-caret-right"></i>
			</button>
			<button className="pagination-button pagination-button-last" onClick={handleLastPage} disabled={currentPage === totalPages} title="Last page">
				<i className="sbc-icon-caret-double-right"></i>
			</button>
		</div>
	);
}
