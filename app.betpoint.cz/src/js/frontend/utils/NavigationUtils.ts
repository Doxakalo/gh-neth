
/**
 * Scrolls an element into view, adding a fixed header offset.
 * 
 * @param element The element to scroll into view.
 * @param extraOffset Additional offset to apply beyond the header height.
 * @returns void
 */
export function scrollIntoViewWithHeaderOffset(element: HTMLElement | null, extraOffset: number = 0) {
	if (!element) return;

	const header = document.getElementById('main-header');
	const offset = header?.offsetHeight || 0;

	const elementTop = element.getBoundingClientRect().top;
	const scrollTop = window.pageYOffset + elementTop - offset;

	window.scrollTo({
		top: scrollTop - extraOffset,
		behavior: 'smooth',
	});
}
