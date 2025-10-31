# Carpeta `admin`

Esta carpeta contiene los componentes relacionados con la administración del plugin CF7 Option Limiter:

- `class-admin-page.php`: página de configuración dentro del menú de Contact Form 7 para consultar límites, alternar el modo de depuración, mostrar el visor del log en texto plano, verificar el esquema antes de permitir guardados y exponer los nonces utilizados por los formularios ocultos que gestionan las acciones rápidas (liberar o eliminar reglas) sin recargar la vista.
- `class-cf7-editor-panel.php`: integración directa con el editor de Contact Form 7 que permite crear y editar límites sin salir del formulario, imprime los formularios ocultos en el pie global y delega en ellos las acciones de liberar y borrar para evitar que el formulario principal se cierre inesperadamente.
