<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="controles">Controles implementados</h2>
<p>Los formularios de cambio validan tokens CSRF de sesión. Las consultas parametrizadas separan datos y SQL. h() escapa valores al producir HTML. Las ventas y cancelaciones usan transacciones, bloqueos y restricciones de base de datos. La configuración privada está ignorada por Git y Apache bloquea recursos internos mediante .htaccess.</p>
<h2 id="diagnostico">Diagnóstico seguro</h2>
<p>Los errores de conexión y configuración devuelven HTTP 500 con un mensaje general. Los detalles quedan en el archivo local de errores y los manejadores globales evitan mostrarlos en la respuesta pública. El historial técnico permite consultarlos aun con SQL detenido; su acceso depende de mantener el sistema local y controlado.</p>
<h2 id="limites">Límites</h2>
<p>No hay usuarios, autenticación ni roles. Los controles no sustituyen un sistema de autorización para Internet. Las pruebas cubren casos definidos; no constituyen una auditoría de penetración ni una certificación general de seguridad.</p>
