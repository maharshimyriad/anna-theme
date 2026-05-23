import gsap from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function initAnimations() {
	// Respect user's motion preferences
	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	
	if (prefersReducedMotion) return;

	// Basic section reveal
	const sections = document.querySelectorAll('section');
	sections.forEach(section => {
		gsap.fromTo(section, 
			{ opacity: 0, y: 30 },
			{
				opacity: 1,
				y: 0,
				duration: 0.8,
				ease: 'power2.out',
				scrollTrigger: {
					trigger: section,
					start: 'top 80%',
				}
			}
		);
	});
}
