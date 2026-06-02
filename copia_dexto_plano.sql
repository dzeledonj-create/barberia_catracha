-- COMENTARIO PARA DEFENSA:
-- Copia SQL en texto plano para importar o revisar la estructura y datos.
-- Este archivo no se ejecuta en la web directamente; sirve como respaldo/importacion de la base de datos.
--
-- PostgreSQL database dump
--

\restrict 5aVHnAKibJCNZKrqQMa8EF6KdECNiylxUoGhRe2DAHu0C6wAw9454QxMsEa3erA

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

-- Started on 2026-05-22 13:09:26

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_table_access_method = heap;

--
-- TOC entry 235 (class 1259 OID 33106)
-- Name: barbero_servicio; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.barbero_servicio (
    barbero_id integer NOT NULL,
    servicio_id integer NOT NULL
);


--
-- TOC entry 222 (class 1259 OID 32999)
-- Name: barberos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.barberos (
    barbero_id integer NOT NULL,
    especialidad character varying(100),
    foto_url text,
    descripcion text,
    etiquetas character varying(255),
    usuario_id integer NOT NULL
);


--
-- TOC entry 221 (class 1259 OID 32998)
-- Name: barberos_barbero_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.barberos_barbero_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5135 (class 0 OID 0)
-- Dependencies: 221
-- Name: barberos_barbero_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.barberos_barbero_id_seq OWNED BY public.barberos.barbero_id;


--
-- TOC entry 230 (class 1259 OID 33065)
-- Name: blog_posts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.blog_posts (
    post_id integer NOT NULL,
    titulo character varying(255) NOT NULL,
    contenido text NOT NULL,
    imagen_url text,
    autor_id integer,
    fecha_publicacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    etiquetas character varying(100),
    instagram_embed text
);


--
-- TOC entry 229 (class 1259 OID 33064)
-- Name: blog_posts_post_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.blog_posts_post_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5136 (class 0 OID 0)
-- Dependencies: 229
-- Name: blog_posts_post_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.blog_posts_post_id_seq OWNED BY public.blog_posts.post_id;


--
-- TOC entry 220 (class 1259 OID 32983)
-- Name: clientes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.clientes (
    cliente_id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    apellido character varying(100) NOT NULL,
    telefono character varying(20) NOT NULL,
    email character varying(150),
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- TOC entry 219 (class 1259 OID 32982)
-- Name: clientes_cliente_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.clientes_cliente_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5137 (class 0 OID 0)
-- Dependencies: 219
-- Name: clientes_cliente_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.clientes_cliente_id_seq OWNED BY public.clientes.cliente_id;


--
-- TOC entry 239 (class 1259 OID 33152)
-- Name: datos_ubicacion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.datos_ubicacion (
    id integer NOT NULL,
    direccion character varying(255),
    telefono character varying(50),
    whatsapp character varying(50),
    mapa_embed text
);


--
-- TOC entry 238 (class 1259 OID 33151)
-- Name: datos_ubicacion_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.datos_ubicacion_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5138 (class 0 OID 0)
-- Dependencies: 238
-- Name: datos_ubicacion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.datos_ubicacion_id_seq OWNED BY public.datos_ubicacion.id;


--
-- TOC entry 234 (class 1259 OID 33099)
-- Name: horarios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.horarios (
    horario_id integer NOT NULL,
    dia_semana character varying(20),
    hora_apertura time without time zone,
    hora_cierre time without time zone,
    cerrado boolean DEFAULT false
);


--
-- TOC entry 233 (class 1259 OID 33098)
-- Name: horarios_horario_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.horarios_horario_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5139 (class 0 OID 0)
-- Dependencies: 233
-- Name: horarios_horario_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.horarios_horario_id_seq OWNED BY public.horarios.horario_id;


--
-- TOC entry 232 (class 1259 OID 33083)
-- Name: mural_sugerencias; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.mural_sugerencias (
    sugerencia_id integer NOT NULL,
    nombre_corte character varying(100),
    descripcion text,
    imagen_url text NOT NULL,
    estilo character varying(50),
    activo boolean DEFAULT true
);


--
-- TOC entry 231 (class 1259 OID 33082)
-- Name: mural_sugerencias_sugerencia_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.mural_sugerencias_sugerencia_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5140 (class 0 OID 0)
-- Dependencies: 231
-- Name: mural_sugerencias_sugerencia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.mural_sugerencias_sugerencia_id_seq OWNED BY public.mural_sugerencias.sugerencia_id;


--
-- TOC entry 228 (class 1259 OID 33050)
-- Name: pagos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pagos (
    pago_id integer NOT NULL,
    reserva_id integer,
    monto numeric(10,2) NOT NULL,
    metodo_pago character varying(50),
    estado_pago character varying(20),
    fecha_pago timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- TOC entry 227 (class 1259 OID 33049)
-- Name: pagos_pago_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pagos_pago_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5141 (class 0 OID 0)
-- Dependencies: 227
-- Name: pagos_pago_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pagos_pago_id_seq OWNED BY public.pagos.pago_id;


--
-- TOC entry 226 (class 1259 OID 33024)
-- Name: reservas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reservas (
    reserva_id integer NOT NULL,
    cliente_id integer,
    barbero_id integer,
    servicio_id integer,
    fecha_hora timestamp without time zone NOT NULL,
    estado character varying(20) DEFAULT 'pendiente'::character varying,
    creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- TOC entry 225 (class 1259 OID 33023)
-- Name: reservas_reserva_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.reservas_reserva_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5142 (class 0 OID 0)
-- Dependencies: 225
-- Name: reservas_reserva_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.reservas_reserva_id_seq OWNED BY public.reservas.reserva_id;


--
-- TOC entry 224 (class 1259 OID 33011)
-- Name: servicios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.servicios (
    servicio_id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    precio numeric(10,2) NOT NULL,
    duracion_minutos integer NOT NULL,
    categoria character varying(50)
);


--
-- TOC entry 223 (class 1259 OID 33010)
-- Name: servicios_servicio_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.servicios_servicio_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5143 (class 0 OID 0)
-- Dependencies: 223
-- Name: servicios_servicio_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.servicios_servicio_id_seq OWNED BY public.servicios.servicio_id;


--
-- TOC entry 237 (class 1259 OID 33124)
-- Name: usuarios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuarios (
    usuario_id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    email character varying(150) NOT NULL,
    password character varying(255) NOT NULL,
    rol character varying(20) NOT NULL,
    activo boolean DEFAULT true,
    CONSTRAINT usuarios_rol_check CHECK (((rol)::text = ANY ((ARRAY['admin'::character varying, 'barbero'::character varying])::text[])))
);


--
-- TOC entry 236 (class 1259 OID 33123)
-- Name: usuarios_usuario_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuarios_usuario_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5144 (class 0 OID 0)
-- Dependencies: 236
-- Name: usuarios_usuario_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuarios_usuario_id_seq OWNED BY public.usuarios.usuario_id;


--
-- TOC entry 4907 (class 2604 OID 33002)
-- Name: barberos barbero_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.barberos ALTER COLUMN barbero_id SET DEFAULT nextval('public.barberos_barbero_id_seq'::regclass);


--
-- TOC entry 4914 (class 2604 OID 33068)
-- Name: blog_posts post_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blog_posts ALTER COLUMN post_id SET DEFAULT nextval('public.blog_posts_post_id_seq'::regclass);


--
-- TOC entry 4905 (class 2604 OID 32986)
-- Name: clientes cliente_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clientes ALTER COLUMN cliente_id SET DEFAULT nextval('public.clientes_cliente_id_seq'::regclass);


--
-- TOC entry 4922 (class 2604 OID 33155)
-- Name: datos_ubicacion id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datos_ubicacion ALTER COLUMN id SET DEFAULT nextval('public.datos_ubicacion_id_seq'::regclass);


--
-- TOC entry 4918 (class 2604 OID 33102)
-- Name: horarios horario_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.horarios ALTER COLUMN horario_id SET DEFAULT nextval('public.horarios_horario_id_seq'::regclass);


--
-- TOC entry 4916 (class 2604 OID 33086)
-- Name: mural_sugerencias sugerencia_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mural_sugerencias ALTER COLUMN sugerencia_id SET DEFAULT nextval('public.mural_sugerencias_sugerencia_id_seq'::regclass);


--
-- TOC entry 4912 (class 2604 OID 33053)
-- Name: pagos pago_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pagos ALTER COLUMN pago_id SET DEFAULT nextval('public.pagos_pago_id_seq'::regclass);


--
-- TOC entry 4909 (class 2604 OID 33027)
-- Name: reservas reserva_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reservas ALTER COLUMN reserva_id SET DEFAULT nextval('public.reservas_reserva_id_seq'::regclass);


--
-- TOC entry 4908 (class 2604 OID 33014)
-- Name: servicios servicio_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.servicios ALTER COLUMN servicio_id SET DEFAULT nextval('public.servicios_servicio_id_seq'::regclass);


--
-- TOC entry 4920 (class 2604 OID 33127)
-- Name: usuarios usuario_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN usuario_id SET DEFAULT nextval('public.usuarios_usuario_id_seq'::regclass);


--
-- TOC entry 5125 (class 0 OID 33106)
-- Dependencies: 235
-- Data for Name: barbero_servicio; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.barbero_servicio VALUES (1, 1);
INSERT INTO public.barbero_servicio VALUES (1, 2);
INSERT INTO public.barbero_servicio VALUES (1, 3);
INSERT INTO public.barbero_servicio VALUES (1, 4);
INSERT INTO public.barbero_servicio VALUES (1, 5);
INSERT INTO public.barbero_servicio VALUES (1, 6);
INSERT INTO public.barbero_servicio VALUES (1, 7);
INSERT INTO public.barbero_servicio VALUES (1, 8);
INSERT INTO public.barbero_servicio VALUES (1, 9);
INSERT INTO public.barbero_servicio VALUES (1, 10);
INSERT INTO public.barbero_servicio VALUES (1, 11);
INSERT INTO public.barbero_servicio VALUES (1, 12);
INSERT INTO public.barbero_servicio VALUES (1, 13);
INSERT INTO public.barbero_servicio VALUES (2, 4);
INSERT INTO public.barbero_servicio VALUES (2, 3);
INSERT INTO public.barbero_servicio VALUES (2, 6);
INSERT INTO public.barbero_servicio VALUES (2, 2);
INSERT INTO public.barbero_servicio VALUES (2, 1);
INSERT INTO public.barbero_servicio VALUES (2, 10);
INSERT INTO public.barbero_servicio VALUES (2, 11);
INSERT INTO public.barbero_servicio VALUES (3, 4);
INSERT INTO public.barbero_servicio VALUES (3, 3);
INSERT INTO public.barbero_servicio VALUES (3, 2);
INSERT INTO public.barbero_servicio VALUES (3, 1);
INSERT INTO public.barbero_servicio VALUES (3, 10);
INSERT INTO public.barbero_servicio VALUES (3, 11);


--
-- TOC entry 5112 (class 0 OID 32999)
-- Dependencies: 222
-- Data for Name: barberos; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.barberos VALUES (1, 'Barbero profesional', 'assets/img/ross.jpeg', NULL, NULL, 2);
INSERT INTO public.barberos VALUES (2, 'Barbero profesional', 'assets/img/rolando.jpeg', NULL, NULL, 3);
INSERT INTO public.barberos VALUES (3, 'Barbero profesional', 'assets/img/luis.jpeg', NULL, NULL, 4);


--
-- TOC entry 5120 (class 0 OID 33065)
-- Dependencies: 230
-- Data for Name: blog_posts; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.blog_posts VALUES (9, 'Accesorios y Productos Premium', 'La barbería moderna va más allá del corte de cabello. Descubre los mejores accesorios y productos premium que todo caballero debe tener: peines de madera fina, brochas naturales, aceites esenciales, bálsamos para barba y herramientas especializadas. Cada producto está seleccionado por su calidad, durabilidad y contribución al ritual de cuidado personal.', 'assets/img/blog9.jpg', 3, '2026-05-05 13:30:48.385875', 'Accesorios, Premium, Productos', '
<iframe
src="https://www.instagram.com/p/DYTEpVfoDhH/embed"
width="100%"
height="500"
frameborder="0"
scrolling="no"
allowtransparency="true">
</iframe>
');
INSERT INTO public.blog_posts VALUES (10, '¿No sabes por dónde empezar?', 'Empieza por un buen corte. 
💈✂️😎✨💯
@barberia.catracha 
C/Fray Julián Garcés 3 (Torrero - Zaragoza)', NULL, NULL, '2026-05-14 13:22:22.029131', NULL, '
    <iframe
    src="https://www.instagram.com/reel/DWb94GboHAj/embed"
    width="100%"
    height="500"
    frameborder="0"
    scrolling="no"
    allowtransparency="true">
    </iframe>
    ');
INSERT INTO public.blog_posts VALUES (12, 'Transformación de corte', 'Transformación completa realizada en Barbería Catracha. Un trabajo enfocado en marcar estilo, limpiar líneas y conseguir un acabado moderno y cuidado.', NULL, 1, '2026-05-18 09:36:33.041433', 'CORTE', '<iframe src="https://www.instagram.com/p/DWFh-yACFPO/embed" width="100%" height="500" frameborder="0" scrolling="no" allowtransparency="true"></iframe>');
INSERT INTO public.blog_posts VALUES (13, 'Fade rizado con textura natural', 'Trabajo realizado combinando un fade limpio con rizos definidos y textura natural. Un estilo moderno que mantiene volumen arriba y un degradado preciso en laterales y nuca.', NULL, 1, '2026-05-18 09:41:43.93068', 'RIZOS, FADE, TEXTURA', '<iframe src="https://www.instagram.com/p/DQpnZMNCBa5/embed" width="100%" height="500" frameborder="0" scrolling="no" allowtransparency="true"></iframe>');
INSERT INTO public.blog_posts VALUES (14, 'Perfilado de barba premium', 'Perfilado y definición de barba realizado con precisión para conseguir un acabado limpio, elegante y profesional. Un servicio pensado para cuidar cada detalle del estilo masculino.', NULL, 1, '2026-05-18 09:45:03.126203', 'BARBA, PERFILADO', '
        <blockquote class="instagram-media"
        data-instgrm-permalink="https://www.instagram.com/p/CjJjMEHIhEc/"
        data-instgrm-version="14">
        </blockquote>
        ');


--
-- TOC entry 5110 (class 0 OID 32983)
-- Dependencies: 220
-- Data for Name: clientes; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.clientes VALUES (1, 'bravas', 'wifi', '645983674', 'Bravaslocas@gmail.com', '2026-05-22 11:00:40.453878');
INSERT INTO public.clientes VALUES (5, 'Douglas', 'wifi', '654738291', 'douglas@gmail.com', '2026-05-22 11:29:36.832808');
INSERT INTO public.clientes VALUES (6, 'Pablo', 'López', '+34 876 719 454', 'plopezm@campusdigitalfp.com', '2026-05-22 11:52:22.7266');
INSERT INTO public.clientes VALUES (7, 'NUNO', 'XU', '645780983', 'nuno@gmail.com', '2026-05-22 12:35:57.17581');
INSERT INTO public.clientes VALUES (8, 'Douglas', 'wifi', '674939303', 'dzeledonj@campusdigitalfp.com', '2026-05-22 12:38:04.759882');


--
-- TOC entry 5129 (class 0 OID 33152)
-- Dependencies: 239
-- Data for Name: datos_ubicacion; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.datos_ubicacion VALUES (1, 'C. de Fray Julián Garcés, 3-5, Local 2, 50007 Zaragoza', '+34 876 719 599', '+34 630 846 042', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2982.121807280215!2d-0.88832412344782!3d41.6314984808173!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd5915c8fb1ab363%3A0x46e27ebc29b9dad!2sBarberia%20Catracha!5e0!3m2!1ses!2ses!4v1778750232383!5m2!1ses!2ses" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade');


--
-- TOC entry 5124 (class 0 OID 33099)
-- Dependencies: 234
-- Data for Name: horarios; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.horarios VALUES (8, 'Lunes', '10:00:00', '19:30:00', false);
INSERT INTO public.horarios VALUES (9, 'Martes', '09:30:00', '20:30:00', false);
INSERT INTO public.horarios VALUES (10, 'Miércoles', '09:30:00', '20:30:00', false);
INSERT INTO public.horarios VALUES (11, 'Jueves', '09:30:00', '20:30:00', false);
INSERT INTO public.horarios VALUES (12, 'Viernes', '09:30:00', '20:30:00', false);
INSERT INTO public.horarios VALUES (13, 'Sábado', '09:30:00', '20:30:00', false);
INSERT INTO public.horarios VALUES (14, 'Domingo', '10:00:00', '13:30:00', false);


--
-- TOC entry 5122 (class 0 OID 33083)
-- Dependencies: 232
-- Data for Name: mural_sugerencias; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.mural_sugerencias VALUES (1, 'Fade bajo', 'Degradado bajo limpio y elegante.', 'assets/img/mural/fade-bajo.jpg', 'Fade', true);
INSERT INTO public.mural_sugerencias VALUES (2, 'Fade medio', 'Degradado medio moderno y versátil.', 'assets/img/mural/fade-medio.jpg', 'Fade', true);
INSERT INTO public.mural_sugerencias VALUES (3, 'Crop texturizado', 'Corte moderno con textura en la parte superior.', 'assets/img/mural/crop.jpg', 'Moderno', true);
INSERT INTO public.mural_sugerencias VALUES (4, 'Clásico con raya', 'Corte clásico con raya marcada.', 'assets/img/mural/clasico-raya.jpg', 'Clásico', true);
INSERT INTO public.mural_sugerencias VALUES (5, 'Buzz cut', 'Corte corto, práctico y fácil de mantener.', 'assets/img/mural/buzz-cut.jpg', 'Fade', true);


--
-- TOC entry 5118 (class 0 OID 33050)
-- Dependencies: 228
-- Data for Name: pagos; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 5116 (class 0 OID 33024)
-- Dependencies: 226
-- Data for Name: reservas; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.reservas VALUES (1, 5, 1, 7, '2026-05-22 12:00:00', 'confirmada', '2026-05-22 11:29:36.841288');
INSERT INTO public.reservas VALUES (2, 7, 2, 1, '2026-05-22 13:00:00', 'confirmada', '2026-05-22 12:35:57.186654');


--
-- TOC entry 5114 (class 0 OID 33011)
-- Dependencies: 224
-- Data for Name: servicios; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.servicios VALUES (4, 'Corte Y Cejas Con Hilo', 'Corte de pelo y cejas con hilo', 15.00, 40, NULL);
INSERT INTO public.servicios VALUES (5, 'Barba + tinte', 'Arreglo de barba con tinte', 12.00, 30, 'barba');
INSERT INTO public.servicios VALUES (6, 'Cejas con cuchilla', 'Diseño de cejas con cuchilla', 4.00, 5, 'cejas');
INSERT INTO public.servicios VALUES (7, 'Cejas con hilo', 'Diseño de cejas con hilo', 5.00, 15, 'cejas');
INSERT INTO public.servicios VALUES (8, 'Tinte negro', 'Tinte negro para el pelo', 10.00, 10, 'color');
INSERT INTO public.servicios VALUES (9, 'Tinte de colores', 'Tinte de colores', 50.00, 180, 'color');
INSERT INTO public.servicios VALUES (2, 'Corte y barba', 'Corte de pelo más arreglo de barba', 16.00, 45, 'corte_y_barba');
INSERT INTO public.servicios VALUES (12, 'Corte, barba y tinte De Pelo', 'Corte, barba y tinte de pelo', 25.00, 60, 'corte_y_barba');
INSERT INTO public.servicios VALUES (1, 'Corte', 'Corte de pelo clásico o moderno', 11.00, 30, 'corte');
INSERT INTO public.servicios VALUES (10, 'Corte para jubilados', 'Corte especial para jubilados', 10.00, 30, 'corte');
INSERT INTO public.servicios VALUES (11, 'Corte y diseño', 'Corte con diseño personalizado', 12.00, 35, 'corte');
INSERT INTO public.servicios VALUES (13, 'Permanente', 'Tratamiento de permanente', 50.00, 180, 'permanente');
INSERT INTO public.servicios VALUES (3, 'Barba', 'Arreglo de barba', 7.00, 15, 'barba');


--
-- TOC entry 5127 (class 0 OID 33124)
-- Dependencies: 237
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.usuarios VALUES (1, 'Administrador', 'admin@barberia.com', '1234', 'admin', true);
INSERT INTO public.usuarios VALUES (2, 'Ross', 'ross@barberia.com', '1234', 'barbero', true);
INSERT INTO public.usuarios VALUES (3, 'Rolando', 'rolando@barberia.com', '1234', 'barbero', true);
INSERT INTO public.usuarios VALUES (4, 'Luis', 'luis@barberia.com', '1234', 'barbero', true);
INSERT INTO public.usuarios VALUES (7, 'Nuevo Barbero Test', 'barbero_test_1779090341@barberia.com', '1234', 'barbero', true);
INSERT INTO public.usuarios VALUES (8, 'Nuevo Barbero Test', 'barbero_test@barberia.com', '1234', 'barbero', true);


--
-- TOC entry 5145 (class 0 OID 0)
-- Dependencies: 221
-- Name: barberos_barbero_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.barberos_barbero_id_seq', 3, true);


--
-- TOC entry 5146 (class 0 OID 0)
-- Dependencies: 229
-- Name: blog_posts_post_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.blog_posts_post_id_seq', 15, true);


--
-- TOC entry 5147 (class 0 OID 0)
-- Dependencies: 219
-- Name: clientes_cliente_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.clientes_cliente_id_seq', 8, true);


--
-- TOC entry 5148 (class 0 OID 0)
-- Dependencies: 238
-- Name: datos_ubicacion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.datos_ubicacion_id_seq', 1, true);


--
-- TOC entry 5149 (class 0 OID 0)
-- Dependencies: 233
-- Name: horarios_horario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.horarios_horario_id_seq', 14, true);


--
-- TOC entry 5150 (class 0 OID 0)
-- Dependencies: 231
-- Name: mural_sugerencias_sugerencia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.mural_sugerencias_sugerencia_id_seq', 5, true);


--
-- TOC entry 5151 (class 0 OID 0)
-- Dependencies: 227
-- Name: pagos_pago_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.pagos_pago_id_seq', 1, false);


--
-- TOC entry 5152 (class 0 OID 0)
-- Dependencies: 225
-- Name: reservas_reserva_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.reservas_reserva_id_seq', 2, true);


--
-- TOC entry 5153 (class 0 OID 0)
-- Dependencies: 223
-- Name: servicios_servicio_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.servicios_servicio_id_seq', 13, true);


--
-- TOC entry 5154 (class 0 OID 0)
-- Dependencies: 236
-- Name: usuarios_usuario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usuarios_usuario_id_seq', 9, true);


--
-- TOC entry 4947 (class 2606 OID 33112)
-- Name: barbero_servicio barbero_servicio_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.barbero_servicio
    ADD CONSTRAINT barbero_servicio_pkey PRIMARY KEY (barbero_id, servicio_id);


--
-- TOC entry 4931 (class 2606 OID 33009)
-- Name: barberos barberos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.barberos
    ADD CONSTRAINT barberos_pkey PRIMARY KEY (barbero_id);


--
-- TOC entry 4941 (class 2606 OID 33076)
-- Name: blog_posts blog_posts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blog_posts
    ADD CONSTRAINT blog_posts_pkey PRIMARY KEY (post_id);


--
-- TOC entry 4925 (class 2606 OID 32997)
-- Name: clientes clientes_email_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_email_key UNIQUE (email);


--
-- TOC entry 4927 (class 2606 OID 32993)
-- Name: clientes clientes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (cliente_id);


--
-- TOC entry 4929 (class 2606 OID 32995)
-- Name: clientes clientes_telefono_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_telefono_key UNIQUE (telefono);


--
-- TOC entry 4953 (class 2606 OID 33160)
-- Name: datos_ubicacion datos_ubicacion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datos_ubicacion
    ADD CONSTRAINT datos_ubicacion_pkey PRIMARY KEY (id);


--
-- TOC entry 4945 (class 2606 OID 33105)
-- Name: horarios horarios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.horarios
    ADD CONSTRAINT horarios_pkey PRIMARY KEY (horario_id);


--
-- TOC entry 4943 (class 2606 OID 33093)
-- Name: mural_sugerencias mural_sugerencias_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mural_sugerencias
    ADD CONSTRAINT mural_sugerencias_pkey PRIMARY KEY (sugerencia_id);


--
-- TOC entry 4939 (class 2606 OID 33058)
-- Name: pagos pagos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_pkey PRIMARY KEY (pago_id);


--
-- TOC entry 4937 (class 2606 OID 33033)
-- Name: reservas reservas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_pkey PRIMARY KEY (reserva_id);


--
-- TOC entry 4935 (class 2606 OID 33022)
-- Name: servicios servicios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.servicios
    ADD CONSTRAINT servicios_pkey PRIMARY KEY (servicio_id);


--
-- TOC entry 4933 (class 2606 OID 33169)
-- Name: barberos unique_usuario_en_barberos; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.barberos
    ADD CONSTRAINT unique_usuario_en_barberos UNIQUE (usuario_id);


--
-- TOC entry 4949 (class 2606 OID 33140)
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- TOC entry 4951 (class 2606 OID 33138)
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (usuario_id);


--
-- TOC entry 4960 (class 2606 OID 33113)
-- Name: barbero_servicio barbero_servicio_barbero_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.barbero_servicio
    ADD CONSTRAINT barbero_servicio_barbero_id_fkey FOREIGN KEY (barbero_id) REFERENCES public.barberos(barbero_id);


--
-- TOC entry 4961 (class 2606 OID 33118)
-- Name: barbero_servicio barbero_servicio_servicio_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.barbero_servicio
    ADD CONSTRAINT barbero_servicio_servicio_id_fkey FOREIGN KEY (servicio_id) REFERENCES public.servicios(servicio_id);


--
-- TOC entry 4954 (class 2606 OID 33162)
-- Name: barberos barberos_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.barberos
    ADD CONSTRAINT barberos_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(usuario_id) ON DELETE CASCADE;


--
-- TOC entry 4959 (class 2606 OID 33170)
-- Name: blog_posts blog_posts_autor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blog_posts
    ADD CONSTRAINT blog_posts_autor_id_fkey FOREIGN KEY (autor_id) REFERENCES public.barberos(barbero_id) ON DELETE SET NULL;


--
-- TOC entry 4958 (class 2606 OID 33059)
-- Name: pagos pagos_reserva_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_reserva_id_fkey FOREIGN KEY (reserva_id) REFERENCES public.reservas(reserva_id);


--
-- TOC entry 4955 (class 2606 OID 33175)
-- Name: reservas reservas_barbero_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_barbero_id_fkey FOREIGN KEY (barbero_id) REFERENCES public.barberos(barbero_id) ON DELETE SET NULL;


--
-- TOC entry 4956 (class 2606 OID 33034)
-- Name: reservas reservas_cliente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_cliente_id_fkey FOREIGN KEY (cliente_id) REFERENCES public.clientes(cliente_id);


--
-- TOC entry 4957 (class 2606 OID 33044)
-- Name: reservas reservas_servicio_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_servicio_id_fkey FOREIGN KEY (servicio_id) REFERENCES public.servicios(servicio_id);


-- Completed on 2026-05-22 13:09:26

--
-- PostgreSQL database dump complete
--

\unrestrict 5aVHnAKibJCNZKrqQMa8EF6KdECNiylxUoGhRe2DAHu0C6wAw9454QxMsEa3erA

