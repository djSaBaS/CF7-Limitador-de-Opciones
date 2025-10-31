# Manual de usuario de CF7 Option Limiter

## Objetivo del plugin
CF7 Option Limiter permite limitar cuántas veces puede elegirse una opción concreta en los campos de tipo lista, radio o casillas de verificación de Contact Form 7. Gracias a este control, resulta sencillo gestionar reservas, plazas y turnos sin sobrescribir información desde otros sistemas.

## Acceso rápido
1. En el panel de WordPress abre **Contact Form 7 → Option Limiter** para ver el listado de reglas existentes.
2. Utiliza el selector **Filtrar por formulario** para centrarte en un único formulario y la paginación (10 elementos por página) para desplazarte por el listado cuando existan muchas reglas.
3. Pulsa **Editar en el formulario** para abrir el editor de Contact Form 7 con la pestaña del limitador y la regla precargada.

## Crear o modificar una regla desde el editor
1. Entra en Contact Form 7 y abre el formulario deseado.
2. Haz clic en la pestaña **Limitador de opciones**.
3. Selecciona el campo que quieras controlar desde el desplegable **Campo a limitar**.
4. Elige la opción concreta en el desplegable **Opción específica**.
5. Indica el número máximo de usos y, si lo necesitas, define un periodo de reinicio (hora, día, semana o ilimitado).
6. Marca la casilla **Ocultar automáticamente las opciones agotadas** si deseas que desaparezcan del formulario cuando alcancen el límite. Déjala sin marcar para que sigan visibles pero no permitan nuevas selecciones.
7. Opcionalmente escribe un **Mensaje personalizado al agotarse** que se mostrará a la persona usuaria.
8. Guarda los cambios con **Guardar límite**. El botón **Cancelar edición** restablece el formulario sin aplicar cambios. Antes de lanzar la petición el plugin copia automáticamente todos los valores introducidos a un formulario oculto, de modo que los datos llegan completos aunque el navegador ignore el atributo `form` del marcado principal.

## Liberar manualmente un uso
- Desde el listado general de **Contact Form 7 → Option Limiter** utiliza el icono de candado abierto situado en cada fila para liberar al instante un uso cuando una persona usuaria cancela su reserva. El formulario auxiliar mantiene los filtros y la paginación activos tras completar la operación para que continúes revisando el mismo conjunto de reglas.
- En la pestaña **Limitador de opciones** del editor verás el mismo icono junto a cada regla del listado lateral. Al pulsarlo, el plugin recrea automáticamente un formulario oculto seguro en el pie del administrador y envía la petición sin cerrar el formulario principal de Contact Form 7, de modo que el botón **Guardar** del formulario vuelve a estar disponible inmediatamente después de devolver la plaza.
- Si no quedan usos disponibles el icono aparece atenuado y deshabilitado para evitar acciones involuntarias. En cuanto liberes una plaza se actualizará el contador y el botón volverá a quedar activo si procede.

## Cómo interpretar el listado principal
- **Formulario, Campo y Opción** identifican dónde se aplica cada regla.
- **Ocultar agotadas** indica si la opción desaparece al alcanzar el límite o si permanece visible con un aviso.
- **Máximo y Usos actuales** permiten vigilar el consumo de plazas.
- **Periodo y Mensaje** muestran los ajustes adicionales de cada regla.
- La paginación situada bajo la tabla permite navegar entre páginas y muestra el número total de reglas encontradas.

## Preguntas frecuentes
- **¿Qué ocurre si no marco "Ocultar automáticamente las opciones agotadas"?**  
  La opción seguirá apareciendo en el formulario, pero quedará deshabilitada y mostrará un aviso indicando que está temporalmente agotada.
- **¿Puedo filtrar el listado por más de un formulario a la vez?**  
  No, el selector está pensado para centrarte en un único formulario cada vez y facilitar la revisión.
- **¿Cuándo se restablece el contador?**
  El contador se resetea automáticamente cuando llegue el periodo indicado (por hora, día o semana). Si eliges el modo total, el contador nunca se reinicia de forma automática.
- **¿Qué ocurre si elimino una regla desde el listado?**
  La regla deja de aplicarse inmediatamente. Siempre puedes crearla de nuevo desde la pestaña del limitador en el formulario correspondiente.
- **¿Cómo activo el modo de depuración?**
  Desde **Contact Form 7 → Option Limiter** desplázate hasta el bloque situado bajo la tabla de reglas, marca la casilla **Activar modo de depuración detallado** y guarda la preferencia. Mientras esté activo, el archivo `wp-content/uploads/cf7-option-limiter/cf7-option-limiter.log` registrará todas las consultas y advertencias y el visor en texto plano mostrará información ampliada; al desactivarlo únicamente se conservarán los eventos esenciales (guardados, eliminaciones, activaciones y desactivaciones) y el visor reflejará ese log mínimo. Cada intento de guardado genera ahora una traza paso a paso con un identificador común que detalla la recepción del formulario, la sanitización, la búsqueda de registros previos, la consulta enviada a la base de datos y el resultado final, lo que facilita detectar bloqueos o datos inesperados incluso sin activar el modo depuración. Además, el visor muestra el texto en color negro sobre fondo claro para garantizar la lectura aunque el panel tenga estilos heredados.
- **Antes las reglas no se guardaban en algunos servidores, ¿qué cambió?**
  Desde la versión 1.0.18 el plugin revisa automáticamente que la columna `hide_exhausted` exista incluso en bases de datos MySQL 5.7 y la crea si falta, evitando los fallos de guardado que se producían tras actualizaciones incompletas.

## Consejos de uso
- Aprovecha el filtrado y la paginación para revisar formularios con muchas reglas sin perder tiempo desplazándote por listados interminables.
- Define mensajes claros para que las personas usuarias sepan qué opciones están agotadas y cuándo podrán volver a seleccionarlas.
- Revisa periódicamente la columna **Usos actuales** para detectar cuándo se alcanzan los límites y decidir si necesitas ampliar el máximo permitido.
