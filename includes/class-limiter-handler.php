<?php
// Evita el acceso directo al archivo.
if ( ! defined( 'ABSPATH' ) ) { // Comprueba si WordPress está cargado.
    exit; // Finaliza la ejecución en caso contrario.
}

// Clase principal que aplica los límites configurados en Contact Form 7.
class CF7_OptionLimiter_Limiter { // Declara la clase que contiene la lógica del limitador.

    // Almacena los mensajes que deben mostrarse por formulario y campo.
    protected static $depleted_messages = array(); // Arreglo estático para mensajes en el frontend.

    // Almacena la acción utilizada para generar y validar el nonce público.
    protected static $public_nonce_action = 'cf7_option_limiter_public'; // Texto identificador del nonce utilizado en AJAX público.

    // Almacena la acción utilizada para validar el nonce del guardado de excepciones desde el editor.

    /**
    * Inicializa los hooks necesarios para operar sobre Contact Form 7.
    *
    * Explicación:
    * - Resume la tarea principal: Inicializa los hooks necesarios para operar sobre Contact Form 7.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function init() { // Método público que conecta los hooks.
        add_filter( 'wpcf7_form_tag', array( __CLASS__, 'filter_form_tag' ), 20, 2 ); // Filtra las etiquetas del formulario antes de renderizar.
        add_filter( 'wpcf7_form_elements', array( __CLASS__, 'inject_messages' ), 20, 2 ); // Inserta mensajes después de generar el HTML del formulario.
        add_action( 'wpcf7_mail_sent', array( __CLASS__, 'after_submit' ), 10, 1 ); // Ejecuta la lógica tras el envío exitoso del formulario.
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_front_assets' ) ); // Encola los recursos frontales para mostrar mensajes en cualquier página pública.
        add_action( 'wpcf7_enqueue_scripts', array( __CLASS__, 'enqueue_front_assets' ) ); // Sincroniza la carga de assets con Contact Form 7 cuando decide incluir sus propios estilos y scripts.
        add_action( 'init', array( __CLASS__, 'autoload_front_assets' ), 20 ); // Fuerza el encolado frontal incluso cuando otros hooks no se ejecutan, cargando siempre el CSS indispensable.
        add_filter( 'wpcf7_validate_select*', array( __CLASS__, 'validate_choice' ), 20, 2 ); // Valida selects obligatorios antes de procesar el envío.
        add_filter( 'wpcf7_validate_select', array( __CLASS__, 'validate_choice' ), 20, 2 ); // Valida selects opcionales asegurando la disponibilidad.
        add_filter( 'wpcf7_validate_checkbox', array( __CLASS__, 'validate_choice' ), 20, 2 ); // Valida checkbox manteniendo los límites establecidos.
        add_filter( 'wpcf7_validate_radio', array( __CLASS__, 'validate_choice' ), 20, 2 ); // Valida radio buttons para detectar opciones agotadas.
        add_action( 'wp_ajax_cf7_option_limiter_check', array( __CLASS__, 'ajax_check_availability' ) ); // Atiende las peticiones AJAX autenticadas desde el frontend.
        add_action( 'wp_ajax_nopriv_cf7_option_limiter_check', array( __CLASS__, 'ajax_check_availability' ) ); // Atiende las peticiones AJAX de usuarios no autenticados.
    }

    /**
    * Filtra los campos del formulario para ocultar opciones agotadas antes de renderizar.
    *
    * Explicación:
    * - Resume la tarea principal: Filtra los campos del formulario para ocultar opciones agotadas antes de renderizar.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param WPCF7_FormTag $tag      Etiqueta de Contact Form 7 que se está procesando.
    * @param array         $instance Información adicional del formulario.
    *
    * @return WPCF7_FormTag
    */
    public static function filter_form_tag( $tag, $instance = null ) { // Método que manipula la etiqueta del formulario aceptando un segundo parámetro opcional para compatibilidad con versiones antiguas de Contact Form 7.
        if ( ! is_object( $tag ) || empty( $tag->name ) ) { // Comprueba que la etiqueta sea válida y tenga nombre.
            return $tag; // Devuelve la etiqueta sin cambios si no cumple los requisitos.
        }
        $base_type = isset( $tag->basetype ) ? $tag->basetype : ''; // Obtiene el tipo base de la etiqueta (select, radio, checkbox).
        if ( ! in_array( $base_type, array( 'select', 'radio', 'checkbox' ), true ) ) { // Verifica si el campo es de un tipo limitable.
            return $tag; // Devuelve la etiqueta original para tipos no soportados.
        }
        $form = function_exists( 'wpcf7_get_current_contact_form' ) ? wpcf7_get_current_contact_form() : null; // Obtiene el formulario actual en renderizado si la función está disponible.
        $form_id = $form ? (int) $form->id() : 0; // Determina el ID del formulario o cero si no está disponible.
        $post_id = self::resolve_render_post_id(); // Determina el identificador del post actual para evaluar excepciones específicas.
        do_action( 'cf7_option_limiter_before_render', $form_id, $tag ); // Lanza el hook personalizado antes de aplicar filtros.
        $values = is_array( $tag->values ) ? $tag->values : array(); // Obtiene la lista de valores definidos para el campo.
        $labels = is_array( $tag->labels ) ? $tag->labels : $values; // Obtiene las etiquetas visibles asociadas a cada valor.
        $new_values = array(); // Inicializa el arreglo que almacenará los valores permitidos.
        $new_labels = array(); // Inicializa el arreglo de etiquetas correspondiente a los valores permitidos.
        $options_removed = false; // Bandera que indica si se ha eliminado alguna opción agotada.
        foreach ( $values as $index => $value ) { // Recorre cada valor definido en el campo.
            $label = isset( $labels[ $index ] ) ? $labels[ $index ] : $value; // Recupera la etiqueta correspondiente o usa el propio valor.
            $limit = CF7_OptionLimiter_DB::get_effective_limit( $form_id, $tag->name, $value, $post_id ); // Consulta la regla efectiva priorizando excepciones por página cuando existan.
            if ( empty( $limit ) ) { // Si no existe una regla para esta opción.
                $new_values[] = $value; // Mantiene el valor disponible.
                $new_labels[] = $label; // Mantiene la etiqueta correspondiente.
                continue; // Continúa con la siguiente opción.
            }
            if ( (int) $limit['max_count'] > 0 && (int) $limit['current_count'] >= (int) $limit['max_count'] ) { // Comprueba si el límite se ha alcanzado.
                $hide_choice = isset( $limit['hide_exhausted'] ) ? (int) $limit['hide_exhausted'] : 0; // Determina si la regla solicita ocultar la opción agotada.
                $message = ! empty( $limit['custom_message'] ) ? $limit['custom_message'] : __( 'Opción agotada.', 'cf7-option-limiter' ); // Determina el mensaje personalizado.
                self::register_message( $form_id, $tag->name, sprintf( '%s: %s', $label, $message ) ); // Registra el mensaje para mostrarlo en el frontend.
                if ( $hide_choice ) { // Comprueba si la opción debe ocultarse por completo.
                    $options_removed = true; // Marca que se ha eliminado al menos una opción.
                    $log_context = array( // Construye el contexto del evento de agotamiento oculto.
                        'form_id'      => (int) $form_id, // Identificador del formulario que se está renderizando.
                        'field_name'   => $tag->name, // Nombre del campo afectado dentro del formulario.
                        'option_value' => $value, // Valor concreto que ha sido ocultado por agotamiento.
                        'message'      => __( 'Opción oculta durante el renderizado.', 'cf7-option-limiter' ), // Mensaje descriptivo asociado al evento.
                        'stage'        => 'render_hide', // Etapa del proceso en la que se registra el evento.
                    );
                    CF7_OptionLimiter_Logger::log( 'exhausted', $log_context ); // Registra el evento sólo cuando la depuración está activa.
                    continue; // Omite añadir la opción agotada al listado para ocultarla.
                }
                $options_removed = true; // Marca que la estructura se modificará al menos en las etiquetas visibles.
                $label = sprintf( __( '%s (agotada temporalmente)', 'cf7-option-limiter' ), $label ); // Añade una nota informativa a la etiqueta manteniendo la opción visible.
                $log_context = array( // Construye el contexto del evento de agotamiento marcado como visible.
                    'form_id'      => (int) $form_id, // Identificador del formulario que contiene la opción.
                    'field_name'   => $tag->name, // Nombre del campo asociado al valor agotado.
                    'option_value' => $value, // Valor concreto de la opción que se mantiene visible.
                    'message'      => __( 'Opción mantenida visible pero marcada como agotada.', 'cf7-option-limiter' ), // Mensaje descriptivo del evento.
                    'stage'        => 'render_marked', // Etapa del proceso donde se añadió la marca visual.
                );
                CF7_OptionLimiter_Logger::log( 'exhausted', $log_context ); // Registra el evento únicamente cuando la depuración está activa.
            }
            $new_values[] = $value; // Añade el valor disponible a la lista filtrada.
            $new_labels[] = $label; // Añade la etiqueta correspondiente a la lista filtrada (con nota si aplica).
        }
        if ( empty( $new_values ) ) { // Comprueba si se han eliminado todas las opciones disponibles.
            self::register_message( $form_id, $tag->name, __( 'Todas las opciones de este campo están agotadas temporalmente.', 'cf7-option-limiter' ) ); // Añade un mensaje global para el campo completamente agotado.
            if ( method_exists( $tag, 'set_option' ) ) { // Comprueba que la etiqueta permita asignar atributos.
                $tag->set_option( 'disabled', 'disabled' ); // Añade el atributo disabled para evitar interacción con el campo vacío.
            }
        }
        if ( $options_removed ) { // Verifica si se eliminaron opciones para reconstruir el conjunto de valores y etiquetas.
            $tag->values = $new_values; // Reemplaza los valores originales por los filtrados.
            $tag->labels = $new_labels; // Reemplaza las etiquetas con la nueva lista filtrada.
            if ( class_exists( 'WPCF7_Pipes' ) && $tag->pipes instanceof WPCF7_Pipes ) { // Comprueba si existen pipes para sincronizar etiquetas y valores.
                $pipes = array(); // Inicializa el arreglo de pipes que se reconstruirá.
                foreach ( $new_values as $index => $value ) { // Recorre los nuevos valores disponibles.
                    $label = isset( $new_labels[ $index ] ) ? $new_labels[ $index ] : $value; // Obtiene la etiqueta asociada al valor.
                    $pipes[] = array( $label, $value ); // Añade el par etiqueta-valor al arreglo de pipes.
                }
                $tag->pipes = new WPCF7_Pipes( $pipes ); // Crea un nuevo objeto de pipes con los datos filtrados.
            }
        }
        return $tag; // Devuelve la etiqueta modificada o sin cambios según corresponda.
    }

    /**
    * Registra mensajes para mostrarlos en el frontend agrupados por formulario y campo.
    *
    * Explicación:
    * - Resume la tarea principal: Registra mensajes para mostrarlos en el frontend agrupados por formulario y campo.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param int    $form_id Identificador del formulario.
    * @param string $field   Nombre del campo.
    * @param string $message Mensaje que se quiere mostrar.
    *
    * @return void
    */
    protected static function register_message( $form_id, $field, $message ) { // Método auxiliar para almacenar mensajes.
        if ( ! isset( self::$depleted_messages[ $form_id ] ) ) { // Comprueba si ya existe un arreglo para el formulario actual.
            self::$depleted_messages[ $form_id ] = array(); // Inicializa el arreglo para el formulario indicado.
        }
        if ( ! isset( self::$depleted_messages[ $form_id ][ $field ] ) ) { // Comprueba si ya se registró el campo.
            self::$depleted_messages[ $form_id ][ $field ] = array(); // Inicializa el arreglo para el campo.
        }
        self::$depleted_messages[ $form_id ][ $field ][] = $message; // Añade el mensaje al listado correspondiente.
    }

    /**
    * Inserta en el HTML final los contenedores de mensajes para su posterior posicionamiento vía JavaScript.
    *
    * Explicación:
    * - Resume la tarea principal: Inserta en el HTML final los contenedores de mensajes para su posterior posicionamiento vía JavaScript.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param string           $elements    HTML completo del formulario.
    * @param WPCF7_ContactForm $contact_form Objeto del formulario actual.
    *
    * @return string
    */
    public static function inject_messages( $elements, $contact_form = null ) { // Método que agrega el HTML de mensajes al formulario aceptando el objeto de formulario como argumento opcional para evitar errores cuando el filtro solo entrega un parámetro.
        $form_id = is_object( $contact_form ) && method_exists( $contact_form, 'id' ) ? (int) $contact_form->id() : 0; // Obtiene el ID del formulario actual.
        if ( empty( self::$depleted_messages[ $form_id ] ) ) { // Verifica si hay mensajes registrados para el formulario.
            return $elements; // Devuelve el HTML original si no hay mensajes que mostrar.
        }
        foreach ( self::$depleted_messages[ $form_id ] as $field => $messages ) { // Recorre cada campo con mensajes.
            $unique_messages = array_unique( $messages ); // Elimina mensajes duplicados para evitar repeticiones.
            $message_text = implode( ' ', $unique_messages ); // Concatena los mensajes en una sola cadena.
            $container = sprintf( // Construye el HTML del contenedor de mensajes.
                '<div class="cf7-option-limiter-message" data-field="%1$s" data-form="%2$d">%3$s</div>', // Contenedor con atributos de datos para su reubicación por JS.
                esc_attr( $field ), // Escapa el nombre del campo para usarlo en HTML.
                $form_id, // Incluye el ID del formulario como atributo data-form.
                esc_html( $message_text ) // Escapa el texto del mensaje antes de imprimirlo.
            );
            $elements .= $container; // Añade el contenedor al final del formulario.
        }
        return $elements; // Devuelve el HTML con los contenedores adicionales.
    }

    /**
    * Incrementa contadores y registra eventos tras el envío del formulario.
    *
    * Explicación:
    * - Resume la tarea principal: Incrementa contadores y registra eventos tras el envío del formulario.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param WPCF7_ContactForm $contact_form Formulario enviado correctamente.
    *
    * @return void
    */
    public static function after_submit( $contact_form ) { // Método que procesa los datos después del envío.
        $submission = WPCF7_Submission::get_instance(); // Obtiene la instancia actual de envío de Contact Form 7.
        if ( ! $submission ) { // Comprueba que exista una instancia válida de envío.
            $log_context = array( // Construye el contexto del error detectado.
                'message' => __( 'No se pudo obtener la sumisión de Contact Form 7.', 'cf7-option-limiter' ), // Mensaje descriptivo del problema.
                'stage'   => 'after_submit', // Etapa del flujo en la que ocurrió el error.
            );
            CF7_OptionLimiter_Logger::log( 'error', $log_context, true ); // Registra el error forzando la escritura aunque la depuración esté desactivada.
            return; // Finaliza si no hay datos de envío disponibles.
        }
        $data = $submission->get_posted_data(); // Recupera los datos enviados por el usuario.
        if ( empty( $data ) || ! is_array( $data ) ) { // Verifica que se hayan recibido datos válidos.
            $log_context = array( // Construye el contexto de la anomalía detectada.
                'message' => __( 'Los datos enviados están vacíos o en formato no válido.', 'cf7-option-limiter' ), // Mensaje que describe el problema encontrado.
                'stage'   => 'after_submit', // Etapa del flujo en la que se detectó la anomalía.
            );
            CF7_OptionLimiter_Logger::log( 'error', $log_context, true ); // Registra la anomalía forzando la escritura.
            return; // Finaliza si no hay datos procesables.
        }
        $form_id = is_object( $contact_form ) && method_exists( $contact_form, 'id' ) ? (int) $contact_form->id() : 0; // Obtiene el ID del formulario actual.
        do_action( 'cf7_option_limiter_after_submit', $form_id, $data ); // Dispara el hook personalizado después de recibir los datos.
        $post_id = self::resolve_submission_post_id( $submission ); // Determina el identificador del post asociado a la sumisión para priorizar excepciones.
        foreach ( $data as $field => $value ) { // Recorre cada campo enviado.
            if ( is_array( $value ) ) { // Comprueba si el campo contiene múltiples valores (checkbox, select múltiple).
                foreach ( $value as $single_value ) { // Recorre cada valor individual.
                    self::process_choice( $form_id, $field, $single_value, $post_id ); // Procesa cada selección individualmente teniendo en cuenta el contexto del post.
                }
                continue; // Continúa con el siguiente campo después de procesar el arreglo.
            }
            self::process_choice( $form_id, $field, $value, $post_id ); // Procesa valores individuales directos considerando el contexto del post.
        }
    }

    /**
    * Procesa una selección incrementando el contador y registrando logs pertinentes.
    *
    * Explicación:
    * - Resume la tarea principal: Procesa una selección incrementando el contador y registrando logs pertinentes.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param int    $form_id Identificador del formulario.
    * @param string $field   Nombre del campo.
    * @param string $value   Valor seleccionado.
    * @param int    $post_id Identificador del post asociado al envío.
    * @return void
    */
    protected static function process_choice( $form_id, $field, $value, $post_id ) { // Método auxiliar que opera sobre cada selección.
        $value = sanitize_text_field( (string) $value ); // Sanitiza el valor recibido para prevenir entradas maliciosas.
        $limit = CF7_OptionLimiter_DB::get_effective_limit( $form_id, $field, $value, $post_id ); // Recupera la regla efectiva priorizando excepciones específicas cuando existan.
        if ( empty( $limit ) ) { // Comprueba si no existe una regla asociada al valor seleccionado.
            return; // Finaliza sin realizar acciones adicionales.
        }
        $incremented = CF7_OptionLimiter_DB::increment_counter_for_context( $form_id, $field, $value, $post_id ); // Incrementa el contador correspondiente (global o excepción) evitando superar el máximo permitido.
        if ( ! $incremented ) { // Comprueba si la operación no pudo ejecutarse (por ejemplo, por falta de disponibilidad).
            $log_context = array( // Construye el contexto del agotamiento que impidió registrar la selección.
                'form_id'      => (int) $form_id, // Identificador del formulario evaluado.
                'field_name'   => $field, // Nombre del campo asociado a la opción agotada.
                'option_value' => $value, // Valor concreto que alcanzó el límite.
                'message'      => __( 'La opción alcanzó el límite antes de registrar el envío.', 'cf7-option-limiter' ), // Mensaje que describe el motivo del bloqueo.
                'stage'        => 'after_submit_prevented', // Indica la etapa del flujo donde se detectó el agotamiento.
            );
            CF7_OptionLimiter_Logger::log( 'exhausted', $log_context, true ); // Registra el evento forzando la escritura para conservar la incidencia.
            return; // Finaliza para evitar lecturas redundantes.
        }
        $log_context = array( // Construye el contexto de la selección registrada correctamente.
            'form_id'      => (int) $form_id, // Identificador del formulario donde se registró la selección.
            'field_name'   => $field, // Nombre del campo procesado.
            'option_value' => $value, // Valor específico que incrementó el contador.
            'message'      => __( 'Selección registrada y contador actualizado.', 'cf7-option-limiter' ), // Mensaje descriptivo del evento.
            'stage'        => 'after_submit_recorded', // Etapa del flujo donde se registró la selección.
        );
        CF7_OptionLimiter_Logger::log( 'registered', $log_context ); // Registra el evento únicamente cuando la depuración está activa.
        $updated_limit = CF7_OptionLimiter_DB::get_effective_limit( $form_id, $field, $value, $post_id ); // Recupera nuevamente la regla efectiva para conocer el nuevo estado.
        if ( $updated_limit && (int) $updated_limit['current_count'] >= (int) $updated_limit['max_count'] ) { // Comprueba si el límite ha sido alcanzado tras la actualización.
            $log_context = array( // Construye el contexto del agotamiento definitivo tras el envío.
                'form_id'      => (int) $form_id, // Identificador del formulario afectado.
                'field_name'   => $field, // Nombre del campo que alcanzó el máximo configurado.
                'option_value' => $value, // Valor concreto que se agotó tras el envío.
                'message'      => __( 'La opción alcanzó el límite configurado tras el envío.', 'cf7-option-limiter' ), // Mensaje que describe el agotamiento definitivo.
                'stage'        => 'after_submit_threshold', // Etapa del flujo donde se confirmó el agotamiento.
            );
            CF7_OptionLimiter_Logger::log( 'exhausted', $log_context ); // Registra el evento únicamente cuando la depuración está activa.
        }
    }

    /**
    * Encola los recursos necesarios para mostrar mensajes en el frontend.
    *
    * Explicación:
    * - Resume la tarea principal: Encola los recursos necesarios para mostrar mensajes en el frontend.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function enqueue_front_assets() { // Método encargado de encolar estilos y scripts frontales.
        wp_register_style( // Registra la hoja de estilos pública que da formato a los mensajes dinámicos.
            'cf7-option-limiter-frontend', // Identificador único del estilo cargado en el frontend.
            CF7_OPTION_LIMITER_URL . 'assets/frontend.css', // Ruta al archivo CSS que define la apariencia de los avisos.
            array(), // No se declaran dependencias adicionales para la hoja de estilos.
            CF7_OPTION_LIMITER_VERSION, // Versión sincronizada con el plugin para invalidar caché tras cada despliegue.
            'all' // Indica que los estilos se aplican en todos los medios soportados.
        );
        wp_register_script( // Registra el script encargado de comprobar la disponibilidad en tiempo real.
            'cf7-option-limiter-frontend', // Identificador único del script público.
            CF7_OPTION_LIMITER_URL . 'assets/frontend-check.js', // Ruta al archivo JavaScript que gestiona las comprobaciones AJAX.
            array( 'jquery' ), // Declara jQuery como dependencia principal.
            CF7_OPTION_LIMITER_VERSION, // Versiona el script con la constante global para mantener sincronizada la caché.
            true // Carga el script al final del documento para optimizar el renderizado.
        );
        wp_localize_script( // Expone parámetros dinámicos al script público.
            'cf7-option-limiter-frontend', // Identificador del script que recibirá los datos.
            'CF7OptionLimiterPublic', // Nombre del objeto JavaScript que almacenará la configuración.
            array( // Arreglo asociativo con la configuración necesaria.
                'ajaxUrl'   => admin_url( 'admin-ajax.php' ), // URL del endpoint AJAX de WordPress para el frontend.
                'nonce'     => wp_create_nonce( self::$public_nonce_action ), // Nonce de seguridad para validar las peticiones.
                'postId'    => self::resolve_render_post_id(), // Identificador del post actual para evaluar y guardar excepciones específicas.
                'messages'  => array( // Colección de textos utilizados por el script para informar al usuario.
                    'unavailable'    => __( 'La opción "%1$s" ya no está disponible. %2$s', 'cf7-option-limiter' ), // Mensaje cuando la opción tiene un aviso personalizado.
                    'unavailableRaw' => __( 'La opción "%1$s" ya no está disponible.', 'cf7-option-limiter' ), // Mensaje básico cuando no existe aviso personalizado.
                    'requestError'   => __( 'No se pudo comprobar la disponibilidad. Inténtalo de nuevo.', 'cf7-option-limiter' ), // Mensaje ante errores inesperados en la petición.
                ),
            )
        );
        if ( ! wp_style_is( 'cf7-option-limiter-frontend', 'enqueued' ) ) { // Comprueba si la hoja de estilos ya fue encolada previamente.
            wp_enqueue_style( 'cf7-option-limiter-frontend' ); // Encola los estilos frontales para mostrar la pastilla de advertencia.
        }
        if ( ! wp_script_is( 'cf7-option-limiter-frontend', 'enqueued' ) ) { // Comprueba si el script de comprobación ya está encolado.
            wp_enqueue_script( 'cf7-option-limiter-frontend' ); // Encola el script público si todavía no se ha añadido a la cola.
        }
    }

    /**
    * Encola de manera proactiva los assets frontales cuando WordPress inicializa la petición.
    *
    * Explicación:
    * - Resume la tarea principal: Encola de manera proactiva los assets frontales cuando WordPress inicializa la petición.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return void
    */
    public static function autoload_front_assets() { // Método auxiliar que garantiza la carga del CSS aunque otros hooks se omitan.
        if ( is_admin() ) { // Comprueba si la petición pertenece al área administrativa.
            return; // Evita cargar recursos públicos dentro del administrador para no contaminar sus estilos.
        }
        self::enqueue_front_assets(); // Encola los recursos esenciales asegurando que el CSS de avisos esté disponible en cualquier vista pública.
    }

    /**
    * Valida los campos limitados durante el proceso de envío para evitar sobrepasar los máximos.
    *
    * Explicación:
    * - Resume la tarea principal: Valida los campos limitados durante el proceso de envío para evitar sobrepasar los máximos.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param mixed $result Resultado de validación proporcionado por Contact Form 7.
    * @param mixed $tag    Etiqueta del campo que se está validando.
    *
    * @return mixed
    */
    public static function validate_choice( $result, $tag ) { // Método que intercepta la validación de campos limitados.
        $field_name = self::extract_field_name( $tag ); // Obtiene el nombre del campo desde la etiqueta recibida.
        if ( '' === $field_name ) { // Comprueba que se haya podido determinar el nombre del campo.
            return $result; // Devuelve el resultado original si no hay nombre de campo válido.
        }
        $submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null; // Obtiene la sumisión actual si está disponible.
        if ( ! $submission || ! method_exists( $submission, 'get_posted_data' ) ) { // Comprueba que se pueda acceder a los datos enviados.
            return $result; // Omite la validación avanzada si no hay datos disponibles.
        }
        $data = $submission->get_posted_data(); // Recupera todos los datos enviados en el formulario.
        if ( empty( $data ) || ! is_array( $data ) || ! array_key_exists( $field_name, $data ) ) { // Comprueba si el campo no existe en los datos enviados.
            return $result; // Devuelve el resultado original cuando no hay valores que analizar.
        }
        $values = $data[ $field_name ]; // Recupera el valor o valores enviados para el campo.
        if ( ! is_array( $values ) ) { // Comprueba si se trata de un valor simple.
            $values = array( $values ); // Convierte el valor en un arreglo para unificar el flujo de validación.
        }
        $form_id = self::resolve_submission_form_id( $submission ); // Obtiene el identificador del formulario asociado a la sumisión.
        $post_id = self::resolve_submission_post_id( $submission ); // Obtiene el identificador del post desde el contexto de la sumisión.
        $messages = array(); // Inicializa el arreglo de mensajes de error a mostrar.
        foreach ( $values as $value ) { // Recorre cada valor enviado.
            $sanitized = sanitize_text_field( (string) $value ); // Sanitiza el valor actual para evitar entradas maliciosas.
            $limit = CF7_OptionLimiter_DB::get_effective_limit( $form_id, $field_name, $sanitized, $post_id ); // Recupera la regla efectiva considerando posibles excepciones por página.
            if ( empty( $limit ) ) { // Comprueba si no existe una regla asociada a este valor.
                continue; // Continúa con el siguiente valor sin generar errores.
            }
            $max = isset( $limit['max_count'] ) ? (int) $limit['max_count'] : 0; // Determina el máximo permitido para la opción.
            $current = isset( $limit['current_count'] ) ? (int) $limit['current_count'] : 0; // Recupera el contador actual registrado.
            if ( 0 !== $max && $current >= $max ) { // Comprueba si el límite se ha alcanzado o superado.
                $message = ! empty( $limit['custom_message'] ) ? $limit['custom_message'] : __( 'Esta opción ya no está disponible.', 'cf7-option-limiter' ); // Determina el mensaje a mostrar al usuario.
                $messages[] = $message; // Añade el mensaje a la lista de incidencias detectadas.
            }
        }
        if ( empty( $messages ) ) { // Comprueba si no se detectaron incidencias.
            return $result; // Devuelve el resultado original cuando todas las opciones están disponibles.
        }
        $unique_messages = array_unique( $messages ); // Elimina mensajes repetidos para no saturar al usuario.
        $final_message = implode( ' ', $unique_messages ); // Construye el texto final concatenando los mensajes únicos.
        if ( is_object( $result ) && method_exists( $result, 'invalidate' ) ) { // Comprueba si el resultado es un objeto válido de validación.
            $result->invalidate( $tag, $final_message ); // Marca el campo como inválido y asocia el mensaje de error.
        }
        return $result; // Devuelve el resultado (modificado o no) para continuar con el flujo de validación.
    }

    /**
    * Atiende las peticiones AJAX que verifican la disponibilidad de opciones en tiempo real.
    *
    * Explicación:
    * - Resume la tarea principal: Atiende las peticiones AJAX que verifican la disponibilidad de opciones en tiempo real.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function ajax_check_availability() { // Método que responde a las peticiones AJAX del frontend.

        $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        $transient_key = 'cf7ol_ratelimit_' . $ip;
        $request_count = get_transient( $transient_key );

        if ( false === $request_count ) {
            set_transient( $transient_key, 1, 60 );
        } else {
            $request_count++;
            if ( $request_count > 60 ) {
                wp_send_json_error(
                    array( 'message' => __( 'Demasiadas solicitudes. Inténtalo de nuevo más tarde.', 'cf7-option-limiter' ) ),
                    429
                );
            }
            set_transient( $transient_key, $request_count, 60 );
        }

        check_ajax_referer( self::$public_nonce_action, 'nonce' ); // Valida el nonce recibido para proteger la petición.
        $form_id = isset( $_POST['form_id'] ) ? (int) $_POST['form_id'] : 0; // Recupera el identificador del formulario desde la petición.
        $field_name = isset( $_POST['field_name'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['field_name'] ) ) : ''; // Obtiene el nombre del campo a validar.
        $raw_values = isset( $_POST['values'] ) ? (array) $_POST['values'] : array(); // Recupera la lista de valores enviados para comprobar disponibilidad.
        $post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0; // Recupera el identificador del post para priorizar excepciones específicas.
        if ( $form_id <= 0 || '' === $field_name || empty( $raw_values ) ) { // Comprueba si faltan datos esenciales para realizar la verificación.
            wp_send_json_error( array( 'message' => __( 'Petición incompleta para comprobar la disponibilidad.', 'cf7-option-limiter' ) ), 400 ); // Devuelve un error JSON indicando que la petición es inválida.
        }
        $values = array(); // Inicializa el arreglo donde se guardarán los valores sanitizados.
        foreach ( $raw_values as $value ) { // Recorre los valores recibidos.
            $values[] = sanitize_text_field( wp_unslash( (string) $value ) ); // Sanitiza cada valor antes de procesarlo.
        }
        $limits = CF7_OptionLimiter_DB::get_effective_limits_for_options( $form_id, $field_name, $values, $post_id ); // Recupera las reglas efectivas priorizando las excepciones asociadas al post cuando existan.
        $response = array(); // Inicializa el arreglo de respuesta.
        foreach ( $values as $value ) { // Recorre cada valor solicitado para construir la respuesta individual.
            if ( isset( $limits[ $value ] ) ) { // Comprueba si existe una regla para el valor actual.
                $limit = $limits[ $value ]; // Recupera los datos de la regla correspondiente.
                $max = isset( $limit['max_count'] ) ? (int) $limit['max_count'] : 0; // Obtiene el máximo configurado para la opción.
                $current = isset( $limit['current_count'] ) ? (int) $limit['current_count'] : 0; // Obtiene el contador actual almacenado.
                $available = ( 0 === $max ) || ( $current < $max ); // Determina si aún quedan plazas disponibles.
                $remaining = ( 0 === $max ) ? null : max( 0, $max - $current ); // Calcula las plazas restantes cuando el máximo es finito.
                $response[ $value ] = array( // Construye la respuesta asociada al valor actual.
                    'available' => $available, // Indica si la opción sigue disponible.
                    'remaining' => $remaining, // Informa sobre las plazas restantes (o null si es ilimitado).
                    'message'   => isset( $limit['custom_message'] ) ? (string) $limit['custom_message'] : '', // Incluye el mensaje personalizado definido en la regla.
                    'hide'      => ! empty( $limit['hide_exhausted'] ), // Indica si la opción debe ocultarse cuando se agote.
                );
                continue; // Continúa con el siguiente valor.
            }
            $response[ $value ] = array( // Define la respuesta para valores sin regla asociada.
                'available' => true, // Considera disponible la opción al no existir límite.
                'remaining' => null, // No hay contador asociado cuando no existe una regla explícita.
                'message'   => '', // No existe mensaje personalizado en ausencia de regla.
                'hide'      => false, // No se oculta la opción al no existir límite.
            );
        }
        wp_send_json_success( array( 'results' => $response ) ); // Devuelve la información en formato JSON para el frontend.
    }

    /**
    * Obtiene el nombre del campo desde la etiqueta entregada por Contact Form 7.
    *
    * Explicación:
    * - Resume la tarea principal: Obtiene el nombre del campo desde la etiqueta entregada por Contact Form 7.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param mixed $tag Etiqueta del formulario proporcionada en filtros de validación.
    *
    * @return string
    */
    protected static function extract_field_name( $tag ) { // Método auxiliar que obtiene el nombre del campo de la etiqueta.
        if ( is_object( $tag ) && isset( $tag->name ) ) { // Comprueba si la etiqueta es un objeto con propiedad name.
            return (string) $tag->name; // Devuelve el nombre directamente desde la propiedad del objeto.
        }
        if ( is_array( $tag ) && isset( $tag['name'] ) ) { // Comprueba si la etiqueta se representa como arreglo con clave name.
            return (string) $tag['name']; // Devuelve el nombre obtenido del arreglo.
        }
        return ''; // Devuelve una cadena vacía cuando no se puede determinar el nombre del campo.
    }

    /**
    * Determina el identificador del post activo durante el renderizado para priorizar excepciones.
    *
    * Explicación:
    * - Resume la tarea principal: Determina el identificador del post activo durante el renderizado para priorizar excepciones.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return int
    */
    protected static function resolve_render_post_id() { // Método auxiliar que obtiene el ID del post actual en renderizado.
        if ( function_exists( 'get_queried_object_id' ) ) { // Comprueba si la función está disponible en el contexto actual.
            $queried = (int) get_queried_object_id(); // Recupera el identificador del objeto consultado.
            if ( $queried > 0 ) { // Verifica que sea un identificador válido.
                return $queried; // Devuelve el identificador encontrado.
            }
        }
        if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance ) && \Elementor\Plugin::$instance && isset( \Elementor\Plugin::$instance->editor ) && method_exists( \Elementor\Plugin::$instance->editor, 'get_post_id' ) ) { // Comprueba si Elementor puede proporcionar el identificador en el editor.
            $editor_post = (int) \Elementor\Plugin::$instance->editor->get_post_id(); // Recupera el identificador del documento en edición.
            if ( $editor_post > 0 ) { // Verifica que el identificador sea válido.
                return $editor_post; // Devuelve el identificador obtenido del editor de Elementor.
            }
        }
        global $post; // Accede al objeto global $post como último recurso.
        if ( isset( $post->ID ) ) { // Comprueba si el objeto global dispone de un identificador.
            return (int) $post->ID; // Devuelve el identificador del post global.
        }
        return 0; // Devuelve cero cuando no se puede determinar el identificador del post actual.
    }

    /**
    * Determina el identificador del formulario asociado a la sumisión actual.
    *
    * Explicación:
    * - Resume la tarea principal: Determina el identificador del formulario asociado a la sumisión actual.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param WPCF7_Submission $submission Instancia de sumisión actual.
    *
    * @return int
    */
    protected static function resolve_submission_form_id( $submission ) { // Método auxiliar que obtiene el ID del formulario desde la sumisión.
        if ( ! $submission || ! method_exists( $submission, 'get_contact_form' ) ) { // Comprueba que la sumisión provea el formulario asociado.
            $form = function_exists( 'wpcf7_get_current_contact_form' ) ? wpcf7_get_current_contact_form() : null; // Intenta obtener el formulario actual como alternativa.
            return ( $form && method_exists( $form, 'id' ) ) ? (int) $form->id() : 0; // Devuelve el ID del formulario o cero si no se puede determinar.
        }
        $form = $submission->get_contact_form(); // Recupera el formulario asociado directamente desde la sumisión.
        if ( $form && method_exists( $form, 'id' ) ) { // Comprueba que el formulario ofrezca un método id().
            return (int) $form->id(); // Devuelve el identificador entero del formulario.
        }
        return 0; // Devuelve cero cuando no se puede resolver el ID.
    }

    /**
    * Determina el identificador del post asociado a la sumisión actual para aplicar excepciones.
    *
    * Explicación:
    * - Resume la tarea principal: Determina el identificador del post asociado a la sumisión actual para aplicar excepciones.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param WPCF7_Submission $submission Instancia de sumisión actual.
    *
    * @return int
    */
    protected static function resolve_submission_post_id( $submission ) { // Método auxiliar que detecta el post asociado a la sumisión.
        if ( $submission && method_exists( $submission, 'get_posted_data' ) ) { // Comprueba si se pueden obtener los datos enviados.
            $posted = $submission->get_posted_data(); // Recupera el arreglo de datos enviados.
            if ( is_array( $posted ) ) { // Verifica que los datos tengan formato de arreglo.
                if ( isset( $posted['_wpcf7_container_post'] ) ) { // Comprueba si Contact Form 7 adjuntó el identificador del post contenedor.
                    $container = (int) $posted['_wpcf7_container_post']; // Convierte el valor en entero.
                    if ( $container > 0 ) { // Comprueba que el identificador sea válido.
                        return $container; // Devuelve el identificador obtenido de los datos enviados.
                    }
                }
            }
        }
        return self::resolve_render_post_id(); // Devuelve el identificador determinado durante el renderizado como respaldo.
    }

}
