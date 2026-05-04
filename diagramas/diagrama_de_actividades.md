```mermaid
graph TD
    %% Inicio del proceso
    A([Inicio: Cliente accede a la Web]) --> B[Seleccionar Barbero y Servicio]
    B --> C[Elegir Fecha y Hora]
    
    %% Validación de disponibilidad
    C --> D{¿Horario disponible?}
    
    D -- No --> E[Mostrar mensaje de error y sugerir otro horario]
    E --> B
    
    D -- Sí --> F[Registrar Datos del Cliente]
    F --> G[Crear Reserva en estado 'Pendiente']
    G --> H[Notificar al Barbero]
    
    %% Día de la cita
    H --> I[Cliente asiste a la cita]
    
    I --> J{¿Se realiza el servicio?}
    
    J -- No (Cancelación) --> K[Actualizar estado a 'Cancelada']
    K --> L([Fin del proceso])
    
    J -- Sí --> M[Barbero ejecuta el servicio]
    M --> N[Actualizar Reserva a 'Completada']
    
    %% Proceso de Pago
    N --> O[Generar Registro de Pago]
    O --> P[Seleccionar Método de Pago]
    P --> Q[Marcar como 'Pagado' en BD]
    
    Q --> R([Fin: Cliente satisfecho])

    %% Estilos para que se vea Pro
    style A fill:#1a1a1a,stroke:#d4af37,color:#fff
    style R fill:#1a1a1a,stroke:#d4af37,color:#fff
    style L fill:#1a1a1a,stroke:#d4af37,color:#fff
    style D fill:#2d2d2d,stroke:#d4af37,color:#fff
    style J fill:#2d2d2d,stroke:#d4af37,color:#fff
```