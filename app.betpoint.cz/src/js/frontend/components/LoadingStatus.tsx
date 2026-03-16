import React from "react";

interface Props {
	status?: 'loading' | 'error';
}

export default function LoadingStatus({ status = 'loading' }: Props) {
	return (
		<div className={`loading-status ${status}`}>
			{status === 'loading' ? (
				<p>Loading...</p>
			) : (
				<p>Failed to load content.</p>
			)}
		</div>
	);
}
