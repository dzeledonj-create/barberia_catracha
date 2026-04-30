const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-visible');
        }
    });
}, { threshold: 0.1 }); // Se activa cuando se ve el 10% de la sección

document.querySelectorAll('.explore-card').forEach(card => {
    observer.observe(card);
});