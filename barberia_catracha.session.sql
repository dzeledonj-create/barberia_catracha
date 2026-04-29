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