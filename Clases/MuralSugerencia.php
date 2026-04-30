<?php

class MuralSugerencia {
    public int $sugerenciaId;
    public ?string $nombreCorte;
    public ?string $descripcion;
    public string $imagenUrl;
    public ?string $estilo;
    public bool $activo = true;
}