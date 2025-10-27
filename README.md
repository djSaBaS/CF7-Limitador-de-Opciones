# CF7 Option Limiter

**CF7 Option Limiter** es un plugin para WordPress que amplía **Contact Form 7**, permitiendo limitar cuántas veces puede seleccionarse una opción concreta en campos tipo *select*, *radio* o *checkbox*.

Diseñado para situaciones donde cada opción solo puede reservarse o elegirse un número limitado de veces (plazas, turnos, inscripciones, etc.), el plugin gestiona automáticamente los contadores, oculta opciones agotadas y permite liberar huecos manualmente desde el panel de administración.

---

## 🚀 Características principales

- **Límites automáticos por opción:** controla cuántas veces puede seleccionarse una opción específica en cualquier formulario de Contact Form 7.  
- **Liberar un uso:** ajusta manualmente un contador desde la tabla administrativa cuando un participante cancela su elección.  
- **Reinicios automáticos:** restablece los contadores cada hora, día, semana o de forma total.  
- **Comprobación en tiempo real:** oculta opciones agotadas al instante mediante AJAX, incluso si varios usuarios abren el formulario simultáneamente.  
- **Gestión visual completa:** crea, edita y revisa reglas desde una pestaña integrada directamente en el editor de Contact Form 7.  
- **Logs automáticos:** registra cada acción y muestra los últimos eventos en un visor integrado.  
- **Modo depuración:** activa registros detallados para diagnóstico sin salir del panel.  
- **Confirmaciones seguras:** al desactivar o eliminar el plugin puedes decidir si limpiar los límites guardados.  

---

## 🧭 Cómo usarlo

1. Instala y activa el plugin (requiere **Contact Form 7**).  
2. Ve a **Contact Form 7 → Option Limiter** para crear tus primeras reglas.  
3. En el editor del formulario, abre la pestaña **Limitador de opciones** para definir los límites de cada campo.  
4. Si una opción alcanza su límite, se ocultará automáticamente o mostrará un aviso impidiendo nuevas selecciones.  
5. Desde la tabla administrativa, puedes **liberar un uso** si un usuario cancela o necesitas reabrir un hueco.  

---

## ⚙️ Requisitos

- **PHP:** 8.0 o superior  
- **WordPress:** 6.0 o superior  
- **Plugin necesario:** Contact Form 7  

---

## 🧩 Instalación

1. Descarga o clona este repositorio.  
2. Comprime la carpeta `cf7-option-limiter` en un archivo ZIP.  
3. En WordPress, ve a **Plugins → Añadir nuevo → Subir plugin**.  
4. Sube el ZIP y activa el plugin.  

---

## 🧪 Actualizaciones

El plugin incluye un sistema automático de **migraciones** que adapta la base de datos cuando se actualiza, sin necesidad de reactivarlo.  
Consulta el archivo [`versions.md`](versions.md) para conocer los cambios de cada versión.

---

## 🧑‍💻 Autor

**Juan Antonio Sánchez Plaza**  
📧 [juanantoniosanchezplaza@hotmail.com](mailto:juanantoniosanchezplaza@hotmail.com)  
📧 [jasanchez@humanitaseducacion.com](mailto:jasanchez@humanitaseducacion.com)

---

## 💙 Agradecimientos

A todo el equipo de **Humanitas Centros Educativos (HCE)** y **Consultora de Educación y Sistemas (CEYS)** por su colaboración, pruebas y apoyo constante.

---

## 📄 Licencia

Este plugin se distribuye bajo la **GPLv2 o posterior**.  
Consulta el archivo [LICENSE](LICENSE) para más detalles.
