CREATE TABLE clientes (
    cliente_id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE barberos (
    barbero_id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100),
    foto_url TEXT,
    activo BOOLEAN DEFAULT TRUE
);
CREATE TABLE servicios (
    servicio_id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    duracion_minutos INT NOT NULL -- Importante para el Motor de Disponibilidad
);
CREATE TABLE reservas (
    reserva_id SERIAL PRIMARY KEY,
    cliente_id INT REFERENCES clientes(cliente_id),
    barbero_id INT REFERENCES barberos(barbero_id),
    servicio_id INT REFERENCES servicios(servicio_id),
    fecha_hora TIMESTAMP NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente', -- pendiente, confirmada, cancelada, completada
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE pagos (
    pago_id SERIAL PRIMARY KEY,
    reserva_id INT REFERENCES reservas(reserva_id),
    monto DECIMAL(10, 2) NOT NULL,
    metodo_pago VARCHAR(50), -- Efectivo, Tarjeta, Transferencia
    estado_pago VARCHAR(20), -- pagado, reembolsado, fallido
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE blog_posts (
    post_id SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    imagen_url TEXT, -- URL de la foto subida a la nube
    autor_id INT REFERENCES barberos(barbero_id), -- Quién escribió el post
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    etiquetas VARCHAR(100) -- Ej: 'tendencias', 'cuidado barba'
);
CREATE TABLE mural_sugerencias (
    sugerencia_id SERIAL PRIMARY KEY,
    nombre_corte VARCHAR(100),
    descripcion TEXT,
    imagen_url TEXT NOT NULL,
    estilo VARCHAR(50), -- Ej: 'Fade', 'Clásico', 'Moderno'
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE horarios (
    horario_id SERIAL PRIMARY KEY,
    dia_semana INT, -- 0 domingo, 1 lunes...
    hora_apertura TIME,
    hora_cierre TIME
);

CREATE TABLE barbero_servicio (
    barbero_id INT REFERENCES barberos(barbero_id),
    servicio_id INT REFERENCES servicios(servicio_id),
    PRIMARY KEY (barbero_id, servicio_id)
);

INSERT INTO barberos (nombre, especialidad, foto_url, activo) VALUES
('Ross', 'Barbero profesional', 'assets/img/ross.jpeg', TRUE),
('Rolando', 'Barbero profesional', 'assets/img/rolando.jpeg', TRUE),
('Luis', 'Barbero profesional', 'assets/img/luis.jpeg', TRUE);

INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos) VALUES
('Corte', 'Corte de pelo clásico o moderno', 11.00, 30),
('Corte y barba', 'Corte de pelo más arreglo de barba', 16.00, 45),
('Barba', 'Arreglo de barba', 7.00, 15),
('Corte Y Cejas Con Hilo', 'Corte de pelo y cejas con hilo', 15.00, 40),
('Barba + tinte', 'Arreglo de barba con tinte', 12.00, 30),
('Cejas con cuchilla', 'Diseño de cejas con cuchilla', 4.00, 5),
('Cejas con hilo', 'Diseño de cejas con hilo', 5.00, 15),
('Tinte negro', 'Tinte negro para el pelo', 10.00, 10),
('Tinte de colores', 'Tinte de colores', 50.00, 180),
('Corte para jubilados', 'Corte especial para jubilados', 10.00, 30),
('Corte y diseño', 'Corte con diseño personalizado', 12.00, 35),
('Corte, barba y tinte De Pelo', 'Corte, barba y tinte de pelo', 25.00, 60),
('Permanente', 'Tratamiento de permanente', 50.00, 180);

INSERT INTO horarios (dia_semana, hora_apertura, hora_cierre) VALUES
(1, '10:00', '19:30'), -- Lunes
(2, '09:30', '20:30'), -- Martes
(3, '09:30', '20:30'), -- Miércoles
(4, '09:30', '20:30'), -- Jueves
(5, '09:30', '20:30'), -- Viernes
(6, '09:30', '20:30'), -- Sábado
(0, '10:00', '13:30'); -- Domingo

INSERT INTO barbero_servicio (barbero_id, servicio_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13);

INSERT INTO barbero_servicio (barbero_id, servicio_id) VALUES
(2, 4),  -- Corte y cejas con hilo
(2, 3),  -- Barba
(2, 6),  -- Cejas cuchilla
(2, 2),  -- Corte y barba
(2, 1),  -- Corte
(2, 10), -- Corte jubilados
(2, 11); -- Corte y diseño

INSERT INTO barbero_servicio (barbero_id, servicio_id) VALUES
(3, 4),  -- Corte y cejas con hilo
(3, 3),  -- Barba
(3, 2),  -- Corte y barba
(3, 1),  -- Corte
(3, 10), -- Corte jubilados
(3, 11); -- Corte y diseño

INSERT INTO mural_sugerencias (nombre_corte, descripcion, imagen_url, estilo, activo) VALUES
('Fade bajo', 'Degradado bajo limpio y elegante.', 'assets/img/mural/fade-bajo.jpg', 'Fade', TRUE),
('Fade medio', 'Degradado medio moderno y versátil.', 'assets/img/mural/fade-medio.jpg', 'Fade', TRUE),
('Crop texturizado', 'Corte moderno con textura en la parte superior.', 'assets/img/mural/crop.jpg', 'Moderno', TRUE),
('Clásico con raya', 'Corte clásico con raya marcada.', 'assets/img/mural/clasico-raya.jpg', 'Clásico', TRUE),
('Buzz cut', 'Corte corto, práctico y fácil de mantener.', 'assets/img/mural/buzz-cut.jpg', 'Moderno', TRUE);

SELECT * FROM horarios;

ALTER TABLE servicios
ADD COLUMN limite VARCHAR(50);

UPDATE servicios SET limite = 'populares' WHERE nombre = 'Corte';
UPDATE servicios SET limite = 'populares' WHERE nombre = 'Corte y barba';
UPDATE servicios SET limite = 'populares' WHERE nombre = 'Barba';

UPDATE servicios SET limite = 'barba' WHERE nombre = 'Barba';
UPDATE servicios SET limite = 'barba' WHERE nombre = 'Barba + tinte';

UPDATE servicios SET limite = 'cejas' WHERE nombre = 'Cejas con cuchilla';
UPDATE servicios SET limite = 'cejas' WHERE nombre = 'Cejas con hilo';

UPDATE servicios SET limite = 'color' WHERE nombre = 'Tinte negro';
UPDATE servicios SET limite = 'color' WHERE nombre = 'Tinte de colores';

UPDATE servicios SET limite = 'corte_y_barba' WHERE nombre = 'Corte y barba';
UPDATE servicios SET limite = 'corte_y_barba' WHERE nombre = 'Corte, barba y tinte De Pelo';

UPDATE servicios SET limite = 'corte' WHERE nombre = 'Corte';
UPDATE servicios SET limite = 'corte' WHERE nombre = 'Corte para jubilados';
UPDATE servicios SET limite = 'corte' WHERE nombre = 'Corte y diseño';

UPDATE servicios SET limite = 'permanente' WHERE nombre = 'Permanente';


CREATE TABLE usuarios (
    usuario_id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL CHECK (rol IN ('admin', 'barbero')),
    barbero_id INT REFERENCES barberos(barbero_id),
    activo BOOLEAN DEFAULT TRUE
);

INSERT INTO usuarios (nombre, email, password, rol, barbero_id) VALUES
('Administrador', 'admin@barberia.com', '1234', 'admin', NULL),
('Ross', 'ross@barberia.com', '1234', 'barbero', 1),
('Rolando', 'rolando@barberia.com', '1234', 'barbero', 2),
('Luis', 'luis@barberia.com', '1234', 'barbero', 3);