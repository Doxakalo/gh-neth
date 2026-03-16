import React, { useState } from "react";
import { Link } from 'react-router-dom';
import usePageTitle from '../hooks/usePageTitle';
import ContactForm from '../forms/ContactForm';

export default function PageContact() {
	usePageTitle('Contact Us');
	const [isFormSuccess, setIsFormSuccess] = useState<boolean>(false);

	const onFormSuccess = () => {
		setIsFormSuccess(true);
	}

	return (
		<div className="page contact">
			<div className="container">
				<div className="row">
					<div className="col-6 col-8-tablet col-12-mobile">
						<h1>Need Help? Contact Us!</h1>

						<p className='mb-2'>
							If you have any questions or encounter any issues, please fill out the form below.
							Our support team will review your inquiry and respond to you via email within 4 business days. 
							Thank you for your patience!
						</p>

						{!isFormSuccess ? (
							<div className="contact-form-container">
								<ContactForm onSubmitSuccess={onFormSuccess} />
							</div>
						) : (
							<div className="box rounded yellow last-child-no-margin">
								<p>
									<i className="icon large sbc-icon-emoji-smile"></i>
								</p>
								<p>
									<strong>
										Your message has been sent. Thanks for contacting us! 
									</strong>
								</p>
								<p>
									<Link to={''} onClick={() => setIsFormSuccess(false)}>Send another message</Link>
								</p>
							</div>
						)}
					</div>
				</div>
			</div>
		</div>
	);
}
