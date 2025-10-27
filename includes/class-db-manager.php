<?php
// Impide que se acceda al archivo directamente.
if ( ! defined( 'ABSPATH' ) ) { // Comprueba si la ejecución proviene de WordPress.
    exit; // Finaliza la ejecución si no es así.
}

// Clase responsable de gestionar la tabla personalizada y sus operaciones.
class CF7_OptionLimiter_DB { // Declara la clase principal de acceso a datos.

    // Nombre completo de la tabla incluyendo el prefijo dinámico.
    protected static $table_name = ''; // Almacena el nombre de la tabla calculado en tiempo de ejecución.

    // Nombre completo de la tabla de excepciones incluyendo el prefijo dinámico.
    protected static $overrides_table_name = ''; // Almacena el nombre de la tabla de excepciones calculado en tiempo de ejecución.

    // Nombre de la opción que almacena la preferencia de limpieza durante la desactivación.
    const CLEANUP_OPTION = 'cf7_option_limiter_cleanup_preference'; // Constante que identifica la opción de preferencia de borrado.

    // Nombre de la opción que conserva la última versión del esquema aplicada correctamente.
    const SCHEMA_VERSION_OPTION = 'cf7_option_limiter_schema_version'; // Constante que permitirá ejecutar migraciones sólo cuando la versión cambie.

    // Nombre de la opción que almacena la firma del esquema vigente para detectar cambios en el archivo SQL base.
    const SCHEMA_SIGNATURE_OPTION = 'cf7_option_limiter_schema_signature'; // Constante que ayuda a sincronizar automáticamente la estructura de la tabla.

    /**
     * Inicializa la clase calculando el nombre real de la tabla.
     *
     * @return void
     */
    public static function init() { // Método de arranque para preparar propiedades estáticas y aplicar migraciones pendientes.
        self::resolve_table_names(); // Calcula y almacena los nombres completos de las tablas personalizadas.
        self::ensure_schema_alignment(); // Verifica que la tabla exista y que coincida con la definición distribuida con el plugin.
        self::maybe_run_pending_migrations(); // Comprueba si la versión del esquema ha cambiado para ejecutar las migraciones necesarias.
    }

    /**
     * Crea la tabla personalizada durante la activación del plugin.
     *
     * @return void
     */
    public static function activate() { // Método ejecutado al activar el plugin.
        self::resolve_table_names(); // Calcula inmediatamente los nombres completos de las tablas antes de trabajar con ellas.
        global $wpdb; // Obtiene el objeto global de base de datos.
        $schema = self::get_schema(); // Recupera el SQL de creación de tabla desde el archivo correspondiente.
        $upgrade_path = trailingslashit( ABSPATH ) . 'wp-admin/includes/upgrade.php'; // Calcula la ruta al archivo de utilidades de base de datos de WordPress.
        if ( file_exists( $upgrade_path ) ) { // Comprueba si el archivo existe en el entorno actual.
            require_once $upgrade_path; // Incluye las funciones de actualización cuando están disponibles.
        }
        if ( ! function_exists( 'dbDelta' ) ) { // Comprueba que la función dbDelta esté disponible tras la inclusión condicional.
            return; // Finaliza silenciosamente si no se puede acceder a dbDelta para evitar errores fatales en entornos reducidos.
        }
        dbDelta( $schema ); // Ejecuta el SQL usando dbDelta para crear o actualizar la tabla principal y dependencias declaradas.
        self::store_schema_signature( self::get_schema_signature( $schema ) ); // Registra la firma del esquema aplicado durante la activación para detectar cambios futuros.
        self::maybe_upgrade_columns(); // Ejecuta comprobaciones adicionales para mantener el esquema actualizado.
        self::run_migrations(); // Ejecuta los scripts incrementales para garantizar la existencia de tablas y columnas nuevas.
        self::mark_schema_version(); // Registra la versión actual del esquema tras aplicar correctamente la actualización.
        CF7_OptionLimiter_Logger::log( 'plugin_activated', array( 'operation' => 'activate_tables' ), true ); // Registra que la activación completó la preparación de tablas.
    }

    /**
     * Calcula los nombres completos de las tablas personalizadas respetando el prefijo de WordPress.
     *
     * @return void
     */
    protected static function resolve_table_names() { // Método protegido que establece los nombres de las tablas personalizadas.
        global $wpdb; // Accede al objeto global de base de datos para conocer el prefijo activo.
        self::$table_name           = $wpdb->prefix . 'cf7_option_limits'; // Calcula el nombre de la tabla principal utilizando el prefijo dinámico.
        self::$overrides_table_name = $wpdb->prefix . 'cf7_option_limits_overrides'; // Calcula el nombre de la tabla de excepciones con el mismo prefijo.
    }

    /**
     * Comprueba si existe una actualización pendiente del esquema y, en caso afirmativo, ejecuta las migraciones correspondientes.
     *
     * @return void
     */
    protected static function maybe_run_pending_migrations() { // Método protegido que controla la ejecución condicional de migraciones.
        if ( ! defined( 'CF7_OPTION_LIMITER_VERSION' ) ) { // Comprueba que la constante de versión del plugin esté disponible antes de continuar.
            return; // Finaliza sin hacer nada cuando la constante todavía no ha sido definida.
        }
        self::maybe_upgrade_columns(); // Ejecuta comprobaciones estructurales incluso cuando la versión almacenada coincide para garantizar columnas críticas.
        $stored_version = get_option( self::SCHEMA_VERSION_OPTION ); // Recupera la última versión del esquema aplicada en el sitio.
        $target_version = CF7_OPTION_LIMITER_VERSION; // Determina la versión objetivo coincidente con el código actual del plugin.
        if ( is_string( $stored_version ) && version_compare( $stored_version, $target_version, '>=' ) ) { // Comprueba si la base de datos ya está en la versión esperada o en una superior.
            return; // Evita ejecutar migraciones nuevamente cuando no son necesarias.
        }
        self::run_migrations(); // Ejecuta los scripts SQL incrementales para adaptar el esquema a la versión actual.
        self::mark_schema_version(); // Actualiza la opción persistente para reflejar que la migración ya se ejecutó.
    }

    /**
     * Actualiza la opción persistente que almacena la versión del esquema aplicada.
     *
     * @return void
     */
    protected static function mark_schema_version() { // Método protegido que almacena la versión del esquema en la base de datos de opciones.
        if ( ! defined( 'CF7_OPTION_LIMITER_VERSION' ) ) { // Comprueba que la constante de versión del plugin esté disponible.
            return; // Finaliza silenciosamente cuando la constante aún no existe (por ejemplo, durante pruebas parciales).
        }
        update_option( self::SCHEMA_VERSION_OPTION, CF7_OPTION_LIMITER_VERSION ); // Guarda la versión del esquema para evitar ejecutar migraciones en cada carga.
        self::store_schema_signature( self::get_schema_signature( self::get_schema() ) ); // Actualiza la firma almacenada para mantener sincronizado el registro con la definición actual.
    }

    /**
     * Realiza acciones de limpieza al desactivar el plugin.
     *
     * @return void
     */
    public static function deactivate() { // Método llamado al desactivar el plugin.
        $preference = get_option( self::CLEANUP_OPTION ); // Recupera la preferencia almacenada antes de la desactivación.
        if ( 'remove' === $preference ) { // Comprueba si se solicitó eliminar los datos.
            self::purge_limits(); // Elimina todos los registros almacenados en las tablas del plugin.
        }
        delete_option( self::CLEANUP_OPTION ); // Limpia la preferencia independientemente de la acción realizada.
        CF7_OptionLimiter_Logger::log( 'plugin_deactivated', array( 'operation' => 'deactivate_plugin' ), true ); // Registra que se desactivó el plugin anotando el usuario responsable.
    }

    /**
     * Ejecuta la limpieza cuando el plugin se desinstala desde el listado de plugins.
     *
     * @return void
     */
    public static function uninstall() { // Método que se ejecuta al eliminar el plugin desde WordPress.
        $preference = get_option( self::CLEANUP_OPTION ); // Recupera la preferencia almacenada.
        if ( 'remove' === $preference ) { // Comprueba si se debe realizar la limpieza final.
            self::purge_limits(); // Elimina los datos restantes de las tablas del plugin.
        }
        delete_option( self::CLEANUP_OPTION ); // Elimina la preferencia para evitar que quede almacenada tras la desinstalación.
        CF7_OptionLimiter_Logger::delete_logs(); // Elimina cualquier archivo de log creado por el plugin al desinstalarlo completamente.
        CF7_OptionLimiter_Logger::log( 'plugin_uninstalled', array( 'operation' => 'uninstall_plugin' ), true ); // Registra que el plugin se eliminó definitivamente del sitio.
    }

    /**
     * Obtiene el esquema SQL desde el archivo de definición.
     *
     * @return string
     */
    protected static function get_schema() { // Método protegido que devuelve el SQL de creación.
        $schema_path = CF7_OPTION_LIMITER_DIR . 'sql/create_table.sql'; // Calcula la ruta absoluta del archivo SQL.
        $schema_sql  = file_get_contents( $schema_path ); // Lee el contenido del archivo con la definición de la tabla.
        return str_replace( '{prefix}', self::get_table_prefix(), $schema_sql ); // Reemplaza el marcador con el prefijo real.
    }

    /**
     * Calcula una firma hash del esquema recibido para detectar cambios estructurales.
     *
     * @param string $schema_sql Contenido SQL con la definición completa de la tabla.
     *
     * @return string
     */
    protected static function get_schema_signature( $schema_sql ) { // Método protegido que resume el esquema en un hash estable.
        return md5( (string) $schema_sql ); // Devuelve un hash MD5 del SQL recibido para comparar versiones del esquema.
    }

    /**
     * Almacena de forma persistente la firma del esquema actualmente aplicado.
     *
     * @param string $signature Hash del esquema que se desea almacenar.
     *
     * @return void
     */
    protected static function store_schema_signature( $signature ) { // Método protegido que persiste la firma del esquema vigente.
        if ( empty( $signature ) ) { // Comprueba si la firma calculada es válida.
            return; // Finaliza sin realizar cambios cuando no existe firma que almacenar.
        }
        update_option( self::SCHEMA_SIGNATURE_OPTION, $signature ); // Guarda la firma del esquema para futuras comparaciones.
    }

    /**
     * Garantiza que la tabla personalizada exista y coincida con la definición distribuida con el plugin.
     *
     * @return void
     */
    protected static function ensure_schema_alignment() { // Método protegido que sincroniza la estructura de la tabla cuando sea necesario.
        $schema_sql = self::get_schema(); // Obtiene la definición actual del esquema desde el archivo SQL.
        if ( empty( $schema_sql ) ) { // Comprueba si no se pudo recuperar el SQL de referencia.
            return; // Finaliza porque no es posible comparar ni aplicar la definición.
        }
        $upgrade_path = trailingslashit( ABSPATH ) . 'wp-admin/includes/upgrade.php'; // Calcula la ruta al archivo que expone dbDelta.
        if ( file_exists( $upgrade_path ) ) { // Comprueba si el archivo está disponible en el entorno actual.
            require_once $upgrade_path; // Incluye las funciones necesarias para ejecutar dbDelta cuando sea posible.
        }
        if ( ! function_exists( 'dbDelta' ) ) { // Comprueba si dbDelta sigue sin estar disponible tras la inclusión condicional.
            return; // Finaliza silenciosamente para evitar errores fatales en entornos de pruebas limitados.
        }
        global $wpdb; // Accede al objeto global de base de datos para ejecutar las comprobaciones.
        $table_exists = $wpdb->get_var( // Ejecuta una consulta directa para comprobar la existencia de la tabla principal.
            $wpdb->prepare( // Prepara la consulta utilizando parámetros seguros.
                'SHOW TABLES LIKE %s', // Consulta SQL que busca la tabla según su nombre completo.
                self::$table_name // Nombre dinámico de la tabla principal con prefijo.
            )
        );
        $expected_signature = self::get_schema_signature( $schema_sql ); // Calcula el hash esperado del esquema distribuido.
        $stored_signature   = get_option( self::SCHEMA_SIGNATURE_OPTION ); // Recupera el hash almacenado la última vez que se sincronizó la estructura.
        if ( ! $table_exists || $stored_signature !== $expected_signature ) { // Comprueba si la tabla no existe o si la definición cambió.
            dbDelta( $schema_sql ); // Ejecuta dbDelta para crear o actualizar la estructura según el SQL actual.
            self::store_schema_signature( $expected_signature ); // Actualiza la firma almacenada tras aplicar la definición.
        }
    }

    /**
     * Elimina todos los registros almacenados en las tablas personalizadas del plugin.
     *
     * @return array<string, int>
     */
    public static function purge_limits() { // Método público que vacía las tablas personalizadas del plugin.
        global $wpdb; // Accede al objeto global de base de datos de WordPress.
        self::init(); // Asegura que los nombres completos de las tablas estén inicializados.
        $deleted_limits     = $wpdb->query( 'DELETE FROM ' . self::$table_name ); // Ejecuta la eliminación sobre la tabla principal.
        $deleted_overrides  = $wpdb->query( 'DELETE FROM ' . self::$overrides_table_name ); // Ejecuta la eliminación sobre la tabla de excepciones.
        $deleted_limits     = is_numeric( $deleted_limits ) ? (int) $deleted_limits : 0; // Normaliza el número de filas eliminadas en la tabla principal.
        $deleted_overrides  = is_numeric( $deleted_overrides ) ? (int) $deleted_overrides : 0; // Normaliza el número de filas eliminadas en la tabla de excepciones.
        CF7_OptionLimiter_Logger::log( 'records_purged', array( 'limits' => $deleted_limits, 'overrides' => $deleted_overrides ), true ); // Registra el vaciado de las tablas incluyendo el número de filas afectadas.
        return array( // Devuelve un resumen de filas eliminadas por tabla.
            'limits'    => $deleted_limits, // Número de filas eliminadas de la tabla principal.
            'overrides' => $deleted_overrides, // Número de filas eliminadas de la tabla de excepciones.
        );
    }

    /**
     * Devuelve el prefijo actual de la base de datos respetando instalaciones multisitio.
     *
     * @return string
     */
    protected static function get_table_prefix() { // Método protegido para obtener el prefijo actual.
        global $wpdb; // Accede al objeto global de base de datos.
        return $wpdb->prefix; // Devuelve el prefijo estándar correspondiente al sitio actual.
    }

    /**
     * Recupera todas las reglas almacenadas para mostrarlas en la administración.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get_all_limits() { // Método público para obtener todas las reglas configuradas.
        global $wpdb; // Utiliza el objeto global de base de datos.
        self::init(); // Asegura que el nombre de la tabla esté disponible.
        $query = "SELECT * FROM " . self::$table_name . " ORDER BY form_id, field_name, option_value"; // Prepara la consulta SQL general sin filtros.
        return $wpdb->get_results( $query, ARRAY_A ); // Ejecuta la consulta y devuelve los resultados como arreglo asociativo.
    }

    /**
     * Recupera un subconjunto paginado de reglas filtrado opcionalmente por formulario.
     *
     * @param int $form_id   Identificador del formulario a filtrar, cero para todos.
     * @param int $per_page  Número de resultados por página.
     * @param int $offset    Desplazamiento inicial calculado a partir de la página solicitada.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get_limits_filtered( $form_id, $per_page, $offset ) { // Devuelve una porción de reglas según filtros y paginación.
        global $wpdb; // Utiliza el objeto global de base de datos proporcionado por WordPress.
        self::init(); // Asegura que el nombre de la tabla esté listo antes de construir la consulta.
        $form_id  = (int) $form_id; // Normaliza el identificador del formulario recibido.
        $per_page = max( 0, (int) $per_page ); // Garantiza que el tamaño de página no sea negativo.
        $offset   = max( 0, (int) $offset ); // Garantiza que el desplazamiento no sea negativo.
        $sql      = "SELECT * FROM " . self::$table_name; // Punto de partida de la consulta SQL.
        if ( $form_id > 0 ) { // Comprueba si se debe aplicar el filtro por formulario.
            $sql .= $wpdb->prepare( " WHERE form_id = %d", $form_id ); // Añade la cláusula WHERE parametrizada para el formulario.
        }
        $sql .= " ORDER BY form_id, field_name, option_value"; // Ordena los resultados para mantener una salida consistente.
        if ( $per_page > 0 ) { // Comprueba si se solicitó limitar el número de filas.
            $sql .= sprintf( ' LIMIT %d OFFSET %d', $per_page, $offset ); // Añade la cláusula LIMIT/OFFSET utilizando enteros ya normalizados.
        }
        return $wpdb->get_results( $sql, ARRAY_A ); // Ejecuta la consulta y devuelve las filas resultantes como arreglo asociativo.
    }

    /**
     * Calcula el número total de reglas disponibles respetando el filtro opcional por formulario.
     *
     * @param int $form_id Identificador del formulario a filtrar, cero para todos.
     *
     * @return int
     */
    public static function count_limits( $form_id ) { // Devuelve el total de reglas considerando el filtro seleccionado.
        global $wpdb; // Utiliza el objeto global de base de datos para ejecutar la consulta.
        self::init(); // Asegura que el nombre de la tabla esté configurado.
        $form_id = (int) $form_id; // Normaliza el identificador del formulario.
        $sql     = "SELECT COUNT(*) FROM " . self::$table_name; // Consulta base que calcula el total de filas.
        if ( $form_id > 0 ) { // Comprueba si se debe filtrar por formulario.
            $sql .= $wpdb->prepare( " WHERE form_id = %d", $form_id ); // Añade la cláusula WHERE parametrizada cuando procede.
        }
        $result = $wpdb->get_var( $sql ); // Ejecuta la consulta y recupera el valor escalar resultante.
        return $result ? (int) $result : 0; // Devuelve el total convertido a entero o cero cuando no hay coincidencias.
    }

    /**
     * Obtiene todas las reglas asociadas a un formulario y campo específicos.
     *
     * @param int    $form_id    Identificador del formulario.
     * @param string $field_name Nombre del campo dentro del formulario.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get_limits_for_field( $form_id, $field_name ) { // Método para recuperar reglas por campo.
        global $wpdb; // Accede al objeto de base de datos.
        self::init(); // Prepara el nombre de la tabla.
        $sql = $wpdb->prepare( // Prepara la consulta de forma segura.
            "SELECT * FROM " . self::$table_name . " WHERE form_id = %d AND field_name = %s", // Consulta parametrizada.
            $form_id, // Primer parámetro: identificador del formulario.
            $field_name // Segundo parámetro: nombre del campo.
        );
        return $wpdb->get_results( $sql, ARRAY_A ); // Ejecuta la consulta y devuelve los resultados.
    }

    /**
     * Obtiene una única regla asociada a formulario, campo y valor específicos.
     *
     * @param int    $form_id    Identificador del formulario.
     * @param string $field_name Nombre del campo.
     * @param string $option     Valor concreto de la opción.
     *
     * @return array<string, mixed>|null
     */
    public static function get_limit( $form_id, $field_name, $option ) { // Método para recuperar una regla concreta.
        global $wpdb; // Objeto de base de datos de WordPress.
        self::init(); // Configura la tabla si es necesario.
        $sql = $wpdb->prepare( // Prepara la consulta con parámetros seguros.
            "SELECT * FROM " . self::$table_name . " WHERE form_id = %d AND field_name = %s AND option_value = %s", // Consulta SQL parametrizada.
            $form_id, // Identificador del formulario.
            $field_name, // Nombre del campo en el formulario.
            $option // Valor específico de la opción a consultar.
        );
        return $wpdb->get_row( $sql, ARRAY_A ); // Ejecuta la consulta y devuelve la fila encontrada o null.
    }

    /**
     * Recupera una regla concreta utilizando su identificador interno.
     *
     * @param int $rule_id Identificador autoincremental de la regla.
     *
     * @return array<string, mixed>|null
     */
    public static function get_limit_by_id( $rule_id ) { // Obtiene una regla mediante su ID.
        global $wpdb; // Objeto global de base de datos.
        self::init(); // Asegura que el nombre de la tabla esté disponible antes de consultar.
        if ( $rule_id <= 0 ) { // Comprueba que el ID proporcionado sea válido.
            return null; // Devuelve null cuando el identificador no es utilizable.
        }
        $sql = $wpdb->prepare( // Construye la consulta segura para obtener la fila específica.
            "SELECT * FROM " . self::$table_name . " WHERE id = %d", // Consulta SQL filtrada por la columna primaria.
            $rule_id // Identificador de la regla que se desea recuperar.
        );
        return $wpdb->get_row( $sql, ARRAY_A ); // Ejecuta la consulta y devuelve la fila asociada o null si no existe.
    }

    /**
     * Recupera todas las reglas asociadas a un formulario concreto.
     *
     * @param int $form_id Identificador del formulario de Contact Form 7.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get_limits_by_form( $form_id ) { // Obtiene todas las reglas vinculadas a un formulario específico.
        global $wpdb; // Objeto global de base de datos.
        self::init(); // Inicializa el nombre de la tabla si todavía no se había hecho.
        if ( $form_id <= 0 ) { // Verifica que el identificador del formulario sea válido.
            return array(); // Devuelve un arreglo vacío cuando no hay formulario definido.
        }
        $sql = $wpdb->prepare( // Prepara la consulta SQL parametrizada.
            "SELECT * FROM " . self::$table_name . " WHERE form_id = %d ORDER BY field_name, option_value", // Ordena por campo y opción para mejorar la legibilidad.
            $form_id // Identificador del formulario para filtrar las reglas.
        );
        $results = $wpdb->get_results( $sql, ARRAY_A ); // Ejecuta la consulta y recupera todas las filas asociadas.
        return $results ? $results : array(); // Devuelve el listado de reglas o un arreglo vacío si no existen registros.
    }

    /**
     * Inserta o actualiza una regla en la tabla personalizada.
     *
     * @param array<string, mixed> $data Datos validados y sanitizados de la regla.
     *
     * @return bool
     */
    public static function upsert_limit( $data ) { // Método para crear o actualizar reglas.
        global $wpdb; // Objeto global de base de datos.
        self::init(); // Asegura que el nombre de la tabla esté disponible.
        $trace_id = uniqid( 'cf7ol_save_', true ); // Genera un identificador único para enlazar todas las etapas del proceso de guardado.
        $raw_payload = $data; // Conserva una copia exacta de los datos recibidos antes de la sanitización para documentar el punto de partida.
        $raw_signature = md5( (string) wp_json_encode( $raw_payload ) ); // Calcula una firma hash del payload original para detectar alteraciones durante el flujo.
        CF7_OptionLimiter_Logger::log( // Registra el inicio del proceso antes de modificar los datos recibidos.
            'limit_save_start', // Evento que indica la recepción de la petición de guardado.
            array( // Contexto adicional asociado a la etapa inicial.
                'trace_id'        => $trace_id, // Identificador correlativo que permitirá unir el resto de eventos.
                'stage'           => 'inicio', // Describe la etapa concreta dentro del flujo completo.
                'message'         => __( 'Se ha recibido una petición para guardar un límite y comienza la preparación.', 'cf7-option-limiter' ), // Mensaje descriptivo que aparecerá en el log.
                'form_id'         => isset( $data['form_id'] ) ? (int) $data['form_id'] : 0, // Identificador del formulario incluido en la petición original.
                'field_name'      => isset( $data['field_name'] ) ? (string) $data['field_name'] : '', // Nombre del campo recibido.
                'option_value'    => isset( $data['option_value'] ) ? (string) $data['option_value'] : '', // Valor concreto de la opción sujeto al límite.
                'raw_signature'   => $raw_signature, // Firma hash del payload original.
                'payload_recibido'=> $raw_payload, // Copia íntegra de los datos tal y como llegaron antes de ser sanitizados.
            ),
            true // Fuerza el registro incluso cuando el modo depuración detallado no está activo.
        );
        $data = self::sanitize_data( $data ); // Sanitiza profundamente los valores recibidos para normalizarlos antes de persistirlos.
        $sanitized_signature = md5( (string) wp_json_encode( $data ) ); // Calcula la firma hash de los datos listos para su almacenamiento.
        $differences = array(); // Inicializa el arreglo que documentará los cambios detectados tras la sanitización.
        foreach ( $data as $key => $value ) { // Recorre cada clave de los datos sanitizados para compararlos con la versión original.
            $original = array_key_exists( $key, $raw_payload ) ? $raw_payload[ $key ] : null; // Recupera el valor original asociado a la clave actual.
            if ( $original !== $value ) { // Comprueba si la sanitización modificó el valor original.
                $differences[ $key ] = array( // Registra los dos valores para análisis detallado en el log.
                    'antes' => $original, // Valor previo a la sanitización.
                    'despues' => $value, // Valor resultante tras aplicar la sanitización.
                );
            }
        }
        CF7_OptionLimiter_Logger::log( // Registra el resultado de la sanitización antes de interactuar con la base de datos.
            'limit_save_sanitized', // Evento dedicado a documentar la preparación de los datos.
            array( // Contexto que detalla la etapa de sanitización.
                'trace_id'             => $trace_id, // Identificador correlativo del flujo actual.
                'stage'                => 'sanitizacion', // Nombre descriptivo de la etapa.
                'message'              => __( 'Los datos han sido sanitizados y normalizados para su almacenamiento.', 'cf7-option-limiter' ), // Mensaje informativo.
                'form_id'              => $data['form_id'], // Identificador del formulario tras la sanitización.
                'field_name'           => $data['field_name'], // Nombre del campo tras la sanitización.
                'option_value'         => $data['option_value'], // Valor de la opción ya normalizado.
                'raw_signature'        => $raw_signature, // Firma del payload original para facilitar la comparación.
                'sanitized_signature'  => $sanitized_signature, // Firma de los datos sanitizados.
                'cambios_detectados'   => $differences, // Conjunto de diferencias detectadas durante la sanitización.
                'payload_sanitizado'   => $data, // Copia íntegra del payload listo para persistirse.
            ),
            true // Fuerza el registro del evento para disponer de la traza incluso con el log mínimo.
        );
        $existing = self::get_limit( $data['form_id'], $data['field_name'], $data['option_value'] ); // Recupera la regla existente para decidir si se actualiza o inserta.
        $existing_signature = ! empty( $existing ) ? md5( (string) wp_json_encode( $existing ) ) : ''; // Calcula la firma hash de la fila encontrada para documentarla en el log.
        CF7_OptionLimiter_Logger::log( // Registra el resultado de la consulta previa a la escritura.
            'limit_save_existing', // Evento específico para la comprobación de existencia.
            array( // Contexto detallado de la etapa de búsqueda.
                'trace_id'            => $trace_id, // Identificador correlativo del flujo de guardado.
                'stage'               => 'consulta', // Describe que la etapa corresponde a la consulta previa.
                'message'             => __( 'Se ha completado la consulta previa para determinar si la fila ya existe.', 'cf7-option-limiter' ), // Mensaje legible para el log.
                'form_id'             => $data['form_id'], // Identificador del formulario consultado.
                'field_name'          => $data['field_name'], // Nombre del campo consultado.
                'option_value'        => $data['option_value'], // Valor de la opción consultada.
                'found'               => ! empty( $existing ), // Resume si se encontró una fila previa.
                'existing_id'         => ! empty( $existing['id'] ) ? (int) $existing['id'] : 0, // Identificador de la fila encontrada o cero cuando no existe.
                'existing_signature'  => $existing_signature, // Firma hash de la fila existente para poder compararla en el futuro.
                'fila_encontrada'     => $existing, // Copia íntegra de la fila localizada, útil cuando la depuración detallada está activa.
            ),
            true // Fuerza el registro independientemente del modo de depuración activo.
        );
        $operation = $existing ? 'update' : 'insert'; // Determina el tipo de operación que se ejecutará sobre la tabla.
        CF7_OptionLimiter_Logger::log( // Registra el momento exacto en el que se solicitará la escritura a la base de datos.
            'limit_save_persist', // Evento que representa la etapa de persistencia.
            array( // Contexto que describe la operación que se enviará a la base de datos.
                'trace_id'            => $trace_id, // Identificador correlativo del flujo.
                'stage'               => 'persistencia', // Nombre de la etapa actual dentro del flujo completo.
                'message'             => __( 'Se va a ejecutar la operación sobre la base de datos con los datos preparados.', 'cf7-option-limiter' ), // Mensaje informativo que aparecerá en el log.
                'form_id'             => $data['form_id'], // Identificador del formulario afectado.
                'field_name'          => $data['field_name'], // Nombre del campo que se modificará.
                'option_value'        => $data['option_value'], // Valor de la opción objetivo.
                'operation'           => $operation, // Tipo de operación que se solicitará (insert o update).
                'table'               => self::$table_name, // Nombre de la tabla sobre la que se operará.
                'sanitized_signature' => $sanitized_signature, // Firma de los datos que se enviarán a la base de datos.
                'payload_final'       => $data, // Copia íntegra de los datos que se enviarán a la consulta SQL.
            ),
            true // Fuerza la escritura del evento en el log.
        );
        $formats = array( // Define los formatos de cada columna para reutilizarlos en insert y update.
            '%d', // form_id.
            '%s', // field_name.
            '%s', // option_value.
            '%d', // hide_exhausted.
            '%d', // max_count.
            '%d', // current_count.
            '%s', // limit_period.
            '%s', // limit_reset.
            '%s', // custom_message.
            '%s', // created_at.
            '%s', // updated_at.
        );
        if ( $existing ) { // Comprueba si ya existe una fila para esta combinación única.
            $result = $wpdb->update( // Ejecuta una actualización directa sobre la fila localizada.
                self::$table_name, // Tabla objetivo de la actualización.
                $data, // Datos que reemplazarán a los existentes.
                array( 'id' => (int) $existing['id'] ), // Condición que limita la actualización al identificador concreto.
                $formats, // Formatos correspondientes a las columnas actualizadas.
                array( '%d' ) // Formato de la cláusula WHERE aplicado al identificador numérico.
            );
        } else { // Cuando la regla todavía no existe.
            $result = $wpdb->insert( // Ejecuta una inserción estándar en la tabla personalizada.
                self::$table_name, // Tabla de destino de la inserción.
                $data, // Datos que se insertarán como nueva fila.
                $formats // Formatos que aseguran el tipo correcto de cada columna.
            );
        }
        $rows_affected = ( false === $result ) ? 0 : (int) $result; // Convierte el resultado numérico a entero para registrarlo en el log.
        $success = ( false !== $result ); // Determina si la operación se consideró exitosa.
        $final_context = array( // Construye el contexto común que resume el desenlace de la operación.
            'trace_id'            => $trace_id, // Identificador correlativo del flujo de guardado.
            'stage'               => $success ? 'finalizado' : 'error', // Indica si el proceso terminó correctamente o con fallo.
            'message'             => $success ? __( 'La operación de guardado se completó correctamente.', 'cf7-option-limiter' ) : __( 'La operación de guardado ha fallado, revisa los detalles adjuntos.', 'cf7-option-limiter' ), // Mensaje humanamente legible del resultado.
            'form_id'             => $data['form_id'], // Identificador del formulario involucrado en la operación final.
            'field_name'          => $data['field_name'], // Nombre del campo afectado.
            'option_value'        => $data['option_value'], // Valor de la opción afectada.
            'operation'           => $operation, // Tipo de operación realizada.
            'table'               => self::$table_name, // Tabla utilizada para la escritura.
            'rows_affected'       => $rows_affected, // Número de filas impactadas según el resultado devuelto por $wpdb.
            'success'             => $success, // Resultado lógico que permitirá filtrar rápidamente las operaciones fallidas.
            'sanitized_signature' => $sanitized_signature, // Firma hash de los datos persistidos para comprobar integridad.
        );
        if ( isset( $wpdb->last_error ) && $wpdb->last_error ) { // Comprueba si la base de datos devolvió un mensaje de error.
            $final_context['db_error'] = $wpdb->last_error; // Almacena el mensaje de error para facilitar el diagnóstico.
        }
        if ( isset( $wpdb->last_query ) && $wpdb->last_query ) { // Comprueba si existe una consulta registrada.
            $final_context['query'] = $wpdb->last_query; // Adjunta la última consulta ejecutada para analizarla cuando sea necesario.
        }
        if ( isset( $wpdb->insert_id ) && $wpdb->insert_id ) { // Comprueba si la operación generó un nuevo identificador autoincremental.
            $final_context['insert_id'] = (int) $wpdb->insert_id; // Registra el identificador para vincularlo con otras auditorías.
        }
        CF7_OptionLimiter_Logger::log( // Registra el desenlace de la operación, diferenciando éxito o error.
            $success ? 'limit_save_complete' : 'limit_save_error', // Selecciona el evento adecuado según el resultado obtenido.
            $final_context, // Contexto previamente construido con la información relevante.
            true // Fuerza la escritura del evento para disponer de la traza incluso con log mínimo.
        );
        $log_context = array( // Construye el contexto que se enviará al log.
            'form_id'        => $data['form_id'], // Identificador del formulario afectado.
            'field_name'     => $data['field_name'], // Nombre del campo asociado al límite.
            'option_value'   => $data['option_value'], // Valor concreto de la opción limitada.
            'hide_exhausted' => $data['hide_exhausted'], // Indicador de si la opción se ocultará al agotarse.
            'max_count'      => $data['max_count'], // Número máximo permitido configurado.
            'operation'      => $operation, // Tipo de operación ejecutada (insert o update).
            'success'        => $success, // Resultado lógico de la operación.
            'trace_id'       => $trace_id, // Identificador correlativo para enlazar este resumen con el resto de eventos generados.
            'stage'          => 'resumen', // Etapa simbólica que indica que la entrada resume todo el proceso.
            'message'        => __( 'Resumen final del guardado de límite tras ejecutar todas las etapas.', 'cf7-option-limiter' ), // Mensaje descriptivo del resumen.
            'table'          => self::$table_name, // Tabla objetivo que se modificó durante el proceso.
            'rows_affected'  => $rows_affected, // Número de filas impactadas útil para detectar inserciones sin efecto.
            'sanitized_signature' => $sanitized_signature, // Firma hash del payload persistido que completa la traza.
        );
        if ( CF7_OptionLimiter_Logger::is_debug_enabled() ) { // Añade información extendida únicamente cuando el modo depuración está activo.
            $log_context['data']   = $data; // Adjunta los datos completos que se intentaron persistir.
            $log_context['result'] = $result; // Registra el valor devuelto por la operación de base de datos.
            $log_context['query']  = isset( $wpdb->last_query ) ? $wpdb->last_query : ''; // Incluye la última consulta ejecutada cuando está disponible.
            $log_context['db_error'] = isset( $wpdb->last_error ) ? $wpdb->last_error : ''; // Adjunta el posible error devuelto por la base de datos.
        }
        CF7_OptionLimiter_Logger::log( 'limit_saved', $log_context, true ); // Registra la operación en el log incluso cuando la depuración está desactivada.
        return $success; // Devuelve true cuando la operación se completó correctamente.
    }

    /**
     * Elimina una regla concreta identificada por su ID.
     *
     * @param int $rule_id Identificador de la regla a eliminar.
     *
     * @return bool
     */
    public static function delete_limit( $rule_id ) { // Método para borrar reglas existentes.
        global $wpdb; // Objeto global de base de datos.
        self::init(); // Asegura que la tabla esté disponible.
        $result = $wpdb->delete( // Ejecuta la sentencia DELETE segura.
            self::$table_name, // Tabla de origen.
            array( 'id' => (int) $rule_id ), // Condición de borrado por ID convertido a entero.
            array( '%d' ) // Define el formato del parámetro ID.
        );
        $success = $result !== false; // Determina si la eliminación fue exitosa.
        $context = array( // Construye el contexto que se registrará en el log.
            'rule_id' => (int) $rule_id, // Identificador de la regla solicitada para eliminación.
            'success' => $success, // Resultado lógico de la operación.
        );
        if ( CF7_OptionLimiter_Logger::is_debug_enabled() ) { // Añade detalles adicionales únicamente en modo depuración.
            $context['db_error'] = isset( $wpdb->last_error ) ? $wpdb->last_error : ''; // Adjunta el posible error devuelto por la base de datos.
            $context['query']    = isset( $wpdb->last_query ) ? $wpdb->last_query : ''; // Añade la consulta ejecutada cuando está disponible.
        }
        CF7_OptionLimiter_Logger::log( 'limit_deleted', $context, true ); // Registra el resultado de la eliminación forzando la escritura.
        return $success; // Devuelve true si el borrado tuvo éxito.
    }

    /**
     * Incrementa el contador de uso de una regla tras un envío del formulario.
     *
     * @param int    $form_id    Identificador del formulario.
     * @param string $field_name Nombre del campo del formulario.
     * @param string $option     Valor seleccionado.
     *
     * @return bool
     */
    public static function increment_counter( $form_id, $field_name, $option ) { // Método para sumar al contador de uso.
        global $wpdb; // Objeto de base de datos global.
        self::init(); // Asegura el nombre de la tabla.
        $sql = $wpdb->prepare( // Construye la consulta preparada para incrementar el contador con una condición adicional.
            "UPDATE " . self::$table_name . " SET current_count = current_count + 1, updated_at = %s WHERE form_id = %d AND field_name = %s AND option_value = %s AND ( max_count = 0 OR current_count < max_count )", // Consulta SQL parametrizada que evita superar el límite máximo.
            current_time( 'mysql' ), // Fecha y hora actuales en formato MySQL.
            $form_id, // ID del formulario.
            $field_name, // Nombre del campo afectado.
            $option // Valor de opción seleccionado.
        );
        $result = $wpdb->query( $sql ); // Ejecuta la consulta de actualización.
        $success = ( false !== $result && $result > 0 ); // Determina si la actualización incrementó alguna fila.
        $context = array( // Construye el contexto que se registrará en el log.
            'form_id'   => (int) $form_id, // Identificador del formulario implicado en el incremento.
            'field_name'=> $field_name, // Nombre del campo cuyos contadores se actualizan.
            'option'    => $option, // Valor concreto de la opción seleccionada.
            'success'   => $success, // Resultado lógico de la operación.
        );
        if ( CF7_OptionLimiter_Logger::is_debug_enabled() || ! $success ) { // Añade detalles adicionales cuando hay depuración o la operación falla.
            $context['rows_affected'] = $result; // Registra el número de filas afectadas, incluyendo cero o falso.
            $context['query']        = isset( $wpdb->last_query ) ? $wpdb->last_query : ''; // Adjunta la consulta ejecutada cuando está disponible.
            $context['db_error']     = isset( $wpdb->last_error ) ? $wpdb->last_error : ''; // Añade el posible error devuelto por la base de datos.
        }
        CF7_OptionLimiter_Logger::log( 'counter_increment', $context, ! $success ); // Registra la operación forzando la escritura únicamente cuando falla.
        return $success; // Devuelve true únicamente cuando se actualizó alguna fila, evitando incrementos por encima del límite.
    }

    /**
     * Resta unidades del contador actual asegurando que nunca sea negativo.
     *
     * @param int $rule_id Identificador de la regla cuyo contador se desea ajustar.
     *
     * @return bool
     */
    public static function decrement_counter_by_id( $rule_id ) { // Método que libera un uso consumido de una regla concreta.
        global $wpdb; // Accede al objeto global de base de datos proporcionado por WordPress.
        self::init(); // Garantiza que los nombres de tabla estén calculados antes de ejecutar consultas.
        $rule_id = (int) $rule_id; // Normaliza el identificador recibido convirtiéndolo en un entero.
        if ( $rule_id <= 0 ) { // Comprueba que el identificador sea válido antes de continuar.
            return false; // Devuelve false porque no es posible actualizar una fila sin ID válido.
        }
        $sql = $wpdb->prepare( // Construye la consulta preparada que decrementa el contador con un límite inferior.
            "UPDATE " . self::$table_name . " SET current_count = GREATEST(current_count - 1, 0), updated_at = %s WHERE id = %d", // Consulta que reduce el contador sin permitir valores negativos y actualiza la fecha de modificación.
            current_time( 'mysql' ), // Obtiene la marca temporal actual en formato compatible con MySQL.
            $rule_id // Inserta el identificador de la regla como parámetro seguro.
        );
        $result = $wpdb->query( $sql ); // Ejecuta la actualización preparada en la base de datos.
        $success = ( false !== $result ); // Considera exitosa cualquier ejecución que no devuelva false, incluso si no cambian filas.
        $context = array( // Construye el contexto que se registrará en el log del plugin.
            'rule_id' => $rule_id, // Incluye el identificador de la regla afectada.
            'success' => $success, // Anota si la operación se ejecutó correctamente según la verificación anterior.
            'action'  => 'decrement', // Describe la naturaleza de la operación realizada.
        );
        if ( CF7_OptionLimiter_Logger::is_debug_enabled() || ! $success ) { // Adjunta datos adicionales cuando la depuración está activa o falla la operación.
            $context['query']    = isset( $wpdb->last_query ) ? $wpdb->last_query : ''; // Incluye la última consulta ejecutada para facilitar diagnósticos.
            $context['db_error'] = isset( $wpdb->last_error ) ? $wpdb->last_error : ''; // Añade el posible mensaje de error devuelto por la base de datos.
            $context['rows']     = $result; // Registra el número de filas afectadas para conocer el impacto real.
        }
        CF7_OptionLimiter_Logger::log( 'counter_adjust', $context, ! $success ); // Envía la información al log forzando la escritura cuando la operación falla.
        return $success; // Devuelve el resultado lógico de la operación para que el llamante determine el siguiente paso.
    }

    /**
     * Recupera en bloque las reglas asociadas a una lista de valores concretos.
     *
     * @param int                  $form_id    Identificador del formulario.
     * @param string               $field_name Nombre del campo dentro del formulario.
     * @param array<int, string>   $options    Lista de valores a comprobar.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function get_limits_for_options( $form_id, $field_name, array $options ) { // Método para obtener varias reglas globales en una sola consulta.
        global $wpdb; // Objeto global de base de datos.
        self::init(); // Asegura que el nombre de la tabla esté configurado.
        if ( empty( $options ) ) { // Comprueba si la lista de opciones está vacía.
            return array(); // Devuelve un arreglo vacío cuando no hay valores que consultar.
        }
        $sanitized = array(); // Inicializa el arreglo donde se guardarán los valores sanitizados.
        foreach ( $options as $option ) { // Recorre cada valor recibido.
            $sanitized[] = sanitize_text_field( wp_unslash( (string) $option ) ); // Limpia y normaliza cada valor antes de construir la consulta.
        }
        $placeholders = implode( ', ', array_fill( 0, count( $sanitized ), '%s' ) ); // Genera la lista de marcadores para la cláusula IN.
        $prepared = $wpdb->prepare( // Prepara la consulta SQL con todos los parámetros necesarios.
            "SELECT option_value, max_count, current_count, custom_message, limit_period, limit_reset, hide_exhausted FROM " . self::$table_name . " WHERE form_id = %d AND field_name = %s AND option_value IN ( $placeholders )", // Consulta parametrizada para obtener las reglas globales específicas.
            array_merge( array( (int) $form_id, sanitize_text_field( wp_unslash( $field_name ) ) ), $sanitized ) // Combina los parámetros del formulario y campo con los valores sanitizados.
        );
        $rows = $wpdb->get_results( $prepared, ARRAY_A ); // Ejecuta la consulta y obtiene las filas resultantes.
        if ( empty( $rows ) ) { // Comprueba si la consulta no devolvió resultados.
            return array(); // Devuelve un arreglo vacío cuando no hay reglas registradas para los valores solicitados.
        }
        $indexed = array(); // Inicializa el arreglo asociativo de respuesta.
        foreach ( $rows as $row ) { // Recorre cada fila obtenida de la base de datos.
            $key = isset( $row['option_value'] ) ? (string) $row['option_value'] : ''; // Determina la clave basada en el valor de la opción.
            if ( '' === $key ) { // Comprueba si la clave es válida.
                continue; // Omite filas sin valor de opción definido.
            }
            $indexed[ $key ] = $row; // Asocia la fila completa al valor correspondiente para facilitar su consulta posterior.
        }
        return $indexed; // Devuelve el arreglo indexado por valor de opción.
    }

    /**
     * Recupera todas las excepciones guardadas para su visualización en administración.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get_all_overrides() { // Método público que devuelve todas las excepciones registradas.
        return array(); // Las excepciones por página han sido retiradas, de modo que la colección siempre está vacía.
    }

    /**
     * Obtiene una excepción concreta por formulario, campo, opción y página.
     *
     * @param int    $form_id    Identificador del formulario.
     * @param string $field_name Nombre del campo dentro del formulario.
     * @param string $option     Valor específico de la opción.
     * @param int    $post_id    Identificador del post asociado.
     *
     * @return array<string, mixed>|null
     */
    public static function get_override( $form_id, $field_name, $option, $post_id ) { // Método que recupera una excepción específica.
        return null; // La funcionalidad de excepciones se eliminó, por lo que nunca se devuelve una coincidencia.
    }

    /**
     * Devuelve todas las excepciones registradas para un formulario y campo específicos.
     *
     * @param int    $form_id    Identificador del formulario.
     * @param string $field_name Nombre del campo dentro del formulario.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get_overrides_for_field( $form_id, $field_name ) { // Método que recupera excepciones por campo.
        return array(); // Sin excepciones activas, siempre se devuelve un listado vacío.
    }

    /**
     * Obtiene en bloque las excepciones asociadas a una lista de valores concretos para una página.
     *
     * @param int                  $form_id    Identificador del formulario.
     * @param string               $field_name Nombre del campo dentro del formulario.
     * @param array<int, string>   $options    Lista de valores a comprobar.
     * @param int                  $post_id    Identificador del post asociado.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function get_overrides_for_options( $form_id, $field_name, array $options, $post_id ) { // Método para recuperar varias excepciones simultáneamente.
        return array(); // La característica ha sido retirada, por lo que no existen coincidencias que devolver.
    }

    /**
     * Inserta o actualiza una excepción en la tabla dedicada.
     *
     * @param array<string, mixed> $data Datos validados y sanitizados de la excepción.
     *
     * @return bool
     */
    public static function upsert_override( $data ) { // Método público que guarda excepciones específicas.
        return false; // La escritura de excepciones ya no está disponible, por lo que se devuelve falso.
    }

    /**
     * Incrementa el contador de una excepción concreta garantizando que no supere el máximo configurado.
     *
     * @param int    $form_id    Identificador del formulario.
     * @param string $field_name Nombre del campo.
     * @param string $option     Valor específico de la opción.
     * @param int    $post_id    Identificador del post asociado a la excepción.
     *
     * @return bool
     */
    public static function increment_override_counter( $form_id, $field_name, $option, $post_id ) { // Método que incrementa contadores en la tabla de excepciones.
        return false; // No se realizan incrementos porque las excepciones por página han sido eliminadas.
    }

    /**
     * Devuelve la regla efectiva combinando excepciones y reglas globales priorizando la coincidencia por post.
     *
     * @param int    $form_id    Identificador del formulario.
     * @param string $field_name Nombre del campo dentro del formulario.
     * @param string $option     Valor específico de la opción.
     * @param int    $post_id    Identificador del post donde se evalúa la regla.
     *
     * @return array<string, mixed>|null
     */
    public static function get_effective_limit( $form_id, $field_name, $option, $post_id ) { // Método que resuelve la prioridad entre excepciones y reglas globales.
        $limit = self::get_limit( $form_id, $field_name, $option ); // Recupera la regla global asociada al campo y opción indicados.
        if ( ! empty( $limit ) ) { // Comprueba si existe una regla global.
            $limit['source'] = 'global'; // Marca explícitamente el origen del registro para mantener compatibilidad.
        }
        return $limit; // Devuelve la regla global o null si no existe.
    }

    /**
     * Devuelve las reglas efectivas para una lista de opciones combinando excepciones y reglas globales.
     *
     * @param int                  $form_id    Identificador del formulario.
     * @param string               $field_name Nombre del campo dentro del formulario.
     * @param array<int, string>   $options    Lista de valores a comprobar.
     * @param int                  $post_id    Identificador del post donde se evalúa la prioridad.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function get_effective_limits_for_options( $form_id, $field_name, array $options, $post_id ) { // Método que fusiona reglas globales y excepciones.
        $effective = array(); // Inicializa el arreglo que almacenará las reglas finales.
        if ( empty( $options ) ) { // Comprueba si la lista de opciones está vacía.
            return $effective; // Devuelve el arreglo vacío porque no hay valores que resolver.
        }
        $global_rows = self::get_limits_for_options( $form_id, $field_name, $options ); // Recupera únicamente las reglas globales asociadas a las opciones solicitadas.
        foreach ( $global_rows as $value => $row ) { // Recorre cada resultado global.
            $row['source'] = 'global'; // Marca el origen como regla global para mantener compatibilidad con el flujo previo.
            $effective[ $value ] = $row; // Añade la regla global a la colección final.
        }
        return $effective; // Devuelve el conjunto de reglas efectivas basadas exclusivamente en límites globales.
    }

    /**
     * Incrementa el contador adecuado priorizando excepciones cuando existan.
     *
     * @param int    $form_id    Identificador del formulario.
     * @param string $field_name Nombre del campo dentro del formulario.
     * @param string $option     Valor específico de la opción.
     * @param int    $post_id    Identificador del post asociado.
     *
     * @return bool
     */
    public static function increment_counter_for_context( $form_id, $field_name, $option, $post_id ) { // Método que decide qué contador incrementar según el contexto.
        return self::increment_counter( $form_id, $field_name, $option ); // Sin excepciones específicas, siempre se incrementa el contador global estándar.
    }

    /**
     * Reinicia los contadores de las opciones según el periodo configurado en tablas globales y de excepciones.
     *
     * @return void
     */
    public static function reset_periods() { // Método encargado de evaluar y resetear los contadores por periodo en todas las tablas.
        self::init(); // Asegura que los nombres de tabla estén preparados.
        self::reset_periods_for_table( self::$table_name, false ); // Procesa los reseteos únicamente en la tabla principal, ya que las excepciones han sido retiradas.
    }

    /**
     * Aplica el reinicio periódico a una tabla concreta.
     *
     * @param string $table_name  Nombre de la tabla sobre la que se ejecutará el reinicio.
     * @param bool   $is_override Indica si se está procesando la tabla de excepciones para registrar el origen adecuado en el log.
     *
     * @return void
     */
    protected static function reset_periods_for_table( $table_name, $is_override ) { // Método auxiliar que procesa reseteos para la tabla indicada.
        global $wpdb; // Objeto global de base de datos.
        $query = "SELECT id, form_id, field_name, option_value, limit_period, limit_reset, post_id FROM " . $table_name . " WHERE limit_period != 'none'"; // Consulta que recupera las reglas con periodo activo.
        $rules = $wpdb->get_results( $query, ARRAY_A ); // Ejecuta la consulta y obtiene las reglas relevantes.
        if ( empty( $rules ) ) { // Comprueba si no se encontraron reglas con periodo.
            return; // Finaliza si no hay reglas que procesar.
        }
        $now = current_time( 'timestamp' ); // Obtiene la marca temporal actual con la zona horaria de WordPress.
        foreach ( $rules as $rule ) { // Recorre cada regla recuperada.
            $needs_reset = self::should_reset( $rule, $now ); // Determina si el contador debe reiniciarse.
            if ( ! $needs_reset ) { // Si no es necesario reiniciar para esta regla.
                continue; // Pasa a la siguiente regla.
            }
            $wpdb->update( // Ejecuta la actualización para reiniciar contadores y fecha de reseteo.
                $table_name, // Tabla objetivo.
                array( // Datos a actualizar.
                    'current_count' => 0, // Resetea el contador a cero.
                    'limit_reset'   => current_time( 'mysql' ), // Guarda la fecha actual como último reseteo.
                    'updated_at'    => current_time( 'mysql' ), // Actualiza la columna updated_at para mantener trazabilidad.
                ),
                array( 'id' => (int) $rule['id'] ), // Condición de actualización por ID.
                array( '%d', '%s', '%s' ), // Formatos de los valores actualizados.
                array( '%d' ) // Formato del identificador en la cláusula WHERE.
            );
            $post_id = isset( $rule['post_id'] ) ? (int) $rule['post_id'] : 0; // Obtiene el post asociado si existe.
            $context_message = $is_override
                ? sprintf( __( 'Reinicio automático por periodo en la página %d.', 'cf7-option-limiter' ), $post_id ) // Mensaje específico para excepciones.
                : __( 'Reinicio automático por periodo.', 'cf7-option-limiter' ); // Mensaje estándar para reglas globales.
            $log_context = array( // Construye el contexto que se registrará en el log.
                'form_id'      => isset( $rule['form_id'] ) ? (int) $rule['form_id'] : 0, // Identificador del formulario reiniciado.
                'field_name'   => isset( $rule['field_name'] ) ? $rule['field_name'] : '', // Nombre del campo asociado a la regla.
                'option_value' => isset( $rule['option_value'] ) ? $rule['option_value'] : '', // Valor concreto de la opción reiniciada.
                'message'      => $context_message, // Mensaje contextual que describe el motivo del reinicio.
                'table'        => $table_name, // Tabla afectada por el reinicio automático.
                'override'     => (bool) $is_override, // Indicador de si el reinicio proviene de la tabla de excepciones.
            );
            if ( CF7_OptionLimiter_Logger::is_debug_enabled() ) { // Añade datos adicionales únicamente cuando la depuración está activa.
                $log_context['rule_id'] = (int) $rule['id']; // Identificador de la regla procesada.
                $log_context['post_id'] = $post_id; // Identificador del post asociado cuando aplica.
            }
            CF7_OptionLimiter_Logger::log( 'reset', $log_context ); // Registra el reinicio respetando el modo de depuración configurado.
        }
    }

    /**
     * Determina si una regla requiere reinicio según su periodo y fecha de último reseteo.
     *
     * @param array<string, mixed> $rule Datos de la regla almacenada.
     * @param int                  $current_timestamp Marca temporal actual.
     *
     * @return bool
     */
    protected static function should_reset( $rule, $current_timestamp ) { // Método auxiliar que decide si se debe reiniciar.
        $period = $rule['limit_period']; // Recupera el periodo configurado para la regla.
        $last_reset = empty( $rule['limit_reset'] ) ? 0 : strtotime( $rule['limit_reset'] ); // Convierte la fecha de último reseteo a timestamp.
        if ( $last_reset === false ) { // Comprueba si la conversión de fecha falló.
            $last_reset = 0; // Establece un valor predeterminado para forzar el reseteo.
        }
        if ( $last_reset === 0 ) { // Si nunca se ha reseteado la regla.
            return true; // Se debe reiniciar inmediatamente para inicializar la ventana temporal.
        }
        switch ( $period ) { // Evalúa según el tipo de periodo configurado.
            case 'hour': // Cuando el periodo es por hora.
                return ( $current_timestamp - $last_reset ) >= HOUR_IN_SECONDS; // Comprueba si ha pasado al menos una hora natural.
            case 'day': // Cuando el periodo es diario.
                return gmdate( 'Y-m-d', $current_timestamp + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) !== gmdate( 'Y-m-d', $last_reset + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ); // Compara el día actual con el del último reseteo.
            case 'week': // Cuando el periodo es semanal.
                return gmdate( 'oW', $current_timestamp + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) !== gmdate( 'oW', $last_reset + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ); // Compara la semana ISO actual con la anterior.
            default: // Para cualquier otro valor (incluyendo 'none').
                return false; // No se requiere reinicio.
        }
    }

    /**
     * Sanitiza los datos antes de insertarlos en base de datos.
     *
     * @param array<string, mixed> $data Datos originales.
     *
     * @return array<string, mixed>
     */
    protected static function sanitize_data( $data ) { // Método auxiliar para limpiar datos antes de persistirlos.
        $clean = array(); // Inicializa el arreglo donde se guardarán los valores sanitizados.
        $clean['form_id']       = isset( $data['form_id'] ) ? (int) $data['form_id'] : 0; // Convierte el identificador de formulario a entero.
        $clean['field_name']    = isset( $data['field_name'] ) ? sanitize_text_field( wp_unslash( $data['field_name'] ) ) : ''; // Sanitiza el nombre del campo.
        $clean['option_value']  = isset( $data['option_value'] ) ? sanitize_text_field( wp_unslash( $data['option_value'] ) ) : ''; // Sanitiza el valor de la opción.
        $clean['hide_exhausted'] = isset( $data['hide_exhausted'] ) ? (int) (bool) $data['hide_exhausted'] : 0; // Convierte el indicador de ocultación a entero 0/1.
        $clean['max_count']     = isset( $data['max_count'] ) ? max( 1, (int) $data['max_count'] ) : 1; // Asegura que el máximo sea al menos 1.
        $clean['current_count'] = isset( $data['current_count'] ) ? max( 0, (int) $data['current_count'] ) : 0; // Garantiza un contador no negativo.
        $allowed_periods        = array( 'none', 'hour', 'day', 'week' ); // Lista de periodos permitidos.
        $clean['limit_period']  = isset( $data['limit_period'] ) && in_array( $data['limit_period'], $allowed_periods, true ) ? $data['limit_period'] : 'none'; // Asegura que el periodo sea válido.
        $clean['limit_reset']   = isset( $data['limit_reset'] ) ? sanitize_text_field( $data['limit_reset'] ) : null; // Sanitiza la fecha de reseteo.
        $clean['custom_message'] = isset( $data['custom_message'] ) ? sanitize_text_field( wp_unslash( $data['custom_message'] ) ) : null; // Limpia el mensaje personalizado.
        $clean['created_at']    = isset( $data['created_at'] ) ? sanitize_text_field( $data['created_at'] ) : current_time( 'mysql' ); // Fecha de creación.
        $clean['updated_at']    = isset( $data['updated_at'] ) ? sanitize_text_field( $data['updated_at'] ) : current_time( 'mysql' ); // Fecha de actualización.
        return $clean; // Devuelve el arreglo limpio listo para insertarse.
    }

    /**
     * Sanitiza los datos destinados a la tabla de excepciones antes de persistirlos.
     *
     * @param array<string, mixed> $data Datos originales a validar.
     *
     * @return array<string, mixed>
     */
    protected static function sanitize_override_data( $data ) { // Método auxiliar que normaliza los datos de la tabla de excepciones.
        $clean = self::sanitize_data( $data ); // Reutiliza la lógica de sanitización general para los campos compartidos.
        $clean['post_id'] = isset( $data['post_id'] ) ? max( 0, (int) $data['post_id'] ) : 0; // Normaliza el identificador del post asociado a la excepción.
        return $clean; // Devuelve el arreglo limpio y listo para insertarse en la tabla de excepciones.
    }

    /**
     * Comprueba si existen columnas desfasadas y las actualiza para mantener el esquema consistente.
     *
     * @return void
     */
    protected static function maybe_upgrade_columns() { // Método protegido que garantiza la compatibilidad del esquema.
        global $wpdb; // Accede al objeto global de base de datos.
        self::resolve_table_names(); // Asegura que los nombres de las tablas estén disponibles sin disparar recursivamente la inicialización completa.
        $column = $wpdb->get_var( // Consulta para verificar la existencia de la columna legacy last_reset.
            $wpdb->prepare( // Prepara la consulta con parámetros seguros.
                "SHOW COLUMNS FROM " . self::$table_name . " LIKE %s", // Consulta SQL que busca la columna legacy.
                'last_reset' // Nombre de la columna antigua a comprobar.
            )
        );
        if ( $column ) { // Comprueba si la columna antigua existe.
            $wpdb->query( "ALTER TABLE " . self::$table_name . " CHANGE `last_reset` `limit_reset` DATETIME NULL" ); // Renombra la columna utilizando SQL directo.
        }
        $hide_column = $wpdb->get_var( // Consulta para verificar si la columna hide_exhausted ya existe en la tabla principal.
            $wpdb->prepare( // Prepara la consulta que buscará la columna moderna.
                "SHOW COLUMNS FROM " . self::$table_name . " LIKE %s", // Consulta SQL parametrizada que busca la columna actual.
                'hide_exhausted' // Nombre de la columna cuyo estado se verificará.
            )
        );
        if ( ! $hide_column ) { // Comprueba si la columna aún no existe para añadirla de forma segura.
            $wpdb->query( "ALTER TABLE " . self::$table_name . " ADD `hide_exhausted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `option_value`" ); // Añade la columna garantizando compatibilidad con MySQL 5.7.
        }
    }

    /**
     * Ejecuta los scripts SQL incrementales para mantener el esquema actualizado.
     *
     * @return void
     */
    protected static function run_migrations() { // Método protegido que recorre los scripts incrementales disponibles.
        global $wpdb; // Accede al objeto global de base de datos para ejecutar sentencias directas.
        self::resolve_table_names(); // Asegura que los nombres de las tablas estén actualizados respetando el prefijo activo.
        $directory = CF7_OPTION_LIMITER_DIR . 'sql/'; // Determina la ruta que contiene los archivos SQL incrementales.
        $files = glob( $directory . 'update_*.sql' ); // Localiza todos los archivos de actualización siguiendo la convención establecida.
        if ( empty( $files ) ) { // Comprueba si no se encontraron archivos.
            return; // Finaliza sin ejecutar consultas adicionales cuando no hay migraciones pendientes.
        }
        sort( $files ); // Ordena la lista para aplicar los scripts en el orden correcto.
        foreach ( $files as $file ) { // Recorre cada archivo localizado.
            $sql = file_get_contents( $file ); // Lee el contenido completo del script actual.
            if ( ! $sql ) { // Comprueba si el archivo está vacío o no se pudo leer.
                continue; // Omite el archivo cuando no hay contenido ejecutable.
            }
            $prepared_sql = str_replace( '{prefix}', self::get_table_prefix(), $sql ); // Sustituye el marcador del prefijo por el valor real del sitio.
            $statements = array_filter( array_map( 'trim', explode( ';', $prepared_sql ) ) ); // Divide el script en sentencias individuales eliminando espacios en blanco.
            foreach ( $statements as $statement ) { // Recorre cada sentencia lista para ejecutarse.
                if ( '' === $statement ) { // Comprueba si la sentencia quedó vacía tras el recorte.
                    continue; // Evita ejecutar consultas vacías.
                }
                $wpdb->query( $statement ); // Ejecuta la sentencia SQL directamente sobre la base de datos.
            }
        }
    }
}
