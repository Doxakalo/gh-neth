import React from "react";
import { Navigate, Outlet } from "react-router-dom";
import { useSelector } from 'react-redux';
import { AccountState } from '../redux/accountSlice';
import { PageRoute } from '../types/PageRoute';

export default function GuestOnlyRoute() {
	const account = useSelector((state: { account: AccountState }) => state.account);

	if (account.loginStatus) {
		return <Navigate to={PageRoute.INDEX} replace />;
	}

	return <Outlet />
};
