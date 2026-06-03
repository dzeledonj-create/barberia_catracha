<?php
require_once __DIR__ . '/BD.php';

class Barbero {
    private ?int $barberoId;
    private ?int $usuarioId; // Guardamos el ID de usuario relacionado
    private string $nombre;
    private ?string $descripcion;
    private ?string $etiquetas;
    private ?string $especialidad;
    private ?string $fotoUrl;
    private bool $activo;
    private ?string $rol;
    private ?string $email;
    private bool $mostrarEnVista;
    private static ?bool $tieneMostrarEnVista = null;

    public function __construct($nombre, $especialidad = null, $fotoUrl = null, $activo = true, $barberoId = null, $descripcion = null, $etiquetas = null, $rol = null, $email = null, $usuarioId = null, bool $mostrarEnVista = false) {
        $this->barberoId = $barberoId;
        $this->usuarioId = $usuarioId;
        $this->nombre = $nombre;
        $this->especialidad = $especialidad;
        $this->fotoUrl = $fotoUrl;
        $this->activo = $activo;
        $this->descripcion = $descripcion;
        $this->etiquetas = $etiquetas;
        $this->rol = $rol;
        $this->email = $email;
        $this->mostrarEnVista = $mostrarEnVista;
    }

    // Método para verificar si el barbero está activo
    public function estaActivo(): bool {
        return $this->activo;
    }

    // Getters y setters para las propiedades del barbero
    public function getBarberoId(): ?int {
        return $this->barberoId;
    }

    public function setBarberoId(?int $barberoId): void {
        $this->barberoId = $barberoId;
    }

    public function getUsuarioId(): ?int {
        return $this->usuarioId;
    }

    public function setUsuarioId(?int $usuarioId): void {
        $this->usuarioId = $usuarioId;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getDescripcion(): ?string {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function getEtiquetas(): ?string {
        return $this->etiquetas;
    }

    public function setEtiquetas(?string $etiquetas): void {
        $this->etiquetas = $etiquetas;
    }

    public function getEspecialidad(): ?string {
        return $this->especialidad;
    }

    public function setEspecialidad(?string $especialidad): void {
        $this->especialidad = $especialidad;
    }

    public function getFotoUrl(): ?string {
        if ($this->fotoUrl === null) {
            return null;
        }

        $url = trim($this->fotoUrl);

        if (str_contains($url, '://')) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $normalized = ltrim($url, '/');

        if (str_starts_with($normalized, 'assets/')) {
            return '/barberia_catracha/' . $normalized;
        }

        return '/barberia_catracha/assets/img/' . $normalized;
    }

    public function setFotoUrl(?string $fotoUrl): void {
        $this->fotoUrl = $fotoUrl;
    }

    public function getActivo(): bool {
        return $this->activo;
    }

    public function setActivo(bool $activo): void {
        $this->activo = $activo;
    }

    public function getRol(): ?string {
        return $this->rol;
    }

    public function setRol(?string $rol): void {
        $this->rol = $rol;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getMostrarEnVista(): bool {
        return $this->mostrarEnVista;
    }

    public function setMostrarEnVista(bool $mostrarEnVista): void {
        $this->mostrarEnVista = $mostrarEnVista;
    }

    public static function tieneColumnaMostrarEnVista(): bool {
        if (self::$tieneMostrarEnVista !== null) {
            return self::$tieneMostrarEnVista;
        }

        $db = BD::obtenerConexion();
        $stmt = $db->prepare(
            "SELECT 1 FROM information_schema.columns WHERE table_name = 'barberos' AND column_name = 'mostrar_en_vista' LIMIT 1"
        );
        $stmt->execute();
        self::$tieneMostrarEnVista = (bool)$stmt->fetchColumn();
        return self::$tieneMostrarEnVista;
    }

    public function guardar(): bool {
        $db = BD::obtenerConexion();

        // Si el barbero no tiene un ID, es una creación; de lo contrario, es una actualización
        try {
            $db->beginTransaction();

            if ($this->barberoId === null) {
                // 1. OPERACIÓN CREAR: Insertar primero en usuarios usando RETURNING para PostgreSQL
                $sqlUsuario = "INSERT INTO usuarios (nombre, email, password, rol, activo) 
                               VALUES (?, ?, '1234', 'barbero', ?)
                               RETURNING usuario_id";
                $stmtU = $db->prepare($sqlUsuario);
                $stmtU->execute([
                    $this->nombre,
                    $this->email,
                    $this->activo ? 1 : 0
                ]);
                // Asignamos el ID de usuario generado al objeto actual para usarlo en la tabla barberos
                $this->usuarioId = (int)$stmtU->fetchColumn();

                // 2. Insertar en la tabla barberos vinculando el usuario_id obtenido
                $sqlBarbero = "INSERT INTO barberos (usuario_id, especialidad, foto_url, descripcion, etiquetas";
                $sqlBarbero .= self::tieneColumnaMostrarEnVista() ? ", mostrar_en_vista" : "";
                $sqlBarbero .= ") VALUES (?, ?, ?, ?, ?";
                $sqlBarbero .= self::tieneColumnaMostrarEnVista() ? ", ?" : "";
                $sqlBarbero .= ") RETURNING barbero_id";

                $stmtB = $db->prepare($sqlBarbero);
                $params = [
                    $this->usuarioId,
                    $this->especialidad,
                    $this->fotoUrl,
                    $this->descripcion,
                    $this->etiquetas
                ];
                if (self::tieneColumnaMostrarEnVista()) {
                    $params[] = $this->mostrarEnVista;
                }
                $stmtB->execute($params);
                $this->barberoId = (int)$stmtB->fetchColumn();

            } else {
                // 2. OPERACIÓN ACTUALIZAR: Modificar tabla barberos
                $sqlBarbero = "UPDATE barberos
                               SET especialidad = ?, foto_url = ?, descripcion = ?, etiquetas = ?";
                if (self::tieneColumnaMostrarEnVista()) {
                    $sqlBarbero .= ", mostrar_en_vista = ?";
                }
                $sqlBarbero .= " WHERE barbero_id = ?";

                $stmtB = $db->prepare($sqlBarbero);
                $params = [
                    $this->especialidad,
                    $this->fotoUrl,
                    $this->descripcion,
                    $this->etiquetas
                ];
                if (self::tieneColumnaMostrarEnVista()) {
                    $params[] = $this->mostrarEnVista;
                }
                $params[] = $this->barberoId;
                $stmtB->execute($params);

                // Modificar tabla usuarios (nombre, email y estado activo)
                $sqlUsuario = "UPDATE usuarios 
                               SET nombre = ?, email = ?, activo = ? 
                               WHERE usuario_id = (SELECT usuario_id FROM barberos WHERE barbero_id = ?)";
                $stmtU = $db->prepare($sqlUsuario);
                $stmtU->execute([
                    $this->nombre,
                    $this->email,
                    $this->activo ? 1 : 0,
                    $this->barberoId
                ]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            // Si ocurre un error, revertimos la transacción para mantener la integridad de los datos
            if ($db->inTransaction()) $db->rollBack();
            return false;
        }
    }

    public function eliminar(): bool {
        if ($this->usuarioId === null) {
            return false;
        }

        $db = BD::obtenerConexion();
        try {
            $db->beginTransaction();

            // Si este usuario también tiene rol admin, solo borramos el perfil de barbero.
            // La eliminación completa del usuario debe hacerse desde el flujo de admin.
            if ($this->rol === 'admin') {
                $stmtB = $db->prepare("DELETE FROM barberos WHERE barbero_id = ?");
                $stmtB->execute([$this->barberoId]);
            } else {
                // Si es un usuario exclusivamente barbero, eliminamos tanto el perfil como el usuario.
                $stmtB = $db->prepare("DELETE FROM barberos WHERE usuario_id = ?");
                $stmtB->execute([$this->usuarioId]);

                $stmtU = $db->prepare("DELETE FROM usuarios WHERE usuario_id = ?");
                $stmtU->execute([$this->usuarioId]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }

    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();
        $mostrarEnVistaCampo = self::tieneColumnaMostrarEnVista() ? 'b.mostrar_en_vista' : 'FALSE AS mostrar_en_vista';

        $stmt = $db->query("SELECT u.usuario_id, u.nombre, u.activo, u.rol, u.email, 
                                b.barbero_id, b.especialidad, b.foto_url, b.descripcion, b.etiquetas, " . $mostrarEnVistaCampo . "
                            FROM usuarios u
                            LEFT JOIN barberos b ON u.usuario_id = b.usuario_id 
                            WHERE u.rol IN ('barbero', 'admin')
                            ORDER BY u.usuario_id DESC");
        
        $barberos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $defaultEspecialidad = $data['rol'] === 'admin' ? 'Administrador' : 'Barbero profesional';
            $defaultDescripcion = $data['rol'] === 'admin' ? 'Administrador del sistema.' : 'Barbero del equipo.';

            $barberos[] = new Barbero(
                $data['nombre'],
                $data['especialidad'] ?? $defaultEspecialidad,
                $data['foto_url'] ?? 'assets/img/default-user.jpg',
                (bool)$data['activo'],
                $data['barbero_id'] ? (int)$data['barbero_id'] : null,
                $data['descripcion'] ?? $defaultDescripcion,
                $data['etiquetas'] ?? '',
                $data['rol'],
                $data['email'],
                (int)$data['usuario_id'],
                isset($data['mostrar_en_vista']) ? (bool)$data['mostrar_en_vista'] : false
            );
        }
        return $barberos;
    }

    public static function obtenerActivos(): array {
        $db = BD::obtenerConexion();
        $mostrarEnVistaCampo = self::tieneColumnaMostrarEnVista() ? 'b.mostrar_en_vista' : 'FALSE AS mostrar_en_vista';
        $condicionAdmin = self::tieneColumnaMostrarEnVista() ? " OR (u.rol = 'admin' AND b.mostrar_en_vista = TRUE)" : '';

        $stmt = $db->query("SELECT b.*, u.nombre, u.activo, u.rol, u.email, u.usuario_id, " . $mostrarEnVistaCampo . "
                            FROM barberos b
                            INNER JOIN usuarios u ON b.usuario_id = u.usuario_id
                            WHERE u.activo = TRUE AND (u.rol = 'barbero'" . $condicionAdmin . ")");

        $barberos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $barberos[] = new Barbero(
                $data['nombre'],
                $data['especialidad'] ?? null,
                $data['foto_url'] ?? null,
                (bool)$data['activo'],
                $data['barbero_id'] ? (int)$data['barbero_id'] : null,
                $data['descripcion'] ?? null,
                $data['etiquetas'] ?? null,
                $data['rol'] ?? null,
                $data['email'] ?? null,
                $data['usuario_id'] ? (int)$data['usuario_id'] : null,
                isset($data['mostrar_en_vista']) ? (bool)$data['mostrar_en_vista'] : false
            );
        }

        return $barberos;
    }

    public static function obtenerPorId($barberoId): ?Barbero {
        $db = BD::obtenerConexion();
        $mostrarEnVistaCampo = self::tieneColumnaMostrarEnVista() ? ', b.mostrar_en_vista' : ', FALSE AS mostrar_en_vista';

        $stmt = $db->prepare("SELECT b.*, u.nombre, u.activo, u.rol, u.email" . $mostrarEnVistaCampo . " 
                              FROM barberos b 
                              INNER JOIN usuarios u ON b.usuario_id = u.usuario_id 
                              WHERE b.barbero_id = ?");
        $stmt->execute([$barberoId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return new Barbero(
            $data['nombre'],
            $data['especialidad'],
            $data['foto_url'],
            (bool)$data['activo'],
            $data['barbero_id'],
            $data['descripcion'],
            $data['etiquetas'],
            $data['rol'],
            $data['email'],
            $data['usuario_id'],
            isset($data['mostrar_en_vista']) ? (bool)$data['mostrar_en_vista'] : false
        );
    }

    public function autorizarEnVista(): bool {
        if (!self::tieneColumnaMostrarEnVista()) {
            return false;
        }

        $this->mostrarEnVista = true;
        return $this->guardar();
    }
}