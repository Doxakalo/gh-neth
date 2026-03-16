import { createSlice } from '@reduxjs/toolkit';
import { Bet } from '../types/BetTypes';

export interface BetModalPayload {
	oddId: number;
}

export interface ComplaintModalPayload {
	bet: Bet;
}

export interface ModalState {
	betVisible: boolean;
	complaintVisible: boolean;
	betPayload: BetModalPayload | null;
	complaintPayload: ComplaintModalPayload | null;
}

const initialState: ModalState = {
	betVisible: false,
	complaintVisible: false,
	betPayload: null,
	complaintPayload: null,
}

export const modalSlice = createSlice({
	name: 'modal',
	initialState,
	reducers: {
		showBetModal: (state, action) => {
			state.betVisible = true;
			state.betPayload = action.payload;
		},
		hideBetModal: (state) => {
			state.betVisible = false;
			state.betPayload = null;
		},
		showComplaintModal: (state, action) => {
			state.complaintVisible = true;
			state.complaintPayload = action.payload;
		},
		hideComplaintModal: (state) => {
			state.complaintVisible = false;
			state.complaintPayload = null;
		},
	},
})

export const {
	showBetModal,
	hideBetModal,
	showComplaintModal,
	hideComplaintModal,
} = modalSlice.actions;
export default modalSlice.reducer;
