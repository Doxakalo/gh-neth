import React, { useState } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { hideComplaintModal as hideModal, ModalState } from '../redux/modalSlice';
import Modal from './Modal';
import ComplaintForm from '../forms/ComplaintForm';
import { declineCurrency, formatCurrency, getTimestampFormatted } from '../../common/utils/FormatUtils';
import { getBetStatusText } from '../types/BetTypes';

export default function ComplaintModal() {
	const dispatch = useDispatch();
	const visible = useSelector((state: { modal: ModalState }) => state.modal.complaintVisible);
	const payload = useSelector((state: { modal: ModalState }) => state.modal.complaintPayload);
	const [isFormSuccess, setIsFormSuccess] = useState<boolean>(false);
	const bet = payload && payload.bet ? payload.bet : null;

	const handleClose = () => {
		dispatch(hideModal());
		setIsFormSuccess(false);
	}

	const onFormSuccess = () => {
		setIsFormSuccess(true);
	}

	return (
		<Modal className='complaint-modal' visible={visible} closeButton={true} dismissOnOutsideClick={false} closeHandler={handleClose}>
			<div className="header">
				<h2>Submit a Complaint</h2>
			</div>
			<div className="body">
				{bet && (
					!isFormSuccess ? (
						<>
							<h3>Bet details</h3>
							<table className="data-list header-nowrap">
								<tbody>
									<tr>
										<th>
											Match:
										</th>
										<td>
											<strong>
												{bet.oddObj.sportMatch.home} / {bet.oddObj.sportMatch.away}
											</strong>
											<br />
											({getTimestampFormatted(bet.oddObj.sportMatch.match_start)})
										</td>
									</tr>
									<tr>
										<th>
											Bet:
										</th>
										<td>
											<strong>
												{bet.oddObj.oddBetType.name} - {bet.oddObj.name}
												{' '}
												/
												{' '}
												Odd: {bet.odd_value}
											</strong>
											<br />
											({getTimestampFormatted(bet.created_at)})
										</td>
									</tr>
									<tr>
										<th>
											Amount:
										</th>
										<td>
											<strong>
												{formatCurrency(bet.amount)}
												{' '}
												{declineCurrency(bet.amount)}
											</strong>
										</td>
									</tr>
									<tr>
										<th>
											Result:
										</th>
										<td>
											<strong>
												{getBetStatusText(bet.status)}
											</strong>
										</td>
									</tr>
								</tbody>
							</table>
							<ComplaintForm betId={bet.id} onSubmitSuccess={onFormSuccess} />
						</>
					) : (
						<div className='complaint-form-success'>
							<h3>
								Your complaint has been submitted.
							</h3>
							<p>
								We will check the complaint and take
								appropriate action to resolve the issue.
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
