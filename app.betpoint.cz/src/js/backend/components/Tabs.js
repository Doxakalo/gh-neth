export default class Tabs {
	constructor(root) {
		this.root = root;
		this.tabButtons = Array.from(root.querySelectorAll('.tab-button'));
		this.tabContents = Array.from(root.querySelectorAll('[data-tab-content]'));

		this.tabButtons.forEach((btn, idx) => {
			btn.addEventListener('click', () => this.activateTab(idx));
		});

		this.activateTab(0);
	}

	activateTab(index) {
		this.tabButtons.forEach((btn, i) => {
			btn.classList.toggle('active', i === index);
			btn.setAttribute('aria-selected', i === index);
			btn.tabIndex = i === index ? 0 : -1;
		});
		this.tabContents.forEach((content, i) => {
			content.classList.toggle('active', i === index);
		});
	}
}
