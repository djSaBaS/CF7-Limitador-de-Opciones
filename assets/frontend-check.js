// Encapsula la lógica de comprobación en vivo dentro de una IIFE para evitar contaminaciones del ámbito global.
(function( $ ) {
    'use strict'; // Fuerza el modo estricto para prevenir errores silenciosos.

    var settings = window.CF7OptionLimiterPublic || {}; // Recupera la configuración expuesta desde PHP o un objeto vacío.
    settings.messages = settings.messages || {}; // Garantiza que exista un contenedor de mensajes aunque no se haya definido en PHP.
    settings.messages.unavailable = settings.messages.unavailable || 'La opción "%1$s" ya no está disponible. %2$s'; // Define un mensaje por defecto para opciones agotadas con texto adicional.
    settings.messages.unavailableRaw = settings.messages.unavailableRaw || 'La opción "%1$s" ya no está disponible.'; // Define un mensaje por defecto sin texto adicional.
    settings.messages.requestError = settings.messages.requestError || 'No se pudo comprobar la disponibilidad. Inténtalo de nuevo.'; // Define un mensaje genérico de error de comunicación.
    if ( ! settings.ajaxUrl || ! settings.nonce ) { // Comprueba que existan los parámetros mínimos para operar.
        return; // Finaliza si no se recibió la configuración necesaria.
    }

    var availabilityCache = {}; // Inicializa un registro en memoria que almacena el estado de disponibilidad por campo y valor.

    function normalizeFieldName( name ) { // Normaliza el nombre del campo eliminando la notación de arreglos.
        if ( ! name ) { // Comprueba si el nombre es una cadena vacía o indefinida.
            return ''; // Devuelve cadena vacía cuando no hay nombre.
        }
        return String( name ).replace( /\[\]$/, '' ); // Elimina la notación [] del final para coincidir con la base de datos.
    }

    function getFormId( $form ) { // Obtiene el identificador del formulario desde el campo oculto estándar de Contact Form 7.
        var $identifier = $form.find( 'input[name="_wpcf7"]' ); // Busca el input oculto que almacena el ID del formulario.
        if ( ! $identifier.length ) { // Comprueba si no existe dicho campo.
            return 0; // Devuelve cero para indicar que no se pudo identificar el formulario.
        }
        return parseInt( $identifier.val(), 10 ) || 0; // Convierte el valor a entero y controla valores no numéricos.
    }

    function findChoiceElement( $field, value ) { // Localiza el elemento HTML asociado a un valor específico del campo.
        if ( $field.is( 'select' ) ) { // Comprueba si el campo es un select.
            return $field.find( 'option' ).filter( function() { // Recorre las opciones del select.
                return String( $( this ).val() ) === value; // Conserva únicamente la opción cuyo valor coincide.
            } );
        }
        var name = $field.attr( 'name' ); // Recupera el nombre original del campo.
        if ( ! name ) { // Comprueba si el campo carece de nombre.
            return $( [] ); // Devuelve un objeto jQuery vacío.
        }
        var selector = 'input[name="' + name + '"][value="' + value.replace( /"/g, '\\"' ) + '"]'; // Construye el selector para inputs de tipo radio/checkbox.
        return $field.closest( 'form' ).find( selector ); // Busca dentro del formulario el input exacto con ese valor.
    }

    function resolveLabel( $choice, value ) { // Determina una etiqueta legible asociada a la opción evaluada.
        if ( ! $choice.length ) { // Comprueba si no se encontró el elemento asociado.
            return value; // Devuelve el propio valor como alternativa mínima.
        }
        if ( $choice.is( 'option' ) ) { // Comprueba si el elemento es una opción de select.
            return $.trim( $choice.text() ); // Devuelve el texto visible de la opción.
        }
        var type = $choice.attr( 'type' ); // Recupera el tipo de input cuando se trata de radio/checkbox.
        if ( type === 'radio' || type === 'checkbox' ) { // Limita la búsqueda de etiquetas a estos tipos de campo.
            var $label = $choice.closest( 'label' ); // Busca un label que envuelva directamente al input.
            if ( $label.length ) { // Comprueba si se encontró un label contenedor.
                return $.trim( $label.text() ); // Devuelve el texto del label asociado.
            }
            var id = $choice.attr( 'id' ); // Recupera el ID del input para buscar un label externo.
            if ( id ) { // Comprueba si existe un identificador disponible.
                var $forLabel = $choice.closest( 'form' ).find( 'label[for="' + id + '"]' ); // Localiza el label que referencia al ID.
                if ( $forLabel.length ) { // Comprueba si se encontró el label externo.
                    return $.trim( $forLabel.text() ); // Devuelve el texto del label encontrado.
                }
            }
        }
        return value; // Fallback para cualquier otro tipo de elemento.
    }

    function ensureMessageContainer( $field ) { // Garantiza la existencia del contenedor de mensajes dinámicos para el campo.
        var fieldName = normalizeFieldName( $field.attr( 'name' ) ); // Normaliza el nombre del campo actual.
        var $form = $field.closest( 'form' ); // Obtiene el formulario al que pertenece el campo.
        var selector = '[data-field="' + fieldName + '"][data-dynamic="1"]'; // Construye el selector del contenedor dinámico empleando sólo data attributes para localizarlo aunque varíen las clases.
        var $container = $form.find( selector ).filter( '.cf7-option-limiter-message, .cf7-option-limiter-message-container' ).first(); // Busca el contenedor reutilizable ya sea que conserve la clase visible o únicamente la clase de marcador inactivo.
        if ( $container.length ) { // Comprueba si se encontró un contenedor previamente creado.
            $container.addClass( 'cf7-option-limiter-message-container' ); // Garantiza que el contenedor reaprovechado conserve la clase identificadora incluso si perdió la clase estilizada.
            return $container; // Devuelve el contenedor existente.
        }
        $container = $( '<div></div>' ) // Crea un nuevo elemento div para alojar el mensaje.
            .addClass( 'cf7-option-limiter-message-container' ) // Añade una clase persistente que identifica contenedores dinámicos sin aplicar estilos de alerta.
            .attr( 'data-field', fieldName ) // Asocia el nombre del campo mediante data attribute.
            .attr( 'data-dynamic', '1' ) // Marca el contenedor como dinámico para distinguirlo de los generados en PHP.
            .attr( 'hidden', 'hidden' ); // Inicialmente oculta el contenedor para evitar parpadeos visuales cuando está vacío.
        var $wrapper = $field.last().closest( 'p, span, label, div' ); // Intenta localizar un contenedor cercano adecuado.
        if ( $wrapper.length ) { // Comprueba si se encontró un contenedor envolvente.
            $container.insertAfter( $wrapper ); // Inserta el mensaje después del contenedor principal.
        } else {
            $container.insertAfter( $field ); // Inserta el mensaje directamente después del campo cuando no hay contenedor.
        }
        return $container; // Devuelve el contenedor recién creado.
    }

    function renderMessages( $field, messages ) { // Muestra u oculta los mensajes dinámicos asociados al campo.
        var $container = ensureMessageContainer( $field ); // Asegura que existe un contenedor disponible.
        if ( messages.length === 0 ) { // Comprueba si no hay mensajes que mostrar.
            $container.text( '' ) // Limpia el texto existente.
                .addClass( 'cf7-option-limiter-message-container' ) // Asegura que el contenedor mantenga la clase identificadora para reutilizarlo posteriormente.
                .removeClass( 'cf7-option-limiter-message is-depleted is-visible' ) // Retira la clase estilizada y los modificadores visuales cuando el mensaje queda vacío.
                .attr( 'hidden', 'hidden' ); // Mantiene oculto el contenedor vacío para evitar indicadores de error sin contenido.
            return; // Finaliza la ejecución al no haber mensajes.
        }
        var text = messages.join( ' ' ); // Concatena los mensajes en una sola cadena.
        $container.text( text ) // Inserta el texto en el contenedor.
            .addClass( 'cf7-option-limiter-message-container' ) // Mantiene la clase identificadora incluso cuando se muestran mensajes para facilitar futuras búsquedas.
            .removeAttr( 'hidden' ) // Garantiza que el contenedor se muestre cuando existe contenido que comunicar.
            .addClass( 'cf7-option-limiter-message' ) // Añade la clase estilizada que aplica el fondo de alerta únicamente cuando hay texto.
            .addClass( 'is-depleted' ); // Aplica el estilo de advertencia.
        setTimeout( function() { // Utiliza un pequeño retraso para activar la animación de aparición.
            $container.addClass( 'is-visible' ); // Añade la clase que hace visible el mensaje mediante CSS.
        }, 20 );
    }

    function disableChoice( $field, value ) { // Oculta y deshabilita una opción agotada en el formulario.
        var $choice = findChoiceElement( $field, value ); // Localiza el elemento asociado al valor.
        if ( ! $choice.length ) { // Comprueba si no se encontró el elemento.
            return; // Finaliza si no hay nada que ocultar.
        }
        if ( $choice.is( 'option' ) ) { // Comprueba si se trata de una opción dentro de un select.
            $choice.prop( 'disabled', true ) // Deshabilita la opción para impedir su selección.
                .attr( 'data-cf7-option-limiter-hidden', '1' ) // Marca la opción para referencias futuras.
                .attr( 'hidden', 'hidden' ) // Solicita al navegador que oculte la opción.
                .removeAttr( 'selected' ); // Elimina la selección si estaba activa.
            return; // Finaliza la función tras gestionar el select.
        }
        $choice.prop( 'disabled', true ) // Deshabilita el input de tipo radio/checkbox.
            .attr( 'data-cf7-option-limiter-hidden', '1' ); // Marca el input como oculto por el limitador.
        var $wrapper = $choice.closest( 'label, span, div, p' ); // Intenta localizar un contenedor visible alrededor del input.
        if ( $wrapper.length ) { // Comprueba si se encontró un contenedor.
            $wrapper.addClass( 'cf7-option-limiter-hidden' ); // Aplica la clase que oculta el contenedor completo.
        } else {
            $choice.addClass( 'cf7-option-limiter-hidden' ); // Como alternativa oculta directamente el input.
        }
    }

    function markChoiceDepleted( $field, value ) { // Marca una opción como agotada manteniéndola visible en el formulario.
        var $choice = findChoiceElement( $field, value ); // Localiza el elemento asociado al valor.
        if ( ! $choice.length ) { // Comprueba si no se encontró el elemento.
            return; // Finaliza sin realizar acciones adicionales.
        }
        if ( $choice.is( 'option' ) ) { // Comprueba si la opción pertenece a un select.
            $choice.prop( 'disabled', true ) // Deshabilita la opción para impedir nuevas selecciones.
                .removeAttr( 'hidden' ) // Asegura que la opción permanezca visible en el desplegable.
                .attr( 'data-cf7-option-limiter-depleted', '1' ) // Marca la opción como agotada para referencias futuras.
                .removeAttr( 'selected' ); // Elimina la selección en caso de que estuviera activa.
            return; // Finaliza tras actualizar la opción del select.
        }
        $choice.prop( 'disabled', true ) // Deshabilita el input de tipo radio/checkbox.
            .attr( 'data-cf7-option-limiter-depleted', '1' ) // Marca el input como agotado.
            .prop( 'checked', false ); // Asegura que el input no permanezca seleccionado.
        var $wrapper = $choice.closest( 'label, span, div, p' ); // Intenta localizar un contenedor visible para añadir una marca.
        if ( $wrapper.length ) { // Comprueba si existe contenedor envolvente.
            $wrapper.removeClass( 'cf7-option-limiter-hidden' ) // Asegura que el contenedor no permanezca oculto.
                .addClass( 'cf7-option-limiter-depleted' ); // Añade una clase visual que indique el estado de agotamiento.
        } else {
            $choice.removeClass( 'cf7-option-limiter-hidden' ) // Asegura que el input siga visible.
                .addClass( 'cf7-option-limiter-depleted' ); // Marca el input directamente como agotado.
        }
    }

    function enableChoice( $field, value ) { // Restaura la visibilidad y disponibilidad de una opción.
        var $choice = findChoiceElement( $field, value ); // Localiza el elemento asociado al valor.
        if ( ! $choice.length ) { // Comprueba si no se encontró el elemento.
            return; // Finaliza si no hay nada que restaurar.
        }
        if ( $choice.is( 'option' ) ) { // Comprueba si es una opción de select.
            $choice.prop( 'disabled', false ) // Habilita de nuevo la opción.
                .removeAttr( 'hidden' ) // Elimina el atributo hidden para que vuelva a mostrarse.
                .removeAttr( 'data-cf7-option-limiter-hidden' ) // Limpia el marcador interno de ocultación.
                .removeAttr( 'data-cf7-option-limiter-depleted' ); // Elimina el indicador de agotamiento visible.
            return; // Finaliza tras restaurar la opción del select.
        }
        $choice.prop( 'disabled', false ) // Habilita el input nuevamente.
            .removeAttr( 'data-cf7-option-limiter-hidden' ) // Elimina el marcador interno.
            .removeAttr( 'data-cf7-option-limiter-depleted' ) // Elimina el indicador de agotamiento.
            .removeClass( 'cf7-option-limiter-hidden cf7-option-limiter-depleted' ); // Quita las clases de ocultación o agotamiento.
        var $wrapper = $choice.closest( 'label, span, div, p' ); // Busca un contenedor envolvente.
        if ( $wrapper.length ) { // Comprueba si existe dicho contenedor.
            $wrapper.removeClass( 'cf7-option-limiter-hidden cf7-option-limiter-depleted' ); // Elimina las clases de ocultación o agotamiento en el contenedor.
        }
    }

    function formatUnavailableMessage( label, customMessage ) { // Construye el texto final mostrado al usuario cuando se agota una opción.
        if ( customMessage ) { // Comprueba si existe un mensaje personalizado definido.
            return settings.messages.unavailable
                .replace( '%1$s', label )
                .replace( '%2$s', customMessage ); // Inserta la etiqueta y el mensaje personalizado.
        }
        return settings.messages.unavailableRaw.replace( '%1$s', label ); // Utiliza el mensaje genérico cuando no hay texto personalizado.
    }

    function handleResults( $field, values, results ) { // Procesa la respuesta del servidor aplicando cambios visuales y mensajes.
        var messages = []; // Inicializa el arreglo de mensajes a mostrar.
        var fieldName = normalizeFieldName( $field.attr( 'name' ) ); // Normaliza el nombre del campo para identificar la caché.
        var fieldCache = {}; // Prepara un objeto temporal donde se almacenará el estado de cada valor procesado.
        values.forEach( function( value ) { // Recorre cada valor comprobado.
            var key = String( value ); // Normaliza el valor como cadena para indexar la respuesta.
            var detail = results[ key ]; // Recupera la información del servidor para el valor actual.
            if ( ! detail ) { // Comprueba si no existe información asociada.
                fieldCache[ key ] = { available: true }; // Registra la opción como disponible cuando el servidor no devolvió datos.
                enableChoice( $field, key ); // Restablece la opción ante la falta de datos.
                return; // Continúa con el siguiente valor.
            }
            if ( detail.available ) { // Comprueba si la opción sigue disponible.
                fieldCache[ key ] = { available: true }; // Almacena que la opción continúa disponible para futuras validaciones.
                enableChoice( $field, key ); // Asegura que la opción permanezca visible y habilitada.
                return; // No añade mensajes cuando la opción está disponible.
            }
            if ( detail.hide ) { // Comprueba si la opción debería ocultarse al agotarse.
                disableChoice( $field, key ); // Oculta la opción agotada para cumplir la preferencia configurada.
            } else {
                markChoiceDepleted( $field, key ); // Mantiene visible la opción pero la marca como agotada.
            }
            var $choice = findChoiceElement( $field, key ); // Localiza de nuevo el elemento para extraer su etiqueta.
            var label = resolveLabel( $choice, key ); // Obtiene una etiqueta legible para el mensaje.
            var message = formatUnavailableMessage( label, detail.message ); // Construye el texto final del mensaje de agotamiento.
            messages.push( message ); // Añade el mensaje a la lista a mostrar.
            fieldCache[ key ] = { // Registra en caché el estado agotado incluyendo detalles adicionales.
                available: false, // Indica que la opción ya no se puede seleccionar.
                hide: detail.hide === true, // Conserva si la opción debe ocultarse para reproducir el comportamiento en bloqueos.
                message: detail.message || '' // Guarda el mensaje personalizado para reutilizarlo antes de un envío.
            };
        } );
        if ( fieldName ) { // Comprueba que el campo tuviera un nombre válido antes de registrar la caché.
            availabilityCache[ fieldName ] = fieldCache; // Persiste los resultados para reutilizarlos durante el envío del formulario.
        }
        renderMessages( $field, messages ); // Actualiza la interfaz con los mensajes resultantes.
    }

    function requestAvailability( $field, values ) { // Realiza la petición AJAX para comprobar la disponibilidad de las opciones seleccionadas.
        var $form = $field.closest( 'form' ); // Obtiene el formulario al que pertenece el campo.
        var formId = getFormId( $form ); // Recupera el identificador numérico del formulario.
        var fieldName = normalizeFieldName( $field.attr( 'name' ) ); // Normaliza el nombre del campo antes de enviarlo al servidor.
        if ( formId === 0 || fieldName === '' ) { // Comprueba que existan los datos necesarios.
            return; // Finaliza si no se puede identificar el formulario o el campo.
        }
        $.ajax( { // Inicia la petición AJAX hacia WordPress.
            url: settings.ajaxUrl, // Define la URL del endpoint proporcionado por WordPress.
            method: 'POST', // Utiliza el método POST para enviar los datos.
            dataType: 'json', // Solicita que la respuesta se interprete como JSON.
            data: { // Construye el cuerpo de la petición.
                action: 'cf7_option_limiter_check', // Define la acción que procesará la solicitud en el servidor.
                nonce: settings.nonce, // Incluye el nonce de seguridad requerido.
                form_id: formId, // Indica el formulario que se está comprobando.
                field_name: fieldName, // Indica el campo concreto dentro del formulario.
                values: values, // Transmite la lista de valores que se desean verificar.
                post_id: settings.postId || 0 // Adjunta el identificador del post para priorizar excepciones específicas.
            }
        } ).done( function( response ) { // Maneja la respuesta exitosa del servidor.
            if ( ! response || ! response.success || ! response.data || ! response.data.results ) { // Comprueba que la respuesta tenga la estructura esperada.
                renderMessages( $field, [ settings.messages.requestError ] ); // Muestra un mensaje genérico cuando la respuesta es inesperada.
                return; // Finaliza tras informar del error.
            }
            handleResults( $field, values, response.data.results ); // Aplica los resultados devueltos por el servidor.
        } ).fail( function() { // Gestiona los errores de transporte o respuesta inválida.
            renderMessages( $field, [ settings.messages.requestError ] ); // Muestra el mensaje de error genérico cuando la petición falla.
        } );
    }

    function gatherValues( $field ) { // Obtiene la lista de valores actualmente seleccionados en el campo.
        if ( $field.is( 'select' ) ) { // Comprueba si el campo es un select.
            var value = $field.val(); // Recupera el valor seleccionado (puede ser cadena o arreglo).
            if ( value === null || value === undefined ) { // Comprueba si no hay selección alguna.
                return []; // Devuelve un arreglo vacío cuando no hay selección.
            }
            return Array.isArray( value ) ? value : [ value ]; // Normaliza el resultado en un arreglo.
        }
        if ( $field.is( ':checkbox' ) || $field.is( ':radio' ) ) { // Comprueba si el campo es un input de selección múltiple.
            var name = $field.attr( 'name' ); // Recupera el nombre del campo para localizar todos los inputs relacionados.
            if ( ! name ) { // Comprueba si no se obtuvo un nombre válido.
                return []; // Devuelve un arreglo vacío al no poder identificar los inputs asociados.
            }
            return $field.closest( 'form' ).find( 'input[name="' + name + '"]:checked' ).map( function() { // Recorre los inputs marcados.
                return $( this ).val(); // Devuelve el valor de cada input marcado.
            } ).get(); // Convierte el resultado en un arreglo plano.
        }
        return []; // Devuelve un arreglo vacío para tipos de campo no contemplados.
    }

    function onFieldChange( event ) { // Gestiona el evento change de los campos limitados.
        var $field = $( event.target ); // Convierte el elemento objetivo del evento en un objeto jQuery.
        var values = gatherValues( $field ); // Obtiene los valores actualmente seleccionados.
        if ( values.length === 0 ) { // Comprueba si no hay valores seleccionados.
            renderMessages( $field, [] ); // Limpia cualquier mensaje previo asociado al campo.
            return; // Finaliza si no hay valores que comprobar.
        }
        requestAvailability( $field, values ); // Solicita al servidor la disponibilidad de las opciones seleccionadas.
    }

    function collectLimitableFields( $form ) { // Reúne una sola referencia por cada campo limitable dentro del formulario recibido.
        var uniqueFields = []; // Inicializa la colección que agrupará los campos encontrados.
        var seenNames = {}; // Registra los nombres normalizados para evitar duplicar controles de checkbox o radio.
        $form.find( 'select, input[type="radio"], input[type="checkbox"]' ).each( function() { // Recorre todos los controles compatibles.
            var $candidate = $( this ); // Envuelve el elemento actual en un objeto jQuery para reutilizar utilidades.
            var originalName = $candidate.attr( 'name' ); // Recupera el nombre original del campo.
            var normalized = normalizeFieldName( originalName ); // Normaliza el nombre para detectar duplicados.
            if ( ! normalized || seenNames[ normalized ] ) { // Comprueba si el nombre es inválido o ya fue procesado.
                return; // Omite el elemento cuando no aporta nueva información.
            }
            seenNames[ normalized ] = true; // Marca el campo como registrado para evitar procesar duplicados.
            uniqueFields.push( $candidate ); // Añade el elemento a la colección para posteriores comprobaciones.
        } );
        return uniqueFields; // Devuelve el listado final de campos únicos.
    }

    function runInitialChecks( $form ) { // Ejecuta peticiones iniciales para comprobar valores preseleccionados antes de que el usuario interactúe.
        collectLimitableFields( $form ).forEach( function( $field ) { // Recorre cada campo único del formulario.
            var values = gatherValues( $field ); // Obtiene los valores actualmente seleccionados en el campo.
            if ( values.length === 0 ) { // Comprueba si el campo no tiene valores marcados.
                return; // Omite las peticiones cuando no hay nada que verificar.
            }
            requestAvailability( $field, values ); // Lanza una petición para sincronizar el estado inicial del campo con el servidor.
        } );
    }

    function onFormSubmit( event ) { // Valida la disponibilidad usando la caché antes de permitir el envío del formulario.
        var $form = $( event.target ); // Convierte el formulario en objeto jQuery para reutilizar utilidades de búsqueda.
        var shouldBlock = false; // Inicializa el indicador que determinará si se debe bloquear el envío.
        collectLimitableFields( $form ).forEach( function( $field ) { // Recorre cada campo limitable del formulario.
            var values = gatherValues( $field ); // Obtiene los valores seleccionados en el campo actual.
            if ( values.length === 0 ) { // Comprueba si no hay selecciones que validar.
                return; // Omite el campo cuando no hay datos relevantes.
            }
            var fieldName = normalizeFieldName( $field.attr( 'name' ) ); // Normaliza el nombre para localizar la caché.
            if ( ! fieldName || ! availabilityCache[ fieldName ] ) { // Comprueba si no existe caché para el campo procesado.
                return; // Permite continuar cuando no hay información almacenada (se validará en servidor como respaldo).
            }
            var cachedResults = availabilityCache[ fieldName ]; // Recupera el mapa de valores procesados previamente.
            var messages = []; // Inicializa el listado de mensajes que se mostrarán en el campo si se bloquea el envío.
            var fieldBlocked = false; // Registra si al menos una opción del campo está agotada.
            values.forEach( function( value ) { // Recorre cada valor seleccionado en el campo.
                var key = String( value ); // Normaliza el valor seleccionado a cadena para consultar la caché.
                var cachedDetail = cachedResults[ key ]; // Recupera la información almacenada para el valor actual.
                if ( ! cachedDetail || cachedDetail.available ) { // Comprueba si no se detectó agotamiento para el valor.
                    return; // Continúa con el siguiente valor disponible.
                }
                fieldBlocked = true; // Marca el campo para bloqueo porque la opción ya no está disponible.
                if ( cachedDetail.hide ) { // Comprueba si la opción debe ocultarse tras detectar el agotamiento.
                    disableChoice( $field, key ); // Oculta la opción para evitar nuevas selecciones.
                } else {
                    markChoiceDepleted( $field, key ); // Mantiene visible la opción pero deshabilitada para informar al usuario.
                }
                var $choice = findChoiceElement( $field, key ); // Localiza el elemento original para componer el mensaje legible.
                var label = resolveLabel( $choice, key ); // Obtiene una etiqueta comprensible asociada a la opción agotada.
                messages.push( formatUnavailableMessage( label, cachedDetail.message ) ); // Almacena el mensaje final para mostrarlo.
            } );
            if ( fieldBlocked ) { // Comprueba si el campo quedó bloqueado tras analizar sus valores.
                shouldBlock = true; // Actualiza el indicador general para impedir el envío global del formulario.
                renderMessages( $field, messages ); // Muestra los mensajes acumulados para advertir al usuario antes del envío.
            }
        } );
        if ( shouldBlock ) { // Comprueba si se detectó alguna opción agotada.
            event.preventDefault(); // Cancela el envío del formulario para evitar que Contact Form 7 procese la petición.
            event.stopImmediatePropagation(); // Evita que se ejecuten otros controladores registrados en el mismo elemento.
            if ( typeof event.stopPropagation === 'function' ) { // Comprueba que exista el método de propagación en el evento nativo.
                event.stopPropagation(); // Detiene la propagación para bloquear también los listeners en fase de burbujeo.
            }
        }
    }

    function bindSubmitListener( $form ) { // Registra un listener nativo en fase de captura que intercepta el envío antes de CF7.
        var formElement = $form.get( 0 ); // Obtiene el elemento DOM bruto asociado al formulario.
        if ( ! formElement ) { // Comprueba si no se encontró el elemento base.
            return; // Finaliza porque no es posible adjuntar listeners.
        }
        var previousListener = $form.data( 'cf7OptionLimiterSubmitListener' ); // Recupera un listener previo almacenado en los datos del formulario.
        if ( previousListener ) { // Comprueba si ya existe un listener registrado.
            formElement.removeEventListener( 'submit', previousListener, true ); // Elimina el listener anterior para evitar duplicidades.
        }
        var handler = function( event ) { // Declara el manejador que se ejecutará en fase de captura.
            onFormSubmit( event ); // Reutiliza la función de validación principal sobre el evento nativo recibido.
        };
        formElement.addEventListener( 'submit', handler, true ); // Adjunta el listener en fase de captura para ejecutarse antes que Contact Form 7.
        $form.data( 'cf7OptionLimiterSubmitListener', handler ); // Almacena el listener para poder retirarlo en futuras inicializaciones.
    }

    function attachHandlers( context ) { // Adjunta los controladores de eventos a los formularios dentro del contexto dado.
        var $forms = $( context ).find( 'form.wpcf7-form' ); // Localiza los formularios de Contact Form 7 dentro del contexto.
        $forms.each( function() { // Recorre cada formulario encontrado.
            var $form = $( this ); // Convierte el formulario actual en objeto jQuery.
            $form.off( 'change.cf7OptionLimiter' ); // Elimina manejadores previos para evitar duplicidades.
            $form.on( 'change.cf7OptionLimiter', 'select, input[type="radio"], input[type="checkbox"]', onFieldChange ); // Registra el evento change para los campos relevantes.
            $form.off( 'submit.cf7OptionLimiter' ); // Mantiene la limpieza de manejadores jQuery heredados de versiones anteriores.
            bindSubmitListener( $form ); // Adjunta el listener nativo en captura garantizando que sólo exista una instancia activa.
            runInitialChecks( $form ); // Ejecuta una comprobación inicial que sincroniza valores preseleccionados.
        } );
    }

    $( document ).ready( function() { // Espera a que el DOM esté completamente cargado.
        attachHandlers( document ); // Adjunta los manejadores iniciales a todos los formularios disponibles.
    } );

    document.addEventListener( 'wpcf7mailsent', function( event ) { // Escucha el evento que Contact Form 7 dispara tras un envío exitoso.
        if ( ! event || ! event.detail || ! event.detail.unitTag ) { // Comprueba si el evento no contiene la información necesaria.
            return; // Finaliza si no se puede localizar el formulario actualizado.
        }
        var formWrapper = document.getElementById( event.detail.unitTag ); // Obtiene el contenedor del formulario mediante su unitTag.
        if ( formWrapper ) { // Comprueba si el contenedor existe en el DOM.
            attachHandlers( formWrapper ); // Reaplica los manejadores dentro del contenedor actualizado.
        }
    } );

})( jQuery );
