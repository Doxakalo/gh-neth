import React, { useState, useEffect } from 'react';
import { useSendContactFormMutation } from '../redux/apiSlice';
import { ApiError } from '../types/ErrorTypes';
import { handleInputChange } from '../../common/utils/FormUtils';
import InputErrorLabel from "../../common/components/InputErrorLabel";

interface Props {
	onSubmitSuccess: () => void;
}

interface FormState {
    topic: string,
    message: string,
}

const initialInputValues:FormState = {
    topic: '',
    message: '',
};

export default function ContactForm({ onSubmitSuccess }: Props) {
	const [sendContact, { data: sendContactResult, isLoading, error }] = useSendContactFormMutation();
	const [inputValues, setInputValues] = useState<FormState>(initialInputValues);

	const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
		event.preventDefault();
		sendContact(inputValues);
	}

	const getErrorMessage = (field: string): string | null => {
		return sendContactResult?.errors?.[field] || 
			(error as ApiError)?.data?.errors?.[field] || 
			null;
	};

	const hasErrorClass = (field: string): string => {
		return getErrorMessage(field) ? 'has-error' : '';
	};
	
	useEffect(() => {
		if(sendContactResult?.success === true) {
			onSubmitSuccess();
		}
    }, [sendContactResult]);

	return (
		<form onSubmit={handleSubmit} className={`contact-form ${isLoading ? 'disabled' : ''}`} noValidate>
			<label className={`form-input full ${hasErrorClass('topic')}`}>
				<span>
					Topic
				</span>
				<select
					name="topic"
					value={inputValues.topic || ''}
					onChange={(e) => handleInputChange(e, setInputValues)}
				>
					<option value="">Select a topic</option>
					<option value="General Inquiry">General Inquiry</option>
					<option value="Technical Support">Technical Support</option>
					<option value="Feedback">Feedback</option>
					<option value="Other">Other</option>
				</select>
				<InputErrorLabel data={getErrorMessage('topic')} />
			</label>

			<label className={`form-input full ${hasErrorClass('message')}`}>
				<span>
					Message
				</span>
				<textarea
					name="message"
					placeholder='Type your message here...'
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
					Send Message
				</button>
			</div>

		</form>
	)
}
