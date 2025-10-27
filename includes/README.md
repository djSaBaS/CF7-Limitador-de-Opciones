# Carpeta `includes`

Aquí se encuentran las clases compartidas que conforman el núcleo del plugin:

- `class-db-manager.php`: capa de acceso a datos responsable de crear las tablas, ejecutar migraciones y exponer operaciones CRUD.
- `class-docs-page.php`: renderiza la página de documentación accesible desde el listado de plugins.
- `class-limiter-handler.php`: aplica los límites durante el envío de los formularios y filtra las opciones agotadas en tiempo real.
- `class-logger.php`: gestiona la escritura y rotación de los archivos de log.
- `hooks.php`: registra los hooks generales y prepara los recursos compartidos en administración.
