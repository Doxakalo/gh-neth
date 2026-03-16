import React from 'react';

interface Props {
	data: string | string[] | null;
}

export default function InputErrorLabel({ data }: Props) {

	const isVisible = () => {
		return String(getText()).length > 0 ? true : false;
	}

	const getText = () => {
		if (Array.isArray(data) && data.length > 0) {
			return data[0];
		} else if (typeof data === 'string') {
			return data;
		}
		return '';
	}

	return isVisible() ? (
		<div className="input-error-label">
			{getText()}
		</div>
	) : null;
}
