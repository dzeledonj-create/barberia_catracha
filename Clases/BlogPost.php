<?php
require_once 'BD.php';

class BlogPost {
    public ?int $postId;
    public string $titulo;
    public string $contenido;
    public ?string $imagenUrl;
    public int $autorId;
    public ?string $fechaPublicacion;
    public ?string $etiquetas;

    //
    public function __construct($titulo,$contenido,$autorId,$imagenUrl = null,$etiquetas = null,$postId = null, $fechaPublicacion = null) {
        $this->postId = $postId;
        $this->titulo = $titulo;
        $this->contenido = $contenido;
        $this->autorId = $autorId;
        $this->imagenUrl = $imagenUrl;
        $this->etiquetas = $etiquetas;
        $this->fechaPublicacion = $fechaPublicacion;
    }


    // Métodos para obtener un resumen del contenido, verificar si tiene imagen y obtener etiquetas como array
    public function resumen($limite = 100): string {
        return substr($this->contenido, 0, $limite) . "...";
    }

    public function tieneImagen(): bool {
        return !empty($this->imagenUrl);
    }

    public function obtenerEtiquetas(): array {
        if (!$this->etiquetas) return [];
        return array_map('trim', explode(',', $this->etiquetas));
    }

    // Método para guardar o actualizar el post en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        if ($this->postId === null) {
            $sql = "INSERT INTO blog_posts (titulo, contenido, imagen_url, autor_id, etiquetas)
                    VALUES (?, ?, ?, ?, ?)
                    RETURNING post_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->titulo,
                $this->contenido,
                $this->imagenUrl,
                $this->autorId,
                $this->etiquetas
            ]);

            $this->postId = $stmt->fetchColumn();
            return true;
        }

        $sql = "UPDATE blog_posts
                SET titulo = ?, contenido = ?, imagen_url = ?, etiquetas = ?
                WHERE post_id = ?";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $this->titulo,
            $this->contenido,
            $this->imagenUrl,
            $this->etiquetas,
            $this->postId
        ]);
    }

    // Método para eliminar el post de la base de datos
    public function eliminar(): bool {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("DELETE FROM blog_posts WHERE post_id = ?");
        return $stmt->execute([$this->postId]);
    }

    // Método para obtener todos los posts con el nombre del autor
    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();

        $sql = "SELECT p.*, b.nombre AS autor_nombre
                FROM blog_posts p
                JOIN barberos b ON p.autor_id = b.barbero_id
                ORDER BY p.fecha_publicacion DESC";

        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener un post por ID
    public static function obtenerPorId($postId): ?BlogPost {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE post_id = ?");
        $stmt->execute([$postId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return new BlogPost(
            $data['titulo'],
            $data['contenido'],
            $data['autor_id'],
            $data['imagen_url'],
            $data['etiquetas'],
            $data['post_id'],
            $data['fecha_publicacion']
        );
    }

    // Método para obtener posts por autor
    public static function obtenerPorAutor($autorId): array {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE autor_id = ? ORDER BY fecha_publicacion DESC");
        $stmt->execute([$autorId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener posts por etiqueta
    public static function obtenerPorEtiqueta($etiqueta): array {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE etiquetas ILIKE ?");
        $stmt->execute(['%' . $etiqueta . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}