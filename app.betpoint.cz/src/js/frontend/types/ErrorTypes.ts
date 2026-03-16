export interface ApiError {
	data?: {
		errors?: {
			[key: string]: string;
		};
	};
}
