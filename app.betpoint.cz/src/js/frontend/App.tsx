import React, { useEffect } from "react";
import { Routes, Route, useNavigate, useLocation } from "react-router-dom";
import { useSelector, useDispatch } from 'react-redux'
import usePrevious from '../common/hooks/usePrevious';
import { useGetAccountQuery, useGetAppStatusQuery, useLogoutMutation } from './redux/apiSlice';
import { setAccountData } from './redux/accountSlice'
import { AccountState } from './redux/accountSlice';
import { resetSportAndCategory } from './redux/sportTreeSlice';
import Header from "./components/Header";
import Footer from "./components/Footer";
import BetModal from './modals/BetModal';
import ComplaintModal from './modals/ComplaintModal';
import { PageRoute } from './types/PageRoute';
import ProtectedRoute from './pages/ProtectedRoute';
import GuestOnlyRoute from './pages/GuestOnlyRoute';
import PageNoMatch from "./pages/PageNoMatch";
import PageIndex from "./pages/PageIndex";
import PageAccount from './pages/PageAccount';
import PageLogin from './pages/PageLogin';
import PageSignup from './pages/PageSignup';
import PageContact from './pages/PageContact'; 
import PageGuide from "./pages/PageGuide";
import PagePrivacy from './pages/PagePrivacy';
import PageGameTips from "./pages/PageGameTips";
import PageTerms from './pages/PageTerms';
import { Constants } from './types/Constants';

export default function App() {
	const dispatch = useDispatch();
	const navigate = useNavigate();
	const { pathname } = useLocation();
	const account = useSelector((state: { account: AccountState }) => state.account);
	const { data: accountLoadedData } = useGetAccountQuery({}, {
		pollingInterval: account.loginStatus ? Constants.ACCOUNT_REFRESH_INTERVAL : undefined,
		refetchOnFocus: account.loginStatus ? true : false,
		skipPollingIfUnfocused: true,
	});
	const { data: appStatusData } = useGetAppStatusQuery({}, {
		pollingInterval: Constants.APP_STATUS_REFRESH_INTERVAL,
		refetchOnFocus: true,
		skipPollingIfUnfocused: true,
	});
	const [logout] = useLogoutMutation();
	const prevAppStatus = usePrevious(appStatusData?.status);
	const prevLoginStatus = usePrevious(account.loginStatus);
	const prevPathname = usePrevious(pathname);
	const signupEnabled = window?.appConfig?.flashData?.signupEnabled ?? false;
	/**
	 * Sync default data
	 */
	useEffect(() => {
		if(accountLoadedData) {
			dispatch(
				setAccountData({
					loginStatus: accountLoadedData.loginStatus,
					profile: accountLoadedData.profile,
					wallet: accountLoadedData.wallet,
				} as AccountState));
		}
	}, [accountLoadedData]);

	
	/**
	 * Handle login state change
	 */
	useEffect(() => {
		if (account.loginStatus !== prevLoginStatus && prevLoginStatus !== undefined) {
			if(account.loginStatus === true) {
				// navigate to default path
				navigate(PageRoute.INDEX);
			} else {
				// user logged out
				// reset selected sport and category
				dispatch(resetSportAndCategory());
				// navigate to login page
				navigate(PageRoute.LOGIN);
			}
		}
	}, [account]);


	/**
	 * Handle app status change
	 */
	useEffect(() => {
		if (appStatusData && appStatusData.status !== prevAppStatus) {
			if(appStatusData.status !== true) {
				// App is down, redirect to login page
				if (account.loginStatus) {
					logout({});
				}
			}
		}
	}, [appStatusData, account.loginStatus]);


	/**
	 * Scroll to top on pathname change
	 */
	useEffect(() => {
		if (pathname !== prevPathname  && prevPathname !== undefined) {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		}
	}, [pathname]);


	return (
		<>
			{ appStatusData && !appStatusData.status && (
				<div className="app-status-bar">
					<p>{appStatusData.message}</p>
				</div>
			)}

			{ pathname !== PageRoute.LOGIN &&
				<Header />
			}

			<main>
				<Routes>
					<Route element={<GuestOnlyRoute />}>
					 	<Route path={PageRoute.LOGIN} element={<PageLogin />} />
						{ signupEnabled ? (
							<Route path={PageRoute.SIGNUP} element={<PageSignup />} />
						) : null}
					</Route>

					<Route element={<ProtectedRoute />}>
						<Route path={PageRoute.INDEX} element={<PageIndex />} />
						<Route path={PageRoute.ACCOUNT} element={<PageAccount />} />
						<Route path={PageRoute.CONTACT} element={<PageContact />} />
					</Route>

					<Route path={PageRoute.GAME_PLAN} element={<PageGameTips />} />
					<Route path={PageRoute.PRIVACY} element={<PagePrivacy />} />
					<Route path={PageRoute.TERMS} element={<PageTerms />} />
					<Route path={PageRoute.NO_MATCH} element={<PageNoMatch />} />
					<Route path={PageRoute.GUIDE} element={<PageGuide />} />
					<Route path="*" element={<PageNoMatch />} />
				</Routes>
			</main>
			
			<Footer loginStatus={account.loginStatus} />

			<BetModal />
			<ComplaintModal />

		</>
	);
}
