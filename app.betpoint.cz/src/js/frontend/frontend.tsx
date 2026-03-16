import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import { Provider } from 'react-redux'
import { ErrorBoundary } from "react-error-boundary";
import store from './redux/store'
import AppError from "./components/AppError";
import App from './App'

const rootElement = document.getElementById('root');

if(rootElement) {
	const root = createRoot(rootElement);
	root.render(
		<React.StrictMode>
			<Provider store={store}>
				<BrowserRouter>
					<ErrorBoundary FallbackComponent={AppError}>
						<App />
					</ErrorBoundary>
				</BrowserRouter>
			</Provider>
		</React.StrictMode>
	);
} else {
	console.error('Root element not found');
}
