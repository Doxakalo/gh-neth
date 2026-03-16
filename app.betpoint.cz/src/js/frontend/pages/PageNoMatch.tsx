import * as React from "react";
import { Link } from "react-router-dom";
import { PageRoute } from '../types/PageRoute';
import usePageTitle from '../hooks/usePageTitle';

export default function PageNoMatch() {
	usePageTitle('Page Not Found');

	return (
		<div className="page">
			<div className="container">
				<div className="row">
					<div className="col-12">
						<h1>Page Not Found</h1>

						<p>
							Please check the URL or return to the {' '}
							<Link to={PageRoute.INDEX}>Home page</Link>.
						</p>
					</div>
				</div>
			</div>
		</div>
	);
}
