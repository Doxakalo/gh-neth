import React from 'react';

interface Props {
	error: Error;
	resetErrorBoundary: () => void;
}

export default function AppError({ error }: Props) {
	return (
		<div>
			<p>Something went wrong:</p>
			<pre style={{ color: "red" }}>{error.message}</pre>
		</div>
	);
}
