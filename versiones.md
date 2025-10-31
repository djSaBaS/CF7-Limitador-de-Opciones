# Control de Versiones

# v1.0.43
– `admin/class-cf7-editor-panel.php` sustituye el formulario incrustado de liberación por un botón con atributos de datos y añade un formulario oculto dedicado en el pie para evitar que el formulario principal de Contact Form 7 se cierre prematuramente.
– `assets/admin.js` sincroniza el nuevo formulario oculto de liberación, recrea su estructura con el nonce localizado y envía la petición manual sin perder las salvaguardas del editor.
– `admin/class-admin-page.php` expone el nonce `cf7_option_limiter_release` en la localización JavaScript para que el script pueda recrear formularios seguros incluso cuando no se imprimen desde PHP.
– `tests/run-tests.php` verifica que el botón de liberar ya no imprime `<form>` anidados, comprueba la recreación del formulario oculto y exige la presencia del nuevo nonce localizado junto a los campos adicionales.
– `README.md` documenta que la liberación manual delega en el formulario oculto global, garantizando que el botón de guardar del formulario principal vuelva a funcionar tras devolver plazas.

# v1.0.42
– `includes/hooks.php` registra los nuevos scripts `assets/index.js` y `assets/survey.js`, y los encola automáticamente en las pantallas del editor de Contact Form 7 para que los toggles y la pestaña de encuestas funcionen sin errores de consola.
– `assets/index.js` encapsula helpers `qs/qsa`, normaliza `data-toggle` evitando prefijos `#` incorrectos sobre clases, admite listas separadas por comas y aplica el estado inicial de los campos para prevenir parpadeos.
– `assets/survey.js` espera al evento `DOMContentLoaded`, expone `window.configureSurveyTabListener` y sólo registra listeners cuando existe `#survey-tab`, evitando errores al cargar formularios sin la pestaña de encuestas.
– `tests/run-tests.php` valida el registro y encolado de los nuevos scripts, inspecciona su contenido para impedir regresiones y mantiene el control de versiones con `CF7_OPTION_LIMITER_VERSION`.
– `README.md` y `assets/README.md` documentan los nuevos assets y recomiendan sustituir IDs duplicados por clases o sufijos únicos para respetar el estándar HTML.

# v1.0.41
– `admin/class-cf7-editor-panel.php` delega la impresión de los formularios ocultos en `admin_footer`, evitando interferir con el formulario principal del editor de Contact Form 7 y manteniendo la marca interna que decide cuándo deben mostrarse.
– `admin/class-admin-page.php` expone en la localización JavaScript la URL de `admin-post.php` y los nonces de guardado y borrado para permitir que el script recree los formularios ocultos con credenciales válidas.
– `assets/admin.js` garantiza la existencia de los formularios ocultos localizándolos en su nueva posición o construyéndolos dinámicamente con los nonces localizados, actualizando las referencias antes de cada sincronización y manteniendo los flujos de guardado y borrado.
– `tests/run-tests.php` cubre la nueva lógica verificando que `print_hidden_forms` sólo active la bandera, que `render_hidden_forms` imprima el marcado completo, y que la localización incluya la URL tradicional junto a los nonces requeridos.
– `README.md` documenta la reubicación de los formularios ocultos y el soporte dinámico del script para mantener operativo el flujo de guardado en el editor.

# v1.0.40
– `assets/admin.js` sustituye la eliminación directa de manejadores `beforeunload` por un listener en captura que bloquea la alerta sólo durante la operación y se restaura automáticamente cuando la navegación no continúa, permitiendo guardar de nuevo los cambios generales del formulario.
– `assets/admin.js` restablece la alerta del editor cuando la validación del formulario oculto falla o el envío queda bloqueado, evitando que la protección quede deshabilitada en sesiones posteriores.
– `README.md` amplía la nota sobre la confirmación de borrado para dejar claro que el aviso se reactiva si la página permanece en el editor, garantizando que el equipo pueda seguir guardando Contact Form 7 sin interrupciones.

# v1.0.39
– `assets/admin.js` incorpora el helper `suppressEditorBeforeUnloadWarning()` para silenciar temporalmente la alerta genérica del editor, lo reutiliza antes de los envíos programáticos y restablece los manejadores cuando no se completa la operación.
– `admin/class-admin-page.php` amplía el mensaje `deleteConfirm` recordando que tras eliminar una regla es necesario guardar el formulario principal.
– `admin/class-cf7-editor-panel.php` reutiliza la cadena ampliada en `data-confirm` para que el botón de borrado incrustado muestre la advertencia actualizada.
– `README.md` documenta que el aviso del editor deja de mostrarse al borrar reglas desde la pestaña de Contact Form 7 y que sigue siendo necesario guardar el formulario principal.

# v1.0.38
– `includes/class-db-manager.php` calcula los reinicios diarios y semanales con `DateTimeImmutable` y `wp_timezone()`, eliminando dependencias del desfase manual `gmt_offset`.
– `tests/run-tests.php` incorpora pruebas que fuerzan desfases positivos y negativos para comprobar que `should_reset` detecta cambios de día y semana con la zona horaria del sitio.
– `README.md` aclara que los reinicios automáticos respetan la zona horaria configurada en WordPress, garantizando resultados coherentes con horarios de verano.

# v1.0.37
– `includes/class-limiter-handler.php` fuerza el encolado de `frontend.css` y su script compañero durante `init`, utiliza la versión global del plugin para cache busting y mantiene la carga habitual mediante los hooks de `wp_enqueue_scripts` y `wpcf7_enqueue_scripts`.
– `admin/class-cf7-editor-panel.php` adopta la iconografía accesible para editar, liberar y eliminar, incorpora el formulario en línea que libera un uso cuando el contador es mayor que cero y sincroniza los tooltips con el panel principal.
– `tests/run-tests.php` comprueba el nuevo hook de `init`, simula la carga frontal para validar `autoload_front_assets`, verifica las versiones basadas en `CF7_OPTION_LIMITER_VERSION` y exige la presencia de los iconos en el panel incrustado.
– `README.md` documenta la carga proactiva de `frontend.css` y la disponibilidad de las acciones icónicas dentro del editor.

# v1.0.36
– `includes/class-limiter-handler.php` registra `enqueue_front_assets` tanto en `wp_enqueue_scripts` como en `wpcf7_enqueue_scripts`, garantizando que `frontend.css` y su script compañero se impriman incluso cuando Contact Form 7 sólo carga recursos en páginas con formularios.
– `tests/run-tests.php` comprueba la presencia de ambos hooks para evitar regresiones que dejen de inyectar estilos en el frontal.
– `README.md` documenta la doble suscripción de los assets públicos para que el equipo tenga claro cómo se asegura la carga de estilos.

# v1.0.35
– `assets/frontend-check.js` añade la clase persistente `cf7-option-limiter-message-container`, controla el atributo `hidden` y elimina el estilo de alerta cuando el mensaje queda vacío para evitar contenedores rojos sin contenido.
– `assets/frontend.css` incorpora reglas que ocultan los contenedores dinámicos mientras están vacíos y neutraliza márgenes, bordes y fondo cuando sólo actúan como marcadores invisibles.
– `tests/run-tests.php` verifica la presencia de la nueva clase JavaScript y de la regla CSS que explota `[hidden]`, impidiendo regresiones que vuelvan a mostrar avisos vacíos.
– `README.md` documenta que los contenedores sin texto permanecen ocultos y que sólo se pinta la pastilla cuando existe un mensaje real para el usuario.

# v1.0.34
– `admin/class-admin-page.php` sustituye los botones textuales por iconos accesibles con `dashicons`, añade tooltips traducibles y sólo habilita la liberación manual cuando el contador es superior a cero.
– `assets/admin.css` define estilos específicos para los botones icónicos, normaliza su tamaño y atenúa el estado deshabilitado para mejorar la jerarquía visual.
– `tests/run-tests.php` ajusta las aserciones para vigilar la presencia del icono de liberación, los atributos accesibles y la nueva clase que identifica el estado inactivo.
– `README.md` documenta la interfaz iconográfica y la disponibilidad condicionada del control de liberación manual.

# v1.0.33
– `admin/class-admin-page.php` centraliza el renderizado del botón **Liberar un uso** para que siempre se imprima junto a cada regla, reutiliza un helper dedicado y mantiene el atributo `disabled` únicamente cuando el contador está en cero.
– `tests/run-tests.php` incorpora stubs enriquecidos para `wp_nonce_field` y `disabled`, además de nuevas pruebas que verifican el marcado generado por el formulario de liberación en estados activo y agotado.
– `README.md` documenta que la acción aparece de forma consistente y que el botón se deshabilita automáticamente cuando no quedan usos por devolver.

# v1.0.32
– Documentación homogénea de todas las funciones con un bloque **Explicación** que aclara la responsabilidad, el flujo interno y el uso de parámetros, facilitando la lectura de cualquier componente.
– `README.md` detalla la nueva estandarización de comentarios para que el equipo conozca la guía de mantenimiento.
– `tests/run-tests.php` mantiene la cobertura actual asegurando que el refuerzo documental no altera los flujos críticos.

# v1.0.31
– `admin/class-admin-page.php` conserva los filtros activos y la paginación al volver desde las acciones de liberar y eliminar, y expone un método auxiliar probado para construir la URL de retorno.
– `assets/admin.js` permite editar reglas cuyos campos u opciones ya no existen mostrando etiquetas temporales, avisos accesibles y manteniendo habilitado el guardado.
– `tests/run-tests.php` verifica que la URL de retorno preserve parámetros personalizados y mantiene la cobertura de la liberación manual.
– `README.md` documenta la persistencia del contexto en la tabla y el nuevo flujo de edición para reglas con valores faltantes.

# v1.0.30
– `admin/class-admin-page.php` añade el botón **Liberar un uso**, valida permisos y nonces en el nuevo manejador `handle_release` y registra avisos específicos cuando se ajusta manualmente un contador.
– `includes/class-db-manager.php` incorpora `decrement_counter_by_id()` para restar una unidad garantizando que el contador nunca sea negativo y documenta la operación en el log.
– `tests/run-tests.php` cubre el decremento directo, confirma que el manejador administrativo valida el nonce, ejecuta la consulta con `GREATEST` y devuelve las notificaciones `released` y `release_failed` según corresponda.
– `README.md` documenta que la tabla administrativa permite liberar huecos manualmente cuando un usuario cancela su elección.

# v1.0.29
– `includes/hooks.php` añade el mensaje localizado que advierte de la pérdida temporal de reglas al desactivar o eliminar el plugin, manteniendo la confirmación protegida por permisos y nonce.
– `assets/plugins.js` muestra una alerta adicional antes de continuar con la desactivación o el borrado para avisar al usuario de que las reglas dejarán de aplicarse hasta reactivar el plugin.
– `tests/run-tests.php` verifica que la localización incluya el nuevo aviso y que el script llame a `window.alert` con la advertencia, asegurando que la alerta permanezca en futuras refactorizaciones.
– `README.md` documenta la nueva advertencia para que los administradores sepan que la interfaz avisará del impacto antes de completar la acción.

# v1.0.28
– `includes/hooks.php` valida la capacidad `activate_plugins`, exige el nonce nativo de WordPress antes de actualizar la preferencia de limpieza y contempla acciones masivas e individuales.
– `assets/plugins.js` garantiza que cualquier redirección que añada `cf7_ol_cleanup` conserve un nonce válido, reutilizando los atributos de datos cuando la URL original no lo incluía.
– `tests/run-tests.php` cubre los escenarios sin permisos, con nonce inválido, desactivaciones individuales y acciones masivas, confirmando que la opción sólo cambia cuando la validación se supera.
– `README.md` documenta el refuerzo de permisos y detalla que las pruebas automatizadas vigilan esta comprobación de seguridad.

# v1.0.27
– `assets/frontend-check.js` utiliza ahora un listener nativo en fase de captura para bloquear el envío antes de que Contact Form 7 dispare su AJAX, eliminando duplicidades al reatachar manejadores y deteniendo completamente la propagación cuando se detectan opciones agotadas.
– `tests/run-tests.php` incorpora una comprobación que garantiza la presencia del nuevo `addEventListener` en captura y protege frente a regresiones donde se vuelva a emplear `jQuery.on` en burbujeo.
– `README.md` se ha actualizado para documentar que la validación frontal intercepta el `submit` en captura asegurando que el formulario no se envía si persisten selecciones agotadas.

# v1.0.26
– Se añadió una caché en `assets/frontend-check.js` que valida las selecciones antes de enviar el formulario, ejecuta una comprobación inicial al cargar y bloquea el envío cuando la opción quedó agotada sin que el usuario cambie de elección.
– Se creó `assets/frontend.css` para mostrar los avisos como una pastilla de fondo rojo pastel y se encola automáticamente desde `CF7_OptionLimiter_Limiter::enqueue_front_assets()` junto al script actualizado.
– Se ampliaron las pruebas en `tests/run-tests.php` para comprobar el registro de los nuevos recursos, la versión del script y la presencia de la lógica de validación anticipada.

# v1.0.25
– Se añadió un endpoint AJAX autenticado (`wp_ajax_cf7_option_limiter_save_rule`) que reutiliza la lógica central de guardado, devuelve mensajes detallados y mantiene un fallback transparente hacia `admin-post.php` cuando la petición asíncrona falla.
– El script `assets/admin.js` ahora envía las reglas mediante AJAX, muestra avisos accesibles en la propia pestaña, actualiza la tabla sin recargar, gestiona el estado de carga del botón y elimina automáticamente la fila vacía inicial al crear la primera regla.
– Se ampliaron las pruebas automatizadas para interceptar las respuestas JSON del endpoint, validar tanto los errores por datos incompletos como el flujo satisfactorio y asegurar que la cola simulada de `$wpdb` reproduce el guardado completo.

# v1.0.24
– Refuerza el script administrativo con validaciones en el lado del cliente que exigen formulario, campo y opción antes de enviar, anunciando los errores con avisos accesibles y enfocando el control pendiente.
– Deshabilita automáticamente el botón de guardado hasta completar los desplegables, sincroniza el atributo `aria-disabled` y mantiene una región `aria-live` para informar del estado.
– Amplía las pruebas automáticas comprobando la presencia de la nueva lógica y documenta el comportamiento actualizado en el README.

# v1.0.23
– Añade una validación temprana en `handle_save` que rechaza peticiones sin identificador de formulario, campo u opción, registra el evento `limit_admin_rejected` cuando la depuración está activa y documenta el flujo para los administradores.
– Replica cada valor del panel incrustado en campos ocultos definitivos antes de enviar el formulario, evitando guardados vacíos incluso cuando el formulario principal de Contact Form 7 intercepta el envío.
– Ajusta el script administrativo para sincronizar y habilitar dinámicamente el campo oculto de `hide_exhausted`, documentando la nueva lógica y asegurando que el checkbox sólo se envía cuando procede.
– Amplía las pruebas automáticas validando la presencia de los nuevos campos ocultos y de la función de sincronización en el script, reforzando la cobertura frente a regresiones.

# v1.0.22
– Evita que el botón incrustado en el editor envíe el formulario de Contact Form 7 interceptando el clic y lanzando `requestSubmit` sobre el formulario oculto del plugin para que `admin-post.php` procese la petición correctamente.
– Ajusta el marcado del botón a `type="button"` y documenta el comportamiento en el README para clarificar que el envío depende del script administrativo.
– Amplía las pruebas automáticas verificando que el script incluye la lógica de envío programático y que el botón se declaró con el nuevo tipo.

# v1.0.21
– Amplía el log del guardado registrando cada etapa (inicio, sanitización, consulta, persistencia y desenlace) con un identificador correlativo para diagnosticar bloqueos en la base de datos incluso con el log mínimo.
– Ajusta el visor administrativo del log para mostrar texto negro sobre fondo claro, asegurando la lectura en paneles con estilos personalizados.
– Actualiza la documentación para reflejar la nueva traza detallada del guardado y el cambio visual del visor.

# v1.0.20
– Mueve el archivo de log a `wp-content/uploads/cf7-option-limiter/`, añade mensajes de error visibles cuando no se puede escribir y elimina cualquier rastro al desinstalar el plugin.
– Sincroniza automáticamente la tabla personalizada comparando la firma del archivo `create_table.sql` con la almacenada y ejecuta `dbDelta` cuando detecta diferencias.
– Actualiza el visor administrativo con la nueva ruta, amplía la documentación y añade pruebas unitarias que validan la ruta del log, la limpieza y la ejecución automática de `dbDelta` en cada arranque.

# v1.0.19
– Convierte el archivo de log a formato de texto plano, añade un visor integrado bajo la tabla principal y mantiene el filtrado de contexto cuando el modo depuración está desactivado.
– Sitúa el conmutador de depuración junto al visor, mejora los estilos del panel y ofrece pistas contextuales según el nivel de detalle activo.
– Amplía la documentación y las pruebas automáticas para verificar la nueva salida del log y el acceso a las líneas recientes desde la interfaz.

# v1.0.18
– Corrige el guardado de límites en servidores MySQL 5.7 añadiendo la columna `hide_exhausted` cuando falta y renombrando columnas heredadas sin depender de `ADD COLUMN IF NOT EXISTS`.
– Añade pruebas unitarias que validan la creación automática de la columna y garantiza que la lógica de migraciones se ejecute incluso cuando la versión almacenada está actualizada.

# v1.0.17
– Corrige el guardado de límites utilizando inserciones y actualizaciones específicas, evitando que los cambios se pierdan en entornos con restricciones de `REPLACE`.
– Añade un control visible en el panel principal para activar o desactivar el modo de depuración, registrando cambios y conservando un log mínimo cuando permanece inactivo.
– Refuerza el sistema de logging con entradas JSON por línea, registro automático de instalaciones y cambios de versión, y mensajes enriquecidos en operaciones de base de datos y formularios.

# v1.0.16
– Añade en la pestaña del editor de Contact Form 7 una columna que muestra si cada regla oculta las opciones agotadas para facilitar auditorías rápidas sin abandonar el formulario.

# v1.0.15
– Ejecuta automáticamente las migraciones pendientes durante el arranque para garantizar que la columna `hide_exhausted` esté disponible sin reactivar el plugin y registra la versión del esquema aplicada.
– Elimina por completo los archivos heredados del widget de Elementor y documenta cada carpeta con un README dedicado para facilitar el mantenimiento.

# v1.0.14
– Restaura la carga de campos y opciones en los desplegables del editor asegurando que el formulario detectado inicialice el selector y que el icono de ayuda esté disponible en todas las vistas.
– Añade confirmaciones en la pantalla de plugins para decidir si se eliminan las limitaciones guardadas durante la desactivación o desinstalación, limpiando ambas tablas cuando el usuario lo solicita.
– Publica la documentación en una página dedicada accesible desde el listado de plugins, enlazada mediante iconos informativos en el panel general y en el editor de Contact Form 7.

# v1.0.13
– Incorpora filtrado por formulario y paginación de diez elementos en la tabla administrativa, añade la casilla para decidir si las opciones agotadas se ocultan o permanecen visibles con aviso en la pestaña del editor, ajusta el script frontal para respetar la preferencia y se documenta el uso en el nuevo `manual_usuario.md` junto con pruebas que cubren el filtrado y el renderizado actualizado.
### v1.0.12
– Elimina por completo el widget de Elementor y las excepciones por página, centralizando la lógica en los límites globales, simplifica la página de administración dejándola como listado con enlace al editor y renueva la pestaña de Contact Form 7 para trabajar únicamente con desplegables detectados automáticamente, junto con la reescritura del script administrativo y la documentación actualizada.
### v1.0.11
– Corrige el guardado de excepciones en Elementor propagando el `post_id` desde el botón del widget, mantiene editable el campo de **Opción específica** incluso durante el escaneo automático y documenta estos ajustes para evitar bloqueos aparentes por permisos o entradas deshabilitadas.
### v1.0.10
– Añade edición directa de reglas con precarga automática desde la tabla administrativa, integra una pestaña del limitador dentro del editor de Contact Form 7 reutilizando los formularios ocultos, habilita la introducción manual inmediata del valor específico y mejora el guardado de excepciones mostrando mensajes precisos cuando fallan los permisos o la base de datos.
### v1.0.9
– Habilita el guardado de excepciones desde el editor de Elementor cargando los scripts frontales dentro del panel visual, añade campos de texto con sugerencias dinámicas en el panel de administración para introducir manualmente el nombre del campo y el valor cuando el escaneo automático falla, y extiende las pruebas para asegurar el registro del nuevo hook.
### v1.0.8
– Reescribe la clase simulada de `$wpdb` en las pruebas para eliminar caracteres erróneos que provocaban errores de sintaxis, añade el lintado automático en GitHub Actions y documenta el nuevo control previo a las pruebas.
### v1.0.7
– Corrige el error fatal durante la activación comprobando la disponibilidad de `dbDelta`, refuerza el registro del widget de Elementor para entornos sin la clase base y añade pruebas unitarias que simulan la activación completa y verifican las nuevas salvaguardas.
### v1.0.6
– Añade advertencias sincronizadas entre el panel lateral y la vista previa del widget de Elementor para destacar diferencias con la regla global sin realizar escrituras automáticas, documenta la interacción y extiende las pruebas para cubrir la nueva alerta.
### v1.0.5
– Incorpora excepciones por página con nueva tabla dedicada, priorización por `post_id`, guardado directo desde el widget de Elementor, listados en administración y pruebas automatizadas junto al flujo de CI para verificarlas.
### v1.0.4
– Añade comprobaciones en vivo mediante AJAX para ocultar opciones agotadas, validaciones servidoras que bloquean envíos cuando el límite se supera y pruebas automatizadas que cubren ambos flujos.
### v1.0.3
– Evita que el widget de Elementor escriba en la base de datos durante el renderizado, muestra advertencias de desincronización en el editor y documenta la interacción con la configuración global.
### v1.0.2
– Añade recuperación resiliente de formularios en el widget de Elementor utilizando `WP_Query` como respaldo, documenta el comportamiento y extiende las pruebas unitarias para cubrir el nuevo flujo.
### v1.0.1
– Corrige errores fatales al procesar etiquetas de Contact Form 7, añade compatibilidad retroactiva para detectar formularios en el panel y agrega pruebas automatizadas ejecutadas en GitHub Actions.
### v1.0.0
– Implementación completa del limitador con panel de administración, widget de Elementor, reset automático por periodos, logs rotativos y scripts/documentación actualizados.
