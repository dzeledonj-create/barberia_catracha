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

// reserva.js//

document.addEventListener('DOMContentLoaded', function () {
    let pasoActual = 0;
    const steps = document.querySelectorAll('.reserva-step');
    const pasoItems = document.querySelectorAll('.paso-item');
    const btnSiguiente = document.querySelectorAll('.btn-siguiente');
    const btnAtras = document.querySelectorAll('.btn-atras');
    
    // Almacenamiento temporal de datos seleccionados para el ticket
    let servicioSeleccionadoText = "";
    let servicioPrecio = "";
    let barberoSeleccionadoText = "";
    let horaSeleccionada = "";

    // Inicialización de Flatpickr en Español
    if (document.getElementById('datepicker')) {
        flatpickr("#datepicker", {
            locale: "es",
            minDate: "today",
            dateFormat: "Y-m-d",
            inline: true, // Se muestra incrustado directamente como en tu diseño
            onChange: function(selectedDates, dateStr) {
                cargarHorasDisponibles(dateStr);
            }
        });
    }

    function mostrarPaso(index) {
        steps.forEach(step => step.classList.remove('activo'));
        pasoItems.forEach(item => item.classList.remove('activo'));

        steps[index].classList.add('activo');
        
        // Ilumina los pasos de la barra superior hasta el paso actual
        for(let i = 0; i <= index; i++) {
            pasoItems[i].classList.add('activo');
        }
    }

    // Navegación Adelante
    btnSiguiente.forEach(btn => {
        btn.addEventListener('click', () => {
            if (validarPasoActual()) {
                if (pasoActual < steps.length - 1) {
                    pasoActual++;
                    if (pasoActual === 4) {
                        generarResumenTicket();
                    }
                    mostrarPaso(pasoActual);
                }
            }
        });
    });

    // Navegación Atrás
    btnAtras.forEach(btn => {
        btn.addEventListener('click', () => {
            if (pasoActual > 0) {
                pasoActual--;
                mostrarPaso(pasoActual);
            }
        });
    });

    // Validar que se haya escogido la opción requerida antes de avanzar
    function validarPasoActual() {
        if (pasoActual === 0) {
            const servicio = document.querySelector('input[name="servicio_id"]:checked');
            if (!servicio) {
                alert("Por favor, selecciona un servicio.");
                return false;
            }
            const card = servicio.closest('.servicio-card');
            servicioSeleccionadoText = card.querySelector('h3').innerText;
            servicioPrecio = card.querySelector('.precio').innerText;
        }
        
        if (pasoActual === 1) {
            const barbero = document.querySelector('input[name="barbero_id"]:checked');
            if (!barbero) {
                alert("Por favor, selecciona un barbero.");
                return false;
            }
            barberoSeleccionadoText = barbero.closest('.barbero-card').querySelector('h3').innerText;
        }

        if (pasoActual === 2) {
            if (!document.getElementById('datepicker').value) {
                alert("Por favor, selecciona una fecha en el calendario.");
                return false;
            }
            if (!horaSeleccionada) {
                alert("Por favor, elige una hora para tu cita.");
                return false;
            }
        }

        if (pasoActual === 3) {
            const nombre = document.querySelector('input[name="nombre"]').value.trim();
            const apellido = document.querySelector('input[name="apellido"]').value.trim();
            const telefono = document.querySelector('input[name="telefono"]').value.trim();
            if (!nombre || !apellido || !telefono) {
                alert("Por favor, rellena todos los campos obligatorios (*).");
                return false;
            }
        }

        return true;
    }

    // Rellenar dinámicamente las horas del Paso 3
    function cargarHorasDisponibles(fecha) {
        const horasGrid = document.getElementById('horas-grid');
        horasGrid.innerHTML = ''; // Limpiar
        horaSeleccionada = ""; // Resetear hora previa

        // Simulación de horas comerciales (aquí puedes hacer un fetch real a tu backend)
        const horasSimuladas = ["09:00", "09:45", "10:30", "11:15", "12:00", "16:00", "16:45", "17:30", "18:15", "19:00"];
        
        horasSimuladas.forEach(hora => {
            const div = document.createElement('div');
            div.className = 'hora-item';
            div.innerText = hora;
            div.addEventListener('click', function() {
                document.querySelectorAll('.hora-item').forEach(h => h.classList.remove('selected'));
                this.classList.add('selected');
                horaSeleccionada = this.innerText;
                
                // Creamos o actualizamos un input oculto para enviar la hora en el formulario
                let inputHora = document.getElementById('input-hora-hidden');
                if(!inputHora) {
                    inputHora = document.createElement('input');
                    inputHora.type = 'hidden';
                    inputHora.name = 'hora';
                    inputHora.id = 'input-hora-hidden';
                    document.getElementById('form-reserva').appendChild(inputHora);
                }
                inputHora.value = horaSeleccionada;
            });
            horasGrid.appendChild(div);
        });
    }

    // Armar el resumen del paso 5
    function generarResumenTicket() {
        document.getElementById('resumen-servicio').innerText = servicioSeleccionadoText;
        document.getElementById('resumen-barbero').innerText = barberoSeleccionadoText;
        document.getElementById('resumen-fecha').innerText = document.getElementById('datepicker').value;
        document.getElementById('resumen-hora').innerText = horaSeleccionada;
        document.getElementById('resumen-precio').innerText = servicioPrecio;
    }

    // Filtrado de barberos según el servicio (mantenemos tu lógica existente adaptada)
    const radioServicios = document.querySelectorAll('input[name="servicio_id"]');
    radioServicios.forEach(radio => {
        radio.addEventListener('change', function () {
            const servicioSeleccionado = parseInt(this.value);
            const tarjetasBarberos = document.querySelectorAll('.barbero-card');

            tarjetasBarberos.forEach(tarjeta => {
                const barberoId = parseInt(tarjeta.getAttribute('data-id'));
                // Aquí puedes mapear mediante el array de relaciones PHP si fuese necesario ocultar o mostrar
                tarjeta.style.display = 'block'; 
            });
        });
    });

    // --- LÓGICA DE FILTRADO DE SERVICIOS POR CATEGORÍA ---
    const botonesFiltro = document.querySelectorAll('.filtro-btn');
    const tarjetasServicios = document.querySelectorAll('.servicio-card');

    botonesFiltro.forEach(boton => {
        boton.addEventListener('click', function() {
            // Cambiar clase activa entre botones
            botonesFiltro.forEach(btn => btn.classList.remove('activo'));
            this.classList.add('activo');

            const categoriaSeleccionada = this.getAttribute('data-categoria');

            tarjetasServicios.forEach(tarjeta => {
                const categoriaTarjeta = tarjeta.getAttribute('data-cat');

                if (categoriaSeleccionada === 'todos' || categoriaTarjeta === categoriaSeleccionada) {
                    tarjeta.style.display = 'block';
                    // Pequeña animación de entrada
                    tarjeta.style.opacity = '0';
                    setTimeout(() => {
                        tarjeta.style.opacity = '1';
                    }, 50);
                } else {
                    tarjeta.style.display = 'none';
                    // Desmarcar el radio button si el servicio queda oculto para evitar errores de selección invisibles
                    const radioInput = tarjeta.querySelector('input[type="radio"]');
                    if (radioInput && radioInput.checked) {
                        radioInput.checked = false;
                    }
                }
            });
        });
    });
});