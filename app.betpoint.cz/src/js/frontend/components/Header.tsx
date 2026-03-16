import React from "react";
import { Link, NavLink } from 'react-router-dom';
import { useSelector } from 'react-redux';
import { AccountState } from '../redux/accountSlice';
import { useLogoutMutation } from '../redux/apiSlice';
import { PageRoute } from '../types/PageRoute';
import { formatCurrency } from '../../common/utils/FormatUtils';

export default function Header() {
	const account = useSelector((state: { account: AccountState }) => state.account);
	const [logout] = useLogoutMutation();

	const handleLogout = () => {
		logout({});
	}

	return (
		<>
			<header id="main-header">
				<div className="container">
					<div className="row">
						<div className="col-12">
							<div className="header-inner">
								<Link to={PageRoute.INDEX} className="brand">
									<img
										src={`${window.appConfig.baseUrl}images/betpoint-logo.svg`}
										alt={window.appConfig.appName}
									/>
								</Link>

								{ account.loginStatus && ( 
									<nav>
										<NavLink to={PageRoute.INDEX} className='nav-link home' title='Home'>
											<i className="icon sbc-icon-home"></i>
										</NavLink>

										{(account.profile && account.wallet) && (
											<NavLink to={PageRoute.ACCOUNT} className='nav-link account-shortcut' title='Account'>
												<span className='account'>
													<span className="label">My Account</span>
													<span className="name">
														{account.profile.nickname}
													</span>
												</span>
												<span className='balance'>
													<i className="icon sbc-icon-money"></i>
													<span className="value">
														{formatCurrency(account.wallet.balance)}
													</span>
												</span>
											</NavLink>
										)}

										<button className='nav-link text-only icon-right sign-out' onClick={() => handleLogout()}>
											<span>Sign Out</span>
											<i className="icon sbc-icon-logout"></i>
										</button>
									</nav>
								)}
							</div>
						</div>
					</div>
				</div>
			</header>
			<div id="header-wrapper"></div>
		</>
	);
}
