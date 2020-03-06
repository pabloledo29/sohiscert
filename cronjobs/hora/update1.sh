#!/usr/bin/env bash   
# Update de la db diario a la 01:00h.
sed -i 's/memory_limit = 128M/memory_limit = 1024M/g' /etc/opt/rh/rh-php56/php.ini; 
sed -i 's/max_execution_time = 300/max_execution_time = 1200/g' /etc/opt/rh/rh-php56/php.ini;

/opt/rh/rh-php56/root/usr/bin/php /opt/app-root/src/app/console gsbase:connect 
/opt/rh/rh-php56/root/usr/bin/php /opt/app-root/src/app/console gsbase:update:basicinfo
/opt/rh/rh-php56/root/usr/bin/php /opt/app-root/src/app/console gsbase:update:registro
/opt/rh/rh-php56/root/usr/bin/php /opt/app-root/src/app/console gsbase:update:productos

wait;
echo 0;