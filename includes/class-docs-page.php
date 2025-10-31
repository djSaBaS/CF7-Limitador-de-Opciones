<?php
// Impide el acceso directo al archivo sin la carga de WordPress.
if ( ! defined( 'ABSPATH' ) ) { // Comprueba si la constante de WordPress está definida.
    exit; // Finaliza inmediatamente para evitar ejecuciones directas.
}

// Clase encargada de registrar la página de documentación y generar su contenido HTML.
class CF7_OptionLimiter_Docs { // Declara la clase que agrupa toda la lógica relacionada con la documentación.

    // Nombre del slug utilizado para identificar la página dentro del administrador.
    const PAGE_SLUG = 'cf7-option-limiter-docs'; // Constante que centraliza el slug evitando inconsistencias.

    /**
    * Inicializa los hooks necesarios para exponer la documentación en el administrador.
    *
    * Explicación:
    * - Resume la tarea principal: Inicializa los hooks necesarios para exponer la documentación en el administrador.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function init() { // Método principal que registra los enganches necesarios.
        add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) ); // Añade la página como submenú dentro del área de plugins.
    }

    /**
    * Registra la página oculta dentro del menú de Plugins para acceder a la documentación.
    *
    * Explicación:
    * - Resume la tarea principal: Registra la página oculta dentro del menú de Plugins para acceder a la documentación.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function register_menu() { // Método que crea la página dedicada a la documentación.
        add_submenu_page( // Llama a la API de menús del administrador.
            'plugins.php', // Sitúa la nueva página bajo el menú de Plugins para que encaje con el flujo solicitado.
            __( 'Documentos y preguntas frecuentes', 'cf7-option-limiter' ), // Título que se mostrará en la pestaña del navegador.
            __( 'Documentos y preguntas frecuentes', 'cf7-option-limiter' ), // Texto del elemento de menú (aparece sólo al acceder directamente).
            'manage_options', // Capacidad requerida para consultar la documentación.
            self::PAGE_SLUG, // Slug único reutilizado en enlaces internos.
            array( __CLASS__, 'render_page' ) // Callback encargado de imprimir el contenido HTML completo.
        );
    }

    /**
    * Renderiza la página con el contenido transformado desde el manual en Markdown.
    *
    * Explicación:
    * - Resume la tarea principal: Renderiza la página con el contenido transformado desde el manual en Markdown.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return void
    */
    public static function render_page() { // Método que imprime la documentación dentro del administrador.
        if ( ! current_user_can( 'manage_options' ) ) { // Comprueba que el usuario tenga permisos suficientes.
            wp_die( esc_html__( 'No tienes permisos suficientes para acceder a esta documentación.', 'cf7-option-limiter' ) ); // Interrumpe la ejecución con un mensaje legible.
        }
        echo '<div class="wrap cf7-option-limiter-docs">'; // Abre el contenedor estándar de páginas administrativas.
        echo '<h1>' . esc_html__( 'Documentos y preguntas frecuentes', 'cf7-option-limiter' ) . '</h1>'; // Muestra el título principal de la vista.
        echo '<div class="cf7-option-limiter-docs-content">'; // Contenedor que envolverá el contenido convertido a HTML.
        echo self::get_manual_html(); // Inserta el manual convertido en HTML listo para mostrarse.
        echo '</div>'; // Cierra el contenedor del contenido principal.
        echo '<p class="cf7-option-limiter-docs-actions">'; // Abre el contenedor de acciones inferior.
        echo '<a class="button button-secondary" href="' . esc_url( admin_url( 'plugins.php' ) ) . '">'; // Imprime el botón que devuelve al listado de plugins.
        echo esc_html__( 'Volver al listado de plugins', 'cf7-option-limiter' ); // Texto descriptivo del botón.
        echo '</a>'; // Cierra el enlace del botón.
        echo '</p>'; // Cierra el contenedor de acciones.
        echo '</div>'; // Cierra el contenedor principal.
    }

    /**
    * Devuelve la URL completa hacia la página de documentación dentro del administrador.
    *
    * Explicación:
    * - Resume la tarea principal: Devuelve la URL completa hacia la página de documentación dentro del administrador.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return string
    */
    public static function get_page_url() { // Método auxiliar que construye la URL hacia la página de documentación.
        return add_query_arg( // Utiliza add_query_arg para asegurar la construcción correcta de la URL.
            array( 'page' => self::PAGE_SLUG ), // Parámetro necesario para acceder a la página registrada.
            admin_url( 'plugins.php' ) // Base correspondiente al menú de plugins.
        );
    }

    /**
    * Obtiene el contenido del manual en formato Markdown y lo transforma a HTML seguro.
    *
    * Explicación:
    * - Resume la tarea principal: Obtiene el contenido del manual en formato Markdown y lo transforma a HTML seguro.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @return string
    */
    protected static function get_manual_html() { // Método protegido que carga y procesa el manual.
        $manual_path = CF7_OPTION_LIMITER_DIR . 'manual_usuario.md'; // Calcula la ruta absoluta del manual.
        if ( ! file_exists( $manual_path ) ) { // Comprueba que el archivo exista antes de intentar leerlo.
            return '<p>' . esc_html__( 'El manual no se encuentra disponible en este momento.', 'cf7-option-limiter' ) . '</p>'; // Devuelve un aviso informativo cuando falta el archivo.
        }
        $raw_content = file_get_contents( $manual_path ); // Lee el contenido completo del archivo Markdown.
        if ( false === $raw_content ) { // Comprueba que la lectura se haya realizado correctamente.
            return '<p>' . esc_html__( 'No se pudo cargar el manual solicitado.', 'cf7-option-limiter' ) . '</p>'; // Devuelve un mensaje indicando el fallo de lectura.
        }
        return self::convert_markdown_to_html( $raw_content ); // Convierte el Markdown a HTML utilizando el método auxiliar.
    }

    /**
    * Convierte un texto en Markdown básico a HTML aplicando escapes y etiquetas semánticas sencillas.
    *
    * Explicación:
    * - Resume la tarea principal: Convierte un texto en Markdown básico a HTML aplicando escapes y etiquetas semánticas sencillas.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param string $markdown Texto original en formato Markdown.
    *
    * @return string
    */
    protected static function convert_markdown_to_html( $markdown ) { // Método protegido que realiza la conversión manual.
        $normalized = str_replace( array( "\r\n", "\r" ), "\n", (string) $markdown ); // Unifica saltos de línea para simplificar el procesamiento.
        $lines      = explode( "\n", $normalized ); // Divide el contenido en líneas individuales.
        $html       = ''; // Inicializa la cadena que almacenará el HTML generado.
        $paragraph  = array(); // Acumula líneas consecutivas que conforman un párrafo.
        $in_list    = false; // Indica si actualmente se está generando una lista.
        $list_tag   = ''; // Almacena el tipo de lista activa (ul u ol).
        foreach ( $lines as $line ) { // Recorre cada línea del archivo Markdown.
            $trimmed = trim( $line ); // Elimina espacios laterales para facilitar la detección de patrones.
            if ( '' === $trimmed ) { // Comprueba si la línea está vacía.
                if ( $in_list ) { // Cierra la lista en curso cuando se encuentra una línea vacía.
                    $html    .= '</' . $list_tag . '>'; // Cierra la etiqueta de lista correspondiente.
                    $in_list  = false; // Actualiza el estado indicando que ya no se está dentro de una lista.
                    $list_tag = ''; // Restablece el tipo de lista.
                }
                if ( ! empty( $paragraph ) ) { // Comprueba si existen líneas acumuladas para un párrafo.
                    $html      .= '<p>' . self::render_inline_markup( implode( ' ', $paragraph ) ) . '</p>'; // Convierte el párrafo acumulado a HTML seguro.
                    $paragraph  = array(); // Restablece el acumulador de párrafos.
                }
                continue; // Pasa a la siguiente línea al tratarse de una separación de bloques.
            }
            if ( preg_match( '/^(#{1,6})\s+(.*)$/', $trimmed, $matches ) ) { // Detecta encabezados Markdown al inicio de la línea.
                if ( $in_list ) { // Cierra cualquier lista activa antes de imprimir el encabezado.
                    $html    .= '</' . $list_tag . '>'; // Cierra la lista actual para mantener la estructura válida.
                    $in_list  = false; // Actualiza el estado de lista.
                    $list_tag = ''; // Restablece el tipo de lista.
                }
                if ( ! empty( $paragraph ) ) { // Imprime cualquier párrafo pendiente antes del encabezado.
                    $html      .= '<p>' . self::render_inline_markup( implode( ' ', $paragraph ) ) . '</p>'; // Convierte el párrafo previo en HTML.
                    $paragraph  = array(); // Limpia el acumulador de párrafos.
                }
                $level = min( 6, strlen( $matches[1] ) ); // Determina el nivel del encabezado limitándolo a h6.
                $html .= sprintf( '<h%d>%s</h%d>', $level, self::render_inline_markup( $matches[2] ), $level ); // Genera la etiqueta de encabezado con el contenido escapado.
                continue; // Pasa a la siguiente línea tras procesar el encabezado.
            }
            if ( preg_match( '/^[-*+]\s+(.*)$/', $trimmed, $matches ) ) { // Detecta elementos de lista no ordenada.
                if ( ! $in_list || 'ul' !== $list_tag ) { // Comprueba si se debe iniciar una nueva lista no ordenada.
                    if ( $in_list ) { // Cierra cualquier lista previa para evitar anidamientos incorrectos.
                        $html    .= '</' . $list_tag . '>'; // Cierra la lista anterior.
                        $list_tag = ''; // Limpia el tipo de lista previo.
                    }
                    if ( ! empty( $paragraph ) ) { // Imprime los párrafos pendientes antes de iniciar la lista.
                        $html      .= '<p>' . self::render_inline_markup( implode( ' ', $paragraph ) ) . '</p>'; // Convierte el párrafo en HTML.
                        $paragraph  = array(); // Limpia el acumulador de párrafos.
                    }
                    $html    .= '<ul>'; // Abre la lista no ordenada.
                    $in_list  = true; // Marca que se está dentro de una lista.
                    $list_tag = 'ul'; // Registra el tipo de lista actual.
                }
                $html .= '<li>' . self::render_inline_markup( $matches[1] ) . '</li>'; // Añade el elemento de lista con su contenido procesado.
                continue; // Continúa con la siguiente línea.
            }
            if ( preg_match( '/^\d+\.\s+(.*)$/', $trimmed, $matches ) ) { // Detecta elementos de lista ordenada.
                if ( ! $in_list || 'ol' !== $list_tag ) { // Comprueba si se debe abrir una nueva lista ordenada.
                    if ( $in_list ) { // Cierra la lista previa para evitar estructuras incorrectas.
                        $html    .= '</' . $list_tag . '>'; // Cierra la lista activa.
                        $list_tag = ''; // Restablece el tipo de lista.
                    }
                    if ( ! empty( $paragraph ) ) { // Imprime cualquier párrafo pendiente antes de la lista ordenada.
                        $html      .= '<p>' . self::render_inline_markup( implode( ' ', $paragraph ) ) . '</p>'; // Convierte el párrafo a HTML.
                        $paragraph  = array(); // Limpia el acumulador de párrafos.
                    }
                    $html    .= '<ol>'; // Abre la lista ordenada.
                    $in_list  = true; // Marca que se ha iniciado una lista.
                    $list_tag = 'ol'; // Establece el tipo de lista actual como ordenada.
                }
                $html .= '<li>' . self::render_inline_markup( $matches[1] ) . '</li>'; // Añade el elemento numérico procesado.
                continue; // Continúa con la siguiente línea.
            }
            $paragraph[] = $trimmed; // Acumula la línea actual para componer un párrafo posterior.
        }
        if ( $in_list ) { // Comprueba si quedó una lista abierta al finalizar el recorrido.
            $html .= '</' . $list_tag . '>'; // Cierra la lista pendiente para mantener un HTML válido.
        }
        if ( ! empty( $paragraph ) ) { // Comprueba si hay un último párrafo pendiente.
            $html .= '<p>' . self::render_inline_markup( implode( ' ', $paragraph ) ) . '</p>'; // Convierte el párrafo restante a HTML.
        }
        return $html; // Devuelve el HTML completo generado a partir del Markdown proporcionado.
    }

    /**
    * Procesa enlaces y fragmentos de código en línea garantizando un HTML seguro.
    *
    * Explicación:
    * - Resume la tarea principal: Procesa enlaces y fragmentos de código en línea garantizando un HTML seguro.
    * - Describe brevemente los pasos clave ejecutados internamente.
    * - Clarifica el uso de parámetros y valores de retorno para mantener el contexto.
    *
    *
    * @param string $text Texto plano que puede contener enlaces y fragmentos de código en sintaxis Markdown.
    *
    * @return string
    */
    protected static function render_inline_markup( $text ) { // Método que transforma elementos en línea conservando la seguridad.
        $replacements = array(); // Inicializa el arreglo donde se almacenarán los marcadores temporales.
        $counter      = 0; // Inicializa el contador para generar identificadores únicos.
        $processed    = preg_replace_callback( // Procesa los fragmentos de código en línea primero.
            '/`([^`]+)`/', // Expresión regular que detecta texto encerrado entre acentos graves.
            function ( $matches ) use ( &$replacements, &$counter ) { // Callback que genera el marcador correspondiente.
                $token = '__CF7_OL_CODE_' . $counter . '__'; // Define un token único basado en el contador.
                $counter++; // Incrementa el contador para el siguiente marcador.
                $replacements[ $token ] = '<code>' . esc_html( $matches[1] ) . '</code>'; // Guarda el HTML seguro asociado al código.
                return $token; // Sustituye el fragmento original por el token temporal.
            },
            (string) $text // Asegura que se trabaje con una cadena.
        );
        $processed = preg_replace_callback( // Procesa los enlaces Markdown del texto.
            '/\[([^\]]+)\]\(([^\)]+)\)/', // Expresión que detecta el patrón [texto](url).
            function ( $matches ) use ( &$replacements, &$counter ) { // Callback que genera el token para cada enlace.
                $token = '__CF7_OL_LINK_' . $counter . '__'; // Define un token único basado en el contador.
                $counter++; // Incrementa el contador para mantener unicidad.
                $replacements[ $token ] = '<a href="' . esc_url( $matches[2] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $matches[1] ) . '</a>'; // Guarda el HTML del enlace escapado y seguro.
                return $token; // Reemplaza el enlace original por el token temporal.
            },
            $processed // Utiliza el resultado parcial que ya contiene los tokens de código.
        );
        $escaped = esc_html( $processed ); // Escapa todo el texto restante para evitar inserciones HTML directas.
        if ( ! empty( $replacements ) ) { // Comprueba si existen tokens que deban sustituirse por HTML permitido.
            $escaped = str_replace( array_keys( $replacements ), array_values( $replacements ), $escaped ); // Sustituye los tokens por el HTML seguro.
        }
        return $escaped; // Devuelve el texto con enlaces y código transformados manteniendo la seguridad.
    }
}
