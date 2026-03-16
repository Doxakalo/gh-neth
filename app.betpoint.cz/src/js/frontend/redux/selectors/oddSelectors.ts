import { createSelector } from '@reduxjs/toolkit';
import { RootState } from '../store';
import { apiSlice } from '../apiSlice';
import { SportOdd } from '../../types/SportTypes';

// Selector to get active category ID from sportTree state
const selectActiveCategoryId = (state: RootState) =>
	state.sportTree.sportCategory?.id ?? 0;

// Selector to get cached matches for active category
const selectCachedMatches = createSelector(
	[selectActiveCategoryId],
	(categoryId) => apiSlice.endpoints.getSportMatches.select({ categoryId })
);

// Selector factory to get SportOdd by ID
export const selectOddById = (oddId: number): (state: RootState) => SportOdd | null => {
	return createSelector(
		[selectCachedMatches, (state: RootState) => state],
		(matchesSelector, state) => {
			const matchesResult = matchesSelector(state);
			const matches = matchesResult.data?.matches || [];

			for (const match of matches) {
				for (const group of match.odd_groups || []) {
					const foundOdd: SportOdd | undefined = group.odds?.find((odd: SportOdd) => {
						return odd.id === oddId;
					});
					if (foundOdd) {
						return foundOdd;
					}
				}
			}

			return null;
		}
	);
};
