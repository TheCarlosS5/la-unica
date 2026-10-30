<?php
/**
 * Archivo: paginas.php
 * Módulo: Wiki — catálogo de páginas.
 * Propósito: definir las secciones del menú lateral y las páginas que se pueden abrir.
 * Funcionamiento: cada clave corresponde a un archivo en wiki/paginas/<clave>.php. El orden de este
 * arreglo es el orden del menú y de los botones «Anterior / Siguiente».
 * Punto clave: index.php solo carga páginas que estén aquí, así la URL no puede pedir otros archivos.
 */

function catalogoWiki()
{
    return [
        'Guía de uso' => [
            'guia/introduccion' => [
                'titulo' => 'Bienvenido a la wiki de La Única',
                'menu' => 'Bienvenida',
                'descripcion' => 'Qué es La Única, qué puedes hacer con ella y cómo está organizada esta wiki.',
            ],
            'guia/instalacion' => [
                'titulo' => 'Instalar / Actualizar / Desinstalar',
                'descripcion' => 'Cómo poner a funcionar La Única en Windows con Laragon, cómo actualizarla y cómo quitarla.',
            ],
            'guia/primeros-pasos' => [
                'titulo' => 'Primeros pasos',
                'descripcion' => 'Conoce la pantalla y haz tu primera venta, de principio a fin.',
            ],
            'guia/clientes' => [
                'titulo' => 'Clientes',
                'descripcion' => 'Crear, buscar, editar y eliminar clientes, paso a paso.',
            ],
            'guia/productos' => [
                'titulo' => 'Productos',
                'descripcion' => 'Administrar el catálogo, las existencias y los precios, paso a paso.',
            ],
            'guia/facturas' => [
                'titulo' => 'Facturas',
                'descripcion' => 'Registrar ventas, buscar facturas, imprimir comprobantes y cancelar, paso a paso.',
            ],
            'guia/reportes' => [
                'titulo' => 'Reportes',
                'descripcion' => 'El resumen general y los siete reportes: cómo abrirlos y cómo leerlos.',
            ],
            'guia/sobre-nosotros' => [
                'titulo' => 'Sobre nosotros y ayuda',
                'descripcion' => 'Manual de usuario integrado, video explicativo y esta wiki.',
            ],
            'guia/movil' => [
                'titulo' => 'En celular y tableta',
                'descripcion' => 'Cómo se adapta La Única a pantallas pequeñas y cómo usar el menú.',
            ],
        ],
        'Configuración' => [
            'configuracion/base-de-datos' => [
                'titulo' => 'Conexión a la base de datos',
                'descripcion' => 'Dónde y cómo cambiar host, puerto, usuario, contraseña y nombre de la base.',
            ],
            'configuracion/servidor' => [
                'titulo' => 'Servidor y entorno',
                'descripcion' => 'Apache, PHP, sesiones, zona horaria y URL base de La Única.',
            ],
            'configuracion/personalizar' => [
                'titulo' => 'Personalizar',
                'descripcion' => 'Cambiar logo, colores, fuentes, video explicativo y manual.',
            ],
        ],
        'Solución de problemas' => [
            'guia/problemas' => [
                'titulo' => 'Solución de problemas',
                'descripcion' => 'Errores comunes al instalar o usar La Única y cómo resolverlos.',
            ],
            'guia/faq' => [
                'titulo' => 'Preguntas frecuentes',
                'descripcion' => 'Dudas habituales sobre el funcionamiento de La Única.',
            ],
            'guia/mensajes' => [
                'titulo' => 'Catálogo de mensajes',
                'descripcion' => 'Todos los mensajes que muestra La Única, qué significan y qué hacer.',
            ],
        ],
        'Notas técnicas' => [
            'tecnico/arquitectura' => [
                'titulo' => 'Arquitectura',
                'descripcion' => 'Cómo está construida La Única por dentro, el ciclo de una página y las decisiones de diseño.',
            ],
            'tecnico/estructura' => [
                'titulo' => 'Estructura de carpetas',
                'descripcion' => 'Qué hay en cada carpeta y archivo del repositorio.',
            ],
            'tecnico/base-de-datos' => [
                'titulo' => 'Base de datos',
                'descripcion' => 'Tablas, relaciones, restricciones, vistas y procedimientos de referencias/db.sql.',
            ],
            'tecnico/flujo-de-ventas' => [
                'titulo' => 'Flujo de ventas y cancelaciones',
                'descripcion' => 'Cómo se garantiza que una venta o una cancelación nunca quede a medias.',
            ],
            'tecnico/reportes' => [
                'titulo' => 'Cómo funcionan los reportes',
                'descripcion' => 'Catálogo central, plantilla reutilizable y acceso a vistas y procedimientos.',
            ],
            'tecnico/seguridad' => [
                'titulo' => 'Seguridad',
                'descripcion' => 'Protecciones que implementa La Única y sus límites conocidos.',
            ],
            'tecnico/funciones' => [
                'titulo' => 'Referencia de funciones',
                'descripcion' => 'API de las funciones compartidas de php/config/conexion.php y del módulo de reportes.',
            ],
            'tecnico/interfaz' => [
                'titulo' => 'Interfaz (CSS y JS)',
                'descripcion' => 'Sistema visual, plantilla de páginas, menú adaptable y el script del formulario de facturas.',
            ],
            'tecnico/requisitos' => [
                'titulo' => 'Requisitos (SRS 1.5)',
                'descripcion' => 'Resumen del SRS vigente y matriz de trazabilidad requisito → código.',
            ],
            'tecnico/contribuir' => [
                'titulo' => 'Contribuir al proyecto',
                'descripcion' => 'Flujo de trabajo con ramas, estilo de código y lista de pruebas manuales.',
            ],
            'tecnico/releases' => [
                'titulo' => 'Publicar un release',
                'descripcion' => 'Convención de versiones y pasos para etiquetar y publicar La Única en GitHub (resumen de RELEASE.md).',
            ],
            'tecnico/wiki' => [
                'titulo' => 'Mantener esta wiki',
                'descripcion' => 'Cómo está hecha la wiki en PHP, cómo agregar o editar páginas y cómo regenerar las capturas numeradas.',
            ],
        ],
        'General' => [
            'general/creditos' => [
                'titulo' => 'Créditos',
                'descripcion' => 'Equipo, instructor, recursos y licencias usados en La Única.',
            ],
            'general/novedades' => [
                'titulo' => 'Historial del proyecto',
                'descripcion' => 'Cronología de La Única reconstruida a partir del historial de Git.',
            ],
        ],
    ];
}
