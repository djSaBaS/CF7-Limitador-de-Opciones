/* global CF7OptionLimiterPlugins */
// Encapsula la lógica en una función autoejecutable para evitar contaminar el ámbito global.
(function( window, document ) { // Abre la función inmediata recibiendo window y document como dependencias.
    'use strict'; // Activa el modo estricto para evitar errores silenciosos.

    // Espera a que el DOM esté listo antes de manipular los enlaces.
    document.addEventListener( 'DOMContentLoaded', function() { // Registra un listener para la carga completa del documento.
        if ( ! window.CF7OptionLimiterPlugins ) { // Comprueba que la configuración necesaria esté disponible.
            return; // Finaliza si no se proporcionó la configuración desde PHP.
        }
        var settings = window.CF7OptionLimiterPlugins; // Almacena la configuración en una variable local para simplificar el acceso.
        if ( ! settings.pluginFile ) { // Comprueba que se haya definido el identificador del plugin objetivo.
            return; // Finaliza si falta el identificador necesario para localizar la fila del plugin.
        }
        var pluginRow = document.querySelector( 'tr[data-plugin="' + settings.pluginFile + '"]' ); // Localiza la fila del plugin dentro del listado de plugins.
        if ( ! pluginRow ) { // Comprueba que la fila exista en la tabla.
            return; // Finaliza si no se encontró la fila (por ejemplo, en acciones masivas que ya ocultaron la fila).
        }
        var deactivateLink = pluginRow.querySelector( 'a.deactivate' ); // Localiza el enlace de desactivación estándar.
        var deleteLink = pluginRow.querySelector( '.delete a, a.delete' ); // Intenta localizar el enlace de borrado cuando el plugin ya está desactivado.
        attachPrompt( deactivateLink ); // Adjunta la confirmación al enlace de desactivación si existe.
        attachPrompt( deleteLink ); // Adjunta la confirmación al enlace de borrado si ya está disponible.

        /**
        * Adjunta el diálogo de confirmación al enlace recibido.
        *
        * Explicación:
        * - Resume la tarea principal: Adjunta el diálogo de confirmación al enlace recibido.
        * - Describe brevemente los pasos clave ejecutados internamente.
        * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
        *
        *
        * @param {HTMLAnchorElement|null} link Elemento de enlace al que se añadirá la confirmación.
        *
        * @return void
        */
        function attachPrompt( link ) { // Declara la función auxiliar que añade la confirmación.
            if ( ! link ) { // Comprueba que el enlace exista en el DOM.
                return; // Finaliza cuando no hay enlace que procesar.
            }
            link.addEventListener( 'click', function( event ) { // Escucha el clic sobre el enlace seleccionado.
                event.preventDefault(); // Evita la navegación inmediata para capturar la preferencia del usuario.
                var wantsCleanup = window.confirm( settings.prompt ); // Muestra el diálogo utilizando el mensaje configurado.
                var targetUrl = new window.URL( link.href, window.location.href ); // Construye un objeto URL basado en el destino del enlace.
                var nonceParam = '_wpnonce'; // Define el nombre del parámetro que almacena el nonce generado por WordPress.
                var nonceValue = targetUrl.searchParams.get( nonceParam ); // Recupera el nonce presente en la URL original para conservarlo.
                if ( ! nonceValue && link.dataset && link.dataset.nonce ) { // Comprueba si existe un nonce alternativo almacenado como atributo de datos.
                    nonceValue = link.dataset.nonce; // Captura el nonce proporcionado mediante atributos de datos cuando no venía en la URL.
                    targetUrl.searchParams.set( nonceParam, nonceValue ); // Inserta explícitamente el nonce en la URL final para garantizar su presencia.
                }
                if ( ! nonceValue ) { // Verifica que se haya localizado un nonce válido antes de modificar la URL.
                    window.location.href = link.href; // Restablece la navegación original sin añadir parámetros adicionales cuando no se pudo garantizar el nonce.
                    return; // Finaliza la ejecución evitando enviar la petición sin protección.
                }
                targetUrl.searchParams.set( settings.param, wantsCleanup ? settings.removeValue : settings.keepValue ); // Inserta el parámetro que describe la preferencia elegida.
                if ( settings.warning ) { // Comprueba si se proporcionó un mensaje de advertencia desde PHP.
                    window.alert( settings.warning ); // Muestra la alerta indicando que las reglas dejarán de aplicarse tras la acción.
                }
                window.location.href = targetUrl.toString(); // Redirige manualmente hacia la URL original incluyendo la preferencia.
            } );
        }
    } );
})( window, document );
