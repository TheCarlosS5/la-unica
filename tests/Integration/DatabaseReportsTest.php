<?php
use PHPUnit\Framework\TestCase;

final class DatabaseReportsTest extends TestCase {
    private mysqli $db;
    protected function setUp(): void {
        $this->db=conexionPruebas();
        $this->db->begin_transaction();
        $this->db->query("INSERT INTO clientes(id_cliente,nombre) VALUES(2000000091,'Cliente actual'),(2000000092,'Sin ventas')");
        $this->db->query("INSERT INTO productos(id_producto,nombre,unidad_medida,stock,precio_unitario) VALUES(2000000091,'Producto actual','unidad',10,9999)");
        $this->db->query("INSERT INTO facturas(id_factura,id_cliente,nombre_cliente,estado_factura) VALUES(2000000091,2000000091,'Cliente histórico','Pagado'),(2000000092,2000000091,'Cliente histórico','Cancelado')");
        $this->db->query("INSERT INTO detalle_facturas(id_factura,id_producto,nombre_producto,unidad_medida,precio_unitario,cantidad) VALUES(2000000091,2000000091,'Producto histórico','unidad',1234.50,2),(2000000092,2000000091,'Producto histórico','unidad',1234.50,5)");
    }
    protected function tearDown(): void { $this->db->rollback(); $this->db->close(); }
    public function testTotalHistoricoExcluyeCanceladas(): void {
        $rows=array_values(array_filter(obtenerFilasReporte($this->db,'total_facturas'),fn($x)=>in_array((int)$x['id_factura'],[2000000091,2000000092],true)));
        self::assertCount(1,$rows); self::assertSame('2469.00',$rows[0]['total_factura']); self::assertSame('Cliente histórico',$rows[0]['cliente']);
    }
    public function testUnidadesExcluyenCanceladas(): void {
        $rows=array_values(array_filter(obtenerFilasReporte($this->db,'unidades_productos'),fn($x)=>(int)$x['id_producto']===2000000091));
        self::assertSame('2',$rows[0]['unidades_facturadas']);
    }
    public function testDetalleConservaDatosHistoricos(): void {
        $rows=array_values(array_filter(obtenerFilasReporte($this->db,'detalle_facturas'),fn($x)=>(int)$x['id_factura']===2000000091));
        self::assertSame('Producto histórico',$rows[0]['producto']); self::assertSame('Cliente histórico',$rows[0]['cliente']); self::assertSame('2469.00',$rows[0]['subtotal']);
    }
    public function testClientesSinVentasSeConservan(): void {
        $rows=array_values(array_filter(obtenerFilasReporte($this->db,'clientes'),fn($x)=>(int)$x['id_cliente']===2000000092));
        self::assertCount(1,$rows); self::assertSame(0,(int)$rows[0]['total_facturas']);
    }
    public function testStockNegativoSeRechaza(): void {
        $this->expectException(mysqli_sql_exception::class); $this->db->query('UPDATE productos SET stock=-1 WHERE id_producto=2000000091');
    }
    public function testClienteConFacturaNoSeElimina(): void {
        $this->expectException(mysqli_sql_exception::class); $this->db->query('DELETE FROM clientes WHERE id_cliente=2000000091');
    }
    public function testPendientesNoAplicable(): void { self::assertSame([],obtenerFilasReporte($this->db,'pendientes')); }
    public function testProcedimientosDelEsquema(): void {
        foreach(["CALL reporte_ventas_por_fecha(NULL,NULL)","CALL reporte_ventas_por_periodo('1900-01-01','2999-12-31','dia')","CALL reporte_unidades_vendidas(NULL,NULL)",'CALL reporte_existencias()'] as $sql){
            self::assertNotEmpty(consultarFilasReporte($this->db,$sql,true));
        }
    }
}
