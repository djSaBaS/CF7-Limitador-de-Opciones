<?php
/**
 * Plugin Name: CF7 Option Limiter
 * Plugin URI: https://humanitaseducacion.com/
 * Description: Amplía Contact Form 7 permitiendo limitar el número de selecciones por opción y mostrando mensajes personalizados cuando se agotan.
 * Version: 1.0.30
 * Author: Juan Antonio Sánchez Plaza - Sistemas HCE
 * Author URI: https://www.linkedin.com/in/juanantoniosanchezplaza/
 * Text Domain: cf7-option-limiter
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Evita ejecuciones directas fuera del contexto de WordPress.
if ( ! defined( 'ABSPATH' ) ) { // Comprueba si la constante base de WordPress está definida.
    exit; // Finaliza el script inmediatamente para proteger el archivo.
}

// Define la ruta absoluta del plugin para reutilizarla en todo el código.
define( 'CF7_OPTION_LIMITER_DIR', plugin_dir_path( __FILE__ ) ); // Constante con el directorio físico del plugin.

// Declara la versión actual del plugin para reutilizarla en recursos y pantallas.
define( 'CF7_OPTION_LIMITER_VERSION', '1.0.30' ); // Constante que sincroniza la versión mostrada y utilizada en caché con la nueva publicación.

// Guarda el basename del plugin para reutilizarlo en filtros y enlaces personalizados.
define( 'CF7_OPTION_LIMITER_BASENAME', plugin_basename( __FILE__ ) ); // Constante con el identificador único del plugin.

// Define la URL base del plugin por si fuese necesaria para cargar recursos externos.
define( 'CF7_OPTION_LIMITER_URL', plugin_dir_url( __FILE__ ) ); // Constante con la URL pública del plugin.

// Define el nombre del archivo principal del plugin útil para hooks de activación y desactivación.
define( 'CF7_OPTION_LIMITER_FILE', __FILE__ ); // Constante con la ruta completa de este archivo.

// Carga el gestor de base de datos que contiene todas las operaciones SQL.
require_once CF7_OPTION_LIMITER_DIR . 'includes/class-db-manager.php'; // Incluye la clase responsable de la tabla personalizada.

// Carga el registrador de eventos que crea y rota los archivos de log.
require_once CF7_OPTION_LIMITER_DIR . 'includes/class-logger.php'; // Incluye la clase encargada del logging interno.

// Carga el manejador principal de límites que se enlaza con los hooks de Contact Form 7.
require_once CF7_OPTION_LIMITER_DIR . 'includes/class-limiter-handler.php'; // Incluye la lógica que aplica los límites en formularios.

// Carga los hooks comunes que inicializan el plugin y validan dependencias.
require_once CF7_OPTION_LIMITER_DIR . 'includes/hooks.php'; // Incluye el archivo donde se registran los hooks globales.

// Carga el panel de administración para gestionar las reglas desde la interfaz de WordPress.
require_once CF7_OPTION_LIMITER_DIR . 'admin/class-admin-page.php'; // Incluye la clase que genera la página de ajustes.

// Carga la integración con el editor de Contact Form 7 para gestionar límites sin abandonar el formulario.
require_once CF7_OPTION_LIMITER_DIR . 'admin/class-cf7-editor-panel.php'; // Incluye la clase que renderiza el panel incrustado.

// Carga la página de documentación y preguntas frecuentes accesible desde el listado de plugins.
require_once CF7_OPTION_LIMITER_DIR . 'includes/class-docs-page.php'; // Incluye la clase encargada de renderizar la documentación interna.

// Registra la creación de la tabla personalizada durante la activación del plugin.
register_activation_hook( CF7_OPTION_LIMITER_FILE, array( 'CF7_OptionLimiter_DB', 'activate' ) ); // Ejecuta la rutina de activación.

// Registra el limpiado de recursos temporales durante la desactivación del plugin.
register_deactivation_hook( CF7_OPTION_LIMITER_FILE, array( 'CF7_OptionLimiter_DB', 'deactivate' ) ); // Ejecuta la rutina de desactivación.

// Registra la rutina de desinstalación para permitir limpiezas controladas cuando se elimina el plugin.
register_uninstall_hook( CF7_OPTION_LIMITER_FILE, array( 'CF7_OptionLimiter_DB', 'uninstall' ) ); // Ejecuta la rutina de desinstalación personalizada.

// Inicializa todos los componentes del plugin una vez que WordPress y los plugins han cargado.
add_action( 'plugins_loaded', 'cf7_option_limiter_bootstrap', 5 ); // Asocia la función bootstrap al evento plugins_loaded.

if ( ! function_exists( 'cf7_option_limiter_bootstrap' ) ) { // Evita redeclaraciones en caso de múltiples inclusiones.
    /**
     * Inicializa cada subsistema del plugin asegurando el orden correcto.
     *
     * @return void
     */
    function cf7_option_limiter_bootstrap() { // Define la función principal de arranque.
        CF7_OptionLimiter_Logger::init(); // Inicializa el logger creando el archivo y preparando la rotación.
        CF7_OptionLimiter_Logger::maybe_log_version_change(); // Registra instalación o actualización antes de continuar con el arranque.
        CF7_OptionLimiter_DB::init(); // Configura el gestor de base de datos y registra el nombre de tabla con prefijo dinámico.
        CF7_OptionLimiter_Hooks::init(); // Inicializa los hooks compartidos y las comprobaciones de dependencias.
        CF7_OptionLimiter_Limiter::init(); // Prepara los filtros y acciones que afectan a Contact Form 7.
        CF7_OptionLimiter_Admin::init(); // Carga el panel de administración y los recursos asociados.
        CF7_OptionLimiter_CF7_Panel::init(); // Registra el panel incrustado dentro del editor de Contact Form 7.
        CF7_OptionLimiter_Docs::init(); // Registra la página de documentación y los enlaces asociados en el administrador.
    }
}
