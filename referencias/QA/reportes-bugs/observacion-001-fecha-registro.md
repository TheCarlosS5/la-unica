# OBS-001 Fecha de registro del cliente

Estado vigente al **11/11/2026: comportamiento anterior sustituido**. Nuevo cliente solicita documento y nombre. SQL asigna la fecha del día; Editar cliente la muestra de solo lectura. No hay un campo de fecha editable ni se documenta permitir fechas pasadas o futuras desde el formulario.

El antecedente de octubre describía una interfaz anterior que aceptaba fecha manual. Sus decisiones y cambios se conservan en el historial; no describen el SRS 1.6 ni la operación de main actual.

Verificación: registrar un cliente ficticio y comprobar fecha_registro=CURDATE() en la conexión UTC-05:00; editar solo el nombre y comprobar que la fecha permanece. La revisión HTTP del 11/11/2026 cubre el alta y la fecha automática.
