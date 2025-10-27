<?php
// Evita acceso directo al archivo sin pasar por WordPress.
if ( ! defined( 'ABSPATH' ) ) { // Verifica la constante de seguridad estándar de WordPress.
    exit; // Finaliza inmediatamente si no se cumple la condición de seguridad.
}

// Declara la clase encargada de reunir los hooks comunes del plugin.
class CF7_OptionLimiter_Hooks { // Define la clase que gestionará los hooks globales.

    // Guarda si se ha mostrado ya el aviso de dependencias para evitar duplicados.
    protected static $notice_printed = false; // Propiedad estática para controlar la impresión de avisos.

    // Indica si los recursos administrativos ya han sido registrados durante esta petición.
    protected static $admin_assets_registered = false; // Propiedad que evita registrar estilos y scripts múltiples veces.

    /**
     * Inicializa los hooks necesarios para dependencias y reseteos periódicos.
     *
     * @return void
     */
    public static function init() { // Método estático de arranque.
        add_action( 'init', array( __CLASS__, 'maybe_reset_limits' ), 8 ); // Programa el reseteo de límites en cada carga pública.
        add_action( 'admin_init', array( __CLASS__, 'maybe_reset_limits' ), 8 ); // Asegura el reseteo también en el área de administración.
        add_action( 'admin_init', array( __CLASS__, 'capture_cleanup_preference' ), 9 ); // Captura la preferencia de limpieza antes de ejecutar la desactivación.
        add_action( 'admin_notices', array( __CLASS__, 'print_dependency_notice' ) ); // Genera avisos si faltan plugins requeridos.
        add_action( 'network_admin_notices', array( __CLASS__, 'print_dependency_notice' ) ); // Muestra el mismo aviso en el administrador de red.
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_shared_admin_assets' ) ); // Encola estilos y scripts compartidos.
        add_filter( 'plugin_action_links_' . CF7_OPTION_LIMITER_BASENAME, array( __CLASS__, 'add_plugin_action_links' ) ); // Añade enlaces personalizados en la fila del plugin.
        add_filter( 'plugin_row_meta', array( __CLASS__, 'add_plugin_row_meta' ), 10, 2 ); // Inserta enlaces adicionales en la sección de metadatos del plugin.
    }

    /**
     * Resetea los contadores cuando corresponda según la política establecida.
     *
     * @return void
     */
    public static function maybe_reset_limits() { // Método que invoca el gestor de base de datos para resetear límites.
        CF7_OptionLimiter_DB::reset_periods(); // Utiliza el gestor de base de datos para recalcular los contadores caducados.
    }

    /**
     * Imprime un aviso si Contact Form 7 no está activo.
     *
     * @return void
     */
    public static function print_dependency_notice() { // Método encargado de mostrar avisos en el escritorio.
        if ( self::$notice_printed ) { // Comprueba si ya se imprimió el aviso para no repetirlo.
            return; // Finaliza anticipadamente si el aviso ya ha sido mostrado.
        }

        $missing = array(); // Inicializa un arreglo para guardar dependencias faltantes.

        if ( ! defined( 'WPCF7_VERSION' ) ) { // Revisa si Contact Form 7 está disponible mediante su constante.
            $missing[] = __( 'Contact Form 7', 'cf7-option-limiter' ); // Añade Contact Form 7 a la lista de faltantes.
        }

        if ( empty( $missing ) ) { // Verifica si la lista de dependencias faltantes está vacía.
            return; // Si no falta nada, no se muestra aviso alguno.
        }

        self::$notice_printed = true; // Marca que el aviso ha sido generado para evitar duplicados.

        printf( // Imprime el HTML del aviso con los plugins faltantes.
            '<div class="notice notice-warning"><p>%s</p></div>', // Plantilla HTML con clases de aviso de WordPress.
            esc_html( sprintf( // Escapa y formatea el texto final del aviso.
                /* translators: %s: lista de plugins faltantes */
                __( 'CF7 Option Limiter requiere los siguientes plugins activos: %s.', 'cf7-option-limiter' ), // Texto traducible del aviso.
                implode( ', ', $missing ) // Convierte el listado de dependencias en una cadena legible.
            ) )
        );
    }

    /**
     * Encola recursos compartidos utilizados por varios componentes en la administración.
     *
     * @param string $hook Identificador del hook actual de admin enqueue.
     *
     * @return void
     */
    public static function enqueue_shared_admin_assets( $hook ) { // Método para encolar estilos y scripts compartidos.
        if ( ! self::$admin_assets_registered ) { // Comprueba si los recursos ya se registraron.
            wp_register_style( // Registra la hoja de estilos administrativa.
                'cf7-option-limiter-admin', // Identificador único del estilo.
                CF7_OPTION_LIMITER_URL . 'assets/admin.css', // URL del archivo CSS dentro del plugin.
                array(), // Lista vacía de dependencias para el CSS.
                CF7_OPTION_LIMITER_VERSION // Utiliza la versión actual del plugin para controlar la caché.
            );

            wp_register_script( // Registra el script administrativo principal.
                'cf7-option-limiter-admin', // Identificador único del script.
                CF7_OPTION_LIMITER_URL . 'assets/admin.js', // URL del archivo JavaScript administrativo.
                array( 'jquery' ), // Declara jQuery como dependencia para asegurar su carga previa.
                CF7_OPTION_LIMITER_VERSION, // Utiliza la versión del plugin para sincronizar el cache busting.
                true // Indica que el script debe cargarse al final del documento.
            );

            self::$admin_assets_registered = true; // Marca que los recursos ya han sido registrados en esta petición.
        }

        if ( 'plugins.php' === $hook ) { // Comprueba si nos encontramos en el listado de plugins.
            wp_enqueue_script( // Encola el script encargado de gestionar la confirmación de limpieza.
                'cf7-option-limiter-plugins', // Identificador único del script específico de la pantalla de plugins.
                CF7_OPTION_LIMITER_URL . 'assets/plugins.js', // URL del archivo JavaScript con la lógica de confirmación.
                array(), // No requiere dependencias adicionales.
                CF7_OPTION_LIMITER_VERSION, // Versión del recurso alineada con el plugin.
                true // Solicita cargar el script en el pie de página para no bloquear la renderización.
            );
            wp_localize_script( // Expone la configuración necesaria para el script de confirmación.
                'cf7-option-limiter-plugins', // Identificador del script que recibirá la configuración.
                'CF7OptionLimiterPlugins', // Nombre del objeto JavaScript que contendrá los datos.
                array( // Arreglo con la configuración necesaria para el diálogo de limpieza.
                    'prompt'      => __( '¿Quieres eliminar también las limitaciones guardadas en la base de datos? Pulsa Aceptar para eliminarlas o Cancelar para conservarlas.', 'cf7-option-limiter' ), // Mensaje mostrado en la confirmación sobre la limpieza opcional.
                    'param'       => 'cf7_ol_cleanup', // Nombre del parámetro que transportará la preferencia elegida.
                    'removeValue' => '1', // Valor que indica que se deben eliminar los datos almacenados.
                    'keepValue'   => '0', // Valor que indica que se deben conservar los registros.
                    'pluginFile'  => CF7_OPTION_LIMITER_BASENAME, // Identificador del plugin utilizado para localizar la fila adecuada.
                    'warning'     => __( 'Al desactivar o eliminar CF7 Option Limiter las reglas configuradas dejarán de aplicarse en los formularios hasta volver a activarlo.', 'cf7-option-limiter' ), // Mensaje que advierte de la pérdida temporal de las reglas al desactivar o borrar el plugin.
                )
            );
            return; // Finaliza tras preparar la pantalla de plugins porque no es necesario registrar más recursos aquí.
        }

        if ( strpos( $hook, 'wpcf7' ) === false ) { // Comprueba si estamos en pantallas relevantes para Contact Form 7.
            return; // Sale si no se trata de las pantallas soportadas.
        }
    }

    /**
     * Captura la preferencia del usuario respecto a eliminar o conservar los datos durante la desactivación.
     *
     * @return void
     */
    public static function capture_cleanup_preference() { // Método que almacena temporalmente la preferencia de limpieza.
        if ( ! is_admin() ) { // Comprueba que la ejecución ocurra en el administrador.
            return; // Finaliza si la petición no pertenece al área administrativa.
        }
        if ( ! current_user_can( 'activate_plugins' ) ) { // Verifica que el usuario tenga permisos suficientes para gestionar plugins.
            return; // Finaliza cuando el usuario no puede modificar el estado de los plugins.
        }
        if ( ! isset( $_REQUEST['cf7_ol_cleanup'] ) ) { // Comprueba si se proporcionó el parámetro de limpieza.
            return; // Finaliza si no se detecta la preferencia.
        }
        $action = ''; // Inicializa la variable que almacenará la acción solicitada en la pantalla de plugins.
        if ( isset( $_REQUEST['action'] ) && '-1' !== $_REQUEST['action'] ) { // Comprueba si se definió la acción principal en la petición.
            $action = sanitize_text_field( wp_unslash( (string) $_REQUEST['action'] ) ); // Normaliza el valor recibido conservando el formato esperado por WordPress.
        }
        if ( '' === $action && isset( $_REQUEST['action2'] ) && '-1' !== $_REQUEST['action2'] ) { // Comprueba la acción secundaria utilizada en acciones masivas.
            $action = sanitize_text_field( wp_unslash( (string) $_REQUEST['action2'] ) ); // Normaliza la acción secundaria conservando los separadores originales.
        }
        $targets        = array(); // Inicializa el arreglo que almacenará los plugins afectados en la petición actual.
        $primary_target = ''; // Guarda el plugin individual cuando la acción no es masiva.
        if ( isset( $_REQUEST['plugin'] ) ) { // Comprueba si se recibió un plugin específico.
            $primary_target = sanitize_text_field( wp_unslash( (string) $_REQUEST['plugin'] ) ); // Normaliza el identificador del plugin individual.
            $targets[]      = $primary_target; // Añade el plugin recibido al listado.
        }
        $is_bulk_action = false; // Inicializa el indicador que identifica si la acción se ejecuta en modo masivo.
        if ( isset( $_REQUEST['checked'] ) && is_array( $_REQUEST['checked'] ) ) { // Comprueba si se trata de una acción masiva.
            $is_bulk_action = true; // Marca que se está procesando una acción sobre múltiples plugins.
            foreach ( $_REQUEST['checked'] as $plugin_file ) { // Recorre los plugins seleccionados.
                $targets[] = sanitize_text_field( wp_unslash( (string) $plugin_file ) ); // Añade cada plugin al listado de objetivos.
            }
        }
        if ( empty( $targets ) || ! in_array( CF7_OPTION_LIMITER_BASENAME, $targets, true ) ) { // Comprueba si nuestro plugin está implicado en la acción.
            return; // Finaliza sin almacenar preferencias cuando la acción no nos afecta.
        }
        $nonce_action = self::determine_cleanup_nonce_action( $action, $primary_target, $is_bulk_action ); // Calcula la acción de nonce correspondiente según el contexto.
        if ( '' === $nonce_action ) { // Comprueba si no se pudo determinar la acción del nonce.
            return; // Finaliza si no se puede validar la petición de forma segura.
        }
        check_admin_referer( $nonce_action ); // Valida el nonce asociado a la acción antes de modificar cualquier opción persistente.
        $raw_preference = sanitize_text_field( wp_unslash( (string) $_REQUEST['cf7_ol_cleanup'] ) ); // Normaliza el valor recibido del parámetro.
        $preference     = ( '1' === $raw_preference || 'remove' === $raw_preference ) ? 'remove' : 'keep'; // Convierte el valor a una etiqueta conocida.
        update_option( CF7_OptionLimiter_DB::CLEANUP_OPTION, $preference ); // Almacena la preferencia para que pueda consultarse durante la desactivación o desinstalación.
    }

    /**
     * Determina la acción de nonce que debe verificarse según el contexto de la petición.
     *
     * @param string $action          Acción solicitada en la pantalla de plugins.
     * @param string $primary_target  Plugin recibido en la petición individual.
     * @param bool   $is_bulk_action  Indicador de que la petición procesa múltiples elementos.
     *
     * @return string
     */
    protected static function determine_cleanup_nonce_action( $action, $primary_target, $is_bulk_action ) { // Método auxiliar que identifica la acción de nonce a validar.
        if ( 'deactivate' === $action && $primary_target ) { // Comprueba si se trata de una desactivación individual.
            return 'deactivate-plugin_' . $primary_target; // Devuelve la acción específica utilizada por WordPress para validar la desactivación individual.
        }
        if ( 'delete-plugin' === $action && $primary_target ) { // Comprueba si se trata de un borrado individual.
            return 'delete-plugin_' . $primary_target; // Devuelve la acción específica utilizada por WordPress para validar el borrado desde un enlace directo.
        }
        if ( $is_bulk_action && in_array( $action, array( 'deactivate-selected', 'delete-selected' ), true ) ) { // Comprueba si se trata de una acción masiva admitida.
            $is_network_admin = function_exists( 'is_network_admin' ) ? is_network_admin() : false; // Determina si la petición se ejecuta en el administrador de red.
            return $is_network_admin ? 'bulk-plugins-network' : 'bulk-plugins'; // Devuelve la acción de nonce adecuada según el contexto (sitio individual o red).
        }
        if ( 'delete-selected' === $action && $primary_target ) { // Contempla el caso en el que WordPress envía un solo plugin pero reutiliza la acción masiva.
            $is_network_admin = function_exists( 'is_network_admin' ) ? is_network_admin() : false; // Determina si estamos operando en la administración de red.
            return $is_network_admin ? 'bulk-plugins-network' : 'bulk-plugins'; // Devuelve la acción de nonce compartida para borrados individuales que usan la ruta masiva.
        }
        return ''; // Devuelve cadena vacía cuando no existe una acción conocida, evitando validar o actualizar opciones sin garantías.
    }

    /**
     * Añade un enlace directo a la documentación dentro de la fila del plugin.
     *
     * @param array<int, string> $links  Conjunto de enlaces actuales.
     *
     * @return array<int, string>
     */
    public static function add_plugin_action_links( $links ) { // Método que añade enlaces personalizados a la fila del plugin.
        $links[] = '<a href="' . esc_url( CF7_OptionLimiter_Docs::get_page_url() ) . '">' . esc_html__( 'Documentos y preguntas frecuentes', 'cf7-option-limiter' ) . '</a>'; // Inserta el enlace hacia la documentación interna.
        return $links; // Devuelve el arreglo con el nuevo enlace incluido.
    }

    /**
     * Añade enlaces adicionales en la sección de metadatos situada bajo la descripción del plugin.
     *
     * @param array<int, string> $links Conjunto de enlaces actuales.
     * @param string             $file  Identificador del plugin al que pertenecen los enlaces.
     *
     * @return array<int, string>
     */
    public static function add_plugin_row_meta( $links, $file ) { // Método que extiende los metadatos de la fila del plugin.
        if ( CF7_OPTION_LIMITER_BASENAME !== $file ) { // Comprueba si la fila corresponde a nuestro plugin.
            return $links; // Devuelve los enlaces sin modificar cuando se trata de otro plugin.
        }
        $links[] = '<span class="cf7-option-limiter-row-docs"><a href="' . esc_url( CF7_OptionLimiter_Docs::get_page_url() ) . '">' . esc_html__( 'Documentos y preguntas frecuentes', 'cf7-option-limiter' ) . '</a></span>'; // Añade el enlace con un contenedor que permite personalizar estilos.
        return $links; // Devuelve el arreglo extendido con el nuevo enlace.
    }
}
