# CF7 Option Limiter

CF7 Option Limiter es un plugin completo para WordPress que amplía Contact Form 7 permitiendo limitar cuántas veces puede seleccionarse una opción concreta en campos de tipo select, radio o checkbox. Incluye gestión visual desde el panel de administración, integración directa en el editor de formularios, sistema de logs rotativos y reinicios automáticos por periodos.

## Características principales

- Creación automática de la tabla `wp_cf7_option_limits` durante la activación con soporte multisitio.
- Página Contact Form 7 → Option Limiter centrada en listar las reglas existentes y enlazar con el editor del formulario correspondiente para editar.
- Pestaña integrada en el editor de Contact Form 7 con formularios basados en desplegables obligatorios que sólo permiten elegir campos y opciones detectadas automáticamente.
- Reinicios automáticos de contadores por hora, día, semana o límite total con mensajes personalizados.
- Los reinicios diarios y semanales respetan la zona horaria configurada en WordPress, evitando desajustes por horarios de verano o desfases personalizados.
- Gestión simplificada basada exclusivamente en límites globales para cada formulario, eliminando excepciones por página y unificando la lógica de cómputo.
- Control granular para ocultar automáticamente las opciones agotadas o mantenerlas visibles con un aviso que impide nuevas selecciones.
- Tabla incrustada en el editor que muestra de un vistazo si cada regla oculta las opciones agotadas para validar rápidamente la configuración.
- Las acciones del panel incrustado replican la iconografía accesible (editar, liberar y eliminar) y permiten liberar manualmente un uso desde el propio editor cuando el contador es superior a cero.
- Filtrado por formulario y paginación de diez elementos en la tabla administrativa para localizar límites con rapidez.
- Acciones rápidas en la tabla administrativa representadas mediante iconos (editar, liberar y eliminar) con ayudas contextuales accesibles; el icono de desbloqueo permite liberar manualmente un uso cuando un participante cancela su elección, reabriendo al instante un hueco sin modificar el límite máximo configurado y permaneciendo habilitado únicamente mientras el contador sea superior a cero para evitar confusiones.
- Tras liberar un uso o eliminar una regla desde la tabla administrativa se conserva el filtro aplicado y la página actual, evitando perder el contexto cuando se revisan lotes amplios de límites.
- Ejecución automática de migraciones de base de datos cuando la versión del plugin se actualiza, evitando reactivar la extensión tras desplegar cambios.
- Auditoría automática de columnas críticas que corrige la ausencia de `hide_exhausted` en instalaciones MySQL 5.7, garantizando que los límites se guarden correctamente incluso si una actualización previa falló.
- Comprobación en tiempo real mediante AJAX que oculta opciones agotadas incluso si varios usuarios abren el formulario simultáneamente, realiza una comprobación inicial al cargar el formulario y bloquea el envío cuando la opción seleccionada se agotó sin mostrar aún la advertencia, interceptando el `submit` en fase de captura antes de que Contact Form 7 lance su petición.
- Los mensajes dinámicos se muestran como una pastilla con fondo rojo muy suave y contorno contrastado para llamar la atención sin resultar invasivos, manteniendo una transición suave cuando aparecen o desaparecen y ocultando automáticamente los contenedores vacíos para evitar resaltes rojos sin texto al cargar el formulario.
- Logs en texto plano almacenados en `wp-content/uploads/cf7-option-limiter/cf7-option-limiter.log` con rotación automática a 1 MB, mensajes de diagnóstico cuando el servidor no puede escribirlos y visor integrado en la parte inferior de la página principal para revisar los últimos eventos sin salir del panel; cada guardado documenta todas las etapas (recepción, sanitización, consulta, persistencia y resultado) mediante un identificador de traza compartido para reconstruir el flujo completo.
- Validación temprana en el guardado administrativo que bloquea peticiones sin formulario, campo u opción y registra el rechazo en el log cuando el modo depuración está activo para simplificar el diagnóstico.
- El visor administrativo del log emplea texto negro sobre un fondo claro para mantener la legibilidad incluso cuando otros plugins inyectan estilos personalizados.
- Conmutador visible en la página principal que activa un modo de depuración exhaustivo o mantiene un log mínimo centrado en operaciones críticas.
- El botón **Guardar límite** de la pestaña integrada utiliza una petición AJAX autenticada que guarda la regla y actualiza la tabla sin recargar la página, mostrando avisos accesibles de éxito o error; sólo si la llamada falla por red recurre automáticamente al envío tradicional mediante `admin-post.php`, reutilizando el formulario oculto como respaldo.
- Los formularios ocultos que sirven de respaldo se imprimen ahora en el pie global del administrador para no interferir con el formulario principal de Contact Form 7, y el script administrativo los localiza o recrea dinámicamente con los nonces correspondientes cuando la vista no los ha recibido todavía.
- El botón de **Liberar un uso** de la pestaña incrustada delega ahora su envío en el formulario oculto global, evitando anidar formularios dentro del editor de Contact Form 7 y permitiendo que el botón de guardar del formulario principal vuelva a funcionar tras liberar plazas manualmente.
- Al eliminar reglas desde la pestaña incrustada, el plugin desactiva temporalmente la alerta genérica del editor de Contact Form 7 para evitar avisos de cambios sin guardar y recuerda que es necesario guardar el formulario principal tras confirmar la eliminación; si la navegación no se completa, el aviso se restablece automáticamente para que el formulario principal vuelva a guardarse con normalidad.
- Los desplegables deben completarse en cadena y el botón **Guardar límite** permanece deshabilitado hasta que exista un formulario, campo y opción válidos; si falta algún dato, se emite un aviso accesible y el foco se desplaza automáticamente al control pendiente.
- Detección resiliente de formularios incluso cuando `wpcf7_contact_forms()` no está disponible gracias a una consulta de respaldo con `WP_Query`.
- Acceso directo a la documentación desde el listado de plugins y desde el editor de Contact Form 7 para resolver dudas sin abandonar el flujo de trabajo.
- Confirmación interactiva al desactivar o borrar el plugin que permite eliminar en bloque los límites almacenados en la base de datos cuando se desee una limpieza total.
- Al continuar con la desactivación o el borrado se muestra una alerta informativa avisando que las reglas configuradas dejarán de aplicarse hasta volver a activar el plugin, evitando sorpresas al personal editorial.
- Persistencia protegida de la preferencia de limpieza, exigiendo permisos para gestionar plugins y un nonce válido antes de actualizar la opción almacenada.

## Instalación

1. Comprime la carpeta `cf7-option-limiter` en un archivo ZIP.
2. Sube el ZIP desde el administrador de plugins de WordPress (`Plugins → Añadir nuevo → Subir plugin`).
3. Activa el plugin. Durante la activación se creará la tabla personalizada y se aplicarán actualizaciones de esquema si son necesarias.
4. Accede a **Contact Form 7 → Option Limiter** para crear o modificar reglas de límite.
5. Abre el formulario deseado en Contact Form 7 y utiliza la pestaña **Limitador de opciones** para crear o editar reglas directamente.

## Proceso de actualización del plugin

1. Registra los cambios realizados en `versiones.md` y actualiza la constante `CF7_OPTION_LIMITER_VERSION` junto con la cabecera del plugin para mantener sincronizada la numeración visible en WordPress.
2. Genera un nuevo archivo ZIP con la carpeta completa del plugin tras verificar los ajustes en un entorno de pruebas.
3. Desde el administrador de WordPress accede a **Plugins → Añadir nuevo → Subir plugin**, selecciona el nuevo ZIP y confirma la opción **Reemplazar la versión actual con la subida** cuando WordPress detecte que el plugin ya existe.
4. Una vez finalizada la sustitución, revisa la página **Contact Form 7 → Option Limiter** y la pestaña incrustada del formulario para confirmar que los selectores cargan los campos y opciones correctamente.
5. Si el flujo de actualización requiere limpiar datos antiguos, desactiva el plugin y confirma el borrado cuando aparezca el aviso; los registros almacenados se eliminarán sin necesidad de acceder manualmente a la base de datos.

## Requisitos

- PHP 8.0 o superior.
- WordPress 6.0 o superior.
- Contact Form 7 activo.

## Desarrollo

- Los archivos SQL se encuentran en `sql/`. Cualquier ajuste estructural futuro debe añadirse como un nuevo script incremental siguiendo el ejemplo `sql/update_001_rename_last_reset.sql`.
- El logger se ubica en `wp-content/uploads/cf7-option-limiter/cf7-option-limiter.log`, se crea automáticamente si la carpeta no existe y muestra diagnósticos en la interfaz cuando el servidor no puede escribirlo.
- Durante la inicialización se compara la firma del archivo `sql/create_table.sql` con la registrada en la base de datos y se ejecuta `dbDelta` automáticamente si la tabla necesita sincronizarse sin requerir reactivar el plugin.
- Los estilos y scripts administrativos residen en `assets/` y están comentados para facilitar su mantenimiento.
- El script `assets/index.js` normaliza cualquier atributo `data-toggle` declarado en los metaboxes del editor (por ejemplo Redirect), admite listas separadas por comas, respeta selectores por clase sin anteponer `#` y aplica el estado inicial para evitar parpadeos.
- El script `assets/survey.js` espera a que el DOM esté listo antes de adjuntar eventos sobre `#survey-tab` y expone la función en `window.configureSurveyTabListener` para mantener compatibilidad con integraciones previas sin disparar errores cuando el botón no existe.
- El script `assets/frontend-check.js` ejecuta las verificaciones en vivo coordinándose con `includes/class-limiter-handler.php`, que expone el endpoint AJAX y valida los envíos en Contact Form 7.
- La hoja `assets/frontend.css` se encola tanto en `wp_enqueue_scripts` como en `wpcf7_enqueue_scripts` y, adicionalmente, se fuerza su carga durante `init` para garantizar que los avisos siempre dispongan del CSS incluso si otros hooks no se ejecutan.
- Todas las funciones del proyecto incluyen bloques de documentación estandarizados con una sección **Explicación** que resume la tarea principal, el flujo interno y la relación con los parámetros y valores devueltos, facilitando auditorías técnicas rápidas.
- Cuando se generen campos hidden repetidos como `cf7_option_limiter_release_nonce` o `nonce` dentro de listados o formularios dinámicos, sustituye los IDs por clases o genera sufijos únicos (por ejemplo `cf7_option_limiter_release_nonce-<rule_id>`) manteniendo el mismo `name`; de esta forma se respeta el estándar HTML y se eliminan los avisos de duplicidad sin alterar la lógica existente.

## Interacción entre la página general y el editor de Contact Form 7

El panel **Contact Form 7 → Option Limiter** actúa como panel de consulta: muestra todas las reglas registradas y ofrece enlaces directos hacia el formulario correspondiente para editar desde su propio editor. Al pulsar en **Editar en el formulario**, se abre el editor de Contact Form 7 con la pestaña del limitador seleccionada y la regla cargada automáticamente, de modo que toda la gestión se realiza sin salir del flujo habitual del formulario.

Dentro del editor, la pestaña **Limitador de opciones** utiliza desplegables dependientes que sólo permiten seleccionar campos y opciones detectados automáticamente en el formulario. De esta forma se evita introducir valores a mano, se minimizan errores tipográficos y se acelera la configuración de límites. El formulario incluye controles para guardar y cancelar ediciones, precarga automática de la regla seleccionada desde la tabla general y un listado lateral con las reglas existentes del formulario para facilitar revisiones rápidas.

Si una regla hace referencia a un campo u opción que ya no existen en el formulario, la interfaz muestra etiquetas temporales claramente identificadas, mantiene los selectores habilitados y añade avisos accesibles para que la regla pueda corregirse o eliminarse sin perder la información original.

Se han eliminado las excepciones por página y cualquier integración con Elementor, simplificando la base de datos y centrando el flujo en límites globales consistentes. Los contadores y reinicios se aplican exclusivamente sobre la tabla principal, de manera que todos los formularios comparten una lógica uniforme y más predecible.


## Manual de uso para usuarios finales

Hemos incluido un documento específico con instrucciones de uso paso a paso, preguntas frecuentes y ejemplos prácticos. Puedes consultarlo en [manual_usuario.md](./manual_usuario.md) para conocer cómo crear reglas, filtrar el listado, liberar manualmente usos desde la tabla o la pestaña incrustada y decidir si las opciones agotadas deben seguir mostrándose en el formulario.


## Pruebas automáticas y CI

- Ejecuta `php tests/run-tests.php` para validar la creación de límites, la priorización de reglas globales, la eliminación de excepciones por página y los flujos principales del limitador.
- Este conjunto de pruebas también verifica que la preferencia de limpieza sólo se actualice cuando la petición incluye un nonce válido y el usuario actual puede gestionar plugins.
- El repositorio incluye un flujo de GitHub Actions que lanza estas pruebas en cada push o pull request para mantener la calidad del código.
- Antes de ejecutar las pruebas en CI se recorre cada archivo con `php -l` para detectar errores de sintaxis como el que impedía activar el plugin en el pipeline.
- Se añadió una simulación completa de activación que comprueba la creación de tablas, la ausencia de errores fatales y la correcta inicialización de los paneles administrativos sin dependencias externas.
- Las pruebas incluyen un escenario específico que simula el guardado administrativo con datos incompletos, verifica que se rechaza antes de tocar la base de datos y comprueba la presencia del registro `limit_admin_rejected` en el log cuando la depuración está activa.


## 📄 Licencia
Este proyecto está destinado a ser utilizado exclusivamente dentro de la empresa.

## 📬 Contacto
Autor: Juan Antonio Sánchez Plaza
📧 juanantoniosanchezplaza@hotmail.com 📧 jasanchez@humanitaseducacion.com

## 💙 Agradecimientos
A todos los compañeros de Humanitas Centros Educativos (HCE) y Consultora de Educación y Sistemas (CEYS), en especial al equipo de Sistemas (Juan Fernández y David Durán) y a las compañeras de MKT por su colaboración y pruebas.
