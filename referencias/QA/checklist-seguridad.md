# Checklist de Seguridad Básico — La Única

Lista de verificación para revisar que el código no tenga fallas de seguridad evidentes.

- [ ] **Inyección SQL:** Revisar que las consultas a la base de datos usen sentencias preparadas o `mysqli_real_escape_string`, en lugar de concatenar variables directamente.
- [ ] **XSS (Cross-Site Scripting):** Verificar que los datos ingresados por el usuario (como nombres de clientes o productos) se limpien antes de mostrarse en la pantalla.
- [ ] **Validación de Entradas:** Asegurar que el sistema no permita ingresar números negativos en precios o cantidades, ni campos vacíos obligatorios.
- [ ] **Manejo de Errores:** Confirmar que los mensajes de error de la base de datos no se muestren tal cual al usuario en la pantalla.