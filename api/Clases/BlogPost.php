<?php
require_once __DIR__ . '/BD.php';

class BlogPost {
    private ?int $postId;
    private string $titulo;
    private string $contenido;
    private ?string $imagenUrl;
    private int $autorId;
    private ?string $fechaPublicacion;
    private ?string $etiquetas;
    private ?string $instagramEmbed;

    public function __construct($titulo, $contenido, $autorId, $imagenUrl = null, $etiquetas = null, $postId = null, $fechaPublicacion = null, $instagramEmbed = null) {
        $this->postId = $postId;
        $this->titulo = $titulo;
        $this->contenido = $contenido;
        $this->autorId = $autorId;
        $this->imagenUrl = $imagenUrl;
        $this->etiquetas = $etiquetas;
        $this->fechaPublicacion = $fechaPublicacion;
        $this->instagramEmbed = $instagramEmbed;
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

    // Getters y setters para las propiedades del post
    public function getPostId(): ?int {
        return $this->postId;
    }

    public function setPostId(?int $postId): void {
        $this->postId = $postId;
    }

    public function getTitulo(): string {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void {
        $this->titulo = $titulo;
    }

    public function getContenido(): string {
        return $this->contenido;
    }

    public function setContenido(string $contenido): void {
        $this->contenido = $contenido;
    }

    public function getImagenUrl(): ?string {
        return $this->imagenUrl;
    }

    public function setImagenUrl(?string $imagenUrl): void {
        $this->imagenUrl = $imagenUrl;
    }

    public function getAutorId(): int {
        return $this->autorId;
    }

    public function setAutorId(int $autorId): void {
        $this->autorId = $autorId;
    }

    public function getFechaPublicacion(): ?string {
        return $this->fechaPublicacion;
    }

    public function setFechaPublicacion(?string $fechaPublicacion): void {
        $this->fechaPublicacion = $fechaPublicacion;
    }

    public function getEtiquetas(): ?string {
        return $this->etiquetas;
    }

    public function setEtiquetas(?string $etiquetas): void {
        $this->etiquetas = $etiquetas;
    }

    public function getInstagramEmbed(): ?string {
        return $this->instagramEmbed;
    }

    public function setInstagramEmbed(?string $instagramEmbed): void {
        $this->instagramEmbed = $instagramEmbed;
    }

    // --- GETTERS PARA ACCESO DIRECTO A PROPIEDADES ---
    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    // Método para guardar o actualizar el post en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        if ($this->postId === null) {
            $sql = "INSERT INTO blog_posts 
                    (titulo, contenido, imagen_url, autor_id, etiquetas, instagram_embed)
                    VALUES (?, ?, ?, ?, ?, ?)
                    RETURNING post_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->titulo,
                $this->contenido,
                $this->imagenUrl,
                $this->autorId,
                $this->etiquetas,
                $this->instagramEmbed
            ]);

            $this->postId = $stmt->fetchColumn();
            return true;
        }

        $sql = "UPDATE blog_posts
                SET titulo = ?, contenido = ?, imagen_url = ?, etiquetas = ?, instagram_embed = ?
                WHERE post_id = ?";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $this->titulo,
            $this->contenido,
            $this->imagenUrl,
            $this->etiquetas,
            $this->instagramEmbed,
            $this->postId
        ]);
    }

    // Método para eliminar el post de la base de datos
    public function eliminar(): bool {
        $db = BD::obtenerConexion();
        $stmt = $db->prepare("DELETE FROM blog_posts WHERE post_id = ?");
        return $stmt->execute([$this->postId]);
    }

    // Método para obtener todos los posts con el nombre del autor (Corregido)
    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();

        // Usamos LEFT JOIN para que no desaparezcan los posts que no tienen autor asignado
        $sql = "SELECT p.*, u.nombre AS autor_nombre
                FROM blog_posts p
                LEFT JOIN barberos b ON p.autor_id = b.barbero_id
                LEFT JOIN usuarios u ON b.usuario_id = u.usuario_id
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
            $data['fecha_publicacion'],
            $data['instagram_embed']
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