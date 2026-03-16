import React from "react";
import usePageTitle from '../hooks/usePageTitle';
import TermsConditionsContent from '../content/TermsConditionsContent';

export default function PageTerms() {
	usePageTitle('Terms and Conditions');

	return (
		<div className="page">
			<div className="container">
				<div className="row">
					<div className="col-12">
						<TermsConditionsContent />
					</div>
				</div>
			</div>
		</div>
	);
}
