<?php

require 'src/smsup/smsuplib.php';

$s = new smsup\smsuplib('TU_ID_USUARIO', 'TU_CLAVE_SECRETA');
$resul = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['ord']=='envio') {
        $texto = $_POST['texto'];
        $numeros = array($_POST['tel']);
        $referencia = $_POST['ref'];
        $remitente = (array_key_exists('remit', $_POST))?$_POST['remit']:'';
        $codificacion = $_POST['cod'];
        $resul = json_encode($s->NuevoSMS($texto, $numeros, '', $referencia, $remitente, $codificacion), JSON_PRETTY_PRINT);
    } elseif ($_POST['ord']=='eliminar') {
        $resul = json_encode($s->EliminarSMS($_POST['id']), JSON_PRETTY_PRINT);
    } elseif ($_POST['ord']=='estado') {
        $resul = json_encode($s->EstadoSMS($_POST['id']), JSON_PRETTY_PRINT);
    } elseif ($_POST['ord']=='creditos') {
        $resul = json_encode($s->CreditosDisponibles(), JSON_PRETTY_PRINT);
    } elseif ($_POST['ord']=='resultado') {
        $resul = json_encode($s->ResultadoPeticion($_POST['ref']), JSON_PRETTY_PRINT);
    }

    echo '<pre>'.$resul.'</pre>';
}

?>


<!DOCTYPE html>
<html lang="es">
    <head>
    </head>
    <body>
        <form method="POST">
            <h3>Enviar sms</h3>
            <input type="hidden" name="ord" value="envio" />
            Texto:<br>
            <textarea maxlenght="160" name="texto" cols="50" rows="10"></textarea>
            <br>Telefono:<br>
            <input type="text" name="tel" />
            <br>Referencia:<br>
            <input type="text" name="ref" />
            <br>Remitente:<br>
            <input type="text" name="remit" />
            <br>Codificacion:<br>
            <select name="cod">
                <option value="<?php echo smsup\smsuplib::GSM ?>">Gsm</option>
                <option value="<?php echo smsup\smsuplib::UNICODE ?>">Unicode</option>
                <option value="<?php echo smsup\smsuplib::AUTO ?>">Auto</option>
            </select>
            <br>
            <input type="submit" value="Enviar sms" />
        </form>
        <br>
        <form method="POST">
            <h3>Estado sms</h3>
            <input type="hidden" name="ord" value="estado" />
            Id sms obtenido del envio:<br>
            <input type="text" name="id" />
            <br>
            <input type="submit" value="Ver estado" />
        </form>
        <br>
        <form method="POST">
            <h3>Eliminar sms</h3>
            <input type="hidden" name="ord" value="eliminar" />
            Id sms obtenido del envio:<br>
            <input type="text" name="id" />
            <br>
            <input type="submit" value="Eliminar sms" />
        </form>
        <br>
        <form method="POST">
            <h3>Resultado envio anterior</h3>
            <input type="hidden" name="ord" value="resultado" />
            Referencia de la peticion:<br>
            <input type="text" name="ref" />
            <br>
            <input type="submit" value="Ver resultado" />
        </form>
        <br>
        <form method="POST">
            <h3>Creditos disponibles</h3>
            <input type="hidden" name="ord" value="creditos" />
            <input type="submit" value="Ver creditos" />
        </form>
    </body>
</html>