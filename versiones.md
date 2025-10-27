# Control de Versiones

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
