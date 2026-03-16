export enum TransactionTypeEnum {
	INITIAL_CREDIT = 10,
	UPDATE_CREDIT = 11,
	BET = 20,
	WIN = 30,
	REEVALUATION = 40,
	RETURN = 50,
}

export interface Transaction {
	id: number;
	amount: number;
	action: string;
	description: string;
	type: TransactionTypeEnum;
	user_bet_id: number | null;
	user_bet_id_hash: string | null;
	created_at: number;
}

const transactionTypeClassNames: Record<TransactionTypeEnum, string> = {
	[TransactionTypeEnum.INITIAL_CREDIT]: "type-initial-credit",
	[TransactionTypeEnum.UPDATE_CREDIT]: "type-update-credit",
	[TransactionTypeEnum.BET]: "type-bet",
	[TransactionTypeEnum.WIN]: "type-win",
	[TransactionTypeEnum.REEVALUATION]: "type-reevaluation",
	[TransactionTypeEnum.RETURN]: "type-return",
};

export const getTransactionTypeClassName = (status: TransactionTypeEnum): string => {
	return transactionTypeClassNames[status] ?? "";
};
