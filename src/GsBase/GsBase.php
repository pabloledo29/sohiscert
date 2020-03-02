<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\GsBase;

use Symfony\Component\DependencyInjection\Container;

define("GSBASE_SEP", chr(2));
define("GSBASE_LOGIN_END", chr(1).chr(2));
define("GSBASE_MAX_LENGTH", 1024);
define("GSBASE_MAX_ITERACIONES", 100000);
define("GSBASE_LOGIN_CMD", 'p_logon');


class GsBase
{

    private $servidor;
    private $puerto;
    private $user;
    private $pass;
    private $empresa;
    private $aplicacion;
    private $ejercicio;
    private $accion;
    private $ventana;

    private $errno;
    private $errstr;


    protected $gsbase;

    public function __construct($servidor, $puerto, $user, $pass, $empresa, $aplicacion, $ejercicio, $accion, $ventana)
    {

        $this->servidor = $servidor;
        $this->puerto = $puerto;
        $this->user = $user;
        $this->pass = $pass;
        $this->empresa = $empresa;
        $this->aplicacion = $aplicacion;
        $this->ejercicio = $ejercicio;
        $this->accion = $accion;
        $this->ventana = $ventana;
        $this->errno = null;
        $this->errstr = null;

        try {

            $this->gsbase = fsockopen($this->servidor, $this->puerto, $this->errno, $this->errstr);
            if (!$this->gsbase) {
                $this->gsbase_error($this->errstr);
            }
            $this->gsbase_conecta();
            $this->gsbase_login($this->empresa, $this->user, $this->pass, $this->aplicacion, $this->ejercicio, '', '');

        } catch (\Symfony\Component\Debug\Exception\ContextErrorException $e) {

            return $gsbase = null;
        }

    }

    public function gsbase_conecta()
    {
        if (!$this->gsbase) {
            return (false);
        }

        $msg = '';
        while (!(GSBASE_LOGIN_END == substr($msg, -2))) {
            $msg .= fgets($this->gsbase, 2);

        }

        return (true);
    }

    public function gsbase_error($msg)
    {
        echo "ERROR: $msg\n";
    }


    # >gsbase_exec('consulta_xml',$xml,'consulta-xml') (acción = consulta_xml  || ventana = consulta-xml)
    #consulta_xml ||||||| SELECT clientes 0 = 01772 CL_CIF CL_DENO ||||||| consulta-xml
    # Cuando hace login -> p_logon |||| shc,__*web,S0h1scert,gsges_shc,shc |||| consulta-xml
    public function gsbase_exec($comando, $argumentos, $ventana = '', &$salida = '')
    {

        #die("$comando,$argumentos,$ventana,$salida"); # p_logon,shc,__*web,S0h1scert,gsges_shc,shc,,
        //ld('Comando: ' . $comando .' Argumentos: '. $argumentos . ' Ventana: ' . $ventana);

        if ($ventana != '') {
            $comando .= '|'.$ventana;
        }
        $comando = $comando.GSBASE_SEP.$argumentos;

        $hex = dechex(strlen($comando));
        $hex = str_pad($hex, 6, '0', STR_PAD_LEFT);

        $gsbase = $this->gsbase;
        if (is_null($gsbase)) {
            die();
        }
        fputs($gsbase, $hex.$comando);


        $strlen = fgets($this->gsbase, 7);
        $len = hexdec($strlen);

        $iteraciones = 0;
        $response = '';
        $dyn_iteraciones = GSBASE_MAX_ITERACIONES;

        while ($len >= 1 && $iteraciones < GSBASE_MAX_ITERACIONES) {
            if ($len < $dyn_iteraciones) {
                $dyn_iteraciones = $len;
            }
            $str = fgets($this->gsbase, $dyn_iteraciones + 1);
            $response .= $str;
            $len = $len - strlen($str);
            $iteraciones++;
        }

        $salida = $response;

        if ($ventana == '') {
            return (true);
        } else {

            $vector = preg_split('/'.GSBASE_SEP.'/', $salida, -1, PREG_SPLIT_NO_EMPTY);


            if (sizeof($vector) > 1) {
                if ($vector[1] != '') {
                    $this->gsbase_error('EXEC:'.$vector[1]);

                    return (false);
                }
            } else {

                if ($comando != 'p_logon' || $comando != 'p_login') {
                    $this->gsbase_stop();
                }

                return ($vector[0]); #2015: Ok, it Works

            }
        }

    }


    public function gsbase_exec_no_close($comando, $argumentos, $ventana = '', &$salida = '')
    {

        #die("$comando,$argumentos,$ventana,$salida"); # p_logon,shc,__*web,S0h1scert,gsges_shc,shc,,
        //ld('Comando: ' . $comando .' Argumentos: '. $argumentos . ' Ventana: ' . $ventana);

        if ($ventana != '') {
            $comando .= '|'.$ventana;
        }
        $comando = $comando.GSBASE_SEP.$argumentos;

        $hex = dechex(strlen($comando));
        $hex = str_pad($hex, 6, '0', STR_PAD_LEFT);


        fputs($this->gsbase, $hex.$comando);


        $strlen = fgets($this->gsbase, 7);
        $len = hexdec($strlen);

        $iteraciones = 0;
        $response = '';
        $dyn_iteraciones = GSBASE_MAX_ITERACIONES;

        while ($len >= 1 && $iteraciones < GSBASE_MAX_ITERACIONES) {
            if ($len < $dyn_iteraciones) {
                $dyn_iteraciones = $len;
            }
            $str = fgets($this->gsbase, $dyn_iteraciones + 1);
            $response .= $str;
            $len = $len - strlen($str);
            $iteraciones++;
        }

        $salida = $response;
        
        if ($ventana == '') {
            return (true);
        } else {

            $vector = preg_split('/'.GSBASE_SEP.'/', $salida, -1, PREG_SPLIT_NO_EMPTY);


            if (sizeof($vector) > 1) {
                if ($vector[1] != '') {
                    $this->gsbase_error('EXEC:'.$vector[1]);

                    return (false);
                }
            } else {

                /*
                if($comando!='p_logon' || $comando!='p_login'){
                    $this->gsbase_stop();
                }
                */
                return ($vector[0]); #2015: Ok, it Works

            }
        }

    }


    public function gsbase_login(
        $empresa,
        $user,
        $pass,
        $aplicacion,
        $ejercicio,
        $clave_aplicacion = '',
        $clave_ejercicio = ''
    ) {

        #die("$empresa,$user,$pass,$aplicacion,$ejercicio"); #shc,__*web,S0h1scert,gsges_shc,shc
        if (!$this->gsbase_exec(
            GSBASE_LOGIN_CMD,
            "$empresa,$user,$pass,$aplicacion,$ejercicio",
            '',
            $out
        )
        ) {   # SI login -> 2015: Ok, it Works 	consulta-xml
            $this->gsbase_error('p_login');

            return (false);
        }
        if ($out == '') {
            return (false);
        }

        //$vector=split(GSBASE_SEP,$out); #Array ( [0] => Ok [1] => ) Ok
        $vector = explode(GSBASE_SEP, $out); #Array ( [0] => Ok [1] => ) Ok

        if (sizeof($vector) > 1) {
            if ($vector[1] != '') {
                $this->gsbase_error('EXEC:'.$vector[1]);

                return (false);
            }
        } else {

            return ($vector[0]); #2015: Ok, it Works

        }

        if (sizeof($vector) > 1) {
            $login_ok = true;

        } else {
            $login_ok = false;
            $this->gsbase_error('p_login:'.$vector[1]);
        }

        return ($login_ok);
    }

    public function gsbase_stop()
    {
        fclose($this->gsbase);
    }


    public function getGsbase()
    {
        return $this->gsbase;
    }

}

