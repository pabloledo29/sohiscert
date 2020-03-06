#!/usr/bin/env bash  
# Update de la db diario a las 03:00h.      
sed -i 's/memory_limit = 128M/memory_limit = 1024M/g' /etc/opt/rh/rh-php56/php.ini;
sed -i 's/max_execution_time = 300/max_execution_time = 1200/g' /etc/opt/rh/rh-php56/php.ini;

/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:connect   
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:update:operator # Comprobar opCif B99192684 de gs_operator (Eliminar entrada duplicada)
wait;
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:remove:operator
wait;
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:update:operatorentity
wait;
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:update:productosindus
wait;
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:update:productospae
wait;
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:update:industrias
wait;
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:update:ganaderias
wait;
/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console gsbase:update:cultivosrec2
wait;
echo 0;