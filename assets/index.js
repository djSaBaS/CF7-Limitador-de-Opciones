/* global window, document */
// Encapsula la lógica de los toggles del metabox Redirect en una IIFE para evitar fugas al ámbito global.
(function( window, document ) { // Abre la función autoejecutable recibiendo window y document como dependencias explícitas.
    'use strict'; // Activa el modo estricto para detectar asignaciones silenciosas o variables implícitas.

    /**
    * Localiza el primer elemento que coincide con el selector indicado dentro de un contexto opcional.
    *
    * Explicación:
    * - Resume la tarea principal: Localiza el primer elemento que coincide con el selector indicado dentro de un contexto opcional.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param {string} selector Cadena CSS utilizada para buscar el elemento deseado.
    * @param {ParentNode} [root=document] Nodo contenedor que se empleará como raíz de búsqueda.
    *
    * @return {Element|null} Devuelve el primer elemento encontrado o `null` si no existe coincidencia.
    */
    function qs( selector, root ) { // Declara el helper que simplifica el acceso a querySelector con raíz opcional.
        var context = root || document; // Define el contexto efectivo priorizando el recibido sobre document.
        return context.querySelector( selector ); // Delegada la búsqueda directamente en querySelector para obtener la coincidencia.
    }

    /**
    * Devuelve todos los elementos que coinciden con un selector convirtiendo el NodeList en un arreglo real.
    *
    * Explicación:
    * - Resume la tarea principal: Devuelve todos los elementos que coinciden con un selector convirtiendo el NodeList en un arreglo real.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param {string} selector Cadena CSS utilizada para buscar múltiples elementos.
    * @param {ParentNode} [root=document] Nodo contenedor que se empleará como raíz de búsqueda.
    *
    * @return {Element[]} Lista de elementos coincidentes convertida en un arreglo estándar.
    */
    function qsa( selector, root ) { // Declara el helper que encapsula querySelectorAll devolviendo un arreglo.
        var context = root || document; // Determina el contexto de búsqueda priorizando el parámetro recibido.
        var nodeList = context.querySelectorAll( selector ); // Obtiene el NodeList con todas las coincidencias encontradas.
        return Array.prototype.slice.call( nodeList ); // Convierte el NodeList en arreglo para disponer de métodos estándar.
    }

    /**
    * Normaliza un fragmento de selector evitando prefijos inválidos y manteniendo compatibilidad con IDs y clases.
    *
    * Explicación:
    * - Resume la tarea principal: Normaliza un fragmento de selector evitando prefijos inválidos y manteniendo compatibilidad con IDs y clases.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param {string} fragment Cadena parcial de selector leída desde `data-toggle`.
    *
    * @return {string} Selector corregido listo para ejecutarse con `querySelectorAll` o cadena vacía si el fragmento no era válido.
    */
    function normalizeSelectorFragment( fragment ) { // Declara la función que se encarga de limpiar cada segmento.
        if ( ! fragment ) { // Comprueba si el fragmento está vacío o es `undefined`.
            return ''; // Devuelve cadena vacía porque no hay nada que normalizar.
        }
        var trimmed = fragment.trim(); // Elimina espacios laterales para evitar que arruinen el selector final.
        if ( trimmed === '' ) { // Comprueba si tras recortar la cadena quedó vacía.
            return ''; // Devuelve cadena vacía para omitir el fragmento incorrecto.
        }
        var firstChar = trimmed.charAt( 0 ); // Recupera el primer carácter para evaluar el prefijo existente.
        if ( firstChar === '#' || firstChar === '.' || firstChar === '[' ) { // Comprueba si ya es un ID, clase o selector de atributo.
            return trimmed; // Devuelve el fragmento intacto porque ya incluye un prefijo válido.
        }
        return '#' + trimmed; // Asume que el fragmento representa un ID y antepone # para construir un selector correcto.
    }

    /**
    * Convierte el valor bruto de `data-toggle` en una lista depurada de selectores válidos.
    *
    * Explicación:
    * - Resume la tarea principal: Convierte el valor bruto de `data-toggle` en una lista depurada de selectores válidos.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param {string} rawSelector Cadena original obtenida del atributo `data-toggle`.
    *
    * @return {string[]} Arreglo de selectores ya normalizados y filtrados.
    */
    function normalizeSelectorList( rawSelector ) { // Define la función que procesa el atributo completo.
        if ( ! rawSelector ) { // Comprueba si la cadena recibida está vacía o es `undefined`.
            return []; // Devuelve un arreglo vacío porque no hay selectores que procesar.
        }
        var cleaned = String( rawSelector ).trim(); // Asegura una cadena y elimina espacios laterales superfluos.
        if ( cleaned === '' ) { // Comprueba si tras limpiar la cadena quedó sin contenido.
            return []; // Devuelve un arreglo vacío porque no existen selectores útiles.
        }
        var fragments = cleaned.split( ',' ); // Divide la cadena por comas para permitir múltiples selectores en un solo atributo.
        var selectors = []; // Inicializa el arreglo que almacenará los selectores válidos.
        for ( var i = 0; i < fragments.length; i++ ) { // Recorre cada fragmento obtenido del split.
            var normalized = normalizeSelectorFragment( fragments[ i ] ); // Normaliza el fragmento actual garantizando el prefijo adecuado.
            if ( normalized ) { // Comprueba si la normalización devolvió un selector válido.
                selectors.push( normalized ); // Añade el selector a la lista final para utilizarlo posteriormente.
            }
        }
        return selectors; // Devuelve la lista completa de selectores corregidos.
    }

    /**
    * Obtiene los nodos objetivo asociados a un control con `data-toggle` evitando duplicados.
    *
    * Explicación:
    * - Resume la tarea principal: Obtiene los nodos objetivo asociados a un control con `data-toggle` evitando duplicados.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param {HTMLElement} control Elemento que contiene el atributo `data-toggle`.
    *
    * @return {HTMLElement[]} Arreglo de nodos únicos que deben mostrar u ocultarse según el estado del control.
    */
    function getToggleTargets( control ) { // Declara la función que resuelve los elementos asociados a un control concreto.
        var dataset = control ? control.dataset || {} : {}; // Recupera el dataset del control garantizando un objeto válido.
        var selectors = normalizeSelectorList( dataset.toggle ); // Normaliza la lista de selectores declarada en `data-toggle`.
        if ( selectors.length === 0 ) { // Comprueba si no quedaron selectores válidos tras la normalización.
            return []; // Devuelve un arreglo vacío porque no hay elementos que buscar.
        }
        var nodes = []; // Inicializa el arreglo que almacenará los elementos encontrados.
        for ( var i = 0; i < selectors.length; i++ ) { // Recorre cada selector normalizado.
            var partial = qsa( selectors[ i ] ); // Obtiene todos los elementos que coinciden con el selector actual.
            for ( var j = 0; j < partial.length; j++ ) { // Recorre cada elemento encontrado para evitar duplicados.
                if ( nodes.indexOf( partial[ j ] ) === -1 ) { // Comprueba si el elemento aún no está registrado.
                    nodes.push( partial[ j ] ); // Añade el elemento para mantener una colección única.
                }
            }
        }
        return nodes; // Devuelve la lista consolidada de elementos objetivo.
    }

    /**
    * Determina si el control se considera activo evaluando su tipo y los atributos personalizados.
    *
    * Explicación:
    * - Resume la tarea principal: Determina si el control se considera activo evaluando su tipo y los atributos personalizados.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param {HTMLElement} control Elemento que dispara el toggle.
    *
    * @return {boolean} Devuelve `true` cuando el control debe mostrar sus objetivos asociados.
    */
    function isControlActive( control ) { // Declara la función que interpreta el estado del control según su naturaleza.
        if ( ! control ) { // Comprueba que exista el elemento antes de evaluar su estado.
            return false; // Devuelve false cuando no hay control disponible.
        }
        var dataset = control.dataset || {}; // Recupera el dataset para acceder a atributos personalizados.
        var tagName = control.tagName ? control.tagName.toLowerCase() : ''; // Obtiene el tag del elemento en minúsculas para simplificar comparaciones.
        var type = control.type ? control.type.toLowerCase() : ''; // Obtiene el tipo del control cuando aplica (inputs y selects).
        if ( type === 'checkbox' || type === 'radio' ) { // Comprueba si el control es un checkbox o un radio.
            return control.checked === true; // Considera activo únicamente cuando está marcado.
        }
        if ( tagName === 'select' ) { // Comprueba si se trata de un elemento select.
            if ( dataset.toggleValue ) { // Verifica si se definió un valor específico que activa el toggle.
                return String( control.value ) === String( dataset.toggleValue ); // Considera activo cuando el valor actual coincide con el declarado.
            }
            return control.value !== ''; // En ausencia de valor específico considera activo cualquier selección no vacía.
        }
        if ( dataset.toggleValue ) { // Comprueba si se proporcionó un valor específico en otros tipos de controles.
            return String( control.value ) === String( dataset.toggleValue ); // Compara directamente el valor actual con el requerido.
        }
        return control.value !== ''; // En último término considera activo si el control contiene texto.
    }

    /**
    * Aplica el estado del toggle añadiendo o eliminando clases/atributos en los elementos objetivo.
    *
    * Explicación:
    * - Resume la tarea principal: Aplica el estado del toggle añadiendo o eliminando clases/atributos en los elementos objetivo.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @param {HTMLElement} control Elemento que contiene `data-toggle` y define el estado a evaluar.
    *
    * @return void
    */
    function applyToggleState( control ) { // Declara la función que muestra u oculta los objetivos según el estado del control.
        var targets = getToggleTargets( control ); // Obtiene los elementos asociados al control.
        if ( targets.length === 0 ) { // Comprueba si no hay elementos que actualizar.
            return; // Finaliza porque no hay nada que modificar.
        }
        var dataset = control.dataset || {}; // Recupera el dataset para extraer configuraciones opcionales.
        var activeClass = dataset.toggleClass || 'is-active'; // Determina la clase a aplicar cuando el control está activo.
        var inactiveClass = dataset.toggleInactiveClass || 'is-inactive'; // Determina la clase para el estado inactivo si se desea contraste.
        var attributeName = dataset.toggleAttribute || 'hidden'; // Define el atributo que se aplicará al ocultar, por defecto `hidden`.
        var keepVisible = dataset.toggleKeepVisible === '1' || dataset.toggleKeepVisible === 'true'; // Interpreta si debe mantenerse visible aunque esté inactivo.
        var active = isControlActive( control ); // Evalúa si el control se considera activo siguiendo las reglas definidas.
        for ( var i = 0; i < targets.length; i++ ) { // Recorre cada elemento objetivo para aplicar el estado correspondiente.
            var target = targets[ i ]; // Obtiene el elemento actual del arreglo.
            if ( activeClass ) { // Comprueba si se definió una clase para el estado activo.
                if ( active ) { // Verifica si el control está activo.
                    target.classList.add( activeClass ); // Añade la clase activa cuando corresponde.
                } else {
                    target.classList.remove( activeClass ); // Elimina la clase activa cuando el control deja de estarlo.
                }
            }
            if ( inactiveClass ) { // Comprueba si se definió una clase específica para el estado inactivo.
                if ( active ) { // Verifica si el control está activo.
                    target.classList.remove( inactiveClass ); // Elimina la clase inactiva para mostrar correctamente el contenido.
                } else {
                    target.classList.add( inactiveClass ); // Añade la clase inactiva cuando se desactiva el control.
                }
            }
            if ( keepVisible ) { // Comprueba si se solicitó mantener visibles los objetivos aun estando inactivos.
                if ( attributeName ) { // Comprueba si se definió un atributo que deba gestionarse igualmente.
                    target.removeAttribute( attributeName ); // Elimina el atributo para garantizar que permanezcan visibles.
                }
                continue; // Continúa con el siguiente elemento sin ocultar.
            }
            if ( ! attributeName ) { // Comprueba si no se definió ningún atributo para controlar la visibilidad.
                if ( active ) { // Verifica si el control está activo.
                    target.classList.remove( 'is-hidden' ); // Elimina la clase genérica de ocultación cuando se activa.
                } else {
                    target.classList.add( 'is-hidden' ); // Añade la clase genérica de ocultación cuando se desactiva.
                }
                continue; // Continúa con el siguiente objetivo tras manejar las clases genéricas.
            }
            if ( active ) { // Verifica si el control está activo.
                target.removeAttribute( attributeName ); // Elimina el atributo de ocultación para mostrar el contenido.
            } else {
                target.setAttribute( attributeName, attributeName ); // Añade el atributo indicando al navegador que debe ocultarse.
            }
        }
    }

    /**
    * Inicializa los listeners de todos los controles con `data-toggle` asegurando un estado coherente desde el arranque.
    *
    * Explicación:
    * - Resume la tarea principal: Inicializa los listeners de todos los controles con `data-toggle` asegurando un estado coherente desde el arranque.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    * @return void
    */
    function bindToggles() { // Declara la función principal que prepara todos los toggles disponibles en la vista.
        var controls = qsa( '[data-toggle]' ); // Obtiene todos los elementos que declaran el atributo `data-toggle`.
        if ( controls.length === 0 ) { // Comprueba si no existen controles en la página actual.
            return; // Finaliza porque no hay nada que configurar.
        }
        for ( var i = 0; i < controls.length; i++ ) { // Recorre cada control encontrado para preparar su comportamiento.
            (function( control ) { // Crea un cierre por cada control para preservar la referencia en los listeners.
                var eventName = control.tagName && control.tagName.toLowerCase() === 'select' ? 'change' : 'input'; // Determina el evento principal según el tipo de elemento.
                if ( control.type && ( control.type.toLowerCase() === 'checkbox' || control.type.toLowerCase() === 'radio' ) ) { // Comprueba si se trata de un input tipo checkbox o radio.
                    eventName = 'change'; // Ajusta el evento a `change` para reflejar correctamente el estado marcado.
                }
                control.addEventListener( eventName, function() { // Registra el listener que reaccionará a cada cambio del control.
                    applyToggleState( control ); // Recalcula el estado de los elementos objetivo al modificarse el control.
                } );
                applyToggleState( control ); // Aplica inmediatamente el estado inicial para evitar parpadeos o estados incorrectos.
            })( controls[ i ] ); // Invoca la función inmediatamente pasando el control actual como argumento.
        }
    }

    // Espera a que el DOM esté completamente cargado antes de buscar y enlazar los toggles declarados.
    document.addEventListener( 'DOMContentLoaded', function() { // Registra el listener que arranca la configuración al finalizar la carga inicial.
        bindToggles(); // Ejecuta la preparación de todos los controles con `data-toggle` en el documento.
    } );
})( window, document ); // Cierra la IIFE pasando los objetos globales necesarios.
