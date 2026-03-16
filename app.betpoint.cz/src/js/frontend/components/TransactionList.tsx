import React, {useState} from "react";
import { useGetTransactionsQuery } from '../redux/apiSlice';
import { getTransactionTypeClassName, Transaction } from '../types/TransactionTypes';
import { formatCurrency, getTimestampFormatted } from '../../common/utils/FormatUtils';
import LoadingStatus from './LoadingStatus';
import Pagination from './Pagination';

type Props = {
	onPageChange?: () => void;
};

export default function TransactionList({ onPageChange }: Props) {
	const [currentPage, setCurrentPage] = useState(1);
	const { data, error, isLoading, isFetching } = useGetTransactionsQuery({
		page: currentPage,
	});
	const totalPages = data?.pagination ? data.pagination.page_count : 1;

	const handlePageChange = (page:number) => {
		setCurrentPage(page);
		if (onPageChange) {
			onPageChange();
		}
	};

	return (
		<div className="transaction-list-container">
			{error ? (
				<LoadingStatus status='error' />
			) : isLoading || isFetching ? (
				<LoadingStatus />
			) : (
				<>
					<table className="transaction-list">
						<thead>
							<tr>
								<th className='action'>Action</th>
								<th className='detail'>Detail</th>
								<th className='date'>Date</th>
								<th className='amount tar'>Betcoins</th>
							</tr>
						</thead>
						<tbody>
							{data.transactions.map((transaction: Transaction) => (
								<tr key={transaction.id}>
									<td>
										<span className="cell-header">Action:</span> 
										<span className={`transaction-tag-label ${getTransactionTypeClassName(transaction.type)}`}>
											{transaction.action}
										</span>
									</td>
									<td>
										<span className="cell-header">Detail:</span>
										<span>
											{transaction.description}
											{transaction.user_bet_id_hash && (
												<>
													{' '}
													<span className="secondary-info">
														(Bet #{transaction.user_bet_id_hash})
													</span>
												</>
											)}
										</span>
									</td>
									<td>
										<span className="cell-header">Date:</span>
										{getTimestampFormatted(transaction.created_at)}
									</td>
									<td className='tar'>
										<span className="cell-header">Betcoins:</span>
										<span className={`amount-value ${transaction.amount >= 0 ? 'positive' : 'negative'}`}>
											{transaction.amount > 0 ? '+' : ''}{formatCurrency(transaction.amount, false, true)}
										</span>
									</td>
								</tr>
							))}
						</tbody>
					</table>

					{data.transactions.length === 0 && (
						<div className='inline-message'>
							You have no transactions yet.
						</div>
					)}

					{data.transactions.length > 0 && (
						<Pagination
							currentPage={currentPage}
							totalPages={totalPages}
							onPageChange={handlePageChange}
						/>
					)}
				</>
			)}
		</div>
	);
}
