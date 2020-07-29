#!/usr/bin/env bash
# Update de la db diario a las 06:00h.

sed -i 's/memory_limit = 128M/memory_limit = 1024M/g' /etc/opt/rh/rh-php72/php.ini;
sed -i 's/max_execution_time = 300/max_execution_time = 1200/g' /etc/opt/rh/rh-php72/php.ini;

/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:connect &
wait;
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:update:contact &
wait;
echo 0;