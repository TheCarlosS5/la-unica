<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/*
 * Las funciones del servicio abren y confirman sus propias transacciones, así que estas pruebas no
 * pueden envolverse en un rollback como DatabaseReportsTest: los datos se borran en tearDown().
 */
final class FacturasServicioTest extends TestCase
{
    private mysqli $db;
    private int $clientId;
    private int $productAId;
    private int $productBId;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var mysqli $connection */
        $connection = $GLOBALS['dbTestConnection'];
        $this->db = $connection;

        do {
            $this->clientId = random_int(100000000, 1900000000);
        } while (consultaPreparada($this->db, 'SELECT 1 FROM clientes WHERE id_cliente = ?', 'i', $this->clientId) !== []);

        $name = 'PHPUnit cliente ventas';
        $stmt = $this->db->prepare('INSERT INTO clientes (id_cliente, nombre) VALUES (?, ?)');
        $stmt->bind_param('is', $this->clientId, $name);
        $stmt->execute();
        $stmt->close();

        $this->productAId = $this->insertProduct('PHPUnit venta A', 10, '1000.00');
        $this->productBId = $this->insertProduct('PHPUnit venta B', 5, '250.50');
    }

    protected function tearDown(): void
    {
        $this->db->query(
            'DELETE d FROM detalle_facturas d INNER JOIN facturas f ON f.id_factura = d.id_factura'
            . ' WHERE f.id_cliente = ' . $this->clientId
        );
        $this->db->query('DELETE FROM facturas WHERE id_cliente = ' . $this->clientId);
        $this->db->query(
            'DELETE FROM productos WHERE id_producto IN (' . $this->productAId . ', ' . $this->productBId . ')'
        );
        $this->db->query('DELETE FROM clientes WHERE id_cliente = ' . $this->clientId);

        parent::tearDown();
    }

    private function insertProduct(string $name, int $stock, string $price): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO productos (nombre, unidad_medida, stock, precio_unitario) VALUES (?, 'unidad', ?, ?)"
        );
        $stmt->bind_param('sis', $name, $stock, $price);
        $stmt->execute();
        $id = (int)$this->db->insert_id;
        $stmt->close();

        return $id;
    }

    private function stock(int $productId): int
    {
        return (int)consultaPreparada($this->db, 'SELECT stock FROM productos WHERE id_producto = ?', 'i', $productId)[0]['stock'];
    }

    private function estado(int $invoiceId): string
    {
        return consultaPreparada($this->db, 'SELECT estado_factura FROM facturas WHERE id_factura = ?', 'i', $invoiceId)[0]['estado_factura'];
    }

    private function facturasDelCliente(): int
    {
        return count(consultaPreparada($this->db, 'SELECT id_factura FROM facturas WHERE id_cliente = ?', 'i', $this->clientId));
    }

    public function testSaleStoresPriceAndDiscountsStock(): void
    {
        $venta = registrarVenta($this->db, $this->clientId, [$this->productAId => 3, $this->productBId => 2]);

        self::assertSame('3501.00', $venta['total']);
        self::assertSame('Pagado', $this->estado($venta['id']));
        self::assertSame(7, $this->stock($this->productAId));
        self::assertSame(3, $this->stock($this->productBId));

        $lineas = consultaPreparada(
            $this->db,
            'SELECT id_producto, cantidad, precio_unitario FROM detalle_facturas WHERE id_factura = ? ORDER BY id_producto',
            'i',
            $venta['id']
        );
        self::assertSame('1000.00', $lineas[0]['precio_unitario']);
        self::assertSame('250.50', $lineas[1]['precio_unitario']);
    }

    public function testCatalogPriceChangeDoesNotAlterPastInvoice(): void
    {
        $venta = registrarVenta($this->db, $this->clientId, [$this->productAId => 2]);

        $this->db->query('UPDATE productos SET precio_unitario = 1500.00 WHERE id_producto = ' . $this->productAId);

        self::assertSame('2000.00', totalFactura($this->db, $venta['id']));
        $vista = consultaPreparada($this->db, 'SELECT total_factura FROM vista_total_facturas WHERE id_factura = ?', 'i', $venta['id']);
        self::assertSame('2000.00', $vista[0]['total_factura']);
    }

    public function testInsufficientStockRollsBackEverything(): void
    {
        try {
            registrarVenta($this->db, $this->clientId, [$this->productAId => 1, $this->productBId => 6]);
            self::fail('La venta con stock insuficiente fue aceptada.');
        } catch (DomainException $e) {
            self::assertStringContainsString('existencias', $e->getMessage());
        }

        self::assertSame(0, $this->facturasDelCliente());
        self::assertSame(10, $this->stock($this->productAId));
        self::assertSame(5, $this->stock($this->productBId));
    }

    public function testUnknownProductRollsBackEverything(): void
    {
        $this->expectException(DomainException::class);

        try {
            registrarVenta($this->db, $this->clientId, [$this->productAId => 1, 2147483647 => 1]);
        } finally {
            self::assertSame(0, $this->facturasDelCliente());
            self::assertSame(10, $this->stock($this->productAId));
        }
    }

    public function testUnknownClientIsRejected(): void
    {
        $this->expectException(DomainException::class);
        registrarVenta($this->db, 2147483647, [$this->productAId => 1]);
    }

    public function testInvalidStateIsRejected(): void
    {
        $this->expectException(DomainException::class);
        registrarVenta($this->db, $this->clientId, [$this->productAId => 1], 'Cancelado');
    }

    public function testPendingSaleCanBePaidOnce(): void
    {
        $venta = registrarVenta($this->db, $this->clientId, [$this->productAId => 1], 'Pendiente');

        self::assertSame('Pendiente', $this->estado($venta['id']));
        self::assertSame(9, $this->stock($this->productAId));
        $pendientes = array_column(obtenerFilasReporte($this->db, 'pendientes'), 'id_factura');
        self::assertContains($venta['id'], array_map('intval', $pendientes));

        registrarPagoFactura($this->db, $venta['id']);
        self::assertSame('Pagado', $this->estado($venta['id']));
        self::assertSame(9, $this->stock($this->productAId), 'Registrar el pago no debe mover inventario.');

        $this->expectException(DomainException::class);
        registrarPagoFactura($this->db, $venta['id']);
    }

    public function testCancellationRestoresStockOnlyOnce(): void
    {
        $venta = registrarVenta($this->db, $this->clientId, [$this->productAId => 4, $this->productBId => 1]);

        cancelarFactura($this->db, $venta['id']);

        self::assertSame('Cancelado', $this->estado($venta['id']));
        self::assertSame(10, $this->stock($this->productAId));
        self::assertSame(5, $this->stock($this->productBId));

        try {
            cancelarFactura($this->db, $venta['id']);
            self::fail('Se aceptó cancelar dos veces la misma factura.');
        } catch (DomainException $e) {
            self::assertStringContainsString('ya está cancelada', $e->getMessage());
        }
        self::assertSame(10, $this->stock($this->productAId));
    }

    public function testPendingSaleCanBeCancelled(): void
    {
        $venta = registrarVenta($this->db, $this->clientId, [$this->productBId => 2], 'Pendiente');

        cancelarFactura($this->db, $venta['id']);

        self::assertSame('Cancelado', $this->estado($venta['id']));
        self::assertSame(5, $this->stock($this->productBId));
    }

    public function testCancelledSaleLeavesSalesReports(): void
    {
        $venta = registrarVenta($this->db, $this->clientId, [$this->productAId => 2]);
        cancelarFactura($this->db, $venta['id']);

        $totales = consultaPreparada($this->db, 'SELECT id_factura FROM vista_total_facturas WHERE id_factura = ?', 'i', $venta['id']);
        $unidades = consultaPreparada($this->db, 'SELECT id_producto FROM vista_unidades_productos WHERE id_producto = ?', 'i', $this->productAId);

        self::assertSame([], $totales);
        self::assertSame([], $unidades);
    }

    public function testUnknownInvoiceCannotBeCancelledOrPaid(): void
    {
        foreach (['cancelarFactura', 'registrarPagoFactura'] as $operacion) {
            try {
                $operacion($this->db, 2147483647);
                self::fail($operacion . ' aceptó una factura inexistente.');
            } catch (DomainException $e) {
                self::assertStringContainsString('No existe', $e->getMessage());
            }
        }
    }

    public function testFailedPreparedQueryThrowsSqlExceptionNotError(): void
    {
        // Regresión: imprimir_factura.php cerraba dos veces la misma sentencia y lanzaba Error.
        $this->expectException(mysqli_sql_exception::class);
        consultaPreparada($this->db, 'SELECT columna_inexistente FROM facturas WHERE id_factura = ?', 'i', 1);
    }
}
