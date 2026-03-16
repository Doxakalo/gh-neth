import React, {useState} from "react";
import { useGetBetsQuery } from '../redux/apiSlice';
import { Bet } from '../types/BetTypes';
import LoadingStatus from './LoadingStatus';
import BetItem from './BetItem';
import Pagination from './Pagination';

type Props = {
	onPageChange?: () => void;
};

export default function BetList({ onPageChange }: Props) {
	const [currentPage, setCurrentPage] = useState(1);
	const { data, error, isLoading, isFetching } = useGetBetsQuery({
		page: currentPage,
	});
	const totalPages = data?.pagination ? data.pagination.page_count : 1;

	const handlePageChange = (page:number) => {
		setCurrentPage(page);
		if (onPageChange) {
			onPageChange();
		}
	};
	//console.log(data);
	return (
		<div className="bet-list-container">
			{error ? (
				<LoadingStatus status='error' />
			) : isLoading || isFetching ? (
				<LoadingStatus />
			) : (
				<>
					<div className='bet-list'>
						{data.bets.map((bet: Bet) => (
							<BetItem bet={bet} key={bet.id} />
						))}
					</div>

					{data.bets.length === 0 && (
						<div className='inline-message'>
							You have no bets yet.
						</div>
					)}

					{data.bets.length > 0 && (
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
