import React, { useState } from "react";
import { Link } from 'react-router-dom';
import usePageTitle from '../hooks/usePageTitle';
import { PageRoute } from '../types/PageRoute';
import SignupForm from "../forms/SignupForm";

export default function PageSignup() {
	usePageTitle('Sign Up');
	const [isFormSuccess, setIsFormSuccess] = useState<boolean>(false);

	const onFormSuccess = () => {
		setIsFormSuccess(true);
	}

	return (
		<div className="page signup">
			<div className="container">
				<div className="row">
					<div className="col-12">
						<h1>Sign Up for BetPoint Account</h1>
			
						<div className="signup-form-container">
							<p className='mb-2'>
								To sign up, please enter the details below, confirm your age, and agree to our {' '}
								<Link to={PageRoute.PRIVACY} target='_blank'>Privacy Policy</Link>.
							</p>

							{!isFormSuccess ? (
								<>
								<SignupForm onSubmitSuccess={onFormSuccess} />
								</>
							) : (
								<div className="box rounded yellow last-child-no-margin signup-success">
									<h2>
										You have signed up successfully. 
									</h2>
									<p>
										Please Sign In to access your account.
									</p>
									<div className="button-container">
										<Link to={PageRoute.LOGIN} className="button">Sign In</Link>
									</div>
								</div>
							)}
						</div>
					</div>
				</div>
			</div>
		</div>
	);
}
