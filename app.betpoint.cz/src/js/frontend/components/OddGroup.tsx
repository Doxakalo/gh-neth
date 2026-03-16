import React from "react";
import { SportOddGroup } from '../types/SportTypes';
import OddItem from './OddItem';
import { SportTreeState } from '../redux/sportTreeSlice';
import { useSelector } from 'react-redux';
import { SportOdd } from "../types/SportTypes";
interface Props {
	oddGroup: SportOddGroup;
};

export default function OddGroup({ oddGroup }: Props) {

	//console.log(`group`,oddGroup);
	const sportTreeState = useSelector((state: { sportTree: SportTreeState }) => state.sportTree);
	//console.log("newtreestatesliceoddgroup", sportTreeState);

	const getListClassname = () => {
		if(['match-winner'].includes(oddGroup.alias)) {
			return 'odd-list-3-col';
		} 

		if(['result-total-goals'].includes(oddGroup.alias)) {
			let returnVal:string;
			if(sportTreeState.sport?.alias === "baseball" || sportTreeState.sport?.alias === "hockey") {
				returnVal = 'odd-list-4-col';
			} else {
				returnVal = 'odd-item-3';
			}
			return returnVal;
		}
		return '';
	};

	const oddItemClassname = () => {

		if(['result-total-goals'].includes(oddGroup.alias)) {
			let returnVal:string;
			if(sportTreeState.sport?.alias === "baseball" || sportTreeState.sport?.alias === 'hockey' || sportTreeState.sport?.alias === 'nfl') {
				returnVal = 'odd-item-4';
			} else {
				returnVal = 'odd-item-3';
			}
			return returnVal;
		}

		return 'odd-item';
	}

	let passedOdds: SportOdd[] = [];

	type ResultTotalOdd = SportOdd & { team: 'Home' | 'Away'; type: 'Over' | 'Under'; line: number };

	if (oddGroup.alias === 'result-total-goals' && ['baseball', 'hockey', 'nfl'].includes(sportTreeState.sport?.alias || '')) {
		const processedOdds: ResultTotalOdd[] = oddGroup.odds
			.map(odd => {
			const match = odd.name.match(/^(Home|Away)\/(Over|Under)\s*([\d.]+)$/i);
			if (!match) return null;
			return {
				...odd,
				team: match[1] as 'Home' | 'Away',
				type: match[2] as 'Over' | 'Under',
				line: parseFloat(match[3])
			} as ResultTotalOdd;
			})
			.filter((o): o is ResultTotalOdd => o !== null);

		const homeLines = processedOdds.filter(o => o.team === 'Home');
		const awayLines = processedOdds.filter(o => o.team === 'Away');

		const uniqueHomeLines = Array.from(new Set(homeLines.map(o => o.line))).sort((a, b) => a - b);
		const uniqueAwayLines = Array.from(new Set(awayLines.map(o => o.line))).sort((a, b) => a - b);

		const finalOdds: ResultTotalOdd[] = [];
		const maxRows = Math.max(uniqueHomeLines.length, uniqueAwayLines.length);

		for (let i = 0; i < maxRows; i++) {
			const homeLine = uniqueHomeLines[i];
			const awayLine = uniqueAwayLines[i];

			if (homeLine !== undefined) {
			finalOdds.push(homeLines.find(o => o.type === 'Over' && o.line === homeLine) as ResultTotalOdd);
			finalOdds.push(homeLines.find(o => o.type === 'Under' && o.line === homeLine) as ResultTotalOdd);
			}

			finalOdds.push({} as ResultTotalOdd); // divider for home away

			if (awayLine !== undefined) {
			finalOdds.push(awayLines.find(o => o.type === 'Over' && o.line === awayLine) as ResultTotalOdd);
			finalOdds.push(awayLines.find(o => o.type === 'Under' && o.line === awayLine) as ResultTotalOdd);
			}
		}

		passedOdds = finalOdds.filter(o => o);
	} else if (oddGroup.alias === 'result-total-goals') {
		
		const processedOdds = oddGroup.odds.map(odd => {
			const match = odd.name.match(/^(Home|Away|Draw)\/(Over|Under)\s*([\d.]+)$/i);
			if (match) {
				return {
					...odd,
					team: match[1],              // Home, Away, Draw
					type: match[2],              // Over, Under
					line: parseFloat(match[3])   // 2.5, 3.5...
				};
			}
			return { ...odd, team: odd.name, type: '', line: 0 };
		});

		type Team = 'Away' | 'Home' | 'Draw';
		type BetType = 'Over' | 'Under';

		const teamOrder: Record<Team, number> = { Home: 1, Draw: 2, Away: 3 };
		const typeOrder: Record<BetType, number> = { Over: 1, Under: 2 };

		const sortedOdds = processedOdds.sort((a, b) => {
			if (a.line !== b.line) return a.line - b.line;
			if (a.type !== b.type) return typeOrder[a.type as BetType] - typeOrder[b.type as BetType];
			return teamOrder[a.team as Team] - teamOrder[b.team as Team];
		});

		
		passedOdds = sortedOdds;
	} else if (oddGroup.alias !== 'match-winner' && oddGroup.alias !== 'result-total-goals') {
		//1. Dividing odds to get name and number 
		const processedOdds = oddGroup.odds.map(odd => {
			const match = odd.name.match(/^([a-zA-Z\s]+)([\d.]+)$/);
			if (match) {
				return {
					...odd,
					text: match[1].trim(),
					number: parseFloat(match[2])
				};
			}
			return { ...odd, text: odd.name, number: 0 };
		});

		//2. Sort items by numbers
		const sortedOdds = processedOdds.sort((a, b) => a.number - b.number);

		//3. Delete duplicities
		const finalOdds: typeof sortedOdds = [];
		let lastText: string | null = null;
		for (const odd of sortedOdds) {
			if (odd.text !== lastText) {
				finalOdds.push(odd);
				lastText = odd.text;
			}
		}



		// 4. Filter pairs by value duplicates
		const filteredOdds: typeof finalOdds = [];
		for (let i = 0; i < finalOdds.length; i += 2) {
			const odd1 = finalOdds[i];
			const odd2 = finalOdds[i + 1];

			if (!odd2) break; 

			if (odd1.value !== odd2.value) {
				filteredOdds.push(odd1, odd2);
			}
		}
		passedOdds = filteredOdds;
		if (oddGroup.name === '3Way Result' && passedOdds.length !== 3) {
			passedOdds = [];
		}
	} else {
		passedOdds = oddGroup.odds;
		if (oddGroup.name === '3Way Result' && passedOdds.length !== 3) {
			passedOdds = [];
		}
	}



	const getGroupName = (oddGroup: SportOddGroup) => {
	if (oddGroup.alias === 'result-total-goals') {
		return 'Result/total';
	}
	return oddGroup.name;
	};


	//!! extra handler for nfl at current sports api state!!//
	if ((oddGroup.alias === 'over-under' || oddGroup.alias === 'total-away' || oddGroup.alias === 'total-home') && sportTreeState.sport?.alias === 'nfl') {
		type OUOdd = SportOdd & { team: 'Home' | 'Away'; type: 'Over' | 'Under'; line: number };

		const processedOdds: OUOdd[] = oddGroup.odds
			.map(odd => {
				const match = odd.name.match(/^(Over|Under)\s*([\d.]+)$/i);
				if (!match) return null;

				let team: 'Home' | 'Away' = 'Home';
				if (oddGroup.alias === 'total-away') team = 'Away';
				if (oddGroup.alias === 'total-home') team = 'Home';

				return {
					...odd,
					team,
					type: match[1] as 'Over' | 'Under',
					line: parseFloat(match[2])
				} as OUOdd;
			})
			.filter((o): o is OUOdd => o !== null);

		// Remove duplicities and use newest odd for pass
		const latestOddsMap: Record<string, OUOdd> = {};
		processedOdds.forEach(odd => {
			if (!latestOddsMap[odd.name] || odd.updated_at > latestOddsMap[odd.name].updated_at) {
				latestOddsMap[odd.name] = odd;
			}
		});

		const uniqueOdds = Object.values(latestOddsMap);

		// Home away divider
		const homeLines = uniqueOdds.filter(o => o.team === 'Home').sort((a, b) => a.line - b.line);
		const awayLines = uniqueOdds.filter(o => o.team === 'Away').sort((a, b) => a.line - b.line);

		// remove rows where under and over having the same vals
		const filterSameValue = (lines: OUOdd[]) => {
			const result: OUOdd[] = [];
			for (let i = 0; i < lines.length; i += 2) {
				const odd1 = lines[i];
				const odd2 = lines[i + 1];
				if (!odd2) {
					result.push(odd1);
					break;
				}
				if (odd1.value !== odd2.value) {
					result.push(odd1, odd2);
				}
			}
			return result;
		};

		const filteredHome = filterSameValue(homeLines);
		const filteredAway = filterSameValue(awayLines);

		const finalOdds: (OUOdd | null)[] = [];
		const maxRows = Math.max(filteredHome.length / 2, filteredAway.length / 2);

		for (let i = 0; i < maxRows; i++) {
			const homeOver = filteredHome[i * 2];
			const homeUnder = filteredHome[i * 2 + 1];
			const awayOver = filteredAway[i * 2];
			const awayUnder = filteredAway[i * 2 + 1];

			if (homeOver) finalOdds.push(homeOver);
			if (homeUnder) finalOdds.push(homeUnder);

			finalOdds.push(null); // mezera mezi Home a Away

			if (awayOver) finalOdds.push(awayOver);
			if (awayUnder) finalOdds.push(awayUnder);
		}

		passedOdds = finalOdds.filter((o): o is OUOdd => o !== null);
	}

	if (oddGroup.alias === 'home-away' && sportTreeState.sport?.alias === 'nfl') {
		const latestOddsMap: Record<string, SportOdd> = {};
		oddGroup.odds.forEach(odd => {
			if (!latestOddsMap[odd.name] || odd.updated_at > latestOddsMap[odd.name].updated_at) {
				latestOddsMap[odd.name] = odd;
			}
		});

		const order: Record<string, number> = { Home: 1, Away: 2 };
		passedOdds = Object.values(latestOddsMap).sort((a, b) => order[a.name] - order[b.name]);
	}

	passedOdds = passedOdds.filter(o => o && o.id != null);
	return (
		<div className="odd-group">
			<h3>{getGroupName(oddGroup)}</h3>
			<div className={`odd-list ${getListClassname()}`}>
			{passedOdds.length > 0 ? (
				passedOdds.map((odd) => (
				<OddItem className={`${oddItemClassname()}`} key={odd.id || odd.name} odd={odd} />
				))
			) : (
				<div className="no-valid-bets">No valid bets available</div>
			)}
			</div>
		</div>
	);
}
