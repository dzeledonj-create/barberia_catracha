<?php
require_once __DIR__ . '/BD.php';

class MuralSugerencia {
    private ?int $sugerenciaId;
    private ?string $nombreCorte;
    private ?string $descripcion;
    private string $imagenUrl;
    private ?string $estilo;
    private bool $activo;

    public function __construct($imagenUrl,$nombreCorte = null, $descripcion = null, $estilo = null, $activo = true, $sugerenciaId = null) {
        $this->sugerenciaId = $sugerenciaId;
        $this->nombreCorte = $nombreCorte;
        $this->descripcion = $descripcion;
        $this->imagenUrl = $imagenUrl;
        $this->estilo = $estilo;
        $this->activo = $activo;
    }

    // Métodos para activar/desactivar la sugerencia y verificar su estado
    public function activar(): void {
        $this->activo = true;
    }

    public function desactivar(): void {
        $this->activo = false;
    }

    public function estaActiva(): bool {
        return $this->activo;
    }

    public function tieneDescripcion(): bool {
        return !empty($this->descripcion);
    }

    // Getters y setters para las propiedades de la sugerencia
    public function getSugerenciaId(): ?int {
        return $this->sugerenciaId;
    }

    public function setSugerenciaId(?int $sugerenciaId): void {
        $this->sugerenciaId = $sugerenciaId;
    }

    public function getNombreCorte(): ?string {
        return $this->nombreCorte;
    }

    public function setNombreCorte(?string $nombreCorte): void {
        $this->nombreCorte = $nombreCorte;
    }

    public function getDescripcion(): ?string {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function getImagenUrl(): string {
        if (empty($this->imagenUrl)) {
            return 'assets/img/default-user.jpg';
        }

        $url = trim($this->imagenUrl);

        // Si contiene protocolo (http://, https://), es URL absoluta
        if (str_contains($url, '://')) {
            return $url;
        }

        // Si comienza con /, es ruta absoluta
        if (str_starts_with($url, '/')) {
            return $url;
        }

        // Normalizar y añadir prefijo de barberia_catracha
        $normalized = ltrim($url, '/');

        if (str_starts_with($normalized, 'assets/')) {
            return BASE_PATH . '/' . $normalized;
        }

        return BASE_PATH . '/assets/img/' . $normalized;
    }

    public function setImagenUrl(string $imagenUrl): void {
        $this->imagenUrl = $imagenUrl;
    }

    public function getEstilo(): ?string {
        return $this->estilo;
    }

    public function setEstilo(?string $estilo): void {
        $this->estilo = $estilo;
    }

    public function getActivo(): bool {
        return $this->activo;
    }

    public function setActivo(bool $activo): void {
        $this->activo = $activo;
    }

    // Método para guardar o actualizar la sugerencia en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        if ($this->sugerenciaId === null) {
            $sql = "INSERT INTO mural_sugerencias 
                    (nombre_corte, descripcion, imagen_url, estilo, activo)
                    VALUES (?, ?, ?, ?, ?)
                    RETURNING sugerencia_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->nombreCorte,
                $this->descripcion,
                $this->imagenUrl,
                $this->estilo,
                $this->activo
            ]);

            $this->sugerenciaId = $stmt->fetchColumn();
            return true;
        }

        $sql = "UPDATE mural_sugerencias
                SET nombre_corte = ?, descripcion = ?, imagen_url = ?, estilo = ?, activo = ?
                WHERE sugerencia_id = ?";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            $this->nombreCorte,
            $this->descripcion,
            $this->imagenUrl,
            $this->estilo,
            $this->activo,
            $this->sugerenciaId
        ]);
    }

    // Método para eliminar la sugerencia de la base de datos
    public function eliminar(): bool {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("DELETE FROM mural_sugerencias WHERE sugerencia_id = ?");
        return $stmt->execute([$this->sugerenciaId]);
    }

    // Métodos para obtener sugerencias de la base de datos
    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT * FROM mural_sugerencias ORDER BY sugerencia_id DESC");
        
        $sugerencias = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sugerencias[] = new MuralSugerencia(
                $data['imagen_url'],
                $data['nombre_corte'],
                $data['descripcion'],
                $data['estilo'],
                (bool)$data['activo'],
                (int)$data['sugerencia_id']
            );
        }
        return $sugerencias;
    }

    // Método para obtener solo las sugerencias activas
    public static function obtenerActivas(): array {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT * FROM mural_sugerencias WHERE activo = TRUE ORDER BY sugerencia_id DESC");
        
        $sugerencias = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sugerencias[] = new MuralSugerencia(
                $data['imagen_url'],
                $data['nombre_corte'],
                $data['descripcion'],
                $data['estilo'],
                (bool)$data['activo'],
                (int)$data['sugerencia_id']
            );
        }
        return $sugerencias;
    }

    // Método para obtener todas las categorías (estilos) únicas que tienen cortes activos
public static function obtenerCategoriasRecientes(): array {
    $db = BD::obtenerConexion();
    // Seleccionamos estilos únicos, que no sean nulos y estén activos
    $stmt = $db->query("SELECT DISTINCT estilo FROM mural_sugerencias 
                        WHERE estilo IS NOT NULL AND activo = TRUE 
                        ORDER BY estilo ASC");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

    // Método para obtener una sugerencia por ID
    public static function obtenerPorId($sugerenciaId): ?MuralSugerencia {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM mural_sugerencias WHERE sugerencia_id = ?");
        $stmt->execute([$sugerenciaId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new MuralSugerencia(
            $data['imagen_url'],
            $data['nombre_corte'],
            $data['descripcion'],
            $data['estilo'],
            $data['activo'],
            $data['sugerencia_id']
        );
    }

    // Método para obtener sugerencias por estilo
    public static function obtenerPorEstilo($estilo): array {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM mural_sugerencias 
                              WHERE estilo ILIKE ? AND activo = TRUE
                              ORDER BY sugerencia_id DESC");

        $stmt->execute(['%' . $estilo . '%']);

        $sugerencias = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sugerencias[] = new MuralSugerencia(
                $data['imagen_url'],
                $data['nombre_corte'],
                $data['descripcion'],
                $data['estilo'],
                (bool)$data['activo'],
                (int)$data['sugerencia_id']
            );
        }
        return $sugerencias;
    }
}