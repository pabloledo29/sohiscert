#!/bin/bash
#
# Lanza el comando de limpieza de la cache de Symfony en Desarrollo, la Aplicación 
#

/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console cache:clear --env=dev
