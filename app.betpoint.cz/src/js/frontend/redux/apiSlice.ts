import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'

export const TAG_ACCOUNT = 'account';
export const TAG_SPORTS = 'sports';
export const TAG_MATCHES = 'matches';
export const TAG_TRANSACTIONS = 'transactions';
export const TAG_BETS = 'bets';
export const TAG_TOTAL_BETS = 'totalbets'

export const apiSlice = createApi({
	reducerPath: 'api',
	baseQuery: fetchBaseQuery({ baseUrl: window.appConfig.apiBaseUrl }),
	tagTypes: [
		TAG_ACCOUNT, 
		TAG_SPORTS,
		TAG_MATCHES, 
		TAG_TRANSACTIONS,
		TAG_BETS,
		TAG_TOTAL_BETS,
	],
	endpoints: build => ({

		getAppStatus: build.query({
			query: () => ({
				url: 'app-status',
				method: 'GET',
			}),
		}),

		login: build.mutation({
			query: (body) => ({
				url: 'login',
				method: 'POST',
				body: new URLSearchParams(body).toString(),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
			}),
			invalidatesTags: [
				TAG_ACCOUNT, 
				TAG_SPORTS,
				TAG_MATCHES, 
				TAG_TRANSACTIONS,
				TAG_BETS,
				TAG_TOTAL_BETS,
			],
		}),

		logout: build.mutation({
			query: () => ({
				url: 'logout',
				method: 'POST',
			}),
			invalidatesTags: [TAG_ACCOUNT],
		}),

		signup: build.mutation({
			query: (body) => ({
				url: 'signup',
				method: 'POST',
				body: new URLSearchParams(body).toString(),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
			}),
			invalidatesTags: [TAG_ACCOUNT],
		}),

		getAccount: build.query({
			query: () => ({
				url: 'account',
				method: 'GET',
			}),
			providesTags: [TAG_ACCOUNT],
		}),

		getSports: build.query({
			query: () => ({
				url: 'sports',
				method: 'GET',
			}),
			providesTags: [TAG_SPORTS],
		}),

		getSportMatches: build.query({
			query: (queryParams) => {
				return `sport-matches?${new URLSearchParams(queryParams).toString()}`;
			},
			providesTags: [TAG_MATCHES],
		}),

		getTransactions: build.query({
			query: (queryParams) => {
				return `transactions?${new URLSearchParams(queryParams).toString()}`;
			},
			providesTags: [TAG_TRANSACTIONS],
		}),

		getBets: build.query({
			query: (queryParams) => {
				return `bets?${new URLSearchParams(queryParams).toString()}`;
			},
			providesTags: [TAG_BETS],
		}),

		getTotalBet: build.query<number, void>({
			query: () => 'totalbet', 
			providesTags: [TAG_TOTAL_BETS],
		}),
		
		createBet: build.mutation({
			query: (body) => ({
				url: 'bet',
				method: 'POST',
				body: new URLSearchParams(body).toString(),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
			}),
			invalidatesTags: [TAG_ACCOUNT, TAG_BETS, TAG_TRANSACTIONS, TAG_TOTAL_BETS],
		}),

		sendContactForm: build.mutation({
			query: (body) => ({
				url: 'contact-form',
				method: 'POST',
				body: new URLSearchParams(body).toString(),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
			}),
		}),

		sendComplaintForm: build.mutation({
			query: (body) => ({
				url: 'complaint-form',
				method: 'POST',
				body: new URLSearchParams(body).toString(),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
			}),
		}),

	})
})

export const { 
	useLoginMutation,
	useLogoutMutation,
	useSignupMutation,
	useGetAppStatusQuery,
	useGetAccountQuery,
	useGetSportsQuery,
	useGetSportMatchesQuery,
	useGetBetsQuery,
	useGetTransactionsQuery,
	useCreateBetMutation,
	useSendContactFormMutation,
	useSendComplaintFormMutation,
	useGetTotalBetQuery,
 } = apiSlice;
