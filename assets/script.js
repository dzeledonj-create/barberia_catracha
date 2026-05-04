// 1. Control del Header al hacer Scroll
window.addEventListener('scroll', () => {
    const header = document.querySelector('.main-header');
    if (window.scrollY > 100) {
        header.style.background = 'rgba(10, 10, 10, 0.95)';
        header.style.height = '70px';
        header.style.backdropFilter = 'blur(10px)'; // Efecto cristalino moderno
    } else {
        header.style.background = '#0a0a0a';
        header.style.height = '80px';
        header.style.backdropFilter = 'none';
    }
});

// 2. Animación de revelado para las tarjetas de servicio
const observerOptions = { threshold: 0.2 };

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.explore-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'all 0.6s ease-out';
    observer.observe(card);
});