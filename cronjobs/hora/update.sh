#!/usr/bin/env bash
# Update de la db diario.
sed -i 's/memory_limit = 128M/memory_limit = 512M/g' /etc/opt/rh/rh-php72/php.ini;
sed -i 's/max_execution_time = 300/max_execution_time = 1200/g' /etc/opt/rh/rh-php72/php.ini;
if [ `TZ=Europe/Madrid date +%H` == "01" ]
then
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:connect
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:basicinfo
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:registro
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:productos
fi
if [ `TZ=Europe/Madrid date +%H` == "03" ]
then
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:connect
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:operator
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:remove:operator
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:operatorentity
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:productosindus
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:productospae
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:industrias
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:ganaderias
    ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:cultivosrec2
fi

if [ `TZ=Europe/Madrid date +%H` == "04" ]
then
   ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:connect
   ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:cultivosrec
fi

if [ `TZ=Europe/Madrid date +%H` == "05" ]
then
   ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:connect
   ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:clients
fi

if [ `TZ=Europe/Madrid date +%H` == "06" ]
then
   ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:connect
   ${OPENSHIFT_HOMEDIR}/app-root/runtime/bin/php ${OPENSHIFT_REPO_DIR}/htdocs/app/console gsbase:update:contact
fi
