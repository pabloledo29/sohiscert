#!/usr/bin/env bash
# Envia mail, si hay en cola, cada 2 minutos
    
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console swiftmailer:spool:send --message-limit=24 --env=prod
#/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console swiftmailer:spool:send --message-limit=24 --env=dev
