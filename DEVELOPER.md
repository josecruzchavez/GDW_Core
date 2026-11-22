# GDW_Core - Documentación para desarrolladores

## Catálogo de versiones de módulos GDW

`GDW\Core\Model\ModuleVersionCatalog` obtiene el catálogo público desde:

```text
https://gdw.mx/module-versions.json
```

El trabajo `gdw_core_refresh_module_version_catalog` ejecuta `GDW\Core\Cron\RefreshModuleVersionCatalog::execute` en el grupo `gdw`, todos los días a las 03:00 (`0 3 * * *`), según la zona horaria configurada en Magento.

La respuesta se acepta únicamente cuando el servidor devuelve HTTP 200 y el JSON cumple el esquema esperado. El cliente HTTP usa un timeout de 15 segundos. El catálogo válido se guarda en caché con el identificador `gdw_core_module_versions_v1` y la etiqueta `GDW_CORE_MODULE_VERSIONS`.

Si falla la petición, la respuesta HTTP, la validación del JSON o el guardado en caché, el proceso registra una advertencia y no sustituye el catálogo válido que ya estuviera en caché. El método `refresh()` devuelve `false` en esos casos y el trabajo cron no propaga esos fallos.

Al leer la caché, si el contenido está corrupto o no cumple el esquema actual, `getCatalog()` registra una advertencia, intenta eliminar esa entrada y devuelve `null` para que la pantalla use el mensaje de información no disponible.

En **Módulos Instalados**, el catálogo se consulta por paquete Composer y se compara con la versión más reciente de la línea correspondiente. Se muestra la fecha de publicación, el enlace al changelog y un aviso cuando hay una versión más reciente. Si no hay datos públicos para la versión, se muestra: “Sin información pública para esta versión.” Las actualizaciones continúan instalándose mediante Composer.

## Identificación de módulos de pago

Los metadatos de un módulo pueden incluir `'pricing_model' => 'paid'` en `GdwModuleMeta::getMeta()`. GDW_Core muestra el indicador `$` en la fila del módulo instalado si tiene ese valor o si otro módulo instalado lo declara como `pro_module`. Esta clasificación no comprueba licencias ni acceso a Packeton.

## Pruebas unitarias

Las pruebas del catálogo están en `Test/Unit/Model/ModuleVersionCatalogTest.php`. Cubren errores HTTP y de transporte, JSON y esquema inválidos, fallo de escritura en caché, conservación del catálogo previo, refresco correcto y presentación administrativa.

La rama `3.x` requiere PHP 7.4 y `magento/framework >=102.0.0 <103.0.0`; para validarla usa Magento 2.3.7 (`magento237`).

```bash
export GDW_CORE_WORKTREE="/Users/jcruz/Desarrollo/1 - Github/GDW_Modulos/_worktrees/GDW_Core_3x_port"
cd /Users/jcruz/Desarrollo/Magentos/magento237
docker compose run --rm --no-deps \
  -v "$GDW_CORE_WORKTREE:/var/www/html/vendor/gdw/core:cached" \
  php vendor/bin/phpunit --bootstrap vendor/autoload.php vendor/gdw/core/Test/Unit/Model/ModuleVersionCatalogTest.php
```
