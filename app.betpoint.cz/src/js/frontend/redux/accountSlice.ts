import { createSlice } from '@reduxjs/toolkit';
import { Profile, Wallet } from '../types/ProfileTypes';

export interface AccountState {
	loginStatus: boolean;
	profile: Profile | null;
	wallet: Wallet | null;
}

const initialState: AccountState = {
	loginStatus: false,
	profile: null,
	wallet: null,
}

export const accountSlice = createSlice({
	name: 'account',
	initialState: window?.appConfig?.state?.account ?? initialState,
	reducers: {
		setAccountData: (state, action) => {
			state.loginStatus = action.payload.loginStatus ?? false;
			state.profile = action.payload.profile ?? null;
			state.wallet = action.payload.wallet ?? null;
		},
	},
});

export const { setAccountData } = accountSlice.actions;
export default accountSlice.reducer;
