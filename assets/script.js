// 1. Control del Header al hacer Scroll
// 
window.addEventListener('scroll', () => {
    const header = document.querySelector('.main-header');
    if (!header) return; // Evita errores en páginas que no tengan este header (ej. panel admin)
    
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


// 3. Reservas: filtros, calendario y selección de horas
document.addEventListener('DOMContentLoaded', function () {
    // FIX: Si no existe el contenedor de reservas en la página actual, detenemos la ejecución de este bloque.
    // Esto evita que el panel de administrador (o cualquier otra vista) sea secuestrado por el history.replaceState
    if (!document.querySelector('.reserva-step')) return;

    // Datos de horarios por día, inyectados desde PHP en reserva.php
    const scheduleData = window.reservaScheduleData || [];
    const dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const horariosPorDia = scheduleData.reduce((map, horario) => {
        map[horario.dia_semana] = horario;
        return map;
    }, {});

    // Elementos del DOM relacionados con la reserva
    const servicios = document.querySelectorAll('input[name="servicio_id"]');
    const barberos = document.querySelectorAll('.barbero-card');
    const steps = document.querySelectorAll('.reserva-step');
    const pasosBar = document.querySelectorAll('.paso-item');
    const btnSiguiente = document.querySelectorAll('.btn-siguiente');
    const btnAtras = document.querySelectorAll('.btn-atras');
    const datepickerInput = document.getElementById('datepicker');
    const horasGrid = document.getElementById('horas-grid');
    const horaHidden = document.getElementById('hora-seleccionada');
    const trackServicio = document.getElementById('track-servicio');
    const trackBarbero = document.getElementById('track-barbero');
    const trackCita = document.getElementById('track-cita');

    let pasoActual = 0;
    let servicioTexto = "";
    let barberoTexto = "";
    let horaSeleccionada = null;
    let servicioDuracionMinutos = 30;
    const isReservationSuccess = window.location.search.includes('reserva=ok');

    const btnSiguienteStep1 = steps[0]?.querySelector('.btn-siguiente');
    const btnSiguienteStep2 = steps[1]?.querySelector('.btn-siguiente');
    const btnSiguienteStep3 = steps[2]?.querySelector('.btn-siguiente');

    const initialState = { step: pasoActual, reservationComplete: isReservationSuccess };
    history.replaceState(initialState, '', window.location.pathname + window.location.search);

    if (isReservationSuccess) {
        sessionStorage.setItem('reservaCompletada', '1');
    } else {
        sessionStorage.removeItem('reservaCompletada');
    }

    function mostrarPaso(index) {
        steps.forEach(step => step.classList.remove('activo'));
        pasosBar.forEach(paso => paso.classList.remove('activo'));

        if (steps[index]) {
            steps[index].classList.add('activo');
        }
        if (pasosBar[index]) {
            pasosBar[index].classList.add('activo');
        }

        if (index === 2 && btnSiguienteStep3) {
            btnSiguienteStep3.disabled = !horaHidden.value;
            if (horaHidden.value) {
                btnSiguienteStep3.classList.remove('deshabilitado');
            } else {
                btnSiguienteStep3.classList.add('deshabilitado');
            }
        }
    }

    function setPaso(index, pushState = true) {
        pasoActual = index;
        mostrarPaso(index);
        if (pushState && !isReservationSuccess) {
            history.pushState({ step: pasoActual }, '', window.location.pathname + window.location.search);
        }
    }

    window.addEventListener('popstate', event => {
        if (event.state && typeof event.state.step === 'number') {
            if (sessionStorage.getItem('reservaCompletada')) {
                window.location.href = '/';
                return;
            }
            setPaso(event.state.step, false);
        }
    });

    function parseTimeToMinutes(time) {
        const [hours, minutes] = time.split(':').map(Number);
        return hours * 60 + minutes;
    }

    function formatMinutesToTime(value) {
        const hours = Math.floor(value / 60).toString().padStart(2, '0');
        const minutes = (value % 60).toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    function getDayName(date) {
        return dayNames[date.getDay()];
    }

    function isDateEnabled(date) {
        const horario = horariosPorDia[getDayName(date)];
        if (!horario) {
            return false;
        }
        return !['true', '1', 1, true].includes(horario.cerrado);
    }

    function clearHorasGrid(message) {
        horasGrid.innerHTML = `<p class="select-date-msg">${message}</p>`;
    }

    function resetSelectedHour() {
        horaSeleccionada = null;
        if (horaHidden) {
            horaHidden.value = '';
        }
        if (btnSiguienteStep3) {
            btnSiguienteStep3.disabled = true;
            btnSiguienteStep3.classList.add('deshabilitado');
        }
        document.querySelectorAll('.hora-item.selected').forEach(btn => btn.classList.remove('selected'));
    }

    function renderHorasDisponibles(date) {
        resetSelectedHour();

        if (!date) {
            clearHorasGrid('Por favor, selecciona una fecha primero.');
            return;
        }

        const diaNombre = getDayName(date);
        const horario = horariosPorDia[diaNombre];

        if (!horario) {
            clearHorasGrid('No hay horario disponible para ese día.');
            return;
        }

        if (['true', '1', 1, true].includes(horario.cerrado)) {
            clearHorasGrid('La barbería está cerrada ese día.');
            return;
        }

        const aperturaMin = parseTimeToMinutes(horario.hora_apertura);
        const cierreMin = parseTimeToMinutes(horario.hora_cierre);
        const duracion = 30;
        const slots = [];
        const hoy = new Date();
        const esHoy = date.toDateString() === hoy.toDateString();
        const corteHoy = esHoy ? hoy.getHours() * 60 + hoy.getMinutes() : 0;

        for (let minuto = aperturaMin; minuto + duracion <= cierreMin; minuto += 30) {
            if (esHoy && minuto <= corteHoy) {
                continue;
            }
            slots.push(formatMinutesToTime(minuto));
        }

        if (!slots.length) {
            clearHorasGrid('No hay horas disponibles para esa fecha con el servicio seleccionado.');
            return;
        }

        horasGrid.innerHTML = '';
        slots.forEach(hora => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'hora-item';
            button.textContent = hora;
            button.dataset.hora = hora;
            button.addEventListener('click', () => {
                horaSeleccionada = hora;
                if (horaHidden) {
                    horaHidden.value = hora;
                }
                document.querySelectorAll('.hora-item.selected').forEach(btn => btn.classList.remove('selected'));
                button.classList.add('selected');
                if (btnSiguienteStep3) {
                    btnSiguienteStep3.disabled = false;
                    btnSiguienteStep3.classList.remove('deshabilitado');
                }
                if (trackCita) {
                    trackCita.querySelector('span').innerText = `${datepickerInput.value} a las ${hora}`;
                    trackCita.classList.add('completado');
                }
            });
            horasGrid.appendChild(button);
        });
    }

    function validarPaso(index) {
        if (index === 0) {
            const servicioSeleccionado = document.querySelector('input[name="servicio_id"]:checked');
            if (!servicioSeleccionado) {
                return { valido: false, mensaje: 'Debes seleccionar un servicio para poder avanzar.' };
            }
        }
        if (index === 1) {
            const barberoSeleccionado = document.querySelector('input[name="barbero_id"]:checked');
            if (!barberoSeleccionado) {
                return { valido: false, mensaje: 'Por favor, selecciona un barbero antes de ir al siguiente paso.' };
            }
        }
        if (index === 2) {
            const fechaInput = datepickerInput ? datepickerInput.value : '';
            if (!fechaInput || !horaHidden || !horaHidden.value) {
                return { valido: false, mensaje: 'Selecciona una fecha y una de las horas disponibles.' };
            }
        }
        if (index === 3) {
            const nom = document.querySelector('input[name="nombre"]').value.trim();
            const ape = document.querySelector('input[name="apellido"]').value.trim();
            const tel = document.querySelector('input[name="telefono"]').value.trim();
            if (!nom || !ape || !tel) {
                return { valido: false, mensaje: 'Por favor, rellena todos los campos requeridos (*).' };
            }
        }
        return { valido: true, mensaje: '' };
    }

    // Genera el resumen que se muestra en el paso de confirmación
    function generarResumenTicket() {
        const resumenServicio = document.getElementById('resumen-servicio');
        const resumenBarbero = document.getElementById('resumen-barbero');
        const resumenFecha = document.getElementById('resumen-fecha');
        const resumenHora = document.getElementById('resumen-hora');
        const resumenPrecio = document.getElementById('resumen-precio');

        // Servicio
        const servicioSel = document.querySelector('input[name="servicio_id"]:checked');
        if (servicioSel) {
            const card = servicioSel.closest('.servicio-card');
            const nombre = card.querySelector('h3') ? card.querySelector('h3').innerText : '';
            const precioText = card.querySelector('.precio') ? card.querySelector('.precio').innerText : '-';
            resumenServicio.innerText = nombre ? `${nombre} (${precioText})` : '-';
            resumenPrecio.innerText = precioText || '-';
        } else {
            resumenServicio.innerText = '-';
            resumenPrecio.innerText = '-';
        }

        // Barbero
        const barberoSel = document.querySelector('input[name="barbero_id"]:checked');
        if (barberoSel) {
            const barberoCard = barberoSel.closest('.barbero-card');
            const nombreB = barberoCard ? (barberoCard.querySelector('h3') ? barberoCard.querySelector('h3').innerText : '') : '';
            resumenBarbero.innerText = nombreB || '-';
        } else {
            resumenBarbero.innerText = '-';
        }

        // Fecha y hora
        const fecha = datepickerInput ? datepickerInput.value : '';
        const hora = horaHidden ? horaHidden.value : '';
        resumenFecha.innerText = fecha || '-';
        resumenHora.innerText = hora || '-';
    }

    if (btnSiguienteStep1) {
        btnSiguienteStep1.disabled = true;
        btnSiguienteStep1.classList.add('deshabilitado');
    }
    if (btnSiguienteStep2) {
        btnSiguienteStep2.disabled = true;
        btnSiguienteStep2.classList.add('deshabilitado');
    }
    if (btnSiguienteStep3) {
        btnSiguienteStep3.disabled = true;
        btnSiguienteStep3.classList.add('deshabilitado');
    }

    servicios.forEach(input => {
        input.addEventListener('change', function () {
            const card = this.closest('.servicio-card');
            servicioTexto = card.querySelector('h3').innerText;
            servicioDuracionMinutos = Number(card.dataset.duracion || 30);
            const precio = card.querySelector('.precio').innerText;

            if (trackServicio) {
                trackServicio.querySelector('span').innerText = `${servicioTexto} (${precio})`;
                trackServicio.classList.add('completado');
            }

            if (btnSiguienteStep1) {
                btnSiguienteStep1.disabled = false;
                btnSiguienteStep1.classList.remove('deshabilitado');
            }

            if (datepickerInput && datepickerInput._flatpickr && datepickerInput.value) {
                const selectedDate = datepickerInput._flatpickr.selectedDates[0];
                renderHorasDisponibles(selectedDate);
            }
        });
    });

    barberos.forEach(card => {
        const radio = card.querySelector('input[name="barbero_id"]');
        if (!radio) return;

        radio.addEventListener('change', function () {
            barberoTexto = card.querySelector('h3').innerText;
            if (trackBarbero) {
                trackBarbero.querySelector('span').innerText = barberoTexto;
                trackBarbero.classList.add('completado');
            }
            if (btnSiguienteStep2) {
                btnSiguienteStep2.disabled = false;
                btnSiguienteStep2.classList.remove('deshabilitado');
            }
        });
    });

    servicios.forEach(servicio => {
        servicio.addEventListener('change', function () {
            const servicioSeleccionado = this.value;
            barberos.forEach(barbero => {
                const serviciosBarbero = (barbero.dataset.servicios || '').split(',').filter(Boolean);
                if (serviciosBarbero.includes(servicioSeleccionado)) {
                    barbero.style.display = 'flex';
                } else {
                    barbero.style.display = 'none';
                    const radio = barbero.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = false;
                    }
                }
            });
        });
    });

    if (datepickerInput && typeof flatpickr !== 'undefined') {
        flatpickr(datepickerInput, {
            locale: 'es',
            dateFormat: 'd/m/Y',
            minDate: 'today',
            disable: [date => !isDateEnabled(date)],
            onChange: function (selectedDates) {
                if (selectedDates.length) {
                    renderHorasDisponibles(selectedDates[0]);
                } else {
                    clearHorasGrid('Por favor, selecciona una fecha primero.');
                }
            }
        });
    }

    btnSiguiente.forEach(btn => {
        btn.addEventListener('click', () => {
            const validacion = validarPaso(pasoActual);
            if (!validacion.valido) {
                alert(validacion.mensaje);
                return;
            }

            if (pasoActual === 2 && trackCita) {
                const fecha = datepickerInput ? datepickerInput.value : '';
                trackCita.querySelector('span').innerText = `${fecha} a las ${horaSeleccionada}`;
                trackCita.classList.add('completado');
            }

            if (pasoActual < steps.length - 1) {
                setPaso(pasoActual + 1);
                if (pasoActual === 4 && typeof generarResumenTicket === 'function') {
                    generarResumenTicket();
                }
            }
        });
    });

    btnAtras.forEach(btn => {
        btn.addEventListener('click', () => {
            if (pasoActual > 0) {
                setPaso(pasoActual - 1);
            }
        });
    });

    const botonesFiltro = document.querySelectorAll('.filtro-btn');
    const tarjetasServicios = document.querySelectorAll('.servicio-card');

    botonesFiltro.forEach(boton => {
        boton.addEventListener('click', function () {
            botonesFiltro.forEach(btn => btn.classList.remove('activo'));
            this.classList.add('activo');
            const categoriaSeleccionada = this.getAttribute('data-categoria');
            tarjetasServicios.forEach(tarjeta => {
                const categoriaTarjeta = tarjeta.getAttribute('data-cat');
                if (categoriaSeleccionada === 'todos' || categoriaTarjeta === categoriaSeleccionada) {
                    tarjeta.style.display = 'block';
                    tarjeta.style.opacity = '0';
                    setTimeout(() => { tarjeta.style.opacity = '1'; }, 50);
                } else {
                    tarjeta.style.display = 'none';
                    const radioInput = tarjeta.querySelector('input[type="radio"]');
                    if (radioInput && radioInput.checked) {
                        radioInput.checked = false;
                        if (trackServicio) {
                            trackServicio.querySelector('span').innerText = 'Ninguno seleccionado';
                            trackServicio.classList.remove('completado');
                        }
                    }
                }
            });
        });
    });
});

// Funcionalidad admin: seleccionar todas las reservas y control botón eliminar
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const eliminarBtn = document.querySelector('button[name="eliminar_seleccionadas"]');

    function updateEliminarBtn() {
        if (!eliminarBtn) return;
        const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
        eliminarBtn.disabled = !anyChecked;
        eliminarBtn.style.opacity = anyChecked ? '1' : '0.6';
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateEliminarBtn();
        });
    }

    rowCheckboxes.forEach(cb => cb.addEventListener('change', function () {
        if (!selectAll) return;
        const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
        selectAll.checked = allChecked;
        updateEliminarBtn();
    }));

    updateEliminarBtn();
});

// Hamburger menu toggle for client views
document.addEventListener('DOMContentLoaded', function () {
    const hambBtn = document.getElementById('hamburger-btn');
    const hambMenu = document.getElementById('hamburger-menu');

    if (!hambBtn || !hambMenu) return;

    function closeMenu() {
        hambMenu.classList.remove('open');
        hambBtn.classList.remove('open');
        hambBtn.setAttribute('aria-expanded', 'false');
        hambMenu.setAttribute('aria-hidden', 'true');
    }

    function openMenu() {
        hambMenu.classList.add('open');
        hambBtn.classList.add('open');
        hambBtn.setAttribute('aria-expanded', 'true');
        hambMenu.setAttribute('aria-hidden', 'false');
    }

    hambBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (hambMenu.classList.contains('open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', function (e) {
        if (!hambMenu.contains(e.target) && !hambBtn.contains(e.target)) {
            closeMenu();
        }
    });

    // Close on escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
});

// Admin sidebar toggle (for tablet/mobile)
document.addEventListener('DOMContentLoaded', function () {
    const adminBtn = document.getElementById('admin-hamburger-btn');
    const adminSidebar = document.querySelector('.admin-sidebar');

    if (!adminBtn || !adminSidebar) return;

    // Prevent double-initialization if script loaded/executed multiple times
    if (window.adminHamburgerInitialized) return;
    window.adminHamburgerInitialized = true;

    // create overlay element if not present
    let overlay = document.querySelector('.admin-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'admin-overlay';
        document.body.appendChild(overlay);
    }

    function openAdmin() {
        adminSidebar.classList.add('open');
        adminBtn.setAttribute('aria-expanded', 'true');
        overlay.classList.add('show');
    }

    function closeAdmin() {
        adminSidebar.classList.remove('open');
        adminBtn.setAttribute('aria-expanded', 'false');
        overlay.classList.remove('show');
    }

    adminBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (adminSidebar.classList.contains('open')) closeAdmin(); else openAdmin();
    });

    overlay.addEventListener('click', function () { closeAdmin(); });

    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAdmin(); });
});

// Ensure admin overlay and sidebar state reset on window resize (desktop)
window.addEventListener('resize', function () {
    const adminSidebar = document.querySelector('.admin-sidebar');
    const overlay = document.querySelector('.admin-overlay');
    if (!adminSidebar) return;
    if (window.innerWidth > 1024) {
        adminSidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
        const adminBtn = document.getElementById('admin-hamburger-btn');
        if (adminBtn) adminBtn.setAttribute('aria-expanded', 'false');
    }
});

// Si el script se carga después de que el DOM ya esté listo, disparar el evento
// para que los listeners que se agregan en este archivo se ejecuten igualmente.
if (document.readyState !== 'loading') {
    document.dispatchEvent(new Event('DOMContentLoaded'));
}