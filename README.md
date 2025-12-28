![gdw_opengraph](https://img-module.gdw.mx/github_assets/gdw_core/gdw_core_01.jpg)

# GDW_Core
[![Latest Stable Version](http://poser.pugx.org/gdw/core/v?style=for-the-badge)](https://packagist.org/packages/gdw/core) [![Total Downloads](http://poser.pugx.org/gdw/core/downloads?style=for-the-badge)](https://packagist.org/packages/gdw/core) [![PHP Version Require](http://poser.pugx.org/gdw/core/require/php?style=for-the-badge)](https://packagist.org/packages/gdw/core)


#### Descripción

GDW_Core es un módulo base para Magento 2, diseñado para centralizar
configuraciones, utilidades y convenciones comunes en los módulos
desarrollados por GDW (Gestión Digital Web).

Este módulo actúa como el core técnico del ecosistema GDW, reduciendo
código duplicado, mejorando la mantenibilidad y estandarizando la forma
en la que se construyen y administran los módulos personalizados.

#### Atención:
Este módulo puede ser requerido como dependencia por otros módulos GDW.
No se recomienda desinstalarlo si existen módulos dependientes activos.



#### Características principales

- Crea una tab general en la configuración del administrador
- Define un grupo personalizado para tareas cron
- Centraliza un grupo ACL general para permisos
- Agrega una sección administrativa con:
  * Listado de módulos GDW instalados
  * Información general del desarrollador
- Incluye comandos de consola (CLI) para:
  * Probar cron jobs
  * Ejecutar funciones manualmente
  * Debug y testing controlado


#### ¿Por qué usar GDW_Core?

GDW_Core existe para resolver problemas comunes en proyectos Magento
medianos y grandes:

- Evitar repetir configuraciones (system.xml, acl.xml, crontab.xml)
- Centralizar helpers y utilidades
- Mantener una arquitectura consistente entre módulos
- Facilitar soporte, mantenimiento y escalabilidad

Ideal para:
- Proyectos con múltiples módulos personalizados
- Entornos con cron jobs e integraciones
- Equipos que buscan estandarización



#### Comandos de consola

GDW_Core incluye comandos CLI para ejecutar lógica personalizada sin
depender de eventos o cron.

Ejemplo de uso:

php bin/magento gdw:run:function --class="Vendor\\Module\\Model\\Example" --method="execute"

Útil para:
- Ejecutar cron manualmente
- Probar integraciones
- Debug en entornos productivos (con precaución)

#### Configuración en el administrador

Ruta:
Stores -> Configuration -> GDW

Desde esta sección puedes:
- Consultar módulos GDW instalados
- Gestionar configuraciones globales
- Validar permisos ACL
- Ver información del desarrollador


#### Uso como dependiencia

GDW_Core está diseñado para ser requerido por otros módulos GDW.
```
Ejemplo composer.json:
{
  "require": {
    "gdw/core": "^2.0"
  }
}
```

#### Esto garantiza:
- Compatibilidad entre módulos
- Configuración compartida
- Menor mantenimiento a largo plazo


#### Compatibilidad

- Magento Open Source 2.3.x
- Magento Open Source / Adobe Commerce 2.4.x
- PHP según requisitos oficiales de Magento

#### Instalación
```
Ejecutar en la raíz de Magento:
composer require gdw/core
php bin/magento module:enable GDW_Core
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

#### Actualización
```
composer update gdw/core
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

#### Eliminación
```
php bin/magento module:disable GDW_Core
composer remove gdw/core
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```
### Importante
Verifica que no existan módulos dependientes activos.


### Roadmap
- Helpers compartidos para integraciones externas
- Sistema de logging centralizado
- Panel de diagnóstico GDW
- Exportación de configuración GDW


### Expresiones de gratitud
- 📢 Comenta a otros sobre este proyecto
- ⭐ Dale una estrella en GitHub
- 👨🏽‍💻 Da las gracias públicamente
- [🍺 Invítame una cerveza](https://www.paypal.me/gestiondigitalweb)


### Otros enlaces
[🌐 Sitio web](https://gdw.mx/?utm_source=github&utm_medium=gdw&utm_campaign=core&utm_id=link)
[🌐 Listado de módulos](https://gdw.mx/modulos/)
[🌐 Facebook](https://www.facebook.com/GestionDigitalWeb)
[🌐 YouTube](https://www.youtube.com/c/Gestiondigitalweb)


### Licencia
Este proyecto está licenciado bajo la licencia MIT.
