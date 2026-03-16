export interface Bet {
	id: number;
	id_hash: string;
	amount: number;
	odd_value: number;
	status: BetStatusType;
	odd_id: number;
	created_at: number;
	oddObj: {
		id: number;
		name: string;
		sport_match_id: number;
		odd_bet_type_id: number;
		oddBetType: {
			id: number;
			name: string;
		};
		sportMatch: {
			id: number;
			name: string;
			sport_id: number;
			category_id: number;
			home: string;
			away: string;
			match_start: number;
			evaluated: number;
			result: string;
			sport: {
				id: number;
				name: string;
			};
			category: {
				id: number;
				name: string;
				country_name: string;
				twice_enabled: number;		
			};
		};
	};
}

export const BetStatus = {
	PENDING: 0,
	WIN: 20,
	LOSS: 30,
	CANCELLED: 40,
} as const;

export type BetStatusType = typeof BetStatus[keyof typeof BetStatus];

export const complaintAllowedStatuses = [
	BetStatus.WIN,
	BetStatus.LOSS,
] as const;

export const evaluatedStatuses = [
	BetStatus.WIN,
	BetStatus.LOSS,
] as const;


/**
 * Check if a bet status allows for a complaint
 * 
 * @param status 
 * @returns boolean
 */
export const isComplaintAllowed = (status: BetStatusType): boolean => {
	return (complaintAllowedStatuses as readonly BetStatusType[]).includes(status);
};


/**
 * Check if a bet status is evaluated
 * 
 * @param status 
 * @returns boolean
 */
export const isBetEvaluated = (status: BetStatusType): boolean => {
	return (evaluatedStatuses as readonly BetStatusType[]).includes(status);
};


/**
 * Get CSS class name for bet status
 * 
 * @param status
 * @returns {string} CSS class name
 */
export const getBetStatusClassName = (status: BetStatusType): string => {
	switch (status) {
		case BetStatus.WIN:
			return "status-win";
		case BetStatus.LOSS:
			return "status-loss";
		case BetStatus.CANCELLED:
			return "status-cancelled";
		case BetStatus.PENDING:
		default:
			return "status-pending";
	}
};


/**
 * Get text representation of bet status
 * 
 * @param status
 * @returns {string} Text representation of bet status
 */
export const getBetStatusText = (status: BetStatusType): string => {
	switch (status) {
		case BetStatus.WIN:
			return "Won";
		case BetStatus.LOSS:
			return "Lost";
		case BetStatus.CANCELLED:
			return "Cancelled";
		case BetStatus.PENDING:
		default:
			return "Not Finished";
	}
};


/**
 * Get result amount for a bet
 * 
 * @param bet - Bet object
 * @returns {number} Result amount
 */
export const getBetResultAmount = (bet:Bet): number => {
	switch (bet.status) {
		case BetStatus.WIN:
			return bet.amount * bet.odd_value;
		case BetStatus.LOSS:
			return bet.amount * -1;
		default:
			return 0;
	}
};

/**
 * Calculate potential win amount for a bet
 * 
 * @param betAmount - Amount of the bet
 * @param oddValue - Value of the odd
 * @returns {number} Potential win
 */
export const getBetPotentialWin = (betAmount: number, oddValue: number): number => {
	return betAmount * oddValue;
}
