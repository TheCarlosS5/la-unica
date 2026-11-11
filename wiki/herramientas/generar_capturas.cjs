/** Capturas reales en Brave sobre una base nueva y exclusiva de pruebas. */
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const { spawn, execFileSync } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');
const net = require('node:net');
const assert = require('node:assert/strict');
const root = path.resolve(__dirname, '../..');
const output = path.join(root, 'wiki/img/actuales');
const database = process.env.DB_TEST_DATABASE || '';
assert.match(database, /^la_unica_test(?:_[A-Za-z0-9_]+)?$/, 'Se exige una base exclusiva la_unica_test_');
const php = process.env.PHP_BINARY || 'php';
const mysql = process.env.MYSQL_BINARY || 'mysql';
const env = { ...process.env };
for (const [key, fallback] of [['HOST','127.0.0.1'],['PORT','3306'],['USERNAME','root'],['PASSWORD',''],['DATABASE',database]]) {
  env['DB_' + key] = process.env['DB_TEST_' + key] ?? fallback;
}
function sql(query) {
  return execFileSync(mysql, ['-h',env.DB_HOST,'-P',env.DB_PORT,'-u',env.DB_USERNAME,'--default-character-set=utf8mb4','-N',database], {
    input: "SET time_zone='-05:00';\n" + query, encoding:'utf8', windowsHide:true, env:{...env,MYSQL_PWD:env.DB_PASSWORD}, stdio:['pipe','pipe','pipe']
  }).trim();
}
const pause = ms => new Promise(resolve => setTimeout(resolve,ms));
async function freePort() {
  const socket = net.createServer();
  await new Promise(resolve => socket.listen(0,'127.0.0.1',resolve));
  const port = socket.address().port;
  await new Promise(resolve => socket.close(resolve));
  return port;
}
(async () => {
  assert.equal(sql('SELECT COUNT(*) FROM facturas'), '0', 'Usar una base nueva sin ventas');
  assert.equal(sql('SELECT COUNT(*) FROM clientes WHERE id_cliente=1900000001'), '0', 'No reutilizar una ejecución anterior');
  assert.equal(sql('SELECT COUNT(*) FROM productos'), '30', 'Importar primero los datos ficticios del repositorio');
  sql("UPDATE productos SET stock=0 WHERE id_producto=29; UPDATE productos SET stock=3 WHERE id_producto=30;");
  const port = await freePort();
  const server = spawn(php,['-S',`127.0.0.1:${port}`,'-t',root],{env,windowsHide:true,stdio:'ignore'});
  let browser;
  const records = [];
  const errors = [];
  try {
    for (let n=0;n<100;n++) {
      try { await fetch(`http://127.0.0.1:${port}/wiki/`); break; } catch { await pause(50); }
    }
    browser = await chromium.launch({executablePath:process.env.BRAVE_BINARY || 'C:/Program Files/BraveSoftware/Brave-Browser/Application/brave.exe',headless:true,ignoreDefaultArgs:['--no-sandbox']});
    const context = await browser.newContext({viewport:{width:1280,height:960},locale:'es-CO',timezoneId:'America/Bogota'});
    const page = await context.newPage();
    page.on('pageerror',error=>errors.push(error.message));
    const base = `http://127.0.0.1:${port}/`;
    async function go(route) {
      const response = await page.goto(base+route,{waitUntil:'networkidle'});
      assert.equal(response.status(),200,route);
      await page.evaluate(()=>document.fonts.ready);
      await page.evaluate(()=>window.scrollTo(0,0));
    }
    async function capture(name, description, options={}) {
      fs.mkdirSync(output,{recursive:true});
      await page.screenshot({path:path.join(output,name+'.jpg'),type:'jpeg',quality:92,animations:'disabled',...options});
      records.push({archivo:name+'.jpg',descripcion:description,ruta:page.url().replace(base,''),ancho:page.viewportSize().width,alto:page.viewportSize().height});
      console.log('Captura: '+name);
    }
    async function submit() {
      await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.locator('form[method="post"] button[type="submit"]').first().click()]);
    }
    async function posted(route, data) {
      // Navegar mediante un formulario real mantiene el contexto de la respuesta y sus recursos.
      const token = await page.locator('input[name="csrf_token"]').first().inputValue();
      await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.evaluate(({route,data,token})=>{
        const form=document.createElement('form');form.method='post';form.action=route;
        for(const [name,value] of Object.entries({...data,csrf_token:token})) {
          const input=document.createElement('input');input.type='hidden';input.name=name;input.value=value;form.appendChild(input);
        }
        document.body.appendChild(form);form.submit();
      },{route:base+route,data,token})]);
    }

    await go('main.php');
    await capture('inicio','Inicio y módulos disponibles');
    await go('php/clientes/crear_cliente.php');
    await page.locator('#id_cliente').fill('1900000001');
    await page.locator('#nombre').fill('Cliente Demostración Wiki');
    await capture('clientes-crear','Documento único y nombre del cliente');
    await submit();
    assert.ok((await page.textContent('body')).includes('Cliente creado correctamente'));
    await capture('clientes-creado','Confirmación real después de registrar al cliente');
    await go('php/clientes/crear_cliente.php');
    await page.locator('#id_cliente').fill('1900000001');
    await page.locator('#nombre').fill('Cliente Demostración Wiki');
    await submit();
    await capture('clientes-duplicado','Validación de documento duplicado');
    await go('php/clientes/ver_clientes.php');
    await capture('clientes-lista','Listado, búsqueda y acciones de clientes');
    await go('php/clientes/ver_clientes.php?buscar=1900000001');
    await capture('clientes-buscar','Resultado de búsqueda por documento');
    await go('php/clientes/editar_cliente.php?id=1900000001');
    await capture('clientes-editar','Edición del nombre sin modificar el documento');
    await go('php/clientes/ver_clientes.php?buscar=1900000001');
    await posted('php/clientes/eliminar_cliente.php',{id_cliente:'1900000001'});
    await capture('clientes-eliminar','Confirmación previa; todavía no se elimina el cliente');

    await go('php/productos/crear_producto.php');
    for(const [name,value] of Object.entries({nombre:'Café demostración 250 g',unidad_medida:'paquete',stock:'20',precio_unitario:'9500.00'})) await page.locator('#'+name).fill(value);
    await capture('productos-crear','Nombre, unidad, stock y precio');
    await submit();
    const product = sql("SELECT id_producto FROM productos WHERE nombre='Café demostración 250 g'");
    assert.ok(product);
    await capture('productos-creado','Confirmación de producto registrado');
    await go('php/productos/ver_productos.php');
    await capture('productos-lista','Catálogo y buscador de productos');
    await go('php/productos/ver_productos.php?buscar=30');
    await capture('productos-stock','Producto de ejemplo con stock bajo');
    await go('php/productos/editar_producto.php?id='+product);
    await capture('productos-editar','Edición del producto y sus existencias');
    await page.locator('#stock').fill('-1');
    await page.locator('form[method="post"]').evaluate(form=>form.noValidate=true);
    await submit();
    await capture('productos-validacion','El servidor rechaza existencias negativas');
    await go('php/productos/ver_productos.php?buscar='+product);
    await posted('php/productos/eliminar_producto.php',{id_producto:product});
    await capture('productos-eliminar','Confirmación previa a la baja de un producto sin ventas');

    // Se completa una venta por UI y se comprueba su persistencia en la base ficticia.
    await go('php/facturas/crear_factura.php');
    await page.locator('#id_cliente').selectOption('1900000001');
    await capture('facturas-cliente','Primer paso: selección del cliente');
    await page.locator('[data-producto-select]').first().selectOption(product);
    await page.locator('[data-cantidad]').first().fill('2');
    await page.locator('[data-cantidad]').first().dispatchEvent('input');
    await page.locator('#agregar-linea-factura').click();
    await page.locator('[data-producto-select]').nth(1).selectOption('1');
    await page.locator('[data-cantidad]').nth(1).fill('3');
    await page.locator('[data-cantidad]').nth(1).dispatchEvent('input');
    await capture('facturas-lineas','Dos productos, cantidades y total estimado',{fullPage:true});
    await page.locator('.factura-resumen').screenshot({path:path.join(output,'facturas-total.jpg'),type:'jpeg',quality:94});
    records.push({archivo:'facturas-total.jpg',descripcion:'Total estimado actualizado por JavaScript',ruta:'php/facturas/crear_factura.php'});
    await submit();
    const invoice=sql('SELECT MAX(id_factura) FROM facturas');
    assert.equal(sql('SELECT stock FROM productos WHERE id_producto='+product),'18');
    await capture('facturas-creada','Venta guardada como Pagado');
    await go('php/facturas/ver_facturas.php');
    await capture('facturas-lista','Listado de facturas y filtros');
    await go('php/facturas/imprimir_factura.php?id='+invoice);
    await capture('facturas-comprobante','Comprobante con nombres y precios históricos');
    await page.emulateMedia({media:'print'});
    await capture('facturas-impresion','Comprobante con sus estilos de impresión');
    await page.emulateMedia({media:'screen'});
    await posted('php/facturas/cancelar_factura.php',{id_factura:invoice});
    await capture('facturas-confirmar-cancelacion','Confirmación de cancelación del mismo día');
    await posted('php/facturas/cancelar_factura.php',{id_factura:invoice,confirmar:'si'});
    assert.equal(sql('SELECT stock FROM productos WHERE id_producto='+product),'20');
    await capture('facturas-cancelada','Estado Cancelado después de reponer el inventario');

    // Una segunda venta vigente permite mostrar totales y unidades distintas de cero.
    await go('php/facturas/crear_factura.php');
    await page.locator('#id_cliente').selectOption('1075234567');
    await page.locator('[data-producto-select]').first().selectOption('1');
    await page.locator('[data-cantidad]').first().fill('4');
    await submit();
    await go('php/reportes/ver_reportes.php');
    await capture('reportes-resumen','Resumen diario y cinco reportes vigentes');
    for (const [file,name] of [['ventas','reportes-ventas'],['unidades_vendidas_por_producto','reportes-unidades'],['existencias_productos','reportes-existencias'],['clientes_y_facturas','reportes-clientes'],['detalle_facturas','reportes-detalle']]) {
      if(file==='detalle_facturas') await page.setViewportSize({width:1600,height:1050});
      await go('php/reportes/'+file+'.php');
      await capture(name,'Reporte vigente: '+file);
      if(file==='detalle_facturas') await page.setViewportSize({width:1280,height:960});
      if(file==='ventas') {
        await page.emulateMedia({media:'print'});
        await capture('reportes-impresion','Vista con estilos de impresión del reporte de ventas');
        await page.emulateMedia({media:'screen'});
      }
    }
    await go('php/historial/acciones.php');
    await capture('historial-acciones','Operaciones realmente realizadas con datos ficticios');
    await go('php/sobre_nosotros/index.php');
    await capture('sobre-nosotros','SRS, manuales, PAC, video y wiki integrados');
    for(const [route,name] of [['manual_usuario','manual-usuario'],['manual_tecnico','manual-tecnico'],['pac','pac'],['srs','srs'],['video_explicativo','video']]) {
      await go('php/sobre_nosotros/'+route+'.php');
      if(route==='video_explicativo') {
        await page.locator('video').evaluate(async video=>{
          video.muted=true;
          await Promise.race([video.play().catch(()=>{}),new Promise(resolve=>setTimeout(resolve,2500))]);
          video.pause();
        });
      }
      await pause(500);
      await capture(name,'Recurso integrado: '+route);
    }
    await page.setViewportSize({width:390,height:844});
    await go('php/clientes/ver_clientes.php?buscar=1900000001');
    await capture('movil-clientes','Consulta de cliente en pantalla estrecha');
    await page.locator('#btn-abrir-drawer').click();
    await pause(300);
    await capture('movil-menu','Menú del aplicativo abierto en celular');
    await go('php/facturas/crear_factura.php');
    await page.locator('#id_cliente').selectOption('1900000001');
    await page.locator('[data-producto-select]').first().selectOption(product);
    await page.locator('[data-cantidad]').first().fill('1');
    await capture('movil-factura','Formulario de venta adaptable; no se guarda esta captura',{fullPage:true});
    assert.deepEqual(errors,[],'No debe haber errores JavaScript al capturar');
    fs.writeFileSync(path.join(output,'capturas.json'),JSON.stringify({origen:'Interfaz real de la-unica-2; datos ficticios en base exclusiva',navegador:'Brave',capturas:records},null,2)+'\n');
    console.log(records.length+' capturas generadas y flujos comprobados.');
  } finally {
    if(browser) await browser.close();
    server.kill();
  }
})().catch(error=>{console.error(error.message);process.exitCode=1;});
