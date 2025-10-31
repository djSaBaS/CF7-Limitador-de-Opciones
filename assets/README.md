# Carpeta `assets`

Esta carpeta agrupa los recursos estáticos que acompañan al plugin:

- `admin.css`: hoja de estilos aplicada en la página de administración y en la pestaña incrustada del editor de Contact Form 7.
- `admin.js`: script principal que gestiona la interfaz de límites tanto en la página general como en el panel del editor. Reutiliza los nonces localizados para recrear formularios ocultos en el pie del administrador, sincroniza los contadores tras liberar usos y silencia temporalmente la alerta de cambios de Contact Form 7 durante las acciones rápidas.
- `index.js`: utilidades ligeras para normalizar los toggles declarados mediante `data-toggle` en los metaboxes.
- `frontend-check.js`: utilidades compartidas en el frontal para bloquear opciones agotadas sin recargar la página.
- `plugins.js`: script que controla el cuadro de confirmación mostrado al desactivar el plugin desde el listado de extensiones.
- `survey.js`: inicializa la pestaña de encuestas sólo cuando el botón existe en la vista actual.
