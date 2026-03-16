import React from "react";
import { NavLink } from 'react-router-dom';
import { PageRoute } from '../types/PageRoute';

interface Props {
	loginStatus: boolean;
}

export default function Footer({ loginStatus }: Props) {
	return (
		<footer className={`main-footer bg-dark ${loginStatus ? 'logged-in' : ''}`}>
			<div className="container">
				<div className="row">
					<div className="col-12">
						<div className="footer-inner">
							<p className="copyright">
								&copy; {new Date().getFullYear()} {window.appConfig.appName}
							</p>
							<nav>
								<NavLink to={PageRoute.GAME_PLAN}>
									Bet types & Evaluation
								</NavLink>
								<NavLink to={PageRoute.PRIVACY}>
									Privacy Policy
								</NavLink>
								<NavLink to={PageRoute.TERMS}>
									Terms &amp; Conditions
								</NavLink>
								{loginStatus && (
									<NavLink to={PageRoute.CONTACT} className="icon-right">
										Support / Contact Us
										<i className="icon sbc-icon-mail"></i>
									</NavLink>
								)}
							</nav>
						</div>
					</div>
				</div>
			</div>
		</footer>
	);
}
