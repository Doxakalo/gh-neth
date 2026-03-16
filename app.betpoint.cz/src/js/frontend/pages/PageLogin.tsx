import React from "react";
import LoginForm from "../forms/LoginForm";
import usePageTitle from '../hooks/usePageTitle';

export default function PageLogin() {
	usePageTitle('Sign In');

	return (
		<div className="page login">
			<div className="container">
				<div className="row">
					<div className="col-12">
						<div className="intro last-child-no-margin">
							<img
								src={`${window.appConfig.baseUrl}images/betpoint-logo.svg`}
								alt={window.appConfig.appName}
							/>
							<h1>BetPoint is an application designed for teaching safe betting on sporting events.</h1>
							<p>
								Are you interested in learning more and gaining access to it?
								{' '}
								<a href="mailto:support@betpoint.cz">
									Contact us
								</a> 
							</p>
						</div>
						<div className="box rounded yellow login-form-container">
							<h2>Sign In to Your Account</h2>
							<LoginForm />
							<div className="forgot-password last-child-no-margin">
								<p className='small text-gray'>
									Forgot your password? <br />
									<a className='text-gray' href="mailto:support@betpoint.cz">
										Contact us
									</a> 
									{' '} for help.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	);
}
