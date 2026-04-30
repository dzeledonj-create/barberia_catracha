<?php

class BlogPost {
    public int $postId;
    public string $titulo;
    public string $contenido;
    public ?string $imagenUrl;
    public Barbero $autor;
    public string $fechaPublicacion;
    public ?string $etiquetas;
}