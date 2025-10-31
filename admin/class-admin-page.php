<?php
// Impide accesos directos al archivo.
if ( ! defined( 'ABSPATH' ) ) { // Verifica si WordPress está cargado.
    exit; // Finaliza si se accede directamente.
}

// Clase que controla la página de administración del plugin.
class CF7_OptionLimiter_Admin { // Declara la clase encargada del área de administración.

    // Constante interna con el slug de la página para reutilizarlo en varios métodos.
    const MENU_SLUG = 'cf7-option-limiter'; // Define el identificador del submenú dentro de Contact Form 7.

    /**
    * Configura los puntos de entrada del panel de administración del plugin.
    *
    * Explicación:
    * - Resume la tarea principal: Configura los puntos de entrada del panel de administración del plugin.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return void
    */
    public static function init() { // Método principal de arranque para el área administrativa.
        add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) ); // Agrega la página al menú de Contact Form 7.
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) ); // Encola estilos y scripts cuando se visita la página.
        add_action( 'admin_post_cf7_option_limiter_save', array( __CLASS__, 'handle_save' ) ); // Maneja el guardado de reglas mediante admin-post.
        add_action( 'admin_post_cf7_option_limiter_delete', array( __CLASS__, 'handle_delete' ) ); // Gestiona las peticiones de borrado de reglas.
        add_action( 'admin_post_cf7_option_limiter_release', array( __CLASS__, 'handle_release' ) ); // Atiende la liberación manual de usos desde la tabla principal.
        add_action( 'admin_post_cf7_option_limiter_toggle_debug', array( __CLASS__, 'handle_toggle_debug' ) ); // Gestiona el cambio de estado del modo depuración desde el panel principal.
        add_action( 'wp_ajax_cf7_option_limiter_scan_form', array( __CLASS__, 'ajax_scan_form' ) ); // Provee datos de formularios mediante AJAX.
        add_action( 'wp_ajax_cf7_option_limiter_save_rule', array( __CLASS__, 'ajax_save_rule' ) ); // Expone un endpoint AJAX autenticado para guardar reglas sin recargar la página.
    }

    /**
    * Registra la pantalla del plugin como submenú del panel de Contact Form 7.
    *
    * Explicación:
    * - Resume la tarea principal: Registra la pantalla del plugin como submenú del panel de Contact Form 7.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return void
    */
    public static function register_menu() { // Método que agrega la página al menú de Contact Form 7.
        add_submenu_page( // Llama a la función de WordPress que crea un submenú.
            'wpcf7', // Slug del menú padre perteneciente a Contact Form 7.
            __( 'Option Limiter', 'cf7-option-limiter' ), // Título de la página que se mostrará en la parte superior.
            __( 'Option Limiter', 'cf7-option-limiter' ), // Texto del enlace del menú lateral.
            'manage_options', // Capacidad requerida para acceder a la página.
            self::MENU_SLUG, // Slug único para la página de opciones del plugin.
            array( __CLASS__, 'render_page' ) // Callback que renderiza el contenido de la página.
        );
    }

    /**
    * Encola los recursos necesarios únicamente en la pantalla del plugin.
    *
    * Explicación:
    * - Resume la tarea principal: Encola los recursos necesarios únicamente en la pantalla del plugin.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param string $hook Identificador de la pantalla actual.
    *
    * @return void
    */
    public static function enqueue_assets( $hook ) {
        // Verifica que estamos en la página correspondiente al plugin antes de cargar los recursos.
        if ( strpos( $hook, self::MENU_SLUG ) === false ) {
            return;
        }

        // Encola los estilos y scripts previamente registrados.
        wp_enqueue_style( 'cf7-option-limiter-admin' );
        wp_enqueue_script( 'cf7-option-limiter-admin' );

        // Inyecta datos de configuración y textos traducibles al script de administración reutilizando el método centralizado.
        wp_localize_script(
            'cf7-option-limiter-admin', // Identificador del script JS.
            'CF7OptionLimiterAdmin',    // Objeto JS accesible globalmente.
            self::get_localization_data() // Arreglo completo de configuración compartida entre pantallas.
        );
    }


    /**
    * Renderiza la interfaz principal de configuración de límites.
    *
    * Explicación:
    * - Resume la tarea principal: Renderiza la interfaz principal de configuración de límites.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return void
    */
    public static function render_page() { // Método encargado de generar el HTML de la página de ajustes.
        if ( ! current_user_can( 'manage_options' ) ) { // Comprueba permisos antes de mostrar contenido.
            wp_die( esc_html__( 'No tienes permisos suficientes para acceder a esta página.', 'cf7-option-limiter' ) ); // Muestra un mensaje de error si el usuario no tiene permisos.
        }
        $forms = self::get_forms(); // Obtiene la lista de formularios de Contact Form 7 para resolver títulos en la tabla.
        $selected_form = isset( $_GET['form_filter'] ) ? (int) $_GET['form_filter'] : 0; // Recupera el formulario seleccionado en el filtro o cero si no se aplicó ninguno.
        $current_page = isset( $_GET['ol_page'] ) ? max( 1, (int) $_GET['ol_page'] ) : 1; // Determina la página actual para la paginación garantizando que sea al menos 1.
        $per_page = 10; // Define la cantidad de elementos por página solicitada en los requisitos.
        $offset = ( $current_page - 1 ) * $per_page; // Calcula el desplazamiento en función de la página actual.
        $rules = CF7_OptionLimiter_DB::get_limits_filtered( $selected_form, $per_page, $offset ); // Recupera únicamente las reglas que corresponden al filtro y a la página en curso.
        $total_rules = CF7_OptionLimiter_DB::count_limits( $selected_form ); // Calcula el número total de reglas disponibles para construir la paginación.
        $total_pages = $per_page > 0 ? max( 1, (int) ceil( $total_rules / $per_page ) ) : 1; // Determina cuántas páginas son necesarias asegurando que exista al menos una.
        $notice = null; // Inicializa la variable que almacenará mensajes contextuales para el usuario.
        $redirect_to = self::build_redirect_with_context( $_GET ); // Calcula la URL de retorno preservando filtros y paginación.
        $debug_enabled = CF7_OptionLimiter_Logger::is_debug_enabled(); // Recupera el estado actual del modo de depuración persistente.
        $log_lines = CF7_OptionLimiter_Logger::get_recent_lines( 120 ); // Obtiene las últimas líneas del log para mostrarlas en la interfaz.
        $log_error = CF7_OptionLimiter_Logger::get_last_error(); // Recupera un posible mensaje de error al preparar el archivo de log.
        $log_file_path = CF7_OptionLimiter_Logger::get_log_file_path(); // Recupera la ruta absoluta del archivo de log activo cuando está disponible.
        $log_hint = $debug_enabled ? __( 'El modo de depuración está activo, verás consultas y avisos detallados.', 'cf7-option-limiter' ) : __( 'El modo de depuración está desactivado; se muestra el log mínimo con los eventos esenciales.', 'cf7-option-limiter' ); // Prepara un mensaje contextual según el estado del modo depuración.
        if ( isset( $_GET['ol_notice'] ) ) { // Comprueba si se envió un parámetro de notificación personalizada.
            $notice_code = sanitize_key( wp_unslash( $_GET['ol_notice'] ) ); // Normaliza el código recibido para evitar valores inválidos.
            if ( 'created' === $notice_code ) { // Evalúa si se trata de la creación de una regla.
                $notice = array( 'class' => 'updated notice-success', 'message' => __( 'Límite guardado correctamente.', 'cf7-option-limiter' ) ); // Configura el mensaje de éxito correspondiente.
            } elseif ( 'updated' === $notice_code ) { // Comprueba si se informó de una actualización.
                $notice = array( 'class' => 'updated notice-success', 'message' => __( 'Límite actualizado correctamente.', 'cf7-option-limiter' ) ); // Mensaje cuando se modifica una regla existente.
            } elseif ( 'deleted' === $notice_code ) { // Determina si se eliminó una regla.
                $notice = array( 'class' => 'updated notice-success', 'message' => __( 'Límite eliminado correctamente.', 'cf7-option-limiter' ) ); // Mensaje de confirmación de borrado.
            } elseif ( 'released' === $notice_code ) { // Comprueba si se solicitó mostrar la confirmación de liberación manual.
                $notice = array( 'class' => 'updated notice-success', 'message' => __( 'Se liberó un uso y el formulario vuelve a aceptar una selección adicional.', 'cf7-option-limiter' ) ); // Mensaje que informa del decremento exitoso del contador.
            } elseif ( 'release_failed' === $notice_code ) { // Gestiona el escenario en que no se pudo liberar el uso solicitado.
                $notice = array( 'class' => 'notice-error', 'message' => __( 'No se pudo liberar el uso indicado. Revisa que la regla siga existiendo e inténtalo de nuevo.', 'cf7-option-limiter' ) ); // Mensaje de error asociado al ajuste manual del contador.
            } elseif ( 'debug_on' === $notice_code ) { // Comprueba si se acaba de activar el modo de depuración.
                $notice = array( 'class' => 'updated notice-success', 'message' => __( 'El modo de depuración detallado está activo. Los eventos se registrarán con información extendida.', 'cf7-option-limiter' ) ); // Mensaje que confirma la activación del modo depuración.
            } elseif ( 'debug_off' === $notice_code ) { // Comprueba si se acaba de desactivar el modo de depuración.
                $notice = array( 'class' => 'updated notice-success', 'message' => __( 'El modo de depuración detallado se ha desactivado. Se conservará únicamente el log mínimo.', 'cf7-option-limiter' ) ); // Mensaje que confirma la desactivación del modo depuración.
            } elseif ( 'error' === $notice_code ) { // Gestiona notificaciones de error.
                $error_code = isset( $_GET['ol_error'] ) ? sanitize_key( wp_unslash( $_GET['ol_error'] ) ) : ''; // Recupera el código de error concreto si está disponible.
                $error_message = __( 'No se pudo completar la operación solicitada.', 'cf7-option-limiter' ); // Define un mensaje genérico por defecto.
                if ( 'conflict' === $error_code ) { // Error cuando ya existe una regla con la misma combinación.
                    $error_message = __( 'Ya existe un límite registrado para ese formulario, campo y opción.', 'cf7-option-limiter' ); // Mensaje específico de conflicto.
                } elseif ( 'missing' === $error_code ) { // Error cuando no se encuentra la regla a editar.
                    $error_message = __( 'El límite seleccionado para editar ya no existe.', 'cf7-option-limiter' ); // Mensaje cuando la regla desapareció antes de guardar cambios.
                } elseif ( 'db' === $error_code ) { // Error relacionado con la base de datos.
                    $error_message = __( 'La base de datos rechazó la operación. Revisa los registros para más detalles.', 'cf7-option-limiter' ); // Mensaje orientado a problemas de persistencia.
                }
                $notice = array( 'class' => 'notice-error', 'message' => $error_message ); // Configura la notificación de error con el mensaje correspondiente.
            }
        } elseif ( isset( $_GET['updated'] ) ) { // Mantiene compatibilidad con el parámetro histórico utilizado anteriormente.
            $notice = array( 'class' => 'updated notice-success', 'message' => __( 'Límite guardado correctamente.', 'cf7-option-limiter' ) ); // Mensaje heredado para creación o actualización.
        } elseif ( isset( $_GET['deleted'] ) ) { // Mantiene compatibilidad con la notificación clásica de borrado.
            $notice = array( 'class' => 'updated notice-success', 'message' => __( 'Límite eliminado correctamente.', 'cf7-option-limiter' ) ); // Mensaje heredado para eliminaciones.
        }
        ?>
        <div class="wrap cf7-option-limiter-admin"> <!-- Contenedor principal de la página de administración que ahora actúa como panel de consulta. -->
            <h1><?php echo esc_html__( 'Limitador de opciones para Contact Form 7', 'cf7-option-limiter' ); ?></h1> <!-- Título general de la página para mantener la identidad del plugin. -->
            <div class="cf7-option-limiter-heading-actions"> <!-- Contenedor de acciones rápidas situado junto al título principal. -->
                <a class="cf7-option-limiter-info-link" href="<?php echo esc_url( CF7_OptionLimiter_Docs::get_page_url() ); ?>"> <!-- Enlace que dirige a la documentación del plugin. -->
                    <span class="dashicons dashicons-info"></span> <!-- Icono informativo que identifica visualmente el acceso a la ayuda. -->
                    <span class="screen-reader-text"><?php echo esc_html__( 'Abrir documentos y preguntas frecuentes del limitador de opciones', 'cf7-option-limiter' ); ?></span> <!-- Texto descriptivo exclusivo para lectores de pantalla. -->
                </a>
            </div>
            <?php if ( $notice ) : ?>
                <div class="notice <?php echo esc_attr( $notice['class'] ); ?>"> <!-- Contenedor del aviso contextual reutilizando el estilo estándar. -->
                    <p><?php echo esc_html( $notice['message'] ); ?></p> <!-- Mensaje del aviso contextual. -->
                </div>
            <?php endif; ?>
            <div class="cf7-option-limiter-panel cf7-option-limiter-panel-table"> <!-- Contenedor único centrado en el listado de reglas globales. -->
                <p class="description"><?php echo esc_html__( 'Gestiona la creación y edición de límites desde el editor del formulario. Esta tabla sólo muestra el estado actual de cada regla.', 'cf7-option-limiter' ); ?></p> <!-- Mensaje introductorio que orienta al usuario hacia el flujo en Contact Form 7. -->
                <h2><?php echo esc_html__( 'Reglas configuradas', 'cf7-option-limiter' ); ?></h2> <!-- Encabezado de la tabla de reglas. -->
                <form method="get" class="cf7-option-limiter-filter"> <!-- Formulario que permite filtrar el listado por formulario. -->
                    <input type="hidden" name="page" value="<?php echo esc_attr( self::MENU_SLUG ); ?>" /> <!-- Mantiene el slug de la página en la petición para conservar el contexto. -->
                    <label for="cf7-ol-filter-form"><?php echo esc_html__( 'Filtrar por formulario', 'cf7-option-limiter' ); ?></label> <!-- Etiqueta descriptiva del selector de filtro. -->
                    <select id="cf7-ol-filter-form" name="form_filter"> <!-- Selector que lista los formularios disponibles para filtrar. -->
                        <option value="0"<?php selected( 0, $selected_form ); ?>><?php echo esc_html__( 'Todos los formularios', 'cf7-option-limiter' ); ?></option> <!-- Opción que muestra todas las reglas sin filtrar. -->
                        <?php foreach ( $forms as $form ) : ?>
                            <option value="<?php echo esc_attr( $form['id'] ); ?>"<?php selected( (int) $form['id'], $selected_form ); ?>><?php echo esc_html( $form['title'] ); ?></option> <!-- Opción individual para cada formulario detectado. -->
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="button button-secondary"><?php echo esc_html__( 'Aplicar filtro', 'cf7-option-limiter' ); ?></button> <!-- Botón que envía el formulario para aplicar el filtro seleccionado. -->
                    <?php if ( $selected_form > 0 ) : ?>
                        <a class="button" href="<?php echo esc_url( add_query_arg( array( 'page' => self::MENU_SLUG ), admin_url( 'admin.php' ) ) ); ?>"><?php echo esc_html__( 'Mostrar todos', 'cf7-option-limiter' ); ?></a> <!-- Enlace que restablece el filtro para mostrar todas las reglas. -->
                    <?php endif; ?>
                </form>
                <table class="wp-list-table widefat fixed striped cf7-option-limiter-table"> <!-- Tabla con estilo WordPress para listar reglas. -->
                    <thead> <!-- Cabecera de la tabla. -->
                        <tr> <!-- Fila de cabecera. -->
                            <th><?php echo esc_html__( 'Formulario', 'cf7-option-limiter' ); ?></th> <!-- Encabezado de columna para el formulario. -->
                            <th><?php echo esc_html__( 'Campo', 'cf7-option-limiter' ); ?></th> <!-- Encabezado de columna para el campo. -->
                            <th><?php echo esc_html__( 'Opción', 'cf7-option-limiter' ); ?></th> <!-- Encabezado de columna para la opción. -->
                            <th><?php echo esc_html__( 'Ocultar agotadas', 'cf7-option-limiter' ); ?></th> <!-- Encabezado de columna para indicar si la opción se oculta al agotarse. -->
                            <th><?php echo esc_html__( 'Máximo', 'cf7-option-limiter' ); ?></th> <!-- Encabezado para el máximo permitido. -->
                            <th><?php echo esc_html__( 'Usos actuales', 'cf7-option-limiter' ); ?></th> <!-- Encabezado para el contador actual. -->
                            <th><?php echo esc_html__( 'Periodo', 'cf7-option-limiter' ); ?></th> <!-- Encabezado para el periodo configurado. -->
                            <th><?php echo esc_html__( 'Mensaje', 'cf7-option-limiter' ); ?></th> <!-- Encabezado para el mensaje personalizado. -->
                            <th><?php echo esc_html__( 'Acciones', 'cf7-option-limiter' ); ?></th> <!-- Encabezado para acciones disponibles. -->
                        </tr>
                    </thead>
                    <tbody> <!-- Cuerpo de la tabla. -->
                        <?php if ( empty( $rules ) ) : ?>
                            <tr class="cf7-ol-empty-row"> <!-- Fila única que indica ausencia de datos y se identifica para poder eliminarla dinámicamente cuando se cree la primera regla. -->
                                <td colspan="9"><?php echo esc_html__( 'Aún no se han configurado límites para los criterios seleccionados.', 'cf7-option-limiter' ); ?></td> <!-- Mensaje informativo cuando no hay resultados en el filtro aplicado. -->
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $rules as $rule ) : ?>
                                <tr data-rule-id="<?php echo esc_attr( $rule['id'] ); ?>"> <!-- Fila individual de la tabla identificada por el ID de la regla para permitir actualizaciones dinámicas desde JavaScript. -->
                                    <td><?php echo esc_html( self::get_form_title( $rule['form_id'], $forms ) ); ?></td> <!-- Muestra el título del formulario según la lista disponible. -->
                                    <td><?php echo esc_html( $rule['field_name'] ); ?></td> <!-- Muestra el nombre del campo. -->
                                    <td><?php echo esc_html( $rule['option_value'] ); ?></td> <!-- Muestra el valor de la opción. -->
                                    <td><?php echo esc_html( $rule['hide_exhausted'] ? __( 'Sí', 'cf7-option-limiter' ) : __( 'No', 'cf7-option-limiter' ) ); ?></td> <!-- Indica si la opción se ocultará al agotarse. -->
                                    <td><?php echo esc_html( $rule['max_count'] ); ?></td> <!-- Indica el máximo permitido. -->
                                    <td><?php echo esc_html( $rule['current_count'] ); ?></td> <!-- Indica el contador actual. -->
                                    <td><?php echo esc_html( self::get_period_label( $rule['limit_period'] ) ); ?></td> <!-- Traduce el periodo configurado. -->
                                    <td><?php echo esc_html( $rule['custom_message'] ); ?></td> <!-- Muestra el mensaje personalizado si existe. -->
                                    <td> <!-- Columna de acciones. -->
                                        <?php
                                        $edit_url = add_query_arg( // Calcula la URL del editor de Contact Form 7 con la pestaña del limitador activa.
                                            array(
                                                'page'        => 'wpcf7', // Slug del menú principal de Contact Form 7.
                                                'post'        => (int) $rule['form_id'], // Identificador del formulario asociado a la regla.
                                                'active-tab'  => 'cf7-option-limiter', // Solicita mostrar la pestaña del limitador inmediatamente.
                                                'cf7_ol_rule' => (int) $rule['id'], // Identificador de la regla que se desea editar dentro del editor.
                                            ),
                                            admin_url( 'admin.php' ) // Base del área de administración de WordPress.
                                        );
                                        $edit_label = esc_attr__( 'Editar límite en el formulario de Contact Form 7', 'cf7-option-limiter' ); // Traduce el texto descriptivo del icono de edición.
                                        ?>
                                        <a class="button cf7-option-limiter-action-button" href="<?php echo esc_url( $edit_url ); ?>" title="<?php echo $edit_label; ?>" aria-label="<?php echo $edit_label; ?>"> <!-- Enlace icónico que abre la regla en el editor original. -->
                                            <span class="dashicons dashicons-edit"></span> <!-- Icono visual de edición proporcionado por Dashicons. -->
                                            <span class="screen-reader-text"><?php echo esc_html( $edit_label ); ?></span> <!-- Texto descriptivo exclusivo para lector de pantalla. -->
                                        </a>
                                        <?php echo self::render_release_form( $rule, $redirect_to ); // Inserta el formulario encapsulado que libera manualmente un uso manteniendo el mismo formato en todas las filas. ?>
                                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="cf7-option-limiter-inline-form"> <!-- Formulario para eliminar la regla. -->
                                            <?php wp_nonce_field( 'cf7_option_limiter_delete', 'cf7_option_limiter_delete_nonce' ); ?> <!-- Nonce de seguridad para el borrado. -->
                                            <input type="hidden" name="action" value="cf7_option_limiter_delete" /> <!-- Acción admin-post correspondiente. -->
                                            <input type="hidden" name="rule_id" value="<?php echo esc_attr( $rule['id'] ); ?>" /> <!-- Identificador de la regla a eliminar. -->
                                            <input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>" /> <!-- Redirección de retorno tras eliminar la regla conservando filtros. -->
                                            <?php $delete_label = esc_attr__( 'Eliminar definitivamente esta regla', 'cf7-option-limiter' ); // Traduce la descripción del botón de borrado para utilizarla en atributos accesibles. ?>
                                            <button type="submit" class="button button-secondary cf7-option-limiter-action-button" title="<?php echo $delete_label; ?>" aria-label="<?php echo $delete_label; ?>" onclick="return confirm('<?php echo esc_js( __( '¿Seguro que deseas eliminar esta regla?', 'cf7-option-limiter' ) ); ?>');"> <!-- Botón que inicia la eliminación tras confirmación. -->
                                                <span class="dashicons dashicons-trash"></span> <!-- Icono de papelera que identifica la acción destructiva. -->
                                                <span class="screen-reader-text"><?php echo esc_html( $delete_label ); ?></span> <!-- Texto accesible que describe la acción al lector de pantalla. -->
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if ( $total_pages > 1 ) : ?>
                    <div class="tablenav cf7-option-limiter-pagination"> <!-- Contenedor que alberga la navegación entre páginas. -->
                        <div class="tablenav-pages"> <!-- Sección estándar de paginación utilizada en el administrador. -->
                            <span class="displaying-num"><?php echo esc_html( sprintf( __( '%d reglas en total', 'cf7-option-limiter' ), $total_rules ) ); ?></span> <!-- Texto que informa del número total de reglas encontradas. -->
                            <?php
                            $base_args = array( 'page' => self::MENU_SLUG ); // Argumentos base para construir las URLs de paginación.
                            if ( $selected_form > 0 ) { // Comprueba si el filtro por formulario está activo.
                                $base_args['form_filter'] = $selected_form; // Mantiene el filtro en los enlaces de paginación.
                            }
                            $base_url = add_query_arg( $base_args, admin_url( 'admin.php' ) ); // Construye la URL base reutilizada por los enlaces.
                            if ( $current_page > 1 ) { // Comprueba si existe una página anterior disponible.
                                $prev_url = add_query_arg( 'ol_page', $current_page - 1, $base_url ); // Calcula la URL de la página anterior.
                                echo '<a class="prev-page" href="' . esc_url( $prev_url ) . '">&lsaquo;</a>'; // Imprime el enlace hacia la página anterior usando el símbolo estándar.
                            }
                            echo '<span class="paging-input">' . esc_html( sprintf( __( 'Página %1$d de %2$d', 'cf7-option-limiter' ), $current_page, $total_pages ) ) . '</span>'; // Muestra el indicador de página actual.
                            if ( $current_page < $total_pages ) { // Comprueba si existe una página posterior disponible.
                                $next_url = add_query_arg( 'ol_page', $current_page + 1, $base_url ); // Calcula la URL de la página siguiente.
                                echo '<a class="next-page" href="' . esc_url( $next_url ) . '">&rsaquo;</a>'; // Imprime el enlace hacia la página siguiente usando el símbolo estándar.
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="cf7-option-limiter-panel cf7-option-limiter-debug"> <!-- Contenedor dedicado al control del modo depuración y al visor del log en texto plano. -->
                <h2><?php echo esc_html__( 'Modo de depuración y registro', 'cf7-option-limiter' ); ?></h2> <!-- Título que agrupa la preferencia y el visor del log. -->
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="cf7-option-limiter-debug-form"> <!-- Formulario que envía la preferencia del modo depuración. -->
                    <?php wp_nonce_field( 'cf7_option_limiter_toggle_debug', 'cf7_option_limiter_debug_nonce' ); // Genera el nonce que protege el cambio de modo depuración. ?>
                    <input type="hidden" name="action" value="cf7_option_limiter_toggle_debug" /> <!-- Acción admin-post que procesará la preferencia. -->
                    <label for="cf7-ol-debug-mode"> <!-- Etiqueta asociada al checkbox del modo depuración. -->
                        <input type="checkbox" id="cf7-ol-debug-mode" name="cf7_ol_debug_mode" value="1" <?php checked( $debug_enabled ); ?> /> <!-- Casilla que activa o desactiva el modo depuración. -->
                        <?php echo esc_html__( 'Activar modo de depuración detallado', 'cf7-option-limiter' ); ?> <!-- Texto descriptivo del control. -->
                    </label>
                    <p class="description"> <!-- Texto descriptivo adicional del modo depuración. -->
                        <?php echo esc_html__( 'Cuando está activo se registran todas las consultas, advertencias y errores. Al desactivarlo se conserva un log mínimo con las operaciones principales.', 'cf7-option-limiter' ); ?> <!-- Mensaje que explica el comportamiento del modo depuración. -->
                    </p>
                    <button type="submit" class="button button-secondary"> <!-- Botón que guarda la preferencia seleccionada. -->
                        <?php echo esc_html__( 'Guardar preferencia de depuración', 'cf7-option-limiter' ); ?> <!-- Texto del botón de guardado. -->
                    </button>
                </form>
                <div class="cf7-option-limiter-log-viewer"> <!-- Contenedor que muestra el log reciente en un formato agradable. -->
                    <?php if ( ! empty( $log_error ) ) : ?>
                        <div class="notice notice-error inline"> <!-- Aviso en línea que informa de problemas al escribir el log. -->
                            <p><?php echo esc_html( $log_error ); ?></p> <!-- Mensaje descriptivo del error detectado al preparar el archivo de log. -->
                        </div>
                    <?php elseif ( empty( $log_lines ) ) : ?>
                        <p class="description"><?php echo esc_html__( 'Todavía no hay eventos registrados en el log.', 'cf7-option-limiter' ); ?></p> <!-- Mensaje que indica la ausencia de entradas en el log. -->
                    <?php else : ?>
                        <textarea readonly rows="12" class="cf7-option-limiter-log-output"><?php echo esc_textarea( implode( "\n", $log_lines ) ); ?></textarea> <!-- Área de texto de sólo lectura que muestra las últimas entradas del log. -->
                    <?php endif; ?>
                    <p class="description"><?php echo esc_html( $log_hint ); ?></p> <!-- Mensaje contextual que aclara el nivel de detalle mostrado. -->
                    <?php if ( $log_file_path ) : ?>
                        <p class="description"><?php printf( esc_html__( 'El archivo completo se encuentra en: %s', 'cf7-option-limiter' ), esc_html( $log_file_path ) ); ?></p> <!-- Nota que indica la ubicación real del archivo de log activo. -->
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
    * Construye la URL de retorno para los formularios internos preservando filtros y paginación.
    *
    * Explicación:
    * - Resume la tarea principal: Construye la URL de retorno para los formularios internos preservando filtros y paginación.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param array<string, mixed> $request Conjunto de parámetros procedentes de la petición actual.
    *
    * @return string
    */
    protected static function build_redirect_with_context( $request ) { // Método auxiliar que calcula la URL de retorno contextualizada.
        $base_url = admin_url( 'admin.php' ); // Obtiene la base del área de administración sobre la que se añadirán los parámetros.
        $redirect_args = array( 'page' => self::MENU_SLUG ); // Inicializa los argumentos obligatorios incluyendo el slug de la página del plugin.
        if ( isset( $request['form_filter'] ) ) { // Comprueba si la petición incluye un filtro por formulario.
            $raw_filter = wp_unslash( $request['form_filter'] ); // Extrae el valor permitiendo eliminar barras añadidas por WordPress.
            $form_filter = (int) $raw_filter; // Convierte el filtro en entero para garantizar un valor seguro.
            if ( $form_filter > 0 ) { // Verifica que el filtro represente un formulario específico distinto del estado por defecto.
                $redirect_args['form_filter'] = $form_filter; // Añade el filtro al conjunto de argumentos a preservar.
            }
        }
        if ( isset( $request['ol_page'] ) ) { // Comprueba si la petición especifica una página concreta dentro del listado.
            $raw_page = wp_unslash( $request['ol_page'] ); // Extrae el valor original de la petición.
            $page_number = (int) $raw_page; // Convierte el valor en entero para evitar inyecciones de parámetros no numéricos.
            if ( $page_number > 1 ) { // Evita añadir la página inicial para mantener URLs limpias cuando no es necesario.
                $redirect_args['ol_page'] = $page_number; // Preserva la página actual cuando el usuario navega por páginas posteriores.
            }
        }
        return add_query_arg( $redirect_args, $base_url ); // Devuelve la URL final combinando los argumentos con la base administrativa.
    }

    /**
    * Genera el formulario en línea encargado de liberar un uso manualmente.
    *
    * Explicación:
    * - Resume la tarea principal: Genera el formulario en línea encargado de liberar un uso manualmente.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param array<string, mixed> $rule Registro de la regla actual que se está renderizando en la tabla.
    * @param string               $redirect_to URL a la que debe regresar la interfaz tras procesar la liberación.
    *
    * @return string
    */
    protected static function render_release_form( $rule, $redirect_to ) { // Método auxiliar que encapsula la construcción del formulario de liberación.
        $rule_id = isset( $rule['id'] ) ? (int) $rule['id'] : 0; // Asegura que el identificador de la regla sea un entero válido antes de imprimirlo.
        $current_count = isset( $rule['current_count'] ) ? (int) $rule['current_count'] : 0; // Normaliza el contador actual para evaluar si queda algún uso liberable.
        $can_release = $current_count > 0; // Determina si existe al menos un uso disponible para habilitar el control.
        $action_url = esc_url( admin_url( 'admin-post.php' ) ); // Calcula la URL segura hacia admin-post donde se procesará la petición.
        $redirect_url = esc_url( $redirect_to ); // Normaliza la URL de retorno preservando el contexto del filtro.
        $nonce_field = wp_nonce_field( 'cf7_option_limiter_release', 'cf7_option_limiter_release_nonce', true, false ); // Genera el campo nonce como cadena para insertarlo manualmente dentro del formulario.
        if ( ! is_string( $nonce_field ) ) { // Comprueba que el nonce generado sea una cadena antes de añadirlo al marcado.
            $nonce_field = ''; // Evita avisos cuando el entorno de pruebas devuelve valores no textuales.
        }
        $release_label = esc_attr__( 'Liberar un uso reservado manualmente', 'cf7-option-limiter' ); // Traduce el texto descriptivo que se mostrará como tooltip y etiqueta accesible.
        $button_classes = 'button button-secondary cf7-option-limiter-action-button'; // Agrupa las clases que unifican el estilo con el resto de acciones de la tabla.
        if ( ! $can_release ) { // Comprueba si el contador está agotado para reflejarlo visualmente.
            $button_classes .= ' cf7-option-limiter-action-button--disabled'; // Añade una clase auxiliar que permitirá matizar el estilo en estado inactivo.
        }
        $disabled_attribute = $can_release ? '' : ' disabled="disabled" aria-disabled="true"'; // Calcula el atributo disabled sólo cuando no es posible liberar usos.
        $form  = '<form method="post" action="' . $action_url . '" class="cf7-option-limiter-inline-form"> <!-- Formulario que libera un uso restando una unidad al contador. -->'; // Abre el formulario en línea reutilizando la clase existente y conserva el comentario descriptivo original.
        $form .= $nonce_field; // Inserta el nonce generado para proteger la operación frente a peticiones manipuladas.
        $form .= '<input type="hidden" name="action" value="cf7_option_limiter_release" /> <!-- Acción admin-post que identifica el manejador responsable de restar el contador. -->'; // Añade la acción que WordPress utilizará para despachar la solicitud.
        $form .= '<input type="hidden" name="rule_id" value="' . esc_attr( (string) $rule_id ) . '" /> <!-- Identificador de la regla cuyo contador se ajustará. -->'; // Inserta el identificador de la regla asegurando su escape en el atributo.
        $form .= '<input type="hidden" name="redirect_to" value="' . $redirect_url . '" /> <!-- Redirección de retorno tras completar la liberación conservando filtros. -->'; // Mantiene la URL de retorno para preservar el contexto tras la operación.
        $form .= '<button type="submit" class="' . esc_attr( $button_classes ) . '" title="' . $release_label . '" aria-label="' . $release_label . '"' . $disabled_attribute . '>'; // Configura el botón icónico incluyendo los atributos accesibles y el estado habilitado.
        $form .= '<span class="dashicons dashicons-unlock"></span>'; // Inserta el icono de desbloqueo que representa la liberación de plazas.
        $form .= '<span class="screen-reader-text">' . esc_html( $release_label ) . '</span>'; // Añade texto sólo para lectores de pantalla con la misma descripción del tooltip.
        $form .= '</button>'; // Cierra el botón de envío del formulario en línea.
        $form .= '</form>'; // Cierra el formulario en línea para completar el bloque de acciones.
        return $form; // Devuelve el marcado construido para que pueda insertarse en la tabla.
    }

    /**
    * Maneja el guardado de una regla proveniente del formulario.
    *
    * Explicación:
    * - Resume la tarea principal: Maneja el guardado de una regla proveniente del formulario.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function handle_save() { // Método que procesa la creación o actualización de una regla.
        if ( ! current_user_can( 'manage_options' ) ) { // Comprueba permisos de usuario.
            wp_die( esc_html__( 'Permisos insuficientes.', 'cf7-option-limiter' ) ); // Muestra error si no tiene permisos.
        }
        check_admin_referer( 'cf7_option_limiter_save', 'cf7_option_limiter_nonce' ); // Verifica el nonce para validar la petición.
        $request_params = self::collect_save_params_from_request( $_POST ); // Normaliza todos los parámetros enviados en la petición.
        $redirect_to = $request_params['redirect_to']; // Recupera la URL de retorno ya sanitizada por el método auxiliar.
        $default_redirect = add_query_arg( array( 'page' => self::MENU_SLUG ), admin_url( 'admin.php' ) ); // Calcula la ruta por defecto de la página del plugin.
        if ( empty( $redirect_to ) || ( function_exists( 'wp_http_validate_url' ) && ! wp_http_validate_url( $redirect_to ) ) ) { // Verifica que la URL suministrada sea válida.
            $redirect_to = $default_redirect; // Reemplaza por la ruta por defecto en caso de no ser válida.
        }
        $result = self::process_save_operation( $request_params ); // Ejecuta la lógica centralizada que valida y persiste la regla.
        if ( is_wp_error( $result ) ) { // Comprueba si la operación produjo un error.
            $error_code = $result->get_error_code(); // Recupera el identificador de error para mostrar el aviso adecuado.
            $redirect_error = add_query_arg( array( 'ol_notice' => 'error', 'ol_error' => $error_code ), $redirect_to ); // Compone la URL con los parámetros que describen el error.
            wp_safe_redirect( $redirect_error ); // Redirige hacia la interfaz mostrando el error correspondiente.
            exit; // Finaliza la ejecución tras despachar la redirección.
        }
        $notice_code = isset( $result['notice_code'] ) ? $result['notice_code'] : 'created'; // Recupera el código de notificación a mostrar en la interfaz tradicional.
        wp_safe_redirect( add_query_arg( array( 'ol_notice' => $notice_code ), $redirect_to ) ); // Redirige hacia la interfaz con el aviso de éxito.
        exit; // Finaliza la ejecución tras lanzar la redirección.
    }

    /**
    * Gestiona el guardado mediante peticiones AJAX autenticadas.
    *
    * Explicación:
    * - Resume la tarea principal: Gestiona el guardado mediante peticiones AJAX autenticadas.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function ajax_save_rule() { // Método que procesa las peticiones AJAX para guardar reglas sin recargar la página.
        if ( ! current_user_can( 'manage_options' ) ) { // Comprueba que la persona usuaria tenga permisos de administración.
            wp_send_json_error( array( 'message' => __( 'No tienes permisos suficientes para guardar límites.', 'cf7-option-limiter' ) ), 403 ); // Devuelve un error JSON con código 403 indicando la falta de permisos.
        }
        check_ajax_referer( 'cf7_option_limiter_ajax', 'nonce' ); // Valida el nonce específico de las peticiones AJAX antes de procesar la solicitud.
        $request_params = self::collect_save_params_from_request( $_POST ); // Normaliza la estructura recibida desde la petición AJAX reutilizando el colector centralizado.
        $result = self::process_save_operation( $request_params ); // Ejecuta la lógica compartida que valida y persiste la regla.
        if ( is_wp_error( $result ) ) { // Comprueba si la operación concluyó con error.
            $error_payload = array( // Prepara la carga útil de error que recibirá el cliente.
                'code'    => $result->get_error_code(), // Incluye el código de error para permitir decisiones condicionales en la interfaz.
                'message' => $result->get_error_message(), // Incluye el mensaje traducido listo para mostrarse al usuario final.
            );
            wp_send_json_error( $error_payload, 400 ); // Devuelve el error con un código HTTP genérico 400 que indica fallo de validación.
        }
        wp_send_json_success( array( // Devuelve la respuesta satisfactoria con toda la información necesaria para refrescar la interfaz.
            'notice_code'     => $result['notice_code'], // Expone el código de notificación coherente con el flujo tradicional.
            'message'         => $result['message'], // Mensaje traducido que describe el resultado de la operación.
            'rule'            => $result['rule'], // Incluye la fila completa recién guardada para poder actualizar la tabla sin recargar.
            'removed_rule_id' => $result['removed_rule_id'], // Indica si se eliminó una regla previa debido a un cambio de combinación.
        ) );
    }

    /**
    * Extrae y normaliza los parámetros provenientes de una petición de guardado.
    *
    * Explicación:
    * - Resume la tarea principal: Extrae y normaliza los parámetros provenientes de una petición de guardado.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param array<string, mixed> $request Datos brutos recibidos desde el formulario o desde AJAX.
    *
    * @return array<string, mixed>
    */
    protected static function collect_save_params_from_request( $request ) { // Método auxiliar que centraliza la normalización de parámetros de guardado.
        $form_id = isset( $request['form_id'] ) ? (int) $request['form_id'] : 0; // Convierte el identificador del formulario a entero evitando valores inesperados.
        $field_name = isset( $request['field_name'] ) ? sanitize_text_field( wp_unslash( $request['field_name'] ) ) : ''; // Sanitiza el nombre del campo seleccionado.
        $option_value = isset( $request['option_value'] ) ? sanitize_text_field( wp_unslash( $request['option_value'] ) ) : ''; // Sanitiza el valor concreto de la opción limitada.
        $max_count = isset( $request['max_count'] ) ? max( 1, (int) $request['max_count'] ) : 1; // Garantiza que el límite máximo sea al menos uno.
        $limit_period = isset( $request['limit_period'] ) ? sanitize_text_field( wp_unslash( $request['limit_period'] ) ) : 'none'; // Recupera el periodo solicitado o none como valor por defecto.
        $custom_message = isset( $request['custom_message'] ) ? sanitize_text_field( wp_unslash( $request['custom_message'] ) ) : ''; // Sanitiza el mensaje personalizado que se mostrará cuando se alcance el límite.
        $hide_exhausted = isset( $request['hide_exhausted'] ) && ( '1' === (string) $request['hide_exhausted'] || 'yes' === (string) $request['hide_exhausted'] ) ? 1 : 0; // Normaliza la casilla de ocultar opciones agotadas aceptando distintos formatos comunes.
        $rule_id = isset( $request['rule_id'] ) ? (int) $request['rule_id'] : 0; // Convierte el identificador de la regla en entero.
        $redirect_to = isset( $request['redirect_to'] ) ? esc_url_raw( wp_unslash( $request['redirect_to'] ) ) : ''; // Sanitiza la URL de retorno utilizada en el flujo tradicional.
        $original_form_id = isset( $request['original_form_id'] ) ? (int) $request['original_form_id'] : 0; // Recupera el formulario original almacenado durante la edición.
        $original_field_name = isset( $request['original_field_name'] ) ? sanitize_text_field( wp_unslash( $request['original_field_name'] ) ) : ''; // Recupera el campo original para futuras comparaciones.
        $original_option_value = isset( $request['original_option_value'] ) ? sanitize_text_field( wp_unslash( $request['original_option_value'] ) ) : ''; // Recupera la opción original cuando se edita una regla existente.
        return array( // Devuelve un arreglo completo con todos los parámetros normalizados.
            'form_id'               => $form_id, // Identificador del formulario objetivo.
            'field_name'            => $field_name, // Campo seleccionado.
            'option_value'          => $option_value, // Valor concreto del campo que se limitará.
            'max_count'             => $max_count, // Límite máximo permitido.
            'limit_period'          => $limit_period, // Periodo configurado para reiniciar el contador.
            'custom_message'        => $custom_message, // Mensaje personalizado que se mostrará cuando el límite se alcance.
            'hide_exhausted'        => $hide_exhausted, // Indicador de ocultar opciones agotadas.
            'rule_id'               => $rule_id, // Identificador de la regla en edición.
            'redirect_to'           => $redirect_to, // URL de retorno opcional utilizada por el flujo tradicional.
            'original_form_id'      => $original_form_id, // Formulario original cuando se edita una regla existente.
            'original_field_name'   => $original_field_name, // Campo original cuando se edita una regla existente.
            'original_option_value' => $original_option_value, // Opción original cuando se edita una regla existente.
        );
    }

    /**
    * Ejecuta la lógica de validación y persistencia reutilizada por admin-post y AJAX.
    *
    * Explicación:
    * - Resume la tarea principal: Ejecuta la lógica de validación y persistencia reutilizada por admin-post y AJAX.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param array<string, mixed> $params Parámetros normalizados devueltos por collect_save_params_from_request().
    *
    * @return array<string, mixed>|WP_Error
    */
    protected static function process_save_operation( $params ) { // Método centralizado que valida y guarda la regla devolviendo un resultado homogéneo.
        $form_id = isset( $params['form_id'] ) ? (int) $params['form_id'] : 0; // Recupera el identificador del formulario objetivo garantizando un entero.
        $field_name = isset( $params['field_name'] ) ? (string) $params['field_name'] : ''; // Recupera el nombre del campo seleccionado.
        $option_value = isset( $params['option_value'] ) ? (string) $params['option_value'] : ''; // Recupera el valor específico limitado.
        if ( $form_id <= 0 || '' === $field_name || '' === $option_value ) { // Verifica que los tres datos imprescindibles estén presentes.
            $log_context = array( // Construye el contexto que documentará el rechazo en el log.
                'form_id'      => $form_id, // Identificador del formulario recibido.
                'field_name'   => $field_name, // Campo recibido.
                'option_value' => $option_value, // Opción recibida.
                'status'       => 'rejected', // Indicador que facilita filtrar eventos similares en el log.
                'message'      => __( 'Se rechazó la petición de guardado por datos incompletos.', 'cf7-option-limiter' ), // Mensaje descriptivo que explica el motivo del rechazo.
            );
            CF7_OptionLimiter_Logger::log( 'limit_admin_rejected', $log_context ); // Registra el rechazo para facilitar el diagnóstico en modo depuración.
            return new WP_Error( 'incomplete', __( 'Debes seleccionar un formulario, un campo y una opción antes de guardar el límite.', 'cf7-option-limiter' ) ); // Devuelve un error estándar que la interfaz puede interpretar.
        }
        $allowed_periods = array( 'none', 'hour', 'day', 'week' ); // Declara los periodos permitidos por la lógica del plugin.
        $limit_period = in_array( $params['limit_period'], $allowed_periods, true ) ? $params['limit_period'] : 'none'; // Normaliza el periodo recibido garantizando que sea válido.
        $max_count = isset( $params['max_count'] ) ? max( 1, (int) $params['max_count'] ) : 1; // Asegura que el máximo sea al menos uno.
        $custom_message = isset( $params['custom_message'] ) ? (string) $params['custom_message'] : ''; // Recupera el mensaje personalizado para almacenarlo.
        $hide_exhausted = ! empty( $params['hide_exhausted'] ) ? 1 : 0; // Normaliza el indicador de ocultar opciones agotadas en formato entero.
        $rule_id = isset( $params['rule_id'] ) ? (int) $params['rule_id'] : 0; // Determina si la operación corresponde a una edición.
        $is_edit = $rule_id > 0; // Calcula rápidamente si se trata de una actualización existente.
        $existing_by_id = $is_edit ? CF7_OptionLimiter_DB::get_limit_by_id( $rule_id ) : null; // Recupera la regla actual por su identificador cuando procede.
        if ( $is_edit && empty( $existing_by_id ) ) { // Comprueba que la regla siga existiendo antes de actualizarla.
            return new WP_Error( 'missing', __( 'La regla indicada no se encontró o ya fue eliminada.', 'cf7-option-limiter' ) ); // Devuelve un error indicando que la regla ya no está disponible.
        }
        $conflict = CF7_OptionLimiter_DB::get_limit( $form_id, $field_name, $option_value ); // Busca si existe una regla con la misma combinación.
        if ( $conflict && ( ! $existing_by_id || (int) $conflict['id'] !== (int) $existing_by_id['id'] ) ) { // Comprueba si la combinación corresponde a otra regla distinta.
            return new WP_Error( 'conflict', __( 'Ya existe un límite configurado para esta combinación de formulario, campo y opción.', 'cf7-option-limiter' ) ); // Devuelve un error describiendo el conflicto detectado.
        }
        $reference_row = $existing_by_id ? $existing_by_id : $conflict; // Determina una fila de referencia para mantener contadores y fechas previas.
        $current_count = $reference_row ? (int) $reference_row['current_count'] : 0; // Mantiene el contador previo cuando existe.
        $limit_reset = $reference_row && ! empty( $reference_row['limit_reset'] ) ? $reference_row['limit_reset'] : current_time( 'mysql' ); // Mantiene la fecha de reseteo previa cuando está disponible.
        $created_at = $reference_row && ! empty( $reference_row['created_at'] ) ? $reference_row['created_at'] : current_time( 'mysql' ); // Mantiene la fecha de creación previa cuando procede.
        $data = array( // Construye el conjunto de datos listo para persistirse en base de datos.
            'form_id'        => $form_id, // Identificador del formulario objetivo.
            'field_name'     => $field_name, // Nombre del campo objetivo.
            'option_value'   => $option_value, // Valor concreto que se limitará.
            'hide_exhausted' => $hide_exhausted, // Indicador de ocultar opciones agotadas representado como entero.
            'max_count'      => $max_count, // Límite máximo permitido.
            'current_count'  => $current_count, // Contador actual heredado en caso de existir una regla previa.
            'limit_period'   => $limit_period, // Periodo configurado para el reinicio del contador.
            'limit_reset'    => $limit_reset, // Fecha de reinicio del contador.
            'custom_message' => $custom_message, // Mensaje personalizado asociado a la regla.
            'created_at'     => $created_at, // Fecha de creación conservada o generada en el momento.
            'updated_at'     => current_time( 'mysql' ), // Fecha de actualización asignada al guardar.
        );
        $saved = CF7_OptionLimiter_DB::upsert_limit( $data ); // Persiste la regla mediante la operación centralizada de la capa de datos.
        if ( ! $saved ) { // Comprueba si la operación devolvió un error.
            return new WP_Error( 'db', __( 'Ocurrió un problema al guardar la regla en la base de datos.', 'cf7-option-limiter' ) ); // Devuelve un error genérico de base de datos.
        }
        $removed_rule_id = 0; // Inicializa el identificador de una posible regla eliminada tras cambiar la combinación.
        if ( $is_edit && $existing_by_id && ( (int) $existing_by_id['form_id'] !== $form_id || $existing_by_id['field_name'] !== $field_name || $existing_by_id['option_value'] !== $option_value ) ) { // Comprueba si la combinación cambió respecto a la original.
            CF7_OptionLimiter_DB::delete_limit( $rule_id ); // Elimina la regla previa para evitar duplicados cuando cambia la combinación principal.
            $removed_rule_id = (int) $rule_id; // Almacena el identificador eliminado para notificarlo a la interfaz.
        }
        $latest_rule = CF7_OptionLimiter_DB::get_limit( $form_id, $field_name, $option_value ); // Recupera la fila recién guardada para devolverla al cliente.
        $log_action = $is_edit ? 'updated' : 'saved'; // Determina el tipo de evento a registrar en el log administrativo.
        $log_message = $is_edit ? __( 'Regla actualizada desde el panel de administración.', 'cf7-option-limiter' ) : __( 'Regla guardada desde el panel de administración.', 'cf7-option-limiter' ); // Prepara el mensaje que se registrará en el log.
        $log_context = array( // Construye el contexto que documentará la operación en el log.
            'form_id'      => $form_id, // Identificador del formulario afectado.
            'field_name'   => $field_name, // Campo afectado.
            'option_value' => $option_value, // Opción afectada.
            'action'       => $log_action, // Tipo de operación realizada.
            'message'      => $log_message, // Mensaje descriptivo de la operación.
        );
        CF7_OptionLimiter_Logger::log( 'limit_admin_action', $log_context ); // Registra la operación respetando el nivel de depuración configurado.
        $notice_code = $is_edit ? 'updated' : 'created'; // Determina el código de notificación coherente con el flujo clásico.
        $message = $is_edit ? __( 'La regla se actualizó correctamente.', 'cf7-option-limiter' ) : __( 'La regla se guardó correctamente.', 'cf7-option-limiter' ); // Define el mensaje que recibirá la interfaz AJAX.
        if ( $latest_rule ) { // Comprueba que se haya recuperado la fila para enriquecerla antes de devolverla.
            $latest_rule['period_label'] = self::get_period_label( $latest_rule['limit_period'] ); // Añade la etiqueta legible del periodo para evitar cálculos en JavaScript.
            $latest_rule['hide_label'] = $latest_rule['hide_exhausted'] ? __( 'Sí', 'cf7-option-limiter' ) : __( 'No', 'cf7-option-limiter' ); // Añade la etiqueta accesible correspondiente al indicador de ocultar opciones agotadas.
        }
        return array( // Devuelve el resultado consolidado para el flujo llamante.
            'notice_code'     => $notice_code, // Código de notificación a mostrar.
            'message'         => $message, // Mensaje descriptivo para la interfaz AJAX.
            'rule'            => $latest_rule, // Fila persistida lista para actualizar la tabla.
            'removed_rule_id' => $removed_rule_id, // Identificador de la regla eliminada cuando cambió la combinación.
        );
    }

    /**
    * Gestiona la liberación manual de un uso cuando un administrador lo solicita.
    *
    * Explicación:
    * - Resume la tarea principal: Gestiona la liberación manual de un uso cuando un administrador lo solicita.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function handle_release() { // Método que decrementa el contador almacenado para una regla específica.
        if ( ! current_user_can( 'manage_options' ) ) { // Comprueba que el usuario tenga permisos para modificar la configuración.
            wp_die( esc_html__( 'Permisos insuficientes.', 'cf7-option-limiter' ) ); // Detiene la ejecución mostrando un mensaje seguro cuando falta la capacidad requerida.
        }
        check_admin_referer( 'cf7_option_limiter_release', 'cf7_option_limiter_release_nonce' ); // Valida el nonce recibido para evitar peticiones forzadas desde otros sitios.
        $rule_id = isset( $_POST['rule_id'] ) ? (int) $_POST['rule_id'] : 0; // Recupera y normaliza el identificador de la regla que se desea ajustar.
        $redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : ''; // Obtiene la URL a la que se debe regresar tras completar la acción.
        $default_redirect = add_query_arg( array( 'page' => self::MENU_SLUG ), admin_url( 'admin.php' ) ); // Calcula la URL por defecto de la página principal del plugin para usarla en caso necesario.
        if ( empty( $redirect_to ) || ( function_exists( 'wp_http_validate_url' ) && ! wp_http_validate_url( $redirect_to ) ) ) { // Comprueba si la URL recibida es válida para realizar la redirección.
            $redirect_to = $default_redirect; // Sustituye por la ruta por defecto cuando la URL facilitada no supera la validación.
        }
        $notice_code = 'release_failed'; // Inicializa el código de notificación asumiendo un fallo hasta completar la operación correctamente.
        $rule = ( $rule_id > 0 ) ? CF7_OptionLimiter_DB::get_limit_by_id( $rule_id ) : null; // Recupera la regla completa para verificar su existencia antes de modificarla.
        if ( $rule && isset( $rule['current_count'] ) && (int) $rule['current_count'] > 0 ) { // Comprueba que la regla exista y que el contador sea mayor que cero.
            $success = CF7_OptionLimiter_DB::decrement_counter_by_id( $rule_id ); // Intenta restar una unidad al contador almacenado.
            if ( $success ) { // Comprueba si la operación devolvió un resultado satisfactorio.
                $notice_code = 'released'; // Ajusta el código de notificación para mostrar el mensaje de éxito al usuario.
            }
        }
        $log_context = array( // Construye el contexto que se registrará independientemente del resultado.
            'rule_id'   => $rule_id, // Incluye el identificador de la regla objetivo para facilitar auditorías.
            'action'    => $notice_code, // Anota el desenlace de la operación reutilizando el mismo código que se mostrará en la interfaz.
            'message'   => ( 'released' === $notice_code ) ? __( 'Se liberó un uso desde la página de administración.', 'cf7-option-limiter' ) : __( 'No se pudo liberar el uso solicitado desde la página de administración.', 'cf7-option-limiter' ), // Mensaje descriptivo acorde al resultado.
        );
        if ( $rule ) { // Añade información contextual adicional cuando la regla existe.
            $log_context['form_id']      = isset( $rule['form_id'] ) ? (int) $rule['form_id'] : 0; // Registra el formulario asociado para identificar la regla en los logs.
            $log_context['field_name']   = isset( $rule['field_name'] ) ? $rule['field_name'] : ''; // Anota el nombre del campo limitado.
            $log_context['option_value'] = isset( $rule['option_value'] ) ? $rule['option_value'] : ''; // Almacena el valor específico de la opción limitada.
            $log_context['count_before'] = isset( $rule['current_count'] ) ? (int) $rule['current_count'] : 0; // Documenta el contador previo para reconstruir el estado antes del ajuste.
            if ( 'released' === $notice_code ) { // Calcula el contador resultante únicamente cuando la operación fue satisfactoria.
                $log_context['count_after'] = max( 0, (int) $rule['current_count'] - 1 ); // Determina el valor esperado tras restar una unidad garantizando que no quede negativo.
            }
        }
        CF7_OptionLimiter_Logger::log( 'limit_admin_action', $log_context ); // Registra el resultado en el log manteniendo la trazabilidad desde la interfaz.
        wp_safe_redirect( add_query_arg( array( 'ol_notice' => $notice_code ), $redirect_to ) ); // Redirige de vuelta a la página principal mostrando el aviso correspondiente.
        exit; // Finaliza la ejecución inmediatamente después de iniciar la redirección.
    }

    /**
    * Maneja la eliminación de una regla específica.
    *
    * Explicación:
    * - Resume la tarea principal: Maneja la eliminación de una regla específica.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function handle_delete() { // Método que elimina una regla existente.
        if ( ! current_user_can( 'manage_options' ) ) { // Verifica permisos de administración.
            wp_die( esc_html__( 'Permisos insuficientes.', 'cf7-option-limiter' ) ); // Detiene la ejecución si no tiene permisos.
        }
        check_admin_referer( 'cf7_option_limiter_delete', 'cf7_option_limiter_delete_nonce' ); // Verifica el nonce de borrado.
        $rule_id = isset( $_POST['rule_id'] ) ? (int) $_POST['rule_id'] : 0; // Obtiene el ID de la regla a eliminar.
        $redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : ''; // Recupera la URL a la que se debe regresar tras la eliminación.
        $default_redirect = add_query_arg( array( 'page' => self::MENU_SLUG ), admin_url( 'admin.php' ) ); // Define la URL por defecto hacia la página principal del plugin.
        if ( empty( $redirect_to ) || ( function_exists( 'wp_http_validate_url' ) && ! wp_http_validate_url( $redirect_to ) ) ) { // Comprueba que la URL recibida sea válida.
            $redirect_to = $default_redirect; // Utiliza la ruta por defecto si la URL proporcionada es inválida.
        }
        if ( $rule_id > 0 ) { // Comprueba que el ID sea válido.
            CF7_OptionLimiter_DB::delete_limit( $rule_id ); // Elimina la regla mediante el gestor de base de datos.
            $log_context = array( // Construye el contexto del registro administrativo de eliminación.
                'rule_id' => (int) $rule_id, // Identificador numérico de la regla eliminada.
                'message' => sprintf( __( 'Regla eliminada (ID %d) desde administración.', 'cf7-option-limiter' ), $rule_id ), // Mensaje descriptivo del evento.
                'action'  => 'deleted', // Tipo de acción ejecutada.
            );
            CF7_OptionLimiter_Logger::log( 'limit_admin_action', $log_context ); // Registra la eliminación únicamente en modo depuración.
        }
        wp_safe_redirect( add_query_arg( array( 'ol_notice' => 'deleted' ), $redirect_to ) ); // Redirige al destino deseado mostrando la notificación de eliminación.
        exit; // Termina la ejecución tras la redirección.
    }

    /**
    * Gestiona el formulario que activa o desactiva el modo de depuración persistente.
    *
    * Explicación:
    * - Resume la tarea principal: Gestiona el formulario que activa o desactiva el modo de depuración persistente.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function handle_toggle_debug() { // Método público que alterna el modo depuración del plugin.
        if ( ! current_user_can( 'manage_options' ) ) { // Verifica que el usuario tenga permisos suficientes.
            wp_die( esc_html__( 'Permisos insuficientes.', 'cf7-option-limiter' ) ); // Detiene la ejecución si el usuario no puede gestionar ajustes.
        }
        check_admin_referer( 'cf7_option_limiter_toggle_debug', 'cf7_option_limiter_debug_nonce' ); // Valida el nonce recibido desde el formulario de depuración.
        $enabled = isset( $_POST['cf7_ol_debug_mode'] ); // Determina si el checkbox se marcó solicitando activar el modo depuración.
        CF7_OptionLimiter_Logger::set_debug_mode( $enabled ); // Actualiza el estado persistente del modo depuración y registra el cambio en el log mínimo.
        $notice_code = $enabled ? 'debug_on' : 'debug_off'; // Calcula el código de notificación que se mostrará tras la redirección.
        $redirect_to = add_query_arg( array( 'page' => self::MENU_SLUG, 'ol_notice' => $notice_code ), admin_url( 'admin.php' ) ); // Construye la URL de retorno a la página principal del plugin.
        wp_safe_redirect( $redirect_to ); // Redirige al usuario a la página principal del plugin mostrando la notificación adecuada.
        exit; // Finaliza la ejecución después de iniciar la redirección.
    }

    /**
    * Devuelve la lista de formularios de Contact Form 7 disponibles.
    *
    * Explicación:
    * - Resume la tarea principal: Devuelve la lista de formularios de Contact Form 7 disponibles.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return array<int, array<string, mixed>>
    */
    protected static function get_forms() { // Método auxiliar para obtener los formularios disponibles.
        $forms = array(); // Inicializa el listado de formularios que se devolverá al final.
        if ( function_exists( 'wpcf7_contact_forms' ) ) { // Comprueba si Contact Form 7 expone su función principal para listar formularios.
            foreach ( wpcf7_contact_forms() as $form ) { // Recorre cada formulario detectado mediante la API de Contact Form 7.
                $form_id = self::resolve_form_id( $form ); // Obtiene el identificador numérico del formulario usando un método auxiliar tolerante a distintos tipos de objetos.
                if ( $form_id <= 0 ) { // Verifica que se haya obtenido un identificador válido antes de continuar.
                    continue; // Omite el formulario actual en caso de que no tenga un ID utilizable.
                }
                $forms[] = array( // Añade al listado la información relevante del formulario recuperado.
                    'id'    => $form_id, // Almacena el identificador del formulario para usarlo en selects y consultas.
                    'title' => self::resolve_form_title( $form ), // Obtiene el título legible del formulario usando un método auxiliar que contempla varios escenarios.
                );
            }
        }
        if ( empty( $forms ) && class_exists( 'WP_Query' ) ) { // Si no se recuperaron formularios mediante la API anterior y se dispone de WP_Query, se intenta una búsqueda manual.
            $query = new WP_Query( array( // Construye una consulta directa a la tabla de formularios de Contact Form 7.
                'post_type'      => 'wpcf7_contact_form', // Define el tipo de contenido propio de Contact Form 7.
                'post_status'    => array( 'publish', 'pending', 'draft' ), // Recupera formularios publicados o en borrador para permitir su gestión anticipada.
                'posts_per_page' => -1, // Obtiene todos los formularios sin paginación para completar el selector.
                'orderby'        => 'title', // Ordena los resultados por título para mejorar la legibilidad en el selector.
                'order'          => 'ASC', // Define un orden ascendente alfabético.
            ) );
            if ( ! empty( $query->posts ) ) { // Comprueba que la consulta haya devuelto resultados antes de iterar.
                foreach ( $query->posts as $post ) { // Recorre cada entrada encontrada en la base de datos.
                    $forms[] = array( // Añade la información del formulario al listado principal.
                        'id'    => isset( $post->ID ) ? (int) $post->ID : 0, // Obtiene el identificador del post asociado garantizando un entero.
                        'title' => isset( $post->post_title ) ? $post->post_title : sprintf( __( 'Formulario #%d', 'cf7-option-limiter' ), isset( $post->ID ) ? (int) $post->ID : 0 ), // Usa el título almacenado o un fallback si no existe.
                    );
                }
            }
            if ( function_exists( 'wp_reset_postdata' ) ) { // Comprueba que la función de limpieza de consultas esté disponible.
                wp_reset_postdata(); // Restablece la consulta global para no interferir con otros procesos de WordPress.
            }
        }
        return $forms; // Devuelve la colección de formularios disponibles tras aplicar ambas estrategias de obtención.
    }

    /**
    * Devuelve la configuración localizada utilizada por el script administrativo.
    *
    * Explicación:
    * - Resume la tarea principal: Devuelve la configuración localizada utilizada por el script administrativo.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return array<string, mixed>
    */

    public static function get_localization_data() { // Método público que expone los textos y parámetros utilizados por el script JS.
        return array( // Devuelve el arreglo completo de configuración.
            'ajaxUrl'       => admin_url( 'admin-ajax.php' ), // URL para las peticiones AJAX.
            'adminPostUrl'  => admin_url( 'admin-post.php' ), // URL base que se reutiliza al construir formularios ocultos dinámicos.
            'nonce'         => wp_create_nonce( 'cf7_option_limiter_ajax' ), // Nonce de seguridad para validar peticiones.
            'embeddedNonces' => array( // Colección de nonces que permiten recrear los formularios ocultos cuando no se imprimen desde PHP.
                'save'    => wp_create_nonce( 'cf7_option_limiter_save' ), // Nonce empleado por el flujo de guardado tradicional.
                'delete'  => wp_create_nonce( 'cf7_option_limiter_delete' ), // Nonce empleado por el flujo de eliminación tradicional.
                'release' => wp_create_nonce( 'cf7_option_limiter_release' ), // Nonce empleado por el flujo de liberación tradicional.
            ),
            'i18n'    => array( // Textos traducibles utilizados en la interfaz JS.
                'loading'      => __( 'Cargando campos...', 'cf7-option-limiter' ), // Texto mostrado mientras se cargan campos.
                'noFields'     => __( 'No se detectaron campos compatibles en este formulario.', 'cf7-option-limiter' ), // Mensaje cuando no hay campos disponibles.
                'selectForm'   => __( 'Selecciona un formulario para listar sus campos.', 'cf7-option-limiter' ), // Mensaje inicial.
                'fieldManual'  => __( 'Selecciona un campo del desplegable una vez finalice el análisis del formulario.', 'cf7-option-limiter' ), // Indicación para utilizar únicamente el desplegable de campos.
                'optionManual' => __( 'Selecciona una opción del desplegable asociado al campo elegido.', 'cf7-option-limiter' ), // Indicación para elegir valores detectados automáticamente.
                'selectField'  => __( 'Selecciona un campo para ver sus opciones disponibles.', 'cf7-option-limiter' ), // Mensaje que recuerda seleccionar primero el campo.
                'noOptions'    => __( 'No se detectaron opciones en el formulario para este campo.', 'cf7-option-limiter' ), // Mensaje cuando no se detectan valores compatibles para un campo.
                'missingFieldLabel'   => __( '%s (campo eliminado)', 'cf7-option-limiter' ), // Etiqueta temporal que muestra claramente que el campo ya no existe.
                'missingFieldStatus'  => __( 'El campo original ya no está disponible en el formulario. Selecciona otro campo o elimina la regla.', 'cf7-option-limiter' ), // Mensaje contextual que advierte de la ausencia del campo.
                'missingFieldNotice'  => __( 'La regla hace referencia a un campo que ya no está en el formulario. Revisa la configuración antes de guardarla.', 'cf7-option-limiter' ), // Aviso detallado mostrado en el panel cuando falta el campo.
                'missingOptionLabel'  => __( '%s (opción eliminada)', 'cf7-option-limiter' ), // Etiqueta temporal utilizada cuando una opción dejó de existir.
                'missingOptionStatus' => __( 'La opción original ya no está disponible en el formulario. Selecciona otra opción antes de guardar la regla.', 'cf7-option-limiter' ), // Mensaje contextual para el selector de opciones.
                'missingOptionNotice' => __( 'La regla apunta a una opción que ya no existe en el formulario. Ajusta el valor o elimina la regla para evitar inconsistencias.', 'cf7-option-limiter' ), // Aviso extendido que anima a corregir la regla cuando falta la opción.
                'errorLoading' => __( 'No se pudieron cargar los campos automáticamente. Revisa el formulario y vuelve a intentarlo.', 'cf7-option-limiter' ), // Mensaje mostrado cuando la petición AJAX falla.
                'saveLabel'    => __( 'Guardar límite', 'cf7-option-limiter' ), // Etiqueta utilizada por el botón de guardado.
                'updateLabel'  => __( 'Actualizar límite', 'cf7-option-limiter' ), // Etiqueta utilizada cuando se edita una regla existente.
                'deleteConfirm' => __( '¿Seguro que deseas eliminar esta regla? Recuerda guardar el formulario principal para aplicar los cambios.', 'cf7-option-limiter' ), // Mensaje de confirmación reutilizado en los botones de borrado e incluye una advertencia sobre guardar el formulario principal.
                'saving'       => __( 'Guardando…', 'cf7-option-limiter' ), // Texto mostrado mientras la interfaz espera la respuesta AJAX.
                'saveSuccess'  => __( 'El límite se guardó correctamente.', 'cf7-option-limiter' ), // Mensaje general de confirmación tras un guardado satisfactorio.
                'ajaxValidationError' => __( 'No se pudo guardar el límite porque faltan datos o existe un conflicto.', 'cf7-option-limiter' ), // Mensaje mostrado cuando el servidor rechaza la petición AJAX por validación.
                'ajaxFallback' => __( 'No se pudo completar el guardado mediante AJAX, se intentará con el flujo clásico.', 'cf7-option-limiter' ), // Mensaje mostrado antes de recurrir al envío tradicional.
                'deleteLabel'  => __( 'Eliminar', 'cf7-option-limiter' ), // Etiqueta del botón de borrado generada dinámicamente en la tabla.
                'editLabel'    => __( 'Editar', 'cf7-option-limiter' ), // Etiqueta del botón de edición generado dinámicamente.
                'yesLabel'     => __( 'Sí', 'cf7-option-limiter' ), // Etiqueta reutilizable para indicadores afirmativos.
                'noLabel'      => __( 'No', 'cf7-option-limiter' ), // Etiqueta reutilizable para indicadores negativos.
            ),
        );
    }

    /**
    * Obtiene el identificador de un formulario sin importar si llega como objeto de Contact Form 7 o como post estándar.
    *
    * Explicación:
    * - Resume la tarea principal: Obtiene el identificador de un formulario sin importar si llega como objeto de Contact Form 7 o como post estándar.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param mixed $form Instancia del formulario recuperado.
    *
    * @return int
    */
    protected static function resolve_form_id( $form ) { // Método auxiliar que intenta extraer el ID del formulario en distintos formatos.
        if ( is_object( $form ) && method_exists( $form, 'id' ) ) { // Comprueba si el formulario es un objeto con método id() típico de Contact Form 7.
            return (int) $form->id(); // Devuelve el identificador convertido a entero.
        }
        if ( is_object( $form ) && isset( $form->ID ) ) { // Comprueba si el formulario es un objeto estándar con propiedad ID.
            return (int) $form->ID; // Devuelve la propiedad ID como entero.
        }
        if ( is_array( $form ) && isset( $form['id'] ) ) { // Contempla el caso de que el formulario llegue como arreglo asociativo.
            return (int) $form['id']; // Devuelve el valor de la clave id convertido a entero.
        }
        return 0; // Devuelve cero cuando no es posible determinar el identificador.
    }

    /**
    * Obtiene el título de un formulario asegurando un texto legible en todos los escenarios soportados.
    *
    * Explicación:
    * - Resume la tarea principal: Obtiene el título de un formulario asegurando un texto legible en todos los escenarios soportados.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param mixed $form Instancia del formulario recuperado.
    *
    * @return string
    */
    protected static function resolve_form_title( $form ) { // Método auxiliar que devuelve el título del formulario según el tipo de dato recibido.
        if ( is_object( $form ) && method_exists( $form, 'title' ) ) { // Comprueba si existe el método title() típico de Contact Form 7.
            return (string) $form->title(); // Devuelve el resultado del método como cadena.
        }
        if ( is_object( $form ) && isset( $form->post_title ) ) { // Comprueba si la instancia tiene la propiedad post_title heredada de WP_Post.
            return (string) $form->post_title; // Devuelve el título almacenado en la propiedad.
        }
        if ( is_array( $form ) && isset( $form['title'] ) ) { // Considera formularios representados como arreglo.
            return (string) $form['title']; // Devuelve el valor de la clave title como cadena.
        }
        $form_id = self::resolve_form_id( $form ); // Obtiene el identificador para usarlo en el fallback.
        return sprintf( __( 'Formulario #%d', 'cf7-option-limiter' ), $form_id ); // Devuelve un título genérico basado en el identificador.
    }

    /**
    * Devuelve el título de un formulario usando la lista preparada o el ID en caso de no encontrarlo.
    *
    * Explicación:
    * - Resume la tarea principal: Devuelve el título de un formulario usando la lista preparada o el ID en caso de no encontrarlo.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param int   $form_id Identificador del formulario.
    * @param array $forms   Lista de formularios disponibles.
    *
    * @return string
    */
    protected static function get_form_title( $form_id, $forms ) { // Método auxiliar para obtener el título de un formulario.
        foreach ( $forms as $form ) { // Recorre la lista de formularios proporcionada.
            if ( (int) $form['id'] === (int) $form_id ) { // Comprueba si coincide el ID buscado.
                return $form['title']; // Devuelve el título encontrado.
            }
        }
        return sprintf( __( 'Formulario #%d', 'cf7-option-limiter' ), $form_id ); // Devuelve un texto genérico si no se encontró el título.
    }

    /**
    * Traduce el identificador del periodo a una etiqueta legible para el usuario.
    *
    * Explicación:
    * - Resume la tarea principal: Traduce el identificador del periodo a una etiqueta legible para el usuario.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param string $period Identificador del periodo (none, hour, day, week).
    *
    * @return string
    */
    public static function get_period_label( $period ) { // Método auxiliar que convierte el periodo en texto.
        switch ( $period ) { // Evalúa cada valor posible del periodo.
            case 'hour': // Cuando el periodo es por hora.
                return __( 'Por hora', 'cf7-option-limiter' ); // Devuelve la etiqueta traducida correspondiente.
            case 'day': // Cuando el periodo es por día.
                return __( 'Por día', 'cf7-option-limiter' ); // Devuelve la etiqueta adecuada.
            case 'week': // Cuando el periodo es por semana.
                return __( 'Por semana', 'cf7-option-limiter' ); // Devuelve la etiqueta correspondiente.
            default: // Cualquier otro valor (incluye 'none').
                return __( 'Total', 'cf7-option-limiter' ); // Devuelve la etiqueta para límites totales.
        }
    }

    /**
    * Maneja la petición AJAX que analiza un formulario y devuelve sus campos limitables.
    *
    * Explicación:
    * - Resume la tarea principal: Maneja la petición AJAX que analiza un formulario y devuelve sus campos limitables.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function ajax_scan_form() { // Método que responde a las peticiones AJAX desde la interfaz administrativa.
        check_ajax_referer( 'cf7_option_limiter_ajax', 'nonce' ); // Valida el nonce para la petición AJAX.
        if ( ! current_user_can( 'manage_options' ) ) { // Verifica los permisos de usuario.
            wp_send_json_error( array( 'message' => __( 'Permisos insuficientes.', 'cf7-option-limiter' ) ), 403 ); // Devuelve un error JSON con código 403.
        }
        $form_id = isset( $_GET['form_id'] ) ? (int) $_GET['form_id'] : 0; // Obtiene el ID del formulario a escanear.
        if ( $form_id <= 0 || ! class_exists( 'WPCF7_ContactForm' ) ) { // Comprueba la validez del ID y disponibilidad de la clase.
            wp_send_json_error( array( 'message' => __( 'Formulario no válido.', 'cf7-option-limiter' ) ) ); // Devuelve un error si no se puede procesar.
        }
        $form = WPCF7_ContactForm::get_instance( $form_id ); // Recupera la instancia del formulario solicitado.
        if ( ! $form ) { // Comprueba si el formulario existe.
            wp_send_json_error( array( 'message' => __( 'No se encontró el formulario solicitado.', 'cf7-option-limiter' ) ) ); // Devuelve un error si no existe.
        }
        $tags = method_exists( $form, 'scan_form_tags' ) ? $form->scan_form_tags() : array(); // Obtiene la lista de etiquetas del formulario utilizando el método proporcionado por Contact Form 7.
        $fields = array(); // Inicializa el arreglo donde se almacenarán los campos compatibles.
        foreach ( $tags as $tag ) { // Recorre cada etiqueta analizada sin importar si llega como objeto o arreglo.
            $normalized = self::normalize_tag( $tag ); // Convierte la etiqueta en una estructura homogénea y devuelve null si no es compatible.
            if ( empty( $normalized ) ) { // Comprueba si la etiqueta procesada no es válida para el limitador.
                continue; // Omite la etiqueta actual cuando no cumple los requisitos necesarios.
            }
            $fields[] = $normalized; // Añade el campo normalizado a la colección final que se enviará por AJAX.
        }
        wp_send_json_success( array( 'fields' => $fields ) ); // Devuelve la respuesta JSON con los campos compatibles.
    }

    /**
    * Normaliza una etiqueta de Contact Form 7 para obtener nombre y opciones independientemente del formato recibido.
    *
    * Explicación:
    * - Resume la tarea principal: Normaliza una etiqueta de Contact Form 7 para obtener nombre y opciones independientemente del formato recibido.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param mixed $tag Etiqueta devuelta por Contact Form 7 que puede ser objeto o arreglo.
    *
    * @return array<string, mixed>|null
    */
    protected static function normalize_tag( $tag ) { // Método auxiliar que transforma la etiqueta en un formato uniforme.
        $base_type = ''; // Inicializa la variable que almacenará el tipo base de campo (select, radio o checkbox).
        $field_name = ''; // Inicializa la variable para el nombre del campo.
        $values = array(); // Preparación del listado de valores disponibles en el campo.
        $labels = array(); // Preparación del listado de etiquetas asociadas a los valores.
        if ( is_object( $tag ) ) { // Comprueba si la etiqueta llega como objeto WPCF7_FormTag.
            $base_type = isset( $tag->basetype ) ? (string) $tag->basetype : ''; // Extrae el tipo base utilizando la propiedad pública del objeto.
            $field_name = isset( $tag->name ) ? (string) $tag->name : ''; // Obtiene el nombre del campo desde la propiedad del objeto.
            $values = isset( $tag->values ) ? (array) $tag->values : array(); // Recupera los valores definidos en el formulario.
            $labels = isset( $tag->labels ) ? (array) $tag->labels : $values; // Recupera las etiquetas asociadas o reutiliza los valores si no existen.
        } elseif ( is_array( $tag ) ) { // Comprueba si la etiqueta llega como arreglo asociativo para mantener compatibilidad con versiones antiguas.
            $base_type = isset( $tag['basetype'] ) ? (string) $tag['basetype'] : ''; // Obtiene el tipo base del arreglo.
            $field_name = isset( $tag['name'] ) ? (string) $tag['name'] : ''; // Recupera el nombre del campo desde el arreglo.
            $values = isset( $tag['raw_values'] ) ? (array) $tag['raw_values'] : array(); // Obtiene los valores crudos definidos en el formulario.
            $labels = isset( $tag['labels'] ) ? (array) $tag['labels'] : $values; // Recupera las etiquetas asociadas o reutiliza los valores en su defecto.
        } else { // Si la etiqueta no es ni objeto ni arreglo se considera incompatible.
            return null; // Finaliza retornando null para indicar que la etiqueta no puede procesarse.
        }
        if ( empty( $field_name ) || ! in_array( $base_type, array( 'select', 'radio', 'checkbox' ), true ) ) { // Verifica que exista nombre y que el tipo sea uno de los soportados.
            return null; // Devuelve null para omitir etiquetas que no se ajustan a los requisitos.
        }
        $options = array(); // Inicializa el arreglo donde se almacenarán las opciones normalizadas.
        foreach ( $values as $index => $value ) { // Recorre cada valor definido para el campo limitable.
            $raw_value = (string) $value; // Convierte el valor a cadena para evitar problemas de tipos.
            $label = isset( $labels[ $index ] ) ? (string) $labels[ $index ] : $raw_value; // Obtiene la etiqueta correspondiente o reutiliza el valor si no existe etiqueta.
            $options[] = array( // Añade la opción normalizada al listado final.
                'value' => $raw_value, // Define el valor real que se enviará en el formulario.
                'label' => $label, // Define la etiqueta visible asociada al valor.
            );
        }
        return array( // Devuelve la estructura homogénea requerida por la interfaz administrativa.
            'name'    => $field_name, // Nombre del campo que será usado en los selectores de la interfaz.
            'options' => $options, // Listado de opciones compatibles con la limitación.
        );
    }
}
