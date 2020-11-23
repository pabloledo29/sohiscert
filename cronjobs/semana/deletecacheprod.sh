#!/bin/bash
#
# Lanza el comando de limpieza de la cache de Symfony en Producción, la Aplicación 
#

/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console cache:clear --env=prod

/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console cache:pool:clear cache.app
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console cache:pool:clear cache.system
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console cache:pool:clear cache.validator
