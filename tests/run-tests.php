<?php
// Define constantes y funciones mínimas para que los archivos del plugin se carguen en el entorno de pruebas sin WordPress.
define( 'ABSPATH', __DIR__ . '/../' ); // Define ABSPATH para evitar que los archivos finalicen su ejecución.
if ( ! defined( 'WPCF7_VERSION' ) ) { // Comprueba si la constante de Contact Form 7 no está definida en el entorno de pruebas.
    define( 'WPCF7_VERSION', '1.0.0' ); // Define una versión simulada para permitir que los hooks del panel se registren.
}
if ( ! function_exists( '__' ) ) { // Comprueba si la función de traducción estándar no existe.
    /**
    * Declara una versión simplificada de __().
    *
    * Explicación:
    * - Resume la tarea principal: Declara una versión simplificada de __().
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $text Valor utilizado por la función __.
    * @param mixed $domain Valor utilizado por la función __.
    *
    * @return mixed Resultado devuelto por la función __.
    */
    function __( $text, $domain = null ) { // Declara una versión simplificada de __().
        return $text; // Devuelve el mismo texto ya que no se evalúan traducciones en las pruebas.
    }
}
if ( ! function_exists( 'esc_html__' ) ) { // Comprueba si la función esc_html__ no está disponible.
    /**
    * Declara la función de ayuda de traducción.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función de ayuda de traducción.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $text Valor utilizado por la función esc_html__.
    * @param mixed $domain Valor utilizado por la función esc_html__.
    *
    * @return mixed Resultado devuelto por la función esc_html__.
    */
    function esc_html__( $text, $domain = null ) { // Declara la función de ayuda de traducción.
        return $text; // Devuelve el texto sin modificar para fines de prueba.
    }
}
if ( ! function_exists( 'esc_attr__' ) ) { // Comprueba si la función esc_attr__ no existe.
    /**
    * Declara un sustituto básico.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto básico.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $text Valor utilizado por la función esc_attr__.
    * @param mixed $domain Valor utilizado por la función esc_attr__.
    *
    * @return mixed Resultado devuelto por la función esc_attr__.
    */
    function esc_attr__( $text, $domain = null ) { // Declara un sustituto básico.
        return $text; // Devuelve el texto directamente sin escapar adicional.
    }
}
if ( ! function_exists( 'esc_html' ) ) { // Comprueba si la función esc_html no está disponible.
    /**
    * Declara la función ficticia.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función ficticia.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $text Valor utilizado por la función esc_html.
    *
    * @return mixed Resultado devuelto por la función esc_html.
    */
    function esc_html( $text ) { // Declara la función ficticia.
        return $text; // Devuelve el texto sin procesamiento adicional.
    }
}
if ( ! function_exists( 'esc_attr' ) ) { // Comprueba si la función esc_attr no existe.
    /**
    * Declara la función ficticia.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función ficticia.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $text Valor utilizado por la función esc_attr.
    *
    * @return mixed Resultado devuelto por la función esc_attr.
    */
    function esc_attr( $text ) { // Declara la función ficticia.
        return $text; // Devuelve el texto sin cambios.
    }
}
if ( ! class_exists( 'WP_Error' ) ) { // Comprueba si la clase WP_Error no está disponible en el entorno de pruebas.
    class WP_Error { // Declara una implementación mínima de la clase WP_Error.
        public $errors = array(); // Almacena los mensajes registrados por código de error.
        public $error_data = array(); // Almacena datos adicionales asociados a los códigos registrados.
        /**
        * Constructor que permite registrar un error inicial.
        *
        * Explicación:
        * - Resume la tarea principal: Constructor que permite registrar un error inicial.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $code Valor utilizado por la función __construct.
        * @param mixed $message Valor utilizado por la función __construct.
        * @param mixed $data Valor utilizado por la función __construct.
        *
        * @return mixed Resultado devuelto por la función __construct.
        */
        public function __construct( $code = '', $message = '', $data = array() ) { // Constructor que permite registrar un error inicial.
            if ( $code ) { // Comprueba si se proporcionó un código de error.
                $this->add( $code, $message, $data ); // Registra el error inicial utilizando el método add para mantener consistencia.
            }
        }
        /**
        * Permite añadir errores adicionales replicando la firma original.
        *
        * Explicación:
        * - Resume la tarea principal: Permite añadir errores adicionales replicando la firma original.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $code Valor utilizado por la función add.
        * @param mixed $message Valor utilizado por la función add.
        * @param mixed $data Valor utilizado por la función add.
        *
        * @return mixed Resultado devuelto por la función add.
        */
        public function add( $code, $message, $data = array() ) { // Permite añadir errores adicionales replicando la firma original.
            if ( ! isset( $this->errors[ $code ] ) ) { // Comprueba si aún no se han registrado errores con el mismo código.
                $this->errors[ $code ] = array(); // Inicializa la colección de mensajes para el código indicado.
            }
            $this->errors[ $code ][] = $message; // Añade el mensaje recibido a la lista asociada al código.
            if ( ! empty( $data ) ) { // Comprueba si se proporcionaron datos adicionales.
                $this->error_data[ $code ] = $data; // Almacena los datos adicionales asociados al código.
            }
        }
        /**
        * Devuelve el primer código registrado replicando el comportamiento habitual.
        *
        * Explicación:
        * - Resume la tarea principal: Devuelve el primer código registrado replicando el comportamiento habitual.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @return mixed Resultado devuelto por la función get_error_code.
        */
        public function get_error_code() { // Devuelve el primer código registrado replicando el comportamiento habitual.
            $codes = array_keys( $this->errors ); // Recupera todos los códigos registrados.
            return isset( $codes[0] ) ? $codes[0] : ''; // Devuelve el primer código o una cadena vacía si no existen errores.
        }
        /**
        * Recupera el primer mensaje almacenado para un código específico.
        *
        * Explicación:
        * - Resume la tarea principal: Recupera el primer mensaje almacenado para un código específico.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $code Valor utilizado por la función get_error_message.
        *
        * @return mixed Resultado devuelto por la función get_error_message.
        */
        public function get_error_message( $code = '' ) { // Recupera el primer mensaje almacenado para un código específico.
            $code = $code ? $code : $this->get_error_code(); // Determina qué código se debe consultar.
            if ( empty( $code ) || empty( $this->errors[ $code ] ) ) { // Comprueba si no existen mensajes registrados para el código indicado.
                return ''; // Devuelve una cadena vacía cuando no hay mensajes disponibles.
            }
            return $this->errors[ $code ][0]; // Devuelve el primer mensaje asociado al código.
        }
    }
}
if ( ! function_exists( 'is_wp_error' ) ) { // Comprueba si la función is_wp_error no está disponible.
    /**
    * Declara un sustituto que replica el comportamiento de WordPress.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que replica el comportamiento de WordPress.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $thing Valor utilizado por la función is_wp_error.
    *
    * @return mixed Resultado devuelto por la función is_wp_error.
    */
    function is_wp_error( $thing ) { // Declara un sustituto que replica el comportamiento de WordPress.
        return $thing instanceof WP_Error; // Devuelve true únicamente cuando el valor recibido es una instancia de WP_Error.
    }
}
if ( ! function_exists( 'esc_url' ) ) { // Comprueba si la función esc_url no existe.
    /**
    * Declara la función ficticia para las pruebas.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función ficticia para las pruebas.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $url Valor utilizado por la función esc_url.
    *
    * @return mixed Resultado devuelto por la función esc_url.
    */
    function esc_url( $url ) { // Declara la función ficticia para las pruebas.
        return $url; // Devuelve la URL sin modificación, suficiente para las comprobaciones de lógica.
    }
}
if ( ! function_exists( 'esc_url_raw' ) ) { // Comprueba si la función esc_url_raw no está disponible en las pruebas.
    /**
    * Declara un sustituto básico alineado con la firma original de WordPress.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto básico alineado con la firma original de WordPress.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $url Valor utilizado por la función esc_url_raw.
    *
    * @return mixed Resultado devuelto por la función esc_url_raw.
    */
    function esc_url_raw( $url ) { // Declara un sustituto básico alineado con la firma original de WordPress.
        return $url; // Devuelve la URL sin alteraciones porque no se evalúa sanitización real en el entorno de pruebas.
    }
}
if ( ! function_exists( 'wp_nonce_field' ) ) { // Comprueba si la función wp_nonce_field no está disponible.
    /**
    * Declara un sustituto que emula la salida HTML básica del nonce.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que emula la salida HTML básica del nonce.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $action Identificador del nonce solicitado.
    * @param mixed $name   Nombre del campo oculto que almacenará el nonce.
    * @param bool  $referer Indica si se debe incluir el campo referer adicional.
    * @param bool  $echo   Controla si el resultado se imprime directamente o se devuelve como cadena.
    *
    * @return string Resultado que representa los campos ocultos generados.
    */
    function wp_nonce_field( $action = -1, $name = '_wpnonce', $referer = true, $echo = true ) { // Declara un sustituto que emula la salida HTML básica del nonce.
        $action_value = is_scalar( $action ) ? (string) $action : ''; // Normaliza el identificador del nonce para insertarlo en el valor del campo.
        $field_name = $name ? (string) $name : '_wpnonce'; // Garantiza que exista un nombre de campo válido reutilizando el predeterminado cuando falte.
        $field  = '<input type="hidden" name="' . $field_name . '" value="nonce-' . $action_value . '" />'; // Construye el campo oculto principal simulando el comportamiento de WordPress.
        if ( $referer ) { // Comprueba si se debe añadir el campo referer.
            $field .= '<input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php" />'; // Añade un campo referer consistente para las pruebas automatizadas.
        }
        if ( $echo ) { // Comprueba si el resultado debe imprimirse directamente.
            echo $field; // Imprime el marcado generado para imitar el comportamiento original.
        }
        return $field; // Devuelve el marcado para los escenarios que necesitan manipularlo manualmente.
    }
}
if ( ! function_exists( 'disabled' ) ) { // Comprueba si la función disabled no está disponible en el entorno de pruebas.
    /**
    * Declara un sustituto que replica la firma de WordPress para imprimir el atributo disabled.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que replica la firma de WordPress para imprimir el atributo disabled.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $disabled Valor que determina si se debe marcar el atributo.
    * @param mixed $current  Valor de comparación utilizado internamente por WordPress.
    * @param bool  $echo     Indica si el atributo se imprime directamente o se devuelve como cadena.
    *
    * @return string Resultado que contiene el atributo disabled cuando procede.
    */
    function disabled( $disabled, $current = true, $echo = true ) { // Declara un sustituto que replica la firma de WordPress para imprimir el atributo disabled.
        $should_disable = ( $disabled == $current ); // Evalúa si los valores coinciden para determinar si debe aplicarse el atributo.
        $attribute = $should_disable ? ' disabled="disabled"' : ''; // Construye el atributo únicamente cuando los valores coinciden.
        if ( $echo ) { // Comprueba si el resultado debe imprimirse directamente.
            echo $attribute; // Imprime el atributo simulando el comportamiento de WordPress.
        }
        return $attribute; // Devuelve el atributo para los escenarios que requieren manipularlo manualmente.
    }
}
if ( ! function_exists( 'wp_reset_postdata' ) ) { // Comprueba si la función wp_reset_postdata no existe en el entorno.
    /**
    * Declara la función ficticia utilizada tras consultas manuales.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función ficticia utilizada tras consultas manuales.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función wp_reset_postdata.
    */
    function wp_reset_postdata() { // Declara la función ficticia utilizada tras consultas manuales.
        return true; // Devuelve verdadero sin realizar operaciones, suficiente para las pruebas unitarias.
    }
}
if ( ! defined( 'ARRAY_A' ) ) { // Comprueba si la constante ARRAY_A no está definida.
    define( 'ARRAY_A', 'ARRAY_A' ); // Define la constante con un valor simbólico para las llamadas a $wpdb.
}
if ( ! function_exists( 'wp_unslash' ) ) { // Comprueba si wp_unslash no existe.
    /**
    * Declara la función de utilidad.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función de utilidad.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $value Valor utilizado por la función wp_unslash.
    *
    * @return mixed Resultado devuelto por la función wp_unslash.
    */
    function wp_unslash( $value ) { // Declara la función de utilidad.
        return $value; // Devuelve el valor original sin modificaciones.
    }
}
if ( ! function_exists( 'sanitize_text_field' ) ) { // Comprueba si sanitize_text_field no está disponible.
    /**
    * Declara la función simplificada.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función simplificada.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $value Valor utilizado por la función sanitize_text_field.
    *
    * @return mixed Resultado devuelto por la función sanitize_text_field.
    */
    function sanitize_text_field( $value ) { // Declara la función simplificada.
        return is_string( $value ) ? $value : (string) $value; // Convierte valores a cadena para simular la sanitización básica.
    }
}
if ( ! function_exists( 'sanitize_key' ) ) { // Comprueba si sanitize_key no existe en el entorno de pruebas.
    /**
    * Declara una versión simplificada de la función de WordPress.
    *
    * Explicación:
    * - Resume la tarea principal: Declara una versión simplificada de la función de WordPress.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $key Valor utilizado por la función sanitize_key.
    *
    * @return mixed Resultado devuelto por la función sanitize_key.
    */
    function sanitize_key( $key ) { // Declara una versión simplificada de la función de WordPress.
        $key = strtolower( (string) $key ); // Convierte la entrada a minúsculas para seguir la convención habitual.
        return preg_replace( '/[^a-z0-9_]/', '', $key ); // Elimina cualquier carácter que no sea alfanumérico o guion bajo.
    }
}
if ( ! function_exists( 'check_admin_referer' ) ) { // Comprueba si la función check_admin_referer no existe en el entorno de pruebas.
    /**
    * Declara la función ficticia responsable de validar peticiones en el administrador.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función ficticia responsable de validar peticiones en el administrador.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $action Valor utilizado por la función check_admin_referer.
    *
    * @return mixed Resultado devuelto por la función check_admin_referer.
    */
    function check_admin_referer( $action = -1 ) { // Declara la función ficticia responsable de validar peticiones en el administrador.
        $GLOBALS['cf7_option_limiter_last_nonce_action'] = $action; // Registra la acción del nonce para poder verificarla en las aserciones.
        if ( ! empty( $GLOBALS['cf7_option_limiter_nonce_should_fail'] ) ) { // Comprueba si las pruebas solicitaron forzar un fallo en la validación.
            throw new RuntimeException( 'nonce_failure' ); // Simula el corte de ejecución que realizaría WordPress cuando el nonce no coincide.
        }
        return true; // Acepta la validación cuando no se solicitó un fallo explícito.
    }
}
if ( ! function_exists( 'current_user_can' ) ) { // Comprueba si current_user_can no existe.
    /**
    * Declara la función stub.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función stub.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $capability Valor utilizado por la función current_user_can.
    *
    * @return mixed Resultado devuelto por la función current_user_can.
    */
    function current_user_can( $capability = '' ) { // Declara la función stub.
        $GLOBALS['cf7_option_limiter_last_capability'] = $capability; // Registra la capacidad solicitada para que las pruebas puedan inspeccionarla.
        if ( 'activate_plugins' === $capability && isset( $GLOBALS['cf7_option_limiter_capability_result'] ) ) { // Comprueba si las pruebas definieron un resultado explícito para la capacidad clave.
            return (bool) $GLOBALS['cf7_option_limiter_capability_result']; // Devuelve el resultado configurado para simular usuarios con o sin permisos.
        }
        return true; // Concede el resto de capacidades por defecto para no interferir con otras comprobaciones.
    }
}
if ( ! function_exists( 'check_ajax_referer' ) ) { // Comprueba si check_ajax_referer no existe.
    /**
    * Declara un sustituto vacío.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto vacío.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función check_ajax_referer.
    */
    function check_ajax_referer() { // Declara un sustituto vacío.
        return true; // Acepta todas las peticiones en pruebas.
    }
}
if ( ! function_exists( 'is_admin' ) ) { // Comprueba si la función is_admin no está disponible.
    /**
    * Declara la función simplificada utilizada en el panel incrustado.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función simplificada utilizada en el panel incrustado.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función is_admin.
    */
    function is_admin() { // Declara la función simplificada utilizada en el panel incrustado.
        if ( isset( $GLOBALS['cf7_option_limiter_is_admin'] ) ) { // Comprueba si las pruebas han definido un contexto concreto.
            return (bool) $GLOBALS['cf7_option_limiter_is_admin']; // Devuelve el valor indicado por las pruebas para simular frontend o administración.
        }
        return true; // Indica que por defecto las pruebas se ejecutan en el administrador.
    }
}
if ( ! class_exists( 'CF7_Option_Limiter_Test_JSON_Response' ) ) { // Comprueba si la excepción personalizada para respuestas JSON ya fue declarada.
    class CF7_Option_Limiter_Test_JSON_Response extends RuntimeException { // Excepción utilizada para interceptar respuestas JSON en las pruebas.
        public $payload; // Almacena los datos devueltos por la función JSON.
        public $success; // Indica si la respuesta representa un éxito o un error.
        public $status; // Almacena el código de estado HTTP simulado.
        /**
        * Constructor que inicializa la respuesta interceptada.
        *
        * Explicación:
        * - Resume la tarea principal: Constructor que inicializa la respuesta interceptada.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $success Valor utilizado por la función __construct.
        * @param mixed $payload Valor utilizado por la función __construct.
        * @param mixed $status Valor utilizado por la función __construct.
        *
        * @return mixed Resultado devuelto por la función __construct.
        */
        public function __construct( $success, $payload, $status = 200 ) { // Constructor que inicializa la respuesta interceptada.
            parent::__construct( 'json_response', $status ); // Llama al constructor base indicando un mensaje genérico.
            $this->success = $success; // Almacena el indicador de éxito.
            $this->payload = $payload; // Almacena la carga útil devuelta por la función interceptada.
            $this->status = $status; // Almacena el código de estado asociado.
        }
    }
}
if ( ! function_exists( 'wp_send_json_success' ) ) { // Comprueba si wp_send_json_success no existe.
    /**
    * Declara la función sustituta.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función sustituta.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $data Valor utilizado por la función wp_send_json_success.
    *
    * @return mixed Resultado devuelto por la función wp_send_json_success.
    */
    function wp_send_json_success( $data ) { // Declara la función sustituta.
        throw new CF7_Option_Limiter_Test_JSON_Response( true, $data, 200 ); // Lanza una excepción para permitir que la prueba capture la respuesta sin finalizar la ejecución.
    }
}
if ( ! function_exists( 'wp_send_json_error' ) ) { // Comprueba si wp_send_json_error no existe.
    /**
    * Declara la función sustituta para los errores.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función sustituta para los errores.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $data Valor utilizado por la función wp_send_json_error.
    * @param mixed $status Valor utilizado por la función wp_send_json_error.
    *
    * @return mixed Resultado devuelto por la función wp_send_json_error.
    */
    function wp_send_json_error( $data, $status = 400 ) { // Declara la función sustituta para los errores.
        throw new CF7_Option_Limiter_Test_JSON_Response( false, $data, $status ); // Lanza una excepción que representa la respuesta de error sin detener las pruebas.
    }
}
if ( ! function_exists( 'admin_url' ) ) { // Comprueba si admin_url no existe.
    /**
    * Declara la función ficticia.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función ficticia.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $path Valor utilizado por la función admin_url.
    *
    * @return mixed Resultado devuelto por la función admin_url.
    */
    function admin_url( $path = '' ) { // Declara la función ficticia.
        return $path; // Devuelve el mismo valor recibido.
    }
}
if ( ! function_exists( 'plugin_dir_path' ) ) { // Comprueba si plugin_dir_path no existe en el entorno de pruebas.
    /**
    * Declara un sustituto que replica el comportamiento original.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que replica el comportamiento original.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $file Valor utilizado por la función plugin_dir_path.
    *
    * @return mixed Resultado devuelto por la función plugin_dir_path.
    */
    function plugin_dir_path( $file ) { // Declara un sustituto que replica el comportamiento original.
        return rtrim( dirname( $file ), '/\\' ) . '/'; // Devuelve la ruta absoluta del directorio con barra final.
    }
}
if ( ! function_exists( 'plugin_dir_url' ) ) { // Comprueba si plugin_dir_url no está disponible durante las pruebas.
    /**
    * Declara un sustituto sencillo.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto sencillo.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función plugin_dir_url.
    */
    function plugin_dir_url() { // Declara un sustituto sencillo.
        return 'http://example.com/plugin/'; // Devuelve una URL fija suficiente para pruebas lógicas.
    }
}
if ( ! function_exists( 'wp_upload_dir' ) ) { // Comprueba si wp_upload_dir no está disponible en el entorno de pruebas.
    /**
    * Declara un sustituto que imita la estructura devuelta por WordPress.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que imita la estructura devuelta por WordPress.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función wp_upload_dir.
    */
    function wp_upload_dir() { // Declara un sustituto que imita la estructura devuelta por WordPress.
        $base_dir = sys_get_temp_dir() . '/cf7-option-limiter-tests-uploads'; // Calcula un directorio temporal dedicado para los logs de prueba.
        return array( // Devuelve la estructura esperada por el plugin.
            'basedir' => $base_dir, // Directorio base de subidas simulado.
            'baseurl' => 'http://example.com/wp-content/uploads', // URL ficticia asociada al directorio temporal.
        );
    }
}
if ( ! function_exists( 'wp_mkdir_p' ) ) { // Comprueba si wp_mkdir_p no está disponible durante las pruebas.
    /**
    * Declara una versión simplificada para crear directorios anidados.
    *
    * Explicación:
    * - Resume la tarea principal: Declara una versión simplificada para crear directorios anidados.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $target Valor utilizado por la función wp_mkdir_p.
    *
    * @return mixed Resultado devuelto por la función wp_mkdir_p.
    */
    function wp_mkdir_p( $target ) { // Declara una versión simplificada para crear directorios anidados.
        if ( is_dir( $target ) ) { // Comprueba si el directorio ya existe.
            return true; // Devuelve true porque no es necesario crearlo nuevamente.
        }
        return mkdir( $target, 0777, true ); // Intenta crear el directorio y devuelve el resultado de la operación.
    }
}
if ( ! function_exists( 'dbDelta' ) ) { // Comprueba si dbDelta no está disponible en el entorno aislado.
    $GLOBALS['cf7_option_limiter_dbdelta_calls'] = array(); // Inicializa el registro de invocaciones para las pruebas.
    /**
    * Declara una implementación mínima que registre las llamadas.
    *
    * Explicación:
    * - Resume la tarea principal: Declara una implementación mínima que registre las llamadas.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $sql Valor utilizado por la función dbDelta.
    *
    * @return mixed Resultado devuelto por la función dbDelta.
    */
    function dbDelta( $sql ) { // Declara una implementación mínima que registre las llamadas.
        $GLOBALS['cf7_option_limiter_dbdelta_calls'][] = $sql; // Almacena el SQL recibido para validaciones posteriores.
        return true; // Devuelve true para simular una ejecución satisfactoria.
    }
}
if ( ! function_exists( 'trailingslashit' ) ) { // Comprueba si trailingslashit no existe en el entorno reducido.
    /**
    * Declara la función que asegura la barra final.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función que asegura la barra final.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $path Valor utilizado por la función trailingslashit.
    *
    * @return mixed Resultado devuelto por la función trailingslashit.
    */
    function trailingslashit( $path ) { // Declara la función que asegura la barra final.
        return rtrim( $path, '/\\' ) . '/'; // Elimina barras sobrantes y añade exactamente una al final.
    }
}
if ( ! function_exists( 'plugin_basename' ) ) { // Comprueba si plugin_basename no está disponible.
    /**
    * Declara el sustituto simplificado.
    *
    * Explicación:
    * - Resume la tarea principal: Declara el sustituto simplificado.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $file Valor utilizado por la función plugin_basename.
    *
    * @return mixed Resultado devuelto por la función plugin_basename.
    */
    function plugin_basename( $file ) { // Declara el sustituto simplificado.
        return basename( $file ); // Devuelve el nombre del archivo para reproducir el identificador del plugin.
    }
}
if ( ! function_exists( 'wp_register_style' ) ) { // Comprueba si wp_register_style no existe.
    $GLOBALS['cf7_option_limiter_registered_styles'] = array(); // Inicializa el registro de estilos simulados.
    /**
    * Declara un sustituto que almacena la información recibida.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que almacena la información recibida.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $handle Valor utilizado por la función wp_register_style.
    * @param mixed $src Valor utilizado por la función wp_register_style.
    * @param mixed $deps Valor utilizado por la función wp_register_style.
    *
    * @return mixed Resultado devuelto por la función wp_register_style.
    */
    function wp_register_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) { // Declara un sustituto que almacena la información recibida.
        $GLOBALS['cf7_option_limiter_registered_styles'][ $handle ] = array( // Guarda los metadatos del estilo registrado.
            'src'   => $src, // Ruta del archivo CSS registrado.
            'deps'  => $deps, // Dependencias declaradas para el estilo.
            'ver'   => $ver, // Versión proporcionada para control de caché.
            'media' => $media, // Medio declarado para el estilo.
        );
        return true; // Devuelve true para mantener el flujo de ejecución durante las pruebas.
    }
}
if ( ! function_exists( 'wp_register_script' ) ) { // Comprueba si wp_register_script no existe.
    $GLOBALS['cf7_option_limiter_registered_scripts'] = array(); // Inicializa el registro de scripts simulados.
    /**
    * Declara un sustituto que almacena los parámetros recibidos.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que almacena los parámetros recibidos.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $handle Valor utilizado por la función wp_register_script.
    * @param mixed $src Valor utilizado por la función wp_register_script.
    * @param mixed $deps Valor utilizado por la función wp_register_script.
    *
    * @return mixed Resultado devuelto por la función wp_register_script.
    */
    function wp_register_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) { // Declara un sustituto que almacena los parámetros recibidos.
        $GLOBALS['cf7_option_limiter_registered_scripts'][ $handle ] = array( // Guarda los metadatos del script registrado.
            'src'       => $src, // Ruta al archivo JavaScript registrado.
            'deps'      => $deps, // Dependencias declaradas para el script.
            'ver'       => $ver, // Versión proporcionada para control de caché.
            'in_footer' => $in_footer, // Indicador de carga en el pie de página.
        );
        return true; // Devuelve true para mantener el flujo de ejecución en las pruebas.
    }
}
if ( ! function_exists( 'wp_enqueue_style' ) ) { // Comprueba si wp_enqueue_style no existe.
    $GLOBALS['cf7_option_limiter_enqueued_styles'] = array(); // Inicializa la lista de estilos encolados durante las pruebas.
    /**
    * Declara un sustituto que registra cada encolado.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que registra cada encolado.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $handle Valor utilizado por la función wp_enqueue_style.
    *
    * @return mixed Resultado devuelto por la función wp_enqueue_style.
    */
    function wp_enqueue_style( $handle ) { // Declara un sustituto que registra cada encolado.
        $GLOBALS['cf7_option_limiter_enqueued_styles'][] = $handle; // Añade el identificador del estilo encolado.
        return true; // Devuelve true para simular un encolado satisfactorio.
    }
}
if ( ! function_exists( 'wp_enqueue_script' ) ) { // Comprueba si wp_enqueue_script no existe.
    $GLOBALS['cf7_option_limiter_enqueued_scripts'] = array(); // Inicializa la lista de scripts encolados.
    /**
    * Declara el sustituto que registra los scripts encolados.
    *
    * Explicación:
    * - Resume la tarea principal: Declara el sustituto que registra los scripts encolados.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $handle Valor utilizado por la función wp_enqueue_script.
    *
    * @return mixed Resultado devuelto por la función wp_enqueue_script.
    */
    function wp_enqueue_script( $handle ) { // Declara el sustituto que registra los scripts encolados.
        $GLOBALS['cf7_option_limiter_enqueued_scripts'][] = $handle; // Añade el identificador del script encolado.
        return true; // Devuelve true para mantener el flujo esperado durante las pruebas.
    }
}
if ( ! function_exists( 'wp_localize_script' ) ) { // Comprueba si wp_localize_script no existe.
    $GLOBALS['cf7_option_limiter_localized_scripts'] = array(); // Inicializa el registro de localizaciones simuladas.
    /**
    * Declara un sustituto que almacena la configuración localizada.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que almacena la configuración localizada.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $handle Valor utilizado por la función wp_localize_script.
    * @param mixed $name Valor utilizado por la función wp_localize_script.
    * @param mixed $data Valor utilizado por la función wp_localize_script.
    *
    * @return mixed Resultado devuelto por la función wp_localize_script.
    */
    function wp_localize_script( $handle, $name, $data ) { // Declara un sustituto que almacena la configuración localizada.
        $GLOBALS['cf7_option_limiter_localized_scripts'][ $handle ] = array( // Guarda el nombre del objeto y los datos asociados.
            'name' => $name, // Identificador del objeto JavaScript creado.
            'data' => $data, // Datos expuestos al script desde PHP.
        );
        return true; // Devuelve true para indicar que la localización simulada se realizó.
    }
}
if ( ! function_exists( 'wp_style_is' ) ) { // Comprueba si wp_style_is no existe.
    /**
    * Declara un sustituto que consulta los estilos registrados o encolados.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que consulta los estilos registrados o encolados.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $handle Valor utilizado por la función wp_style_is.
    * @param mixed $list Valor utilizado por la función wp_style_is.
    *
    * @return mixed Resultado devuelto por la función wp_style_is.
    */
    function wp_style_is( $handle, $list = 'enqueued' ) { // Declara un sustituto que consulta los estilos registrados o encolados.
        $registered = isset( $GLOBALS['cf7_option_limiter_registered_styles'] ) ? $GLOBALS['cf7_option_limiter_registered_styles'] : array(); // Recupera el registro de estilos simulados.
        $enqueued = isset( $GLOBALS['cf7_option_limiter_enqueued_styles'] ) ? $GLOBALS['cf7_option_limiter_enqueued_styles'] : array(); // Recupera la lista de estilos encolados.
        if ( 'registered' === $list ) { // Comprueba si se consulta la lista de registros.
            return isset( $registered[ $handle ] ); // Devuelve true cuando el estilo se registró previamente.
        }
        return in_array( $handle, $enqueued, true ); // Devuelve true cuando el estilo figura como encolado.
    }
}
if ( ! function_exists( 'wp_script_is' ) ) { // Comprueba si wp_script_is no existe.
    /**
    * Declara un sustituto que consulta los scripts registrados o encolados.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que consulta los scripts registrados o encolados.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $handle Valor utilizado por la función wp_script_is.
    * @param mixed $list Valor utilizado por la función wp_script_is.
    *
    * @return mixed Resultado devuelto por la función wp_script_is.
    */
    function wp_script_is( $handle, $list = 'enqueued' ) { // Declara un sustituto que consulta los scripts registrados o encolados.
        $registered = isset( $GLOBALS['cf7_option_limiter_registered_scripts'] ) ? $GLOBALS['cf7_option_limiter_registered_scripts'] : array(); // Recupera el registro de scripts simulados.
        $enqueued = isset( $GLOBALS['cf7_option_limiter_enqueued_scripts'] ) ? $GLOBALS['cf7_option_limiter_enqueued_scripts'] : array(); // Recupera la lista de scripts encolados.
        if ( 'registered' === $list ) { // Comprueba si se solicita la lista de registros.
            return isset( $registered[ $handle ] ); // Devuelve true cuando el script figura como registrado.
        }
        return in_array( $handle, $enqueued, true ); // Devuelve true cuando el script ya se encoló.
    }
}
if ( ! function_exists( 'wp_json_encode' ) ) { // Comprueba si wp_json_encode no está disponible en el entorno de pruebas.
    /**
    * Declara un sustituto compatible con la firma original.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto compatible con la firma original.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $data Valor utilizado por la función wp_json_encode.
    * @param mixed $options Valor utilizado por la función wp_json_encode.
    * @param mixed $depth Valor utilizado por la función wp_json_encode.
    *
    * @return mixed Resultado devuelto por la función wp_json_encode.
    */
    function wp_json_encode( $data, $options = 0, $depth = 512 ) { // Declara un sustituto compatible con la firma original.
        return json_encode( $data, $options, $depth ); // Utiliza json_encode para replicar el comportamiento básico.
    }
}
if ( ! function_exists( 'add_submenu_page' ) ) { // Comprueba si add_submenu_page no existe.
    /**
    * Declara una versión sin efectos.
    *
    * Explicación:
    * - Resume la tarea principal: Declara una versión sin efectos.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función add_submenu_page.
    */
    function add_submenu_page() { // Declara una versión sin efectos.
        return true; // Devuelve true porque no es necesario registrar menús reales en las pruebas.
    }
}
if ( ! function_exists( 'selected' ) ) { // Comprueba si la función selected no existe.
    /**
    * Declara un sustituto compatible con la firma de WordPress.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto compatible con la firma de WordPress.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $selected Valor utilizado por la función selected.
    * @param mixed $current Valor utilizado por la función selected.
    * @param mixed $echo Valor utilizado por la función selected.
    *
    * @return mixed Resultado devuelto por la función selected.
    */
    function selected( $selected, $current, $echo = true ) { // Declara un sustituto compatible con la firma de WordPress.
        $result = ( (string) $selected === (string) $current ) ? ' selected="selected"' : ''; // Calcula el atributo cuando los valores coinciden.
        if ( $echo ) { // Comprueba si se debe imprimir el resultado directamente.
            echo $result; // Imprime el atributo simulado para mantener el comportamiento por defecto.
        }
        return $result; // Devuelve el atributo para permitir comprobaciones adicionales en las pruebas.
    }
}
if ( ! function_exists( 'checked' ) ) { // Comprueba si la función checked no existe en el entorno reducido.
    /**
    * Declara el sustituto compatible con la firma original.
    *
    * Explicación:
    * - Resume la tarea principal: Declara el sustituto compatible con la firma original.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $checked Valor utilizado por la función checked.
    * @param mixed $current Valor utilizado por la función checked.
    * @param mixed $echo Valor utilizado por la función checked.
    *
    * @return mixed Resultado devuelto por la función checked.
    */
    function checked( $checked, $current = true, $echo = true ) { // Declara el sustituto compatible con la firma original.
        $result = ( (bool) $checked === (bool) $current ) ? ' checked="checked"' : ''; // Calcula el atributo checked cuando los valores coinciden.
        if ( $echo ) { // Verifica si se debe imprimir el resultado directamente.
            echo $result; // Imprime el atributo para reproducir el comportamiento por defecto de WordPress.
        }
        return $result; // Devuelve el atributo calculado para permitir comprobaciones adicionales.
    }
}
if ( ! function_exists( 'add_action' ) ) { // Comprueba si add_action no existe en el entorno de pruebas.
    $GLOBALS['cf7_option_limiter_actions'] = array(); // Inicializa el registro global de acciones simuladas.
    /**
    * Declara la función stub.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función stub.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $hook Valor utilizado por la función add_action.
    * @param mixed $callback Valor utilizado por la función add_action.
    * @param mixed $priority Valor utilizado por la función add_action.
    * @param mixed $accepted_args Valor utilizado por la función add_action.
    *
    * @return mixed Resultado devuelto por la función add_action.
    */
    function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { // Declara la función stub.
        $GLOBALS['cf7_option_limiter_actions'][] = array( // Almacena la acción registrada para posibles verificaciones.
            'hook'          => $hook, // Nombre del hook asociado.
            'callback'      => $callback, // Callback proporcionado.
            'priority'      => $priority, // Prioridad indicada.
            'accepted_args' => $accepted_args, // Número de argumentos aceptados.
        );
        return true; // Devuelve true para mantener la compatibilidad con el flujo habitual de WordPress.
    }
}
if ( ! function_exists( 'do_action' ) ) { // Comprueba si do_action no existe en el entorno de pruebas.
    /**
    * Declara un stub sin efectos que permita ejecutar hooks sin dependencia de WordPress.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un stub sin efectos que permita ejecutar hooks sin dependencia de WordPress.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función do_action.
    */
    function do_action() { // Declara un stub sin efectos que permita ejecutar hooks sin dependencia de WordPress.
        return true; // No realiza ninguna operación adicional.
    }
}
if ( ! function_exists( 'add_filter' ) ) { // Comprueba si add_filter no existe.
    /**
    * Declara la función stub.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función stub.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $hook Valor utilizado por la función add_filter.
    * @param mixed $callback Valor utilizado por la función add_filter.
    * @param mixed $priority Valor utilizado por la función add_filter.
    * @param mixed $accepted_args Valor utilizado por la función add_filter.
    *
    * @return mixed Resultado devuelto por la función add_filter.
    */
    function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) { // Declara la función stub.
        add_action( $hook, $callback, $priority, $accepted_args ); // Reutiliza la lógica de add_action para almacenar el registro.
        return true; // Devuelve true para mantener el flujo sin interrupciones.
    }
}
if ( ! function_exists( 'register_activation_hook' ) ) { // Comprueba si register_activation_hook no existe.
    $GLOBALS['cf7_option_limiter_activation_callback'] = null; // Inicializa el almacenamiento del callback de activación.
    /**
    * Declara la función stub para capturar el callback.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función stub para capturar el callback.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $file Valor utilizado por la función register_activation_hook.
    * @param mixed $callback Valor utilizado por la función register_activation_hook.
    *
    * @return mixed Resultado devuelto por la función register_activation_hook.
    */
    function register_activation_hook( $file, $callback ) { // Declara la función stub para capturar el callback.
        $GLOBALS['cf7_option_limiter_activation_callback'] = $callback; // Guarda el callback para invocarlo en las pruebas.
    }
}
if ( ! function_exists( 'register_deactivation_hook' ) ) { // Comprueba si register_deactivation_hook no existe.
    /**
    * Declara un stub sin operaciones.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un stub sin operaciones.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función register_deactivation_hook.
    */
    function register_deactivation_hook() { // Declara un stub sin operaciones.
        return true; // Devuelve true para cumplir con la firma esperada.
    }
}
if ( ! function_exists( 'register_uninstall_hook' ) ) { // Comprueba si register_uninstall_hook no existe.
    /**
    * Declara un stub sin operaciones.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un stub sin operaciones.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función register_uninstall_hook.
    */
    function register_uninstall_hook() { // Declara un stub sin operaciones.
        return true; // Devuelve true manteniendo la compatibilidad con el flujo del plugin.
    }
}
if ( ! function_exists( 'did_action' ) ) { // Comprueba si did_action no existe.
    $GLOBALS['cf7_option_limiter_did_actions'] = array(); // Inicializa el seguimiento de hooks ejecutados.
    /**
    * Declara la función stub que consulta el seguimiento.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función stub que consulta el seguimiento.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $hook Valor utilizado por la función did_action.
    *
    * @return mixed Resultado devuelto por la función did_action.
    */
    function did_action( $hook ) { // Declara la función stub que consulta el seguimiento.
        return ! empty( $GLOBALS['cf7_option_limiter_did_actions'][ $hook ] ); // Devuelve true si se marcó el hook como ejecutado.
    }
}
if ( ! function_exists( 'wp_create_nonce' ) ) { // Comprueba si wp_create_nonce no existe.
    /**
    * Declara el sustituto.
    *
    * Explicación:
    * - Resume la tarea principal: Declara el sustituto.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función wp_create_nonce.
    */
    function wp_create_nonce() { // Declara el sustituto.
        return 'nonce'; // Devuelve un valor fijo para las pruebas.
    }
}
if ( ! function_exists( 'add_query_arg' ) ) { // Comprueba si add_query_arg no existe.
    /**
    * Declara el sustituto.
    *
    * Explicación:
    * - Resume la tarea principal: Declara el sustituto.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $args Valor utilizado por la función add_query_arg.
    * @param mixed $url Valor utilizado por la función add_query_arg.
    *
    * @return mixed Resultado devuelto por la función add_query_arg.
    */
    function add_query_arg( $args, $url ) { // Declara el sustituto.
        return $url . '?' . http_build_query( $args ); // Construye una URL simple basada en los argumentos.
    }
}
if ( ! class_exists( 'CF7_Option_Limiter_Test_Redirect' ) ) { // Comprueba si la excepción personalizada aún no se ha declarado.
    class CF7_Option_Limiter_Test_Redirect extends RuntimeException { // Declara una excepción para interceptar redirecciones durante las pruebas.
    }
}
if ( ! function_exists( 'wp_safe_redirect' ) ) { // Comprueba si wp_safe_redirect no existe.
    /**
    * Declara la función ficticia que captura redirecciones.
    *
    * Explicación:
    * - Resume la tarea principal: Declara la función ficticia que captura redirecciones.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $url Valor utilizado por la función wp_safe_redirect.
    *
    * @return mixed Resultado devuelto por la función wp_safe_redirect.
    */
    function wp_safe_redirect( $url ) { // Declara la función ficticia que captura redirecciones.
        $GLOBALS['cf7_option_limiter_last_redirect'] = $url; // Almacena la URL solicitada para poder inspeccionarla en las aserciones.
        throw new CF7_Option_Limiter_Test_Redirect( $url ); // Lanza una excepción para detener la ejecución en las pruebas sin finalizar el proceso completo.
    }
}
if ( ! defined( 'HOUR_IN_SECONDS' ) ) { // Comprueba si la constante de una hora en segundos no está definida.
    define( 'HOUR_IN_SECONDS', 3600 ); // Define el valor estándar de una hora en segundos utilizado por WordPress.
}
if ( ! function_exists( 'update_option' ) ) { // Comprueba si update_option no existe.
    $GLOBALS['cf7_option_limiter_fake_options'] = array(); // Inicializa el almacenamiento de opciones simulado.
    /**
    * Declara el sustituto que actualiza una opción.
    *
    * Explicación:
    * - Resume la tarea principal: Declara el sustituto que actualiza una opción.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $name Valor utilizado por la función update_option.
    * @param mixed $value Valor utilizado por la función update_option.
    *
    * @return mixed Resultado devuelto por la función update_option.
    */
    function update_option( $name, $value ) { // Declara el sustituto que actualiza una opción.
        $GLOBALS['cf7_option_limiter_fake_options'][ $name ] = $value; // Almacena el valor para consultas posteriores.
        return true; // Devuelve true para simular una actualización correcta.
    }
}
update_option( 'cf7_option_limiter_schema_version', '0.9.0' ); // Preconfigura una versión de esquema antigua para comprobar que las migraciones se ejecutan durante las pruebas.
if ( ! function_exists( 'current_time' ) ) { // Comprueba si la función current_time no existe en el entorno de pruebas.
    /**
    * Declara un sustituto sencillo de current_time.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto sencillo de current_time.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $type Valor utilizado por la función current_time.
    *
    * @return mixed Resultado devuelto por la función current_time.
    */
    function current_time( $type ) { // Declara un sustituto sencillo de current_time.
        if ( 'timestamp' === $type ) { // Verifica si se solicita el valor como marca temporal numérica.
            return time(); // Devuelve la hora actual del sistema como entero.
        }
        return gmdate( 'Y-m-d H:i:s' ); // Devuelve la fecha y hora en formato MySQL estándar sin desfase horario.
    }
}
if ( ! function_exists( 'get_option' ) ) { // Comprueba si la función get_option no está disponible.
    /**
    * Declara un sustituto mínimo de get_option.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto mínimo de get_option.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $name Valor utilizado por la función get_option.
    *
    * @return mixed Resultado devuelto por la función get_option.
    */
    function get_option( $name ) { // Declara un sustituto mínimo de get_option.
        if ( 'gmt_offset' === $name ) { // Comprueba si se solicita el desfase horario.
            return isset( $GLOBALS['cf7_option_limiter_fake_options']['gmt_offset'] ) ? (float) $GLOBALS['cf7_option_limiter_fake_options']['gmt_offset'] : 0.0; // Devuelve el desfase configurado en las pruebas o cero cuando no se especifica.
        }
        return isset( $GLOBALS['cf7_option_limiter_fake_options'][ $name ] ) ? $GLOBALS['cf7_option_limiter_fake_options'][ $name ] : null; // Devuelve el valor almacenado o null si no existe.
    }
}
if ( ! function_exists( 'wp_timezone' ) ) { // Comprueba si wp_timezone no está disponible en el entorno de pruebas.
    /**
    * Declara un sustituto que replica la resolución de zona horaria de WordPress.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que replica la resolución de zona horaria de WordPress.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return DateTimeZone Zona horaria calculada a partir de las opciones almacenadas.
    */
    function wp_timezone() { // Declara un sustituto que replica la resolución de zona horaria de WordPress.
        $timezone_string = isset( $GLOBALS['cf7_option_limiter_fake_options']['timezone_string'] ) ? (string) $GLOBALS['cf7_option_limiter_fake_options']['timezone_string'] : ''; // Recupera la cadena de zona horaria si se definió explícitamente.
        if ( '' !== $timezone_string ) { // Comprueba si existe un identificador de zona horaria completo.
            try { // Intenta instanciar la zona horaria indicada directamente.
                return new DateTimeZone( $timezone_string ); // Devuelve la zona horaria configurada explícitamente.
            } catch ( Exception $exception ) { // Intercepta errores cuando la cadena no es válida.
                // Se ignora la excepción para continuar con el cálculo basado en gmt_offset. // Mantiene la compatibilidad con escenarios que sólo ajustan el desfase numérico.
            }
        }
        $offset_hours = get_option( 'gmt_offset' ); // Recupera el desfase en horas para construir la zona horaria.
        if ( ! is_numeric( $offset_hours ) ) { // Comprueba que el valor sea numérico antes de calcular la zona horaria.
            return new DateTimeZone( 'UTC' ); // Devuelve UTC cuando no hay datos fiables disponibles.
        }
        $offset_seconds = (int) round( (float) $offset_hours * HOUR_IN_SECONDS ); // Convierte el desfase de horas a segundos completos.
        $timezone_name = timezone_name_from_abbr( '', $offset_seconds, 0 ); // Solicita un identificador de zona horaria basado en el desfase calculado.
        if ( false !== $timezone_name ) { // Comprueba si se resolvió un identificador válido.
            return new DateTimeZone( $timezone_name ); // Devuelve la zona horaria asociada al desfase horario.
        }
        return new DateTimeZone( 'UTC' ); // Utiliza UTC como reserva cuando no se pudo resolver el identificador.
    }
}
if ( ! function_exists( 'delete_option' ) ) { // Comprueba si delete_option no existe.
    /**
    * Declara el sustituto que elimina una opción almacenada.
    *
    * Explicación:
    * - Resume la tarea principal: Declara el sustituto que elimina una opción almacenada.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $name Valor utilizado por la función delete_option.
    *
    * @return mixed Resultado devuelto por la función delete_option.
    */
    function delete_option( $name ) { // Declara el sustituto que elimina una opción almacenada.
        unset( $GLOBALS['cf7_option_limiter_fake_options'][ $name ] ); // Elimina la opción simulada.
        return true; // Devuelve true para indicar que la eliminación se realizó.
    }
}
if ( ! function_exists( 'wp_die' ) ) { // Comprueba si wp_die no existe.
    /**
    * Declara el sustituto.
    *
    * Explicación:
    * - Resume la tarea principal: Declara el sustituto.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param mixed $message Valor utilizado por la función wp_die.
    *
    * @return mixed Resultado devuelto por la función wp_die.
    */
    function wp_die( $message ) { // Declara el sustituto.
        throw new RuntimeException( $message ); // Lanza una excepción para detener la ejecución durante las pruebas.
    }
}
if ( ! function_exists( 'wpcf7_get_current_contact_form' ) ) { // Comprueba si la función auxiliar de Contact Form 7 no existe.
    /**
    * Declara un sustituto que devuelve null.
    *
    * Explicación:
    * - Resume la tarea principal: Declara un sustituto que devuelve null.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return mixed Resultado devuelto por la función wpcf7_get_current_contact_form.
    */
    function wpcf7_get_current_contact_form() { // Declara un sustituto que devuelve null.
        return null; // No se simula un formulario activo fuera de las sumisiones configuradas manualmente.
    }
}
if ( ! class_exists( 'WPCF7_Submission' ) ) { // Comprueba si la clase de sumisión de Contact Form 7 no está disponible.
    class WPCF7_Submission { // Declara una versión simplificada de la clase de sumisión.
        public static $instance = null; // Propiedad estática donde se almacenará la instancia simulada.
        public $posted_data = array(); // Datos enviados en la sumisión simulada.
        public $form = null; // Referencia al formulario asociado a la sumisión.
        public static function get_instance() { // Método estático que Contact Form 7 utiliza para recuperar la sumisión actual.
            return self::$instance; // Devuelve la instancia configurada manualmente en las pruebas.
        }
        /**
        * Devuelve los datos enviados en el formulario.
        *
        * Explicación:
        * - Resume la tarea principal: Devuelve los datos enviados en el formulario.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @return mixed Resultado devuelto por la función get_posted_data.
        */
        public function get_posted_data() { // Devuelve los datos enviados en el formulario.
            return $this->posted_data; // Retorna el arreglo asignado desde la prueba.
        }
        /**
        * Devuelve el formulario asociado a la sumisión.
        *
        * Explicación:
        * - Resume la tarea principal: Devuelve el formulario asociado a la sumisión.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @return mixed Resultado devuelto por la función get_contact_form.
        */
        public function get_contact_form() { // Devuelve el formulario asociado a la sumisión.
            return $this->form; // Retorna el formulario simulado.
        }
    }
}
if ( ! class_exists( 'WPCF7_ContactForm' ) ) { // Comprueba si la clase de formulario de Contact Form 7 está ausente.
    class WPCF7_ContactForm { // Declara una versión simplificada del formulario.
        protected $identifier; // Almacena el identificador numérico del formulario.
        /**
        * Constructor que recibe el identificador del formulario.
        *
        * Explicación:
        * - Resume la tarea principal: Constructor que recibe el identificador del formulario.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $id Valor utilizado por la función __construct.
        *
        * @return mixed Resultado devuelto por la función __construct.
        */
        public function __construct( $id ) { // Constructor que recibe el identificador del formulario.
            $this->identifier = $id; // Asigna el identificador recibido a la propiedad interna.
        }
        /**
        * Método que devuelve el identificador como haría la clase real.
        *
        * Explicación:
        * - Resume la tarea principal: Método que devuelve el identificador como haría la clase real.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @return mixed Resultado devuelto por la función id.
        */
        public function id() { // Método que devuelve el identificador como haría la clase real.
            return $this->identifier; // Retorna el identificador asignado.
        }
    }
}
if ( ! class_exists( 'WPCF7_ValidationResult' ) ) { // Comprueba si la clase de resultado de validación no está disponible.
    class WPCF7_ValidationResult { // Declara un sustituto compatible con el método invalidate.
        public $invalid = array(); // Almacena la lista de campos marcados como inválidos.
        /**
        * Replica el método que Contact Form 7 utiliza para marcar errores.
        *
        * Explicación:
        * - Resume la tarea principal: Replica el método que Contact Form 7 utiliza para marcar errores.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $tag Valor utilizado por la función invalidate.
        * @param mixed $message Valor utilizado por la función invalidate.
        *
        * @return mixed Resultado devuelto por la función invalidate.
        */
        public function invalidate( $tag, $message ) { // Replica el método que Contact Form 7 utiliza para marcar errores.
            $this->invalid[] = array( 'tag' => $tag, 'message' => $message ); // Registra el campo y el mensaje recibidos.
        }
    }
}
if ( ! class_exists( 'WP_Query' ) ) { // Comprueba si la clase WP_Query no está disponible.
    class WP_Query { // Declara una versión simplificada de WP_Query para las pruebas.
        public $posts = array(); // Propiedad pública que almacenará los posts simulados.
        public static $mock_posts = array(); // Propiedad estática para permitir la configuración externa de resultados.
        /**
        * Constructor que ignora los argumentos recibidos.
        *
        * Explicación:
        * - Resume la tarea principal: Constructor que ignora los argumentos recibidos.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $args Valor utilizado por la función __construct.
        *
        * @return mixed Resultado devuelto por la función __construct.
        */
        public function __construct( $args = array() ) { // Constructor que ignora los argumentos recibidos.
            $this->posts = self::$mock_posts; // Copia los posts simulados para que el plugin pueda iterar sobre ellos.
        }
    }
}
if ( ! class_exists( 'CF7_Option_Limiter_Test_WPDB' ) ) { // Comprueba si la clase de base de datos simulada no existe.
    /**
    * Sustituto íntegramente comentado del objeto $wpdb para las pruebas unitarias.
    *
    * Explicación:
    * - Resume la tarea principal: Sustituto íntegramente comentado del objeto $wpdb para las pruebas unitarias.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * Esta implementación redefine los métodos que el plugin invoca y permite
    * controlar con precisión las respuestas, de modo que podamos validar las
    * ramas de código sin depender de un entorno de WordPress completo.
    */
    class CF7_Option_Limiter_Test_WPDB { // Declara un sustituto mínimo del objeto $wpdb.
        public $prefix = 'wp_'; // Prefijo utilizado por defecto para las tablas durante las pruebas.
        public $expected_row = null; // Propiedad que almacenará el resultado por defecto devuelto en get_row.
        public $expected_rows = array(); // Arreglo asociativo para devolver filas específicas por valor consultado.
        public $expected_row_queue = array(); // Cola opcional de filas que se devolverán secuencialmente en las llamadas a get_row.
        public $expected_results = array(); // Arreglo que devolverá get_results para consultas múltiples.
        public $expected_results_map = array(); // Arreglo asociativo para devolver resultados en función de la combinación de argumentos.
        public $replace_called = false; // Indicador para detectar si se invoca replace en las pruebas.
        public $last_replace = array(); // Almacena los datos utilizados en la última operación replace simulada.
        public $insert_called = false; // Indicador para comprobar si se invoca insert en las pruebas.
        public $last_insert = array(); // Almacena los datos utilizados en la última inserción simulada.
        public $query_return_value = 1; // Valor que devolverá la función query simulando actualizaciones exitosas.
        public $last_query = ''; // Almacena la última consulta ejecutada para permitir aserciones específicas.
        public $get_var_return_value = null; // Valor de retorno configurable para get_var durante las pruebas.
        public $get_var_queue = array(); // Cola de valores que permitirá simular respuestas secuenciales en get_var.
        public $executed_queries = array(); // Registra todas las consultas ejecutadas para permitir verificaciones agrupadas.
        public $last_error = ''; // Propiedad que permitirá simular mensajes de error devueltos por la base de datos.
        public $get_row_calls = 0; // Contador que permitirá verificar cuántas veces se solicitó una fila durante una prueba concreta.

        /**
        * Replica la firma de prepare.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de prepare.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $query Valor utilizado por la función prepare.
        * @param mixed $...$args Valor utilizado por la función prepare.
        *
        * @return mixed Resultado devuelto por la función prepare.
        */
        public function prepare( $query, ...$args ) { // Replica la firma de prepare.
            return array( 'query' => $query, 'args' => $args ); // Devuelve una estructura simple ignorando la interpolación real.
        }

        /**
        * Replica la firma de get_var utilizada en comprobaciones de esquema.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de get_var utilizada en comprobaciones de esquema.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $query Valor utilizado por la función get_var.
        *
        * @return mixed Resultado devuelto por la función get_var.
        */
        public function get_var( $query = null ) { // Replica la firma de get_var utilizada en comprobaciones de esquema.
            $this->last_query = is_array( $query ) && isset( $query['query'] ) ? $query['query'] : (string) $query; // Registra la consulta recibida.
            if ( ! empty( $this->get_var_queue ) ) { // Comprueba si existe una cola de respuestas personalizadas.
                return array_shift( $this->get_var_queue ); // Devuelve el siguiente elemento de la cola permitiendo simulaciones secuenciales.
            }
            return $this->get_var_return_value; // Devuelve el valor configurado por defecto para la prueba actual.
        }

        /**
        * Replica la firma de get_row.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de get_row.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $statement Valor utilizado por la función get_row.
        * @param mixed $output Valor utilizado por la función get_row.
        *
        * @return mixed Resultado devuelto por la función get_row.
        */
        public function get_row( $statement, $output = ARRAY_A ) { // Replica la firma de get_row.
            $this->get_row_calls++; // Incrementa el contador de llamadas para permitir comprobaciones posteriores.
            if ( ! empty( $this->expected_row_queue ) ) { // Comprueba si se configuró una cola de respuestas específicas.
                return array_shift( $this->expected_row_queue ); // Devuelve el siguiente elemento de la cola ignorando argumentos.
            }
            if ( is_array( $statement ) && isset( $statement['args'] ) ) { // Comprueba si se recibieron argumentos parametrizados.
                $arguments = $statement['args']; // Recupera los argumentos utilizados en la preparación.
                $flat_arguments = array(); // Inicializa el arreglo que almacenará los argumentos en texto plano.
                foreach ( $arguments as $argument ) { // Recorre cada argumento recibido.
                    if ( is_array( $argument ) ) { // Comprueba si el argumento es un arreglo (por ejemplo, una lista de valores).
                        foreach ( $argument as $item ) { // Recorre cada elemento del arreglo.
                            $flat_arguments[] = (string) $item; // Añade cada elemento convertido en cadena.
                        }
                    } else { // Cuando el argumento es escalar.
                        $flat_arguments[] = (string) $argument; // Añade el argumento convertido en cadena.
                    }
                }
                $composite_key = implode( '|', $flat_arguments ); // Construye una clave compuesta con todos los argumentos.
                if ( array_key_exists( $composite_key, $this->expected_rows ) ) { // Comprueba si existe una fila configurada para la clave compuesta.
                    return $this->expected_rows[ $composite_key ]; // Devuelve la fila asociada a la clave compuesta.
                }
                $last_argument = end( $flat_arguments ); // Obtiene el último argumento como fallback para compatibilidad.
                if ( array_key_exists( $last_argument, $this->expected_rows ) ) { // Comprueba si existe una fila configurada para el último argumento.
                    return $this->expected_rows[ $last_argument ]; // Devuelve la fila asociada al último argumento.
                }
            }
            return $this->expected_row; // Devuelve el valor general configurado previamente o null si no se definió.
        }
      
        /**
        * Replica la firma de replace.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de replace.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $table Valor utilizado por la función replace.
        * @param mixed $data Valor utilizado por la función replace.
        *
        * @return mixed Resultado devuelto por la función replace.
        */
        public function replace( $table = '', $data = array(), $format = array() ) { // Replica la firma de replace.
            $this->replace_called = true; // Marca que se intentó ejecutar una escritura.
            $this->last_replace = array( 'table' => $table, 'data' => $data, 'format' => $format ); // Almacena los datos recibidos para futuras aserciones.
            return 1; // Devuelve uno para simular una inserción/actualización exitosa.
        }

        /**
        * Replica la firma de insert utilizada en el plugin.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de insert utilizada en el plugin.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $table Valor utilizado por la función insert.
        * @param mixed $data Valor utilizado por la función insert.
        * @param mixed $format Valor utilizado por la función insert.
        *
        * @return mixed Resultado devuelto por la función insert.
        */
        public function insert( $table, $data, $format = array() ) { // Replica la firma de insert utilizada en el plugin.
            $this->insert_called = true; // Marca que se solicitó una inserción en la prueba actual.
            $this->last_insert = array( 'table' => $table, 'data' => $data, 'format' => $format ); // Guarda los parámetros recibidos para verificarlos.
            $this->last_query  = array( 'operation' => 'insert', 'table' => $table, 'data' => $data, 'format' => $format ); // Registra la operación para posibles aserciones.
            return $this->query_return_value; // Devuelve el valor configurado simulando el resultado de la inserción.
        }
      
        /**
        * Replica la firma de get_results utilizada en otras partes del plugin.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de get_results utilizada en otras partes del plugin.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $statement Valor utilizado por la función get_results.
        * @param mixed $output Valor utilizado por la función get_results.
        *
        * @return mixed Resultado devuelto por la función get_results.
        */
        public function get_results( $statement = null, $output = ARRAY_A ) { // Replica la firma de get_results utilizada en otras partes del plugin.
            if ( ! empty( $this->expected_results_map ) && is_array( $statement ) && isset( $statement['args'] ) ) { // Comprueba si se proporcionó un mapa asociativo y argumentos para filtrar.
                $flat_arguments = array(); // Inicializa el arreglo plano de argumentos.
                foreach ( $statement['args'] as $argument ) { // Recorre cada argumento recibido.
                    if ( is_array( $argument ) ) { // Si el argumento es un arreglo se recorren sus elementos.

                        foreach ( $argument as $item ) { // Itera cada elemento del arreglo.
                            $flat_arguments[] = (string) $item; // Añade cada elemento convertido en cadena.
                        }
                    } else { // Cuando el argumento es escalar.
                        $flat_arguments[] = (string) $argument; // Añade el argumento convertido en cadena.
                    }
                }
                $composite_key = implode( '|', $flat_arguments ); // Construye la clave compuesta de la consulta.
                if ( array_key_exists( $composite_key, $this->expected_results_map ) ) { // Comprueba si existe un resultado configurado para la clave compuesta.
                    return $this->expected_results_map[ $composite_key ]; // Devuelve el conjunto de filas correspondiente a la consulta.
                }
            }
            return $this->expected_results; // Devuelve las filas configuradas previamente.
        }

        /**
        * Replica la firma de delete.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de delete.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @return mixed Resultado devuelto por la función delete.
        */
        public function delete() { // Replica la firma de delete.
            return 0; // Devuelve cero indicando que no se realizaron borrados.
        }
      
        /**
        * Replica la firma de query utilizada en incrementos.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de query utilizada en incrementos.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $query Valor utilizado por la función query.
        *
        * @return mixed Resultado devuelto por la función query.
        */
        public function query( $query = '' ) { // Replica la firma de query utilizada en incrementos.
            if ( is_array( $query ) && isset( $query['query'] ) ) { // Comprueba si se recibió el arreglo devuelto por prepare.
                $this->last_query = $query['query']; // Registra únicamente la cadena SQL para facilitar las aserciones.
            } else { // Si se recibió una cadena directa.
                $this->last_query = $query; // Registra la consulta tal cual para mantener compatibilidad con otras rutas.
            }
            $this->executed_queries[] = $this->last_query; // Almacena la consulta ejecutada para inspecciones posteriores.
            return $this->query_return_value; // Devuelve el valor configurado para simular actualizaciones exitosas o fallidas.
        }

        /**
        * Replica la firma de update.
        *
        * Explicación:
        * - Resume la tarea principal: Replica la firma de update.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        * @param mixed $table Valor utilizado por la función update.
        * @param mixed $data Valor utilizado por la función update.
        * @param mixed $where Valor utilizado por la función update.
        * @param mixed $data_format Valor utilizado por la función update.
        * @param mixed $where_format Valor utilizado por la función update.
        *
        * @return mixed Resultado devuelto por la función update.
        */
        public function update( $table, $data, $where, $data_format = null, $where_format = null ) { // Replica la firma de update.
            $this->last_query = array( // Registra los parámetros recibidos para posibles inspecciones.
                'table'        => $table, // Tabla objetivo de la actualización.
                'data'         => $data, // Datos a actualizar.
                'where'        => $where, // Condiciones de filtrado.
                'data_format'  => $data_format, // Formatos de los datos proporcionados.
                'where_format' => $where_format, // Formatos de la cláusula WHERE.
            );
            return $this->query_return_value; // Devuelve el valor configurado simulando éxito en la actualización.
        }
    }
}
global $wpdb; // Declara el objeto global utilizado por las funciones del plugin.
$wpdb = new CF7_Option_Limiter_Test_WPDB(); // Inicializa el sustituto para evitar dependencias de WordPress real.

require_once __DIR__ . '/../cf7-option-limiter.php'; // Carga el archivo principal del plugin para validar la activación.
if ( ! function_exists( 'cf7_option_limiter_bootstrap' ) ) { // Comprueba que la función de arranque se haya definido correctamente.
    throw new RuntimeException( 'cf7_option_limiter_bootstrap no está disponible tras incluir el plugin.' ); // Informa del fallo si la función no existe.
}
cf7_option_limiter_bootstrap(); // Ejecuta el arranque para inicializar subsistemas en el entorno de pruebas.
$migrationQueries = $wpdb->executed_queries; // Captura las consultas ejecutadas durante el arranque para asegurarse de que se lanzaron las migraciones automáticas.
if ( empty( $migrationQueries ) ) { // Comprueba que exista al menos una consulta asociada a las migraciones.
    throw new RuntimeException( 'La inicialización debería ejecutar las migraciones pendientes cuando la versión almacenada está desactualizada.' ); // Falla la prueba si no se ejecutó ninguna migración.
}
if ( get_option( CF7_OptionLimiter_DB::SCHEMA_VERSION_OPTION ) !== CF7_OPTION_LIMITER_VERSION ) { // Verifica que la versión de esquema almacenada se haya actualizado tras las migraciones.
    throw new RuntimeException( 'La opción de versión del esquema debería igualar a CF7_OPTION_LIMITER_VERSION después de ejecutar las migraciones.' ); // Lanza excepción cuando la versión almacenada no coincide con el código actual.
}
if ( empty( $GLOBALS['cf7_option_limiter_activation_callback'] ) || ! is_callable( $GLOBALS['cf7_option_limiter_activation_callback'] ) ) { // Comprueba que el hook de activación se haya registrado.
    throw new RuntimeException( 'El hook de activación no se registró correctamente durante las pruebas.' ); // Falla si no se registró el callback.
}
call_user_func( $GLOBALS['cf7_option_limiter_activation_callback'] ); // Invoca la activación real asegurando que no se produzcan errores fatales.
$schemaVersionAfterActivation = get_option( CF7_OptionLimiter_DB::SCHEMA_VERSION_OPTION ); // Recupera nuevamente la versión almacenada del esquema tras simular la activación.
if ( $schemaVersionAfterActivation !== CF7_OPTION_LIMITER_VERSION ) { // Comprueba que la activación mantenga la versión correcta del esquema.
    throw new RuntimeException( 'La activación debería dejar registrada la versión actual del esquema tras ejecutar las migraciones.' ); // Falla la prueba si la versión almacenada difiere de la esperada.
}
$panelHookRegistered = false; // Inicializa la bandera que confirmará el registro del panel dentro del editor de Contact Form 7.
$frontendHooksRegistered = array( // Inicializa las banderas que confirman el registro de los assets frontales en los hooks esperados.
    'wp_enqueue_scripts'    => false, // Marca si el estilo y script públicos se enganchan al ciclo general de carga del tema.
    'wpcf7_enqueue_scripts' => false, // Marca si los assets también se encolarán cuando Contact Form 7 limita su carga a páginas con formularios.
    'init_autoload'         => false, // Marca si la clase fuerza el encolado durante init para cubrir entornos donde los hooks anteriores no se ejecutan.
); // Finaliza la declaración de banderas para los hooks frontales.
foreach ( $GLOBALS['cf7_option_limiter_actions'] as $registered_action ) { // Recorre todas las acciones registradas durante el arranque.
    $hook_name = $registered_action['hook']; // Recupera el nombre del hook registrado para analizarlo.
    if ( strpos( $hook_name, 'elementor/' ) !== false ) { // Comprueba si aún quedan integraciones con Elementor registradas.
        throw new RuntimeException( 'No deberían registrarse hooks asociados a Elementor tras la limpieza de la integración.' ); // Falla la prueba al detectar un hook heredado.
    }
    if ( 'wpcf7_editor_panels' === $hook_name ) { // Comprueba si se registró el filtro que añade la pestaña personalizada.
        $callback = $registered_action['callback']; // Recupera el callback asociado al hook.
        if ( is_array( $callback ) && 'CF7_OptionLimiter_CF7_Panel' === $callback[0] && 'register_panel' === $callback[1] ) { // Verifica que el callback apunte a la clase correcta.
            $panelHookRegistered = true; // Marca que se detectó el registro esperado.
        }
    }
    if ( 'wp_enqueue_scripts' === $hook_name ) { // Comprueba si el hook corresponde al ciclo general de encolado del frontend.
        $callback = $registered_action['callback']; // Recupera el callback asociado para validar la clase y el método registrados.
        if ( is_array( $callback ) && 'CF7_OptionLimiter_Limiter' === $callback[0] && 'enqueue_front_assets' === $callback[1] ) { // Comprueba que el limitador registre sus assets en el hook global.
            $frontendHooksRegistered['wp_enqueue_scripts'] = true; // Marca que se detectó la inscripción correcta en wp_enqueue_scripts.
        }
    }
    if ( 'wpcf7_enqueue_scripts' === $hook_name ) { // Comprueba si el hook corresponde al ciclo específico de Contact Form 7.
        $callback = $registered_action['callback']; // Recupera el callback asociado para confirmar la suscripción correcta.
        if ( is_array( $callback ) && 'CF7_OptionLimiter_Limiter' === $callback[0] && 'enqueue_front_assets' === $callback[1] ) { // Verifica que la clase limite también sus assets al hook especializado de CF7.
            $frontendHooksRegistered['wpcf7_enqueue_scripts'] = true; // Marca que se detectó la inscripción en wpcf7_enqueue_scripts para cargar estilos sólo cuando hay formularios.
        }
    }
    if ( 'init' === $hook_name ) { // Comprueba si el hook pertenece a la fase init de WordPress.
        $callback = $registered_action['callback']; // Recupera el callback para validarlo.
        if ( is_array( $callback ) && 'CF7_OptionLimiter_Limiter' === $callback[0] && 'autoload_front_assets' === $callback[1] ) { // Verifica que el método de autoload se haya registrado.
            $frontendHooksRegistered['init_autoload'] = true; // Marca que se detectó la inscripción proactiva en init.
        }
    }
}
if ( ! $panelHookRegistered ) { // Comprueba que se haya detectado el registro de la pestaña incrustada.
    throw new RuntimeException( 'El filtro wpcf7_editor_panels debería registrarse para insertar la pestaña del limitador.' ); // Lanza una excepción si no se encontró el hook esperado.
}
if ( ! $frontendHooksRegistered['wp_enqueue_scripts'] ) { // Verifica que el plugin mantenga el registro del encolado frontal en el hook general.
    throw new RuntimeException( 'CF7_OptionLimiter_Limiter::init debería registrar enqueue_front_assets en wp_enqueue_scripts para cargar la hoja de estilos global.' ); // Informa si el estilo dejaría de publicarse en páginas públicas.
}
if ( ! $frontendHooksRegistered['wpcf7_enqueue_scripts'] ) { // Verifica que también se registre el hook específico de Contact Form 7.
    throw new RuntimeException( 'CF7_OptionLimiter_Limiter::init debería registrar enqueue_front_assets en wpcf7_enqueue_scripts para garantizar la carga cuando CF7 optimiza sus assets.' ); // Informa si faltara la inscripción necesaria para compatibilidad con la carga condicional de CF7.
}
if ( ! $frontendHooksRegistered['init_autoload'] ) { // Comprueba que el autoload esté registrado para asegurar la carga incluso sin los otros hooks.
    throw new RuntimeException( 'CF7_OptionLimiter_Limiter::init debería registrar autoload_front_assets en init para garantizar la disponibilidad del CSS en cualquier vista pública.' ); // Informa si faltara el hook proactivo de init.
}
$GLOBALS['cf7_option_limiter_enqueued_styles'] = array(); // Reinicia la lista de estilos encolados para comprobar el autoload.
$GLOBALS['cf7_option_limiter_enqueued_scripts'] = array(); // Reinicia la lista de scripts encolados para la misma comprobación.
$GLOBALS['cf7_option_limiter_is_admin'] = false; // Simula el contexto del frontend para ejecutar el autoload.
CF7_OptionLimiter_Limiter::autoload_front_assets(); // Ejecuta el método proactivo para verificar que encola los assets requeridos.
if ( ! in_array( 'cf7-option-limiter-frontend', $GLOBALS['cf7_option_limiter_enqueued_styles'], true ) ) { // Comprueba que el estilo público se haya encolado automáticamente.
    throw new RuntimeException( 'autoload_front_assets debería encolar la hoja de estilos frontend cuando se ejecuta en el frontend.' ); // Lanza excepción si el estilo no se añadió a la cola.
}
if ( ! in_array( 'cf7-option-limiter-frontend', $GLOBALS['cf7_option_limiter_enqueued_scripts'], true ) ) { // Comprueba que el script público también se encole automáticamente.
    throw new RuntimeException( 'autoload_front_assets debería encolar el script frontend cuando se ejecuta en el frontend.' ); // Lanza excepción si el script no se añadió a la cola.
}
$GLOBALS['cf7_option_limiter_is_admin'] = true; // Restaura el contexto administrativo predeterminado para el resto de pruebas.
unset( $GLOBALS['cf7_option_limiter_is_admin'] ); // Limpia la bandera personalizada para que el stub vuelva a su valor por defecto.

CF7_OptionLimiter_Hooks::enqueue_shared_admin_assets( 'plugins.php' ); // Encola los recursos compartidos simulando la pantalla de plugins para obtener la configuración localizada.
if ( empty( $GLOBALS['cf7_option_limiter_localized_scripts']['cf7-option-limiter-plugins'] ) ) { // Comprueba que el script específico del listado tenga datos localizados.
    throw new RuntimeException( 'enqueue_shared_admin_assets debería localizar datos para cf7-option-limiter-plugins en la pantalla de plugins.' ); // Lanza excepción si no se encontró la configuración esperada.
}
$localizedPluginsScript = $GLOBALS['cf7_option_limiter_localized_scripts']['cf7-option-limiter-plugins']; // Recupera los datos localizados asociados al script del listado de plugins.
if ( empty( $localizedPluginsScript['data']['warning'] ) ) { // Comprueba que el mensaje de advertencia se haya enviado al script.
    throw new RuntimeException( 'La localización del script de plugins debería incluir el mensaje de advertencia sobre la desactivación de reglas.' ); // Lanza excepción si la advertencia no está presente.
}
$pluginsScriptSource = file_get_contents( __DIR__ . '/../assets/plugins.js' ); // Lee el script que manipula la confirmación en el listado de plugins.
if ( false === $pluginsScriptSource ) { // Comprueba que la lectura del archivo haya sido satisfactoria.
    throw new RuntimeException( 'No se pudo leer assets/plugins.js durante las pruebas automáticas.' ); // Lanza excepción si no es posible analizar el script.
}
if ( strpos( $pluginsScriptSource, 'window.alert' ) === false || strpos( $pluginsScriptSource, 'settings.warning' ) === false ) { // Comprueba que el script utilice la advertencia localizada antes de continuar.
    throw new RuntimeException( 'assets/plugins.js debería invocar window.alert con settings.warning para avisar que las reglas dejarán de aplicarse.' ); // Lanza excepción si falta la lógica de alerta.
}

CF7_OptionLimiter_Hooks::enqueue_shared_admin_assets( 'contact-form-7_page_wpcf7' ); // Simula la carga de assets en una pantalla del editor de Contact Form 7.
if ( empty( $GLOBALS['cf7_option_limiter_registered_scripts']['cf7-option-limiter-redirect-toggles'] ) ) { // Comprueba que el script de toggles se registrara correctamente.
    throw new RuntimeException( 'enqueue_shared_admin_assets debería registrar el script cf7-option-limiter-redirect-toggles con la ruta de assets/index.js.' ); // Lanza excepción si la entrada no existe en el registro.
}
if ( empty( $GLOBALS['cf7_option_limiter_registered_scripts']['cf7-option-limiter-survey-tab'] ) ) { // Comprueba que el script de la pestaña de encuestas se registrara correctamente.
    throw new RuntimeException( 'enqueue_shared_admin_assets debería registrar el script cf7-option-limiter-survey-tab con la ruta de assets/survey.js.' ); // Lanza excepción si falta la entrada esperada.
}
if ( ! in_array( 'cf7-option-limiter-redirect-toggles', $GLOBALS['cf7_option_limiter_enqueued_scripts'], true ) ) { // Verifica que el script de toggles se encolara al cargar una pantalla del editor.
    throw new RuntimeException( 'enqueue_shared_admin_assets debería encolar cf7-option-limiter-redirect-toggles cuando el hook pertenece a Contact Form 7.' ); // Informa si el script no quedó programado para imprimirse.
}
if ( ! in_array( 'cf7-option-limiter-survey-tab', $GLOBALS['cf7_option_limiter_enqueued_scripts'], true ) ) { // Verifica que el script de encuestas se encolara en la misma pantalla.
    throw new RuntimeException( 'enqueue_shared_admin_assets debería encolar cf7-option-limiter-survey-tab cuando el hook pertenece a Contact Form 7.' ); // Informa si el script no quedó programado para imprimirse.
}
$redirectToggleMeta = $GLOBALS['cf7_option_limiter_registered_scripts']['cf7-option-limiter-redirect-toggles']; // Recupera los metadatos del script de toggles registrado.
if ( $redirectToggleMeta['ver'] !== CF7_OPTION_LIMITER_VERSION || $redirectToggleMeta['src'] !== CF7_OPTION_LIMITER_URL . 'assets/index.js' ) { // Comprueba que el script utilice la versión global y la ruta esperada.
    throw new RuntimeException( 'El script cf7-option-limiter-redirect-toggles debería registrarse con CF7_OPTION_LIMITER_VERSION y la ruta assets/index.js.' ); // Lanza excepción si alguna de las propiedades no coincide.
}
$surveyScriptMeta = $GLOBALS['cf7_option_limiter_registered_scripts']['cf7-option-limiter-survey-tab']; // Recupera los metadatos del script de encuestas.
if ( $surveyScriptMeta['ver'] !== CF7_OPTION_LIMITER_VERSION || $surveyScriptMeta['src'] !== CF7_OPTION_LIMITER_URL . 'assets/survey.js' ) { // Comprueba que el script de encuestas conserve versión y ruta correctas.
    throw new RuntimeException( 'El script cf7-option-limiter-survey-tab debería registrarse con CF7_OPTION_LIMITER_VERSION y la ruta assets/survey.js.' ); // Lanza excepción cuando los metadatos no coinciden con lo esperado.
}

$toggleScriptSource = file_get_contents( __DIR__ . '/../assets/index.js' ); // Lee el contenido del script que normaliza los toggles del metabox.
if ( false === $toggleScriptSource ) { // Comprueba que la lectura del archivo haya sido satisfactoria.
    throw new RuntimeException( 'No se pudo leer assets/index.js durante las pruebas automáticas.' ); // Lanza excepción si no es posible analizar el script recién añadido.
}
if ( strpos( $toggleScriptSource, 'normalizeSelectorFragment' ) === false || strpos( $toggleScriptSource, 'data-toggle' ) === false ) { // Comprueba que el script documente y procese correctamente los selectores declarados.
    throw new RuntimeException( 'assets/index.js debería incluir normalizeSelectorFragment y hacer referencia a data-toggle para evitar el prefijo # en selectores por clase.' ); // Lanza excepción si faltan las piezas clave de la normalización.
}
if ( strpos( $toggleScriptSource, "qsa( '[data-toggle]' )" ) === false || strpos( $toggleScriptSource, 'applyToggleState' ) === false ) { // Comprueba que se iteren todos los controles con data-toggle y se aplique el estado resultante.
    throw new RuntimeException( 'assets/index.js debería recorrer todos los elementos con data-toggle y aplicar el estado mediante applyToggleState.' ); // Informa si el script no contiene la lógica de inicialización esperada.
}

$surveyScriptSource = file_get_contents( __DIR__ . '/../assets/survey.js' ); // Lee el contenido del script encargado de la pestaña de encuestas.
if ( false === $surveyScriptSource ) { // Comprueba que el archivo exista y se pueda leer.
    throw new RuntimeException( 'No se pudo leer assets/survey.js durante las pruebas automáticas.' ); // Lanza excepción si la lectura falla.
}
if ( strpos( $surveyScriptSource, 'DOMContentLoaded' ) === false || strpos( $surveyScriptSource, '#survey-tab' ) === false ) { // Comprueba que el script espere a que el DOM esté listo y busque el elemento por su ID.
    throw new RuntimeException( 'assets/survey.js debería esperar al evento DOMContentLoaded y localizar el elemento #survey-tab antes de adjuntar listeners.' ); // Informa si falta alguna de las salvaguardas.
}
if ( strpos( $surveyScriptSource, 'window.configureSurveyTabListener' ) === false ) { // Comprueba que la función se exponga globalmente para mantener compatibilidad.
    throw new RuntimeException( 'assets/survey.js debería exponer window.configureSurveyTabListener para reutilizar la función desde otros scripts.' ); // Lanza excepción si la función no queda publicada en el ámbito global.
}

$adminScriptPath = __DIR__ . '/../assets/admin.js'; // Calcula la ruta absoluta del script administrativo.
$adminScriptSource = file_get_contents( $adminScriptPath ); // Lee el contenido completo del archivo JavaScript para inspeccionarlo.
if ( false === $adminScriptSource ) { // Comprueba que la lectura del archivo haya sido satisfactoria.
    throw new RuntimeException( 'No se pudo leer assets/admin.js durante las pruebas automáticas.' ); // Informa del fallo al no poder validar el script.
}
if ( strpos( $adminScriptSource, "#cf7-ol-submit" ) === false || strpos( $adminScriptSource, 'requestSubmit' ) === false ) { // Verifica que el script contenga el manejador que intercepta el clic y dispara requestSubmit.
    throw new RuntimeException( 'El script administrativo debería forzar el envío del formulario oculto utilizando requestSubmit.' ); // Lanza excepción cuando la lógica requerida no está presente.
}
if ( strpos( $adminScriptSource, "cf7-option-limiter-embedded-form" ) === false || strpos( $adminScriptSource, ".trigger( 'submit' )" ) === false ) { // Comprueba que exista el respaldo que dispara el submit tradicional sobre el formulario oculto.
    throw new RuntimeException( 'El script administrativo debería incluir un respaldo que invoque trigger(\'submit\') sobre el formulario oculto.' ); // Falla si el envío alternativo no aparece en el código.
}
if ( strpos( $adminScriptSource, 'syncEmbeddedFormValues' ) === false || strpos( $adminScriptSource, '$hiddenFormId' ) === false ) { // Comprueba que la sincronización de valores esté implementada en el script administrativo.
    throw new RuntimeException( 'El script administrativo debería replicar los valores visibles en campos ocultos antes de enviar la petición.' ); // Informa si falta la nueva lógica de sincronización.
}
if ( strpos( $adminScriptSource, "setHiddenFieldValue" ) === false || strpos( $adminScriptSource, "cf7-ol-hidden-hide-exhausted" ) === false ) { // Verifica que exista el ayudante para copiar valores y el control específico del checkbox.
    throw new RuntimeException( 'El script administrativo debería exponer un ayudante setHiddenFieldValue y gestionar el campo oculto de hide_exhausted.' ); // Lanza excepción si falta cualquiera de los elementos clave.
}
if ( strpos( $adminScriptSource, 'validateSelectionsBeforeSubmit' ) === false || strpos( $adminScriptSource, '! validateSelectionsBeforeSubmit()' ) === false ) { // Comprueba que exista una validación explícita antes del envío programático.
    throw new RuntimeException( 'El script administrativo debería rechazar el envío cuando faltan formulario, campo u opción antes de llamar a requestSubmit.' ); // Informa si la nueva salvaguarda no se detecta en el código.
}
if ( strpos( $adminScriptSource, 'hasCompleteSelection' ) === false || strpos( $adminScriptSource, '$submitButton.prop( \'disabled\',' ) === false ) { // Comprueba que la lógica de activación del botón se base en las selecciones completas.
    throw new RuntimeException( 'El script administrativo debería evaluar hasCompleteSelection y alternar el estado disabled del botón principal.' ); // Lanza excepción si falta la lógica de habilitación/deshabilitación.
}
if ( strpos( $adminScriptSource, "aria-disabled" ) === false ) { // Comprueba que se sincronice el estado accesible del botón principal.
    throw new RuntimeException( 'El botón principal debería actualizar su atributo aria-disabled según la disponibilidad del envío.' ); // Informa si no se encontró la sincronización accesible.
}
if ( strpos( $adminScriptSource, 'ensureAccessibleNotice' ) === false || strpos( $adminScriptSource, "role', 'alert'" ) === false ) { // Comprueba que exista una región aria-live dedicada a los avisos accesibles.
    throw new RuntimeException( 'El script administrativo debería crear una región accesible con role="alert" para anunciar errores antes del envío.' ); // Lanza excepción si falta el soporte de accesibilidad.
}
if ( strpos( $adminScriptSource, 'formIdValue > 0' ) === false ) { // Comprueba que el formulario seleccionado deba tener un identificador positivo.
    throw new RuntimeException( 'La validación de JavaScript debería verificar que el formulario tenga un identificador mayor que cero antes de permitir el envío.' ); // Informa si no se encontró el control del identificador positivo.
}
if ( strpos( $adminScriptSource, 'refreshEmbeddedFormsContext' ) === false || strpos( $adminScriptSource, 'ensureEmbeddedFormsPresence' ) === false ) { // Comprueba que el script gestione la detección y creación dinámica de formularios ocultos.
    throw new RuntimeException( 'El script administrativo debería garantizar la presencia de los formularios ocultos mediante refreshEmbeddedFormsContext y ensureEmbeddedFormsPresence.' ); // Informa si falta la lógica que asegura la creación dinámica.
}
if ( strpos( $adminScriptSource, 'CF7OptionLimiterAdmin.adminPostUrl' ) === false ) { // Comprueba que el script utilice la URL global del endpoint admin-post.
    throw new RuntimeException( 'El script administrativo debería reutilizar CF7OptionLimiterAdmin.adminPostUrl para construir o hidratar los formularios ocultos fuera del editor principal.' ); // Lanza excepción cuando no se detecta la referencia a la URL localizada.
}
if ( strpos( $adminScriptSource, 'embeddedNonces' ) === false ) { // Comprueba que el script haga uso de los nonces localizados para recrear formularios seguros.
    throw new RuntimeException( 'El script administrativo debería consumir la colección embeddedNonces para poblar los formularios ocultos creados dinámicamente.' ); // Informa si no se detecta el uso de los nonces localizados.
}
if ( strpos( $adminScriptSource, 'cf7-option-limiter-embedded-release' ) === false || strpos( $adminScriptSource, 'cf7-ol-release-rule-id' ) === false ) { // Verifica que el script contemple la recreación del formulario oculto de liberación.
    throw new RuntimeException( 'El script administrativo debería gestionar el formulario oculto de liberación recreándolo cuando no existe y sincronizando sus campos.' ); // Lanza excepción si falta la lógica que respalda la liberación en el editor incrustado.
}

$editorPanelPath = __DIR__ . '/../admin/class-cf7-editor-panel.php'; // Calcula la ruta absoluta del panel incrustado.
$editorPanelSource = file_get_contents( $editorPanelPath ); // Lee el archivo PHP para validar el marcado del botón.
if ( false === $editorPanelSource ) { // Comprueba que la lectura haya sido satisfactoria.
    throw new RuntimeException( 'No se pudo leer admin/class-cf7-editor-panel.php durante las pruebas automáticas.' ); // Informa del fallo que impediría analizar el marcado.
}
if ( strpos( $editorPanelSource, 'id="cf7-ol-submit"' ) === false || strpos( $editorPanelSource, 'type="button"' ) === false ) { // Verifica que el botón del panel se haya declarado explícitamente como botón para evitar envíos automáticos.
    throw new RuntimeException( 'El botón principal del panel incrustado debería declararse con type="button" para delegar el envío en JavaScript.' ); // Lanza excepción cuando el marcado no refleja el nuevo flujo.
}
if ( strpos( $editorPanelSource, 'cf7-option-limiter-action-button cf7-option-limiter-edit' ) === false || strpos( $editorPanelSource, 'dashicons-edit' ) === false ) { // Comprueba que el panel utilice el botón icónico de edición solicitado.
    throw new RuntimeException( 'La tabla del editor debería utilizar un botón icónico con dashicons-edit para la acción de editar.' ); // Informa si el icono de edición no está presente.
}
if ( strpos( $editorPanelSource, 'cf7-option-limiter-release' ) === false || strpos( $editorPanelSource, 'dashicons-unlock' ) === false ) { // Comprueba que exista el formulario en línea de liberación con su icono.
    throw new RuntimeException( 'La tabla del editor debería ofrecer el botón icónico de liberar con dashicons-unlock y la clase cf7-option-limiter-release.' ); // Informa si el botón de liberar no se añadió al panel.
}
if ( strpos( $editorPanelSource, 'cf7-option-limiter-action-button cf7-option-limiter-delete' ) === false || strpos( $editorPanelSource, 'dashicons-trash' ) === false ) { // Comprueba que la acción de borrado se represente con un icono de papelera.
    throw new RuntimeException( 'La tabla del editor debería mostrar el icono dashicons-trash dentro del botón de eliminación.' ); // Lanza excepción cuando falta la iconografía de eliminación.
}
$panelReflection = new ReflectionClass( CF7_OptionLimiter_CF7_Panel::class ); // Prepara la reflexión para reutilizarla en comprobaciones adicionales sobre el panel incrustado.
$releaseActionMethod = $panelReflection->getMethod( 'render_release_action_form' ); // Localiza el método que genera el botón de liberación incrustado.
$releaseActionMethod->setAccessible( true ); // Permite invocar el método protegido dentro del entorno de pruebas.
$releaseButtonMarkup = $releaseActionMethod->invoke( null, array( 'id' => 15, 'current_count' => 3 ), 'https://example.com/editor' ); // Genera el marcado del botón cuando existen usos disponibles para liberar.
if ( strpos( $releaseButtonMarkup, '<form' ) !== false ) { // Comprueba que el marcado no incluya formularios anidados.
    throw new RuntimeException( 'render_release_action_form no debería imprimir etiquetas <form> dentro de la tabla incrustada del editor.' ); // Lanza excepción si aún se generan formularios anidados.
}
if ( strpos( $releaseButtonMarkup, 'type="button"' ) === false ) { // Verifica que el control se renderice como botón autónomo.
    throw new RuntimeException( 'render_release_action_form debería declarar el control como type="button" para delegar el envío en JavaScript.' ); // Informa si el tipo de botón no es el esperado.
}
if ( strpos( $releaseButtonMarkup, 'data-rule-id="15"' ) === false ) { // Comprueba que el identificador de la regla viaje en un atributo de datos.
    throw new RuntimeException( 'render_release_action_form debería exponer data-rule-id con el identificador de la regla a liberar.' ); // Informa si falta el atributo imprescindible para el script.
}
if ( strpos( $releaseButtonMarkup, 'data-redirect="https://example.com/editor"' ) === false ) { // Comprueba que la URL de retorno se traslade al botón.
    throw new RuntimeException( 'render_release_action_form debería exponer data-redirect con la URL de retorno del editor.' ); // Lanza excepción si la redirección no se exporta al botón.
}
if ( strpos( $releaseButtonMarkup, 'disabled="disabled"' ) !== false || strpos( $releaseButtonMarkup, 'cf7-option-limiter-action-button--disabled' ) !== false ) { // Comprueba que no se apliquen atributos de deshabilitado cuando existen usos disponibles.
    throw new RuntimeException( 'render_release_action_form no debería marcar como deshabilitado el botón cuando aún quedan usos liberables.' ); // Informa si la representación activa incluye estados inactivos.
}
$releaseButtonDisabledMarkup = $releaseActionMethod->invoke( null, array( 'id' => 16, 'current_count' => 0 ), 'https://example.com/editor' ); // Genera el marcado cuando el contador ya está agotado.
if ( strpos( $releaseButtonDisabledMarkup, 'disabled="disabled"' ) === false || strpos( $releaseButtonDisabledMarkup, 'cf7-option-limiter-action-button--disabled' ) === false ) { // Verifica que el botón refleje el estado deshabilitado en ausencia de usos.
    throw new RuntimeException( 'render_release_action_form debería añadir el atributo disabled y la clase de estado cuando no quedan usos por liberar.' ); // Lanza excepción si la representación inactiva no aplica ambos indicadores.
}
$needsProperty = $panelReflection->getProperty( 'needs_hidden_forms' ); // Recupera la propiedad protegida que controla la impresión de formularios ocultos.
$needsProperty->setAccessible( true ); // Permite modificar la propiedad protegida dentro de la prueba.
$needsProperty->setValue( null, false ); // Restablece la bandera interna para simular una carga limpia del panel incrustado.
ob_start(); // Inicia el búfer de salida para capturar cualquier impresión inesperada de print_hidden_forms.
CF7_OptionLimiter_CF7_Panel::print_hidden_forms(); // Solicita que el panel marque la necesidad de imprimir los formularios ocultos en el pie global.
$printHiddenOutput = ob_get_clean(); // Recupera la salida generada por print_hidden_forms.
if ( '' !== trim( $printHiddenOutput ) ) { // Comprueba que el método no produzca salida directa tras la refactorización.
    throw new RuntimeException( 'print_hidden_forms no debería imprimir HTML directamente; sólo debe marcar la necesidad de renderizar los formularios en el pie global.' ); // Informa cuando el método produce salida prematuramente.
}
if ( true !== $needsProperty->getValue( null ) ) { // Comprueba que la bandera interna quede activada tras la llamada.
    throw new RuntimeException( 'print_hidden_forms debería establecer la bandera interna needs_hidden_forms en true para que los formularios se impriman en admin_footer.' ); // Lanza excepción si la bandera no se activa.
}
ob_start(); // Inicia un nuevo búfer para capturar el HTML generado por la impresión real en el pie global.
CF7_OptionLimiter_CF7_Panel::render_hidden_forms(); // Ejecuta la impresión diferida de los formularios ocultos en el pie de administrador.
$hiddenFormsMarkup = ob_get_clean(); // Recupera el marcado generado por render_hidden_forms.
if ( '' === trim( $hiddenFormsMarkup ) ) { // Comprueba que se haya impreso contenido al invocar el nuevo método.
    throw new RuntimeException( 'render_hidden_forms debería imprimir los formularios ocultos cuando la bandera needs_hidden_forms está activa.' ); // Informa si no se imprimió nada.
}
if ( true === $needsProperty->getValue( null ) ) { // Comprueba que la bandera se restablezca tras imprimir los formularios.
    throw new RuntimeException( 'render_hidden_forms debería restablecer la bandera needs_hidden_forms a false después de imprimir los formularios.' ); // Informa si la bandera permanece activa indebidamente.
}
$requiredHiddenTokens = array( // Define los fragmentos que deben aparecer en el formulario oculto definitivo.
    'id="cf7-ol-hidden-form-id"', // Identificador del campo oculto que replica form_id.
    'id="cf7-ol-hidden-field-name"', // Identificador del campo oculto que replica field_name.
    'id="cf7-ol-hidden-option-value"', // Identificador del campo oculto que replica option_value.
    'id="cf7-ol-hidden-max-count"', // Identificador del campo oculto que replica max_count.
    'id="cf7-ol-hidden-limit-period"', // Identificador del campo oculto que replica limit_period.
    'id="cf7-ol-hidden-custom-message"', // Identificador del campo oculto que replica custom_message.
    'id="cf7-ol-hidden-hide-exhausted"', // Identificador del campo oculto asociado a hide_exhausted.
    'id="cf7-ol-hidden-rule-id"', // Identificador del campo oculto que replica rule_id.
    'id="cf7-ol-hidden-original-form-id"', // Identificador del campo oculto que replica original_form_id.
    'id="cf7-ol-hidden-original-field-name"', // Identificador del campo oculto que replica original_field_name.
    'id="cf7-ol-hidden-original-option-value"', // Identificador del campo oculto que replica original_option_value.
    'id="cf7-ol-hidden-redirect"', // Identificador del campo oculto que replica redirect_to.
    'id="cf7-option-limiter-embedded-release"', // Identificador del formulario oculto dedicado a la liberación manual.
    'id="cf7-ol-release-rule-id"', // Identificador del campo oculto que replica la regla a liberar.
    'id="cf7-ol-release-redirect"', // Identificador del campo oculto que replica la URL de retorno para liberaciones.
    'cf7_option_limiter_release_nonce', // Nombre del nonce específico utilizado durante la liberación.
); // Finaliza la lista de marcadores esperados.
foreach ( $requiredHiddenTokens as $token ) { // Recorre cada marcador requerido para asegurar que existe en el HTML generado.
    if ( strpos( $hiddenFormsMarkup, $token ) === false ) { // Comprueba si el marcador actual está ausente.
        throw new RuntimeException( 'print_hidden_forms debería incluir el campo oculto ' . $token . ' dentro del formulario definitivo.' ); // Lanza excepción indicando el marcador faltante.
    }
}
if ( strpos( $hiddenFormsMarkup, 'id="cf7-ol-hidden-hide-exhausted"' ) === false || strpos( $hiddenFormsMarkup, 'disabled="disabled"' ) === false ) { // Comprueba que el campo oculto del checkbox esté presente y deshabilitado por defecto.
    throw new RuntimeException( 'El campo oculto de hide_exhausted debería imprimirse deshabilitado para emular la ausencia de la casilla cuando está desmarcada.' ); // Lanza excepción si el comportamiento inicial no coincide con lo esperado.
}


require_once __DIR__ . '/../includes/class-db-manager.php'; // Carga la capa de acceso a datos para verificar operaciones de base de datos y migraciones.
require_once __DIR__ . '/../admin/class-admin-page.php'; // Carga la clase de administración a probar.
require_once __DIR__ . '/../includes/class-limiter-handler.php'; // Carga el manejador de límites para probar la validación dinámica.

$shouldResetMethod = new ReflectionMethod( CF7_OptionLimiter_DB::class, 'should_reset' ); // Prepara la reflexión para acceder al método protegido que decide el reinicio automático.
$shouldResetMethod->setAccessible( true ); // Habilita el acceso al método protegido para invocarlo durante las pruebas unitarias.

$GLOBALS['cf7_option_limiter_fake_options']['gmt_offset'] = 3; // Configura un desfase horario positivo de +3 horas para reproducir entornos europeos.
$positiveTimezone = wp_timezone(); // Recupera la zona horaria calculada a partir del desfase configurado.
$positiveLastReset = new DateTimeImmutable( '2024-03-15 23:30:00', $positiveTimezone ); // Crea la marca de último reinicio en horario local cercano al cambio de día.
$positiveCurrent = new DateTimeImmutable( '2024-03-16 00:10:00', $positiveTimezone ); // Define la hora actual en el día siguiente dentro del mismo huso horario.
$positiveRule = array( // Construye la regla simulada asociada al reinicio diario.
    'limit_period' => 'day', // Establece el periodo diario para evaluar el cambio de fecha.
    'limit_reset'  => $positiveLastReset->format( 'Y-m-d H:i:s' ), // Almacena la fecha de último reinicio como la guardaría la base de datos.
); // Finaliza la configuración de la regla diaria.
$shouldResetPositive = $shouldResetMethod->invoke( null, $positiveRule, $positiveCurrent->getTimestamp() ); // Invoca el método protegido con los datos preparados para validar el cambio de día.
if ( true !== $shouldResetPositive ) { // Comprueba que el reinicio diario se active correctamente con desfases positivos.
    throw new RuntimeException( 'should_reset debería detectar el cambio de día utilizando la zona horaria positiva configurada.' ); // Informa del fallo cuando no se detecta el cambio de fecha local.
}

$GLOBALS['cf7_option_limiter_fake_options']['gmt_offset'] = -4; // Configura un desfase horario negativo de -4 horas para simular entornos americanos.
$negativeTimezone = wp_timezone(); // Recupera la zona horaria correspondiente al desfase negativo.
$negativeLastReset = new DateTimeImmutable( '2024-03-10 23:30:00', $negativeTimezone ); // Define el último reinicio al final de la semana ISO local.
$negativeCurrent = new DateTimeImmutable( '2024-03-11 00:15:00', $negativeTimezone ); // Establece la fecha actual en la semana siguiente dentro del mismo huso horario.
$negativeRule = array( // Construye la regla simulada para el reinicio semanal.
    'limit_period' => 'week', // Indica que la comparación debe realizarse por semana ISO.
    'limit_reset'  => $negativeLastReset->format( 'Y-m-d H:i:s' ), // Conserva el formato exacto almacenado en base de datos para la fecha previa.
); // Finaliza la configuración de la regla semanal.
$shouldResetNegative = $shouldResetMethod->invoke( null, $negativeRule, $negativeCurrent->getTimestamp() ); // Ejecuta la evaluación del cambio de semana utilizando la zona horaria negativa.
if ( true !== $shouldResetNegative ) { // Comprueba que el reinicio semanal se active cuando la semana ISO local cambia.
    throw new RuntimeException( 'should_reset debería detectar el cambio de semana utilizando la zona horaria negativa configurada.' ); // Informa del fallo si la comparación semanal no se actualiza con el nuevo huso horario.
}

$GLOBALS['cf7_option_limiter_fake_options']['gmt_offset'] = 0; // Restablece el desfase horario a cero para el resto de las pruebas del archivo.

$refNormalize = new ReflectionMethod( CF7_OptionLimiter_Admin::class, 'normalize_tag' ); // Prepara la reflexión para acceder al método protegido normalize_tag.
$refNormalize->setAccessible( true ); // Habilita el acceso al método protegido para poder invocarlo en las pruebas.

$objectTag = new stdClass(); // Crea un objeto para simular la etiqueta WPCF7_FormTag.
$objectTag->basetype = 'select'; // Define el tipo base compatible.
$objectTag->name = 'turno'; // Asigna un nombre de campo válido.
$objectTag->values = array( 'manana', 'tarde' ); // Define dos valores posibles en el campo.
$objectTag->labels = array( 'Mañana', 'Tarde' ); // Define etiquetas legibles para los valores.
$normalizedObject = $refNormalize->invoke( null, $objectTag ); // Invoca el método protegido con el objeto simulado.
if ( $normalizedObject['name'] !== 'turno' ) { // Comprueba que el nombre se conserve correctamente.
    throw new RuntimeException( 'Fallo al normalizar etiqueta objeto: nombre incorrecto.' ); // Lanza excepción si la aserción falla.
}
if ( count( $normalizedObject['options'] ) !== 2 ) { // Verifica que se hayan detectado las dos opciones definidas.
    throw new RuntimeException( 'Fallo al normalizar etiqueta objeto: conteo de opciones incorrecto.' ); // Lanza excepción ante fallo.
}
if ( $normalizedObject['options'][0]['label'] !== 'Mañana' ) { // Comprueba que la etiqueta se haya mantenido.
    throw new RuntimeException( 'Fallo al normalizar etiqueta objeto: etiqueta no coincide.' ); // Informa del error específico.
}

$arrayTag = array( // Construye un arreglo para simular la estructura usada en versiones antiguas.
    'basetype'   => 'checkbox', // Define el tipo base compatible.
    'name'       => 'dias', // Asigna el nombre del campo.
    'raw_values' => array( 'lunes', 'martes' ), // Define dos valores posibles.
    'labels'     => array( 'Lunes', 'Martes' ), // Establece etiquetas visibles.
);
$normalizedArray = $refNormalize->invoke( null, $arrayTag ); // Invoca el método protegido con la versión en arreglo.
if ( $normalizedArray['name'] !== 'dias' ) { // Comprueba que el nombre del campo sea correcto.
    throw new RuntimeException( 'Fallo al normalizar etiqueta arreglo: nombre incorrecto.' ); // Lanza excepción si hay discrepancia.
}
if ( $normalizedArray['options'][1]['value'] !== 'martes' ) { // Verifica que el valor permanezca sin modificaciones.
    throw new RuntimeException( 'Fallo al normalizar etiqueta arreglo: valor incorrecto.' ); // Informa si el valor no coincide.
}

$refResolveId = new ReflectionMethod( CF7_OptionLimiter_Admin::class, 'resolve_form_id' ); // Prepara la reflexión para el método resolve_form_id.
$refResolveId->setAccessible( true ); // Permite invocar el método protegido.
if ( $refResolveId->invoke( null, $objectTag ) !== 0 ) { // Comprueba que un objeto sin ID específico devuelva cero.
    throw new RuntimeException( 'resolve_form_id debería devolver 0 para objetos sin ID válido.' ); // Señala el fallo concreto.
}
$fakeForm = new stdClass(); // Crea un objeto que simula un formulario real.
$fakeForm->ID = 10; // Asigna un identificador numérico.
$fakeForm->post_title = 'Reservas'; // Define un título accesible.
if ( $refResolveId->invoke( null, $fakeForm ) !== 10 ) { // Verifica que se reconozca la propiedad ID.
    throw new RuntimeException( 'resolve_form_id no detectó la propiedad ID.' ); // Informa del fallo.
}

$refResolveTitle = new ReflectionMethod( CF7_OptionLimiter_Admin::class, 'resolve_form_title' ); // Prepara el método para extraer el título.
$refResolveTitle->setAccessible( true ); // Permite su invocación.
if ( $refResolveTitle->invoke( null, $fakeForm ) !== 'Reservas' ) { // Comprueba que el título se obtenga correctamente.
    throw new RuntimeException( 'resolve_form_title no devolvió el título esperado.' ); // Lanza excepción si la comprobación falla.
}

$wpdb->expected_results = array( // Configura resultados simulados para la consulta en bloque de límites.
    array( // Primera fila simulada correspondiente a la opción turno-a.
        'option_value'   => 'turno-a', // Valor de opción almacenado en base de datos.
        'max_count'      => 5, // Límite máximo configurado.
        'current_count'  => 3, // Contador actual registrado.
        'custom_message' => 'Mensaje turno A', // Mensaje personalizado de agotamiento.
        'hide_exhausted' => 0, // Indicador que conserva la visibilidad de la opción agotada.
    ),
    array( // Segunda fila simulada correspondiente a la opción turno-b.
        'option_value'   => 'turno-b', // Valor de opción almacenado.
        'max_count'      => 2, // Límite máximo configurado.
        'current_count'  => 2, // Contador actual que provoca agotamiento.
        'custom_message' => 'Mensaje turno B', // Mensaje personalizado.
        'hide_exhausted' => 1, // Indicador que solicita ocultar la opción al agotarse.
    ),
); // Finaliza la configuración de resultados simulados.
$limitsMap = CF7_OptionLimiter_DB::get_limits_for_options( 7, 'turno', array( 'turno-a', 'turno-b', 'turno-c' ) ); // Ejecuta la consulta en bloque con un valor inexistente adicional.
if ( count( $limitsMap ) !== 2 ) { // Comprueba que se hayan recuperado únicamente las dos reglas existentes.
    throw new RuntimeException( 'get_limits_for_options debería devolver únicamente las reglas encontradas.' ); // Falla la prueba si el conteo no coincide.
}
if ( $limitsMap['turno-a']['current_count'] !== 3 ) { // Verifica que la opción turno-a conserve el contador configurado.
    throw new RuntimeException( 'get_limits_for_options no devolvió el contador correcto para turno-a.' ); // Lanza excepción en caso de discrepancia.
}
if ( $limitsMap['turno-b']['custom_message'] !== 'Mensaje turno B' ) { // Comprueba que el mensaje personalizado se conserve en la opción turno-b.
    throw new RuntimeException( 'get_limits_for_options no conservó el mensaje personalizado para turno-b.' ); // Indica fallo si el mensaje difiere.
}
if ( (int) $limitsMap['turno-b']['hide_exhausted'] !== 1 ) { // Verifica que se conserve el indicador de ocultación para turno-b.
    throw new RuntimeException( 'get_limits_for_options no propagó la preferencia de ocultación para turno-b.' ); // Indica el fallo si la bandera no coincide.
}
$wpdb->expected_results = array(); // Restablece los resultados simulados para evitar interferencias en pruebas posteriores.

$wpdb->expected_row = array( // Configura la fila que devolverá get_limit_by_id durante la prueba específica.
    'id'             => 42, // Identificador de la regla simulada.
    'form_id'        => 3, // Formulario al que pertenece la regla.
    'field_name'     => 'habitacion', // Nombre del campo asociado.
    'option_value'   => 'suite', // Valor de la opción limitada.
    'max_count'      => 5, // Máximo permitido en la regla simulada.
    'current_count'  => 2, // Contador actual almacenado en la regla.
    'limit_period'   => 'day', // Periodo configurado en la regla.
    'limit_reset'    => '2024-01-01 00:00:00', // Fecha de reseteo simulada.
    'custom_message' => 'Mensaje de prueba', // Mensaje personalizado almacenado en la regla.
    'hide_exhausted' => 0, // Indicador de que la opción permanece visible al agotarse en esta regla simulada.
    'created_at'     => '2023-12-01 10:00:00', // Fecha de creación simulada.
); // Finaliza la configuración de la fila.
$limitById = CF7_OptionLimiter_DB::get_limit_by_id( 42 ); // Recupera la regla utilizando el nuevo método por ID.
if ( $limitById['option_value'] !== 'suite' || $limitById['form_id'] !== 3 ) { // Comprueba que la fila devuelta coincida con la configurada.
    throw new RuntimeException( 'get_limit_by_id no devolvió los datos esperados.' ); // Informa del fallo si los datos difieren.
}
$wpdb->expected_row = null; // Restablece el valor para evitar interferencias en pruebas posteriores.

$wpdb->expected_results = array( // Configura los resultados que devolverá get_limits_by_form.
    array( 'id' => 7, 'form_id' => 9, 'field_name' => 'turno', 'option_value' => 'manana', 'max_count' => 3, 'current_count' => 1, 'limit_period' => 'hour', 'custom_message' => '', 'hide_exhausted' => 0, 'created_at' => '2024-02-01 08:00:00' ), // Primera regla simulada.
    array( 'id' => 8, 'form_id' => 9, 'field_name' => 'turno', 'option_value' => 'tarde', 'max_count' => 4, 'current_count' => 0, 'limit_period' => 'day', 'custom_message' => '', 'hide_exhausted' => 1, 'created_at' => '2024-02-01 09:00:00' ), // Segunda regla simulada.
); // Finaliza la configuración de resultados.
$limitsByForm = CF7_OptionLimiter_DB::get_limits_by_form( 9 ); // Recupera todas las reglas asociadas al formulario 9.
if ( count( $limitsByForm ) !== 2 || $limitsByForm[1]['option_value'] !== 'tarde' ) { // Comprueba que se recuperaron ambas reglas en el orden esperado.
    throw new RuntimeException( 'get_limits_by_form no devolvió el conjunto completo de reglas esperadas.' ); // Indica el fallo si el resultado no coincide.
}
$wpdb->expected_results = array(); // Restablece el arreglo para evitar interferencias futuras.

$wpdb->expected_results = array( array( 'id' => 1, 'option_value' => 'demo' ) ); // Configura una página simulada para la nueva función paginada.
$paginated = CF7_OptionLimiter_DB::get_limits_filtered( 0, 10, 0 ); // Recupera el subconjunto paginado sin filtros.
if ( count( $paginated ) !== 1 || $paginated[0]['option_value'] !== 'demo' ) { // Comprueba que la función devuelva el subconjunto esperado.
    throw new RuntimeException( 'get_limits_filtered no respetó la paginación básica.' ); // Lanza excepción si el resultado no coincide.
}
$wpdb->expected_results = array(); // Limpia los resultados simulados para consultas posteriores.
$wpdb->get_var_return_value = 7; // Configura un total simulado para el conteo paginado.
if ( CF7_OptionLimiter_DB::count_limits( 0 ) !== 7 ) { // Comprueba que el total se devuelva correctamente.
    throw new RuntimeException( 'count_limits no devolvió el total esperado.' ); // Lanza excepción si el conteo es incorrecto.
}
$wpdb->get_var_return_value = null; // Restablece el valor utilizado en la simulación.

$logSetupPath = ''; // Inicializa la variable que almacenará la ruta del log durante las pruebas de guardado.
CF7_OptionLimiter_Logger::init(); // Garantiza que el logger esté listo antes de comenzar las pruebas de guardado.
$logSetupPath = CF7_OptionLimiter_Logger::get_log_file_path(); // Recupera la ruta del archivo de log preparada durante la inicialización.
if ( $logSetupPath ) { // Comprueba que se haya determinado un archivo válido.
    file_put_contents( $logSetupPath, '' ); // Limpia el archivo para que sólo contenga las entradas generadas en esta batería de pruebas.
}

$now = current_time( 'mysql' ); // Captura una marca temporal compartida para las operaciones de inserción y actualización.
$sampleLimit = array( // Construye un arreglo de datos representativo para probar el guardado de reglas.
    'form_id'        => 11, // Identificador del formulario asociado a la regla de prueba.
    'field_name'     => 'modalidad', // Nombre del campo que se limitará en la prueba.
    'option_value'   => 'online', // Valor concreto de la opción controlada.
    'hide_exhausted' => 1, // Solicita ocultar la opción al agotarse para validar el guardado del indicador.
    'max_count'      => 5, // Límite máximo permitido utilizado en la prueba.
    'current_count'  => 0, // Contador inicial que debe conservarse tras la inserción.
    'limit_period'   => 'none', // Periodo configurado para la regla de prueba.
    'limit_reset'    => $now, // Fecha de reseteo utilizada en la inserción y actualización.
    'custom_message' => '', // Mensaje personalizado vacío para simplificar la verificación.
    'created_at'     => $now, // Fecha de creación utilizada durante las pruebas.
    'updated_at'     => $now, // Fecha de actualización inicial.
); // Finaliza la configuración de los datos de prueba.
$wpdb->expected_rows = array(); // Asegura que get_limit devuelva null para forzar una inserción.
$wpdb->insert_called = false; // Restablece el indicador de inserciones previas.
if ( ! CF7_OptionLimiter_DB::upsert_limit( $sampleLimit ) ) { // Ejecuta la inserción inicial de la regla de prueba.
    throw new RuntimeException( 'upsert_limit debería insertar una nueva regla cuando no existe previamente.' ); // Lanza excepción si la inserción falla.
}
if ( ! $wpdb->insert_called ) { // Comprueba que la ruta de inserción se haya ejecutado en la capa de datos.
    throw new RuntimeException( 'upsert_limit debería utilizar $wpdb->insert al crear una regla.' ); // Informa si no se detectó la inserción.
}
if ( empty( $wpdb->last_insert ) || $wpdb->last_insert['table'] !== 'wp_cf7_option_limits' ) { // Verifica que la inserción se haya realizado sobre la tabla correcta.
    throw new RuntimeException( 'upsert_limit debería insertar en la tabla principal wp_cf7_option_limits.' ); // Lanza excepción si la tabla utilizada no coincide.
}
$recentLog = CF7_OptionLimiter_Logger::get_recent_lines( 6 ); // Recupera las entradas más recientes del log tras la inserción.
$joinedRecentLog = implode( "\n", $recentLog ); // Convierte el arreglo en una cadena para buscar eventos fácilmente.
if ( strpos( $joinedRecentLog, 'limit_save_start' ) === false || strpos( $joinedRecentLog, 'limit_save_sanitized' ) === false || strpos( $joinedRecentLog, 'limit_save_persist' ) === false ) { // Comprueba que se hayan registrado las etapas iniciales.
    throw new RuntimeException( 'El log debería registrar las etapas de inicio, sanitización y persistencia durante el guardado.' ); // Informa si faltan etapas clave.
}
if ( strpos( $joinedRecentLog, 'limit_save_existing' ) === false || strpos( $joinedRecentLog, 'found=false' ) === false ) { // Comprueba que la consulta previa indique que no existía una fila.
    throw new RuntimeException( 'El log debería indicar que no existía una fila previa al crear el límite.' ); // Informa si el log no reflejó la búsqueda previa.
}
if ( strpos( $joinedRecentLog, 'limit_save_complete' ) === false ) { // Comprueba que la operación se haya marcado como completada.
    throw new RuntimeException( 'El log debería registrar la finalización correcta del guardado.' ); // Informa si falta el desenlace positivo.
}
$wpdb->insert_called = false; // Restablece el indicador para la comprobación de actualización.
$wpdb->expected_rows = array( // Configura la fila que devolverá get_limit para simular la existencia previa.
    '11|modalidad|online' => array( // Clave compuesta por formulario, campo y valor.
        'id'             => 55, // Identificador de la fila existente que debe actualizarse.
        'form_id'        => 11, // Repite el identificador del formulario.
        'field_name'     => 'modalidad', // Repite el nombre del campo.
        'option_value'   => 'online', // Repite el valor de la opción.
        'max_count'      => 5, // Valores adicionales incluidos para mantener compatibilidad.
        'current_count'  => 0, // Contador existente almacenado en la fila previa.
        'limit_period'   => 'none', // Periodo actual registrado en la base de datos.
        'limit_reset'    => $now, // Fecha de reseteo existente.
        'custom_message' => '', // Mensaje almacenado anteriormente.
        'hide_exhausted' => 1, // Bandera existente en la fila de referencia.
    ),
); // Finaliza la configuración de la fila existente.
$wpdb->query_return_value = 1; // Simula que la actualización se ejecuta correctamente.
if ( ! CF7_OptionLimiter_DB::upsert_limit( $sampleLimit ) ) { // Ejecuta la actualización de la misma regla.
    throw new RuntimeException( 'upsert_limit debería actualizar la regla existente en lugar de fallar.' ); // Informa si la actualización retorna falso.
}
if ( $wpdb->insert_called ) { // Comprueba que no se haya intentado insertar nuevamente durante la actualización.
    throw new RuntimeException( 'upsert_limit no debería invocar insert cuando la regla ya existe.' ); // Señala el fallo si se detecta una inserción en la actualización.
}
if ( empty( $wpdb->last_query ) || ! is_array( $wpdb->last_query ) || $wpdb->last_query['table'] !== 'wp_cf7_option_limits' ) { // Verifica que la operación registrada corresponda a la tabla esperada.
    throw new RuntimeException( 'upsert_limit debería actualizar la tabla principal cuando la regla ya existe.' ); // Lanza excepción si los parámetros registrados no apuntan a la tabla correcta.
}
$recentUpdateLog = CF7_OptionLimiter_Logger::get_recent_lines( 10 ); // Recupera las entradas más recientes tras la actualización.
$joinedUpdateLog = implode( "\n", $recentUpdateLog ); // Convierte el arreglo para comprobar cadenas con facilidad.
if ( strpos( $joinedUpdateLog, 'limit_save_existing' ) === false || strpos( $joinedUpdateLog, 'existing_id=55' ) === false ) { // Comprueba que la traza refleje la detección de la fila existente.
    throw new RuntimeException( 'El log debería indicar el identificador de la fila existente al actualizar el límite.' ); // Informa si falta el detalle esperado.
}
if ( strpos( $joinedUpdateLog, 'limit_save_error' ) !== false && strpos( $joinedUpdateLog, 'limit_save_complete' ) === false ) { // Comprueba que no se haya registrado un error inesperado sin confirmación de éxito.
    throw new RuntimeException( 'El log no debería registrar un error durante una actualización correcta.' ); // Informa si se detectó un error inconsistente.
}
$wpdb->expected_rows = array(); // Restablece las filas simuladas para futuras pruebas.

$refUpgradeColumns = new ReflectionMethod( CF7_OptionLimiter_DB::class, 'maybe_upgrade_columns' ); // Prepara el método protegido responsable de ajustar las columnas.
$refUpgradeColumns->setAccessible( true ); // Permite ejecutar el método protegido en el entorno de pruebas.
$wpdb->executed_queries = array(); // Limpia cualquier consulta previa antes de evaluar el renombrado legacy.
$wpdb->get_var_queue = array( 'legacy_column', 'hide_present' ); // Simula que la columna antigua existe y que hide_exhausted ya está disponible.
$refUpgradeColumns->invoke( null ); // Ejecuta la comprobación de columnas con la simulación configurada.
$rename_detected = false; // Inicializa el indicador para confirmar el renombrado de last_reset.
foreach ( $wpdb->executed_queries as $query ) { // Recorre las consultas ejecutadas durante la comprobación.
    if ( is_string( $query ) && strpos( $query, 'CHANGE `last_reset` `limit_reset`' ) !== false ) { // Identifica la sentencia de renombrado.
        $rename_detected = true; // Marca que la operación se ejecutó correctamente.
        break; // Detiene el bucle tras localizar la sentencia esperada.
    }
}
if ( ! $rename_detected ) { // Comprueba que la sentencia de renombrado se haya ejecutado.
    throw new RuntimeException( 'maybe_upgrade_columns debería renombrar last_reset cuando la columna legacy está presente.' ); // Lanza una excepción descriptiva si no se detectó la operación.
}
$wpdb->executed_queries = array(); // Limpia las consultas para la siguiente simulación.
$wpdb->get_var_queue = array( null, null ); // Simula un escenario donde ninguna de las columnas existe todavía.
$refUpgradeColumns->invoke( null ); // Ejecuta nuevamente el método para evaluar la adición de la columna moderna.
$add_detected = false; // Inicializa el indicador para confirmar la adición de hide_exhausted.
foreach ( $wpdb->executed_queries as $query ) { // Recorre las consultas ejecutadas durante la segunda comprobación.
    if ( is_string( $query ) && strpos( $query, 'ADD `hide_exhausted` TINYINT(1) NOT NULL DEFAULT 0' ) !== false ) { // Busca la sentencia de adición específica.
        $add_detected = true; // Marca que la columna se añadió correctamente.
        break; // Detiene el bucle después de localizar la sentencia esperada.
    }
}
if ( ! $add_detected ) { // Comprueba que la columna se haya añadido cuando no existía.
    throw new RuntimeException( 'maybe_upgrade_columns debería añadir hide_exhausted cuando la columna no existe.' ); // Lanza una excepción informativa si la operación no se ejecutó.
}
$wpdb->get_var_queue = array(); // Restablece la cola para no interferir con el resto de pruebas.

CF7_OptionLimiter_Logger::set_debug_mode( true ); // Activa el modo depuración para verificar el almacenamiento de la preferencia.
if ( ! get_option( CF7_OptionLimiter_Logger::DEBUG_OPTION ) ) { // Comprueba que la opción persistente refleje el estado activo.
    throw new RuntimeException( 'set_debug_mode debería activar la opción persistente cuando se solicita.' ); // Informa si la opción no se almacenó correctamente.
}
CF7_OptionLimiter_Logger::set_debug_mode( false ); // Desactiva el modo depuración para completar la comprobación.
$GLOBALS['cf7_option_limiter_last_redirect'] = ''; // Restablece la variable global de redirección antes de iniciar la prueba del guardado inválido.
CF7_OptionLimiter_Logger::set_debug_mode( true ); // Activa el modo depuración para asegurar que el rechazo quede registrado en el log.
$wpdb->get_row_calls = 0; // Reinicia el contador de lecturas directas en la base de datos.
$wpdb->insert_called = false; // Asegura que no quede marcado un guardado previo antes de la prueba.
$_POST = array( // Construye la petición simulada sin los campos obligatorios para validar la nueva salvaguarda.
    'cf7_option_limiter_nonce' => 'nonce', // Incluye un nonce ficticio para atravesar la verificación simulada.
    'form_id'                  => '0', // Envía un identificador nulo para provocar el rechazo.
    'field_name'               => '', // Deja el nombre del campo vacío para comprobar la validación.
    'option_value'             => '', // Deja el valor de la opción vacío para completar el escenario inválido.
    'redirect_to'              => 'admin.php?page=cf7-option-limiter', // Proporciona la URL de retorno esperada en el administrador.
);
try { // Inicia el bloque de prueba capturando la excepción de redirección.
    CF7_OptionLimiter_Admin::handle_save(); // Ejecuta el guardado con datos incompletos esperando que sea rechazado.
    throw new RuntimeException( 'handle_save debería lanzar una redirección cuando faltan datos obligatorios.' ); // Lanza una excepción si no se produjo la redirección esperada.
} catch ( CF7_Option_Limiter_Test_Redirect $redirect ) { // Captura la redirección simulada para validar su contenido.
    // La excepción contiene la URL de destino, pero el análisis se realiza sobre la variable global almacenada.
}
if ( empty( $GLOBALS['cf7_option_limiter_last_redirect'] ) ) { // Comprueba que la URL se haya almacenado globalmente.
    throw new RuntimeException( 'El guardado inválido debería registrar la URL de redirección en la variable global.' ); // Falla la prueba si no se almacenó la redirección.
}
if ( strpos( $GLOBALS['cf7_option_limiter_last_redirect'], 'ol_notice=error' ) === false || strpos( $GLOBALS['cf7_option_limiter_last_redirect'], 'ol_error=incomplete' ) === false ) { // Verifica que la URL contenga los parámetros de error esperados.
    throw new RuntimeException( 'La redirección por datos incompletos debería incluir los parámetros ol_notice=error y ol_error=incomplete.' ); // Informa si faltan los parámetros que identifican el error.
}
if ( $wpdb->get_row_calls > 0 ) { // Comprueba que no se haya consultado la base de datos cuando los datos eran inválidos.
    throw new RuntimeException( 'handle_save no debería consultar la base de datos cuando rechaza una petición por datos incompletos.' ); // Lanza una excepción si se detectó alguna consulta.
}
if ( $wpdb->insert_called ) { // Comprueba que no se haya intentado insertar un registro cuando la petición se rechazó.
    throw new RuntimeException( 'handle_save no debería intentar insertar datos tras rechazar una petición incompleta.' ); // Informa del fallo si se detecta una inserción.
}
$recent_rejection_log = CF7_OptionLimiter_Logger::get_recent_lines( 10 ); // Recupera las últimas líneas del log para localizar el registro del rechazo.
$found_rejection_event = false; // Inicializa la bandera que confirmará la existencia del evento en el log.
foreach ( $recent_rejection_log as $log_line ) { // Recorre cada línea devuelta para analizarla.
    if ( strpos( $log_line, 'limit_admin_rejected' ) !== false ) { // Comprueba si la línea corresponde al nuevo evento registrado.
        $found_rejection_event = true; // Marca que se encontró el evento esperado en el log.
        break; // Detiene el recorrido tras localizar el evento.
    }
}
if ( ! $found_rejection_event ) { // Comprueba el resultado final de la búsqueda en el log.
    throw new RuntimeException( 'El rechazo por datos incompletos debería dejar una entrada limit_admin_rejected en el log cuando la depuración está activa.' ); // Lanza una excepción descriptiva si no se encontró la entrada.
}
CF7_OptionLimiter_Logger::set_debug_mode( false ); // Restaura el estado del modo depuración tras completar la prueba.
$_POST = array(); // Limpia los datos enviados para no interferir con otras pruebas potenciales.
if ( get_option( CF7_OptionLimiter_Logger::DEBUG_OPTION ) ) { // Verifica que la opción se restablezca tras desactivar el modo.
    throw new RuntimeException( 'set_debug_mode debería desactivar la opción persistente cuando se solicita.' ); // Lanza excepción si la opción permanece activa.
}
$wpdb->expected_row_queue = array(); // Limpia cualquier cola previa antes de iniciar las pruebas específicas de AJAX.

$_POST = array( // Construye una petición AJAX sin los datos obligatorios para validar la respuesta de error.
    'nonce'        => 'nonce', // Incluye el nonce esperado por el endpoint AJAX.
    'form_id'      => '0', // Omite el identificador de formulario para provocar el error de validación.
    'field_name'   => '', // Omite el nombre del campo.
    'option_value' => '', // Omite el valor de la opción.
);
try {
    CF7_OptionLimiter_Admin::ajax_save_rule(); // Ejecuta el endpoint esperando que lance la excepción personalizada.
    throw new RuntimeException( 'ajax_save_rule debería lanzar una respuesta JSON de error cuando faltan datos obligatorios.' ); // Falla la prueba si no se lanza la excepción.
} catch ( CF7_Option_Limiter_Test_JSON_Response $response ) {
    if ( $response->success ) { // Comprueba que la respuesta represente un error.
        throw new RuntimeException( 'ajax_save_rule no debería marcar la respuesta como éxito cuando faltan los campos requeridos.' ); // Informa si la respuesta se marcó como éxito.
    }
    if ( empty( $response->payload['code'] ) || 'incomplete' !== $response->payload['code'] ) { // Verifica que el código de error devuelto sea el esperado.
        throw new RuntimeException( 'ajax_save_rule debería devolver el código de error "incomplete" cuando faltan los datos requeridos.' ); // Lanza excepción si el código devuelto no coincide.
    }
}

$wpdb->expected_row_queue = array( // Configura la cola de respuestas de get_row para simular un guardado sin conflictos.
    null, // La primera llamada a get_limit debe indicar que no existe una regla previa.
    null, // La segunda llamada corresponde al control interno de upsert_limit antes de persistir y también debe indicar ausencia de regla previa.
    array( // La tercera llamada recuperará la fila recién guardada para construir la respuesta JSON.
        'id'            => 21,
        'form_id'       => 3,
        'field_name'    => 'turno',
        'option_value'  => 'mañana',
        'max_count'     => 3,
        'current_count' => 1,
        'limit_period'  => 'day',
        'custom_message'=> 'Mensaje personalizado',
        'hide_exhausted'=> 1,
        'limit_reset'   => '2024-01-01 10:00:00',
        'created_at'    => '2024-01-01 09:00:00',
    ),
);
$wpdb->replace_called = false; // Restablece el indicador que confirma la escritura en la base de datos.
$_POST = array( // Construye la petición AJAX completa con todos los datos obligatorios.
    'nonce'                => 'nonce', // Incluye el nonce esperado por el endpoint AJAX.
    'form_id'              => '3', // Selecciona el formulario con identificador 3.
    'field_name'           => 'turno', // Indica el campo que se está limitando.
    'option_value'         => 'mañana', // Indica la opción específica a limitar.
    'max_count'            => '3', // Define el máximo permitido.
    'limit_period'         => 'day', // Define el periodo de reinicio.
    'custom_message'       => 'Mensaje personalizado', // Proporciona un mensaje personalizado.
    'hide_exhausted'       => '1', // Solicita ocultar la opción al agotarse.
    'rule_id'              => '0', // Indica que se trata de una nueva regla.
    'original_form_id'     => '0', // No se proporciona regla previa.
    'original_field_name'  => '', // No se proporciona campo original.
    'original_option_value'=> '', // No se proporciona opción original.
    'redirect_to'          => 'admin.php?page=wpcf7', // Incluye la URL de retorno utilizada por el flujo tradicional.
);
try {
    CF7_OptionLimiter_Admin::ajax_save_rule(); // Ejecuta el endpoint esperando una respuesta satisfactoria.
    throw new RuntimeException( 'ajax_save_rule debería lanzar una respuesta JSON capturable durante las pruebas.' ); // Lanza excepción si no se interceptó la respuesta.
} catch ( CF7_Option_Limiter_Test_JSON_Response $response ) {
    if ( ! $response->success ) { // Comprueba que la respuesta represente un éxito.
        throw new RuntimeException( 'ajax_save_rule debería considerar exitosa la petición cuando se proporcionan todos los datos válidos.' ); // Falla la prueba si la respuesta se marcó como error.
    }
    if ( empty( $response->payload['notice_code'] ) || 'created' !== $response->payload['notice_code'] ) { // Verifica que el código de notificación corresponda a una creación.
        throw new RuntimeException( 'ajax_save_rule debería devolver el código de notificación "created" tras guardar una nueva regla.' ); // Informa si el código no coincide con lo esperado.
    }
    if ( empty( $response->payload['rule'] ) || $response->payload['rule']['field_name'] !== 'turno' ) { // Comprueba que la respuesta incluya la fila recién guardada.
        throw new RuntimeException( 'ajax_save_rule debería incluir la fila recién guardada dentro de la respuesta JSON.' ); // Falla la prueba si falta la fila o los datos no coinciden.
    }
    if ( empty( $response->payload['rule']['hide_label'] ) || strpos( $response->payload['rule']['hide_label'], 'Sí' ) === false ) { // Verifica que la etiqueta de ocultación se haya traducido correctamente.
        throw new RuntimeException( 'La respuesta JSON debería incluir la etiqueta traducida para indicar que la opción se ocultará al agotarse.' ); // Lanza excepción si la etiqueta no está presente.
    }
}
if ( ! $wpdb->insert_called ) { // Comprueba que la operación de guardado haya utilizado insert al crear una nueva regla.
    throw new RuntimeException( 'upsert_limit debería invocar insert durante el guardado ejecutado por AJAX cuando la regla no existe.' ); // Lanza excepción si no se registró la operación de escritura esperada.
}
$wpdb->insert_called = false; // Restablece el indicador de inserción para evitar interferencias con las pruebas posteriores.
$wpdb->expected_row_queue = array(); // Limpia la cola tras completar la prueba para evitar interferencias futuras.
$_POST = array(); // Restablece los datos enviados para no interferir con las pruebas siguientes.

$uploads_info = wp_upload_dir(); // Recupera la información simulada del directorio de subidas.
$expected_directory = trailingslashit( $uploads_info['basedir'] ) . 'cf7-option-limiter'; // Calcula el directorio dedicado que debería crear el logger.
CF7_OptionLimiter_Logger::init(); // Garantiza que el archivo de log exista antes de manipularlo en la prueba.
$log_path = CF7_OptionLimiter_Logger::get_log_file_path(); // Recupera la ruta real del archivo de log creado durante la inicialización.
if ( empty( $log_path ) ) { // Comprueba que se haya determinado una ruta válida.
    throw new RuntimeException( 'init debería exponer la ruta del archivo de log mediante get_log_file_path.' ); // Informa si el logger no pudo preparar el archivo.
}
if ( strpos( $log_path, $expected_directory ) === false ) { // Verifica que el archivo se encuentre dentro del directorio de subidas simulado.
    throw new RuntimeException( 'El archivo de log debería almacenarse dentro del directorio de subidas preparado para las pruebas.' ); // Lanza excepción si se utilizó otra ubicación.
}
if ( CF7_OptionLimiter_Logger::get_last_error() ) { // Comprueba que no se hayan registrado errores durante la inicialización.
    throw new RuntimeException( 'get_last_error debería devolver una cadena vacía cuando el archivo de log está disponible.' ); // Informa cuando persiste un error inesperado.
}
file_put_contents( $log_path, '' ); // Limpia el archivo para que la prueba sólo analice la nueva entrada generada.
CF7_OptionLimiter_Logger::log( 'limit_saved', array( 'form_id' => 9, 'field_name' => 'turno', 'option_value' => 'tarde', 'success' => true ), true ); // Registra un evento forzado para comprobar el formato en texto plano.
$log_lines = file( $log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ); // Recupera todas las líneas almacenadas en el archivo tras el registro forzado.
if ( empty( $log_lines ) ) { // Comprueba que la escritura se haya realizado correctamente.
    throw new RuntimeException( 'El log debería contener la entrada recién escrita.' ); // Lanza excepción si no se registró ninguna línea.
}
$last_line = trim( end( $log_lines ) ); // Obtiene la última línea del archivo para validar su formato.
if ( strpos( $last_line, '{' ) !== false || strpos( $last_line, '}' ) !== false ) { // Comprueba que no queden restos de formato JSON en la salida.
    throw new RuntimeException( 'El log debería estar formateado como texto plano sin llaves JSON.' ); // Informa si se detectan caracteres propios del formato anterior.
}
if ( strpos( $last_line, 'limit_saved' ) === false || strpos( $last_line, 'form_id=9' ) === false ) { // Asegura que la línea incluya el nombre del evento y el contexto esencial.
    throw new RuntimeException( 'El log debería incluir el nombre del evento y las claves principales del contexto.' ); // Lanza excepción cuando falta información clave.
}
$recent_lines = CF7_OptionLimiter_Logger::get_recent_lines( 1 ); // Recupera la última línea utilizando el nuevo método público de lectura.
if ( count( $recent_lines ) !== 1 || trim( $recent_lines[0] ) !== $last_line ) { // Comprueba que el método devuelva exactamente la línea esperada.
    throw new RuntimeException( 'get_recent_lines debería devolver la misma entrada almacenada más recientemente.' ); // Informa cuando la lectura no coincide con el contenido real del archivo.
}
CF7_OptionLimiter_Logger::delete_logs(); // Elimina el archivo de log y cualquier rotación residual para comprobar la limpieza.
if ( file_exists( $log_path ) ) { // Comprueba que el archivo haya sido eliminado correctamente.
    throw new RuntimeException( 'delete_logs debería eliminar el archivo de log cuando se invoca durante las pruebas.' ); // Lanza excepción si el archivo permanece en disco.
}

$schema_signature = get_option( CF7_OptionLimiter_DB::SCHEMA_SIGNATURE_OPTION ); // Recupera la firma del esquema almacenada tras la inicialización.
if ( empty( $schema_signature ) ) { // Comprueba que la firma se haya registrado.
    throw new RuntimeException( 'init debería almacenar la firma del esquema aplicado para detectar cambios futuros.' ); // Informa si no se guardó el hash esperado.
}
if ( empty( $GLOBALS['cf7_option_limiter_dbdelta_calls'] ) ) { // Comprueba que la sincronización del esquema haya ejecutado dbDelta al menos una vez.
    throw new RuntimeException( 'ensure_schema_alignment debería ejecutar dbDelta para crear o sincronizar la tabla personalizada.' ); // Lanza excepción si no se registró ninguna llamada.
}
$GLOBALS['cf7_option_limiter_dbdelta_calls'] = array(); // Limpia el registro de invocaciones para evitar interferencias con pruebas posteriores.

$validationResult = new WPCF7_ValidationResult(); // Crea un resultado de validación simulado.
$tagObject = (object) array( 'name' => 'turno' ); // Construye una etiqueta simplificada con el nombre del campo.
$submissionMock = new WPCF7_Submission(); // Crea una sumisión simulada.
$submissionMock->posted_data = array( 'turno' => 'turno-b' ); // Configura los datos enviados con una opción agotada.
$submissionMock->form = new WPCF7_ContactForm( 7 ); // Asocia el formulario con identificador 7.
WPCF7_Submission::$instance = $submissionMock; // Registra la sumisión simulada como instancia activa.
$wpdb->expected_rows = array( // Configura el mapa de filas devuelto por get_limit.
    'turno-b' => array( // Define la fila asociada al valor turno-b.
        'max_count'     => 2, // Máximo configurado.
        'current_count' => 2, // Contador que alcanza el máximo, provocando agotamiento.
        'custom_message'=> 'Mensaje turno B', // Mensaje personalizado que debe propagarse al error.
    ),
); // Finaliza la configuración de filas simuladas.
CF7_OptionLimiter_Limiter::validate_choice( $validationResult, $tagObject ); // Ejecuta la validación sobre la opción agotada.
if ( count( $validationResult->invalid ) !== 1 ) { // Comprueba que se haya invalidado el campo exactamente una vez.
    throw new RuntimeException( 'validate_choice debería marcar el campo como inválido cuando se agota el límite.' ); // Lanza excepción si no se registró el error esperado.
}
if ( $validationResult->invalid[0]['message'] !== 'Mensaje turno B' ) { // Verifica que se utilice el mensaje personalizado configurado.
    throw new RuntimeException( 'validate_choice no devolvió el mensaje personalizado esperado.' ); // Falla la prueba si el mensaje difiere.
}

$validationSuccess = new WPCF7_ValidationResult(); // Crea un nuevo resultado para comprobar una opción disponible.
$submissionMock->posted_data = array( 'turno' => 'turno-a' ); // Cambia los datos enviados a una opción con disponibilidad.
$wpdb->expected_rows = array( // Ajusta el mapa de filas para simular disponibilidad.
    'turno-a' => array( // Define la fila correspondiente a turno-a.
        'max_count'     => 5, // Máximo permitido.
        'current_count' => 1, // Contador actual inferior al máximo.
        'custom_message'=> '', // No se establece mensaje personalizado para esta opción.
    ),
); // Finaliza la configuración.
CF7_OptionLimiter_Limiter::validate_choice( $validationSuccess, $tagObject ); // Ejecuta la validación sobre la opción disponible.
if ( ! empty( $validationSuccess->invalid ) ) { // Comprueba que no se hayan registrado errores.
    throw new RuntimeException( 'validate_choice no debería marcar errores cuando la opción sigue disponible.' ); // Lanza excepción si aparece un error inesperado.
}
WPCF7_Submission::$instance = null; // Restablece la instancia de sumisión para limpiar el estado global.

$renderTag = new stdClass(); // Crea una etiqueta simulada para probar el filtrado en el renderizado.
$renderTag->basetype = 'select'; // Establece el tipo de campo compatible.
$renderTag->name = 'plaza'; // Asigna el nombre del campo limitado.
$renderTag->values = array( 'visible', 'oculta', 'libre' ); // Define tres valores simulados.
$renderTag->labels = array( 'Visible', 'Oculta', 'Libre' ); // Define las etiquetas legibles asociadas a cada valor.
$wpdb->expected_rows = array( // Configura las reglas simuladas que devolverá la capa de datos durante el renderizado.
    '0|plaza|visible' => array( 'max_count' => 1, 'current_count' => 1, 'custom_message' => 'Mensaje visible', 'hide_exhausted' => 0 ), // Regla que mantiene visible la opción agotada.
    '0|plaza|oculta'  => array( 'max_count' => 1, 'current_count' => 1, 'custom_message' => 'Mensaje oculto', 'hide_exhausted' => 1 ), // Regla que solicita ocultar la opción agotada.
); // Finaliza la configuración de reglas simuladas.
$renderedTag = CF7_OptionLimiter_Limiter::filter_form_tag( $renderTag ); // Ejecuta el filtrado para simular el renderizado del formulario.
if ( in_array( 'oculta', $renderedTag->values, true ) ) { // Comprueba que la opción marcada para ocultarse haya sido eliminada.
    throw new RuntimeException( 'filter_form_tag debería ocultar las opciones configuradas con hide_exhausted.' ); // Falla la prueba si la opción permanece visible.
}
$visiblePosition = array_search( 'visible', $renderedTag->values, true ); // Localiza la posición de la opción visible.
if ( false === $visiblePosition ) { // Comprueba que la opción visible siga estando disponible.
    throw new RuntimeException( 'filter_form_tag debería mantener la opción visible cuando hide_exhausted está desactivado.' ); // Lanza excepción si la opción desaparece.
}
if ( strpos( $renderedTag->labels[ $visiblePosition ], 'agotada temporalmente' ) === false ) { // Verifica que la etiqueta incluya la nota de agotamiento.
    throw new RuntimeException( 'filter_form_tag debería añadir la nota informativa cuando la opción agotada permanece visible.' ); // Falla si la etiqueta no refleja el nuevo comportamiento.
}
$wpdb->expected_rows = array(); // Restablece el mapa de reglas para no interferir con pruebas futuras.
$wpdb->expected_rows = array(); // Limpia el mapa de filas simuladas.

$overridesList = CF7_OptionLimiter_DB::get_all_overrides(); // Recupera todas las excepciones disponibles.
if ( ! empty( $overridesList ) ) { // Comprueba que el listado esté vacío tras retirar la característica.
    throw new RuntimeException( 'get_all_overrides debería devolver un arreglo vacío tras eliminar las excepciones por página.' ); // Lanza excepción si aún se devuelven registros.
}
if ( null !== CF7_OptionLimiter_DB::get_override( 1, 'habitacion', 'suite', 99 ) ) { // Solicita una excepción concreta.
    throw new RuntimeException( 'get_override debería devolver null cuando las excepciones están deshabilitadas.' ); // Falla si se devuelve algún dato.
}
if ( ! empty( CF7_OptionLimiter_DB::get_overrides_for_field( 1, 'habitacion' ) ) ) { // Recupera todas las excepciones por campo.
    throw new RuntimeException( 'get_overrides_for_field debería devolver un arreglo vacío tras la eliminación de excepciones.' ); // Lanza excepción si existen resultados.
}
if ( ! empty( CF7_OptionLimiter_DB::get_overrides_for_options( 1, 'habitacion', array( 'suite' ), 99 ) ) ) { // Recupera excepciones en bloque.
    throw new RuntimeException( 'get_overrides_for_options debería devolver un arreglo vacío tras retirar la funcionalidad.' ); // Informa si aún se devuelven datos.
}
if ( CF7_OptionLimiter_DB::upsert_override( array() ) ) { // Intenta guardar una excepción.
    throw new RuntimeException( 'upsert_override debería devolver false porque las excepciones ya no se almacenan.' ); // Falla si se considera exitosa la operación.
}
if ( CF7_OptionLimiter_DB::increment_override_counter( 1, 'habitacion', 'suite', 99 ) ) { // Intenta incrementar un contador de excepción inexistente.
    throw new RuntimeException( 'increment_override_counter debería devolver false al haber retirado las excepciones.' ); // Lanza excepción si la operación aparenta éxito.
}

$wpdb->expected_rows = array( '1|habitacion|suite' => array( // Configura la fila global que devolverá get_limit.
    'max_count'     => 5, // Máximo global permitido.
    'current_count' => 4, // Contador global registrado.
    'custom_message'=> 'Global', // Mensaje asociado a la regla global.
    'limit_period'  => 'day', // Periodo configurado en la regla global.
) ); // Finaliza la configuración de filas simuladas.
$effectiveLimit = CF7_OptionLimiter_DB::get_effective_limit( 1, 'habitacion', 'suite', 99 ); // Recupera la regla efectiva para un contexto de página concreto.
if ( $effectiveLimit['custom_message'] !== 'Global' || $effectiveLimit['source'] !== 'global' ) { // Comprueba que la respuesta proceda de la regla global.
    throw new RuntimeException( 'get_effective_limit debería basarse en la regla global tras retirar las excepciones por página.' ); // Lanza excepción si el origen no es global.
}

$wpdb->expected_results = array( // Configura las filas devueltas por get_limits_for_options.
    array( 'option_value' => 'suite', 'max_count' => 5, 'current_count' => 4, 'custom_message' => 'Global', 'limit_period' => 'day' ), // Primera opción recuperada.
    array( 'option_value' => 'premium', 'max_count' => 8, 'current_count' => 2, 'custom_message' => '', 'limit_period' => 'none' ), // Segunda opción recuperada.
); // Finaliza la configuración del conjunto devuelto.
$effectiveMap = CF7_OptionLimiter_DB::get_effective_limits_for_options( 1, 'habitacion', array( 'suite', 'premium' ), 99 ); // Recupera las reglas efectivas para dos valores.
if ( $effectiveMap['suite']['source'] !== 'global' || $effectiveMap['premium']['source'] !== 'global' ) { // Comprueba que ambas entradas indiquen origen global.
    throw new RuntimeException( 'get_effective_limits_for_options debería devolver únicamente reglas globales tras eliminar las excepciones.' ); // Falla si se detecta otro origen.
}
$wpdb->expected_results = array(); // Limpia los resultados simulados para evitar interferencias.

$wpdb->query_return_value = 1; // Garantiza que las actualizaciones simuladas se consideren exitosas.
$wpdb->last_query = ''; // Limpia el registro de la última consulta ejecutada.
CF7_OptionLimiter_DB::increment_counter_for_context( 1, 'habitacion', 'suite', 99 ); // Incrementa el contador para la regla global.
if ( strpos( $wpdb->last_query, 'cf7_option_limits' ) === false || strpos( $wpdb->last_query, 'overrides' ) !== false ) { // Comprueba que la actualización se haga sobre la tabla principal.
    throw new RuntimeException( 'increment_counter_for_context debería operar exclusivamente sobre la tabla principal de límites.' ); // Lanza excepción si se detecta la tabla de overrides.
}

$wpdb->query_return_value = 1; // Asegura que las operaciones de actualización simuladas se consideren exitosas.
$wpdb->last_query = ''; // Limpia la última consulta registrada para inspeccionar únicamente el decremento manual.
if ( ! CF7_OptionLimiter_DB::decrement_counter_by_id( 12 ) ) { // Intenta liberar un uso con un identificador válido.
    throw new RuntimeException( 'decrement_counter_by_id debería devolver true cuando la consulta se ejecuta correctamente.' ); // Informa si la operación no se considera exitosa.
}
if ( strpos( $wpdb->last_query, 'GREATEST' ) === false || strpos( $wpdb->last_query, 'WHERE id = %d' ) === false ) { // Comprueba que la consulta utilice el límite inferior y el filtro por ID parametrizado.
    throw new RuntimeException( 'decrement_counter_by_id debería actualizar el contador utilizando GREATEST y aplicar el filtro por ID.' ); // Lanza excepción cuando la consulta no contiene los fragmentos esperados.
}
if ( CF7_OptionLimiter_DB::decrement_counter_by_id( 0 ) ) { // Intenta liberar un uso con un identificador inválido.
    throw new RuntimeException( 'decrement_counter_by_id debería devolver false cuando el identificador es menor o igual a cero.' ); // Informa si la validación del identificador no se aplica correctamente.
}

$wpdb->expected_results = array( // Configura una regla con periodo horario para probar el reseteo automático.
    array( 'id' => 5, 'form_id' => 2, 'field_name' => 'turno', 'option_value' => 'manana', 'limit_period' => 'hour', 'limit_reset' => gmdate( 'Y-m-d H:i:s', time() - 7200 ), 'post_id' => 0 ), // Fila que requiere reinicio inmediato.
); // Finaliza la configuración de reglas a resetear.
$wpdb->query_return_value = 1; // Simula que la actualización se ejecutará correctamente.
$wpdb->last_query = array(); // Limpia el registro previo almacenado por update.
CF7_OptionLimiter_DB::reset_periods(); // Ejecuta el reseteo de periodos sobre la tabla principal.
if ( empty( $wpdb->last_query ) || $wpdb->last_query['table'] !== 'wp_cf7_option_limits' ) { // Comprueba que la actualización se haya aplicado sobre la tabla principal.
    throw new RuntimeException( 'reset_periods debería actualizar únicamente la tabla global de límites.' ); // Lanza excepción si la tabla no coincide.
}
$wpdb->expected_results = array(); // Limpia las reglas simuladas para futuras pruebas.

$localization = CF7_OptionLimiter_Admin::get_localization_data(); // Recupera la configuración localizada utilizada por el script administrativo.
if ( empty( $localization['ajaxUrl'] ) || strpos( $localization['ajaxUrl'], 'admin-ajax.php' ) === false ) { // Comprueba que la URL devuelta apunte al endpoint AJAX.
    throw new RuntimeException( 'get_localization_data debería exponer la URL de admin-ajax.php.' ); // Lanza excepción si la URL no es la esperada.
}
if ( empty( $localization['adminPostUrl'] ) || strpos( $localization['adminPostUrl'], 'admin-post.php' ) === false ) { // Comprueba que la configuración localizada incluya la URL del endpoint tradicional.
    throw new RuntimeException( 'get_localization_data debería exponer la URL de admin-post.php para que los formularios ocultos puedan recrearse dinámicamente.' ); // Informa si falta la URL necesaria para el flujo clásico.
}
if ( empty( $localization['embeddedNonces']['save'] ) || empty( $localization['embeddedNonces']['delete'] ) || empty( $localization['embeddedNonces']['release'] ) ) { // Verifica que se localicen los nonces utilizados por los formularios ocultos.
    throw new RuntimeException( 'get_localization_data debería incluir los nonces de guardado, borrado y liberación dentro de embeddedNonces.' ); // Informa si falta alguno de los nonces requeridos para recrear los formularios.
}
if ( empty( $localization['i18n']['updateLabel'] ) || $localization['i18n']['updateLabel'] !== 'Actualizar límite' ) { // Verifica que los textos traducibles incluyan la etiqueta de actualización.
    throw new RuntimeException( 'get_localization_data debería incluir el texto de actualización para el modo edición.' ); // Informa del fallo cuando falta la etiqueta.
}
$redirect_reflection = new ReflectionMethod( CF7_OptionLimiter_Admin::class, 'build_redirect_with_context' ); // Prepara la reflexión para probar el método auxiliar que conserva la URL de retorno.
$redirect_reflection->setAccessible( true ); // Marca el método como accesible para poder invocarlo en el contexto de pruebas.
$redirect_with_context = $redirect_reflection->invoke( null, array( 'form_filter' => ' 7 ', 'ol_page' => '3', 'irrelevant' => 'valor' ) ); // Invoca el método simulando una petición con filtro y paginación.
if ( strpos( $redirect_with_context, 'form_filter=7' ) === false || strpos( $redirect_with_context, 'ol_page=3' ) === false ) { // Comprueba que la URL resultante preserve ambos parámetros relevantes.
    throw new RuntimeException( 'build_redirect_with_context debería conservar form_filter y ol_page cuando se proporcionan valores válidos.' ); // Informa del fallo si la URL no contiene los parámetros esperados.
}
$redirect_default = $redirect_reflection->invoke( null, array( 'form_filter' => '0', 'ol_page' => '1' ) ); // Invoca el método con valores que representan el estado por defecto.
if ( strpos( $redirect_default, 'form_filter=' ) !== false || strpos( $redirect_default, 'ol_page=' ) !== false ) { // Comprueba que la URL limpia no conserve parámetros innecesarios.
    throw new RuntimeException( 'build_redirect_with_context no debería añadir parámetros cuando se utilizan los valores por defecto.' ); // Informa si la URL contiene parámetros redundantes.
}

$_POST = array( // Prepara la petición que simula la liberación manual desde la tabla administrativa.
    'rule_id'                           => 15, // Identificador de la regla que se pretende ajustar.
    'redirect_to'                       => 'admin.php?page=' . CF7_OptionLimiter_Admin::MENU_SLUG, // URL relativa de retorno tras completar la acción.
    'cf7_option_limiter_release_nonce'  => 'nonce', // Valor ficticio que supera la validación del nonce en el entorno de pruebas.
    'action'                            => 'cf7_option_limiter_release', // Acción asociada al formulario oculto.
);
$wpdb->expected_row = array( // Configura la regla que devolverá get_limit_by_id durante la prueba.
    'id'            => 15, // Identificador de la regla simulada.
    'form_id'       => 4, // Identificador del formulario asociado.
    'field_name'    => 'turno', // Nombre del campo limitado.
    'option_value'  => 'manana', // Valor concreto de la opción.
    'current_count' => 3, // Contador actual que permitirá restar una unidad.
);
$wpdb->query_return_value = 1; // Asegura que la actualización del contador se considere exitosa.
$wpdb->last_query = ''; // Limpia la última consulta registrada para verificar el decremento ejecutado por el manejador.
try { // Captura la redirección lanzada por wp_safe_redirect dentro de la prueba.
    CF7_OptionLimiter_Admin::handle_release(); // Ejecuta la acción administrativa que libera un uso.
    throw new RuntimeException( 'handle_release debería provocar una redirección tras completar la operación.' ); // Informa si la redirección no se produjo.
} catch ( CF7_Option_Limiter_Test_Redirect $redirect ) { // Intercepta la redirección simulada.
    $redirect_url = $redirect->getMessage(); // Recupera la URL objetivo utilizada en la redirección.
    if ( strpos( $redirect_url, 'ol_notice=released' ) === false ) { // Comprueba que la notificación indique un decremento exitoso.
        throw new RuntimeException( 'handle_release debería redirigir con ol_notice=released cuando el contador se reduce correctamente.' ); // Lanza excepción si falta la notificación esperada.
    }
}
if ( strpos( $wpdb->last_query, 'GREATEST' ) === false || strpos( $wpdb->last_query, 'WHERE id = %d' ) === false ) { // Comprueba que el manejador utilizó la consulta preparada del decremento.
    throw new RuntimeException( 'handle_release debería invocar decrement_counter_by_id utilizando la consulta con GREATEST y filtro parametrizado.' ); // Informa si la consulta no coincide con la esperada.
}
if ( empty( $GLOBALS['cf7_option_limiter_last_nonce_action'] ) || 'cf7_option_limiter_release' !== $GLOBALS['cf7_option_limiter_last_nonce_action'] ) { // Verifica que se haya validado el nonce correcto.
    throw new RuntimeException( 'handle_release debería validar el nonce cf7_option_limiter_release antes de procesar la petición.' ); // Lanza excepción si la acción almacenada no coincide.
}

$_POST = array( // Prepara un segundo escenario donde el contador ya está en cero.
    'rule_id'                           => 16, // Identificador distinto para la segunda prueba.
    'redirect_to'                       => 'admin.php?page=' . CF7_OptionLimiter_Admin::MENU_SLUG, // Mantiene la misma URL de retorno relativa.
    'cf7_option_limiter_release_nonce'  => 'nonce', // Valor ficticio aceptado por la validación del nonce.
    'action'                            => 'cf7_option_limiter_release', // Acción asociada al formulario oculto.
);
$wpdb->expected_row = array( // Configura la regla con contador agotado que devolverá get_limit_by_id.
    'id'            => 16, // Identificador de la regla simulada.
    'form_id'       => 4, // Identificador del formulario asociado.
    'field_name'    => 'turno', // Nombre del campo limitado.
    'option_value'  => 'tarde', // Valor de la opción que ya no registra usos pendientes.
    'current_count' => 0, // Contador en cero que debe impedir el decremento.
);
$wpdb->last_query = 'sin_cambios'; // Define un marcador que permitirá comprobar si se intentó ejecutar una actualización.
try { // Captura la redirección generada por el manejador ante la falta de usos disponibles.
    CF7_OptionLimiter_Admin::handle_release(); // Ejecuta nuevamente la acción administrativa.
    throw new RuntimeException( 'handle_release debería redirigir incluso cuando no hay usos que liberar.' ); // Informa si no se produjo la redirección esperada.
} catch ( CF7_Option_Limiter_Test_Redirect $redirect ) { // Intercepta la redirección simulada.
    $redirect_url = $redirect->getMessage(); // Recupera la URL objetivo utilizada.
    if ( strpos( $redirect_url, 'ol_notice=release_failed' ) === false ) { // Comprueba que se muestre la notificación de fallo.
        throw new RuntimeException( 'handle_release debería redirigir con ol_notice=release_failed cuando el contador ya está en cero.' ); // Lanza excepción si la notificación no coincide.
    }
}
if ( strpos( (string) $wpdb->last_query, 'GREATEST' ) !== false ) { // Comprueba que no se haya ejecutado la consulta de decremento sobre un contador agotado.
    throw new RuntimeException( 'handle_release no debería llamar a decrement_counter_by_id cuando el contador ya está en cero.' ); // Informa si se detecta la consulta de actualización pese a la ausencia de usos disponibles.
}
unset( $_POST ); // Limpia la superglobal para no interferir con el resto de pruebas.

$admin_reflection = new ReflectionClass( 'CF7_OptionLimiter_Admin' ); // Crea una instancia de reflexión para acceder a los métodos protegidos de la clase administrativa.
$release_form_method = $admin_reflection->getMethod( 'render_release_form' ); // Localiza el método que genera el formulario de liberación.
$release_form_method->setAccessible( true ); // Permite invocar el método protegido dentro del entorno de pruebas.
$release_markup = $release_form_method->invoke( null, array( 'id' => 21, 'current_count' => 3 ), 'https://example.com/reglas' ); // Genera el marcado del formulario cuando existen usos disponibles para liberar.
if ( strpos( $release_markup, 'dashicons-unlock' ) === false ) { // Comprueba que el botón incluya el icono esperado para liberar usos.
    throw new RuntimeException( 'render_release_form debería imprimir el icono dashicons-unlock en el botón principal.' ); // Lanza excepción si falta el icono requerido.
}
$expected_label_fragment = 'aria-label="' . esc_attr__( 'Liberar un uso reservado manualmente', 'cf7-option-limiter' ) . '"'; // Construye el fragmento que representa la etiqueta accesible esperada.
if ( strpos( $release_markup, $expected_label_fragment ) === false ) { // Valida que la etiqueta accesible esté presente en el marcado.
    throw new RuntimeException( 'render_release_form debería incluir el atributo aria-label descriptivo en el botón principal.' ); // Indica si falta la ayuda accesible.
}
if ( strpos( $release_markup, 'cf7-option-limiter-action-button--disabled' ) !== false ) { // Verifica que no se aplique la clase de estado inactivo cuando existen usos disponibles.
    throw new RuntimeException( 'render_release_form no debería marcar el botón como deshabilitado cuando aún quedan usos que liberar.' ); // Informa si la clase inactiva aparece indebidamente.
}
if ( strpos( $release_markup, 'disabled="disabled"' ) !== false ) { // Comprueba que el atributo disabled no se imprima en el escenario habilitado.
    throw new RuntimeException( 'render_release_form no debería deshabilitar el botón cuando aún quedan usos que liberar.' ); // Mantiene la validación previa adaptada al nuevo marcado.
}
$release_disabled_markup = $release_form_method->invoke( null, array( 'id' => 22, 'current_count' => 0 ), 'https://example.com/reglas' ); // Genera el marcado cuando el contador ya está agotado.
if ( strpos( $release_disabled_markup, 'disabled="disabled"' ) === false ) { // Comprueba que en este caso el botón aparezca deshabilitado.
    throw new RuntimeException( 'render_release_form debería marcar el botón como deshabilitado cuando el contador está en cero.' ); // Indica si falta el atributo esperado en el escenario agotado.
}
if ( strpos( $release_disabled_markup, 'cf7-option-limiter-action-button--disabled' ) === false ) { // Asegura que la clase que matiza el estado visual se imprima cuando no hay usos disponibles.
    throw new RuntimeException( 'render_release_form debería añadir la clase cf7-option-limiter-action-button--disabled cuando el contador está en cero.' ); // Notifica si la clase adicional no se encuentra en el HTML generado.
}

update_option( CF7_OptionLimiter_DB::CLEANUP_OPTION, 'keep' ); // Restablece la preferencia para comprobar que no cambie sin permisos.
$GLOBALS['cf7_option_limiter_capability_result'] = false; // Fuerza un escenario donde el usuario carece de la capacidad para activar plugins.
$_REQUEST = array( // Simula una petición de desactivación sin permisos suficientes.
    'cf7_ol_cleanup' => '1', // Valor que indica que se desea eliminar los registros almacenados.
    'plugin'         => CF7_OPTION_LIMITER_BASENAME, // Identificador del plugin en la petición.
    'action'         => 'deactivate', // Acción individual de desactivación.
    '_wpnonce'       => 'nonce', // Nonce ficticio presente en la URL.
); // Finaliza la simulación de parámetros.
CF7_OptionLimiter_Hooks::capture_cleanup_preference(); // Ejecuta la captura de preferencia para comprobar que respeta los permisos.
if ( 'keep' !== get_option( CF7_OptionLimiter_DB::CLEANUP_OPTION ) ) { // Comprueba que la preferencia permanezca intacta sin permisos.
    throw new RuntimeException( 'capture_cleanup_preference no debería modificar la opción cuando el usuario carece de activate_plugins.' ); // Informa si la preferencia cambió indebidamente.
}
if ( 'activate_plugins' !== $GLOBALS['cf7_option_limiter_last_capability'] ) { // Verifica que la función consultara la capacidad correcta.
    throw new RuntimeException( 'capture_cleanup_preference debería comprobar la capacidad activate_plugins antes de actualizar la opción.' ); // Indica si la capacidad comprobada no coincide.
}
unset( $GLOBALS['cf7_option_limiter_capability_result'] ); // Restablece el control de capacidades para las pruebas siguientes.

update_option( CF7_OptionLimiter_DB::CLEANUP_OPTION, 'keep' ); // Restablece la preferencia para evaluar el caso de nonce inválido.
$GLOBALS['cf7_option_limiter_nonce_should_fail'] = true; // Configura la simulación para que la verificación del nonce falle.
$_REQUEST = array( // Simula una petición con nonce incorrecto.
    'cf7_ol_cleanup' => '1', // Valor que indica que se desea eliminar los registros almacenados.
    'plugin'         => CF7_OPTION_LIMITER_BASENAME, // Identificador del plugin en la petición.
    'action'         => 'deactivate', // Acción individual de desactivación.
    '_wpnonce'       => 'nonce', // Nonce ficticio que se marcará como inválido.
); // Finaliza la simulación de parámetros.
try { // Inicia el bloque que capturará la excepción simulada.
    CF7_OptionLimiter_Hooks::capture_cleanup_preference(); // Ejecuta la captura de preferencia esperando un fallo de nonce.
    throw new RuntimeException( 'capture_cleanup_preference debería detenerse cuando el nonce no supera la validación.' ); // Informa si la validación no lanzó la excepción prevista.
} catch ( RuntimeException $exception ) { // Intercepta la excepción generada por el stub de check_admin_referer.
    if ( 'nonce_failure' !== $exception->getMessage() ) { // Comprueba que el motivo coincida con la simulación de nonce inválido.
        throw $exception; // Propaga excepciones inesperadas para no ocultar fallos reales.
    }
}
if ( 'keep' !== get_option( CF7_OptionLimiter_DB::CLEANUP_OPTION ) ) { // Comprueba que la opción no haya cambiado tras el fallo de nonce.
    throw new RuntimeException( 'capture_cleanup_preference no debería modificar la opción cuando el nonce es inválido.' ); // Informa si la preferencia cambió pese al error.
}
unset( $GLOBALS['cf7_option_limiter_nonce_should_fail'] ); // Restablece la validación de nonce para las pruebas posteriores.

update_option( CF7_OptionLimiter_DB::CLEANUP_OPTION, 'keep' ); // Restablece la preferencia antes de la comprobación satisfactoria.
$_REQUEST = array( // Simula una desactivación individual válida.
    'cf7_ol_cleanup' => '1', // Valor que indica que se desea eliminar los registros almacenados.
    'plugin'         => CF7_OPTION_LIMITER_BASENAME, // Identificador del plugin en la petición.
    'action'         => 'deactivate', // Acción individual de desactivación.
    '_wpnonce'       => 'nonce', // Nonce ficticio que se considerará válido en esta simulación.
); // Finaliza la simulación de parámetros.
CF7_OptionLimiter_Hooks::capture_cleanup_preference(); // Ejecuta la captura para confirmar que se almacena la preferencia.
if ( 'remove' !== get_option( CF7_OptionLimiter_DB::CLEANUP_OPTION ) ) { // Comprueba que la preferencia se haya almacenado correctamente.
    throw new RuntimeException( 'capture_cleanup_preference debería marcar la opción de limpieza cuando se recibe el valor 1.' ); // Informa si la preferencia no se registró.
}
if ( 'deactivate-plugin_' . CF7_OPTION_LIMITER_BASENAME !== $GLOBALS['cf7_option_limiter_last_nonce_action'] ) { // Verifica que se usara la acción de nonce adecuada.
    throw new RuntimeException( 'capture_cleanup_preference debería validar el nonce deactivate-plugin específico del plugin.' ); // Indica si el nonce empleado no coincide con el esperado.
}

update_option( CF7_OptionLimiter_DB::CLEANUP_OPTION, 'keep' ); // Restablece la preferencia para comprobar el flujo masivo.
$_REQUEST = array( // Simula una acción masiva sobre el listado de plugins.
    'cf7_ol_cleanup' => '1', // Valor que indica que se desea eliminar los registros almacenados.
    'checked'        => array( CF7_OPTION_LIMITER_BASENAME ), // Lista de plugins seleccionados en la acción masiva.
    'action'         => 'deactivate-selected', // Acción masiva de desactivación.
    '_wpnonce'       => 'nonce', // Nonce ficticio asociado a las acciones masivas.
); // Finaliza la simulación de parámetros.
CF7_OptionLimiter_Hooks::capture_cleanup_preference(); // Ejecuta la captura para confirmar el soporte de acciones masivas.
if ( 'remove' !== get_option( CF7_OptionLimiter_DB::CLEANUP_OPTION ) ) { // Comprueba que la preferencia se haya almacenado en acciones masivas.
    throw new RuntimeException( 'capture_cleanup_preference debería persistir la preferencia también en acciones masivas.' ); // Informa si no se registró el valor esperado.
}
if ( 'bulk-plugins' !== $GLOBALS['cf7_option_limiter_last_nonce_action'] ) { // Verifica que se utilice el nonce global de acciones masivas.
    throw new RuntimeException( 'capture_cleanup_preference debería validar el nonce bulk-plugins en acciones masivas.' ); // Indica si la acción de nonce utilizada no coincide.
}
$_REQUEST = array(); // Restablece la petición simulada para evitar interferencias en pruebas posteriores.
$wpdb->executed_queries = array(); // Limpia el registro de consultas ejecutadas antes de la desactivación.
CF7_OptionLimiter_DB::deactivate(); // Ejecuta la desactivación simulada para comprobar la limpieza automática.
if ( get_option( CF7_OptionLimiter_DB::CLEANUP_OPTION ) !== null ) { // Verifica que la preferencia se eliminó tras la desactivación.
    throw new RuntimeException( 'deactivate debería eliminar la opción de preferencia una vez procesada.' ); // Lanza excepción si la opción persiste.
}
if ( count( $wpdb->executed_queries ) < 2 ) { // Comprueba que se hayan ejecutado las consultas de limpieza sobre ambas tablas.
    throw new RuntimeException( 'deactivate debería lanzar consultas de borrado sobre todas las tablas gestionadas.' ); // Indica el fallo si no se detectan las consultas esperadas.
}
update_option( CF7_OptionLimiter_DB::CLEANUP_OPTION, 'remove' ); // Restablece la preferencia para simular el proceso de desinstalación.
$wpdb->executed_queries = array(); // Limpia el registro de consultas antes de la desinstalación.
CF7_OptionLimiter_DB::uninstall(); // Ejecuta la rutina de desinstalación.
if ( get_option( CF7_OptionLimiter_DB::CLEANUP_OPTION ) !== null ) { // Comprueba que la preferencia se elimine tras la desinstalación.
    throw new RuntimeException( 'uninstall debería eliminar cualquier preferencia residual tras el borrado del plugin.' ); // Informa si la opción persiste.
}
if ( count( $wpdb->executed_queries ) < 2 ) { // Comprueba que la desinstalación también ejecute la limpieza de tablas.
    throw new RuntimeException( 'uninstall debería ejecutar las mismas consultas de limpieza que la desactivación cuando la preferencia es remove.' ); // Lanza excepción si no se ejecutaron ambas consultas.
}

$refDocs = new ReflectionMethod( CF7_OptionLimiter_Docs::class, 'convert_markdown_to_html' ); // Prepara la reflexión para probar la conversión de Markdown a HTML.
$refDocs->setAccessible( true ); // Permite acceder al método protegido para la prueba.
$sampleHtml = $refDocs->invoke( null, "# Título\n\n- Elemento [enlace](https://example.com) y `código`" ); // Convierte una muestra de Markdown con enlace y código.
if ( strpos( $sampleHtml, '<h1>' ) === false || strpos( $sampleHtml, '<ul>' ) === false || strpos( $sampleHtml, '<code>' ) === false ) { // Comprueba que el resultado contenga las etiquetas esperadas.
    throw new RuntimeException( 'convert_markdown_to_html debería generar encabezados, listas y etiquetas de código válidas.' ); // Lanza excepción si la conversión no produce el HTML esperado.
}

CF7_OptionLimiter_Limiter::enqueue_front_assets(); // Encola los recursos frontales para comprobar que se registran estilo y script.
if ( empty( $GLOBALS['cf7_option_limiter_registered_styles']['cf7-option-limiter-frontend'] ) ) { // Comprueba que la hoja de estilos pública se registró correctamente.
    throw new RuntimeException( 'enqueue_front_assets debería registrar la hoja de estilos frontend.' ); // Lanza excepción cuando la hoja de estilos no se registró.
}
$frontendStyleMeta = $GLOBALS['cf7_option_limiter_registered_styles']['cf7-option-limiter-frontend']; // Recupera los metadatos del estilo público registrado.
if ( $frontendStyleMeta['ver'] !== CF7_OPTION_LIMITER_VERSION ) { // Comprueba que la versión del estilo coincida con la versión del plugin.
    throw new RuntimeException( 'La hoja de estilos frontend debería registrarse utilizando CF7_OPTION_LIMITER_VERSION para sincronizar la caché.' ); // Informa si el estilo no utiliza la versión global.
}
if ( ! in_array( 'cf7-option-limiter-frontend', $GLOBALS['cf7_option_limiter_enqueued_styles'], true ) ) { // Verifica que el estilo se haya encolado para su impresión en la página.
    throw new RuntimeException( 'enqueue_front_assets debería encolar la hoja de estilos frontend.' ); // Informa si el estilo no se encoló.
}
if ( empty( $GLOBALS['cf7_option_limiter_registered_scripts']['cf7-option-limiter-frontend'] ) ) { // Comprueba que el script público se registró correctamente.
    throw new RuntimeException( 'enqueue_front_assets debería registrar el script frontend.' ); // Indica el fallo cuando el script no está registrado.
}
$frontendScriptMeta = $GLOBALS['cf7_option_limiter_registered_scripts']['cf7-option-limiter-frontend']; // Recupera los metadatos registrados del script público.
if ( $frontendScriptMeta['ver'] !== CF7_OPTION_LIMITER_VERSION ) { // Comprueba que la versión del script coincida con la constante global del plugin.
    throw new RuntimeException( 'El script frontend debería registrarse utilizando CF7_OPTION_LIMITER_VERSION para permanecer sincronizado con los despliegues.' ); // Lanza excepción si la versión difiere.
}
$frontendScriptSource = file_get_contents( __DIR__ . '/../assets/frontend-check.js' ); // Lee el contenido del script para comprobar la presencia de la nueva lógica.
/**
if ( strpos( $frontendScriptSource, '* Comprueba que el script incluya la validación previa, la recolección de campos y la caché.
if ( strpos( $frontendScriptSource, '*
if ( strpos( $frontendScriptSource, '* Explicación:
if ( strpos( $frontendScriptSource, '* - Resume la tarea principal: Comprueba que el script incluya la validación previa, la recolección de campos y la caché.
if ( strpos( $frontendScriptSource, '* - Describe brevemente los pasos clave ejecutados internamente.
if ( strpos( $frontendScriptSource, '* - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
if ( strpos( $frontendScriptSource, '*
if ( strpos( $frontendScriptSource, '* @param mixed $frontendScriptSource Valor utilizado por la función onFormSubmit.
if ( strpos( $frontendScriptSource, '* @param mixed $'collectLimitableFields' Valor utilizado por la función onFormSubmit.
if ( strpos( $frontendScriptSource, '*
if ( strpos( $frontendScriptSource, '* @return mixed Resultado devuelto por la función onFormSubmit.
if ( strpos( $frontendScriptSource, '*/
if ( strpos( $frontendScriptSource, 'function onFormSubmit' ) === false || strpos( $frontendScriptSource, 'collectLimitableFields' ) === false || strpos( $frontendScriptSource, 'availabilityCache' ) === false ) { // Comprueba que el script incluya la validación previa, la recolección de campos y la caché.
    throw new RuntimeException( 'frontend-check.js debería incluir la validación previa al envío con cacheado de opciones.' ); // Informa si falta la lógica añadida.
}
if ( strpos( $frontendScriptSource, "formElement.addEventListener( 'submit', handler, true )" ) === false ) { // Comprueba que el script enganche el envío en fase de captura para adelantarse a Contact Form 7.
    throw new RuntimeException( 'frontend-check.js debería utilizar addEventListener en captura para bloquear los envíos antes de CF7.' ); // Lanza excepción si no se encuentra el nuevo listener nativo.
}
if ( strpos( $frontendScriptSource, 'cf7-option-limiter-message-container' ) === false ) { // Asegura que el script gestione la nueva clase persistente para los contenedores dinámicos.
    throw new RuntimeException( 'frontend-check.js debería utilizar la clase cf7-option-limiter-message-container para ocultar los avisos vacíos.' ); // Previene regresiones donde se pierda la lógica de ocultación dinámica.
}
$frontendStyleSource = file_get_contents( __DIR__ . '/../assets/frontend.css' ); // Lee el contenido de la nueva hoja de estilos pública.
if ( strpos( $frontendStyleSource, '.cf7-option-limiter-message' ) === false || strpos( $frontendStyleSource, 'background-color' ) === false ) { // Comprueba que la hoja de estilos defina la pastilla con fondo destacado.
    throw new RuntimeException( 'frontend.css debería definir el estilo de la pastilla para los mensajes agotados.' ); // Lanza excepción si falta el estilo solicitado.
}
if ( strpos( $frontendStyleSource, '.cf7-option-limiter-message-container[hidden]' ) === false ) { // Comprueba que la hoja de estilos incluya la regla que oculta por completo los contenedores sin contenido.
    throw new RuntimeException( 'frontend.css debería ocultar los contenedores dinámicos vacíos mediante el selector [hidden].' ); // Evita regresiones que vuelvan a mostrar bloques vacíos en rojo.
}

fwrite( STDOUT, "Pruebas ejecutadas correctamente.\n" ); // Informa en la salida estándar que todas las pruebas pasaron.
exit( 0 ); // Finaliza con código de salida exitoso.
