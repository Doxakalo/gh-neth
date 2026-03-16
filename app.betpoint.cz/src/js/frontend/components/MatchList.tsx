import React from "react";
import { useGetSportMatchesQuery } from '../redux/apiSlice';
import { SportMatch } from '../types/SportTypes';
import MatchItem from './MatchItem';
import LoadingStatus from './LoadingStatus';
import { Constants } from '../types/Constants';

interface Props {
	categoryId: number;
};

export default function MatchList({ categoryId }: Props) {
	const { data, error, isLoading } = useGetSportMatchesQuery({ categoryId }, {
		pollingInterval: Constants.MATCH_REFRESH_INTERVAL,
		refetchOnFocus: true,
		skipPollingIfUnfocused: true,
	});

	return (
		<div className="match-list">
			{error ? (
				<LoadingStatus status='error' />
			) : isLoading ? (
				<LoadingStatus />
			) :
				<>
					{data.matches.map((sportMatch: SportMatch) => (
						<MatchItem sportMatch={sportMatch} key={sportMatch.id} />
					))}
				</>
			}
		</div>
	);
}
