# Carpeta `sql`

En este directorio se almacenan los archivos SQL que definen y evolucionan el esquema del plugin:

- `create_table.sql`: script de creación inicial de las tablas personalizadas.
- `update_001_rename_last_reset.sql`, `update_002_add_overrides_table.sql`, `update_003_add_hide_exhausted_column.sql`: migraciones incrementales que se aplican automáticamente cuando la versión del plugin cambia.
