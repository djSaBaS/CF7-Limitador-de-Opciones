-- Actualización que renombra la columna last_reset a limit_reset para alinearse con la especificación actual.
ALTER TABLE `{prefix}cf7_option_limits`
    CHANGE `last_reset` `limit_reset` DATETIME NULL;
