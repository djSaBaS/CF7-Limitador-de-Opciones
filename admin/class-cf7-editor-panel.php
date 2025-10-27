<?php
// Impide el acceso directo al archivo sin pasar por WordPress.
if ( ! defined( 'ABSPATH' ) ) { // Comprueba que WordPress esté cargado correctamente.
    exit; // Finaliza la ejecución si se accede directamente.
}

// Clase responsable de integrar la interfaz del limitador dentro del editor de Contact Form 7.
class CF7_OptionLimiter_CF7_Panel { // Declara la clase que gestionará el panel incrustado en Contact Form 7.

    // Indicador interno para saber si se deben imprimir los formularios ocultos en el pie del administrador.
    protected static $needs_hidden_forms = false; // Propiedad estática que evita imprimir varias veces los formularios auxiliares.

    /**
     * Inicializa los hooks necesarios cuando Contact Form 7 está disponible.
     *
     * @return void
     */
    public static function init() { // Método principal de arranque del panel incrustado.
        if ( ! is_admin() || ! defined( 'WPCF7_VERSION' ) ) { // Comprueba que se esté en el administrador y que Contact Form 7 esté activo.
            return; // Finaliza si no se cumple el contexto requerido.
        }
        add_filter( 'wpcf7_editor_panels', array( __CLASS__, 'register_panel' ) ); // Añade una pestaña personalizada en el editor de Contact Form 7.
        add_action( 'wpcf7_admin_footer', array( __CLASS__, 'print_hidden_forms' ) ); // Imprime formularios auxiliares al final de la página del editor.
    }

    /**
     * Registra la pestaña adicional dentro del editor de Contact Form 7.
     *
     * @param array<string, mixed> $panels Colección de paneles registrados actualmente.
     *
     * @return array<string, mixed>
     */
    public static function register_panel( $panels ) { // Añade el panel del limitador a la lista existente.
        if ( ! is_array( $panels ) ) { // Comprueba que la estructura recibida sea un arreglo.
            $panels = array(); // Inicializa la colección cuando llega en blanco o en un formato inesperado.
        }
        $panels['cf7-option-limiter'] = array( // Inserta el panel personalizado identificándolo mediante una clave única.
            'title'    => __( 'Limitador de opciones', 'cf7-option-limiter' ), // Título que aparecerá en la pestaña del editor.
            'callback' => array( __CLASS__, 'render_panel' ), // Función encargada de renderizar el contenido del panel.
        );
        return $panels; // Devuelve la colección con el nuevo panel incluido.
    }

    /**
     * Renderiza el contenido del panel dentro del editor de Contact Form 7.
     *
     * @param WPCF7_ContactForm $contact_form Instancia del formulario actualmente en edición.
     *
     * @return void
     */

    public static function render_panel( $contact_form ) { // Genera el HTML del panel incrustado.
        if ( ! current_user_can( 'manage_options' ) ) { // Comprueba que el usuario tenga permisos suficientes para gestionar límites.
            echo '<p>' . esc_html__( 'No tienes permisos para gestionar los límites desde este formulario.', 'cf7-option-limiter' ) . '</p>'; // Muestra un aviso indicando la falta de permisos.
            return; // Finaliza sin renderizar el resto del panel.
        }
        if ( function_exists( 'wp_enqueue_style' ) ) { // Comprueba que las funciones de encolado estén disponibles.
            wp_enqueue_style( 'cf7-option-limiter-admin' ); // Asegura que los estilos compartidos se apliquen dentro del editor.
            wp_enqueue_script( 'cf7-option-limiter-admin' ); // Asegura que el script administrativo esté disponible en este contexto.
            wp_localize_script( 'cf7-option-limiter-admin', 'CF7OptionLimiterAdmin', CF7_OptionLimiter_Admin::get_localization_data() ); // Expone la configuración necesaria para el script dentro del editor.
        }
        $form_id = self::resolve_form_id( $contact_form ); // Obtiene el identificador numérico del formulario en edición.
        $form_title = self::resolve_form_title( $contact_form ); // Obtiene el título legible del formulario para mostrarlo al usuario.
        if ( $form_id <= 0 ) { // Comprueba que se haya podido determinar el ID del formulario.
            echo '<p>' . esc_html__( 'No se pudo determinar el identificador del formulario.', 'cf7-option-limiter' ) . '</p>'; // Muestra un aviso informativo cuando falla la detección.
            return; // Finaliza sin mostrar el panel interactivo.
        }
        self::$needs_hidden_forms = true; // Marca que será necesario imprimir los formularios ocultos en el pie de página.
        $rules = CF7_OptionLimiter_DB::get_limits_by_form( $form_id ); // Recupera las reglas existentes para el formulario actual.
        $redirect = add_query_arg( array( 'page' => 'wpcf7', 'post' => $form_id, 'action' => 'edit' ), admin_url( 'admin.php' ) ); // Calcula la URL de retorno hacia el editor de Contact Form 7.
        $prefill_rule = null; // Inicializa la variable que permitirá precargar una regla solicitada desde la tabla general.
        if ( isset( $_GET['cf7_ol_rule'] ) ) { // Comprueba si se solicitó precargar una regla concreta.
            $requested_rule_id = (int) $_GET['cf7_ol_rule']; // Convierte el parámetro recibido a entero para evitar valores no válidos.
            if ( $requested_rule_id > 0 ) { // Comprueba que el identificador sea positivo.
                $requested_rule = CF7_OptionLimiter_DB::get_limit_by_id( $requested_rule_id ); // Recupera la regla indicada para validar su pertenencia.
                if ( ! empty( $requested_rule ) && (int) $requested_rule['form_id'] === (int) $form_id ) { // Comprueba que la regla pertenece al formulario actual.
                    $prefill_rule = array( // Construye la estructura que se expondrá al script para precargar los campos.
                        'id'            => (int) $requested_rule['id'], // Identificador interno de la regla.
                        'field_name'    => $requested_rule['field_name'], // Nombre del campo asociado.
                        'option_value'  => $requested_rule['option_value'], // Valor específico de la opción limitada.
                        'max_count'     => (int) $requested_rule['max_count'], // Máximo permitido configurado.
                        'limit_period'  => $requested_rule['limit_period'], // Periodo configurado para el reinicio del contador.
                        'hide_exhausted'=> isset( $requested_rule['hide_exhausted'] ) ? (int) $requested_rule['hide_exhausted'] : 0, // Indicador de si la opción debe ocultarse al agotarse.
                        'custom_message'=> $requested_rule['custom_message'], // Mensaje personalizado configurado para la regla.
                    );
                }
            }
        }
        ?>
        <div class="cf7-option-limiter-admin cf7-option-limiter-editor"> <!-- Contenedor principal que reutiliza los estilos del panel administrativo. -->
            <div class="cf7-option-limiter-panel cf7-option-limiter-panel-form"> <!-- Columna con el formulario de gestión para este contacto. -->
                <h3><?php echo esc_html__( 'Gestionar límites para este formulario', 'cf7-option-limiter' ); ?></h3> <!-- Título descriptivo del panel incrustado. -->
                <p class="description"><?php echo esc_html__( 'Configura los límites directamente desde el editor para mantener sincronizados los valores con este formulario.', 'cf7-option-limiter' ); ?></p> <!-- Texto introductorio que explica el flujo. -->
                <div class="cf7-option-limiter-heading-actions"> <!-- Contenedor de accesos rápidos dentro del panel incrustado. -->
                    <a class="cf7-option-limiter-info-link" target="_blank" href="<?php echo esc_url( CF7_OptionLimiter_Docs::get_page_url() ); ?>"> <!-- Enlace directo a la documentación desde el editor en una pestaña nueva. -->
                        <span class="dashicons dashicons-info"></span> <!-- Icono que representa información disponible. -->
                        <span class="screen-reader-text"><?php echo esc_html__( 'Consultar documentos y preguntas frecuentes del limitador', 'cf7-option-limiter' ); ?></span> <!-- Texto accesible que describe la acción del enlace. -->
                    </a>
                </div>
                <input type="hidden" id="cf7-ol-form-id" name="form_id" value="<?php echo esc_attr( $form_id ); ?>" form="cf7-option-limiter-embedded-form" data-autoload="1" /> <!-- Identificador del formulario actual asociado al formulario oculto. -->
                <input type="hidden" name="rule_id" id="cf7-ol-rule-id" value="0" form="cf7-option-limiter-embedded-form" /> <!-- Identificador de la regla en edición dentro del editor incrustado. -->
                <input type="hidden" name="original_form_id" id="cf7-ol-original-form-id" value="<?php echo esc_attr( $form_id ); ?>" form="cf7-option-limiter-embedded-form" /> <!-- Conserva el ID original para compatibilidad con el manejador de guardado. -->
                <input type="hidden" name="original_field_name" id="cf7-ol-original-field-name" value="" form="cf7-option-limiter-embedded-form" /> <!-- Campo oculto que registrará el nombre original del campo al editar. -->
                <input type="hidden" name="original_option_value" id="cf7-ol-original-option-value" value="" form="cf7-option-limiter-embedded-form" /> <!-- Campo oculto que registra la opción original al editar. -->
                <input type="hidden" name="redirect_to" id="cf7-ol-redirect" value="<?php echo esc_url( $redirect ); ?>" form="cf7-option-limiter-embedded-form" /> <!-- URL de retorno que asegura volver al editor tras guardar. -->
                <div class="cf7-option-limiter-field"> <!-- Grupo informativo del formulario asociado. -->
                    <label><?php echo esc_html__( 'Formulario seleccionado', 'cf7-option-limiter' ); ?></label> <!-- Etiqueta informativa para el usuario. -->
                    <p class="cf7-option-limiter-field-note"><?php echo esc_html( $form_title ); ?></p> <!-- Muestra el nombre del formulario en edición. -->
                </div>
                <div class="cf7-option-limiter-field"> <!-- Campo para seleccionar el nombre del campo a limitar. -->
                    <label for="cf7-ol-field-name"><?php echo esc_html__( 'Campo a limitar', 'cf7-option-limiter' ); ?></label><!-- Etiqueta del campo correspondiente. -->
                    <select id="cf7-ol-field-name" name="field_name" aria-describedby="cf7-ol-field-status" required form="cf7-option-limiter-embedded-form"> <!-- Selector que se rellenará con los campos detectados. -->
                        <option value="" disabled selected><?php echo esc_html__( 'Selecciona un campo disponible', 'cf7-option-limiter' ); ?></option> <!-- Opción inicial que guía al usuario. -->
                    </select>
                    <p class="cf7-option-limiter-field-note" id="cf7-ol-field-status"><?php echo esc_html__( 'Analiza el formulario para habilitar el desplegable y elegir el campo correspondiente.', 'cf7-option-limiter' ); ?></p> <!-- Mensaje de ayuda que describe el nuevo flujo basado únicamente en desplegables. -->
                </div>
                <div class="cf7-option-limiter-field"> <!-- Campo para seleccionar el valor específico a limitar. -->
                    <label for="cf7-ol-option-value"><?php echo esc_html__( 'Opción específica', 'cf7-option-limiter' ); ?></label><!-- Etiqueta del campo correspondiente. -->
                    <select id="cf7-ol-option-value" name="option_value" aria-describedby="cf7-ol-option-status" required form="cf7-option-limiter-embedded-form" disabled> <!-- Selector dependiente que mostrará las opciones disponibles del campo elegido. -->
                        <option value="" disabled selected><?php echo esc_html__( 'Selecciona un campo para ver las opciones disponibles', 'cf7-option-limiter' ); ?></option> <!-- Opción inicial que indica el siguiente paso. -->
                    </select>
                    <p class="cf7-option-limiter-field-note" id="cf7-ol-option-status"><?php echo esc_html__( 'Elige primero un campo y, después, selecciona la opción concreta desde este desplegable.', 'cf7-option-limiter' ); ?></p> <!-- Mensaje contextual adaptado al uso exclusivo de desplegables. -->
                </div>
                <div class="cf7-option-limiter-field"> <!-- Grupo de campo para introducir el máximo permitido. -->
                    <label for="cf7-ol-max-count"><?php echo esc_html__( 'Máximo permitido', 'cf7-option-limiter' ); ?></label> <!-- Etiqueta del campo. -->
                    <input id="cf7-ol-max-count" type="number" min="1" name="max_count" value="1" required form="cf7-option-limiter-embedded-form" /> <!-- Campo numérico para el límite máximo. -->
                </div>
                <div class="cf7-option-limiter-field"> <!-- Grupo para seleccionar el periodo de limitación. -->
                    <label for="cf7-ol-limit-period"><?php echo esc_html__( 'Periodo de límite', 'cf7-option-limiter' ); ?></label> <!-- Etiqueta del selector. -->
                    <select id="cf7-ol-limit-period" name="limit_period" form="cf7-option-limiter-embedded-form"> <!-- Selector de tipo de periodo. -->
                        <option value="none"><?php echo esc_html__( 'Total (sin reinicio)', 'cf7-option-limiter' ); ?></option> <!-- Opción para límite total. -->
                        <option value="hour"><?php echo esc_html__( 'Por hora', 'cf7-option-limiter' ); ?></option> <!-- Opción para límite horario. -->
                        <option value="day"><?php echo esc_html__( 'Por día', 'cf7-option-limiter' ); ?></option> <!-- Opción para límite diario. -->
                        <option value="week"><?php echo esc_html__( 'Por semana', 'cf7-option-limiter' ); ?></option> <!-- Opción para límite semanal. -->
                    </select>
                </div>
                <div class="cf7-option-limiter-field cf7-option-limiter-field-checkbox"> <!-- Grupo que permite decidir si las opciones agotadas se ocultarán. -->
                    <label for="cf7-ol-hide-exhausted"> <!-- Etiqueta que envuelve la casilla para facilitar su interacción. -->
                        <input type="checkbox" id="cf7-ol-hide-exhausted" name="hide_exhausted" value="1" form="cf7-option-limiter-embedded-form" /> <!-- Casilla que controla el comportamiento de visibilidad al agotarse. -->
                        <?php echo esc_html__( 'Ocultar automáticamente las opciones agotadas en este formulario', 'cf7-option-limiter' ); ?> <!-- Texto descriptivo que resume la acción del checkbox. -->
                    </label>
                    <p class="description"><?php echo esc_html__( 'Cuando no se marque, las opciones agotadas seguirán visibles pero se bloquearán al intentar seleccionarlas.', 'cf7-option-limiter' ); ?></p> <!-- Mensaje aclaratorio que describe el comportamiento alternativo. -->
                </div>
                <div class="cf7-option-limiter-field"> <!-- Grupo de campo para introducir mensaje personalizado. -->
                    <label for="cf7-ol-custom-message"><?php echo esc_html__( 'Mensaje personalizado al agotarse', 'cf7-option-limiter' ); ?></label> <!-- Etiqueta del campo. -->
                    <input id="cf7-ol-custom-message" type="text" name="custom_message" placeholder="<?php echo esc_attr__( 'Ejemplo: Esta opción se ha agotado por ahora.', 'cf7-option-limiter' ); ?>" form="cf7-option-limiter-embedded-form" /> <!-- Campo de texto opcional para el mensaje. -->
                </div>
                <div class="cf7-option-limiter-actions"> <!-- Contenedor para los botones de acción. -->
                    <button type="button" class="button button-primary" id="cf7-ol-submit"><?php echo esc_html__( 'Guardar límite', 'cf7-option-limiter' ); ?></button> <!-- Botón controlado por JavaScript que envía el formulario oculto mediante requestSubmit. -->
                    <button type="button" class="button button-secondary" id="cf7-ol-cancel-edit" style="display:none;"><?php echo esc_html__( 'Cancelar edición', 'cf7-option-limiter' ); ?></button> <!-- Botón que permite abandonar el modo edición. -->
                </div>
                <?php if ( $prefill_rule ) : ?>
                    <div id="cf7-ol-prefill" data-rule="<?php echo esc_attr( wp_json_encode( $prefill_rule ) ); ?>" data-form-id="<?php echo esc_attr( $form_id ); ?>" style="display:none;"></div> <!-- Contenedor oculto que expone al script la regla que debe precargarse automáticamente. -->
                <?php endif; ?>
            </div>
            <div class="cf7-option-limiter-panel cf7-option-limiter-panel-table"> <!-- Columna que lista las reglas del formulario. -->
                <h3><?php echo esc_html__( 'Reglas configuradas en este formulario', 'cf7-option-limiter' ); ?></h3> <!-- Encabezado de la tabla filtrada. -->
                <table class="wp-list-table widefat fixed striped cf7-option-limiter-table"> <!-- Tabla que reutiliza el estilo administrativo. -->
                    <thead> <!-- Cabecera de la tabla. -->
                        <tr> <!-- Fila de cabecera. -->
                            <th><?php echo esc_html__( 'Campo', 'cf7-option-limiter' ); ?></th> <!-- Cabecera para el nombre del campo. -->
                            <th><?php echo esc_html__( 'Opción', 'cf7-option-limiter' ); ?></th> <!-- Cabecera para el valor limitado. -->
                            <th><?php echo esc_html__( 'Máximo', 'cf7-option-limiter' ); ?></th> <!-- Cabecera para el máximo permitido. -->
                            <th><?php echo esc_html__( 'Usos actuales', 'cf7-option-limiter' ); ?></th> <!-- Cabecera para el contador actual. -->
                            <th><?php echo esc_html__( 'Periodo', 'cf7-option-limiter' ); ?></th> <!-- Cabecera para el periodo configurado. -->
                            <th><?php echo esc_html__( 'Mensaje', 'cf7-option-limiter' ); ?></th> <!-- Cabecera para el mensaje personalizado. -->
                            <th><?php echo esc_html__( 'Oculta agotadas', 'cf7-option-limiter' ); ?></th> <!-- Cabecera que indica si la opción desaparece cuando se agota. -->
                            <th><?php echo esc_html__( 'Acciones', 'cf7-option-limiter' ); ?></th> <!-- Cabecera para las acciones disponibles. -->
                        </tr>
                    </thead>
                    <tbody> <!-- Cuerpo de la tabla de reglas. -->
                        <?php if ( empty( $rules ) ) : ?>
                            <tr class="cf7-ol-empty-row"> <!-- Fila mostrada cuando no existen reglas para este formulario e identificada para poder eliminarla dinámicamente tras el primer guardado. -->
                                <td colspan="8"><?php echo esc_html__( 'Este formulario aún no tiene límites configurados.', 'cf7-option-limiter' ); ?></td> <!-- Mensaje informativo. -->
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $rules as $rule ) : ?>
                                <tr data-rule-id="<?php echo esc_attr( $rule['id'] ); ?>"> <!-- Fila individual identificada por el ID de la regla para permitir actualizaciones dinámicas desde JavaScript. -->
                                    <td><?php echo esc_html( $rule['field_name'] ); ?></td> <!-- Muestra el nombre del campo. -->
                                    <td><?php echo esc_html( $rule['option_value'] ); ?></td> <!-- Muestra el valor específico limitado. -->
                                    <td><?php echo esc_html( $rule['max_count'] ); ?></td> <!-- Muestra el máximo configurado. -->
                                    <td><?php echo esc_html( $rule['current_count'] ); ?></td> <!-- Muestra el contador actual almacenado. -->
                                    <td><?php echo esc_html( CF7_OptionLimiter_Admin::get_period_label( $rule['limit_period'] ) ); ?></td> <!-- Reutiliza el método del panel principal para traducir el periodo. -->
                                    <td><?php echo esc_html( $rule['custom_message'] ); ?></td> <!-- Muestra el mensaje personalizado si existe. -->
                                    <td><?php echo esc_html( $rule['hide_exhausted'] ? __( 'Sí', 'cf7-option-limiter' ) : __( 'No', 'cf7-option-limiter' ) ); ?></td> <!-- Indica de forma textual si la opción se ocultará al agotarse. -->
                                    <td> <!-- Columna con las acciones disponibles. -->
                                        <button type="button" class="button button-link cf7-option-limiter-edit" data-rule-id="<?php echo esc_attr( $rule['id'] ); ?>" data-form-id="<?php echo esc_attr( $form_id ); ?>" data-form-title="<?php echo esc_attr( $form_title ); ?>" data-field-name="<?php echo esc_attr( $rule['field_name'] ); ?>" data-option-value="<?php echo esc_attr( $rule['option_value'] ); ?>" data-hide-exhausted="<?php echo esc_attr( $rule['hide_exhausted'] ); ?>" data-max-count="<?php echo esc_attr( $rule['max_count'] ); ?>" data-limit-period="<?php echo esc_attr( $rule['limit_period'] ); ?>" data-custom-message="<?php echo esc_attr( $rule['custom_message'] ); ?>"><?php echo esc_html__( 'Editar', 'cf7-option-limiter' ); ?></button> <!-- Botón que activa el modo edición dentro del panel incrustado incluyendo la preferencia de ocultación. -->
                                        <button type="button" class="button button-secondary cf7-option-limiter-delete" data-rule-id="<?php echo esc_attr( $rule['id'] ); ?>" data-redirect="<?php echo esc_attr( $redirect ); ?>" data-confirm="<?php echo esc_attr__( '¿Seguro que deseas eliminar esta regla?', 'cf7-option-limiter' ); ?>"><?php echo esc_html__( 'Eliminar', 'cf7-option-limiter' ); ?></button> <!-- Botón que dispara la eliminación mediante el formulario oculto. -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Imprime los formularios ocultos que se emplean para enviar los datos sin interferir con el formulario principal de Contact Form 7.
     *
     * @return void
     */
    public static function print_hidden_forms() { // Genera los formularios auxiliares sólo cuando el panel se ha utilizado.
        if ( ! self::$needs_hidden_forms ) { // Comprueba si el panel se ha renderizado en la vista actual.
            return; // Evita imprimir formularios innecesarios en otras pantallas.
        }
        ?>
        <form id="cf7-option-limiter-embedded-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:none;"> <!-- Formulario oculto que procesa la creación o actualización de reglas. -->
            <?php wp_nonce_field( 'cf7_option_limiter_save', 'cf7_option_limiter_nonce' ); // Inyecta el nonce reutilizado por el manejador de guardado. ?>
            <input type="hidden" name="action" value="cf7_option_limiter_save" /> <!-- Acción admin-post reutilizada por el panel principal. -->
            <input type="hidden" name="form_id" id="cf7-ol-hidden-form-id" value="" /> <!-- Campo oculto que almacenará el identificador del formulario antes de enviar la petición. -->
            <input type="hidden" name="field_name" id="cf7-ol-hidden-field-name" value="" /> <!-- Campo oculto que replicará el nombre del campo seleccionado en la interfaz. -->
            <input type="hidden" name="option_value" id="cf7-ol-hidden-option-value" value="" /> <!-- Campo oculto que guardará el valor específico elegido por la persona administradora. -->
            <input type="hidden" name="max_count" id="cf7-ol-hidden-max-count" value="" /> <!-- Campo oculto que conservará el máximo permitido configurado antes de guardar. -->
            <input type="hidden" name="limit_period" id="cf7-ol-hidden-limit-period" value="" /> <!-- Campo oculto que trasladará el periodo seleccionado en el panel incrustado. -->
            <input type="hidden" name="custom_message" id="cf7-ol-hidden-custom-message" value="" /> <!-- Campo oculto que llevará el mensaje personalizado introducido por la persona administradora. -->
            <input type="hidden" name="hide_exhausted" id="cf7-ol-hidden-hide-exhausted" value="" disabled="disabled" /> <!-- Campo oculto que sólo se habilitará cuando la casilla de ocultar opciones agotadas esté activada. -->
            <input type="hidden" name="rule_id" id="cf7-ol-hidden-rule-id" value="0" /> <!-- Campo oculto que replica el identificador de la regla en edición para el manejador PHP. -->
            <input type="hidden" name="original_form_id" id="cf7-ol-hidden-original-form-id" value="" /> <!-- Campo oculto que conserva el identificador original del formulario para comparaciones internas. -->
            <input type="hidden" name="original_field_name" id="cf7-ol-hidden-original-field-name" value="" /> <!-- Campo oculto que guarda el nombre del campo original durante una edición. -->
            <input type="hidden" name="original_option_value" id="cf7-ol-hidden-original-option-value" value="" /> <!-- Campo oculto que almacena la opción original cuando se está editando una regla existente. -->
            <input type="hidden" name="redirect_to" id="cf7-ol-hidden-redirect" value="" /> <!-- Campo oculto que replica la URL de retorno para mantener la navegación correcta tras guardar. -->
        </form>
        <form id="cf7-option-limiter-embedded-delete" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:none;"> <!-- Formulario oculto que procesa las eliminaciones desde el editor. -->
            <?php wp_nonce_field( 'cf7_option_limiter_delete', 'cf7_option_limiter_delete_nonce' ); // Nonce reutilizado por el manejador de borrado. ?>
            <input type="hidden" name="action" value="cf7_option_limiter_delete" /> <!-- Acción admin-post utilizada para eliminar reglas. -->
            <input type="hidden" name="rule_id" id="cf7-ol-delete-rule-id" value="0" /> <!-- Campo que recibirá dinámicamente el ID de la regla a eliminar. -->
            <input type="hidden" name="redirect_to" id="cf7-ol-delete-redirect" value="" /> <!-- Campo que se rellenará con la URL de retorno al editor. -->
        </form>
        <?php
        self::$needs_hidden_forms = false; // Restablece el indicador para evitar imprimir formularios múltiples veces en la misma petición.
    }

    /**
     * Obtiene el identificador del formulario desde la instancia proporcionada.
     *
     * @param WPCF7_ContactForm $contact_form Instancia entregada por el editor.
     *
     * @return int
     */
    protected static function resolve_form_id( $contact_form ) { // Determina el identificador único del formulario.
        if ( is_object( $contact_form ) && method_exists( $contact_form, 'id' ) ) { // Comprueba si la instancia expone el método id().
            return (int) $contact_form->id(); // Devuelve el identificador directamente desde el método.
        }
        if ( is_object( $contact_form ) && isset( $contact_form->id ) ) { // Comprueba si el objeto expone la propiedad id.
            return (int) $contact_form->id; // Devuelve la propiedad convertida a entero.
        }
        return 0; // Devuelve cero cuando no se puede determinar el ID.
    }

    /**
     * Obtiene el título legible del formulario en edición.
     *
     * @param WPCF7_ContactForm $contact_form Instancia entregada por el editor.
     *
     * @return string
     */
    protected static function resolve_form_title( $contact_form ) { // Determina el título del formulario para mostrarlo en la interfaz.
        if ( is_object( $contact_form ) && method_exists( $contact_form, 'title' ) ) { // Comprueba si la instancia expone el método title().
            return (string) $contact_form->title(); // Devuelve el título proporcionado por la instancia.
        }
        if ( is_object( $contact_form ) && isset( $contact_form->title ) ) { // Comprueba si el objeto expone la propiedad title.
            return (string) $contact_form->title; // Devuelve la propiedad convertida a cadena.
        }
        return __( 'Formulario sin título', 'cf7-option-limiter' ); // Devuelve un texto alternativo cuando no se detecta título.
    }
}
