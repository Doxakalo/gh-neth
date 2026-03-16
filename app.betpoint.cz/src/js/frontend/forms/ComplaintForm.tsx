import React, { useState, useEffect } from 'react';
import { useSendComplaintFormMutation } from '../redux/apiSlice';
import { ApiError } from '../types/ErrorTypes';
import { handleInputChange } from '../../common/utils/FormUtils';
import InputErrorLabel from "../../common/components/InputErrorLabel";

interface Props {
	betId: number;
	onSubmitSuccess: () => void;
}

interface FormState {
    bet_id: number|null,
    message: string,
}

const initialInputValues:FormState = {
    bet_id: null,
    message: '',
};

export default function ComplaintForm({ betId, onSubmitSuccess }: Props) {
	const [sendComplaint, { data: sendComplaintResult, isLoading, error }] = useSendComplaintFormMutation();
	const [inputValues, setInputValues] = useState<FormState>({
		...initialInputValues,
		bet_id: betId,
	});

	const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
		event.preventDefault();
		sendComplaint(inputValues);
	}

	const getErrorMessage = (field: string): string | null => {
		return sendComplaintResult?.errors?.[field] || 
			(error as ApiError)?.data?.errors?.[field] || 
			null;
	};

	const hasErrorClass = (field: string): string => {
		return getErrorMessage(field) ? 'has-error' : '';
	};
	
	useEffect(() => {
		if(sendComplaintResult?.success === true) {
			onSubmitSuccess();
		}
	}, [sendComplaintResult]);

	return (
		<form onSubmit={handleSubmit} className={`bet-form ${isLoading ? 'disabled' : ''}`} noValidate>

			<label className={`form-input full ${hasErrorClass('message')}`}>
				<span>
					Your Message:
				</span>
				<textarea
					name="message"
					placeholder='Describe the issue...'
					maxLength={1024}
					value={inputValues.message || ''}
					onChange={(e) => handleInputChange(e, setInputValues)}
				/>
				<InputErrorLabel data={getErrorMessage('message')} />
			</label>
			
			<div className='button-container'>
				<button type="submit"
					disabled={isLoading}
					className={['button', (isLoading ? 'loading' : '')].join(' ')}
				>
					Submit
				</button>
			</div>

		</form>
	)
}
