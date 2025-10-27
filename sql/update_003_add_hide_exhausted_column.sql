-- Añade la columna hide_exhausted para controlar si las opciones agotadas deben ocultarse.
ALTER TABLE `{prefix}cf7_option_limits`
    ADD COLUMN IF NOT EXISTS `hide_exhausted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `option_value`;
