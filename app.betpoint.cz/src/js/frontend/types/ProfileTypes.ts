export interface Profile {
	id: number;
	first_name: string;
	last_name: string;
	nickname: string;
	email: string;
	created_at: number;
}

export interface Wallet {
	balance: number;
}
