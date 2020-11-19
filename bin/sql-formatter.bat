@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../vendor/doctrine/sql-formatter/bin/sql-formatter
php "%BIN_TARGET%" %*
