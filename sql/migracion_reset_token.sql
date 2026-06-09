-- Migración: añadir columnas para recuperación de contraseña
-- Ejecutar en el SQL Editor de Supabase (o en pgAdmin)

ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS reset_token   VARCHAR(64),
    ADD COLUMN IF NOT EXISTS token_expires TIMESTAMPTZ;
