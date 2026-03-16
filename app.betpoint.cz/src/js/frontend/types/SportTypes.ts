export interface Sport {
	id: number;
	name: string;
	alias: string;
	categories: SportCategory[];
}

export interface SportCategory {
	id: number;
	name: string;
	sport_id: number;
	match_count: number;
	country_name: string;
	twice_enabled: boolean;
}

export interface SportMatch {
	id: number;
	sport_id: number;
	category_id: number;
	name: string;
	home: string;
	away: string;
	match_start: number;
	in_progress: boolean;
	odd_groups: SportOddGroup[];
}

export interface SportOddGroup {
	id: number;
	name: string;
	alias: string;
	rank: number;
	odds: SportOdd[];
}

export interface SportOdd {
  id: number;
  name: string;
  value: number;
  updated_at: number;
  team?: string; // <-- místo 'Home' | 'Away' | 'Draw'
  type?: string; // <-- místo 'Over' | 'Under'
  line?: number;
}

export interface BaseballOdd extends SportOdd {
  team: 'Home' | 'Away';
  type: 'Over' | 'Under';
  line: number;
}