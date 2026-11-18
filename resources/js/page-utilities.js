const scrollToTopButton = document.querySelector('[data-scroll-to-top]');

if (scrollToTopButton) {
    const updateScrollToTopVisibility = () => {
        scrollToTopButton.hidden = window.scrollY < 320;
    };

    scrollToTopButton.addEventListener('click', () => {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        window.scrollTo({
            top: 0,
            behavior: prefersReducedMotion ? 'auto' : 'smooth',
        });
    });

    window.addEventListener('scroll', updateScrollToTopVisibility, { passive: true });
    updateScrollToTopVisibility();
}
