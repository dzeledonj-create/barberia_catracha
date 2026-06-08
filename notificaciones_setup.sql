-- Sistema de notificaciones: añade la columna donde se guarda el token de
-- notificaciones push del navegador de cada barbero (lo genera el propio
-- navegador al activar las notificaciones desde el panel).
ALTER TABLE barberos ADD COLUMN IF NOT EXISTS push_token TEXT DEFAULT NULL;

-- Los administradores no tienen fila en "barberos", así que su token de
-- notificaciones push se guarda directamente en su propia fila de "usuarios".
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS push_token TEXT DEFAULT NULL;
