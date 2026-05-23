export function initHeader() {
	const header = document.querySelector('.site-header');
	const menuToggle = document.querySelector('.menu-toggle');
	const primaryMenu = document.getElementById('primary-menu');

	if (!header || !menuToggle || !primaryMenu) return;

	// Sticky header on scroll
	window.addEventListener('scroll', () => {
		if (window.scrollY > 50) {
			header.classList.add('is-sticky');
		} else {
			header.classList.remove('is-sticky');
		}
	});

	// Mobile menu toggle
	menuToggle.addEventListener('click', () => {
		const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
		menuToggle.setAttribute('aria-expanded', !isExpanded);
		primaryMenu.classList.toggle('is-active');
	});
}
