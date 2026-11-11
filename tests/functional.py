from pathlib import Path
import urllib.request as U, urllib.parse as P, urllib.error as E, http.cookiejar, re, subprocess, os, json, hashlib

import socket, time, tempfile, shutil
ROOT=Path(__file__).resolve().parents[1]
W=Path(tempfile.mkdtemp(prefix='la-unica-functional-'))
DB=os.environ.get('DB_TEST_DATABASE','')
assert re.fullmatch(r'la_unica_test(?:_[A-Za-z0-9_]+)?',DB), 'Base de pruebas no autorizada'
MYSQL=os.environ.get('MYSQL_BINARY') or shutil.which('mysql')
assert MYSQL, 'Se requiere un cliente mysql disponible'
env=os.environ.copy(); env['MYSQL_PWD']=os.environ.get('DB_TEST_PASSWORD','')
def sql(q):
    assert 'la_unica.' not in q and 'la_unica_2_demo_20261110.' not in q
    p=subprocess.run([MYSQL,'-h',os.environ.get('DB_TEST_HOST','127.0.0.1'),'-P',os.environ.get('DB_TEST_PORT','3306'),'-u',os.environ.get('DB_TEST_USERNAME','root'),'--default-character-set=utf8mb4','-N',DB],input="SET time_zone = '-05:00';\n"+q,text=True,encoding='utf8',capture_output=True,env=env)
    if p.returncode: raise RuntimeError(p.stderr)
    return p.stdout.strip()

results=[]
def check(name, value, req):
    results.append({'prueba':name,'requisitos':req,'resultado':'APROBADA' if value else 'FALLIDA'})
    print(results[-1]['resultado']+': '+name)
    if not value: raise AssertionError(name)

op=U.build_opener(U.HTTPCookieProcessor(http.cookiejar.CookieJar()))
sock=socket.socket();sock.bind(('127.0.0.1',0));port=sock.getsockname()[1];sock.close()
serverenv=os.environ.copy()
for source,target in [('HOST','HOST'),('PORT','PORT'),('USERNAME','USERNAME'),('PASSWORD','PASSWORD'),('DATABASE','DATABASE')]:
    serverenv['DB_'+target]=os.environ.get('DB_TEST_'+source,'')
php=os.environ.get('PHP_BINARY') or shutil.which('php')
server=subprocess.Popen([php,'-S','127.0.0.1:'+str(port),'-t',str(ROOT)],env=serverenv,stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL,creationflags=0x08000000 if os.name=='nt' else 0)
base='http://127.0.0.1:'+str(port)+'/'
for _ in range(50):
    if server.poll() is not None: raise RuntimeError('No se pudo iniciar el servidor de pruebas')
    try:
        with socket.create_connection(('127.0.0.1',port),timeout=.2): break
    except OSError:time.sleep(.1)

def req(path,data=None):
    try:
        r=op.open(base+path,P.urlencode(data,doseq=True).encode() if data is not None else None,timeout=15)
    except E.HTTPError as e: r=e
    return r.status,r.read().decode('utf8',errors='replace')
def token(path):
    code,body=req(path)
    m=re.search(r'name="csrf_token" value="([a-f0-9]+)"',body)
    assert code==200 and m,(path,code)
    return m.group(1)
def post(path,data):
    return req(path,dict(data,csrf_token=csrf))

try:
    pages=['','php/clientes/ver_clientes.php','php/productos/ver_productos.php','php/facturas/ver_facturas.php','php/facturas/crear_factura.php','php/reportes/ver_reportes.php','php/reportes/ventas.php','php/reportes/unidades_vendidas_por_producto.php','php/reportes/existencias_productos.php','php/historial/acciones.php','php/historial/errores.php','php/sobre_nosotros/index.php']
    for page in pages:
        code,body=req(page)
        check('HTTP '+(page or 'inicio'),code==200 and 'Ocurrió un error inesperado' not in body and 'No se pudo conectar' not in body,'RF-01 a RF-16 (disponibilidad)')
    csrf=token('php/clientes/crear_cliente.php')
    code,_=req('php/clientes/crear_cliente.php',{'id_cliente':'2000000001','nombre':'Sin token'})
    check('Rechazo sin CSRF',code==403 and sql('SELECT COUNT(*) FROM clientes WHERE id_cliente=2000000001')=='0','RNF-03')
    post('php/clientes/crear_cliente.php',{'id_cliente':'2000000001','nombre':'Cliente revisión'})
    check('Alta de cliente y fecha automática',sql("SELECT nombre,fecha_registro=CURDATE() FROM clientes WHERE id_cliente=2000000001").endswith('\t1'),'RF-01')
    post('php/clientes/crear_cliente.php',{'id_cliente':'2000000001','nombre':'Duplicado'})
    check('Documento duplicado rechazado',sql('SELECT COUNT(*) FROM clientes WHERE id_cliente=2000000001')=='1','RF-01, RF-11')
    post('php/clientes/crear_cliente.php',{'id_cliente':'2000000002','nombre':'   '})
    check('Nombre vacío rechazado',sql('SELECT COUNT(*) FROM clientes WHERE id_cliente=2000000002')=='0','RF-11')
    post('php/clientes/editar_cliente.php?id=2000000001',{'nombre':'Cliente editado'})
    code,body=req('php/clientes/ver_clientes.php?buscar=Cliente+editado')
    check('Edición y búsqueda de cliente',sql('SELECT nombre FROM clientes WHERE id_cliente=2000000001')=='Cliente editado' and 'Cliente editado' in body,'RF-02')
    post('php/productos/crear_producto.php',{'nombre':'Producto revisión','unidad_medida':'unidad','stock':'10','precio_unitario':'2500.50'})
    pid=int(sql("SELECT id_producto FROM productos WHERE nombre='Producto revisión'"))
    check('Alta de producto con ID automático',pid>0,'RF-03')
    post('php/productos/crear_producto.php',{'nombre':'Inválido','unidad_medida':'unidad','stock':'-1','precio_unitario':'1.00'})
    check('Stock negativo rechazado',sql("SELECT COUNT(*) FROM productos WHERE nombre='Inválido'")=='0','RF-11, RNF-04')
    venta={'id_cliente':'2000000001','id_producto[]':[str(pid),str(pid)],'cantidad[]':['1','2']}
    post('php/facturas/crear_factura.php',venta)
    fid=int(sql('SELECT MAX(id_factura) FROM facturas'))
    check('Venta y descuento de stock',sql(f'SELECT stock FROM productos WHERE id_producto={pid}')=='7' and sql(f'SELECT estado_factura FROM facturas WHERE id_factura={fid}')=='Pagado','RF-05, RF-06, RF-08')
    check('Líneas repetidas agrupadas y total exacto',sql(f'SELECT COUNT(*),SUM(cantidad),SUM(cantidad*precio_unitario) FROM detalle_facturas WHERE id_factura={fid}')=='1\t3\t7501.50','RF-05, RF-07')
    post(f'php/productos/editar_producto.php?id={pid}',{'nombre':'Producto cambiado','unidad_medida':'caja','stock':'7','precio_unitario':'9999.00'})
    post('php/clientes/editar_cliente.php?id=2000000001',{'nombre':'Nombre posterior'})
    code,body=req(f'php/facturas/imprimir_factura.php?id={fid}')
    check('Factura conserva nombres y precio histórico',code==200 and 'Cliente editado' in body and 'Producto revisión' in body and sql(f'SELECT precio_unitario FROM detalle_facturas WHERE id_factura={fid}')=='2500.50','RF-06, RF-07, RF-09')
    code,body=req('php/productos/ver_productos.php?buscar=Producto+cambiado')
    check('Edición y búsqueda de productos','Producto cambiado' in body,'RF-04')
    before=sql('SELECT COUNT(*) FROM facturas')
    code,_=post('php/facturas/crear_factura.php',{'id_cliente':'2000000001','id_producto[]':[str(pid)],'cantidad[]':['9999']})
    check('Venta sin stock revierte todos los cambios',code==409 and sql('SELECT COUNT(*) FROM facturas')==before and sql(f'SELECT stock FROM productos WHERE id_producto={pid}')=='7','RF-06, RF-08, RNF-04')
    post('php/clientes/eliminar_cliente.php',{'id_cliente':'2000000001','confirmar':'si'})
    post('php/productos/eliminar_producto.php',{'id_producto':str(pid),'confirmar':'si'})
    check('Bloqueo de borrado con facturas',sql('SELECT COUNT(*) FROM clientes WHERE id_cliente=2000000001')=='1' and sql(f'SELECT COUNT(*) FROM productos WHERE id_producto={pid}')=='1','RF-12')
    code,body=post('php/facturas/cancelar_factura.php',{'id_factura':str(fid)})
    check('Confirmación previa sin cancelar',code==200 and sql(f'SELECT estado_factura FROM facturas WHERE id_factura={fid}')=='Pagado','RF-10')
    post('php/facturas/cancelar_factura.php',{'id_factura':str(fid),'confirmar':'si'})
    check('Cancelación repone stock',sql(f'SELECT stock FROM productos WHERE id_producto={pid}')=='10' and sql(f'SELECT estado_factura FROM facturas WHERE id_factura={fid}')=='Cancelado','RF-10')
    post('php/facturas/cancelar_factura.php',{'id_factura':str(fid),'confirmar':'si'})
    check('Cancelación repetida no duplica inventario',sql(f'SELECT stock FROM productos WHERE id_producto={pid}')=='10','RF-10, RNF-04')
    post('php/facturas/crear_factura.php',{'id_cliente':'2000000001','id_producto[]':[str(pid)],'cantidad[]':['1']})
    old=int(sql('SELECT MAX(id_factura) FROM facturas'))
    sql(f'UPDATE facturas SET fecha_factura=NOW()-INTERVAL 1 DAY WHERE id_factura={old}')
    code,body=req(f'php/facturas/imprimir_factura.php?id={old}')
    no_cancel_button='action="cancelar_factura.php"' not in body
    post('php/facturas/cancelar_factura.php',{'id_factura':str(old),'confirmar':'si'})
    check('Factura anterior no se puede cancelar',no_cancel_button and sql(f'SELECT estado_factura FROM facturas WHERE id_factura={old}')=='Pagado' and sql(f'SELECT stock FROM productos WHERE id_producto={pid}')=='9','RF-10')
    # A paid sale today provides an exact reference for the report.
    post('php/facturas/crear_factura.php',{'id_cliente':'2000000001','id_producto[]':[str(pid)],'cantidad[]':['1']})
    for group in ['factura','dia','semana','mes','anio']:
        code,body=req('php/reportes/ventas.php?ver='+group)
        check('Reporte Ventas por '+group,code==200 and 'No se pudo' not in body,'RF-13')
    total=sql("SELECT SUM(d.cantidad*d.precio_unitario) FROM facturas f JOIN detalle_facturas d ON f.id_factura=d.id_factura WHERE f.estado_factura='Pagado' AND DATE(f.fecha_factura)=CURDATE()")
    rows=sql('CALL reporte_ventas_por_periodo(CURDATE(),CURDATE(),\'dia\')')
    check('Reporte diario excluye canceladas y coincide con SQL',total=='9999.00' and '\t9999.00\t' in rows,'RF-13')
    units=sql('CALL reporte_unidades_vendidas(CURDATE(),CURDATE())')
    check('Unidades excluyen canceladas','Producto cambiado' in units and '\t9999.00' in units,'RF-13')
    post('php/clientes/crear_cliente.php',{'id_cliente':'2000000002','nombre':'Cliente desechable'})
    post('php/clientes/eliminar_cliente.php',{'id_cliente':'2000000002','confirmar':'si'})
    post('php/productos/crear_producto.php',{'nombre':'Producto desechable','unidad_medida':'unidad','stock':'0','precio_unitario':'0.00'})
    disposable=int(sql("SELECT id_producto FROM productos WHERE nombre='Producto desechable'"))
    post('php/productos/eliminar_producto.php',{'id_producto':str(disposable),'confirmar':'si'})
    check('Eliminación de cliente y producto sin ventas',sql('SELECT COUNT(*) FROM clientes WHERE id_cliente=2000000002')=='0' and sql(f'SELECT COUNT(*) FROM productos WHERE id_producto={disposable}')=='0','RF-12')
    actions=sql('SELECT DISTINCT accion FROM log_acciones').splitlines()
    check('Historial registra crear, editar, eliminar y cancelar',all(x in actions for x in ['Crear','Editar','Eliminar','Cancelar']),'RF-14')
    code,body=req('php/historial/acciones.php?modulo=Facturas')
    check('Historial se consulta con filtro',code==200 and 'Factura #' in body,'RF-14')
    php=os.environ.get('PHP_BINARY') or shutil.which('php')
    files=[f for f in ROOT.rglob('*.php') if 'vendor' not in f.parts and '.git' not in f.parts]
    lint=[subprocess.run([php,'-l',str(f)],capture_output=True).returncode==0 for f in files]
    check(f'Sintaxis de {len(files)} archivos PHP',all(lint),'RNF-08')
except Exception as e:
    results.append({'prueba':'Ejecución interrumpida','resultado':'ERROR','detalle':str(e)})
    raise
finally:
    server.terminate()
    server.wait(timeout=10)
    (W/'resultados-pruebas.json').write_text(json.dumps(results,ensure_ascii=False,indent=2),encoding='utf8')
