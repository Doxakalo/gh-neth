import React from "react";
import { useGetSportsQuery } from '../redux/apiSlice';
import { Sport } from '../types/SportTypes';
import LoadingStatus from './LoadingStatus';
import SportTreeItem from './SportTreeItem';

export default function SportTree() {
	const { data, error, isFetching } = useGetSportsQuery({});

	return (
		<div className="sport-tree">
			{error ? (
				<LoadingStatus status='error' />
			) : isFetching ? (
				<LoadingStatus />
			) :
				<>
					{data.sports.map((sport: Sport) => (
						<SportTreeItem sport={sport} key={sport.id} />
					))}
				</>
			}
		</div>
	);
}
