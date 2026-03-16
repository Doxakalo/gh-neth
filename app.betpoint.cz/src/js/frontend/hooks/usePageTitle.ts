import { useEffect } from "react";
import { useLocation } from 'react-router-dom';

export default function usePageTitle(title: string): void {
	const location = useLocation();
	useEffect(() => {
		if(title && title !== '') {
			document.title = `${title} - ${window.appConfig?.appName}`;
		} else {
			document.title = window.appConfig?.appName;
		}
	}, [location, title]);
}
