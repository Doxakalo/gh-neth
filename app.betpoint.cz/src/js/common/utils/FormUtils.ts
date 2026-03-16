import React from 'react';

export const handleInputChange = <T extends Record<string, any>>(
	event: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>,
	setInputValues: React.Dispatch<React.SetStateAction<T>>
) => {
	const { name, type } = event.target;
	let value: string | number | boolean | File | FileList | null = event.target.value;

	if (event.target instanceof HTMLInputElement) {
		if (type === "checkbox") {
			// checkbox
			value = event.target.checked ? 1 : 0;
		} else if (type === "file" && event.target.files?.length) {
			// file input
			value = event.target.files[0]; // get the first file
		}
	}

	setInputValues(values => ({ ...values, [name]: value }));
};
