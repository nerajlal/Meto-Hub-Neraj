(() => {
    'use strict';
    document.querySelectorAll('[data-pantry-reels]').forEach(section => {
        const rail = section.querySelector('[data-reels-rail]');
        const previous = section.querySelector('[data-reels-prev]');
        const next = section.querySelector('[data-reels-next]');
        const updateArrows = () => {
            if (!previous || !next) return;
            const overflowing = rail.scrollWidth > rail.clientWidth + 2;
            previous.parentElement.hidden = !overflowing;
            previous.disabled = rail.scrollLeft <= 2;
            next.disabled = rail.scrollLeft >= rail.scrollWidth - rail.clientWidth - 2;
        };
        const move = direction => rail.scrollBy({left: direction * ((rail.firstElementChild?.getBoundingClientRect().width || 260) + (parseFloat(getComputedStyle(rail).columnGap) || 0)),
            behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'});
        previous?.addEventListener('click', () => move(-1));
        next?.addEventListener('click', () => move(1));
        rail.addEventListener('scroll', updateArrows, {passive: true});
        if (typeof ResizeObserver === 'function') new ResizeObserver(updateArrows).observe(rail);
        updateArrows();
    });
})();
