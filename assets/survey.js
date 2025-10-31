/* global window, document */
// Agrupa la lógica de la pestaña de encuestas en una IIFE para evitar contaminar el ámbito global del administrador.
(function( window, document ) { // Inicia la IIFE recibiendo los objetos globales que necesita manipular.
    'use strict'; // Activa el modo estricto para reforzar las validaciones de JavaScript.

    /**
    * Configura el listener que abre la pestaña de encuestas cuando existe el botón correspondiente en pantalla.
    *
    * Explicación:
    * - Resume la tarea principal: Configura el listener que abre la pestaña de encuestas cuando existe el botón correspondiente en pantalla.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return void
    */
    function configureSurveyTabListener() { // Declara la función encargada de preparar el botón de encuestas.
        var surveyTab = document.querySelector( '#survey-tab' ); // Localiza el botón o pestaña asociado mediante su ID estándar.
        if ( ! surveyTab ) { // Comprueba si el elemento no existe en la vista actual.
            return; // Finaliza silenciosamente evitando errores al intentar registrar listeners sobre null.
        }
        surveyTab.addEventListener( 'click', function( event ) { // Registra el listener cuando el elemento está disponible.
            if ( typeof window.CF7OptionLimiterSurvey === 'function' ) { // Comprueba si existe una función externa para gestionar la pestaña.
                window.CF7OptionLimiterSurvey( event ); // Delegada la ejecución en la función global cuando está disponible.
            }
        } );
    }

    // Expone la función en el objeto global para mantener compatibilidad con integraciones existentes.
    window.configureSurveyTabListener = configureSurveyTabListener; // Publica la función garantizando que el resto del código pueda reutilizarla.

    // Garantiza que el DOM esté disponible antes de intentar buscar el elemento de la pestaña.
    document.addEventListener( 'DOMContentLoaded', function() { // Registra el listener que inicializa la configuración tras cargar el documento.
        configureSurveyTabListener(); // Ejecuta la preparación del botón de encuestas de forma segura.
    } );
})( window, document ); // Cierra la IIFE pasando los objetos globales necesarios.
