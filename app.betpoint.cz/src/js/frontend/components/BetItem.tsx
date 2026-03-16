import React, { useState } from "react";
import { useDispatch } from 'react-redux';
import { Bet, BetStatus, isComplaintAllowed, isBetEvaluated, getBetPotentialWin, getBetStatusClassName, getBetStatusText, getBetResultAmount } from '../types/BetTypes';
import { formatCurrency, formatNumber, getTimestampFormatted } from '../../common/utils/FormatUtils';
import { showComplaintModal } from '../redux/modalSlice';

interface Props {
	bet: Bet;
};

export default function BetItem({ bet }: Props) {

	const [open, setOpen] = useState<boolean>(false);
	const dispatch = useDispatch();
	//console.log(bet);
	const getBetResultAmountFormatted = (): string => {
		const value = getBetResultAmount(bet);
		const formatted = formatCurrency(value);
		return value > 0 ? `+${formatted}` : formatted;
	};

	const getToggleButtonText = (): string => {
		return open ? "Hide detail" : "Show detail";
	};

	const handleComplaintClick = () => {
		dispatch(showComplaintModal({
			bet: bet
		}));
	}

	return (
		<div className={`bet-item ${open ? 'open' : ''} ${getBetStatusClassName(bet.status)}`}>
			<div className="column info">
				<div className="basic-info">
					<div className="group match-info">
						<div className="sport-name">
							{bet.oddObj.sportMatch.sport.name}
							{' / '}
							{bet.oddObj.sportMatch.category.name}
							{bet.oddObj.sportMatch.category.twice_enabled === 1 && (
							 <span> &#40; {bet.oddObj.sportMatch.category.country_name} &#41;</span>
							)}
						</div>

						<div className="match-name-with-date">
							<h2 className="match-name">
								<span className="field">
									<span className="label">Home</span>
									{bet.oddObj.sportMatch.home}&nbsp;/&nbsp;
								</span>
								<span className="field">
									<span className="label">Away</span>
									{bet.oddObj.sportMatch.away}
								</span>
							</h2>
							<span className="match-date" title='Match start date/time'>
								{getTimestampFormatted(bet.oddObj.sportMatch.match_start)}
							</span>
						</div>
					</div>

					<div className="group bet-type">
						<div className="field">
							<span className="label">
								Bet:
							</span>
							{' '}
							<strong>
								{bet.oddObj.oddBetType.name} - {bet.oddObj.name}
							</strong>
						</div>
					</div>

					<div className="group bet-values">
						<div className="field horizontal">
							<span className="label">
								Odd:
							</span>
							{' '}
							<strong>
								{formatNumber(bet.odd_value)}
							</strong>
						</div>
						<div className="field horizontal">
							<span className="label">
								Bet Amount:
							</span>
							{' '}
							<strong>
								{formatCurrency(bet.amount)}
							</strong>
						</div>

						{bet.oddObj?.sportMatch?.evaluated === 1 && bet.oddObj?.sportMatch?.result && (
							(() => {
							const result = JSON.parse(bet.oddObj.sportMatch.result);
							return (
								<div className="field horizontal">
								<span className="label">Result:</span>{' '}
								<strong>
									{/*{bet.oddObj.sportMatch.home}*/} {result.home} : {result.away} {/*{bet.oddObj.sportMatch.away}*/}
								</strong>
								</div>
							);
							})()
						)}
											</div>
				</div>
				{open && (
					<div className="detail">
						<div className="group date-info">
							<div className="field">
								<span className="label">Match date:</span>
								<strong>{getTimestampFormatted(bet.oddObj.sportMatch.match_start)}</strong>
							</div>
							<div className="field">
								<span className="label">Bet date:</span>
								<strong>{getTimestampFormatted(bet.created_at)}</strong>
							</div>
							<div className="field">
								<span className="label">Bet ID:</span>
								<strong>#{bet.id_hash}</strong>
							</div>
						</div>
						<div className="group buttons">
							{isComplaintAllowed(bet.status) && (
								<button className="button small" onClick={() => handleComplaintClick()}>
									Submit a Complaint
								</button>
							)}
						</div>
					</div>
				)}
			</div>
			<div className={`column status ${getBetStatusClassName(bet.status)}`}>
				{isBetEvaluated(bet.status) && (
					<span className="amount-label">
						{getBetResultAmountFormatted()}
					</span>
				)}

				<span className="status-label">
					{getBetStatusText(bet.status)}
				</span>

				{bet.status === BetStatus.PENDING && (
					<span className="status-label tiny">
						Potential Win: {' '}
						{formatCurrency(getBetPotentialWin(bet.amount, bet.odd_value))}
					</span>
				)}
			</div>
			<div className="column toggle">
				<button
					className={`toggle-button${open ? ' open' : ''}`}
					title={getToggleButtonText()}
					aria-label={getToggleButtonText()}
					aria-expanded={open}
					onClick={() => setOpen(!open)}
				>
					<span>{getToggleButtonText()}</span>
					<i className="icon sbc-icon-caret-right"></i>
				</button>
			</div>
		</div>
	);
}
