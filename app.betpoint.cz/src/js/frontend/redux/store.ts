import { configureStore, Middleware } from '@reduxjs/toolkit';
import { setupListeners } from '@reduxjs/toolkit/query';
import { apiSlice } from './apiSlice';
import accountReducer from './accountSlice';
import modalReducer from './modalSlice';
import sportTreeReducer from './sportTreeSlice';


const store = configureStore({
	reducer: {
		account: accountReducer,
		modal: modalReducer,
		sportTree: sportTreeReducer,
		[apiSlice.reducerPath]: apiSlice.reducer,
	},

	// Adding the api middleware enables caching, invalidation, polling,
	// and other useful features of `rtk-query`.
	middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(apiSlice.middleware),
})

// optional, but required for refetchOnFocus/refetchOnReconnect behaviors
// see `setupListeners` docs - takes an optional callback as the 2nd arg for customization
setupListeners(store.dispatch);

export default store;

// Infer the `RootState` and `AppDispatch` types from the store itself
export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
