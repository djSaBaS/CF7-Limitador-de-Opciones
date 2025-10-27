-- Crea la tabla de excepciones específicas si aún no existe respetando el prefijo dinámico.
CREATE TABLE IF NOT EXISTS `{prefix}cf7_option_limits_overrides` (
    -- Identificador único autoincremental de la excepción.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- ID del formulario de Contact Form 7 asociado a la excepción.
    form_id BIGINT NOT NULL,
    -- Nombre del campo dentro del formulario que recibe la excepción.
    field_name VARCHAR(100) NOT NULL,
    -- Valor exacto de la opción a limitar dentro de la excepción.
    option_value VARCHAR(255) NOT NULL,
    -- Identificador del post o página donde aplica la excepción.
    post_id BIGINT NOT NULL,
    -- Número máximo de selecciones permitidas en la excepción.
    max_count INT NOT NULL DEFAULT 1,
    -- Contador actual de selecciones acumuladas en la excepción.
    current_count INT NOT NULL DEFAULT 0,
    -- Periodo de aplicación del límite específico.
    limit_period ENUM('none','hour','day','week') DEFAULT 'none',
    -- Fecha y hora del último reinicio automático asociado al límite específico.
    limit_reset DATETIME NULL,
    -- Mensaje personalizado que se mostrará cuando la excepción se agote.
    custom_message VARCHAR(255) DEFAULT NULL,
    -- Fecha de creación del registro de excepción.
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- Fecha de última actualización del registro de excepción.
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Clave única que evita excepciones duplicadas para la misma combinación.
    UNIQUE KEY unique_override (form_id, field_name, option_value, post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
