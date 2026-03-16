import React, { useState } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import Modal from './Modal';
import { hideBetModal as hideModal, ModalState } from '../redux/modalSlice';
import BetForm from '../forms/BetForm';
import { AccountState } from '../redux/accountSlice';
import { declineCurrency, formatCurrency } from '../../common/utils/FormatUtils';

export default function BetModal() {
	const dispatch = useDispatch();
	const visible = useSelector((state: { modal: ModalState }) => state.modal.betVisible);
	const payload = useSelector((state: { modal: ModalState }) => state.modal.betPayload);
	const account = useSelector((state: { account: AccountState }) => state.account);
	const currentBalance = account?.wallet?.balance || 0;
	const [isFormSuccess, setIsFormSuccess] = useState<boolean>(false);

	const handleClose = () => {
		dispatch(hideModal());
		setIsFormSuccess(false);
	}

	const onFormSuccess = () => {
		setIsFormSuccess(true);
	}

	return (
		<Modal className='bet-modal' visible={visible} closeButton={true} dismissOnOutsideClick={false} closeHandler={handleClose}>
			<div className="header">
				<h2>New Bet</h2>
			</div>
			<div className="body">
				{payload && (
					!isFormSuccess ? (
						<BetForm oddId={payload.oddId} onSubmitSuccess={onFormSuccess} />
					) : (
						<div className='bet-form-success'>
							<h3>
								Your bet has been successfully placed.
							</h3>
							<p>
									Your ballance after this bet: <br />
									{' '}
									<strong>
										{formatCurrency(currentBalance)}
									</strong>
									{' '}
									{declineCurrency(currentBalance)}
							</p>
							<div className="button-container">
								<button className="button" onClick={handleClose}>
									Close
								</button>
							</div>
						</div>
					)
				)}
			</div>
		</Modal>
	)
}
