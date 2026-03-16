import { AccountState } from '../redux/accountSlice';

export { };

declare global {
	interface Window {
		appConfig: {
			baseUrl: string;
			apiBaseUrl: string;
			currentPath: string;
			appName: string;
			state: {
				account: AccountState;
			};
			flashData: {
				signupEnabled: boolean | null;
			}
			debug: boolean;
		};
	}
}
