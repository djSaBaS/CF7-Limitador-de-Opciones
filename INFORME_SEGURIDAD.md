# Informe de Auditoría de Seguridad - Sentinel 🛡️

## Resumen Ejecutivo

La auditoría de seguridad del plugin "CF7 Option Limiter" ha concluido. El código demuestra un alto nivel de madurez en seguridad, con una implementación robusta de las defensas contra vulnerabilidades comunes como Inyección SQL, Cross-Site Scripting (XSS) y Cross-Site Request Forgery (CSRF).

Sin embargo, se han identificado **dos vulnerabilidades de severidad MEDIA** que deberían ser abordadas para fortalecer aún más la postura de seguridad del plugin y protegerlo contra riesgos de exposición de datos y denegación de servicio.

A continuación se detallan los hallazgos.

---

## Vulnerabilidad 1: Exposición de Información Sensible en Logs

- **Severidad:** 🔒 MEDIA
- **Descripción:** El método `upsert_limit` en la clase `CF7_OptionLimiter_DB` registra la carga útil completa (`$raw_payload`) de las solicitudes de guardado antes de cualquier sanitización. Aunque esto es útil para la depuración, crea un riesgo de seguridad. Si el valor de una opción de formulario (`option_value`) contuviera accidentalmente datos sensibles (por ejemplo, un email, DNI, etc.), esta información se almacenaría en texto plano en el archivo de log (`wp-content/uploads/cf7-option-limiter/cf7-option-limiter.log`).
- **Impacto:** Divulgación no intencionada de información potencialmente sensible a cualquier persona con acceso al sistema de archivos del servidor (administradores del sitio, personal de soporte, o un atacante que haya ganado acceso). Esto podría infringir normativas de protección de datos.
- **Ubicación:**
  - **Archivo:** `includes/class-db-manager.php`
  - **Método:** `upsert_limit()`
- **Recomendación:** Modificar la lógica de registro para evitar almacenar datos sin procesar. En lugar de registrar el `$raw_payload` completo, se podría:
    1.  Registrar únicamente los datos ya sanitizados.
    2.  Registrar una versión anonimizada o truncada de los valores.
    3.  Omitir por completo el registro del payload y registrar solo metadatos de la operación (ej: "Regla para form_id 123 guardada").
- **Complejidad de Corrección:** Baja.

---

## Vulnerabilidad 2: Falta de Limitación de Tasa (Rate Limiting) en Endpoint AJAX Público

- **Severidad:** 🔒 MEDIA
- **Descripción:** El endpoint AJAX `ajax_check_availability`, accesible tanto para usuarios autenticados como anónimos (`wp_ajax_nopriv_cf7_option_limiter_check`), no implementa ningún tipo de limitación de tasa (rate limiting). Esto significa que un atacante puede enviar un número ilimitado de peticiones a este endpoint en un corto período de tiempo.
- **Impacto:** Un atacante podría crear un script para inundar el endpoint con miles de solicitudes. Cada solicitud desencadena una o más consultas a la base de datos, lo que consumiría recursos significativos del servidor (CPU, memoria, conexiones a la base de datos). Esto podría llevar a una **Denegación de Servicio (DoS)**, ralentizando el sitio web para los usuarios legítimos o incluso haciéndolo completamente inaccesible.
- **Ubicación:**
  - **Archivo:** `includes/class-limiter-handler.php`
  - **Método:** `ajax_check_availability()`
- **Recomendación:** Implementar un mecanismo de limitación de tasa basado en la dirección IP. Se puede lograr utilizando la API de Transients de WordPress para registrar la hora y el número de solicitudes de cada IP. Si una IP excede un umbral razonable (por ejemplo, 60 solicitudes por minuto), las solicitudes posteriores de esa IP se bloquearían con un error HTTP `429 Too Many Requests` durante un período de tiempo determinado (ej: 5 minutos).
- **Complejidad de Corrección:** Media.
