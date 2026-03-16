import React, { useEffect, useState } from 'react';
import { useLoginMutation } from '../redux/apiSlice';
import { ApiError } from '../types/ErrorTypes';
import { handleInputChange } from '../../common/utils/FormUtils';
import InputErrorLabel from "../../common/components/InputErrorLabel";


interface FormState {
	email: string;
	password: string;
}

export default function LoginForm() {
	const [login, { data: loginResult, isLoading, error }] = useLoginMutation();
	const [inputValues, setInputValues] = useState<FormState>({
		email: '',
		password: '',
	});

	const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
		event.preventDefault();
		login(inputValues);
	}

	const getErrorMessage = (field: string): string | null => {
		return loginResult?.errors?.[field] || (error as ApiError)?.data?.errors?.[field] || null;
	};

	const hasErrorClass = (field: string): string => {
		return getErrorMessage(field) ? 'has-error' : '';
	};

	return (
		<form onSubmit={handleSubmit} className={`login-form ${isLoading ? 'disabled' : ''}`} noValidate>
			<label className={`form-input full ${hasErrorClass('email')}`}>
				<input
					type="email"
					name="email"
					id="login-email"
					placeholder="Email"
					maxLength={255}
					value={inputValues.email || ''}
					onChange={(e) => handleInputChange(e, setInputValues)}
				/>
				<InputErrorLabel data={getErrorMessage('email')} />
			</label>
			
			<label className={`form-input full ${hasErrorClass('password')}`}>
				<input
					type="password"
					name="password"
					id="login-password"
					placeholder='Password'
					maxLength={255}
					value={inputValues.password || ''}
					onChange={(e) => handleInputChange(e, setInputValues)}
				/>
				<InputErrorLabel data={getErrorMessage('password')} />
			</label>

			{/*{loginResult?.appStatus === false && (
				<div className="app-status-error">
					{loginResult?.message || 'The application is currently experiencing issues. Please try again later.'}
				</div>
			)}*/}

			<div className="button-container">
				<button type="submit"
					disabled={isLoading}
					className={['button', (isLoading ? 'loading' : '')].join(' ')}
					>
					Sign In
				</button>
			</div>
		</form>
	)
}
