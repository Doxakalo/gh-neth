import React, { useState, useEffect } from 'react';
import { useSelector } from 'react-redux';
import { useCreateBetMutation } from '../redux/apiSlice';
import { selectOddById } from '../redux/selectors/oddSelectors';
import { ApiError } from '../types/ErrorTypes';
import InputErrorLabel from "../../common/components/InputErrorLabel";
import { AccountState } from '../redux/accountSlice';
import { declineCurrency, formatCurrency, formatNumber, parseNumber } from '../../common/utils/FormatUtils';
import { getBetPotentialWin } from '../types/BetTypes';

interface Props {
	oddId: number;
	onSubmitSuccess: () => void;
}

interface FormState {
    odd_id: number|null,
	amount: number|'',
}

const initialInputValues:FormState = {
    odd_id: null,
    amount: '',
};

export default function BetForm({ oddId, onSubmitSuccess }: Props) {
	const [createBet, { data: createBetResult, isLoading, error }] = useCreateBetMutation();
	const [inputValues, setInputValues] = useState<FormState>({
		...initialInputValues,
		odd_id: oddId,
	});
	const account = useSelector((state: { account: AccountState }) => state.account);
	const currentBalance = account?.wallet?.balance || 0;
	const selectedOdd = useSelector(selectOddById(oddId));

	const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
		event.preventDefault();
		createBet(inputValues);
	}

	const getErrorMessage = (field: string): string | null => {
		return createBetResult?.errors?.[field] || 
			(error as ApiError)?.data?.errors?.[field] || 
			null;
	};

	const hasErrorClass = (field: string): string => {
		return getErrorMessage(field) ? 'has-error' : '';
	};

	const handleAmountChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const rawValue = event.target.value;

        // If the input is cleared, set to empty string
        if (rawValue === '') {
            setInputValues(values => ({ ...values, amount: '' }));
            return;
        }

        // Otherwise, parse the number and apply constraints
        let value: number = parseNumber(rawValue);
        
		// Prevent negative numbers
        if (value < 0) {
            value = 0; 
        }

		// Limit to current balance, round to two decimal places
        if (value > currentBalance) {
            value = Math.floor(currentBalance * 100) / 100; 
        }
		
        setInputValues(values => ({ ...values, amount: value }));
    };

	const handleAmountFocus = (event: React.FocusEvent<HTMLInputElement>) => {
		if(parseFloat(event.target.value) === 0) {
			event.target.select();
		}
	};

	const canAmountBeBet = (): boolean => {
		const betAmount = typeof inputValues.amount === 'number' ? inputValues.amount : 0;
		return betAmount > 0 && betAmount <= currentBalance;
	}

	const getFutureBalanceAfterBet = (): number => {
		const betAmount = typeof inputValues.amount === 'number' ? inputValues.amount : 0;
		if (betAmount >= 0 && betAmount <= currentBalance) {
			return currentBalance - betAmount;
		}
		return currentBalance;
	}

	const canBetBePlaced = (): boolean => {
		return getErrorMessage('odd_id') ? false : true;
	}
	
	useEffect(() => {
		if(createBetResult?.success === true) {
			onSubmitSuccess();
		}
    }, [createBetResult]);

	return (
		<form onSubmit={handleSubmit} className={`bet-form ${isLoading ? 'disabled' : ''}`} noValidate>
			<div className="info mb-2">
				<div className="info-row">
					<div className="label">Bet type:</div>
					<div className="value">{selectedOdd?.name || '--'}</div>
				</div>
				<div className="info-row">
					<div className="label">Odd:</div>
					<div className="value">
						{selectedOdd?.value ? formatNumber(selectedOdd.value) : '--'}
					</div>
				</div>
			</div>

			<div className="row mb-1">
				<div className="col-6 col-12-mobile-small">
					<label className={`form-input full ${hasErrorClass('amount')}`}>
						<span>
							Amount to bet:
						</span>
						<input
							type="number"
							name="amount"
							placeholder='Amount'
							step={1}
							min={0}
							max={currentBalance}
							value={inputValues.amount}
							onChange={(e) => handleAmountChange(e)}
							onFocus={(e) => handleAmountFocus(e)}
						/>
						<InputErrorLabel data={getErrorMessage('amount')} />
					</label>
				</div>
				<div className="col-6">
					<label className={`form-input full potential-win ${hasErrorClass('amount')}`}>
						<span>
							Potential win:
						</span>
						<span className="value">
							{formatCurrency(getBetPotentialWin(
								inputValues.amount || 0,
								selectedOdd?.value || 0
							), true)}
						</span>
					</label>
				</div>
			</div>

			<p className='balance tac small'>
				{canAmountBeBet() ? (
					<>
						Your ballance after this bet: 
						{' '}
						<strong>
							{formatCurrency(getFutureBalanceAfterBet())}
						</strong>
						{' '}
						{declineCurrency(getFutureBalanceAfterBet())}
					</>
				) : (
					<>
						Please enter amount to bet.
					</>
				)}
			</p>

			<div className='global-error'>
				<InputErrorLabel data={getErrorMessage('odd_id')} />
			</div>

			<div className='button-container'>
				<button type="submit"
					disabled={isLoading || !canAmountBeBet() || !canBetBePlaced()}
					className={['button', (isLoading ? 'loading' : '')].join(' ')}
				>
					Place Bet
				</button>
			</div>

		</form>
	)
}
