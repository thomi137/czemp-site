if (window.matchMedia('(hover: none)').matches) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            entry.target.classList.toggle('is-visible', entry.isIntersecting);
        });
    }, { rootMargin: '-30% 0px -30% 0px', threshold: 0 });

    document.querySelectorAll('.gallery-item').forEach(el => observer.observe(el));
}
