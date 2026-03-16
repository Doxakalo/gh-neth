import PageController from './controllers/PageController';
import UserUpdateController from './controllers/UserUpdateController';
import UserViewController from './controllers/UserViewController';

onDocumentReady(function() {
	const html = document.querySelector('html');
	const controllerAction = html ? html.dataset.controllerAction : null;

	switch (controllerAction) {
		case 'user/update':
			new UserUpdateController();
			break;

		case 'user/view':
			new UserViewController();
			break;
		
		default:
			new PageController();
			break;
	}
});

function onDocumentReady(fn) {
	if (document.readyState === "complete" || document.readyState === "interactive") {
		setTimeout(fn, 1);
	} else {
		document.addEventListener("DOMContentLoaded", fn);
	}
}
