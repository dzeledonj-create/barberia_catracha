<?php
require_once __DIR__ . '/BD.php';

class Pago {
    private ?int $pagoId;
    private int $reservaId;
    private float $monto;
    private string $metodoPago;
    private string $estadoPago;
    private ?string $fechaPago;

   
    public function __construct($reservaId, $monto, $metodoPago, $estadoPago = "pagado", $pagoId = null, $fechaPago = null) {
        $this->pagoId = $pagoId;
        $this->reservaId = $reservaId;
        $this->monto = $monto;
        $this->metodoPago = $metodoPago;
        $this->estadoPago = $estadoPago;
        $this->fechaPago = $fechaPago;
    }

    // Métodos para cambiar el estado del pago
    public function marcarPagado(): void {
        $this->estadoPago = "pagado";
    }

    public function marcarFallido(): void {
        $this->estadoPago = "fallido";
    }

    public function reembolsar(): void {
        $this->estadoPago = "reembolsado";
    }

    public function estaPagado(): bool {
        return $this->estadoPago === "pagado";
    }

    public function formatearMonto(): string {
        return number_format($this->monto, 2) . " €";
    }

    // Getters y setters para las propiedades del pago
    public function getPagoId(): ?int {
        return $this->pagoId;
    }

    public function setPagoId(?int $pagoId): void {
        $this->pagoId = $pagoId;
    }

    public function getReservaId(): int {
        return $this->reservaId;
    }

    public function setReservaId(int $reservaId): void {
        $this->reservaId = $reservaId;
    }

    public function getMonto(): float {
        return $this->monto;
    }

    public function setMonto(float $monto): void {
        $this->monto = $monto;
    }

    public function getMetodoPago(): string {
        return $this->metodoPago;
    }

    public function setMetodoPago(string $metodoPago): void {
        $this->metodoPago = $metodoPago;
    }

    public function getEstadoPago(): string {
        return $this->estadoPago;
    }

    public function setEstadoPago(string $estadoPago): void {
        $this->estadoPago = $estadoPago;
    }

    public function getFechaPago(): ?string {
        return $this->fechaPago;
    }

    public function setFechaPago(?string $fechaPago): void {
        $this->fechaPago = $fechaPago;
    }

    // Método para guardar o actualizar el pago en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        if ($this->pagoId === null) {
            $sql = "INSERT INTO pagos (reserva_id, monto, metodo_pago, estado_pago)
                    VALUES (?, ?, ?, ?)
                    RETURNING pago_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->reservaId,
                $this->monto,
                $this->metodoPago,
                $this->estadoPago
            ]);

            $this->pagoId = $stmt->fetchColumn();
            return true;
        }

        $sql = "UPDATE pagos
                SET reserva_id = ?, monto = ?, metodo_pago = ?, estado_pago = ?
                WHERE pago_id = ?";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            $this->reservaId,
            $this->monto,
            $this->metodoPago,
            $this->estadoPago,
            $this->pagoId
        ]);
    }

    // Método para eliminar el pago de la base de datos
    public function eliminar(): bool {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("DELETE FROM pagos WHERE pago_id = ?");
        return $stmt->execute([$this->pagoId]);
    }

    //Método para obtener todos los pagos con detalles de reserva
    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();

        $sql = "SELECT p.*, r.fecha_hora
                FROM pagos p
                JOIN reservas r ON p.reserva_id = r.reserva_id
                ORDER BY p.fecha_pago DESC";
//$reservaId, $monto, $metodoPago, $estadoPago = "pagado", $pagoId = null, $fechaPago = null
       // return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
       $pagos = [];
       $stmt = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
       foreach ($stmt as $row) {
        $pagos[] = new Pago(
            $row['reserva_id'],
            $row['monto'],
            $row['metodo_pago'],
            $row['estado_pago'],
            $row['pago_id'],
            $row['fecha_pago']
        );
       }
       return $pagos;
    }

    // Método para obtener un pago por ID
    public static function obtenerPorId($pagoId): ?Pago {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM pagos WHERE pago_id = ?");
        $stmt->execute([$pagoId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return new Pago(
            $data['reserva_id'],
            $data['monto'],
            $data['metodo_pago'],
            $data['estado_pago'],
            $data['pago_id'],
            $data['fecha_pago']
        );
    }

    // Método para obtener un pago por ID de reserva
    public static function obtenerPorReserva($reservaId): ?Pago {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM pagos WHERE reserva_id = ?");
        $stmt->execute([$reservaId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return new Pago(
            $data['reserva_id'],
            $data['monto'],
            $data['metodo_pago'],
            $data['estado_pago'],
            $data['pago_id'],
            $data['fecha_pago']
        );
    }
}