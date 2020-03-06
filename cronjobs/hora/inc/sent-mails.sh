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
					/opt/rh/rh-php72/root/usr/bin/php /opt/app-root/src/bin/console swiftmailer:spool:send --message-limit=24 --env=prod
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
