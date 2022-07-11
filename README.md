![gdw_opengraph](https://gestiondigitalweb.com/github_assets/gdw_core/gdw_core_01.jpg)

# GDW_Core

Configuración base para módulos de magento 2 creados por GDW
* Crea una tab general en la configuración del administrador.
* Crea un grupo personalizado para las tareas cron.
* Crea un grupo general para el acceso ACL.
* Crea una tab para mostrar un listados de módulos creados por GDW e instalados en su magento.
* Crea una tab con la información general de mi trabajo.

## Compatibilidad
✓ Magento 2.3.x, ✓ Magento 2.4.x

###### Ejecuta los siguientes comandos en la ruta base de Magento.

### Instalación 

```
composer require gdw/core

php bin/magento module:enable GDW_Core
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

### Actualización 

```
composer update gdw/core

php bin/magento module:enable GDW_Core
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

### Eliminación del módulo

```
php bin/magento module:disable GDW_Core

composer remove gdw/core

php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

### Expresiones de gratitud

* 📢 Comenta a otros sobre este proyecto.
* 👨🏽‍💻 Da las gracias públicamente.
* [🍺 Invítame una cerveza.](https://www.paypal.me/gestiondigitalweb)

### Otros enlaces
* [Sitio web](https://gestiondigitalweb.com/?utm_source=github&utm_medium=gdw&utm_campaign=core&utm_id=link)
* [Listado de Módulos](https://gestiondigitalweb.com/gdw-modulos/index.php)
* [Facebook](https://www.facebook.com/GestionDigitalWeb)
* [Youtube](https://www.youtube.com/c/Gestiondigitalweb)
