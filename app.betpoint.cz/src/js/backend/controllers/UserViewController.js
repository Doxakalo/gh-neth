import PageController from './PageController';
import Tabs from '../components/Tabs';

export default class UserViewController extends PageController {

	constructor() {
		super();

		// init tabs
		const tabsEl = document.getElementById('user-bet-transactions-tabs')
		if (tabsEl) {
			new Tabs(tabsEl);
		}

		// init callback for tab pjax content load
		window.onUserViewPjaxContentLoad = () => {
			window.scrollTo({ top: 0, behavior: 'smooth' });
			this.initToggleButtons();
		};	

		this.initToggleButtons();
	}


	initToggleButtons() {
		const toggleButtons = document.querySelectorAll('.bet-item .toggle-button');
		toggleButtons.forEach(button => {
			button.addEventListener('click', () => {
				button.closest('.bet-item').classList.toggle('open');
				button.classList.toggle('open');
			});
		});
	}

}
