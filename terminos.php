<?php
/**
 * ==============================================================================
 * Sistema Web de Gestión Comercial e Inventario «La Única»
 * Archivo: terminos.php
 * Módulo: Portal de Acceso y Términos de Operación
 * Vinculación: Plan de Acción de Mejora Continua (PAC v1.3 - Ficha 3407799)
 * Propósito: Presentar un recuadro interactivo paso a paso («Siguiente / Siguiente»)
 *            que permita visualizar y aceptar las políticas operativas del sistema
 *            de manera ágil, sin forzar lecturas ni tiempos de espera.
 * ==============================================================================
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

date_default_timezone_set('America/Bogota');

function h(mixed $valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Comprobación de estado
$terminosAceptados = !empty($_SESSION['terminos_aceptados']) || (!empty($_COOKIE['la_unica_terminos']) && $_COOKIE['la_unica_terminos'] === 'aceptados');
$fueRechazado = isset($_GET['rechazado']) && $_GET['rechazado'] === '1';

$fechaActualizacion = '11 de noviembre de 2026';
$versionSoftware = 'Versión 2.0 (PAC v1.3 - Ficha 3407799)';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso y Términos de Operación · La Única</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        :root {
            --color-fondo: #faf7f0;
            --color-superficie: #fffdf7;
            --color-superficie-elevada: #ffffff;
            --color-borde: #d6cfb8;
            --color-borde-suave: #e9e3d0;
            --color-texto: #2b2b36;
            --color-texto-secundario: #4d4d5a;
            --color-texto-terciario: #6b6b78;
            --color-primario: #2d6a4f;
            --color-primario-hover: #245641;
            --color-primario-suave: #e2f0e3;
            --color-resaltador: #fff1b8;
            --color-resaltador-fuerte: #ffe27a;
            --color-peligro: #b3301f;
            --color-peligro-suave: #fdf2f0;
            --color-exito: #1f6a3c;
            --color-exito-suave: #e4f4e6;
            --sombra-recuadro: 0 10px 30px rgba(43, 43, 54, 0.08), 0 2px 8px rgba(43, 43, 54, 0.04);
            --radio-recuadro: 14px;
        }

        body {
            background-color: var(--color-fondo);
            color: var(--color-texto);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.55;
            padding: 1.5rem 1rem;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Mosaico de fondo con el logo de La Única rotado a 45 grados (marca de agua sutil) */
        .mosaico-fondo {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            opacity: 0.055; /* casi visible, sutil y elegante */
            user-select: none;
        }

        .contenedor-portal {
            width: 100%;
            max-width: 620px;
            margin: auto;
            position: relative;
            z-index: 1;
        }

        /* Encabezado exterior discreto */
        .cabecera-exterior {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding: 0 0.5rem;
        }

        .marca-exterior {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .icono-marca {
            width: 36px;
            height: 36px;
            background: #ffffff;
            border: 1px solid var(--color-borde);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .icono-marca img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .marca-exterior h1 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--color-primario);
            margin: 0;
            line-height: 1.1;
        }

        .marca-exterior span {
            font-size: 0.8rem;
            color: var(--color-texto-secundario);
        }

        /* Recuadro Pequeño e Interactivo (Wizard Card) */
        .recuadro-terminos {
            background: var(--color-superficie);
            border: 1px solid var(--color-borde);
            border-radius: var(--radio-recuadro);
            box-shadow: var(--sombra-recuadro);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Barra superior del recuadro con progreso */
        .recuadro-topbar {
            background: var(--color-fondo);
            border-bottom: 1px solid var(--color-borde);
            padding: 0.85rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .insignia-pac {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-primario);
            background: var(--color-primario-suave);
            padding: 0.2rem 0.55rem;
            border-radius: 4px;
            border: 1px solid rgba(45, 106, 79, 0.25);
        }

        .indicador-pasos {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--color-texto-secundario);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .puntos-progreso {
            display: flex;
            gap: 5px;
        }

        .punto-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-borde);
            transition: all 0.25s ease;
        }

        .punto-dot.activo {
            background: var(--color-primario);
            width: 20px;
            border-radius: 10px;
        }

        /* Área de contenido del paso */
        .recuadro-cuerpo {
            padding: 1.75rem 1.75rem 1.25rem;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .paso-seccion {
            display: none;
            animation: fadeInPaso 0.25s ease;
        }

        .paso-seccion.activo {
            display: block;
        }

        @keyframes fadeInPaso {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .paso-icono-wrap {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--color-primario-suave);
            color: var(--color-primario);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.85rem;
        }

        .paso-titulo {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--color-texto);
            margin: 0 0 0.5rem;
            line-height: 1.25;
        }

        .paso-texto {
            font-size: 0.94rem;
            color: var(--color-texto);
            margin: 0 0 0.85rem;
            line-height: 1.55;
        }

        .paso-destaque {
            background: var(--color-resaltador);
            border-left: 3px solid var(--color-primario);
            border-radius: 0 6px 6px 0;
            padding: 0.6rem 0.9rem;
            font-size: 0.86rem;
            color: #383424;
            line-height: 1.45;
        }

        .paso-destaque strong {
            color: var(--color-primario);
        }

        /* Barra de navegación inferior del recuadro */
        .recuadro-navegacion {
            background: #ffffff;
            border-top: 1px solid var(--color-borde);
            padding: 0.95rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .enlace-saltar {
            font-size: 0.84rem;
            color: var(--color-texto-terciario);
            text-decoration: underline;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }

        .enlace-saltar:hover {
            color: var(--color-primario);
        }

        .grupo-botones-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.55rem 1.15rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--color-borde);
            background: var(--color-superficie);
            color: var(--color-texto);
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-nav:hover:not(:disabled) {
            background: #eee7d5;
            border-color: var(--color-texto-secundario);
        }

        .btn-nav:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .btn-nav-siguiente {
            background: var(--color-primario);
            color: #ffffff;
            border-color: var(--color-primario);
        }

        .btn-nav-siguiente:hover:not(:disabled) {
            background: var(--color-primario-hover);
            color: #ffffff;
            border-color: var(--color-primario-hover);
            transform: translateY(-1px);
        }

        .btn-nav-aceptar {
            background: var(--color-exito);
            color: #ffffff;
            border-color: var(--color-exito);
            box-shadow: 0 2px 6px rgba(31, 106, 60, 0.25);
        }

        .btn-nav-aceptar:hover {
            background: #17542f;
            border-color: #17542f;
            transform: translateY(-1px);
        }

        .btn-nav-rechazar {
            background: transparent;
            color: var(--color-peligro);
            border-color: transparent;
            font-size: 0.85rem;
        }

        .btn-nav-rechazar:hover {
            background: var(--color-peligro-suave);
            border-color: rgba(179, 48, 31, 0.3);
        }

        /* Alerta de rechazo */
        .alerta-mini {
            background: var(--color-peligro-suave);
            border: 1px solid rgba(179, 48, 31, 0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.86rem;
            color: #5c180e;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Estado "Ya aceptado" */
        .estado-aceptado-card {
            background: var(--color-exito-suave);
            border: 1px solid var(--color-exito);
            border-radius: var(--radio-recuadro);
            padding: 2rem;
            text-align: center;
            box-shadow: var(--sombra-recuadro);
        }

        .estado-aceptado-card h2 {
            color: var(--color-exito);
            font-size: 1.3rem;
            margin: 0 0 0.5rem;
        }

        .estado-aceptado-card p {
            color: var(--color-texto-secundario);
            font-size: 0.92rem;
            margin: 0 0 1.25rem;
        }

        /* Acordeón inferior desplegable para ver el texto formal si se desea */
        .detalle-completo-wrap {
            margin-top: 1.5rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .btn-toggle-detalle {
            background: none;
            border: none;
            color: var(--color-texto-terciario);
            font-size: 0.83rem;
            cursor: pointer;
            text-decoration: underline;
            padding: 0.25rem;
        }

        .btn-toggle-detalle:hover {
            color: var(--color-primario);
        }

        .caja-detalle-completo {
            display: none;
            background: var(--color-superficie);
            border: 1px solid var(--color-borde);
            border-radius: 8px;
            padding: 1.25rem;
            margin-top: 0.75rem;
            text-align: left;
            font-size: 0.86rem;
            max-height: 260px;
            overflow-y: auto;
        }

        .caja-detalle-completo.abierto {
            display: block;
        }

        .pie-portal {
            text-align: center;
            font-size: 0.8rem;
            color: var(--color-texto-terciario);
            margin-top: 1.5rem;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 480px) {
            .recuadro-topbar {
                padding: 0.65rem 0.85rem;
                gap: 0.4rem;
            }
            .insignia-pac {
                font-size: 0.7rem;
                padding: 0.18rem 0.4rem;
                white-space: nowrap;
            }
            .indicador-pasos {
                font-size: 0.76rem;
                white-space: nowrap;
                flex-shrink: 0;
                gap: 0.35rem;
            }
            .puntos-progreso {
                gap: 3px;
            }
            .punto-dot {
                width: 6px;
                height: 6px;
            }
            .punto-dot.activo {
                width: 14px;
            }
            .recuadro-cuerpo {
                padding: 1.15rem 0.95rem;
                min-height: 270px;
            }
            .recuadro-navegacion {
                flex-direction: column;
                align-items: stretch;
                padding: 0.85rem 1rem;
            }
            .grupo-botones-nav {
                justify-content: space-between;
                width: 100%;
            }
        }

        @media print {
            .mosaico-fondo {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<!-- Mosaico repetitivo con el logo de La Única rotado a 45 grados (marca de agua sutil y elegante de fondo) -->
<svg class="mosaico-fondo" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <pattern id="mosaico-logo-unica" width="130" height="130" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
            <image href="assets/img/logo.png" x="25" y="25" width="80" height="80" preserveAspectRatio="xMidYMid meet" />
        </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#mosaico-logo-unica)" />
</svg>

<div class="contenedor-portal">
    <!-- Cabecera exterior sobria -->
    <header class="cabecera-exterior">
        <div class="marca-exterior">
            <div class="icono-marca" aria-hidden="true">
                <img src="assets/img/logo.png" alt="Logo La Única">
            </div>
            <div>
                <h1>La Única</h1>
                <span>Administración local de ventas y catálogo</span>
            </div>
        </div>

        <button type="button" class="btn-nav" onclick="window.print()" title="Imprimir copia">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            <span>Imprimir</span>
        </button>
    </header>

    <?php if ($fueRechazado): ?>
        <div class="alerta-mini" role="alert">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span><strong>Acceso no completado:</strong> Has rechazado los términos. Avanza por los pasos y confirma la aceptación para ingresar al sistema.</span>
        </div>
    <?php endif; ?>

    <?php if ($terminosAceptados): ?>
        <!-- Si ya fueron aceptados para este equipo/navegador -->
        <main class="estado-aceptado-card" role="main">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: #ffffff; color: var(--color-exito); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; border: 2px solid var(--color-exito);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <h2>Términos Aceptados</h2>
            <p>El acuerdo operativo del Plan de Acción de Mejora Continua (PAC v1.3) ya está confirmado en este equipo.</p>
            <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
                <a href="main.php" class="btn-nav btn-nav-siguiente" style="font-size: 0.95rem; padding: 0.65rem 1.4rem;">
                    <span>Entrar a La Única (main.php)</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
                <form action="procesar_terminos.php" method="post" style="display:inline;">
                    <button type="submit" name="rechazar" class="btn-nav btn-nav-rechazar">
                        <span>Revocar y salir</span>
                    </button>
                </form>
            </div>
        </main>
    <?php else: ?>
        <!-- Recuadro Pequeño Interactivo: Siguiente -> Siguiente -> Aceptar -->
        <main class="recuadro-terminos" id="recuadroWizard" role="main">
            <!-- Barra superior con indicador de pasos -->
            <div class="recuadro-topbar">
                <span class="insignia-pac">PAC v1.3 · Ficha 3407799</span>
                <div class="indicador-pasos">
                    <span id="textoPaso">Paso 1 de 5</span>
                    <div class="puntos-progreso" aria-hidden="true">
                        <span class="punto-dot activo" data-dot="1"></span>
                        <span class="punto-dot" data-dot="2"></span>
                        <span class="punto-dot" data-dot="3"></span>
                        <span class="punto-dot" data-dot="4"></span>
                        <span class="punto-dot" data-dot="5"></span>
                    </div>
                </div>
            </div>

            <!-- Cuerpo del Recuadro: Pasos Ágiles -->
            <form action="procesar_terminos.php" method="post" id="formTerminos">
                <div class="recuadro-cuerpo">
                    <!-- Paso 1: Bienvenida y Alcance PAC -->
                    <div class="paso-seccion activo" data-paso="1">
                        <div class="paso-icono-wrap">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                <line x1="8" y1="21" x2="16" y2="21"/>
                                <line x1="12" y1="17" x2="12" y2="21"/>
                            </svg>
                        </div>
                        <h2 class="paso-titulo">1. Bienvenida y Alcance Operativo</h2>
                        <p class="paso-texto">
                            <strong>«La Única»</strong> es una aplicación local para el control de inventario, clientes, ventas y reportes, alineada con los estándares de calidad del <em>Plan de Acción de Mejora Continua (PAC v1.3 - SENA Regional Huila)</em>.
                        </p>
                        <div class="paso-destaque">
                            <strong>Entorno Seguro y Local (AM 03):</strong> Opera exclusivamente en este servidor local bajo Laragon. Tus datos no se transmiten a la nube ni a plataformas externas.
                        </div>
                    </div>

                    <!-- Paso 2: Régimen de Ventas y Comprobantes -->
                    <div class="paso-seccion" data-paso="2">
                        <div class="paso-icono-wrap">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <path d="M7 9h10M7 13h3m3 0h4m-10 3h10"/>
                            </svg>
                        </div>
                        <h2 class="paso-titulo">2. Ventas de Contado y Comprobantes</h2>
                        <p class="paso-texto">
                            Todas las ventas se asientan de contado bajo estado <em>«Pagado»</em>. Los documentos expedidos corresponden a <strong>recibos y comprobantes internos de caja</strong> para soporte de compraventa al cliente.
                        </p>
                        <div class="paso-destaque">
                            <strong>Delimitación Fiscal del PAC:</strong> El software no procesa pasarelas bancarias, ventas a crédito ni facturación electrónica DIAN (RF-05 a RF-10 del SRS).
                        </div>
                    </div>

                    <!-- Paso 3: Stock Transaccional y Cancelación Intradía -->
                    <div class="paso-seccion" data-paso="3">
                        <div class="paso-icono-wrap">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <h2 class="paso-titulo">3. Control de Stock y Cancelaciones</h2>
                        <p class="paso-texto">
                            Al registrar una venta, el inventario se descuenta atómicamente y los precios históricos quedan protegidos para siempre. Para garantizar la cuadratura de caja:
                        </p>
                        <div class="paso-destaque">
                            <strong>Regla Intradía de Calidad:</strong> Solo se admiten cancelaciones de facturas el mismo día de la venta antes de medianoche, reponiendo las existencias en una sola exhibición.
                        </div>
                    </div>

                    <!-- Paso 4: Seguridad, Auditoría y Respaldos -->
                    <div class="paso-seccion" data-paso="4">
                        <div class="paso-icono-wrap">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h2 class="paso-titulo">4. Protección, Auditoría y Respaldo</h2>
                        <p class="paso-texto">
                            Cumplimiento de la <strong>Ley 1581 de 2012</strong> en el tratamiento de cédulas y nombres de clientes. Toda acción operativa se registra en la tabla <code>log_acciones</code>.
                        </p>
                        <div class="paso-destaque">
                            <strong>Acciones PAC AM 01 y AM 09:</strong> Los fallos responden con HTTP 500 sin exponer rutas internas (diagnóstico seguro). El operador debe resguardar copias de seguridad periódicas de MySQL.
                        </div>
                    </div>

                    <!-- Paso 5: Confirmación y Aceptación Final -->
                    <div class="paso-seccion" data-paso="5">
                        <div class="paso-icono-wrap" style="background: var(--color-exito-suave); color: var(--color-exito);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <h2 class="paso-titulo">5. Confirmación y Acceso</h2>
                        <p class="paso-texto">
                            Has visualizado los pilares de operación y aseguramiento de calidad del sistema <strong>«La Única»</strong>. Pulsa el botón verde para ingresar al panel de control y comenzar a operar.
                        </p>
                        <div class="paso-destaque" style="background: #eaf5ed; border-left-color: var(--color-exito);">
                            <strong>Listo para ingresar:</strong> Al aceptar, podrás gestionar clientes, productos, facturar y liquidar reportes sin restricciones.
                        </div>
                    </div>
                </div>

                <!-- Barra de Navegación del Recuadro: Siguiente / Siguiente / Aceptar -->
                <div class="recuadro-navegacion">
                    <button type="button" class="enlace-saltar" id="btnSaltar">Saltar al final</button>

                    <div class="grupo-botones-nav">
                        <button type="button" class="btn-nav" id="btnAnterior" disabled>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"/>
                                <polyline points="12 19 5 12 12 5"/>
                            </svg>
                            <span>Anterior</span>
                        </button>

                        <button type="button" class="btn-nav btn-nav-siguiente" id="btnSiguiente">
                            <span>Siguiente</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </button>

                        <!-- Botón Aceptar que aparece en el paso 5 -->
                        <button type="submit" name="aceptar" id="btnAceptar" class="btn-nav btn-nav-aceptar" style="display: none;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Aceptar y entrar</span>
                        </button>

                        <button type="submit" name="rechazar" class="btn-nav btn-nav-rechazar" formnovalidate title="Salir sin entrar">
                            <span>Rechazar</span>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    <?php endif; ?>

    <!-- Desplegable opcional para quien desee leer los 10 artículos legales completos -->
    <div class="detalle-completo-wrap">
        <button type="button" class="btn-toggle-detalle" id="btnToggleDetalle">
            <span>Ver texto legal completo y trazabilidad PAC (10 artículos)</span>
        </button>

        <div class="caja-detalle-completo" id="cajaDetalleCompleto">
            <h4 style="margin: 0 0 0.5rem; color: var(--color-primario);">Términos Integrales vinculados al PAC v1.3:</h4>
            <p><strong>Art. 1 (Objeto):</strong> Administración local de inventario, catálogo, clientes y ventas de contado bajo el PAC v1.3.</p>
            <p><strong>Art. 2 (Despliegue Local):</strong> Instalación en equipo local (Laragon). Datos desacoplados de la nube pública.</p>
            <p><strong>Art. 3 (Comprobantes de Caja):</strong> Recibos internos para soporte de venta. No reemplaza facturación electrónica DIAN.</p>
            <p><strong>Art. 4 (Ventas de Contado):</strong> Registro como Pagado con débito inmediato de inventario y snapshot histórico de precios.</p>
            <p><strong>Art. 5 (Cancelación Intradía):</strong> Restricción de anulación al mismo día de la venta con reposición única de existencias.</p>
            <p><strong>Art. 6 (Datos Personales):</strong> Protección de cédulas y nombres de clientes conforme a la Ley 1581 de 2012.</p>
            <p><strong>Art. 7 (Auditoría):</strong> Registro inmutable en log_acciones de toda operación de alta, edición y baja.</p>
            <p><strong>Art. 8 (Respaldos AM 01):</strong> Deber del comercio de efectuar volcados periódicos de la base de datos MySQL.</p>
            <p><strong>Art. 9 (Diagnóstico Seguro AM 09):</strong> Errores técnicos tratados en logs/errores.log sin divulgar rutas al navegador.</p>
            <p><strong>Art. 10 (Aceptación y Garantía):</strong> Software suministrado tal cual para operaciones autorizadas según AUTORES.md.</p>
        </div>
    </div>

    <footer class="pie-portal">
        <span>La Única · Almacén y Comercio Local</span> ·
        <span>Vigencia <?= h($fechaActualizacion) ?></span>
    </footer>
</div>

<script>
(function() {
    'use strict';

    var totalPasos = 5;
    var pasoActual = 1;

    var btnAnterior = document.getElementById('btnAnterior');
    var btnSiguiente = document.getElementById('btnSiguiente');
    var btnAceptar = document.getElementById('btnAceptar');
    var btnSaltar = document.getElementById('btnSaltar');
    var textoPaso = document.getElementById('textoPaso');

    function actualizarPaso(nuevoPaso) {
        if (nuevoPaso < 1 || nuevoPaso > totalPasos) return;
        pasoActual = nuevoPaso;

        // Alternar secciones visibles
        var secciones = document.querySelectorAll('.paso-seccion');
        secciones.forEach(function(sec) {
            sec.classList.remove('activo');
            if (parseInt(sec.getAttribute('data-paso'), 10) === pasoActual) {
                sec.classList.add('activo');
            }
        });

        // Actualizar puntos de progreso
        var dots = document.querySelectorAll('.punto-dot');
        dots.forEach(function(dot) {
            var n = parseInt(dot.getAttribute('data-dot'), 10);
            dot.classList.toggle('activo', n === pasoActual);
        });

        // Actualizar texto "Paso X de 5"
        if (textoPaso) {
            textoPaso.textContent = 'Paso ' + pasoActual + ' de ' + totalPasos;
        }

        // Estado de botones Anterior / Siguiente / Aceptar
        if (btnAnterior) {
            btnAnterior.disabled = (pasoActual === 1);
        }

        if (pasoActual === totalPasos) {
            if (btnSiguiente) btnSiguiente.style.display = 'none';
            if (btnAceptar) btnAceptar.style.display = 'inline-flex';
            if (btnSaltar) btnSaltar.style.display = 'none';
        } else {
            if (btnSiguiente) btnSiguiente.style.display = 'inline-flex';
            if (btnAceptar) btnAceptar.style.display = 'none';
            if (btnSaltar) btnSaltar.style.display = 'inline-block';
        }
    }

    if (btnSiguiente) {
        btnSiguiente.addEventListener('click', function() {
            actualizarPaso(pasoActual + 1);
        });
    }

    if (btnAnterior) {
        btnAnterior.addEventListener('click', function() {
            actualizarPaso(pasoActual - 1);
        });
    }

    if (btnSaltar) {
        btnSaltar.addEventListener('click', function() {
            actualizarPaso(totalPasos);
        });
    }

    // Navegación con teclado (Flecha Derecha / Flecha Izquierda)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowRight' && pasoActual < totalPasos) {
            actualizarPaso(pasoActual + 1);
        } else if (e.key === 'ArrowLeft' && pasoActual > 1) {
            actualizarPaso(pasoActual - 1);
        }
    });

    // Desplegable opcional de lectura completa
    var btnToggle = document.getElementById('btnToggleDetalle');
    var cajaDetalle = document.getElementById('cajaDetalleCompleto');
    if (btnToggle && cajaDetalle) {
        btnToggle.addEventListener('click', function() {
            var abierto = cajaDetalle.classList.toggle('abierto');
            btnToggle.textContent = abierto 
                ? 'Ocultar texto legal completo' 
                : 'Ver texto legal completo y trazabilidad PAC (10 artículos)';
        });
    }
})();
</script>

</body>
</html>
