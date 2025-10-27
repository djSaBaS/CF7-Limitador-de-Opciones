/* global CF7OptionLimiterAdmin */
// Asegura que el script se ejecute cuando todo el DOM esté listo.
jQuery( document ).ready( function( $ ) { // Inicia la función principal utilizando jQuery en modo no conflicto.

    var $formSelect = $( '#cf7-ol-form-id' ); // Referencia al campo que almacena el identificador del formulario.
    var $fieldSelect = $( '#cf7-ol-field-name' ); // Referencia al selector desplegable que mostrará los campos detectados.
    var $fieldStatus = $( '#cf7-ol-field-status' ); // Referencia al texto auxiliar que informa del estado del campo.
    var $optionSelect = $( '#cf7-ol-option-value' ); // Referencia al selector dependiente destinado al valor específico de la opción.
    var $optionStatus = $( '#cf7-ol-option-status' ); // Referencia al mensaje informativo asociado al campo de opciones.
    var $maxInput = $( '#cf7-ol-max-count' ); // Referencia al campo numérico que define el máximo permitido.
    var $limitSelect = $( '#cf7-ol-limit-period' ); // Referencia al selector que determina el periodo de reseteo.
    var $customInput = $( '#cf7-ol-custom-message' ); // Referencia al campo opcional de mensaje personalizado.
    var $hideCheckbox = $( '#cf7-ol-hide-exhausted' ); // Referencia a la casilla que controla si las opciones agotadas se ocultan.
    var $submitButton = $( '#cf7-ol-submit' ); // Referencia al botón principal que envía el formulario.
    var $cancelButton = $( '#cf7-ol-cancel-edit' ); // Referencia al botón que cancela el modo edición.
    var $ruleInput = $( '#cf7-ol-rule-id' ); // Referencia al campo oculto que almacena el identificador de la regla en edición.
    var $originalFormInput = $( '#cf7-ol-original-form-id' ); // Referencia al campo oculto que conserva el identificador original del formulario.
    var $originalFieldInput = $( '#cf7-ol-original-field-name' ); // Referencia al campo oculto que conserva el nombre original del campo.
    var $originalOptionInput = $( '#cf7-ol-original-option-value' ); // Referencia al campo oculto que conserva la opción original.
    var $redirectInput = $( '#cf7-ol-redirect' ); // Referencia al campo oculto que almacena la URL de retorno tras guardar.
    var $embeddedForm = $( '#cf7-option-limiter-embedded-form' ); // Referencia al formulario oculto que se enviará programáticamente.
    var $hiddenFormId = $( '#cf7-ol-hidden-form-id' ); // Referencia al campo oculto definitivo que recibirá el identificador del formulario antes del envío.
    var $hiddenFieldName = $( '#cf7-ol-hidden-field-name' ); // Referencia al campo oculto definitivo que recibirá el nombre del campo seleccionado.
    var $hiddenOptionValue = $( '#cf7-ol-hidden-option-value' ); // Referencia al campo oculto definitivo que conservará el valor específico elegido.
    var $hiddenMaxCount = $( '#cf7-ol-hidden-max-count' ); // Referencia al campo oculto definitivo que almacenará el máximo permitido.
    var $hiddenLimitPeriod = $( '#cf7-ol-hidden-limit-period' ); // Referencia al campo oculto definitivo que replicará el periodo configurado.
    var $hiddenCustomMessage = $( '#cf7-ol-hidden-custom-message' ); // Referencia al campo oculto definitivo que llevará el mensaje personalizado al manejador PHP.
    var $hiddenHideExhausted = $( '#cf7-ol-hidden-hide-exhausted' ); // Referencia al campo oculto definitivo que representará la casilla de ocultar opciones agotadas.
    var $hiddenRuleId = $( '#cf7-ol-hidden-rule-id' ); // Referencia al campo oculto definitivo que replicará el identificador de la regla en edición.
    var $hiddenOriginalFormId = $( '#cf7-ol-hidden-original-form-id' ); // Referencia al campo oculto definitivo que conservará el identificador original del formulario.
    var $hiddenOriginalFieldName = $( '#cf7-ol-hidden-original-field-name' ); // Referencia al campo oculto definitivo que almacenará el nombre original del campo.
    var $hiddenOriginalOptionValue = $( '#cf7-ol-hidden-original-option-value' ); // Referencia al campo oculto definitivo que guardará la opción original.
    var $hiddenRedirect = $( '#cf7-ol-hidden-redirect' ); // Referencia al campo oculto definitivo que transportará la URL de retorno.
    var $rulesTableBody = $( '.cf7-option-limiter-editor table tbody' ); // Referencia al cuerpo de la tabla para insertar o actualizar filas sin necesidad de recargar.
    var isAjaxSubmitting = false; // Indicador que permite evitar que el usuario dispare múltiples peticiones simultáneas.
    var saveLabel = ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.saveLabel ) ? CF7OptionLimiterAdmin.i18n.saveLabel : 'Guardar límite'; // Etiqueta por defecto para el botón principal.
    var updateLabel = ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.updateLabel ) ? CF7OptionLimiterAdmin.i18n.updateLabel : 'Actualizar límite'; // Etiqueta mostrada cuando se edita una regla existente.
    var isEditing = false; // Indicador de si el formulario se encuentra en modo edición.
    var pendingRuleData = null; // Estructura temporal utilizada para aplicar datos una vez que los campos estén cargados.
    var $accessibleNotice = ensureAccessibleNotice(); // Contenedor reutilizable que anuncia errores en un área con soporte para lectores de pantalla.
    var $inlineNotice = ensureInlineNoticeContainer(); // Contenedor visible reutilizable para mostrar mensajes informativos, de éxito o de error.

    resetFieldInput( CF7OptionLimiterAdmin.i18n.fieldManual, false ); // Inicializa el selector de campos mostrando el mensaje por defecto.
    resetOptionInput( false ); // Inicializa el selector de opciones dejándolo deshabilitado hasta que se elija un campo.
    $submitButton.text( saveLabel ); // Asegura que el botón principal muestre la etiqueta adecuada al cargar la interfaz.
    updateSubmitState(); // Configura el botón de envío como deshabilitado hasta que el usuario complete todas las selecciones.

    $submitButton.on( 'click', function( event ) { // Gestiona el clic para interceptar el envío estándar y delegarlo en AJAX.
        event.preventDefault(); // Evita que el navegador envíe el formulario ancestro del editor de Contact Form 7.
        if ( isAjaxSubmitting ) { // Comprueba si existe una petición en curso para evitar duplicados.
            return; // Finaliza sin realizar acciones cuando ya se está guardando.
        }
        if ( ! validateSelectionsBeforeSubmit() ) { // Verifica que las selecciones obligatorias estén completas antes de continuar.
            return; // Detiene el flujo cuando falta información esencial.
        }
        syncEmbeddedFormValues(); // Copia los valores visibles a los campos ocultos para conservar un respaldo coherente en caso de recurrir al flujo tradicional.
        var payload = collectAjaxPayload(); // Prepara la carga útil que se enviará mediante AJAX.
        submitRuleViaAjax( payload ); // Inicia la petición AJAX centralizada que gestionará el guardado.
    } );

    // Escucha los cambios en el campo que almacena el formulario para recargar la estructura detectada.
    $formSelect.on( 'change', function() { // Maneja cambios en el identificador de formulario seleccionado.
        if ( isEditing ) { // Comprueba si se estaba editando una regla para restablecer el estado.
            clearEditMode(); // Restablece el formulario antes de cargar los nuevos campos.
        }
        loadFieldsForForm( $( this ).val() ); // Llama a la función que realiza la petición AJAX correspondiente.
        updateSubmitState(); // Ajusta el estado del botón de envío tras modificar el formulario seleccionado.
    } );

    // Carga automáticamente los campos cuando el formulario llega preseleccionado.
    if ( $formSelect.data( 'autoload' ) && $formSelect.val() ) { // Comprueba si se solicitó una precarga automática.
        loadFieldsForForm( $formSelect.val() ); // Ejecuta la carga inicial de campos cuando el formulario ya está definido.
    }

    // Escucha los cambios en el selector de campos para actualizar el listado de opciones dependientes.
    $fieldSelect.on( 'change', function() { // Detecta la elección de un campo concreto.
        var fieldName = $( this ).val(); // Recupera el nombre del campo seleccionado.
        if ( ! fieldName ) { // Comprueba si se seleccionó la opción vacía.
            resetOptionInput( false ); // Restablece el selector de opciones dejando sólo la opción por defecto.
            updateSubmitState(); // Refuerza la desactivación del botón porque falta un campo seleccionado.
            return; // Finaliza porque no hay un campo válido que analizar.
        }
        var populated = populateOptionsForField( fieldName ); // Rellena el selector de opciones para el campo escogido.
        if ( ! populated ) { // Comprueba si no se pudieron cargar opciones.
            $optionSelect.val( '' ); // Limpia cualquier selección previa para evitar inconsistencias.
        }
        updateSubmitState(); // Recalcula la disponibilidad del botón tras la nueva selección de campo.
    } );

    $optionSelect.on( 'change', function() { // Escucha las variaciones en el selector dependiente de opciones.
        updateSubmitState(); // Ajusta el estado del botón principal para reflejar la presencia o ausencia de una opción concreta.
    } );

    // Activa el modo edición cuando el usuario pulsa sobre un botón de editar en la tabla de reglas.
    $( document ).on( 'click', '.cf7-option-limiter-edit', function( event ) { // Escucha los clics sobre los botones de edición.
        event.preventDefault(); // Evita que el botón provoque desplazamientos innecesarios.
        var data = $( this ).data(); // Recupera todos los atributos de datos asociados al botón.
        if ( ! data || ( ! data.ruleId && ! data.id ) ) { // Comprueba que la información mínima esté disponible.
            return; // Finaliza si faltan datos esenciales para editar.
        }
        startRuleEdit( data ); // Inicia el flujo de edición utilizando los datos recogidos del botón.
    } );

    // Gestiona el botón de cancelar edición restaurando los valores por defecto.
    $cancelButton.on( 'click', function( event ) { // Escucha el clic sobre el botón de cancelar.
        event.preventDefault(); // Evita el envío del formulario.
        clearEditMode(); // Restablece el formulario a su estado inicial.
    } );

    // Gestiona los botones de eliminación cuando el panel se encuentra incrustado en Contact Form 7.
    $( document ).on( 'click', '.cf7-option-limiter-delete', function( event ) { // Maneja los clics sobre los botones de eliminación personalizados.
        event.preventDefault(); // Evita acciones por defecto del navegador.
        var ruleId = $( this ).data( 'ruleId' ); // Recupera el identificador de la regla a eliminar.
        var redirect = $( this ).data( 'redirect' ); // Recupera la URL de retorno deseada.
        var confirmMessage = ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.deleteConfirm ) ? CF7OptionLimiterAdmin.i18n.deleteConfirm : ''; // Mensaje de confirmación traducible.
        confirmMessage = $( this ).data( 'confirm' ) || confirmMessage; // Permite que cada botón defina su propio mensaje.
        if ( ! ruleId ) { // Comprueba que exista un identificador válido.
            return; // Finaliza si falta el ID de la regla.
        }
        if ( confirmMessage && ! window.confirm( confirmMessage ) ) { // Solicita confirmación al usuario antes de eliminar.
            return; // Detiene la operación si el usuario cancela.
        }
        var $deleteForm = $( '#cf7-option-limiter-embedded-delete' ); // Localiza el formulario oculto que enviará la eliminación.
        if ( ! $deleteForm.length ) { // Comprueba que el formulario exista en el DOM.
            return; // Finaliza silenciosamente si no se encuentra el formulario auxiliar.
        }
        $deleteForm.find( '#cf7-ol-delete-rule-id' ).val( ruleId ); // Inserta el identificador de la regla en el formulario oculto.
        if ( redirect ) { // Comprueba si se proporcionó una URL de retorno específica.
            $deleteForm.find( '#cf7-ol-delete-redirect' ).val( redirect ); // Actualiza la URL de retorno cuando se facilita una personalizada.
        }
        $deleteForm.trigger( 'submit' ); // Envía el formulario oculto para procesar la eliminación.
    } );

    // Observa cuándo finaliza la carga de campos para aplicar datos pendientes de edición.
    $( document ).on( 'cf7OptionLimiterFieldsLoaded', function( event, loadedFormId ) { // Escucha el evento disparado tras completar la carga AJAX.
        if ( ! pendingRuleData ) { // Comprueba si hay datos pendientes.
            return; // Finaliza si no existe ninguna edición en espera.
        }
        var targetFormId = pendingRuleData.__targetFormId ? String( pendingRuleData.__targetFormId ) : ''; // Recupera el formulario objetivo almacenado.
        if ( targetFormId && String( loadedFormId ) !== targetFormId ) { // Comprueba si la carga pertenece al formulario esperado.
            return; // Espera a que se cargue el formulario correcto.
        }
        if ( finalizeRuleEdit() ) { // Intenta aplicar los datos pendientes una vez que las opciones están disponibles.
            pendingRuleData = null; // Limpia la estructura temporal tras aplicar los datos.
        }
    } );

    // Si el formulario se reutiliza después de un envío, asegura que los estados vuelvan a su valor por defecto.
    $( document ).on( 'cf7OptionLimiterResetForm', function() { // Permite a otros scripts solicitar el reseteo del formulario.
        clearEditMode(); // Restablece los valores del formulario.
    } );

    // Detecta si existe información precargada desde la tabla general para iniciar la edición automáticamente.
    ( function preloadFromMarkup() { // Función autoejecutable que revisa la presencia de datos precargados.
        var $prefill = $( '#cf7-ol-prefill' ); // Localiza el contenedor oculto con los datos de precarga.
        if ( ! $prefill.length ) { // Comprueba si no se encontró el contenedor.
            return; // Finaliza silenciosamente cuando no hay información precargada.
        }
        var rawRule = $prefill.attr( 'data-rule' ); // Recupera la regla serializada en formato JSON.
        if ( ! rawRule ) { // Comprueba si la información está vacía.
            return; // Finaliza si no hay datos disponibles.
        }
        try {
            var parsed = JSON.parse( rawRule ); // Intenta convertir la cadena JSON en un objeto utilizable.
            if ( parsed ) { // Comprueba que la conversión haya producido un objeto.
                parsed.formId = parsed.formId || parsed.form_id || $prefill.data( 'formId' ) || $prefill.attr( 'data-form-id' ); // Garantiza que se conozca el formulario objetivo.
                startRuleEdit( parsed ); // Inicia el flujo de edición automática utilizando la información proporcionada.
            }
        } catch ( error ) { // Captura errores de parseo sin interrumpir el flujo de la página.
            if ( window.console && window.console.warn ) { // Comprueba la disponibilidad de la consola y del método warn.
                window.console.warn( 'CF7 Option Limiter: no se pudo interpretar la regla precargada.', error ); // Informa en consola para depuración.
            }
        }
    }() );

    function setHiddenFieldValue( $field, value ) { // Función auxiliar que asigna un valor seguro a un campo oculto definitivo.
        if ( ! $field.length ) { // Comprueba que el campo exista antes de operar.
            return; // Finaliza sin realizar cambios cuando el campo no está disponible.
        }
        var sanitizedValue = value; // Copia el valor recibido para poder normalizarlo sin afectar a la referencia original.
        if ( typeof sanitizedValue === 'undefined' || sanitizedValue === null ) { // Comprueba si el valor es indefinido o nulo.
            sanitizedValue = ''; // Sustituye los valores no definidos por una cadena vacía para evitar enviar "undefined".
        }
        sanitizedValue = String( sanitizedValue ); // Convierte el valor a cadena para garantizar la consistencia en el envío.
        $field.val( sanitizedValue ); // Asigna el valor normalizado al campo oculto correspondiente.
    }

    function syncEmbeddedFormValues() { // Función que replica todos los valores visibles en los campos del formulario oculto definitivo.
        if ( ! $embeddedForm.length ) { // Comprueba que el formulario oculto exista antes de intentar sincronizar los datos.
            return; // Finaliza silenciosamente cuando el formulario no está disponible.
        }
        setHiddenFieldValue( $hiddenFormId, $formSelect.val() ); // Traslada el identificador del formulario seleccionado.
        setHiddenFieldValue( $hiddenFieldName, $fieldSelect.val() ); // Copia el nombre del campo elegido en el panel.
        setHiddenFieldValue( $hiddenOptionValue, $optionSelect.val() ); // Copia el valor específico seleccionado en el desplegable dependiente.
        setHiddenFieldValue( $hiddenMaxCount, $maxInput.val() ); // Replica el máximo permitido introducido.
        setHiddenFieldValue( $hiddenLimitPeriod, $limitSelect.val() ); // Replica el periodo configurado para el límite.
        setHiddenFieldValue( $hiddenCustomMessage, $customInput.val() ); // Replica el mensaje personalizado introducido.
        setHiddenFieldValue( $hiddenRuleId, $ruleInput.val() ); // Copia el identificador de la regla cuando se edita una existente.
        setHiddenFieldValue( $hiddenOriginalFormId, $originalFormInput.val() ); // Replica el identificador original del formulario.
        setHiddenFieldValue( $hiddenOriginalFieldName, $originalFieldInput.val() ); // Replica el nombre original del campo cuando se edita.
        setHiddenFieldValue( $hiddenOriginalOptionValue, $originalOptionInput.val() ); // Replica la opción original para comparar cambios.
        setHiddenFieldValue( $hiddenRedirect, $redirectInput.val() ); // Copia la URL de retorno configurada en el panel.
        if ( $hiddenHideExhausted.length ) { // Comprueba que el campo oculto encargado del checkbox esté disponible.
            if ( $hideCheckbox.length && $hideCheckbox.is( ':checked' ) ) { // Evalúa si la casilla visible está marcada en la interfaz.
                setHiddenFieldValue( $hiddenHideExhausted, '1' ); // Establece el valor afirmativo cuando la casilla está activa.
                $hiddenHideExhausted.prop( 'disabled', false ); // Habilita el campo para que se envíe en la petición.
            } else {
                setHiddenFieldValue( $hiddenHideExhausted, '' ); // Limpia el valor cuando la casilla no está activa.
                $hiddenHideExhausted.prop( 'disabled', true ); // Deshabilita el campo para emular la ausencia de envío del checkbox desmarcado.
            }
        }
    }

    function collectAjaxPayload() { // Construye la carga útil que se enviará mediante AJAX reutilizando los controles visibles.
        var payload = { // Inicializa el objeto que contendrá los datos preparados para la petición.
            form_id: $formSelect.val(), // Incluye el identificador del formulario seleccionado actualmente.
            field_name: $fieldSelect.val(), // Incluye el nombre del campo elegido.
            option_value: $optionSelect.val(), // Incluye la opción concreta seleccionada.
            max_count: $maxInput.val(), // Incluye el valor máximo permitido configurado.
            limit_period: $limitSelect.val(), // Incluye el periodo seleccionado para el reinicio del contador.
            custom_message: $customInput.val(), // Incluye el mensaje personalizado configurado por la persona administradora.
            hide_exhausted: ( $hideCheckbox.length && $hideCheckbox.is( ':checked' ) ) ? '1' : '0', // Incluye el estado de la casilla de ocultar opciones agotadas.
            rule_id: $ruleInput.val(), // Incluye el identificador de la regla en edición cuando corresponde.
            original_form_id: $originalFormInput.val(), // Conserva el identificador del formulario original en modo edición.
            original_field_name: $originalFieldInput.val(), // Conserva el nombre del campo original en modo edición.
            original_option_value: $originalOptionInput.val(), // Conserva la opción original en modo edición.
            redirect_to: $redirectInput.val() // Propaga la URL de retorno utilizada por el flujo tradicional como respaldo.
        };
        return payload; // Devuelve la estructura lista para su envío mediante AJAX.
    }

    function submitRuleViaAjax( payload ) { // Envía la petición AJAX y gestiona tanto el éxito como los posibles errores.
        setLoadingState( true ); // Marca el inicio del proceso deshabilitando interacciones redundantes.
        clearInlineNotice(); // Oculta mensajes anteriores para evitar confusiones durante el guardado.
        var ajaxData = $.extend( { // Construye la carga útil final que se enviará al endpoint AJAX de WordPress.
            action: 'cf7_option_limiter_save_rule', // Identifica la acción que procesará la petición en PHP.
            nonce: CF7OptionLimiterAdmin.nonce // Incluye el nonce localizado para validar la petición en el servidor.
        }, payload );
        $.post( CF7OptionLimiterAdmin.ajaxUrl, ajaxData, null, 'json' ) // Ejecuta la petición AJAX utilizando jQuery para compatibilidad amplia.
            .done( function( response ) { // Gestiona la respuesta satisfactoria del servidor.
                setLoadingState( false ); // Restaura el estado del botón al completarse la petición.
                if ( ! response || ! response.success ) { // Comprueba si la respuesta indica un fallo lógico aunque la petición se haya resuelto.
                    var message = ( response && response.data && response.data.message ) ? response.data.message : ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.ajaxValidationError ? CF7OptionLimiterAdmin.i18n.ajaxValidationError : 'No se pudo guardar el límite con los datos proporcionados.' ); // Determina el mensaje más adecuado a mostrar.
                    showInlineNotice( 'error', message ); // Muestra el aviso accesible para informar del problema.
                    announceForAssistiveTech( message ); // Reitera el mensaje para tecnologías asistivas.
                    return; // Finaliza tras mostrar el error sin continuar con la actualización de la tabla.
                }
                var data = response.data || {}; // Recupera la estructura de datos devuelta por el servidor.
                updateTableWithRule( data.rule, data.removed_rule_id ); // Actualiza la tabla aplicando los cambios recibidos.
                showInlineNotice( 'success', data.message || ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.saveSuccess ? CF7OptionLimiterAdmin.i18n.saveSuccess : 'El límite se guardó correctamente.' ) ); // Comunica el resultado positivo.
                announceForAssistiveTech( data.message || ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.saveSuccess ? CF7OptionLimiterAdmin.i18n.saveSuccess : 'El límite se guardó correctamente.' ) ); // Anuncia el mensaje en la región accesible.
                clearEditMode(); // Restablece el formulario para permitir crear nuevas reglas rápidamente.
            } )
            .fail( function() { // Gestiona los errores de red o respuestas no válidas.
                setLoadingState( false ); // Restaura el estado del botón para permitir el respaldo tradicional.
                var fallbackMessage = ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.ajaxFallback ) ? CF7OptionLimiterAdmin.i18n.ajaxFallback : 'El guardado AJAX falló, se utilizará el envío clásico con recarga.'; // Determina el mensaje previo al respaldo.
                showInlineNotice( 'warning', fallbackMessage ); // Informa al usuario que se recurrirá al flujo tradicional.
                announceForAssistiveTech( fallbackMessage ); // Comunica el mismo aviso en la región accesible.
                submitViaLegacyFallback(); // Ejecuta el envío tradicional para garantizar que la regla se procese aunque AJAX no esté disponible.
            } );
    }

    function submitViaLegacyFallback() { // Recurre al formulario oculto para mantener compatibilidad con navegadores sin soporte AJAX.
        if ( ! $embeddedForm.length ) { // Comprueba que el formulario auxiliar exista antes de intentar utilizarlo.
            return; // Finaliza silenciosamente cuando el formulario no está disponible en el DOM.
        }
        var domForm = $embeddedForm.get( 0 ); // Obtiene la referencia DOM nativa al formulario oculto.
        if ( domForm && typeof domForm.requestSubmit === 'function' ) { // Comprueba si el navegador soporta requestSubmit.
            domForm.requestSubmit(); // Lanza el envío respetando las validaciones estándar del formulario oculto.
            return; // Finaliza tras ejecutar el envío moderno para evitar disparar el respaldo adicional.
        }
        $embeddedForm.trigger( 'submit' ); // Utiliza el envío tradicional cuando requestSubmit no está disponible.
    }

    function setLoadingState( loading ) { // Actualiza el estado del botón principal durante el proceso de guardado.
        isAjaxSubmitting = !! loading; // Normaliza el estado a un booleano estricto.
        if ( ! $submitButton.length ) { // Comprueba que el botón exista antes de manipularlo.
            return; // Finaliza cuando no hay botón sobre el que actuar.
        }
        if ( loading ) { // Ejecuta los ajustes cuando la petición está en curso.
            $submitButton.addClass( 'cf7-ol-is-busy' ); // Añade una clase que permite aplicar estilos visuales opcionales.
            $submitButton.text( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.saving ? CF7OptionLimiterAdmin.i18n.saving : 'Guardando…' ); // Muestra un texto descriptivo del estado de guardado.
        } else { // Ejecuta los ajustes cuando la petición ha finalizado.
            $submitButton.removeClass( 'cf7-ol-is-busy' ); // Elimina la clase de estado ocupado.
            $submitButton.text( isEditing ? updateLabel : saveLabel ); // Restaura la etiqueta original según el modo actual.
        }
        updateSubmitState(); // Recalcula la disponibilidad del botón considerando el nuevo estado de carga.
    }

    // Lógica que carga los campos del formulario seleccionado mediante AJAX.
    function loadFieldsForForm( formId ) { // Función que analiza el formulario seleccionado y obtiene sus campos.
        resetFieldInput( formId ? CF7OptionLimiterAdmin.i18n.loading : CF7OptionLimiterAdmin.i18n.selectForm, false ); // Limpia los datos previos y muestra el estado correspondiente.
        resetOptionInput( false ); // Limpia también las opciones dependientes del campo seleccionado.
        if ( ! formId ) { // Comprueba si no se proporcionó un formulario válido.
            return; // Finaliza cuando no hay identificador que analizar.
        }
        $.get( // Inicia la petición AJAX para analizar el formulario seleccionado.
            CF7OptionLimiterAdmin.ajaxUrl, // Utiliza el endpoint de WordPress preparado para AJAX.
            { // Define los parámetros necesarios para la acción.
                action: 'cf7_option_limiter_scan_form', // Acción que ejecuta el método ajax_scan_form en PHP.
                nonce: CF7OptionLimiterAdmin.nonce, // Nonce de seguridad que valida la petición.
                form_id: formId // Identificador del formulario que se desea analizar.
            }
        ).done( function( response ) { // Gestiona la respuesta satisfactoria del servidor.
            populateFieldInput( response, formId ); // Interpreta el resultado y rellena las opciones del selector correspondiente.
        } ).fail( function() { // Gestiona los fallos de la petición o respuestas inesperadas.
            setFieldStatus( CF7OptionLimiterAdmin.i18n.errorLoading ); // Informa al usuario de que deberá revisar el formulario.
            $( document ).trigger( 'cf7OptionLimiterFieldsLoaded', [ formId ] ); // Notifica igualmente que la carga ha finalizado para desbloquear posibles ediciones pendientes.
        } );
    }

    function setFieldStatus( message ) { // Define una función auxiliar para actualizar el texto informativo del campo.
        if ( $fieldStatus.length ) { // Comprueba que el elemento exista en el DOM.
            $fieldStatus.text( message ); // Actualiza el contenido textual con el mensaje proporcionado.
        }
        announceForAssistiveTech( message ); // Lanza el mensaje en la región accesible para mantener informados a los lectores de pantalla.
    }

    function setOptionStatus( message ) { // Define una función auxiliar para actualizar el texto informativo de las opciones.
        if ( $optionStatus.length ) { // Verifica que el elemento exista.
            $optionStatus.text( message ); // Sustituye el contenido textual por el mensaje deseado.
        }
        announceForAssistiveTech( message ); // Comunica el mismo mensaje a tecnologías asistivas para garantizar coherencia.
    }

    function resetFieldInput( statusMessage, preserveOptions ) { // Función que restablece el selector de campos a su estado inicial.
        if ( ! $fieldSelect.length ) { // Comprueba que el selector exista.
            return; // Finaliza si el selector no está presente (por ejemplo, en páginas sin el formulario).
        }
        if ( ! preserveOptions ) { // Evalúa si deben eliminarse las opciones previamente cargadas.
            $fieldSelect.find( 'option' ).not( ':first' ).remove(); // Elimina todas las opciones excepto el marcador inicial.
            $fieldSelect.data( 'fieldOptions', {} ); // Limpia el mapa de opciones detectadas para cada campo.
            $fieldSelect.prop( 'disabled', true ); // Deshabilita el selector hasta que se carguen nuevos datos.
        }
        $fieldSelect.val( '' ); // Restablece la selección a la opción por defecto.
        setFieldStatus( statusMessage || CF7OptionLimiterAdmin.i18n.fieldManual ); // Actualiza el mensaje contextual del campo.
        updateSubmitState(); // Deshabilita el botón principal hasta que se completen todas las selecciones nuevamente.
    }

    function resetOptionInput( preserveOptions ) { // Función que restablece el selector de opciones dependiente del campo seleccionado.
        if ( ! $optionSelect.length ) { // Comprueba que el selector exista.
            return; // Finaliza si el selector no está presente.
        }
        if ( ! preserveOptions ) { // Evalúa si deben eliminarse las opciones previamente cargadas.
            $optionSelect.find( 'option' ).not( ':first' ).remove(); // Elimina todas las opciones excepto el marcador inicial.
        }
        $optionSelect.val( '' ); // Restablece la selección a la opción por defecto.
        var hasOptions = $optionSelect.find( 'option' ).length > 1; // Comprueba si existen opciones adicionales.
        $optionSelect.prop( 'disabled', ! hasOptions ); // Habilita el selector sólo cuando hay opciones disponibles.
        setOptionStatus( CF7OptionLimiterAdmin.i18n.selectField ); // Actualiza el mensaje contextual recordando seleccionar primero un campo.
        updateSubmitState(); // Mantiene el botón deshabilitado mientras no se elija una opción concreta.
    }

    function populateFieldInput( response, requestedFormId ) { // Función que procesa la respuesta AJAX para rellenar el selector de campos.
        resetFieldInput( CF7OptionLimiterAdmin.i18n.fieldManual, false ); // Limpia cualquier dato previo antes de cargar la nueva información.
        if ( ! response || ! response.success || ! response.data || ! response.data.fields ) { // Verifica que la respuesta contenga la estructura esperada.
            setFieldStatus( CF7OptionLimiterAdmin.i18n.noFields ); // Informa que no se detectaron campos compatibles en el formulario.
            $( document ).trigger( 'cf7OptionLimiterFieldsLoaded', [ requestedFormId ] ); // Notifica que la carga ha finalizado aunque no existan campos.
            return; // Finaliza la ejecución al no disponer de datos útiles.
        }
        var fields = response.data.fields; // Extrae la lista de campos detectados por el servidor.
        if ( ! fields.length ) { // Comprueba si el arreglo está vacío.
            setFieldStatus( CF7OptionLimiterAdmin.i18n.noFields ); // Reitera que no se encontraron campos compatibles.
            $( document ).trigger( 'cf7OptionLimiterFieldsLoaded', [ requestedFormId ] ); // Notifica que la carga ha finalizado.
            return; // Finaliza sin modificar el selector.
        }
        var fieldOptionsMap = {}; // Inicializa el mapa que asociará cada campo con sus opciones disponibles.
        $.each( fields, function( index, field ) { // Recorre cada elemento devuelto por el servidor.
            if ( ! field || ! field.name ) { // Valida que cada campo contenga un nombre válido.
                return; // Omite entradas inválidas sin interrumpir el bucle.
            }
            fieldOptionsMap[ field.name ] = field.options || []; // Asocia la lista de opciones detectadas con el nombre del campo.
            var option = $( '<option></option>' ) // Crea un elemento option para el selector de campos.
                .attr( 'value', field.name ) // Establece el valor exacto del campo detectado.
                .text( field.name ); // Añade el nombre del campo como texto visible.
            $fieldSelect.append( option ); // Inserta la opción en el selector de campos.
        } );
        $fieldSelect.data( 'fieldOptions', fieldOptionsMap ); // Almacena el mapa en el selector para futuras consultas.
        if ( ! $.isEmptyObject( fieldOptionsMap ) ) { // Comprueba si se cargaron campos válidos.
            $fieldSelect.prop( 'disabled', false ); // Habilita el selector ahora que existen opciones disponibles.
        }
        $( document ).trigger( 'cf7OptionLimiterFieldsLoaded', [ requestedFormId ] ); // Notifica que la carga de campos ha finalizado.
        updateSubmitState(); // Recalcula la disponibilidad del botón tras actualizar la lista de campos.
    }

    function populateOptionsForField( fieldName ) { // Función que carga las opciones disponibles para un campo concreto.
        resetOptionInput( false ); // Limpia cualquier lista previa antes de cargar nuevas opciones.
        var optionsMap = $fieldSelect.data( 'fieldOptions' ) || {}; // Recupera el mapa de opciones almacenado en el selector de campos.
        if ( ! fieldName || ! optionsMap[ fieldName ] || ! optionsMap[ fieldName ].length ) { // Comprueba si no existen opciones para el campo.
            setOptionStatus( CF7OptionLimiterAdmin.i18n.noOptions ); // Informa que el formulario no define opciones para el campo seleccionado.
            updateSubmitState(); // Mantiene el botón deshabilitado porque no hay opción seleccionable asociada al campo elegido.
            return false; // Indica que no se pudieron cargar opciones.
        }
        $.each( optionsMap[ fieldName ], function( index, option ) { // Recorre cada opción disponible para generar las entradas del selector.
            var entry = $( '<option></option>' ) // Crea un elemento option del selector dependiente.
                .attr( 'value', option.value ) // Establece el valor exacto de la opción detectada.
                .text( option.label || option.value ); // Añade la etiqueta legible asociada o el valor en su defecto.
            $optionSelect.append( entry ); // Inserta la opción en el selector.
        } );
        $optionSelect.prop( 'disabled', false ); // Habilita el selector de opciones ahora que existen valores disponibles.
        setOptionStatus( CF7OptionLimiterAdmin.i18n.optionManual ); // Actualiza el mensaje recordando que se debe elegir una de las opciones detectadas.
        updateSubmitState(); // Vuelve a evaluar la disponibilidad del botón porque las opciones ya se encuentran listas.
        return true; // Indica que la población se realizó correctamente.
    }

    function startRuleEdit( data ) { // Función que prepara la edición de una regla utilizando la información disponible.
        pendingRuleData = $.extend( {}, data ); // Clona los datos recibidos para evitar mutaciones inesperadas.
        var targetFormId = data.formId || data.form_id || $formSelect.val(); // Determina el formulario que debe estar activo.
        pendingRuleData.__targetFormId = targetFormId ? String( targetFormId ) : ''; // Almacena el formulario objetivo como texto para comparaciones posteriores.
        if ( targetFormId && $formSelect.val() !== String( targetFormId ) ) { // Comprueba si es necesario recargar los campos porque pertenecen a otro formulario.
            $formSelect.val( targetFormId ); // Establece el formulario correcto antes de iniciar la carga.
            loadFieldsForForm( targetFormId ); // Solicita la recarga de campos para el formulario correspondiente.
            return; // Detiene la ejecución hasta que la carga finalice.
        }
        if ( finalizeRuleEdit() ) { // Intenta aplicar los datos inmediatamente si los campos ya están disponibles.
            pendingRuleData = null; // Limpia los datos temporales si la aplicación fue satisfactoria.
        } else {
            loadFieldsForForm( $formSelect.val() ); // Asegura que los campos se recarguen cuando aún no están listos.
        }
    }

    function finalizeRuleEdit() { // Función que aplica los datos almacenados en pendingRuleData una vez que los campos están disponibles.
        if ( ! pendingRuleData ) { // Comprueba que existan datos pendientes.
            return false; // Finaliza indicando que no se realizó ninguna acción.
        }
        var fieldName = pendingRuleData.fieldName || pendingRuleData.field_name || ''; // Determina el nombre del campo a editar.
        if ( fieldName && ! fieldsReadyFor( fieldName ) ) { // Comprueba que el selector de campos incluya el campo requerido.
            return false; // Espera a que se completen los datos necesarios.
        }
        isEditing = true; // Marca el formulario como en modo edición.
        var ruleId = pendingRuleData.ruleId || pendingRuleData.id || 0; // Determina el identificador de la regla en edición.
        var optionValue = pendingRuleData.optionValue || pendingRuleData.option_value || ''; // Determina la opción específica asociada a la regla.
        var maxCount = pendingRuleData.maxCount || pendingRuleData.max_count || 1; // Determina el máximo permitido configurado.
        var limitPeriod = pendingRuleData.limitPeriod || pendingRuleData.limit_period || 'none'; // Determina el periodo configurado.
        var customMessage = pendingRuleData.customMessage || pendingRuleData.custom_message || ''; // Determina el mensaje personalizado almacenado.
        var hideExhausted = pendingRuleData.hideExhausted || pendingRuleData.hide_exhausted || 0; // Determina si la opción debe ocultarse al agotarse.
        $ruleInput.val( ruleId ); // Almacena el identificador de la regla en el campo oculto correspondiente.
        $originalFieldInput.val( fieldName ); // Guarda el nombre del campo original para el manejador de guardado.
        $originalOptionInput.val( optionValue ); // Guarda la opción original.
        if ( fieldName ) { // Comprueba que exista un campo válido para seleccionar.
            $fieldSelect.val( fieldName ); // Selecciona el campo en el desplegable.
        }
        var hasOptions = populateOptionsForField( fieldName ); // Rellena el selector de opciones con los valores detectados.
        if ( hasOptions && optionValue ) { // Comprueba si se debe seleccionar una opción concreta.
            $optionSelect.val( optionValue ); // Selecciona la opción almacenada previamente.
        } else if ( ! hasOptions ) { // Si no se pudieron cargar opciones para el campo.
            setOptionStatus( CF7OptionLimiterAdmin.i18n.noOptions ); // Mantiene un mensaje claro indicando la ausencia de opciones.
        }
        $maxInput.val( maxCount ); // Rellena el máximo permitido conservando el valor existente.
        $limitSelect.val( limitPeriod ); // Ajusta el periodo configurado.
        $customInput.val( customMessage ); // Rellena el mensaje personalizado si existe.
        if ( $hideCheckbox.length ) { // Comprueba si la casilla de ocultar opciones está presente.
            var hideValue = ( hideExhausted === true || hideExhausted === '1' || hideExhausted === 1 ); // Evalúa la preferencia en distintos formatos.
            $hideCheckbox.prop( 'checked', hideValue ); // Ajusta el estado del checkbox según la configuración almacenada.
        }
        $submitButton.text( updateLabel ); // Cambia la etiqueta del botón para indicar el modo edición.
        updateSubmitState(); // Mantiene el botón activo sólo cuando la edición cuenta con todas las selecciones obligatorias.
        $cancelButton.show(); // Muestra el botón de cancelar edición.
        return true; // Indica que los datos se aplicaron correctamente.
    }

    function fieldsReadyFor( fieldName ) { // Función que verifica si el selector de campos incluye la información necesaria.
        if ( ! $fieldSelect.length ) { // Comprueba que el selector exista.
            return false; // Indica que no hay campos disponibles.
        }
        var optionsMap = $fieldSelect.data( 'fieldOptions' ) || {}; // Recupera el mapa de campos cargados.
        if ( $.isEmptyObject( optionsMap ) ) { // Comprueba si todavía no se ha cargado ningún campo.
            return false; // Indica que los campos no están listos.
        }
        if ( ! fieldName ) { // Si no se especificó un campo concreto.
            return true; // Considera que los campos están listos porque la lista existe.
        }
        return !! optionsMap[ fieldName ]; // Indica si el campo solicitado está presente en el mapa.
    }

    function clearEditMode() { // Función que restablece el formulario tras cancelar la edición.
        isEditing = false; // Indica que se ha salido del modo edición.
        pendingRuleData = null; // Limpia cualquier dato pendiente de aplicar.
        $ruleInput.val( '0' ); // Restablece el identificador de la regla editada.
        $originalFieldInput.val( '' ); // Limpia el nombre original del campo.
        $originalOptionInput.val( '' ); // Limpia la opción original.
        $submitButton.text( saveLabel ); // Restaura la etiqueta del botón principal.
        $cancelButton.hide(); // Oculta el botón de cancelar edición.
        resetFieldInput( CF7OptionLimiterAdmin.i18n.fieldManual, true ); // Restablece el selector de campos conservando las opciones disponibles.
        resetOptionInput( true ); // Restablece el selector de opciones conservando la lista actual.
        $maxInput.val( 1 ); // Restablece el máximo permitido al valor por defecto.
        $limitSelect.val( 'none' ); // Restablece el periodo al valor por defecto.
        $customInput.val( '' ); // Limpia el mensaje personalizado.
        if ( $hideCheckbox.length ) { // Comprueba si existe la casilla de ocultación de opciones.
            $hideCheckbox.prop( 'checked', false ); // Restablece el checkbox a su estado desmarcado.
        }
        updateSubmitState(); // Deshabilita el botón nuevamente al abandonar el modo edición y limpiar las selecciones.
    }

    function hasCompleteSelection() { // Determina si las tres selecciones obligatorias están presentes y son válidas.
        var formIdValue = parseInt( $formSelect.val(), 10 ); // Convierte el identificador del formulario en entero para verificar que sea positivo.
        var formReady = ! isNaN( formIdValue ) && formIdValue > 0; // Confirma que se seleccionó un formulario con identificador mayor que cero.
        var fieldValue = $fieldSelect.val(); // Recupera el valor actualmente seleccionado en el listado de campos.
        var optionValue = $optionSelect.val(); // Recupera el valor seleccionado en el desplegable dependiente de opciones.
        var fieldReady = !! ( fieldValue && String( fieldValue ).trim() ); // Evalúa que exista un campo distinto de la opción vacía.
        var optionReady = !! ( optionValue && String( optionValue ).trim() ); // Evalúa que exista una opción concreta asociada al campo.
        return formReady && fieldReady && optionReady; // Devuelve verdadero únicamente cuando las tres comprobaciones son satisfactorias.
    }

    function updateSubmitState() { // Controla la disponibilidad del botón principal según el estado actual de las selecciones y del proceso de guardado.
        if ( ! $submitButton.length ) { // Comprueba que el botón exista antes de modificarlo.
            return; // Finaliza sin realizar cambios cuando el botón no está presente en el DOM.
        }
        var readyForSubmit = hasCompleteSelection() && ! isAjaxSubmitting; // Determina si se cumplen los requisitos mínimos y no existe una petición en curso.
        $submitButton.prop( 'disabled', ! readyForSubmit ); // Habilita o deshabilita el botón utilizando el atributo estándar.
        $submitButton.attr( 'aria-disabled', readyForSubmit ? 'false' : 'true' ); // Mantiene sincronizado el estado accesible para lectores de pantalla.
    }

    function ensureAccessibleNotice() { // Crea o reutiliza una región aria-live dedicada a los mensajes inmediatos.
        var $existing = $( '#cf7-ol-accessible-notice' ); // Busca si la región ya existe para evitar duplicados.
        if ( $existing.length ) { // Comprueba si se encontró una instancia previa.
            return $existing; // Devuelve la región existente para reutilizarla en sucesivos avisos.
        }
        var $region = $( '<div></div>' ) // Crea un contenedor genérico para alojar los mensajes de accesibilidad.
            .attr( 'id', 'cf7-ol-accessible-notice' ) // Define un identificador estable que permita localizar el elemento rápidamente.
            .attr( 'role', 'alert' ) // Configura el rol ARIA para que los lectores de pantalla anuncien el contenido inmediatamente.
            .attr( 'aria-live', 'assertive' ) // Establece la prioridad assertive para garantizar el anuncio oportuno.
            .attr( 'aria-atomic', 'true' ) // Solicita que el mensaje se anuncie completo aunque sólo cambie parcialmente.
            .addClass( 'screen-reader-text' ); // Aplica la clase estándar de WordPress que oculta visualmente el elemento sin retirarlo de la accesibilidad.
        if ( document.body ) { // Comprueba que el cuerpo del documento esté disponible antes de insertar el elemento.
            $( document.body ).append( $region ); // Añade la región al DOM para que los mensajes puedan emitirse.
        }
        return $region; // Devuelve la nueva región para futuras reutilizaciones.
    }

    function ensureInlineNoticeContainer() { // Garantiza la existencia de un contenedor visible para los mensajes informativos.
        var $existing = $( '#cf7-ol-inline-notice' ); // Localiza si ya existe un contenedor reutilizable.
        if ( $existing.length ) { // Comprueba si se encontró uno previamente.
            return $existing; // Devuelve el contenedor existente para reutilizarlo.
        }
        var $container = $( '<div></div>' ) // Crea un nuevo contenedor genérico.
            .attr( 'id', 'cf7-ol-inline-notice' ) // Asigna un identificador estable para futuras consultas.
            .addClass( 'notice' ) // Aplica la clase base de avisos del administrador de WordPress.
            .hide(); // Lo oculta inicialmente hasta que se utilice.
        var $panelContainer = $submitButton.closest( '.cf7-option-limiter-panel-form' ); // Intenta localizar el panel visual principal para insertar el aviso.
        if ( $panelContainer.length ) { // Comprueba si se encontró el contenedor principal.
            $panelContainer.prepend( $container ); // Inserta el aviso al principio del panel para máxima visibilidad.
        } else if ( $embeddedForm.length ) { // Si no se encuentra el panel, se utiliza el formulario oculto como referencia.
            $embeddedForm.before( $container ); // Inserta el aviso justo antes del formulario oculto.
        } else {
            $( document.body ).append( $container ); // Como último recurso, inserta el aviso en el body para asegurar su disponibilidad.
        }
        return $container; // Devuelve el nuevo contenedor creado.
    }

    function clearInlineNotice() { // Oculta el aviso visible y elimina cualquier contenido previo.
        if ( ! $inlineNotice || ! $inlineNotice.length ) { // Comprueba si existe el contenedor de avisos.
            return; // Finaliza cuando no hay contenedor sobre el que actuar.
        }
        $inlineNotice.removeClass( 'notice-success notice-error notice-warning' ).empty().hide(); // Restablece el contenedor a su estado neutro y lo oculta.
    }

    function showInlineNotice( type, message ) { // Muestra un aviso visible en el panel sin necesidad de recargar la página.
        if ( ! $inlineNotice || ! $inlineNotice.length ) { // Comprueba que exista el contenedor destinado a los avisos.
            return; // Finaliza cuando no hay contenedor disponible.
        }
        clearInlineNotice(); // Limpia cualquier aviso previo para no mezclar mensajes.
        var normalizedType = type || 'info'; // Normaliza el tipo recibido para seleccionar la clase adecuada.
        var className = 'notice'; // Inicializa la clase base reutilizada por los avisos del administrador.
        if ( 'success' === normalizedType ) { // Comprueba si el mensaje representa un éxito.
            className += ' notice-success'; // Añade la clase específica de éxito.
        } else if ( 'error' === normalizedType ) { // Comprueba si se trata de un error.
            className += ' notice-error'; // Añade la clase específica de error.
        } else if ( 'warning' === normalizedType ) { // Comprueba si se trata de una advertencia.
            className += ' notice-warning'; // Añade la clase específica de advertencia.
        } else {
            className += ' notice-info'; // Utiliza el estilo informativo como valor por defecto.
        }
        var $paragraph = $( '<p></p>' ) // Crea un párrafo que contendrá el mensaje.
            .text( message ); // Inserta el texto recibido asegurando su escapado automático.
        $inlineNotice.attr( 'class', className ).append( $paragraph ).show(); // Aplica la clase calculada, añade el mensaje y muestra el contenedor.
    }

    function announceForAssistiveTech( message ) { // Centraliza el anuncio de mensajes para tecnologías asistivas.
        if ( ! message ) { // Comprueba que exista un texto que anunciar.
            return; // Finaliza silenciosamente cuando no se proporciona mensaje.
        }
        if ( window.wp && window.wp.a11y && typeof window.wp.a11y.speak === 'function' ) { // Comprueba si está disponible la utilidad nativa de WordPress.
            window.wp.a11y.speak( message, 'assertive' ); // Utiliza wp.a11y.speak para emitir el mensaje respetando la configuración de accesibilidad del administrador.
            return; // Finaliza tras anunciar mediante la utilidad dedicada.
        }
        if ( $accessibleNotice && $accessibleNotice.length ) { // Como alternativa, utiliza la región aria-live creada por este script.
        $accessibleNotice.text( message ); // Actualiza el contenido de la región para que los lectores de pantalla lo anuncien inmediatamente.
        }
    }

    function updateTableWithRule( rule, removedRuleId ) { // Inserta o actualiza la fila correspondiente en la tabla de reglas del panel.
        if ( ! $rulesTableBody.length ) { // Comprueba que exista la tabla antes de intentar manipularla.
            return; // Finaliza cuando el panel no está presente en la vista actual.
        }
        if ( removedRuleId ) { // Comprueba si se solicitó eliminar una fila antigua.
            var $oldRow = $rulesTableBody.find( 'tr[data-rule-id="' + removedRuleId + '"]' ); // Busca la fila cuyo identificador coincide con la regla eliminada.
            if ( $oldRow.length ) { // Comprueba si la fila existe en la tabla actual.
                $oldRow.remove(); // Elimina la fila obsoleta para evitar duplicados.
            }
        }
        if ( ! rule ) { // Comprueba si no se proporcionó una fila actualizada.
            return; // Finaliza cuando no hay datos que insertar o actualizar.
        }
        var ruleId = rule.id ? String( rule.id ) : ''; // Normaliza el identificador de la regla recién guardada.
        var $existingRow = ruleId ? $rulesTableBody.find( 'tr[data-rule-id="' + ruleId + '"]' ) : $(); // Localiza si la fila ya existía en la tabla.
        var $newRow = createRuleRow( rule ); // Construye la fila completa en formato jQuery lista para insertarse en el DOM.
        removeEmptyPlaceholderRow(); // Asegura que la fila indicativa de tabla vacía se elimine cuando haya datos reales.
        if ( $existingRow.length ) { // Comprueba si se debe reemplazar una fila existente.
            $existingRow.replaceWith( $newRow ); // Sustituye la fila previa por la versión actualizada.
        } else {
            $rulesTableBody.append( $newRow ); // Inserta la nueva fila al final de la tabla cuando no existía previamente.
        }
    }

    function createRuleRow( rule ) { // Construye dinámicamente una fila de la tabla de reglas con los datos proporcionados.
        var $row = $( '<tr></tr>' ) // Crea el elemento de fila principal.
            .attr( 'data-rule-id', rule.id || '' ); // Añade el identificador de la regla para futuras actualizaciones.
        $row.append( $( '<td></td>' ).text( rule.field_name || '' ) ); // Inserta la celda con el nombre del campo.
        $row.append( $( '<td></td>' ).text( rule.option_value || '' ) ); // Inserta la celda con el valor de la opción.
        $row.append( $( '<td></td>' ).text( rule.max_count || '' ) ); // Inserta la celda con el máximo permitido.
        $row.append( $( '<td></td>' ).text( rule.current_count || 0 ) ); // Inserta la celda con el contador actual.
        $row.append( $( '<td></td>' ).text( rule.period_label || rule.limit_period || '' ) ); // Inserta la celda con la etiqueta del periodo configurado.
        $row.append( $( '<td></td>' ).text( rule.custom_message || '' ) ); // Inserta la celda con el mensaje personalizado.
        $row.append( $( '<td></td>' ).text( rule.hide_label || ( rule.hide_exhausted ? ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.yesLabel ? CF7OptionLimiterAdmin.i18n.yesLabel : 'Sí' ) : ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.noLabel ? CF7OptionLimiterAdmin.i18n.noLabel : 'No' ) ) ) ); // Inserta la celda que indica si la opción se ocultará al agotarse.
        var $actionsCell = $( '<td></td>' ); // Crea la celda que contendrá las acciones disponibles.
        var editLabel = ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.editLabel ) ? CF7OptionLimiterAdmin.i18n.editLabel : 'Editar'; // Determina la etiqueta del botón de edición.
        var deleteLabel = ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.deleteLabel ) ? CF7OptionLimiterAdmin.i18n.deleteLabel : 'Eliminar'; // Determina la etiqueta del botón de eliminación.
        var deleteConfirm = ( CF7OptionLimiterAdmin.i18n && CF7OptionLimiterAdmin.i18n.deleteConfirm ) ? CF7OptionLimiterAdmin.i18n.deleteConfirm : ''; // Recupera el mensaje de confirmación configurado.
        var redirect = $redirectInput.val() || ''; // Recupera la URL de retorno que utilizará el formulario de eliminación.
        var formTitle = $formSelect.find( 'option:selected' ).text() || ''; // Recupera el título del formulario actual para incluirlo como referencia.
        var $editButton = $( '<button></button>' ) // Crea el botón de edición compatible con el resto de la interfaz.
            .attr( 'type', 'button' ) // Declara el tipo button para evitar envíos accidentales.
            .addClass( 'button button-link cf7-option-limiter-edit' ) // Reutiliza las clases existentes en el panel.
            .attr( 'data-rule-id', rule.id || '' ) // Inyecta el identificador de la regla.
            .attr( 'data-form-id', rule.form_id || $formSelect.val() || '' ) // Inyecta el identificador del formulario asociado.
            .attr( 'data-form-title', formTitle ) // Inyecta el título del formulario para coherencia con las filas generadas en PHP.
            .attr( 'data-field-name', rule.field_name || '' ) // Inyecta el nombre del campo.
            .attr( 'data-option-value', rule.option_value || '' ) // Inyecta el valor de la opción.
            .attr( 'data-hide-exhausted', rule.hide_exhausted || 0 ) // Inyecta el estado de ocultación.
            .attr( 'data-max-count', rule.max_count || 1 ) // Inyecta el máximo permitido.
            .attr( 'data-limit-period', rule.limit_period || 'none' ) // Inyecta el periodo configurado.
            .attr( 'data-custom-message', rule.custom_message || '' ) // Inyecta el mensaje personalizado.
            .text( editLabel ); // Asigna la etiqueta traducida.
        var $deleteButton = $( '<button></button>' ) // Crea el botón de eliminación coherente con el marcado existente.
            .attr( 'type', 'button' ) // Declara el tipo button para evitar envíos automáticos.
            .addClass( 'button button-secondary cf7-option-limiter-delete' ) // Reutiliza las clases aplicadas en el panel generado por PHP.
            .attr( 'data-rule-id', rule.id || '' ) // Inyecta el identificador de la regla para el formulario oculto.
            .attr( 'data-redirect', redirect ) // Inyecta la URL de retorno utilizada tras eliminar.
            .attr( 'data-confirm', deleteConfirm ) // Inyecta el mensaje de confirmación configurado.
            .text( deleteLabel ); // Asigna la etiqueta traducida.
        $actionsCell.append( $editButton ).append( ' ' ).append( $deleteButton ); // Inserta ambos botones en la celda de acciones.
        $row.append( $actionsCell ); // Añade la celda de acciones a la fila completa.
        return $row; // Devuelve la fila ya construida para su inserción en la tabla.
    }

    function removeEmptyPlaceholderRow() { // Elimina la fila de marcador que indica que no existen reglas configuradas.
        if ( ! $rulesTableBody.length ) { // Comprueba que exista el cuerpo de la tabla antes de manipularlo.
            return; // Finaliza cuando no se encontró la tabla.
        }
        var $placeholder = $rulesTableBody.find( 'tr.cf7-ol-empty-row' ); // Localiza la fila de marcador si existe.
        if ( $placeholder.length ) { // Comprueba si la fila está presente.
            $placeholder.remove(); // Elimina la fila para mostrar únicamente datos reales.
        }
    }


    function focusElementSafely( $element ) { // Gestiona el enfoque de los campos cuando se detectan errores de validación.
        if ( ! $element || ! $element.length ) { // Comprueba que exista el elemento objetivo.
            return; // Finaliza cuando no hay un elemento válido que enfocar.
        }
        if ( typeof $element.focus === 'function' ) { // Comprueba si el elemento expone el método focus nativo.
            $element.focus(); // Sitúa el foco directamente utilizando la API estándar.
            return; // Finaliza tras enfocar correctamente el elemento.
        }
        $element.trigger( 'focus' ); // Utiliza el mecanismo de jQuery como alternativa para compatibilizar con versiones anteriores de navegadores.
    }

    function validateSelectionsBeforeSubmit() { // Revisa que el formulario cuente con todos los datos imprescindibles antes de enviar la petición.
        if ( hasCompleteSelection() ) { // Comprueba si todas las selecciones están completas desde el inicio.
            return true; // Permite continuar con el envío cuando no faltan datos.
        }
        var formIdValue = parseInt( $formSelect.val(), 10 ); // Obtiene el identificador numérico del formulario seleccionado.
        if ( isNaN( formIdValue ) || formIdValue <= 0 ) { // Comprueba si el formulario no se ha seleccionado o es inválido.
            var formMessage = CF7OptionLimiterAdmin.i18n.selectForm || 'Selecciona un formulario válido antes de continuar.'; // Determina el mensaje más apropiado reutilizando las traducciones disponibles.
            setFieldStatus( formMessage ); // Refuerza el mensaje en el área visual destinada a los campos.
            focusElementSafely( $formSelect ); // Mueve el foco al selector de formulario para facilitar la corrección inmediata.
            return false; // Impide el envío hasta que se elija un formulario válido.
        }
        if ( ! $fieldSelect.val() ) { // Comprueba si falta la selección del campo.
            var fieldMessage = CF7OptionLimiterAdmin.i18n.selectField || 'Selecciona un campo antes de guardar la regla.'; // Prepara el mensaje a mostrar cuando no hay campo elegido.
            setFieldStatus( fieldMessage ); // Actualiza la pista contextual recordando la acción pendiente.
            focusElementSafely( $fieldSelect ); // Sitúa el foco en el selector correspondiente para mejorar la usabilidad.
            return false; // Evita el envío mientras no se elija un campo válido.
        }
        if ( ! $optionSelect.val() ) { // Comprueba si falta seleccionar una opción concreta.
            var optionMessage = CF7OptionLimiterAdmin.i18n.optionManual || 'Selecciona una opción antes de guardar la regla.'; // Determina el mensaje adecuado cuando falta la opción.
            setOptionStatus( optionMessage ); // Refuerza el mensaje en la zona visual dedicada a las opciones.
            focusElementSafely( $optionSelect ); // Lleva el foco al selector de opciones para reducir los pasos necesarios para corregirlo.
            return false; // Impide el envío hasta que se seleccione un valor válido.
        }
        return true; // Devuelve true cuando todas las comprobaciones se han superado correctamente.
    }
} );
