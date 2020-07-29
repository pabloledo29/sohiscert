#!/usr/bin/env bash
# Actualizar las Fechas de los Documentos FTPs y Enviar mail al Operador, si hay en cola, cada 2 minutos
#MNN Para mandar tareas de email de conclusiones
#/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console appbundle:emaildoccon:send  
#/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console swiftmailer:spool:send --env=prod
#/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console swiftmailer:spool:send --message-limit=24 --env=prod

sed -i 's/memory_limit = 128M/memory_limit = 1024M/g' /etc/opt/rh/rh-php72/php.ini;
sed -i 's/max_execution_time = 300/max_execution_time = 1200/g' /etc/opt/rh/rh-php72/php.ini;

set -e 
set -o pipefail
#Imports  
export failConnection=2
DIRECTORIO="/opt/app-root/src/app/spool%/mailer_mail/"

function try()
{
    [[ $- = *e* ]]; SAVED_OPT_E=$?
    set +e
}

function throw()
{
    exit $1
}

function catch()
{
    export ex_code=$?
    (( $SAVED_OPT_E )) && set +e
    return $ex_code
}

function throwErrors()
{
    set -e
}

function ignoreErrors()
{
    set +e
}

#TASK 1.1 Envío de emails
function enviar_emails_poco_a_poco()
{
	try
	(
		if [ $tried -le $total_tried ]; then
			echo $"Enviando emails"
			send=$(find $DIRECTORIO -maxdepth 1 -type f | wc -l)
			if [ "$send" -ne "0" ] ; then
				while
					: ${start=$send}
					#Contar documetos
					/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console swiftmailer:spool:send --message-limit=24 --env=prod &
					send=$(find $DIRECTORIO -maxdepth 1 -type f | wc -l)
					sleep 5
					[ "$send" -ne "0" ]
				do :;  done
			fi
		fi
		return 0

	)
	catch || {
		case $ex_code in $failConnection)
			echo $"Reconectando"
			sleep 3
			tried=$((tried+1));
			enviar_emails_poco_a_poco $tried
			return 0
		;;
		*)
			echo $"Ha ocurrido un error"
			echo $ex_code >> error.log
			exit 1
		;;
		esac
	}
}



#TASK 1: Análisis 
function execute_command()
{
	try
	(
		echo $"Ejecutando script de análisis"
		if [ $tried -le $total_tried ]; then
			$execution_string
			echo $"Fin de ejecucion de análisis"
			return 0
		fi
	)
	catch || {
		case $ex_code in $failConnection)
			tried=$((tried+1));
			sleep 3
			echo $"Reconectando"
			analisis $tried
			return 0
        ;;
		*)
        	echo $"Ha ocurrido un error"
			echo $ex_code >> error.log 
			exit 1
        ;;
    	esac
	}
}

#MNN. Añadimos en elvio de conclusiones
tried=0;
total_tried=2
execution_string=/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console email:emaildoccon:send &
state_task=$(execute_command $tried)
wait;
echo $"$state_task"
if [ -d ${DIRECTORIO} ]; then	
	tried=0
	enviar_emails_poco_a_poco $tried
fi
#FIN MNN
wait;
echo 0;