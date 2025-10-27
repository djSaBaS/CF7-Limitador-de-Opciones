<?php
// Bloquea el acceso directo al archivo fuera del entorno de WordPress.
if ( ! defined( 'ABSPATH' ) ) { // Verifica que la constante de WordPress exista.
    exit; // Detiene la ejecución inmediatamente cuando se accede sin el cargador de WordPress.
}

// Clase responsable de registrar eventos y depuración del plugin en un archivo de texto plano legible por personas.
class CF7_OptionLimiter_Logger { // Declara la clase principal de logging del plugin.

    // Constante que almacena el nombre de la opción usada para el modo depuración.
    const DEBUG_OPTION = 'cf7_option_limiter_debug_mode'; // Identificador de la opción que persiste si el modo está activo.

    // Constante que guarda el nombre de la opción empleada para registrar la última versión conocida del plugin.
    const VERSION_OPTION = 'cf7_option_limiter_plugin_version'; // Identificador de la opción que ayuda a detectar actualizaciones.

    // Constante que define el nombre base de los archivos de log creados por el plugin.
    const LOG_FILENAME = 'cf7-option-limiter.log'; // Nombre fijo utilizado tanto para el archivo activo como para los históricos rotados.

    // Conjunto de claves del contexto que se conservarán cuando la depuración detallada esté desactivada.
    const MINIMAL_CONTEXT_KEYS = array( // Define las llaves que se mantendrán para el log mínimo.
        'form_id', // Identificador del formulario afectado.
        'field_name', // Nombre del campo involucrado en la regla.
        'option_value', // Valor de la opción gestionada.
        'hide_exhausted', // Indicador de ocultación de opciones agotadas.
        'max_count', // Máximo configurado para la regla.
        'current_count', // Contador actual registrado.
        'operation', // Tipo de operación realizada en base de datos.
        'success', // Resultado lógico de la operación.
        'rule_id', // Identificador interno de la regla.
        'action', // Acción administrativa solicitada.
        'message', // Mensaje descriptivo asociado al evento.
        'trace_id', // Identificador correlativo que permite enlazar todas las etapas del guardado.
        'stage', // Paso concreto del proceso registrado para reconstruir la secuencia completa.
        'enabled', // Indicador de activación para el modo depuración.
        'previous_version', // Versión previa del plugin detectada.
        'new_version', // Nueva versión registrada tras la actualización.
        'table', // Tabla de base de datos involucrada en el evento.
        'override', // Indicador de si la regla proviene de una excepción.
        'rows_affected', // Número de filas impactadas por la operación.
        'raw_signature', // Firma hash de los datos recibidos antes de la sanitización.
        'sanitized_signature', // Firma hash de los datos preparados para persistirlos.
        'existing_signature', // Firma hash de la fila existente encontrada en base de datos.
        'found', // Indicador booleano que resume si se localizó una fila previa.
        'existing_id', // Identificador de la fila encontrada durante la consulta previa.
        'db_error', // Mensaje de error devuelto por la base de datos.
        'error', // Descripción de error genérica.
        'status', // Código de estado asociado al evento.
    );

    // Ruta absoluta al archivo de log que se actualizará con cada evento.
    protected static $log_file = ''; // Propiedad estática que almacenará la ubicación del fichero de log.

    // Ruta absoluta del directorio donde se almacenan el archivo activo y sus rotaciones.
    protected static $log_directory = ''; // Propiedad estática que facilita limpiar todos los ficheros relacionados.

    // Indicador interno para evitar ejecutar la inicialización más de una vez por petición.
    protected static $initialized = false; // Propiedad estática que previene inicializaciones repetidas.

    // Último mensaje de error detectado durante la preparación del archivo de log.
    protected static $last_error = ''; // Propiedad que almacena información diagnóstica para el panel administrativo.

    /**
     * Prepara el archivo de log creando el fichero y asegurando la rotación.
     *
     * @return void
     */
    public static function init() { // Método público que prepara el entorno de logging.
        if ( self::$initialized ) { // Comprueba si el logger ya fue inicializado en la petición actual.
            return; // Finaliza anticipadamente para evitar trabajo redundante.
        }
        $location = self::resolve_log_location(); // Calcula la ubicación más adecuada disponible para almacenar el log.
        self::$log_directory = $location['directory']; // Guarda el directorio determinado para reutilizarlo en otras operaciones.
        self::$log_file      = $location['file']; // Guarda la ruta completa del archivo activo de log.
        if ( empty( self::$log_file ) ) { // Comprueba si no se pudo determinar una ruta válida para el log.
            self::$last_error = __( 'No se pudo preparar el archivo de log porque ninguna ruta es escribible.', 'cf7-option-limiter' ); // Registra un mensaje orientativo accesible desde la administración.
            self::$initialized = true; // Marca igualmente la inicialización para evitar intentos repetidos.
            return; // Finaliza porque no habrá archivo disponible para registrar eventos.
        }
        if ( ! file_exists( self::$log_file ) ) { // Comprueba si el archivo todavía no existe en el directorio seleccionado.
            $created = file_put_contents( self::$log_file, '' ); // Intenta crear un archivo vacío para habilitar futuras escrituras.
            if ( false === $created ) { // Comprueba si la creación devolvió un fallo.
                self::$last_error = __( 'El plugin no pudo crear el archivo de log en el directorio seleccionado.', 'cf7-option-limiter' ); // Guarda un mensaje descriptivo para mostrarlo al administrador.
                self::$log_file   = ''; // Limpia la ruta para indicar que no se podrá registrar información.
                self::$initialized = true; // Marca igualmente la inicialización para detener intentos posteriores.
                return; // Finaliza porque no se dispone de un archivo físico de log.
            }
        }
        if ( ! is_writable( self::$log_file ) ) { // Comprueba si el archivo existente no permite escrituras.
            self::$last_error = __( 'El archivo de log existe pero no es escribible por el servidor.', 'cf7-option-limiter' ); // Registra el problema para mostrarlo en el panel administrativo.
            self::$log_file   = ''; // Limpia la ruta para evitar intentos de escritura que provocarían errores.
            self::$initialized = true; // Marca la inicialización para evitar bucles infinitos.
            return; // Finaliza porque no es posible escribir en el archivo actual.
        }
        self::$last_error = ''; // Limpia cualquier error previo porque el archivo quedó listo para escribirse.
        self::$initialized = true; // Marca que la inicialización ya se ha ejecutado.
        self::maybe_rotate(); // Comprueba inmediatamente si el archivo debe rotarse para mantener su tamaño controlado.
    }

    /**
     * Devuelve si el modo de depuración persistente está activo.
     *
     * @return bool
     */
    public static function is_debug_enabled() { // Método público que indica si se debe registrar información extendida.
        $value = get_option( self::DEBUG_OPTION ); // Recupera el valor almacenado en la opción correspondiente.
        return (bool) $value; // Convierte el valor recuperado a booleano para simplificar la comprobación.
    }

    /**
     * Activa o desactiva el modo de depuración y registra el cambio de estado.
     *
     * @param bool $enabled Indica si el modo debe quedar activo.
     *
     * @return void
     */
    public static function set_debug_mode( $enabled ) { // Método público que permite alternar el modo depuración.
        $flag = $enabled ? 1 : 0; // Normaliza el estado recibido a un entero compatible con la tabla de opciones.
        update_option( self::DEBUG_OPTION, $flag ); // Guarda de forma persistente el estado del modo depuración.
        $event = $enabled ? 'debug_mode_enabled' : 'debug_mode_disabled'; // Determina el nombre del evento que se registrará.
        self::log( $event, array( 'enabled' => (bool) $flag ), true ); // Registra el cambio de estado forzando la escritura aun sin depuración activa.
    }

    /**
     * Registra un evento en el archivo de log respetando el modo de depuración.
     *
     * @param string               $event   Nombre simbólico del evento que se registrará.
     * @param array<string, mixed> $context Datos adicionales que describen el evento.
     * @param bool                 $force   Indica si debe registrarse aunque el modo depuración esté desactivado.
     *
     * @return void
     */
    public static function log( $event, array $context = array(), $force = false ) { // Método público que añade una entrada al log.
        self::init(); // Garantiza que el archivo de log esté disponible antes de continuar.
        $debug_enabled = self::is_debug_enabled(); // Determina si el modo depuración detallado está activo actualmente.
        if ( ! $force && ! $debug_enabled ) { // Comprueba si no debe registrarse el evento por estar la depuración desactivada.
            return; // Finaliza silenciosamente cuando no se requiere registrar el evento.
        }
        if ( empty( self::$log_file ) ) { // Comprueba si no existe un archivo disponible para escribir.
            return; // Finaliza silenciosamente para evitar errores cuando la ruta no pudo prepararse.
        }
        $normalized_context = self::normalize_context( $context, $debug_enabled ); // Normaliza el contexto respetando el modo activo.
        $entry = self::build_entry( $event, $normalized_context ); // Construye la estructura de datos que se almacenará en el archivo.
        self::write_entry( $entry ); // Persiste la entrada en disco utilizando un formato de texto plano amigable.
    }

    /**
     * Registra en el log la instalación inicial o una actualización del plugin.
     *
     * @return void
     */
    public static function maybe_log_version_change() { // Método público que detecta cambios de versión del plugin.
        if ( ! defined( 'CF7_OPTION_LIMITER_VERSION' ) ) { // Comprueba que la constante de versión esté disponible.
            return; // Finaliza si no se puede determinar la versión actual.
        }
        $stored_version = get_option( self::VERSION_OPTION ); // Recupera la última versión registrada en la base de datos.
        if ( $stored_version === CF7_OPTION_LIMITER_VERSION ) { // Comprueba si la versión almacenada coincide con la actual.
            return; // Finaliza si no hay cambios de versión que registrar.
        }
        $event = empty( $stored_version ) ? 'plugin_installed' : 'plugin_updated'; // Determina si se trata de una instalación inicial o de una actualización.
        $context = array( // Construye el contexto que acompañará al evento registrado.
            'previous_version' => $stored_version, // Registra la versión previa almacenada, aunque sea null.
            'new_version'      => CF7_OPTION_LIMITER_VERSION, // Registra la versión actual del plugin.
        );
        self::log( $event, $context, true ); // Registra el evento forzando la escritura incluso sin depuración activa.
        update_option( self::VERSION_OPTION, CF7_OPTION_LIMITER_VERSION ); // Actualiza la opción para reflejar la versión vigente.
    }

    /**
     * Construye la estructura base de una entrada del log incluyendo contexto común.
     *
     * @param string               $event   Nombre simbólico del evento que se registrará.
     * @param array<string, mixed> $context Datos adicionales proporcionados por el llamador.
     *
     * @return array<string, mixed>
     */
    protected static function build_entry( $event, array $context ) { // Método protegido que crea la entrada del log.
        $entry = self::get_default_context(); // Obtiene el contexto base con la marca temporal, la versión y el usuario.
        $event_key = sanitize_key( $event ); // Normaliza el nombre del evento a un formato estable y sin espacios.
        $entry['event']       = $event_key; // Guarda el nombre del evento ya normalizado.
        $entry['event_label'] = self::describe_event( $event_key ); // Obtiene una descripción legible del evento registrado.
        $entry['details']     = self::stringify_context( $context ); // Convierte el contexto en una cadena legible y sin saltos de línea.
        return $entry; // Devuelve la estructura final preparada para escribirse en disco.
    }

    /**
     * Proporciona los datos comunes que acompañan a cada línea del log.
     *
     * @return array<string, mixed>
     */
    protected static function get_default_context() { // Método protegido que devuelve información común a cada entrada.
        $timestamp = function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'Y-m-d H:i:s' ); // Obtiene la fecha en formato MySQL utilizando WordPress cuando está disponible.
        return array( // Devuelve el contexto base preconfigurado.
            'timestamp'      => $timestamp, // Registra la marca temporal del evento.
            'plugin_version' => defined( 'CF7_OPTION_LIMITER_VERSION' ) ? CF7_OPTION_LIMITER_VERSION : 'unknown', // Incluye la versión del plugin para contextualizar la línea.
            'user'           => self::get_user_context(), // Adjunta información del usuario responsable de la acción.
        );
    }

    /**
     * Obtiene información resumida del usuario actual para el log.
     *
     * @return array<string, mixed>
     */
    protected static function get_user_context() { // Método protegido que recopila datos del usuario actual.
        $user_data = array( 'id' => 0, 'login' => 'desconocido' ); // Establece valores por defecto cuando no hay sesión.
        if ( function_exists( 'wp_get_current_user' ) ) { // Comprueba si WordPress expone la función para recuperar al usuario.
            $user = wp_get_current_user(); // Recupera la instancia del usuario actual.
            if ( $user && isset( $user->ID ) && $user->ID ) { // Comprueba que la instancia contenga un identificador válido.
                $user_data['id']    = (int) $user->ID; // Guarda el identificador numérico del usuario.
                $user_data['login'] = isset( $user->user_login ) ? (string) $user->user_login : $user_data['login']; // Guarda el nombre de usuario si está disponible.
                return $user_data; // Devuelve el contexto completo cuando se identificó un usuario válido.
            }
        }
        if ( function_exists( 'get_current_user_id' ) ) { // Comprueba si existe la función auxiliar para obtener el ID.
            $maybe_id = get_current_user_id(); // Recupera el identificador numérico del usuario actual.
            if ( $maybe_id ) { // Comprueba si la llamada devolvió un identificador válido.
                $user_data['id'] = (int) $maybe_id; // Actualiza el contexto con el identificador detectado.
            }
        }
        return $user_data; // Devuelve el contexto incluso si no se pudo determinar un usuario autenticado.
    }

    /**
     * Escribe una entrada en el archivo de log aplicando formato de texto plano y bloqueo exclusivo.
     *
     * @param array<string, mixed> $entry Datos completos de la entrada a escribir.
     *
     * @return void
     */
    protected static function write_entry( array $entry ) { // Método protegido que gestiona la escritura en disco.
        if ( empty( self::$log_file ) ) { // Comprueba nuevamente que exista una ruta válida antes de escribir.
            return; // Evita intentar escribir cuando el archivo no está disponible.
        }
        $formatted_line = self::format_line( $entry ); // Convierte la entrada estructurada en una línea de texto plano legible.
        file_put_contents( self::$log_file, $formatted_line . PHP_EOL, FILE_APPEND | LOCK_EX ); // Añade la línea al final del archivo aplicando un bloqueo exclusivo.
        self::maybe_rotate(); // Verifica si es necesario rotar el archivo tras añadir la nueva entrada.
    }

    /**
     * Filtra el contexto registrado en función del modo de depuración activo.
     *
     * @param array<string, mixed> $context Datos adicionales proporcionados por el evento.
     * @param bool                 $debug   Indica si el modo depuración detallado está activo.
     *
     * @return array<string, mixed>
     */
    protected static function normalize_context( array $context, $debug ) { // Método protegido que ajusta el contexto según el modo activo.
        if ( $debug ) { // Comprueba si debe conservarse toda la información disponible.
            return $context; // Devuelve el contexto sin modificaciones para el modo depuración completo.
        }
        $allowed_keys = array_fill_keys( self::MINIMAL_CONTEXT_KEYS, true ); // Construye un mapa de claves permitidas para el log mínimo.
        $filtered = array(); // Inicializa el arreglo que almacenará únicamente los valores permitidos.
        foreach ( $context as $key => $value ) { // Recorre cada elemento del contexto original.
            if ( isset( $allowed_keys[ $key ] ) ) { // Comprueba si la clave actual está permitida en el log mínimo.
                $filtered[ $key ] = $value; // Conserva el valor asociado cuando está permitido.
            }
        }
        return empty( $filtered ) ? $context : $filtered; // Devuelve el contexto filtrado o el original cuando no se conservaron claves.
    }

    /**
     * Convierte el contexto en una cadena formateada `clave=valor` separada por punto y coma.
     *
     * @param array<string, mixed> $context Datos adicionales ya filtrados.
     *
     * @return string
     */
    protected static function stringify_context( array $context ) { // Método protegido que convierte el contexto en texto plano.
        if ( empty( $context ) ) { // Comprueba si no se recibieron datos adicionales.
            return __( 'Sin detalles adicionales', 'cf7-option-limiter' ); // Devuelve un mensaje genérico indicando ausencia de detalles.
        }
        $parts = array(); // Inicializa el arreglo que almacenará cada fragmento `clave=valor`.
        foreach ( $context as $key => $value ) { // Recorre cada par clave/valor del contexto filtrado.
            $normalized_key = sanitize_key( (string) $key ); // Normaliza la clave para asegurar un formato estable.
            $string_value   = self::stringify_value( $value ); // Convierte el valor en una cadena limpia y monolínea.
            $parts[]        = $normalized_key . '=' . $string_value; // Combina la clave y el valor en el formato esperado.
        }
        return implode( '; ', $parts ); // Une todos los fragmentos separados por punto y coma para mantener la legibilidad.
    }

    /**
     * Convierte un valor arbitrario en texto limpio apto para el log.
     *
     * @param mixed $value Valor original que debe representarse en texto.
     *
     * @return string
     */
    protected static function stringify_value( $value ) { // Método protegido que garantiza que cada valor sea legible y seguro.
        if ( is_bool( $value ) ) { // Comprueba si el valor es booleano.
            $value = $value ? 'true' : 'false'; // Convierte booleanos en sus representaciones textuales.
        } elseif ( is_scalar( $value ) || null === $value ) { // Comprueba si el valor es escalar o nulo.
            $value = (string) $value; // Convierte el valor directamente a cadena para mantener su representación natural.
        } else { // Maneja arreglos u objetos complejos.
            $encoder = function_exists( 'wp_json_encode' ) ? 'wp_json_encode' : 'json_encode'; // Selecciona la función disponible para codificar estructuras.
            $value   = $encoder( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); // Convierte la estructura en JSON legible sin caracteres escapados.
        }
        $value = preg_replace( "/[\r\n\t]+/", ' ', (string) $value ); // Sustituye saltos de línea o tabulaciones por espacios simples.
        $value = trim( $value ); // Elimina espacios al inicio y final para mantener la cadena compacta.
        if ( function_exists( 'sanitize_text_field' ) ) { // Comprueba si WordPress ofrece la función de sanitización.
            $value = sanitize_text_field( $value ); // Aplica la sanitización estándar para asegurar que el texto sea seguro.
        }
        return $value === '' ? 'n/d' : $value; // Devuelve la cadena resultante o un marcador cuando quedó vacía tras la sanitización.
    }

    /**
     * Devuelve una etiqueta descriptiva para cada tipo de evento registrado.
     *
     * @param string $event Clave del evento registrado.
     *
     * @return string
     */
    protected static function describe_event( $event ) { // Método protegido que traduce la clave del evento a un texto legible.
        switch ( $event ) { // Evalúa el nombre del evento.
            case 'limit_saved': // Cuando se guarda o actualiza una regla.
                return __( 'Guardado de regla', 'cf7-option-limiter' ); // Devuelve la descripción legible correspondiente.
            case 'limit_deleted': // Cuando se elimina una regla existente.
                return __( 'Eliminación de regla', 'cf7-option-limiter' ); // Describe la eliminación de una regla.
            case 'limit_admin_action': // Cuando se realiza una acción desde la administración.
                return __( 'Acción administrativa', 'cf7-option-limiter' ); // Describe una acción iniciada desde el panel.
            case 'limit_save_start': // Cuando se inicia el proceso de guardado con los datos recibidos.
                return __( 'Inicio de guardado de límite', 'cf7-option-limiter' ); // Describe la recepción inicial de la petición.
            case 'limit_save_sanitized': // Cuando finaliza la sanitización de los datos previos a la escritura.
                return __( 'Datos sanitizados para guardado', 'cf7-option-limiter' ); // Describe la preparación de los valores.
            case 'limit_save_existing': // Cuando se comprueba la existencia previa de una fila.
                return __( 'Consulta previa de límite', 'cf7-option-limiter' ); // Describe el resultado de la búsqueda previa.
            case 'limit_save_persist': // Cuando se lanza la operación contra la base de datos.
                return __( 'Ejecución de guardado en base de datos', 'cf7-option-limiter' ); // Describe la escritura solicitada.
            case 'limit_save_complete': // Cuando la operación culmina correctamente.
                return __( 'Guardado completado', 'cf7-option-limiter' ); // Describe la finalización satisfactoria del proceso.
            case 'limit_save_error': // Cuando la operación falla y se capturan detalles del error.
                return __( 'Error durante el guardado', 'cf7-option-limiter' ); // Describe el fallo en la escritura de la tabla.
            case 'debug_mode_enabled': // Cuando se activa el modo depuración.
                return __( 'Modo depuración activado', 'cf7-option-limiter' ); // Indica la activación del modo depuración.
            case 'debug_mode_disabled': // Cuando se desactiva el modo depuración.
                return __( 'Modo depuración desactivado', 'cf7-option-limiter' ); // Indica la desactivación del modo depuración.
            case 'plugin_installed': // Cuando se instala el plugin.
                return __( 'Instalación del plugin', 'cf7-option-limiter' ); // Describe la instalación inicial.
            case 'plugin_updated': // Cuando se actualiza el plugin.
                return __( 'Actualización del plugin', 'cf7-option-limiter' ); // Describe la actualización de versión.
            case 'reset': // Cuando se reinician contadores por periodo.
                return __( 'Reinicio de contadores', 'cf7-option-limiter' ); // Describe el reseteo programado de contadores.
            default: // Para cualquier otro evento no contemplado explícitamente.
                return sprintf( __( 'Evento %s', 'cf7-option-limiter' ), $event ); // Devuelve una descripción genérica con la clave del evento.
        }
    }

    /**
     * Compone la línea final que se escribirá en el log de texto plano.
     *
     * @param array<string, mixed> $entry Datos estructurados del evento.
     *
     * @return string
     */
    protected static function format_line( array $entry ) { // Método protegido que construye la línea final del log.
        $timestamp = isset( $entry['timestamp'] ) ? $entry['timestamp'] : gmdate( 'Y-m-d H:i:s' ); // Obtiene la marca temporal registrada.
        $version   = isset( $entry['plugin_version'] ) ? $entry['plugin_version'] : 'unknown'; // Obtiene la versión del plugin asociada al evento.
        $user_id   = isset( $entry['user']['id'] ) ? (int) $entry['user']['id'] : 0; // Recupera el identificador numérico del usuario responsable.
        $user_login = isset( $entry['user']['login'] ) ? $entry['user']['login'] : 'desconocido'; // Recupera el alias público del usuario responsable.
        if ( function_exists( 'sanitize_text_field' ) ) { // Comprueba si se dispone de la función de sanitización.
            $user_login = sanitize_text_field( $user_login ); // Asegura que el alias sea seguro para mostrarlo en el log.
        }
        $event_label = isset( $entry['event_label'] ) ? $entry['event_label'] : $entry['event']; // Obtiene la etiqueta descriptiva del evento.
        $event_key   = isset( $entry['event'] ) ? $entry['event'] : 'evento'; // Recupera la clave interna del evento.
        $details     = isset( $entry['details'] ) ? $entry['details'] : __( 'Sin detalles adicionales', 'cf7-option-limiter' ); // Recupera la descripción formateada del contexto.
        return sprintf( // Construye la línea final asegurando un formato consistente.
            '[%1$s] v%2$s | Usuario #%3$d (%4$s) | %5$s [%6$s] | %7$s', // Plantilla que ordena los datos para facilitar la lectura.
            $timestamp, // Inserta la marca temporal en la línea de log.
            $version, // Inserta la versión del plugin.
            $user_id, // Inserta el identificador del usuario.
            $user_login, // Inserta el alias del usuario.
            $event_label, // Inserta la etiqueta legible del evento.
            $event_key, // Inserta la clave técnica del evento.
            $details // Inserta la cadena con los detalles del contexto.
        );
    }

    /**
     * Devuelve las últimas líneas registradas en el log en formato de arreglo.
     *
     * @param int $limit Número máximo de líneas a recuperar.
     *
     * @return array<int, string>
     */
    public static function get_recent_lines( $limit = 120 ) { // Método público que expone las últimas entradas del log.
        self::init(); // Asegura que la ruta del archivo de log esté inicializada.
        if ( empty( self::$log_file ) || ! file_exists( self::$log_file ) ) { // Comprueba si el archivo de log está disponible.
            return array(); // Devuelve un arreglo vacío cuando no existe el archivo.
        }
        $limit = (int) $limit; // Convierte el límite a entero para evitar valores inesperados.
        if ( $limit <= 0 ) { // Comprueba que el límite solicitado sea válido.
            $limit = 50; // Establece un valor por defecto cuando se recibe un límite inválido.
        }
        $lines = file( self::$log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ); // Lee el archivo línea por línea ignorando saltos vacíos.
        if ( false === $lines || empty( $lines ) ) { // Comprueba si la lectura falló o no se recuperaron entradas.
            return array(); // Devuelve un arreglo vacío cuando no hay líneas que mostrar.
        }
        return array_slice( $lines, -1 * $limit ); // Devuelve únicamente las últimas líneas solicitadas respetando el límite.
    }

    /**
     * Gestiona la rotación del archivo cuando supera 1 MB conservando un histórico inmediato.
     *
     * @return void
     */
    protected static function maybe_rotate() { // Método protegido que controla el tamaño del archivo de log.
        if ( empty( self::$log_file ) ) { // Comprueba que la ruta del log esté inicializada antes de continuar.
            return; // Finaliza sin hacer nada cuando el logger aún no estableció la ruta del archivo.
        }
        $max_size = 1024 * 1024; // Define el tamaño máximo permitido (1 MB) antes de rotar el archivo.
        if ( file_exists( self::$log_file ) && filesize( self::$log_file ) > $max_size ) { // Comprueba si el archivo supera el tamaño máximo permitido.
            $archive = self::$log_file . '.' . gmdate( 'YmdHis' ); // Genera el nombre del archivo rotado añadiendo la fecha y hora actual en UTC.
            rename( self::$log_file, $archive ); // Renombra el archivo actual para conservarlo como histórico inmediato.
            file_put_contents( self::$log_file, '' ); // Crea un archivo nuevo vacío que continuará recibiendo eventos.
        }
    }

    /**
     * Devuelve información básica sobre el último error detectado durante la inicialización del logger.
     *
     * @return string
     */
    public static function get_last_error() { // Método público que permite conocer problemas al preparar el archivo de log.
        return self::$last_error; // Devuelve el mensaje descriptivo almacenado, vacío cuando no hubo errores.
    }

    /**
     * Proporciona la ruta absoluta del archivo de log activo para mostrarla en la interfaz.
     *
     * @return string
     */
    public static function get_log_file_path() { // Método público que expone la ubicación física del log.
        self::init(); // Asegura que la ruta esté disponible antes de devolverla.
        return self::$log_file; // Devuelve la ruta completa o cadena vacía cuando no se pudo preparar.
    }

    /**
     * Elimina tanto el archivo de log activo como las rotaciones conservadas.
     *
     * @return void
     */
    public static function delete_logs() { // Método público que limpia los archivos de log creados por el plugin.
        self::init(); // Garantiza que se conozca el directorio configurado.
        $paths = array(); // Inicializa el listado de rutas que se intentarán eliminar.
        if ( ! empty( self::$log_file ) && file_exists( self::$log_file ) ) { // Comprueba si el archivo principal está disponible.
            $paths[] = self::$log_file; // Añade el archivo activo al listado de borrado.
        }
        if ( ! empty( self::$log_directory ) && is_dir( self::$log_directory ) ) { // Comprueba si existe el directorio de logs para buscar rotaciones.
            $pattern = trailingslashit( self::$log_directory ) . self::LOG_FILENAME . '.*'; // Construye el patrón que coincidirá con las rotaciones fechadas.
            $archives = glob( $pattern ); // Obtiene todos los archivos que siguen el patrón establecido.
            if ( is_array( $archives ) ) { // Comprueba que la llamada devolvió una lista válida.
                $paths = array_merge( $paths, $archives ); // Fusiona los archivos rotados con el listado de borrado.
            }
        }
        foreach ( array_unique( $paths ) as $path ) { // Recorre cada archivo evitando duplicados.
            if ( is_file( $path ) ) { // Comprueba que la ruta corresponde a un archivo regular.
                @unlink( $path ); // Intenta eliminar el archivo suprimiendo posibles avisos por permisos.
            }
        }
    }

    /**
     * Determina el directorio y la ruta de archivo más adecuados según los permisos disponibles.
     *
     * @return array{directory:string,file:string}
     */
    protected static function resolve_log_location() { // Método protegido que calcula la mejor ubicación para el archivo de log.
        $locations = array(); // Inicializa el listado de ubicaciones candidatas.
        if ( function_exists( 'wp_upload_dir' ) ) { // Comprueba si está disponible la función que informa del directorio de subidas.
            $uploads = wp_upload_dir(); // Recupera la información completa del directorio de subidas de WordPress.
            if ( is_array( $uploads ) && ! empty( $uploads['basedir'] ) ) { // Comprueba que la información sea válida y contenga el directorio base.
                $uploads_dir = trailingslashit( $uploads['basedir'] ) . 'cf7-option-limiter'; // Calcula un subdirectorio dedicado dentro de las subidas.
                $locations[] = $uploads_dir; // Añade el directorio candidato a la lista para evaluarlo después.
            }
        }
        $locations[] = trailingslashit( CF7_OPTION_LIMITER_DIR ); // Añade el directorio del plugin como alternativa de respaldo.
        foreach ( $locations as $directory ) { // Recorre cada directorio candidato.
            $prepared_directory = self::prepare_directory( $directory ); // Intenta crear el directorio y verificar que sea escribible.
            if ( $prepared_directory ) { // Comprueba si la preparación se completó correctamente.
                $file = trailingslashit( $prepared_directory ) . self::LOG_FILENAME; // Construye la ruta completa del archivo de log dentro del directorio listo.
                return array( // Devuelve la información necesaria para trabajar con el log.
                    'directory' => $prepared_directory, // Directorio listo para almacenar los archivos.
                    'file'      => $file, // Ruta completa del archivo de log principal.
                );
            }
        }
        return array( // Devuelve un conjunto vacío cuando ninguna ubicación es válida.
            'directory' => '', // Directorio vacío que indica que no hay ruta utilizable.
            'file'      => '', // Ruta vacía que impide escribir entradas en el log.
        );
    }

    /**
     * Garantiza que un directorio exista y sea escribible.
     *
     * @param string $directory Ruta del directorio que se desea preparar.
     *
     * @return string
     */
    protected static function prepare_directory( $directory ) { // Método protegido que intenta crear y validar un directorio de trabajo.
        if ( empty( $directory ) ) { // Comprueba si la ruta proporcionada está vacía.
            return ''; // Devuelve cadena vacía para indicar que el directorio no es válido.
        }
        if ( ! is_dir( $directory ) ) { // Comprueba si el directorio todavía no existe.
            if ( function_exists( 'wp_mkdir_p' ) ) { // Comprueba si está disponible la función nativa de WordPress para crear directorios anidados.
                if ( ! wp_mkdir_p( $directory ) ) { // Intenta crear el directorio utilizando la función específica.
                    return ''; // Devuelve cadena vacía cuando no se pudo crear el directorio.
                }
            } elseif ( ! mkdir( $directory, 0755, true ) && ! is_dir( $directory ) ) { // Intenta crear el directorio utilizando mkdir estándar cuando no existe la función de WordPress.
                return ''; // Devuelve cadena vacía si la creación falla.
            }
        }
        if ( ! is_writable( $directory ) ) { // Comprueba si el directorio existente no permite escrituras.
            return ''; // Devuelve cadena vacía porque no se podrá trabajar dentro de ese directorio.
        }
        return rtrim( $directory, '/\\' ); // Devuelve la ruta preparada sin barras finales redundantes.
    }
}
