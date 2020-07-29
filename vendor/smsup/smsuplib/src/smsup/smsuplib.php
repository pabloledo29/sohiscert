<?php

namespace smsup;

class smsuplib
{
    const GSM = 'gsm';
    const UNICODE = 'uni';
    const AUTO = 'aut';

    const HOST = 'https://www.smsup.es';
    const URL_SMS = '/api/sms/';
    const URL_CREDITOS = '/api/creditos/';
    const URL_ENVIO = '/api/peticion/';

    protected $usuario = '';
    protected $clave = '';

    public function __construct($usuario, $clave)
    {
        $this->usuario = $usuario;
        $this->clave = $clave;
    }

    public function NuevoSMS($texto, array $numeros, $fechaenvio = null, $referencia = '', $remitente = '', $codificacion = null)
    {
        $fechac = (($fechaenvio===null || !is_object($fechaenvio))?'NOW':$fechaenvio->format('c'));
        $post = ['texto'=>$texto, 'fecha' => $fechac,
            'telefonos' => $numeros, 'referencia'=>$referencia, 'remitente'=> $remitente];
        if ($codificacion!=null && in_array($codificacion, [self::GSM, self::UNICODE, self::AUTO])) {
            $post['codificacion'] = $codificacion;
        }
        $post = json_encode($post);
        $cabeceras = $this->generarCabeceras('POST', self::URL_SMS, $post);
        return $this->enviar(self::URL_SMS, 'POST', $cabeceras, $post);
    }

    public function EliminarSMS($idsms)
    {
        $url = self::URL_SMS.$idsms.'/';
        $cabeceras = $this->generarCabeceras('DELETE', $url);
        return $this->enviar($url, 'DELETE', $cabeceras);
    }

    public function EstadoSMS($idsms)
    {
        $url = self::URL_SMS.$idsms.'/';
        $cabeceras = $this->generarCabeceras('GET', $url);
        return $this->enviar($url, 'GET', $cabeceras);
    }

    public function CreditosDisponibles()
    {
        $cabeceras = $this->generarCabeceras('GET', self::URL_CREDITOS);
        return $this->enviar(self::URL_CREDITOS, 'GET', $cabeceras);
    }

    public function ResultadoPeticion($referencia)
    {
        $url = self::URL_ENVIO.$referencia.'/';
        $cabeceras = $this->generarCabeceras('GET', $url);
        return $this->enviar($url, 'GET', $cabeceras);
    }



    protected function generarCabeceras($verbo, $url, $post = '')
    {
        $cabeceras = array();
        $smsdate = date('c');
        $cabeceras[] = 'Sms-Date: '.$smsdate;
        $text = $verbo.$url.$smsdate.$post;
        $firma = hash_hmac('sha1', $text, $this->clave);
        $cabeceras[] = 'Firma: '.$this->usuario.':'.$firma;
        return $cabeceras;
    }

    protected function enviar($url, $method, $cabeceras, $body = null)
    {
        $url = self::HOST.$url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method=='DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_values($cabeceras));
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        } else {
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        curl_close($ch);
        if ($data) {
            $result = json_decode($data, true);
            if ($result !== false) {
                return array('httpcode'=>$statusCode, 'resultado' => $result);
            }
        }

        return false;
    }
}
