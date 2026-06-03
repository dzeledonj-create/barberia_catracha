-- Migra la tabla barberos para almacenar si un barbero admin está autorizado para verse en la vista de clientes.
ALTER TABLE barberos
ADD COLUMN mostrar_en_vista BOOLEAN DEFAULT FALSE;

-- Si quieres activar algunos barberos administradores existentes para la vista del cliente, puedes usar:
-- UPDATE barberos SET mostrar_en_vista = TRUE WHERE usuario_id IN (...);
