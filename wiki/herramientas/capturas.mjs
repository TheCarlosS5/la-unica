/**
 * Archivo: capturas.mjs
 * Módulo: Wiki — herramienta de capturas.
 * Propósito: generar las imágenes de la wiki recorriendo La Única en Brave/Chromium sobre una base nueva la_unica_test_, con servidor independiente.
 * Funcionamiento: abre cada pantalla, rellena formularios cuando hace falta, dibuja recuadros numerados
 * sobre los elementos importantes y recorta solo la zona que explica cada paso.
 * Uso:
 *   cd wiki/herramientas
 *   npm install
 *   node capturas.mjs [RUTA_NAVEGADOR] con variables DB_TEST_* y PHP_BINARY
 * Requisitos: la base debe estar recién creada con referencias/db.sql + datos_ejemplo.sql, porque el
 * recorrido registra un cliente, una venta y cancela esa venta.
 */
import { createRequire } from 'node:module';
import { resolve } from 'node:path';
import { spawn } from 'node:child_process';
import net from 'node:net';
const cargar = createRequire(process.env.CAPTURAS_NODE_MODULES ? resolve(process.env.CAPTURAS_NODE_MODULES, '__capturas__.cjs') : import.meta.url);
const { chromium } = cargar('playwright');
const sharp = cargar('sharp');
import { mkdirSync } from 'node:fs';
import { fileURLToPath } from 'node:url';

const basePruebas = process.env.DB_TEST_DATABASE || '';
if (!/^la_unica_test_[a-zA-Z0-9_]+$/.test(basePruebas)) throw new Error('Se exige una base nueva con prefijo la_unica_test_');
const env = { ...process.env };
for (const [clave, defecto] of [['HOST','127.0.0.1'],['PORT','3306'],['DATABASE',basePruebas],['USERNAME','root'],['PASSWORD','']]) env['DB_' + clave] = process.env['DB_TEST_' + clave] ?? defecto;
const puertoLibre = net.createServer();
await new Promise(r => puertoLibre.listen(0, '127.0.0.1', r));
const puerto = puertoLibre.address().port;
await new Promise(r => puertoLibre.close(r));
const BASE = 'http://127.0.0.1:' + puerto;
const servidor = spawn(process.env.PHP_BINARY || 'php', ['-S', '127.0.0.1:' + puerto, '-t', fileURLToPath(new URL('../../', import.meta.url))], { env, windowsHide:true, stdio:'ignore' });
for(let i=0;i<100;i++){const listo=await new Promise(r=>{const c=net.createConnection({host:'127.0.0.1',port:puerto},()=>{c.destroy();r(true);});c.on('error',()=>r(false));});if(listo)break;await new Promise(r=>setTimeout(r,50));}
const NAVEGADOR = process.argv[2] || 'C:/Program Files/BraveSoftware/Brave-Browser/Application/brave.exe';
const SALIDA = fileURLToPath(new URL('../img/', import.meta.url));
mkdirSync(SALIDA, { recursive: true });

const COLOR_MARCA = '#e8590c';
const ESCRITORIO = { width: 1280, height: 800 };
const MOVIL = { width: 390, height: 844 };

let navegador;
try {
navegador = await chromium.launch({
  executablePath: NAVEGADOR,
  headless: true,
  ignoreDefaultArgs: ['--no-sandbox'],
  args: ['--no-first-run', '--disable-extensions', '--hide-scrollbars'],
});
const contexto = await navegador.newContext({viewport:{width:1280,height:800},deviceScaleFactor:2,locale:'es-CO',timezoneId:'America/Bogota'});
const pagina = await contexto.newPage();
pagina.on('dialog', (dialogo) => dialogo.accept());
let vistaActual = ESCRITORIO;
await pagina.setViewportSize(vistaActual);

const esperar = (ms) => new Promise((r) => setTimeout(r, ms));

async function ir(ruta) {
  await pagina.goto(BASE + ruta, { waitUntil: 'networkidle' });
  await pagina.evaluate(() => document.fonts.ready);
}

async function escribir(selector, texto) {
  await pagina.$eval(selector, (campo, valor) => {
    campo.value = valor;
    campo.dispatchEvent(new Event('input', { bubbles: true }));
    campo.dispatchEvent(new Event('change', { bubbles: true }));
  }, String(texto));
}

async function enviar(selectorFormulario) {
  await Promise.all([
    pagina.waitForNavigation({ waitUntil: 'networkidle' }),
    pagina.$eval(selectorFormulario, (f) => f.requestSubmit()),
  ]);
  await pagina.evaluate(() => document.fonts.ready);
}

/**
 * Dibuja las marcas y devuelve el rectángulo que las contiene a todas.
 * Cada marca: { sel, contiene?, dentro?, indice?, margen?, n?, num? ('izquierda'|'derecha'|'esquina'),
 *               texto?, lado? ('derecha'|'izquierda'|'arriba'|'abajo') }.
 */
async function dibujarMarcas(marcas) {
  return pagina.evaluate(({marcas, color}) => {
    document.getElementById('capa-anotaciones')?.remove();
    const capa = document.createElement('div');
    capa.id = 'capa-anotaciones';
    Object.assign(capa.style, { position: 'absolute', left: '0', top: '0', width: '0', height: '0', zIndex: '2147483647', pointerEvents: 'none' });
    document.body.appendChild(capa);

    const buscar = (m) => {
      let candidatos = [...document.querySelectorAll(m.sel)];
      if (m.contiene) candidatos = candidatos.filter((el) => el.textContent.includes(m.contiene));
      let el = candidatos[m.indice || 0];
      if (el && m.dentro) el = el.querySelector(m.dentro);
      if (!el) throw new Error('No se encontró la marca: ' + JSON.stringify(m));
      return el;
    };

    const cajas = [];
    for (const m of marcas) {
      const r = buscar(m).getBoundingClientRect();
      const margen = m.margen ?? 5;
      const x = r.left + scrollX - margen;
      const y = r.top + scrollY - margen;
      const caja = document.createElement('div');
      Object.assign(caja.style, {
        position: 'absolute', left: x + 'px', top: y + 'px',
        width: r.width + margen * 2 + 'px', height: r.height + margen * 2 + 'px',
        border: `3px solid ${color}`, borderRadius: '9px',
        boxShadow: '0 0 0 3px rgba(255,255,255,.85), 0 6px 18px rgba(232,89,12,.35)', boxSizing: 'border-box',
      });
      capa.appendChild(caja);
      cajas.push(caja);

      if (m.n !== undefined) {
        const num = document.createElement('div');
        num.textContent = m.n;
        // Por defecto el número va a la izquierda del recuadro para no tapar la etiqueta del campo.
        const ancho = r.width + margen * 2;
        const alto = r.height + margen * 2;
        const posicion = {
          izquierda: [x - 38, y + alto / 2 - 14],
          derecha: [x + ancho + 10, y + alto / 2 - 14],
          esquina: [x - 13, y - 13],
        }[m.num || 'izquierda'];
        Object.assign(num.style, {
          position: 'absolute', left: posicion[0] + 'px', top: posicion[1] + 'px', width: '28px', height: '28px',
          borderRadius: '50%', background: color, color: '#fff', font: '700 15px/28px system-ui, sans-serif',
          textAlign: 'center', boxShadow: '0 0 0 3px #fff, 0 3px 8px rgba(0,0,0,.3)',
        });
        capa.appendChild(num);
        cajas.push(num);
      }

      if (m.texto) {
        const etiqueta = document.createElement('div');
        etiqueta.textContent = m.texto;
        Object.assign(etiqueta.style, {
          position: 'absolute', background: color, color: '#fff', font: '600 14px/1.3 system-ui, sans-serif',
          padding: '6px 10px', borderRadius: '7px', whiteSpace: 'nowrap', boxShadow: '0 3px 10px rgba(0,0,0,.25)',
        });
        capa.appendChild(etiqueta);
        const e = etiqueta.getBoundingClientRect();
        const w = r.width + margen * 2;
        const h = r.height + margen * 2;
        const lado = m.lado || 'derecha';
        const pos = {
          derecha: [x + w + 12, y + h / 2 - e.height / 2],
          izquierda: [x - e.width - 12, y + h / 2 - e.height / 2],
          arriba: [x, y - e.height - 12],
          abajo: [x, y + h + 12],
        }[lado];
        etiqueta.style.left = pos[0] + 'px';
        etiqueta.style.top = pos[1] + 'px';
        cajas.push(etiqueta);
      }
    }

    let x1 = Infinity, y1 = Infinity, x2 = -Infinity, y2 = -Infinity;
    for (const c of cajas) {
      const r = c.getBoundingClientRect();
      x1 = Math.min(x1, r.left + scrollX); y1 = Math.min(y1, r.top + scrollY);
      x2 = Math.max(x2, r.right + scrollX); y2 = Math.max(y2, r.bottom + scrollY);
    }
    return { x1, y1, x2, y2 };
  }, {marcas, color: COLOR_MARCA});
}

async function rectanguloDe(selector) {
  return pagina.$eval(selector, (el) => {
    const r = el.getBoundingClientRect();
    return { x1: r.left + scrollX, y1: r.top + scrollY, x2: r.right + scrollX, y2: r.bottom + scrollY };
  });
}

/**
 * Toma una captura. opciones:
 *  - marcas: lista de marcas numeradas.
 *  - recorte: selector de la zona a recortar, 'marcas' (alrededor de las marcas) o null (pantalla completa).
 *  - margen: espacio extra alrededor del recorte.
 *  - completa: true para capturar todo el alto de la página.
 */
async function foto(nombre, opciones = {}) {
  const { marcas = [], recorte = null, margen = 24, completa = false, altoMaximo = null } = opciones;
  const alto = await pagina.evaluate(() => document.documentElement.scrollHeight);
  if (completa || recorte) {
    // Se agranda la ventana para que el menú lateral fijo y todo el contenido queden dentro.
    await pagina.setViewportSize({ ...vistaActual, height: Math.max(vistaActual.height, alto) });
    await esperar(200);
  }
  let zona = marcas.length ? await dibujarMarcas(marcas) : null;
  let clip;
  if (recorte) {
    let r = recorte === 'marcas' ? zona : await rectanguloDe(recorte);
    if (zona && recorte !== 'marcas') {
      r = { x1: Math.min(r.x1, zona.x1), y1: Math.min(r.y1, zona.y1), x2: Math.max(r.x2, zona.x2), y2: Math.max(r.y2, zona.y2) };
    }
    const anchoDoc = await pagina.evaluate(() => document.documentElement.scrollWidth);
    const altoDoc = await pagina.evaluate(() => document.documentElement.scrollHeight);
    const x = Math.max(0, r.x1 - margen);
    const y = Math.max(0, r.y1 - margen);
    clip = { x, y, width: Math.min(anchoDoc, r.x2 + margen) - x, height: Math.min(altoDoc, r.y2 + margen) - y };
    if (altoMaximo !== null) clip.height = Math.min(clip.height, altoMaximo);
  }
  await esperar(150);
  const imagen = await pagina.screenshot({ type:'png', clip, fullPage: !clip && completa });
  await sharp(imagen).webp({quality:90}).toFile(SALIDA + nombre + '.webp');
  await pagina.evaluate(() => document.getElementById('capa-anotaciones')?.remove());
  await pagina.setViewportSize(vistaActual);
  console.log('ok', nombre);
}

const menu = (archivo) => `#barra-lateral a[href$="${archivo}"]`;

// ───────────────────────── Inicio y navegación ─────────────────────────
await ir('/terminos.php');
await foto('acceso-terminos');
await pagina.click('#btnSaltar');
await Promise.all([pagina.waitForNavigation({waitUntil:'networkidle'}),pagina.click('#btnAceptar')]);
await ir('/main.php');
await foto('inicio');
await foto('inicio-menu', {
  recorte: 'marcas', margen: 16,
  marcas: [
    { sel: '.sidebar-grupo', indice: 1, n: 1, num: 'derecha', margen: 2 },
    { sel: '.sidebar-grupo', indice: 2, n: 2, num: 'derecha', margen: 2 },
    { sel: '.sidebar-grupo', indice: 3, n: 3, num: 'derecha', margen: 2 },
  ],
});
await foto('inicio-barra-superior', {
  recorte: 'header.topbar-principal', margen: 8,
  marcas: [{ sel: '.topbar-contexto', n: 1, num: 'derecha' }, { sel: '.topbar-meta', n: 2 }],
});
await foto('inicio-tarjetas', {
  recorte: '.modulos-grid', margen: 16,
  marcas: [
    { sel: '.modulos-grid a.boton', contiene: 'Abrir clientes', n: 1, num: 'esquina' },
    { sel: '.modulos-grid a', contiene: 'Crear cliente', n: 2, num: 'esquina' },
  ],
});

// ───────────────────────── Clientes ─────────────────────────
await foto('clientes-menu', {
  recorte: '.sidebar-grupo:nth-of-type(3)', margen: 14,
  marcas: [{ sel: menu('ver_clientes.php'), n: 1, num: 'derecha', margen: 2 }, { sel: menu('crear_cliente.php'), n: 2, num: 'derecha', margen: 2 }],
});
await ir('/php/clientes/ver_clientes.php');
await foto('clientes-lista-columnas', {
  recorte: '.tabla-contenedor', margen: 16,
  marcas: [
    { sel: 'tbody tr', contiene: 'Tienda Don Pepe', dentro: '.insignia-facturas', n: 1, num: 'esquina' },
    { sel: 'tbody tr', contiene: 'Droguería', dentro: '.boton-peligro', n: 2, num: 'esquina' },
    { sel: 'tbody tr', contiene: 'Tienda Don Pepe', dentro: '.etiqueta-bloqueado', n: 3, num: 'esquina' },
  ],
});
await escribir('#buscar-clientes', 'tienda');
await foto('clientes-buscar', {
  recorte: '.filtro-busqueda', margen: 14,
  marcas: [{ sel: '#buscar-clientes', n: 1 }, { sel: '.filtro-busqueda button[type=submit]', n: 2, num: 'esquina' }],
});
await enviar('.filtro-busqueda');
await foto('clientes-buscar-resultado', { recorte: '.tabla-contenedor', margen: 12 });

await ir('/php/clientes/crear_cliente.php');
await escribir('#id_cliente', '1009');
await escribir('#nombre', 'Tienda La Económica');
await foto('clientes-crear-formulario', {
  recorte: '.tarjeta-formulario', margen: 20,
  marcas: [
    { sel: '#id_cliente', n: 1 },
    { sel: '#nombre', n: 2 },
    { sel: '.tarjeta-formulario button[type=submit]', n: 3 },
  ],
});
await enviar('.tarjeta-formulario form');
await foto('clientes-crear-exito', { recorte: '.mensaje.alerta', margen: 16, marcas: [{ sel: '.mensaje.alerta', margen: 3 }] });
await ir('/php/clientes/crear_cliente.php');
await escribir('#id_cliente', '1001');
await escribir('#nombre', 'Cliente repetido');
await enviar('.tarjeta-formulario form');
await foto('clientes-crear-error', {
  recorte: '.tarjeta-formulario', margen: 16,
  marcas: [{ sel: '.alerta-error', n: 1, margen: 3 }, { sel: '#id_cliente', n: 2 }],
});

await ir('/php/clientes/ver_clientes.php');
await foto('clientes-editar-boton', {
  recorte: 'marcas', margen: 60,
  marcas: [{ sel: 'tbody tr', contiene: 'Tienda Don Pepe', dentro: 'a.editar', n: 1 }],
});
await ir('/php/clientes/editar_cliente.php?id=1001');
await foto('clientes-editar-formulario', {
  recorte: '.tarjeta-formulario', margen: 20,
  marcas: [
    { sel: '.tarjeta-id-fijo', n: 1 },
    { sel: '#nombre', n: 2 },
    { sel: '.tarjeta-formulario button[type=submit]', n: 3 },
  ],
});

await ir('/php/clientes/ver_clientes.php');
await foto('clientes-eliminar-boton', {
  recorte: 'marcas', margen: 60,
  marcas: [{ sel: 'tbody tr', contiene: 'Droguería', dentro: '.boton-peligro', n: 1 }],
});
await Promise.all([
  pagina.waitForNavigation({ waitUntil: 'networkidle' }),
  pagina.$eval('input[name="id_cliente"][value="1007"]', (i) => i.form.submit()),
]);
await foto('clientes-eliminar-confirmar', {
  recorte: '.panel-confirmacion', margen: 20,
  marcas: [
    { sel: '.tarjeta-registro-eliminar', n: 1 },
    { sel: '.panel-confirmacion-pie a', n: 2, num: 'esquina' },
    { sel: '.panel-confirmacion-pie button', n: 3, num: 'esquina' },
  ],
});

// ───────────────────────── Productos ─────────────────────────
await ir('/main.php');
await foto('productos-menu', {
  recorte: '.sidebar-grupo:nth-of-type(3)', margen: 14,
  marcas: [{ sel: menu('ver_productos.php'), n: 1, num: 'derecha', margen: 2 }, { sel: menu('crear_producto.php'), n: 2, num: 'derecha', margen: 2 }],
});
await ir('/php/productos/ver_productos.php');
await foto('productos-lista-marcas', {
  recorte: '.tabla-contenedor', margen: 16,
  marcas: [
    { sel: 'tbody tr', contiene: 'Panela', dentro: '.insignia-stock', n: 1, num: 'esquina' },
    { sel: 'tbody tr', contiene: 'Arroz', dentro: '.insignia-facturas', n: 2, num: 'esquina' },
    { sel: 'tbody tr', contiene: 'Arroz', dentro: '.etiqueta-bloqueado', n: 3, num: 'esquina' },
    { sel: 'tbody tr', contiene: 'Panela', dentro: '.boton-peligro', n: 4, num: 'esquina' },
  ],
});
await ir('/php/productos/crear_producto.php');
await escribir('#nombre', 'Chocolate Corona');
await escribir('#unidad_medida', 'pastilla');
await escribir('#stock', '50');
await escribir('#precio_unitario', '2900.50');
await foto('productos-crear-formulario', {
  recorte: '.tarjeta-formulario', margen: 20,
  marcas: [
    { sel: '#nombre', n: 1 },
    { sel: '#unidad_medida', n: 2 },
    { sel: '#stock', n: 3, num: 'esquina' },
    { sel: '#precio_unitario', n: 4 },
    { sel: '.tarjeta-formulario button[type=submit]', n: 5 },
  ],
});
await ir('/php/productos/editar_producto.php?id=1');
await foto('productos-editar-formulario', {
  recorte: '.tarjeta-formulario', margen: 20,
  marcas: [
    { sel: '.campo:has(#unidad_medida)', n: 1 },
    { sel: '#stock', n: 2, num: 'esquina' },
    { sel: '#precio_unitario', n: 3 },
  ],
});
await escribir('#stock', '-1');
await pagina.$eval('.tarjeta-formulario form', f => {f.noValidate=true;});
await enviar('.tarjeta-formulario form');
await foto('productos-editar-error', { recorte: '.alerta-error', margen: 16, marcas: [{ sel: '.alerta-error', margen: 3 }] });
await ir('/php/productos/ver_productos.php');
await Promise.all([
  pagina.waitForNavigation({ waitUntil: 'networkidle' }),
  pagina.$eval('input[name="id_producto"][value="7"]', (i) => i.form.submit()),
]);
await foto('productos-eliminar-confirmar', {
  recorte: '.panel-confirmacion', margen: 20,
  marcas: [{ sel: '.panel-confirmacion-pie a', n: 1, num: 'esquina' }, { sel: '.panel-confirmacion-pie button', n: 2, num: 'esquina' }],
});

// ───────────────────────── Facturas: nueva venta ─────────────────────────
await ir('/main.php');
await foto('facturas-menu', {
  recorte: '.sidebar-grupo:nth-of-type(2)', margen: 14,
  marcas: [{ sel: menu('ver_facturas.php'), n: 1, num: 'derecha', margen: 2 }, { sel: menu('crear_factura.php'), n: 2, num: 'derecha', margen: 2 }],
});
await ir('/php/facturas/crear_factura.php');
await pagina.selectOption('#id_cliente', '1008');
await foto('facturas-paso-cliente', {
  recorte: 'marcas', margen: 30,
  marcas: [{ sel: '#id_cliente', n: 1 }],
});
const lineas = () => pagina.$$('[data-linea-factura]');
await (await (await lineas())[0].$('[data-producto-select]')).selectOption('5');
await (await (await lineas())[0].$('[data-cantidad]')).evaluate((c) => { c.value = '4'; c.dispatchEvent(new Event('input', { bubbles: true })); });
await foto('facturas-paso-linea', {
  recorte: '.factura-linea', margen: 24,
  marcas: [
    { sel: '.factura-linea [data-producto-select]', n: 1 },
    { sel: '.factura-linea [data-cantidad]', n: 2, num: 'esquina' },
    { sel: '.factura-linea [data-detalle-producto]', n: 3, margen: 3 },
  ],
});
await pagina.click('#agregar-linea-factura');
await pagina.click('#agregar-linea-factura');
const llenar = async (i, producto, cantidad) => {
  const fila = (await lineas())[i];
  await (await fila.$('[data-producto-select]')).selectOption(producto);
  await (await fila.$('[data-cantidad]')).evaluate((c, v) => { c.value = v; c.dispatchEvent(new Event('input', { bubbles: true })); }, String(cantidad));
};
await llenar(1, '6', 1);
await llenar(2, '3', 2);
await foto('facturas-paso-agregar', {
  recorte: '#lineas-factura', margen: 20,
  marcas: [
    { sel: '#agregar-linea-factura', n: 1 },
    { sel: '.factura-quitar-linea', indice: 2, n: 2, num: 'esquina' },
  ],
});
await foto('facturas-paso-total', {
  recorte: 'marcas', margen: 24,
  marcas: [
    { sel: '.factura-resumen', n: 1, margen: 3 },
    { sel: '#formulario-factura button[type=submit]', n: 2 },
  ],
});
await foto('manual-venta-completa',{recorte:'#contenido-principal',margen:16});
await enviar('#formulario-factura');
await foto('facturas-paso-exito', { recorte: '.mensaje.alerta', margen: 16, marcas: [{ sel: '.mensaje.alerta', margen: 3 }] });

// ───────────────────────── Facturas: buscar, comprobante y cancelar ─────────────────────────
await ir('/php/facturas/ver_facturas.php');
await foto('facturas-lista', { completa: true });
await escribir('#buscar-facturas', 'Esquina');
await foto('facturas-buscar', {
  recorte: '.filtros-facturas', margen: 14,
  marcas: [
    { sel: '#buscar-facturas', n: 1 },
    { sel: '#fecha-facturas', n: 2, num: 'esquina' },
    { sel: '.filtros-facturas button[type=submit]', n: 3, num: 'esquina' },
  ],
});
await ir('/php/facturas/ver_facturas.php');
await foto('facturas-ver-boton', {
  recorte: 'tbody tr:last-child', margen: 16,
  marcas: [
    { sel: 'tbody tr:last-child .estado-factura', n: 1 },
    { sel: 'tbody tr:last-child .boton-factura-detalle', n: 2 },
  ],
});
await ir('/php/facturas/imprimir_factura.php?id=1');
await foto('facturas-comprobante-botones', {
  recorte: '.factura-acciones', margen: 18,
  marcas: [
    { sel: '.factura-acciones button.boton', n: 1, num: 'esquina' },
    { sel: '.factura-acciones button.boton-peligro', n: 2, num: 'esquina' },
    { sel: '.factura-acciones a', n: 3, num: 'esquina' },
  ],
});
await foto('facturas-comprobante-detalle', {
  recorte: '.comprobante-factura', margen: 16,
  marcas: [{ sel: '.comprobante-factura .estado-factura', n: 1 }, { sel: '.comprobante-factura tfoot', n: 2, margen: 2 }],
});
await pagina.emulateMedia({media:'print'});
await ir('/php/facturas/imprimir_factura.php?id=1');
await foto('facturas-impresion', { completa: true });
await pagina.emulateMedia({media:'screen'});

await ir('/php/facturas/imprimir_factura.php?id=9');
await Promise.all([
  pagina.waitForNavigation({ waitUntil: 'networkidle' }),
  pagina.$eval('.form-cancelar-factura', (f) => f.submit()),
]);
await foto('facturas-cancelar-confirmacion', {recorte:'.panel-confirmacion',margen:20});
await enviar('.panel-confirmacion form');
await foto('facturas-cancelada', {
  recorte: 'marcas', margen: 24,
  marcas: [{ sel: '.mensaje.alerta', n: 1, margen: 3 }, { sel: '.comprobante-factura .estado-factura', n: 2 }],
});

// ───────────────────────── Reportes ─────────────────────────
await ir('/php/reportes/ver_reportes.php');
await foto('reportes-menu', {
  recorte: '.sidebar-grupo:nth-of-type(4)', margen: 14,
  marcas: [{ sel: menu('ver_reportes.php'), n: 1, num: 'derecha', margen: 2 }, { sel: menu('existencias_productos.php'), n: 2, num: 'derecha', margen: 2 }],
});
await foto('reportes-resumen', { completa: true });
await foto('reportes-tarjeta', {
  recorte: '.reportes-resumen-card:nth-of-type(1)', margen: 20,
  marcas: [
    { sel: '.reportes-resumen-card:nth-of-type(1) .reportes-resumen-valor', n: 1 },
    { sel: '.reportes-resumen-card:nth-of-type(1) .reportes-resumen-detalle', n: 2 },
    { sel: '.reportes-resumen-card:nth-of-type(1) .modulo-acciones a', n: 3 },
  ],
});
for (const [ruta, archivo] of [
 ['clientes_y_facturas','reporte-clientes-y-facturas'], ['ventas','reporte-total-vigente-por-factura'],
 ['unidades_vendidas_por_producto','reporte-unidades-facturadas-por-producto'],
 ['existencias_productos','reporte-existencias-productos'], ['detalle_facturas','reporte-detalle-facturas']
]) {
 await ir('/php/reportes/'+ruta+'.php');
 await foto(archivo,{recorte:'#contenido-principal',margen:16});
 if (ruta === 'detalle_facturas') await foto('manual-detalle-primeras-filas',{recorte:'.panel-reporte',margen:16,altoMaximo:750});
}
await ir('/php/reportes/ventas.php');
await pagina.emulateMedia({media:'print'});
await foto('reporte-ventas-impresion',{completa:true});
await pagina.emulateMedia({media:'screen'});
await ir('/php/reportes/existencias_productos.php');
await foto('reportes-individual-partes', {
  recorte: '#contenido-principal', margen: 20,
  marcas: [
    { sel: '.encabezado-vista', n: 1, margen: 3 },
    { sel: '.acciones-impresion-reporte', n: 2, margen: 3 },
    { sel: 'tbody tr', indice: 0, n: 3, margen: 1 },
  ],
});

await ir('/php/facturas/imprimir_factura.php?id=2');
await foto('manual-factura-anterior',{recorte:'#contenido-principal',margen:16});
await ir('/php/reportes/ventas.php?ver=dia');
await foto('manual-ventas-dia',{recorte:'#contenido-principal',margen:16});
await ir('/php/historial/acciones.php');
await foto('manual-historial',{recorte:'#contenido-principal',margen:16});
await ir('/php/historial/errores.php?desde=2000-01-01&hasta=2000-01-01');
await foto('manual-errores',{recorte:'#contenido-principal',margen:16});

// ───────────────────────── Sobre nosotros ─────────────────────────
await ir('/php/sobre_nosotros/index.php');
await foto('sobre-nosotros', {
  recorte: '.reportes-resumen-grid', margen: 20,
  marcas: [
    { sel: '.reportes-resumen-card', indice: 1, dentro: 'a.boton', n: 1 },
    { sel: '.reportes-resumen-card', indice: 4, dentro: 'a.boton', n: 2, num: 'esquina' },
    { sel: '.reportes-resumen-card', indice: 5, dentro: 'a.boton', n: 3, num: 'esquina' },
  ],
});
await ir('/php/sobre_nosotros/manual_usuario.php');
await esperar(1500);
await foto('sobre-nosotros-manual');
await ir('/php/sobre_nosotros/video_explicativo.php');
await foto('sobre-nosotros-video');

// ───────────────────────── Celular ─────────────────────────
vistaActual = MOVIL;
await pagina.setViewportSize(vistaActual);
await ir('/main.php');
await foto('movil-boton-menu', {
  recorte: 'marcas', margen: 20,
  marcas: [{ sel: '#btn-abrir-drawer', n: 1, num: 'esquina', texto: 'Abre el menú', lado: 'abajo' }],
});
await pagina.click('#btn-abrir-drawer');
await esperar(400);
await foto('movil-menu', { marcas: [{ sel: '#btn-cerrar-drawer', n: 1 }] });
await ir('/php/clientes/ver_clientes.php');
await foto('movil-clientes');
await ir('/php/facturas/crear_factura.php');
await foto('movil-factura');

} finally {
  try { if (navegador) await navegador.close(); }
  finally { servidor.kill(); }
}
