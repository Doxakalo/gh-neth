import React from "react";
import { useDispatch } from 'react-redux';
import { showBetModal } from '../redux/modalSlice';
import { SportOdd } from '../types/SportTypes';
import { formatNumber } from '../../common/utils/FormatUtils';

interface Props {
	odd: SportOdd;
	className: string;
}

export default function OddItem({ odd, className }: Props) {
	const dispatch = useDispatch();

	const handleClick = () => {
		dispatch(showBetModal({
			oddId: odd.id,
		}));
	}

	return (
		<button className={className} title={odd.name} onClick={() => handleClick()}>
			<div className="label">{odd.name}</div>
			<div className="value">{formatNumber(odd.value)}</div>
		</button>
	);
}
