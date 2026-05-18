```mermaid
%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#1a1a1a',
    'primaryTextColor': '#fff',
    'primaryBorderColor': '#d4af37',
    'lineColor': '#d4af37',
    'secondaryColor': '#2d2d2d',
    'tertiaryColor': '#f4f4f4',
    'mainBkg': '#2d2d2d',
    'nodeBorder': '#d4af37',
    'clusterBkg': '#2d2d2d',
    'titleColor': '#d4af37',
    'fontFamily': 'trebuchet ms'
  }
} }%%

classDiagram
    class BD {
        +obtenerConexion() PDO$
    }

    class Usuario {
        +int usuarioId
        +string nombre
        +string email
        +string rol
        +bool activo
        +__construct(nombre, email, rol, activo, usuarioId)
        +estaActivo() bool
        +getNombre() string
        +crear() void
        +actualizar() void
        +eliminar() void
        +obtenerPorId(usuarioId) Usuario$
        +obtenerTodos() array$
        +listarUsuarios() array
    }

    class UsuarioBarbero {
        +int barberoId
        +__construct(nombre, email, barberoId, activo, usuarioId)
        +puedeGestionarSusReservas() bool
        +getBarberoId() int
        +puedeVerTodasLasReservas() bool
    }

    class Administrador {
        +__construct(nombre, email, activo, usuarioId)
        +puedeVerTodasLasReservas() bool
        +puedeGestionarReservas() bool
        +puedeGestionarEquipo() bool
        +puedeGestionarServicios() bool
        +puedeGestionarGaleria() bool
        +puedeGestionarBlog() bool
        +puedeGestionarResenas() bool
        +puedeGestionarUbicacion() bool
    }

    class GestorUsuarios {
        +autenticar(email, password) Usuario$
        +obtenerDatosSesion(usuario) array$
        +obtenerDesdeSesion() Usuario$
    }

    %% Relaciones de Herencia (Especialización)
    Usuario <|-- UsuarioBarbero
    Usuario <|-- Administrador

    %% Relaciones de Dependencia (Uso de clases)
    GestorUsuarios ..> Usuario : Instancia / Maneja
    GestorUsuarios ..> BD : Obtiene conexión
    Usuario ..> BD : Obtiene conexión
    ```