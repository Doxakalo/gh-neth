import React from "react";
import usePageTitle from '../hooks/usePageTitle';
import PrivacyPolicyContent from '../content/PrivacyPolicyContent';

export default function PagePrivacy() {
	usePageTitle('Privacy Policy');

	return (
		<div className="page">
			<div className="container">
				<div className="row">
					<div className="col-12">
						<PrivacyPolicyContent />
					</div>
				</div>
			</div>
		</div>
	);
}
