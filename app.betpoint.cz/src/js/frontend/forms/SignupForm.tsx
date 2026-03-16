import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useSignupMutation } from '../redux/apiSlice';
import { ApiError } from '../types/ErrorTypes';
import { handleInputChange } from '../../common/utils/FormUtils';
import InputErrorLabel from "../../common/components/InputErrorLabel";
import { PageRoute } from '../types/PageRoute';

interface Props {
	onSubmitSuccess: () => void;
}

interface FormState {
    first_name: string,
    last_name: string,
    nickname: string,
    email: string,
    password: string,
    password_verify: string,
	agree_terms: number,
	confirm_age: number,
}

const initialInputValues:FormState = {
    first_name: '',
    last_name: '',
	nickname: '',
    email: '',
    password: '',
    password_verify: '',
	agree_terms: 0,
	confirm_age: 0,
};

export default function SignupForm({ onSubmitSuccess }: Props) {
	const [signup, { data: signupResult, isLoading, error }] = useSignupMutation();
	const [inputValues, setInputValues] = useState<FormState>(initialInputValues);

	const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
		event.preventDefault();
		signup(inputValues);
	}

	const getErrorMessage = (field: string): string | null => {
		return signupResult?.errors?.[field] || 
			(error as ApiError)?.data?.errors?.[field] || 
			null;
	};

	const hasErrorClass = (field: string): string => {
		return getErrorMessage(field) ? 'has-error' : '';
	};
	
	useEffect(() => {
		if(signupResult?.success === true) {
			onSubmitSuccess();
		}
    }, [signupResult]);

	return (
		<form onSubmit={handleSubmit} className={`signup-form ${isLoading ? 'disabled' : ''}`} noValidate>
			<div className="row">
				<div className="col-6 col-12-mobile">
					<label className={`form-input full ${hasErrorClass('nickname')}`}>
						<span>
							Nickname <i className="required">*</i>
						</span>
						<input
							type="text"
							name="nickname"
							maxLength={128}
							value={inputValues.nickname || ''}
							onChange={(e) => handleInputChange(e, setInputValues)}
						/>
						<InputErrorLabel data={getErrorMessage('nickname')} />
					</label>
				</div>
			</div>
			<div className="row">
				<div className="col-6 col-12-mobile">

					<label className={`form-input full ${hasErrorClass('first_name')}`}>
						<span>
							First Name
						</span>
						<input
							type="text"
							name="first_name"
							maxLength={128}
							value={inputValues.first_name || ''}
							onChange={(e) => handleInputChange(e, setInputValues)}
						/>
						<InputErrorLabel data={getErrorMessage('first_name')} />
					</label>
				</div>
				<div className="col-6 col-12-mobile">

					<label className={`form-input full ${hasErrorClass('last_name')}`}>
						<span>
							Last Name
						</span>
						<input
							type="text"
							name="last_name"
							maxLength={128}
							value={inputValues.last_name || ''}
							onChange={(e) => handleInputChange(e, setInputValues)}
						/>
						<InputErrorLabel data={getErrorMessage('last_name')} />
					</label>
				</div>
			</div>
			<div className="row">
				<div className="col-12">
					<label className={`form-input full ${hasErrorClass('email')}`}>
						<span>
							Email <i className="required">*</i>
						</span>
						<input
							type="email"
							name="email"
							maxLength={255}
							value={inputValues.email || ''}
							onChange={(e) => handleInputChange(e, setInputValues)}
						/>
						<InputErrorLabel data={getErrorMessage('email')} />
					</label>
				</div>
			</div>
			<div className="row">
				<div className="col-6 col-12-mobile">


					<label className={`form-input full ${hasErrorClass('password')}`}>
						<span>
							Password <i className="required">*</i>
						</span>
						<input
							type="password"
							name="password"
							maxLength={255}
							value={inputValues.password || ''}
							onChange={(e) => handleInputChange(e, setInputValues)}
						/>
						<InputErrorLabel data={getErrorMessage('password')} />
					</label>
				</div>
				<div className="col-6 col-12-mobile">


					<label className={`form-input full ${hasErrorClass('password_verify')}`}>
						<span>
							Confirm Password <i className="required">*</i>
						</span>
						<input
							type="password"
							name="password_verify"
							maxLength={32}
							value={inputValues.password_verify || ''}
							onChange={(e) => handleInputChange(e, setInputValues)}
						/>
						<InputErrorLabel data={getErrorMessage('password_verify')} />
					</label>
				</div>
			</div>

			<div className={`form-input full ${hasErrorClass('confirm_age')}`}>
				<label className='checkbox'>
					<input
						type="checkbox"
						name="confirm_age"
						checked={inputValues.confirm_age === 1}
						onChange={(e) => handleInputChange(e, setInputValues)}
					/>
					<span>
						I am over 18 years old.
					</span>
				</label>
				<InputErrorLabel data={getErrorMessage('confirm_age')} />
			</div>

			<div className={`form-input full ${hasErrorClass('agree_terms')}`}>
				<label className='checkbox'>
					<input
						type="checkbox"
						name="agree_terms"
						checked={inputValues.agree_terms === 1}
						onChange={(e) => handleInputChange(e, setInputValues)}
					/>
					<span>
						I agree to the processing of my data according to the 
						{' '}
						<Link to={PageRoute.PRIVACY} target='_blank' title='Otevře odkaz v novém okně'>Privacy Policy</Link>.
					</span>
				</label>
				<InputErrorLabel data={getErrorMessage('agree_terms')} />
			</div>

			<div className='button-container'>
				<button type="submit"
					disabled={isLoading}
					className={['button', (isLoading ? 'loading' : '')].join(' ')}
				>
					Sign Up
				</button>
			</div>

		</form>
	)
}
