<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DatabaseReportsTest extends TestCase
{
    private mysqli $db;
    private int $clientId;
    private int $clientWithoutInvoicesId;
    private int $productAId;
    private int $productBId;
    private int $paidInvoiceId;
    private int $pendingInvoiceId;
    private int $cancelledInvoiceId;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var mysqli $connection */
        $connection = $GLOBALS['dbTestConnection'];
        $this->db = $connection;
        $this->db->begin_transaction();

        $this->clientId = random_int(100000000, 1900000000);
        $this->clientWithoutInvoicesId = $this->clientId === 1900000000
            ? 1900000001
            : $this->clientId + 1;

        $stmt = $this->db->prepare(
            'INSERT INTO clientes (id_cliente, nombre) VALUES (?, ?), (?, ?)'
        );
        $name = 'PHPUnit cliente principal';
        $emptyName = 'PHPUnit cliente sin facturas';
        $stmt->bind_param(
            'isis',
            $this->clientId,
            $name,
            $this->clientWithoutInvoicesId,
            $emptyName
        );
        $stmt->execute();
        $stmt->close();

        $this->productAId = $this->insertProduct(
            'PHPUnit producto A',
            30,
            '12.50'
        );
        $this->productBId = $this->insertProduct(
            'PHPUnit producto B',
            8,
            '4.25'
        );

        $this->paidInvoiceId = $this->insertInvoice('Pagado');
        $this->pendingInvoiceId = $this->insertInvoice('Pendiente');
        $this->cancelledInvoiceId = $this->insertInvoice('Cancelado');

        $this->insertDetail($this->paidInvoiceId, $this->productAId, 2);
        $this->insertDetail($this->paidInvoiceId, $this->productBId, 3, '4.25');
        $this->insertDetail($this->pendingInvoiceId, $this->productAId, 1);
        $this->insertDetail($this->cancelledInvoiceId, $this->productAId, 4);
    }

    protected function tearDown(): void
    {
        $this->db->rollback();

        parent::tearDown();
    }

    private function insertProduct(string $name, int $stock, string $price): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO productos (nombre, unidad_medida, stock, precio_unitario)
             VALUES (?, ?, ?, ?)'
        );
        $unit = 'unidad';
        $stmt->bind_param('ssis', $name, $unit, $stock, $price);
        $stmt->execute();
        $id = (int)$this->db->insert_id;
        $stmt->close();

        return $id;
    }

    private function insertInvoice(string $status): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO facturas (id_cliente, estado_factura) VALUES (?, ?)'
        );
        $stmt->bind_param('is', $this->clientId, $status);
        $stmt->execute();
        $id = (int)$this->db->insert_id;
        $stmt->close();

        return $id;
    }

    private function insertDetail(
        int $invoiceId,
        int $productId,
        int $quantity,
        string $price = '12.50'
    ): void {
        $stmt = $this->db->prepare(
            'INSERT INTO detalle_facturas (id_factura, id_producto, cantidad, precio_unitario)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->bind_param('iiis', $invoiceId, $productId, $quantity, $price);
        $stmt->execute();
        $stmt->close();
    }

    private function findRow(array $rows, string $key, int $value): ?array
    {
        foreach ($rows as $row) {
            if ((int)$row[$key] === $value) {
                return $row;
            }
        }

        return null;
    }

    private function assertDatabaseConstraint(
        callable $operation,
        array $acceptedCodes,
        string $message
    ): void {
        try {
            $operation();
            self::fail($message . ': la operación fue aceptada.');
        } catch (mysqli_sql_exception $exception) {
            self::assertContains(
                (int)$exception->getCode(),
                $acceptedCodes,
                $message . ': código SQL inesperado ' . $exception->getCode()
            );
            self::assertSame(
                '23000',
                $exception->getSqlState(),
                $message . ': SQLSTATE inesperado.'
            );
        }
    }

    public function testClientViewIncludesClientsWithAndWithoutInvoices(): void
    {
        $rows = $this->db->query(
            'SELECT id_cliente, nombre, total_facturas
             FROM vista_clientes_total_facturas'
        )->fetch_all(MYSQLI_ASSOC);

        $client = $this->findRow($rows, 'id_cliente', $this->clientId);
        $clientWithoutInvoices = $this->findRow(
            $rows,
            'id_cliente',
            $this->clientWithoutInvoicesId
        );

        self::assertNotNull($client);
        self::assertSame(3, (int)$client['total_facturas']);
        self::assertNotNull($clientWithoutInvoices);
        self::assertSame(0, (int)$clientWithoutInvoices['total_facturas']);
    }

    public function testInvoiceTotalsViewUsesStoredSalePrices(): void
    {
        // Cambiar el catálogo después de la venta no debe alterar el total guardado.
        $this->db->query(
            'UPDATE productos SET precio_unitario = 999.99 WHERE id_producto IN ('
            . $this->productAId . ', ' . $this->productBId . ')'
        );

        $rows = $this->db->query(
            'SELECT id_factura, total_factura FROM vista_total_facturas'
        )->fetch_all(MYSQLI_ASSOC);

        $invoice = $this->findRow($rows, 'id_factura', $this->paidInvoiceId);

        self::assertNotNull($invoice);
        self::assertSame('37.75', (string)$invoice['total_factura']);
    }

    public function testInvoiceTotalsViewExcludesCancelledInvoices(): void
    {
        $rows = $this->db->query(
            'SELECT id_factura FROM vista_total_facturas'
        )->fetch_all(MYSQLI_ASSOC);

        self::assertNotNull($this->findRow($rows, 'id_factura', $this->paidInvoiceId));
        self::assertNotNull($this->findRow($rows, 'id_factura', $this->pendingInvoiceId));
        self::assertNull($this->findRow($rows, 'id_factura', $this->cancelledInvoiceId));
    }

    public function testProductUnitsViewExcludesCancelledInvoices(): void
    {
        $rows = $this->db->query(
            'SELECT id_producto, unidades_facturadas FROM vista_unidades_productos'
        )->fetch_all(MYSQLI_ASSOC);

        $productA = $this->findRow($rows, 'id_producto', $this->productAId);
        $productB = $this->findRow($rows, 'id_producto', $this->productBId);

        // Pagada (2) + pendiente (1); las 4 unidades de la cancelada ya volvieron al inventario.
        self::assertNotNull($productA);
        self::assertSame(3, (int)$productA['unidades_facturadas']);
        self::assertNotNull($productB);
        self::assertSame(3, (int)$productB['unidades_facturadas']);
    }

    public function testClientReportCountsInvoicesOfAllStates(): void
    {
        $rows = obtenerFilasReporte($this->db, 'clientes');
        $client = $this->findRow($rows, 'id_cliente', $this->clientId);
        $withoutInvoices = $this->findRow(
            $rows,
            'id_cliente',
            $this->clientWithoutInvoicesId
        );

        self::assertNotNull($client);
        self::assertSame(3, (int)$client['total_facturas']);
        self::assertNotNull($withoutInvoices);
        self::assertSame(0, (int)$withoutInvoices['total_facturas']);
    }

    public function testDetailStoredProcedureReturnsExpectedSubtotals(): void
    {
        $rows = obtenerFilasReporte($this->db, 'detalle_facturas');
        $invoiceRows = array_values(array_filter(
            $rows,
            fn(array $row): bool => (int)$row['id_factura'] === $this->paidInvoiceId
        ));

        self::assertCount(2, $invoiceRows);

        $subtotals = array_map(
            static fn(array $row): float => (float)$row['subtotal'],
            $invoiceRows
        );
        sort($subtotals);

        self::assertEquals([12.75, 25.00], $subtotals);
    }

    public function testStockStoredProcedureReturnsFixtureProducts(): void
    {
        $rows = obtenerFilasReporte($this->db, 'stock_productos');

        self::assertNotNull($this->findRow($rows, 'id_producto', $this->productAId));
        self::assertNotNull($this->findRow($rows, 'id_producto', $this->productBId));
    }

    public function testStatusStoredProcedureCountsAllThreeStatuses(): void
    {
        $rows = obtenerFilasReporte($this->db, 'por_estado');
        $counts = [];

        foreach ($rows as $row) {
            $counts[$row['estado_factura']] = (int)$row['total_facturas'];
        }

        self::assertGreaterThanOrEqual(1, $counts['Pagado'] ?? 0, 'Debe existir al menos una factura Pagado.');
        self::assertGreaterThanOrEqual(1, $counts['Pendiente'] ?? 0, 'Debe existir al menos una factura Pendiente.');
        self::assertGreaterThanOrEqual(1, $counts['Cancelado'] ?? 0, 'Debe existir al menos una factura Cancelado.');
    }

    public function testPendingStoredProcedureOnlyReturnsPendingInvoices(): void
    {
        $rows = obtenerFilasReporte($this->db, 'pendientes');
        $ids = array_map(
            static fn(array $row): int => (int)$row['id_factura'],
            $rows
        );

        self::assertContains($this->pendingInvoiceId, $ids);
        self::assertNotContains($this->paidInvoiceId, $ids);
        self::assertNotContains($this->cancelledInvoiceId, $ids);

        foreach ($rows as $row) {
            self::assertSame('Pendiente', $row['estado_factura']);
        }
    }

    public function testUnknownReportKeyIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        obtenerFilasReporte($this->db, 'reporte_no_existente');
    }

    public function testDatabaseRejectsNegativeStockWithCheckConstraint(): void
    {
        $this->assertDatabaseConstraint(
            fn() => $this->db->query(
                "INSERT INTO productos (nombre, unidad_medida, stock, precio_unitario)
                 VALUES ('PHPUnit stock negativo', 'unidad', -1, 1.00)"
            ),
            [4025, 3819],
            'CHECK stock >= 0'
        );
    }

    public function testDatabaseRejectsZeroQuantityWithCheckConstraint(): void
    {
        $this->assertDatabaseConstraint(
            fn() => $this->insertDetail($this->paidInvoiceId, $this->productAId, 0),
            [4025, 3819],
            'CHECK cantidad > 0'
        );
    }

    public function testDatabaseRejectsNegativePriceWithCheckConstraint(): void
    {
        $this->assertDatabaseConstraint(
            fn() => $this->db->query(
                "INSERT INTO productos (nombre, unidad_medida, stock, precio_unitario)
                 VALUES ('PHPUnit precio negativo', 'unidad', 1, -1.00)"
            ),
            [4025, 3819],
            'CHECK precio_unitario >= 0'
        );
    }

    public function testDatabaseRejectsInvoiceForUnknownClient(): void
    {
        $stmt = $this->db->prepare(
            'SELECT id_cliente FROM clientes WHERE id_cliente = ?'
        );

        // Buscar un identificador que no exista realmente en la tabla.
        do {
            $unknownClientId = random_int(100000000, 1900000000);
            $stmt->bind_param('i', $unknownClientId);
            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->free_result();
        } while ($exists);

        $stmt->close();

        $this->assertDatabaseConstraint(
            function () use ($unknownClientId): void {
                $insert = $this->db->prepare(
                    "INSERT INTO facturas (id_cliente, estado_factura)
                     VALUES (?, 'Pagado')"
                );
                $insert->bind_param('i', $unknownClientId);
                $insert->execute();
                $insert->close();
            },
            [1452],
            'FK facturas.id_cliente'
        );
    }

    public function testDatabaseRejectsDetailForUnknownProduct(): void
    {
        $unknownProductId = 2147483647;

        $this->assertDatabaseConstraint(
            fn() => $this->insertDetail(
                $this->paidInvoiceId,
                $unknownProductId,
                1
            ),
            [1452],
            'FK detalle_facturas.id_producto'
        );
    }

    public function testDatabaseRejectsDetailForUnknownInvoice(): void
    {
        $unknownInvoiceId = 2147483647;

        $this->assertDatabaseConstraint(
            fn() => $this->insertDetail(
                $unknownInvoiceId,
                $this->productAId,
                1
            ),
            [1452],
            'FK detalle_facturas.id_factura'
        );
    }
}
