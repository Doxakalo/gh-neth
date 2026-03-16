import React, { useEffect, useRef } from "react";
import { useSelector } from 'react-redux';
import usePageTitle from '../hooks/usePageTitle';
import SportTree from '../components/SportTree';
import MatchList from '../components/MatchList';
import { SportTreeState } from '../redux/sportTreeSlice';
import { BREAKPOINTS, useMaxWidth } from '../../common/hooks/useBreakpoint';
import { scrollIntoViewWithHeaderOffset } from '../utils/NavigationUtils';

export default function PageIndex() {
	usePageTitle('Home');
	const sportTreeState = useSelector((state: { sportTree: SportTreeState }) => state.sportTree);
	const pageHeading = useRef<HTMLDivElement | null>(null);
	const isMobile = useMaxWidth(BREAKPOINTS.MOBILE);


	/**
	 * Scroll to top on category change
	 */
	useEffect(() => {
		if(isMobile) {
			// scroll to heading on mobile, delayed to ensure the page is rendered
			setTimeout(() => {
				scrollIntoViewWithHeaderOffset(pageHeading.current, 10);
			}, 100);
		} else {
			// scroll to top on desktop
			window.scrollTo({ top: 0, behavior: 'smooth' });
		}
	}, [sportTreeState.sportCategory]);

	
	/**
	 * Decline match count based on the number of matches
	 * @param count - Number of matches
	 * @returns - String indicating singular or plural form
	 */
	const declineMatch = (count: number): string => {
		if (count === 1) return 'match';
		return 'matches';
	}

	const messageText = <p>Please select a sport and league from the menu to view individual matches and place your bets.</p>;


	return (
		<div className="page index">
			<div className="container">
				<div className="row">
					<div className="col-12">
						<div className="sidebar-layout">
							<div className="sidebar">
								{!(sportTreeState.sport && sportTreeState.sportCategory) && (
									<div className="box rounded yellow match-placeholder last-child-no-margin hide show-mobile">
										{messageText}
										<i className="icon sbc-icon-arrow-down large"></i>
									</div>
								)}
								<SportTree />
							</div>
							<div className="main-content">
								{sportTreeState.sport && sportTreeState.sportCategory ? (
									<>
										<div ref={pageHeading} className="page-heading"> 
											<h1>{sportTreeState.sport.name} &rsaquo; {sportTreeState.sportCategory.name} {sportTreeState.sportCategory.twice_enabled ? `(${sportTreeState.sportCategory.country_name})` : ''}</h1>
											<p>Found <strong>{sportTreeState.sportCategory.match_count}</strong> {declineMatch(sportTreeState.sportCategory.match_count)}:</p>
										</div>

										<MatchList categoryId={sportTreeState.sportCategory.id} />
									</>
								) : (
									<div className="box rounded yellow match-placeholder last-child-no-margin hide-mobile">
										<i className="icon sbc-icon-arrow-left large"></i>
										{messageText}
									</div>
								)}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
}
