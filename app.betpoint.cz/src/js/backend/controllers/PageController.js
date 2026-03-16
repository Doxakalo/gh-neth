export default class PageController {
	
	constructor() {

		// Initialize the application status check
		const checkInterval = window.appConfig.config.appStatusCheckInterval || 60000;
		this.checkAppStatus();
		setInterval(() => {
			this.checkAppStatus();
		}, checkInterval);
	}

	/**
	 * Checks the application status by making an API call to the backend.
	 * If the status is false, it displays a notification bar with the message.
	 * If the status is true, it hides the notification bar.
	 * 
	 * @returns {void}
	 */
	checkAppStatus() {
		const bar = document.getElementById('app-status-bar');
		fetch(window.appConfig.api.appStatus)
			.then(response => response.json())
			.then(data => {
				if (bar) {
					if (data.status === false) {
						bar.classList.add('visible');
						const paragraph = bar.querySelector('p');
						if (paragraph) {
							paragraph.textContent = data.message;
						}
					} else {
						bar.classList.remove('visible');
					}
				}
			});
	}

}
