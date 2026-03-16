import React from "react";
import { Navigate, Outlet } from "react-router-dom";
import { useSelector } from 'react-redux';
import { PageRoute } from '../types/PageRoute';
import { AccountState } from '../redux/accountSlice';

export default function ProtectedRoute() {
	const account = useSelector((state: { account: AccountState }) => state.account);

	if (!account.loginStatus) {
		return <Navigate to={PageRoute.LOGIN} replace />;
	}

	return <Outlet />
};
