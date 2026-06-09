<footer class="footer">

    <section class="footer-container">

        <!-- IZQUIERDA: MAPA -->
        <section class="footer-lado">
            <h4>Ubicación</h4>
            <iframe 
                class="mapa"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2982.121807280215!2d-0.88832412344782!3d41.6314984808173!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd5915c8fb1ab363%3A0x46e27ebc29b9dad!2sBarberia%20Catracha!5e0!3m2!1ses!2ses!4v1777547127456!5m2!1ses!2ses"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </section>

        <!-- CENTRO -->
        <section class="footer-center">

            <section class="footer-col">
                <h4>Horario</h4>
                <p>Lunes: 10:00 - 20:30</p>
                <p>Martes - Sábado: 09:30 - 20:30</p>
                <p>Domingo: 10:00 - 13:30</p>
            </section>

            <section class="footer-col">
                <h4>Contacto</h4>
                <p>Zaragoza</p>
                <p>C. de Fray Julián Garcés, 3-5</p>
                <p>Tel: 876 719 599</p>
            </section>

            <section class="footer-col">
                <h4>Enlaces</h4>
                <a href="https://wa.me/34630846042" target="_blank">WhatsApp</a>
                <a href="https://www.instagram.com/barberia.catracha/" target="_blank">Instagram</a>
                <a href="https://www.facebook.com/rosvinjesusa/" target="_blank">Facebook</a>
            </section>

            <section class="footer-col">
                <h4>Políticas web</h4>
                <a href="/barberia_catracha/api/politicaweb/avisolegal.php">Aviso legal</a>
                <a href="/barberia_catracha/api/politicaweb/politicaprivacidad.php">Política de privacidad</a>
                <a href="/barberia_catracha/api/politicaweb/politicacookies.php">Política de cookies</a>
            </section>

        </section>

        <!-- DERECHA: LOGO -->
        <section class="footer-lado footer-logo">
            <img src="<?= BASE_PATH ?>/assets/img/logo.png" alt="logo barberia">
        </section>

    </section>

    <!-- PARTE INFERIOR -->
    <section class="footer-bottom">
    <p><a href="../login.php" class="footer-login">© </a>2026 Barbería Catracha</p>
    </section>

</footer>

<!-- BANNER DE CONSENTIMIENTO DE COOKIES -->
<div id="cookie-banner" class="cookie-banner">
    <div class="cookie-banner-texto">
        <h4>Uso de cookies</h4>
        <p>
            Utilizamos cookies técnicas necesarias para el funcionamiento de las reservas online,
            cookies de análisis para mejorar tu experiencia, y cookies de terceros
            (Google Maps, Instagram, Google Reviews) para mostrar contenido externo.
            Puedes consultar más información en nuestra
            <a href="<?= BASE_PATH ?>/api/politicaweb/politicacookies.php" target="_blank">Política de Cookies</a>.
        </p>
    </div>
    <div class="cookie-banner-botones">
        <button type="button" id="cookie-permitir-todas" class="cookie-btn cookie-btn-dorado">Permitir todas</button>
        <button type="button" id="cookie-solo-necesarias" class="cookie-btn cookie-btn-borde">Solo necesarias</button>
        <button type="button" id="cookie-bloquear-todas" class="cookie-btn cookie-btn-borde">Bloquear todas</button>
    </div>
</div>

<script>window.BASE_PATH = '<?= BASE_PATH ?>';</script>
<script src="<?= BASE_PATH ?>/assets/script.js"></script>