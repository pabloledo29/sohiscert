#!/usr/bin/env bash 
# Update de la db diario a las 04:00h.

sed -i 's/memory_limit = 128M/memory_limit = 1024M/g' /etc/opt/rh/rh-php72/php.ini;
sed -i 's/max_execution_time = 300/max_execution_time = 1200/g' /etc/opt/rh/rh-php72/php.ini;

php /furanet/sites/clientes.sohiscert.com/web/htdocs gsbase:connect &
wait;
php /furanet/sites/clientes.sohiscert.com/web/htdocs gsbase:update:cultivosrec &
wait;
echo 0;