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


// 3. Filtrado de barberos según el servicio seleccionado
document.addEventListener('DOMContentLoaded', function () {

    const servicios = document.querySelectorAll('input[name="servicio_id"]');
    const barberos = document.querySelectorAll('.opcion-barbero');

    servicios.forEach(servicio => {
        servicio.addEventListener('change', function () {

            const servicioSeleccionado = this.value;

            barberos.forEach(barbero => {
                const serviciosBarbero = barbero.dataset.servicios.split(',');

                if (serviciosBarbero.includes(servicioSeleccionado)) {
                    barbero.style.display = 'flex';
                } else {
                    barbero.style.display = 'none';

                    const radio = barbero.querySelector('input[type="radio"]');
                    radio.checked = false;
                }
            });

        });
    });

});

// 4. Navegación entre pasos de la reserva
document.addEventListener('DOMContentLoaded', function () {
    let pasoActual = 0;

    const steps = document.querySelectorAll('.reserva-step');
    const pasos = document.querySelectorAll('.paso');
    const btnSiguiente = document.querySelectorAll('.btn-siguiente');
    const btnAtras = document.querySelectorAll('.btn-atras');

    function mostrarPaso(index) {
        steps.forEach(step => step.classList.remove('activo'));
        pasos.forEach(paso => paso.classList.remove('activo'));

        steps[index].classList.add('activo');
        pasos[index].classList.add('activo');
    }

    btnSiguiente.forEach(btn => {
        btn.addEventListener('click', () => {
            if (pasoActual < steps.length - 1) {
                pasoActual++;
                mostrarPaso(pasoActual);
            }
        });
    });

    btnAtras.forEach(btn => {
        btn.addEventListener('click', () => {
            if (pasoActual > 0) {
                pasoActual--;
                mostrarPaso(pasoActual);
            }
        });
    });
});