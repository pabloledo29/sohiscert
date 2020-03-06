#!/usr/bin/env bash
#
# OPS Online 2
#find ${OPENSHIFT_REPO_DIR}/htdocs/app/logs/update/* -mtime +60 -type f -delete
#
# OPS Online 3 - Cada día 1 de cada mes a las 0h
/usr/bin/find /opt/app-root/src/app/logs/update/* -type f -delete
/usr/bin/find /opt/app-root/src/web/docs/temp/* -type f -delete