// ==========================================================================
// 1. CONTROL DEL HEADER AL HACER SCROLL (Efecto Glassmorphism / Cristal)
// ==========================================================================
window.addEventListener('scroll', () => {
    const header = document.querySelector('.main-header');
    
    // CONTROL DE SEGURIDAD: Si la página actual no tiene este header (ej. panel admin), 
    // detenemos la ejecución para que no lance un error en la consola.
    if (!header) return; 
    
    // Si el usuario ha bajado más de 100px desde el tope de la página
    if (window.scrollY > 100) {
        header.style.background = 'rgba(10, 10, 10, 0.95)'; // Fondo oscuro semi-transparente
        header.style.height = '70px';                         // Reduce la altura para ganar espacio visual
        header.style.backdropFilter = 'blur(10px)';          // Efecto de desenfoque moderno (estilo Apple)
    } else {
        // Si el usuario regresa al tope de la página, restablece el diseño original
        header.style.background = '#0a0a0a';
        header.style.height = '80px';
        header.style.backdropFilter = 'none';
    }
});

// ==========================================================================
// 2. ANIMACIÓN DE REVELADO PARA LAS TARJETAS DE SERVICIO (Scroll Suave)
// ==========================================================================
// Configuramos el observador: la animación se activará cuando el 20% (0.2) de la tarjeta sea visible
const observerOptions = { threshold: 0.2 };

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        // Si la tarjeta entra en el campo de visión del usuario
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';          // Se vuelve completamente visible
            entry.target.style.transform = 'translateY(0)'; // Regresa a su posición vertical original
        }
    });
}, observerOptions);

// Preparamos e inicializamos todas las tarjetas antes de que empiece el scroll
document.querySelectorAll('.explore-card').forEach(card => {
    card.style.opacity = '0';                         // Inicialmente invisibles
    card.style.transform = 'translateY(30px)';        // Desplazadas 30px hacia abajo
    card.style.transition = 'all 0.6s ease-out';       // Transición suave de 0.6 segundos
    observer.observe(card);                           // Le decimos al Observer que vigile esta tarjeta
});


// ==========================================================================
// 3. SISTEMA DE RESERVAS: PASOS, FILTROS, CALENDARIO Y TURNOS HORARIOS
// ==========================================================================
document.addEventListener('DOMContentLoaded', function () {
    
    // CONTROL DE SEGURIDAD: Si no estamos en la página del formulario de reservas,
    // rompemos la ejecución. Esto evita que el código rompa el historial de otras páginas.
    if (!document.querySelector('.reserva-step')) return;

    // HORARIOS: Extraemos los datos inyectados por PHP desde el servidor en el objeto global de la ventana
    const scheduleData = window.reservaScheduleData || [];
    const dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    
    // Convertimos el Array de horarios de PHP en un Objeto indexado por el día de la semana para búsquedas rápidas
    const horariosPorDia = scheduleData.reduce((map, horario) => {
        map[horario.dia_semana] = horario;
        return map;
    }, {});

    // Captura de elementos del DOM esenciales para el flujo de la reserva
    const servicios = document.querySelectorAll('input[name="servicio_id"]');
    const barberos = document.querySelectorAll('.barbero-card');
    const steps = document.querySelectorAll('.reserva-step');
    const pasosBar = document.querySelectorAll('.paso-item');
    const btnSiguiente = document.querySelectorAll('.btn-siguiente');
    const btnAtras = document.querySelectorAll('.btn-atras');
    const datepickerInput = document.getElementById('datepicker');
    const horasGrid = document.getElementById('horas-grid');
    const horaHidden = document.getElementById('hora-seleccionada'); // Input oculto que se envía en el formulario
    
    // Elementos de la barra lateral de seguimiento (sidebar del ticket de progreso)
    const trackServicio = document.getElementById('track-servicio');
    const trackBarbero = document.getElementById('track-barbero');
    const trackCita = document.getElementById('track-cita');

    // Estado interno de la reserva actual
    let pasoActual = 0;
    let servicioTexto = "";
    let barberoTexto = "";
    let horaSeleccionada = null;
    let servicioDuracionMinutos = 30; // Duración por defecto si no se especifica otra
    const isReservationSuccess = window.location.search.includes('reserva=ok');

    // Capturamos los botones de avance específicos de cada paso para controlar su estado (activar/desactivar)
    const btnSiguienteStep1 = steps[0]?.querySelector('.btn-siguiente');
    const btnSiguienteStep2 = steps[1]?.querySelector('.btn-siguiente');
    const btnSiguienteStep3 = steps[2]?.querySelector('.btn-siguiente');

    // NAVEGACIÓN HISTORIAL: Forzamos el estado inicial en el historial del navegador para controlar el botón de "Atrás"
    const initialState = { step: pasoActual, reservationComplete: isReservationSuccess };
    history.replaceState(initialState, '', window.location.pathname + window.location.search);

    // Guardamos en la sesión si la reserva ya fue completada con éxito
    if (isReservationSuccess) {
        sessionStorage.setItem('reservaCompletada', '1');
    } else {
        sessionStorage.removeItem('reservaCompletada');
    }

    // Cambia visualmente el paso visible del formulario y actualiza la barra de progreso superior
    function mostrarPaso(index) {
        // 
        steps.forEach(step => step.classList.remove('activo'));
        
        pasosBar.forEach(paso => paso.classList.remove('activo'));

        //
        if (steps[index]) steps[index].classList.add('activo');
        if (pasosBar[index]) pasosBar[index].classList.add('activo');

        // Si estamos en el paso de Fecha/Hora (Paso 2), validamos si el botón siguiente debe estar activo o no
        if (index === 2 && btnSiguienteStep3) {
            btnSiguienteStep3.disabled = !horaHidden.value;
            if (horaHidden.value) {
                btnSiguienteStep3.classList.remove('deshabilitado');
            } else {
                btnSiguienteStep3.classList.add('deshabilitado');
            }
        }
    }

    // Cambia el paso activo y registra el movimiento en el historial del navegador para soportar navegación nativa
    function setPaso(index, pushState = true) {
        pasoActual = index;
        mostrarPaso(index);
        if (pushState && !isReservationSuccess) {
            history.pushState({ step: pasoActual }, '', window.location.pathname + window.location.search);
        }
    }

    // Escucha cuando el usuario hace clic en las flechas nativas de Atrás/Adelante del propio navegador web
    window.addEventListener('popstate', event => {
        if (event.state && typeof event.state.step === 'number') {
            // Si la reserva ya se guardó, bloqueamos el regreso y lo mandamos al home
            if (sessionStorage.getItem('reservaCompletada')) {
                window.location.href = '/';
                return;
            }
            setPaso(event.state.step, false); // Cambia el paso sin duplicar el historial
        }
    });

    // MATEMÁTICAS DE TIEMPO: Convierte "14:30" en minutos totales (14 * 60 + 30 = 870 minutos)
    function parseTimeToMinutes(time) {
        const [hours, minutes] = time.split(':').map(Number);
        return hours * 60 + minutes;
    }

    // MATEMÁTICAS DE TIEMPO: Convierte minutos totales de vuelta a formato legible (870 -> "14:30")
    function formatMinutesToTime(value) {
        const hours = Math.floor(value / 60).toString().padStart(2, '0');
        const minutes = (value % 60).toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    // Retorna el nombre del día en español según el objeto Date de JS
    function getDayName(date) {
        return dayNames[date.getDay()];
    }

    // Determina si una fecha está disponible para agendar comparando con los horarios comerciales
    function isDateEnabled(date) {
        const horario = horariosPorDia[getDayName(date)];
        if (!horario) return false; // Si no hay configuración para ese día, se bloquea
        return !['true', '1', 1, true].includes(horario.cerrado); // Retorna falso si está marcado como cerrado
    }

    // Limpia la cuadrícula de horas y coloca un mensaje informativo
    function clearHorasGrid(message) {
        horasGrid.innerHTML = `<p class="select-date-msg">${message}</p>`;
    }

    // Resetea por completo la hora seleccionada (limpieza de estado al cambiar de día)
    function resetSelectedHour() {
        horaSeleccionada = null;
        if (horaHidden) horaHidden.value = '';
        if (btnSiguienteStep3) {
            btnSiguienteStep3.disabled = true;
            btnSiguienteStep3.classList.add('deshabilitado');
        }
        document.querySelectorAll('.hora-item.selected').forEach(btn => btn.classList.remove('selected'));
    }

    // LÓGICA CENTRAL: Renderiza los bloques de horas disponibles en base al día seleccionado
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
        const duracion = 30; // Intervalo de bloques de turnos (30 mins fijado por diseño técnico)
        const slots = [];
        const hoy = new Date();
        const esHoy = date.toDateString() === hoy.toDateString();
        const corteHoy = esHoy ? hoy.getHours() * 60 + hoy.getMinutes() : 0;

        // Bucle para generar intervalos de tiempo desde la apertura hasta el cierre
        for (let minuto = aperturaMin; minuto + duracion <= cierreMin; minuto += 30) {
            // Filtro de seguridad: Si la fecha es HOY, ocultamos las horas que ya transcurrieron
            if (esHoy && minuto <= corteHoy) {
                continue;
            }
            slots.push(formatMinutesToTime(minuto));
        }

        if (!slots.length) {
            clearHorasGrid('No hay horas disponibles para esa fecha con el servicio seleccionado.');
            return;
        }

        // Limpiamos el contenedor y renderizamos los botones de hora dinámicamente
        horasGrid.innerHTML = '';
        slots.forEach(hora => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'hora-item';
            button.textContent = hora;
            button.dataset.hora = hora;
            
            // Evento al elegir una hora específica
            button.addEventListener('click', () => {
                horaSeleccionada = hora;
                if (horaHidden) horaHidden.value = hora;
                
                // Desmarcar hora previa y marcar la nueva
                document.querySelectorAll('.hora-item.selected').forEach(btn => btn.classList.remove('selected'));
                button.classList.add('selected');
                
                // Habilitamos el botón de continuar
                if (btnSiguienteStep3) {
                    btnSiguienteStep3.disabled = false;
                    btnSiguienteStep3.classList.remove('deshabilitado');
                }
                // Actualizamos el ticket lateral
                if (trackCita) {
                    trackCita.querySelector('span').innerText = `${datepickerInput.value} a las ${hora}`;
                    trackCita.classList.add('completado');
                }
            });
            horasGrid.appendChild(button);
        });
    }

    // INTERFAZ DE USUARIO: Reglas de validación antes de permitir avanzar de pantalla
    function validarPaso(index) {
        if (index === 0) { // Paso 1: Servicios
            const servicioSeleccionado = document.querySelector('input[name="servicio_id"]:checked');
            if (!servicioSeleccionado) {
                return { valido: false, mensaje: 'Debes seleccionar un servicio para poder avanzar.' };
            }
        }
        if (index === 1) { // Paso 2: Barberos
            const barberoSeleccionado = document.querySelector('input[name="barbero_id"]:checked');
            if (!barberoSeleccionado) {
                return { valido: false, mensaje: 'Por favor, selecciona un barbero antes de ir al siguiente paso.' };
            }
        }
        if (index === 2) { // Paso 3: Calendario
            const fechaInput = datepickerInput ? datepickerInput.value : '';
            if (!fechaInput || !horaHidden || !horaHidden.value) {
                return { valido: false, mensaje: 'Selecciona una fecha y una de las horas disponibles.' };
            }
        }
        if (index === 3) { // Paso 4: Datos del cliente (Formulario final)
            const nom = document.querySelector('input[name="nombre"]').value.trim();
            const ape = document.querySelector('input[name="apellido"]').value.trim();
            const tel = document.querySelector('input[name="telefono"]').value.trim();
            if (!nom || !ape || !tel) {
                return { valido: false, mensaje: 'Por favor, rellena todos los campos requeridos (*).' };
            }
        }
        return { valido: true, mensaje: '' };
    }

    // RESUMEN FINAL: Toma todos los datos seleccionados y genera el desglose tipo ticket de confirmación
    function generarResumenTicket() {
        const resumenServicio = document.getElementById('resumen-servicio');
        const resumenBarbero = document.getElementById('resumen-barbero');
        const resumenFecha = document.getElementById('resumen-fecha');
        const resumenHora = document.getElementById('resumen-hora');
        const resumenPrecio = document.getElementById('resumen-precio');

        // Procesar servicio seleccionado
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

        // Procesar barbero seleccionado
        const barberoSel = document.querySelector('input[name="barbero_id"]:checked');
        if (barberoSel) {
            const barberoCard = barberoSel.closest('.barbero-card');
            const nombreB = barberoCard ? (barberoCard.querySelector('h3') ? barberoCard.querySelector('h3').innerText : '') : '';
            resumenBarbero.innerText = nombreB || '-';
        } else {
            resumenBarbero.innerText = '-';
        }

        // Procesar fecha y hora elegidas
        const fecha = datepickerInput ? datepickerInput.value : '';
        const hora = horaHidden ? horaHidden.value : '';
        resumenFecha.innerText = fecha || '-';
        resumenHora.innerText = hora || '-';
    }

    // Inicializamos deshabilitados los botones de pasos para forzar la selección obligatoria del usuario
    if (btnSiguienteStep1) { btnSiguienteStep1.disabled = true; btnSiguienteStep1.classList.add('deshabilitado'); }
    if (btnSiguienteStep2) { btnSiguienteStep2.disabled = true; btnSiguienteStep2.classList.add('deshabilitado'); }
    if (btnSiguienteStep3) { btnSiguienteStep3.disabled = true; btnSiguienteStep3.classList.add('deshabilitado'); }

    // EVENTO: Control de cambio de selección en los inputs de Servicio
    servicios.forEach(input => {
        input.addEventListener('change', function () {
            const card = this.closest('.servicio-card');
            servicioTexto = card.querySelector('h3').innerText;
            servicioDuracionMinutos = Number(card.dataset.duracion || 30);
            const precio = card.querySelector('.precio').innerText;

            // Actualiza la barra lateral de progreso de reserva
            if (trackServicio) {
                trackServicio.querySelector('span').innerText = `${servicioTexto} (${precio})`;
                trackServicio.classList.add('completado');
            }

            // Habilita el avance al Paso 2
            if (btnSiguienteStep1) {
                btnSiguienteStep1.disabled = false;
                btnSiguienteStep1.classList.remove('deshabilitado');
            }

            // Si el usuario cambia el servicio teniendo ya una fecha elegida, recalculamos las horas
            if (datepickerInput && datepickerInput._flatpickr && datepickerInput.value) {
                const selectedDate = datepickerInput._flatpickr.selectedDates[0];
                renderHorasDisponibles(selectedDate);
            }
        });
    });

    // EVENTO: Control de cambio de selección de Barberos
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

    // FILTRO DINÁMICO: Oculta o muestra barberos en base a las habilidades/servicios admitidos
    servicios.forEach(servicio => {
        servicio.addEventListener('change', function () {
            const servicioSeleccionado = this.value;
            barberos.forEach(barbero => {
                const serviciosBarbero = (barbero.dataset.servicios || '').split(',').filter(Boolean);
                
                if (serviciosBarbero.includes(servicioSeleccionado)) {
                    barbero.style.display = 'flex'; // Muestra el barbero calificado
                } else {
                    barbero.style.display = 'none'; // Oculta al barbero que no realiza este servicio
                    const radio = barbero.querySelector('input[type="radio"]');
                    if (radio) radio.checked = false; // Deselecciona si estaba marcado internamente
                }
            });
        });
    });

    // CONFIGURACIÓN DE FLATPICKR (Librería del Calendario Dinámico)
    if (datepickerInput && typeof flatpickr !== 'undefined') {
        flatpickr(datepickerInput, {
            locale: 'es',             // Calendario traducido al español
            dateFormat: 'd/m/Y',      // Formato visual europeo de fecha
            minDate: 'today',         // Bloquea días pasados
            disable: [date => !isDateEnabled(date)], // Función que evalúa si el día de la semana está cerrado
            onChange: function (selectedDates) {
                if (selectedDates.length) {
                    renderHorasDisponibles(selectedDates[0]); // Renderiza horas disponibles del día elegido
                } else {
                    clearHorasGrid('Por favor, selecciona una fecha primero.');
                }
            }
        });
    }

    // EVENTO: Botón genérico Siguiente
    btnSiguiente.forEach(btn => {
        btn.addEventListener('click', () => {
            const validacion = validarPaso(pasoActual);
            if (!validacion.valido) {
                alert(validacion.mensaje); // Detiene el avance si rompe las reglas
                return;
            }

            if (pasoActual === 2 && trackCita) {
                const fecha = datepickerInput ? datepickerInput.value : '';
                trackCita.querySelector('span').innerText = `${fecha} a las ${horaSeleccionada}`;
                trackCita.classList.add('completado');
            }

            if (pasoActual < steps.length - 1) {
                setPaso(pasoActual + 1);
                // Si llegamos al paso final, disparamos la renderización del ticket de resumen
                if (pasoActual === 4 && typeof generarResumenTicket === 'function') {
                    generarResumenTicket();
                }
            }
        });
    });

    // EVENTO: Botón genérico Atrás
    btnAtras.forEach(btn => {
        btn.addEventListener('click', () => {
            if (pasoActual > 0) {
                setPaso(pasoActual - 1);
            }
        });
    });

    // FILTRO POR CATEGORÍAS (UI de Servicios: Cortes, Barba, Tintes, etc.)
    const botonesFiltro = document.querySelectorAll('.filtro-btn');
    const tarjetasServicios = document.querySelectorAll('.servicio-card');

    botonesFiltro.forEach(boton => {
        // Al hacer clic en un botón de filtro, actualizamos la interfaz para mostrar solo las tarjetas que correspondan a la categoría seleccionada
        boton.addEventListener('click', function () {
            botonesFiltro.forEach(btn => btn.classList.remove('activo'));
            this.classList.add('activo');
            
            const categoriaSeleccionada = this.getAttribute('data-categoria');
            
            tarjetasServicios.forEach(tarjeta => {
                const categoriaTarjeta = tarjeta.getAttribute('data-cat');
                
                // Muestra todas las tarjetas o filtra por la categoría seleccionada
                if (categoriaSeleccionada === 'todos' || categoriaTarjeta === categoriaSeleccionada) {
                    tarjeta.style.display = 'block';
                    tarjeta.style.opacity = '0';
                    setTimeout(() => { tarjeta.style.opacity = '1'; }, 50); // Pequeña animación fade-in
                } else {
                    tarjeta.style.display = 'none'; // Esconde las tarjetas que no correspondan
                    
                    // Si el usuario oculta un servicio que ya tenía seleccionado, lo desmarcamos de forma limpia
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

// ==========================================================================
// 4. FUNCIONALIDAD PANEL ADMIN: SELECCIÓN MÚLTIPLE DE TABLAS Y ACCIONES
// ==========================================================================
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all'); // Checkbox maestro de la cabecera
    const rowCheckboxes = document.querySelectorAll('.row-checkbox'); // Checkboxes individuales de filas
    const eliminarBtn = document.querySelector('button[name="eliminar_seleccionadas"]');

    // Habilita o deshabilita el botón masivo de "Eliminar" según existan registros marcados
    function updateEliminarBtn() {
        if (!eliminarBtn) return;
        const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked); // ¿Hay al menos uno seleccionado?
        eliminarBtn.disabled = !anyChecked;
        eliminarBtn.style.opacity = anyChecked ? '1' : '0.6'; // Feedback visual transparente si está inactivo
    }

    // Al cambiar el estado de la casilla maestra, iguala el estado de todas las filas individuales
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateEliminarBtn();
        });
    }

    // Al cambiar cualquier fila individual, verifica si todas están seleccionadas para activar el control maestro
    rowCheckboxes.forEach(cb => cb.addEventListener('change', function () {
        if (!selectAll) return;
        const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
        selectAll.checked = allChecked;
        updateEliminarBtn();
    }));

    updateEliminarBtn(); // Inicialización en carga de página
});

// ==========================================================================
// 5. MENÚ HAMBURGUESA MÓVIL (Vistas del Cliente)
// ==========================================================================
document.addEventListener('DOMContentLoaded', function () {
    const hambBtn = document.getElementById('hamburger-btn');
    const hambMenu = document.getElementById('hamburger-menu');

    if (!hambBtn || !hambMenu) return;

    // Cierra el menú móvil de manera limpia aplicando accesibilidad ARIA
    function closeMenu() {
        hambMenu.classList.remove('open');
        hambBtn.classList.remove('open');
        hambBtn.setAttribute('aria-expanded', 'false');
        hambMenu.setAttribute('aria-hidden', 'true');
    }

    // Abre el menú móvil aplicando accesibilidad ARIA
    function openMenu() {
        hambMenu.classList.add('open');
        hambBtn.classList.add('open');
        hambBtn.setAttribute('aria-expanded', 'true');
        hambMenu.setAttribute('aria-hidden', 'false');
    }

    // Alternar apertura y cierre al presionar el botón de hamburguesa
    hambBtn.addEventListener('click', function (e) {
        e.stopPropagation(); // Evita que el evento "flote" al documento e inmediatamente cierre el menú
        if (hambMenu.classList.contains('open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // UX: Si el menú está abierto y el usuario hace clic fuera de él, se cierra automáticamente
    document.addEventListener('click', function (e) {
        if (!hambMenu.contains(e.target) && !hambBtn.contains(e.target)) {
            closeMenu();
        }
    });

    // Accesibilidad por teclado: Al pulsar la tecla Escape se cierra el menú lateral
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
});

// ==========================================================================
// 6. TOGGLE SIDEBAR DE ADMINISTRACIÓN (Adaptabilidad Tablets/Móviles)
// ==========================================================================
document.addEventListener('DOMContentLoaded', function () {
    const adminBtn = document.getElementById('admin-hamburger-btn');
    const adminSidebar = document.querySelector('.admin-sidebar');

    if (!adminBtn || !adminSidebar) return;

    // BANDERA DE CONTROL: Evita que si el script se carga dos veces, se clonen los Event Listeners
    if (window.adminHamburgerInitialized) return;
    window.adminHamburgerInitialized = true;

    // Crear dinámicamente un fondo oscuro (Overlay) si no existe ya en el HTML
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

    // Al hacer click en el fondo opaco (overlay), cerramos la barra lateral
    overlay.addEventListener('click', function () { closeAdmin(); });

    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAdmin(); });
});

// Mostrar notificaciones tipo toast
window.showToast = function(msg) {
    if (!msg) return;
    const toast = document.createElement('div');
    toast.className = 'toast-message';
    toast.textContent = msg;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
    }, 3000);
};

function initEquipoImagePreview() {
    document.querySelectorAll('input[type=file][name="foto"]').forEach(function(input) {
        const form = input.closest('form');
        let preview = form ? form.querySelector('img.preview-image') : null;

        if (!preview) {
            preview = document.createElement('img');
            preview.className = 'preview-image';
            if (input.parentNode) input.parentNode.appendChild(preview);
        }

        const hiddenUrl = form ? form.querySelector('input[type=hidden][name="foto_url"]') : null;
        if (hiddenUrl && hiddenUrl.value) {
            preview.src = hiddenUrl.value;
        } else if (!preview.src) {
            preview.src = '/barberia_catracha/assets/img/default-user.jpg';
        }

        input.addEventListener('change', function() {
            const file = input.files && input.files[0];
            if (!file) return;
            const url = URL.createObjectURL(file);

            const overlay = document.createElement('div');
            overlay.className = 'image-preview-modal-overlay';

            const box = document.createElement('div');
            box.className = 'image-preview-modal-box';

            const img = document.createElement('img');
            img.src = url;
            img.style.maxWidth = '200px';
            img.style.display = 'block';
            img.style.margin = '0 auto 12px';

            const msg = document.createElement('p');
            msg.textContent = '¿Está seguro que quiere subir esta imagen?';
            msg.style.margin = '0 0 12px';
            msg.style.fontWeight = '600';

            const btnAccept = document.createElement('button');
            btnAccept.type = 'button';
            btnAccept.textContent = 'Aceptar';
            btnAccept.style.marginRight = '8px';
            btnAccept.className = 'btn-save';

            const btnCancel = document.createElement('button');
            btnCancel.type = 'button';
            btnCancel.textContent = 'Cancelar';
            btnCancel.className = 'btn-cancel';

            box.appendChild(img);
            box.appendChild(msg);
            box.appendChild(btnAccept);
            box.appendChild(btnCancel);
            overlay.appendChild(box);
            document.body.appendChild(overlay);

            const previousSrc = preview.src;

            btnAccept.addEventListener('click', function() {
                preview.src = url;
                preview.style.display = 'block';
                document.body.removeChild(overlay);
            });

            btnCancel.addEventListener('click', function() {
                input.value = '';
                preview.src = previousSrc || '/barberia_catracha/assets/img/default-user.jpg';
                document.body.removeChild(overlay);
                URL.revokeObjectURL(url);
            });
        });
    });

    document.querySelectorAll('.btn-delete-photo').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const form = btn.closest('form');
            if (!form) return;
            const hidden = form.querySelector('input[type=hidden][name="foto_delete"]');
            const fileInput = form.querySelector('input[type=file][name="foto"]');
            const preview = form.querySelector('img.preview-image');
            if (hidden) hidden.value = '1';
            if (fileInput) fileInput.value = '';
            if (preview) preview.src = '/barberia_catracha/assets/img/default-user.jpg';
            showToast('Foto marcada para eliminar. Pulsa GUARDAR para confirmar.');
        });
    });

    const flashMessage = document.getElementById('flash-message');
    if (flashMessage && flashMessage.dataset.message) {
        showToast(flashMessage.dataset.message);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    initEquipoImagePreview();
});

// CONTROL DE RESPONSIVIDAD: Resetea el estado de los componentes de administración si se agranda la pantalla
window.addEventListener('resize', function () {
    const adminSidebar = document.querySelector('.admin-sidebar');
    const overlay = document.querySelector('.admin-overlay');
    if (!adminSidebar) return;
    
    // Si la pantalla supera los 1024px (escritorio), limpiamos el diseño móvil para que no colisionen
    if (window.innerWidth > 1024) {
        adminSidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
        const adminBtn = document.getElementById('admin-hamburger-btn');
        if (adminBtn) adminBtn.setAttribute('aria-expanded', 'false');
    }
});

// ==========================================================================
// 7. CIERRE DEFENSIVO: DISPARADOR MANUAL DE DOM READY
// ==========================================================================
// Si por razones de rendimiento de carga el script se ejecuta de manera asíncrona ('async' o 'defer')
// y entra en acción *después* de que el navegador procesó el HTML, disparamos el evento manualmente 
// para asegurar que absolutamente todos los EventListeners declarados arriba cobren vida.
if (document.readyState !== 'loading') {
    document.dispatchEvent(new Event('DOMContentLoaded'));
}