import { createSlice } from '@reduxjs/toolkit';
import { Sport, SportCategory } from '../types/SportTypes';

export interface SportTreeState {
	sport: Sport | null;
	sportCategory: SportCategory | null;
}

const initialState: SportTreeState = {
	sport: null,
	sportCategory: null,
}

export const sportTreeSlice = createSlice({
	name: 'sportTree',
	initialState,
	reducers: {
		setSportAndCategory: (state, action) => {
			state.sport = action.payload.sport;
			state.sportCategory = action.payload.category;
		},
		resetSportAndCategory: (state) => {
			state.sport = null;
			state.sportCategory = null;
		},
	},
})

export const {
	setSportAndCategory,
	resetSportAndCategory,
} = sportTreeSlice.actions;
export default sportTreeSlice.reducer;
