"""Regresiones HTTP y contención real sobre una base exclusiva de pruebas."""
from pathlib import Path
from concurrent.futures import ThreadPoolExecutor
import contextlib
import http.cookiejar
import json
import math
import os
import re
import shutil
import socket
import subprocess
import tempfile
import threading
import time
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
DB = os.environ.get('DB_TEST_DATABASE', '')
if not re.fullmatch(r'la_unica_test(?:_[A-Za-z0-9_]+)?', DB):
    raise RuntimeError('Se exige una base exclusiva con prefijo la_unica_test')
PHP = os.environ.get('PHP_BINARY') or shutil.which('php')
MYSQL = os.environ.get('MYSQL_BINARY') or shutil.which('mysql')
assert PHP and MYSQL, 'Se requieren PHP y mysql'
WORK = Path(tempfile.mkdtemp(prefix='la-unica-advanced-'))
REPORT = {'concurrencia': [], 'rendimiento': {}, 'regresiones': []}
ENV = os.environ.copy()
for name, default in [('HOST', '127.0.0.1'), ('PORT', '3306'), ('USERNAME', 'root'), ('PASSWORD', ''), ('DATABASE', DB)]:
    ENV['DB_' + name] = os.environ.get('DB_TEST_' + name, default)


def sql(query):
    env = ENV.copy()
    env['MYSQL_PWD'] = ENV['DB_PASSWORD']
    result = subprocess.run([MYSQL, '-h', ENV['DB_HOST'], '-P', ENV['DB_PORT'], '-u', ENV['DB_USERNAME'],
                             '--default-character-set=utf8mb4', '-N', DB],
                            input="SET time_zone='-05:00';\n" + query, text=True, encoding='utf8',
                            capture_output=True, env=env, timeout=30)
    if result.returncode:
        raise RuntimeError(result.stderr)
    return result.stdout.strip()


def check(name, passed):
    REPORT['regresiones'].append({'prueba': name, 'aprobada': bool(passed)})
    assert passed, name
    print('APROBADA: ' + name, flush=True)


class Client:
    def __init__(self, base):
        self.base = base
        self.opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))

    def request(self, path, data=None):
        encoded = None if data is None else urllib.parse.urlencode(data, doseq=True).encode()
        try:
            response = self.opener.open(self.base + path, encoded, timeout=20)
        except urllib.error.HTTPError as error:
            response = error
        with response:
            return response.status, response.read().decode('utf8')

    def token(self):
        code, body = self.request('php/facturas/crear_factura.php')
        match = re.search(r'name="csrf_token" value="([a-f0-9]+)"', body)
        assert code == 200 and match, 'No se pudo obtener CSRF'
        return match.group(1)


@contextlib.contextmanager
def server(overrides=None):
    with socket.socket() as sock:
        sock.bind(('127.0.0.1', 0))
        port = sock.getsockname()[1]
    env = ENV | (overrides or {})
    process = subprocess.Popen([PHP, '-S', f'127.0.0.1:{port}', '-t', str(ROOT)], env=env,
                               stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL,
                               creationflags=0x08000000 if os.name == 'nt' else 0)
    try:
        for _ in range(100):
            if process.poll() is not None:
                raise RuntimeError('El servidor PHP terminó inesperadamente')
            try:
                with socket.create_connection(('127.0.0.1', port), timeout=.1):
                    break
            except OSError:
                time.sleep(.05)
        else:
            raise RuntimeError('El servidor PHP no inició')
        yield Client(f'http://127.0.0.1:{port}/')
    finally:
        process.terminate()
        process.wait(timeout=10)


def race(clients, tokens, path, payloads, table, row_id):
    """Bloquea la fila antes de disparar dos servidores y sesiones independientes."""
    ready, release = WORK / 'locked', WORK / 'release'
    ready.unlink(missing_ok=True)
    release.unlink(missing_ok=True)
    locker = subprocess.Popen([PHP, str(ROOT / 'tests/lock_fixture.php'), table, str(row_id), str(ready), str(release)],
                              env=os.environ.copy(), stdout=subprocess.DEVNULL, stderr=subprocess.PIPE,
                              creationflags=0x08000000 if os.name == 'nt' else 0)
    try:
        for _ in range(100):
            if ready.exists():
                break
            assert locker.poll() is None, 'No se pudo bloquear la fila de prueba'
            time.sleep(.02)
        else:
            raise RuntimeError('No se obtuvo el bloqueo de prueba')
        barrier = threading.Barrier(3)

        def send(i):
            barrier.wait(timeout=5)
            start = time.perf_counter()
            code, body = clients[i].request(path, payloads[i] | {'csrf_token': tokens[i]})
            return code, body, time.perf_counter() - start

        with ThreadPoolExecutor(max_workers=2) as pool:
            futures = [pool.submit(send, i) for i in range(2)]
            barrier.wait(timeout=5)
            # Ambas solicitudes deben esperar por la fila retenida en otra conexión.
            time.sleep(.35)
            release.touch()
            result = [future.result(timeout=20) for future in futures]
        assert all(r[2] >= .25 for r in result), 'Las solicitudes no contendieron por el bloqueo'
        assert locker.wait(timeout=5) == 0, 'La conexión de bloqueo falló'
        return result
    finally:
        release.touch()
        if locker.poll() is None:
            locker.terminate()
            locker.wait(timeout=5)


def main():
    # Rango exclusivo: no se reutilizan identificadores de functional.py ni PHPUnit.
    assert sql('SELECT COUNT(*) FROM clientes WHERE id_cliente BETWEEN 1700000000 AND 1800001000') == '0', 'Usar una base nueva'
    sql("INSERT INTO clientes(id_cliente,nombre) VALUES (1700000001,'Concurrente A'),(1700000002,'Concurrente B')")
    with server() as first, server() as second:
        clients = [first, second]
        tokens = [c.token() for c in clients]
        for round_id in range(10):
            product = 1700000001 + round_id
            sql(f"INSERT INTO productos(id_producto,nombre,unidad_medida,stock,precio_unitario) VALUES({product},'Última unidad {round_id}','unidad',1,1000)")
            sales = race(clients, tokens, 'php/facturas/crear_factura.php',
                         [{'id_cliente': 1700000001 + i, 'id_producto[]': [product], 'cantidad[]': [1]} for i in range(2)],
                         'productos', product)
            assert sorted(r[0] for r in sales) == [200, 409], 'Debe aceptar una venta y rechazar la otra'
            assert sql(f'SELECT stock FROM productos WHERE id_producto={product}') == '0', 'Stock incorrecto'
            assert sql(f'SELECT COUNT(*) FROM detalle_facturas WHERE id_producto={product}') == '1', 'Doble venta'
            invoice = int(sql(f'SELECT id_factura FROM detalle_facturas WHERE id_producto={product}'))
            cancelled = race(clients, tokens, 'php/facturas/cancelar_factura.php',
                             [{'id_factura': invoice, 'confirmar': 'si'}] * 2, 'facturas', invoice)
            assert all(r[0] == 200 for r in cancelled), 'La cancelación debe redirigir al comprobante'
            assert sql(f'SELECT stock FROM productos WHERE id_producto={product}') == '1', 'Reposición duplicada'
            assert sql(f'SELECT estado_factura FROM facturas WHERE id_factura={invoice}') == 'Cancelado'
            assert sql(f"SELECT COUNT(*) FROM log_acciones WHERE modulo='Facturas' AND accion='Cancelar' AND descripcion LIKE 'Factura #{invoice} %'") == '1', 'Doble registro de cancelación'
            REPORT['concurrencia'].append({'ronda': round_id + 1, 'venta_unica': True, 'reposicion_unica': True})
        check('Diez carreras de venta y diez de cancelación con bloqueo real', True)

        # 1000 registros por entidad; inserción en lotes dentro de la base de pruebas.
        for offset in range(0, 1000, 100):
            ids = range(1800000001 + offset, 1800000101 + offset)
            sql('INSERT INTO clientes(id_cliente,nombre) VALUES ' + ','.join(f"({i},'Cliente carga {i}')" for i in ids))
            sql('INSERT INTO productos(id_producto,nombre,unidad_medida,stock,precio_unitario) VALUES ' + ','.join(f"({i},'Producto carga {i}','unidad',100,1250)" for i in ids))
            sql('INSERT INTO facturas(id_factura,id_cliente,nombre_cliente,estado_factura) VALUES ' + ','.join(f"({i},{i},'Cliente carga {i}','Pagado')" for i in ids))
            sql('INSERT INTO detalle_facturas(id_factura,id_producto,nombre_producto,unidad_medida,precio_unitario,cantidad) VALUES ' + ','.join(f"({i},{i},'Producto carga {i}','unidad',1250,1)" for i in ids))
        for module in ['clientes', 'productos', 'facturas']:
            times = []
            for n in range(20):
                identity = 1800000001 + n * 49
                start = time.perf_counter()
                code, body = first.request(f'php/{module}/ver_{module}.php?buscar={identity}')
                times.append(time.perf_counter() - start)
                expected = ('Producto carga ' if module == 'productos' else 'Cliente carga ') + str(identity)
                assert code == 200 and expected in body and 'No se pudo' not in body
            p95 = sorted(times)[math.ceil(.95 * len(times)) - 1]
            REPORT['rendimiento'][module] = {'consultas': len(times), 'registros_cargados': 1000,
                                             'p95_ms': round(p95 * 1000, 2), 'max_ms': round(max(times) * 1000, 2),
                                             'limite_ms': 2000, 'cumple_95_por_ciento': p95 <= 2}
            check(f'{module}: al menos 95 % de 20 búsquedas en menos de 2 s con 1000 registros', p95 <= 2)

        # Las 29 páginas deben renderizar y sus enlaces internos deben existir.
        for file in sorted((ROOT / 'wiki/paginas').rglob('*.php')):
            key = file.relative_to(ROOT / 'wiki/paginas').with_suffix('').as_posix()
            code, body = first.request('wiki/index.php?pagina=' + key)
            assert code == 200 and 'Referencia histórica' not in body, key
            for link in re.findall(r'(?:href|src)="([^"]+)"', body):
                if link.startswith(('http:', 'https:', '#', 'mailto:', 'data:')):
                    continue
                target = (ROOT / 'wiki' / urllib.parse.unquote(link.split('?')[0].split('#')[0])).resolve()
                assert target.exists(), f'Enlace roto {key}: {link}'
        check('Las 29 páginas de wiki y sus recursos locales están disponibles', True)
        code, body = first.request('wiki/index.php?pagina=../../php/config/conexion.local')
        check('Wiki rechaza rutas ajenas al catálogo', code == 404)
        code, body = first.request('wiki/index.php?buscar=%3Cscript%3Ealert(1)%3C/script%3E')
        check('Buscador de wiki escapa entradas HTML', code == 200 and '<script>alert(1)</script>' not in body)

    with server({'DB_PORT': '1', 'DB_USERNAME': 'USUARIO_PRIVADO_NO_VISIBLE'}) as broken:
        code, body = broken.request('main.php')
        check('Fallo SQL responde 500 sin exponer diagnóstico ni usuario', code == 500 and 'No se pudo conectar' in body and
              all(x not in body for x in ['USUARIO_PRIVADO_NO_VISIBLE', 'mysqli', 'Stack trace', str(ROOT), DB, '127.0.0.1']))
        code, body = broken.request('php/historial/errores.php')
        check('Historial de diagnóstico funciona con SQL caído', code == 200 and 'conexion_mysql' in body)
        code, body = broken.request('wiki/index.php')
        check('Wiki funciona con SQL caído', code == 200)
    with server({'DB_PORT': 'puerto-invalido'}) as invalid:
        code, body = invalid.request('main.php')
        check('Configuración inválida responde de forma segura', code == 500 and 'puerto-invalido' not in body)


if __name__ == '__main__':
    try:
        main()
    finally:
        output = Path(os.environ.get('QA_REPORT_PATH', str(WORK / 'advanced-results.json')))
        output.parent.mkdir(parents=True, exist_ok=True)
        output.write_text(json.dumps(REPORT, ensure_ascii=False, indent=2), encoding='utf8')
        print('Evidencia: ' + str(output), flush=True)
