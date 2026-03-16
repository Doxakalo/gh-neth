import React, { useState } from "react";
import { SportMatch } from '../types/SportTypes';
import { getTimestampFormatted } from '../../common/utils/FormatUtils';
import OddGroup from './OddGroup';

interface Props {
	sportMatch: SportMatch;
}

export default function MatchItem({ sportMatch }: Props) {
	const enabled = !sportMatch.in_progress;
	const [open, setOpen] = useState<boolean>(false);
	//console.log(sportMatch);
	return (
		<div className={`match-item ${open ? 'open' : ''} ${!enabled ? 'disabled' : ''}`}>
			<div className="heading" onClick={() => enabled && setOpen(!open)}>
				<div className="info">
					<h2 className="match-name">
						<span className="field">
							<span className="label">Home</span>
							{sportMatch.home}
						</span>
						<span className="field separator">
							/
						</span>
						<span className="field">
							<span className="label">Away</span>
							{sportMatch.away}
						</span>
					</h2>
					<div className="match-date" title='Match start date/time'>
						{getTimestampFormatted(sportMatch.match_start)}
					</div>
				</div>
				<div className="controls">
					{sportMatch.in_progress ? (
						<span className="status-label in-progress">In Progress</span>
					) : (
						<span className={`toggle ${open ? 'open ' : ''}`}>
							<span className='label-mobile'>
								Odds{' '}
							</span>
							<span className='label'>
								{open ? 'Hide odds' : 'Show odds'}{' '}
							</span>
							<i className='caret icon sbc-icon-caret-down'></i>
						</span>
					)}
					
				</div>
			</div>
			{enabled && open && (
				<div className="odd-group-list">
					{sportMatch.odd_groups?.map((group, idx) => (
						<OddGroup key={group.id || idx} oddGroup={group} />
					))}
				</div>
			)}
		</div>
	);
}
