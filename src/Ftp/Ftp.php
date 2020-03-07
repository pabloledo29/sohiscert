<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Ftp;


use Symfony\Component\Finder\Finder;

/**
 * Class Ftp
 *
 * Servicio FTP adhoc para el uso del servidor FTP de Sohiscert por parte de los controladores de Symfony2
 *
 * @package App\Ftp
 */
class Ftp
{
    /**
     * Rutas de documentos en Servidor FTP Sohiscert.
     */
    const FTP_BILLING = "/facturasintranet"; # Directorio anterior: "/RAIZ/SOHISCERT-GERENCIA/DEPARTAMENTO DE CONTABILIDAD/FACTURAS 2016/";
    const FTP_DOC = "/Documentos/Documentos/";
    const FTP_GENERAL = "/Documentos/General/";
    const FTP_CERTIFICADOS = "/TEST";
    const FTP_ANALISIS = "/SITIO1";
    const FTP_CARTAS = "/SITIO3";
    const FTP_UPLOADS = "/";
    // MNN Nueva ruta para conclusiones
    const FTP_CONCLUSIONES = "/sitio4CON"; 
    //

    protected $ftp;
    protected $nopConversion;

    /**
     * Ftp constructor.
     * @param \Finder $ftp
     * @param array $nopConversion Array von valores parametrizados para conversión de NOPs antiguos.
     */
    public function __construct()
    { 
        $nop_conversion = ['RP'=> 'PAE', 'RI' => 'IAE','RC' => 'CAE', 'RG' => 'GAE', 'RF' => 'FAE', 'RT'=> 'TAE', 'RS' => 'SAE', 'RN' => 'NAE' ];
        
        $this->finder = new Finder();
        $this->nopConversion = $nop_conversion;
         # Datos Conexión FTP para poder Obtener Fecha Modificación de los Archivos
        $this->ftp_server = 'sohiscert3.ddns.cyberoam.com';
        $this->ftp_user_name = 'userftp1';
        $this->ftp_user_pass = 'AtlIntTec.12';
    }

    /**
     * Función para asignació de ruta por tipo de documento.
     *
     * Deteermina el tipo de query del que se trata,
     * recabando de los parámetros de app el directorio que le corresponde.
     * @param $query
     * @return null|string
     */
    private function determineQueryType($query)
    {
        switch ($query) {
            case $query == "facturas":
                return Ftp::FTP_BILLING;
            case $query == "doc":
                return Ftp::FTP_DOC;
            case $query == 'general':
                return Ftp::FTP_GENERAL;
            case $query == 'analisis':
                return Ftp::FTP_ANALISIS;
            case $query == 'certificados':
                return Ftp::FTP_CERTIFICADOS;
            case $query == 'cartas':
                return Ftp::FTP_CARTAS;
            // MNN Añadimos la nueva Query     
            case $query == 'conclusiones':
                return Ftp::FTP_CONCLUSIONES;
            // FIN MNN    
            default:
                return null;
        }
    }

    /**
     * Servicio que proporcionado un listado de documentos por cliente.
     *
     * Dado un array con clientes genera un listado por operador de los archivos contenidos en los directorios
     * correspondientes del FTP en caso de existir
     *
     * @param array $clients
     * @param $query
     * @return array
     */
    public function retrieveFilesFromClients(array $clients, $query)
    {
        $path = "/".$this->determineQueryType($query);
        $clientList = [];

        foreach ($clients as $client) {
            $this->finder->files()->in("ftp://$this->ftp_user_name:$this->ftp_user_pass@$this->ftp_server"."$path")->name($client['codigo']);
           
            if ($this->finder->hasResults()) {
                array_push($clientList, $client['codigo']);
            }
        }

        $operatorList = [];
        foreach ($clientList as $dir) {
            $tempList = $this->finder->size($path . $dir);
            foreach ($tempList as $item) {
                $fileList = $this->finder->size($item);
                $list = [];
                foreach ($fileList as $file) {
                    $list[substr(strrchr($file, '/'), 1)] = $file;
                }
                $operatorList[substr(strrchr($item, '/'), 1)] = $list;
            }
        }

        return $operatorList;
    }

    /**
     * Genera dinámicamente la lista de documentos generales
     *
     * @param $query
     * @return array
     */
    public function retrieveGeneralDocuments($query)
    {
        $path = $this->determineQueryType($query);


        $fileList = $this->finder->files()->in("ftp://$this->ftp_user_name:$this->ftp_user_pass@$this->ftp_server"."$path")->name('*.pdf');
        $list = [];
        foreach ($fileList as $file) {
            $list[substr(strrchr($file, '/'), 1)] = $file;
        }

        return $list;
    }

    /**
     * función validadora de acceso a directorio por credenciales.
     *
     * Comprueba que lo que se solicita está en el directorio de archivos generales
     * @param $path
     * @param $query
     * @return bool
     */
    public function validPath($path, $query)
    {
        $valid = false;
        $dir = $this->determineQueryType($query);

        $fileList = $this->finder->files()->in("ftp://$this->ftp_user_name:$this->ftp_user_pass@$this->ftp_server"."$dir")->name('*.pdf');
        if (in_array($path, $fileList)) {
            $valid = true;
        }

        return $valid;
    }

    /**
     * Servicio que proporcionado un listado de documentos por Operador
     *
     * Dado un NOP ggit aenera un listado de los archivos contenidos en los directorios
     * correspondientes del FTP
     *
     * Al FTP bajo IIS ha de conectarse en modo pasv = true
     *
     * @param string $nop El NOP del Operador
     * @param string $query El tipo de documento que se solicita al servidor FTP
     * @return array
     */
    public function retrieveDocListFromFtpServer($nop, $query)
    {
        
        //$ftpWrapper = $this->ftpWrapper;
        // Passive mode.
        //$ftpWrapper->pasv(true);

        # Arrays para Certificados
        $certList = [];
        $certOrig = [];
        $certFmod = [];

       
        $path = $this->determineQueryType($query);
        # Establecemos Conexión 
        //$conn_id = ftp_connect($ftp_server); 
        $this->finder->in("ftp://$this->ftp_user_name:$this->ftp_user_pass@$this->ftp_server"."$path")->name("AN1363F-21.pdf")->depth('== 0');;
        var_dump($this->finder);
        var_dump($this->finder->files());
        exit;
        if ($this->finder->hasResults()) {
            
        # Inciamos Sesión
        //$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        
       
        /** @var array|false $listado */
        $listado = $this->finder->files();
        
        $numarch = count($listado);
        
        
        if (!$listado) {
            return $certList;
        }
    
        
        if (strpos($nop, '/')) {
            if ($query === "facturas") {
                $nop = str_replace('/', '-', $nop);
            } else {
                $nop = $this->nopConversion($nop);
            }

        }

   
        /** Para limitar el filtrado */
        if ($query === 'cartas') {
            $nop = 'F155-03-' . $nop . '-';
            #$nop = 'F155-02-' . $nop . '-';
        } elseif ($query === 'certificados') {
            $nop .= '-';
        } elseif ($query === 'analisis'){
            $nop = $nop;
        // MNN. añadimos la nueva nomemclatura 
        } elseif ($query === 'conclusiones'){  
            //Comprobamos si tiene texto o no. lo contamos
            $nop = $nop; 
            //$nop3 = 'F194-' . $nop . '-';  
            #$nop = $nop;  
        // FIN       
        } else {
            $nop = '-' . $nop;
        }
        

        /*
        foreach ($listado as $doc) {
            if (strpos($doc, $nop)) {
                $certList[substr(strrchr($doc, '/'), 1)] = $doc;
            }
        }
        */
        # Recorremos el Listado de Certificados FTP
        for ($i=0; $i <$numarch ; $i++) {

            # Comprobamos SI el conclusion contiene el Nombre del Operador
            // MNN: Para mostrar conclusiones. La opcion F27-03 y F194-03
            if ($query==='conclusiones'){   
//          if (strpos($listado[$i], $nop2) or strpos($listado[$i], $nop3)) {
                if (strpos($listado[$i], $nop)) { 
                    $porciones = explode("-", $listado[$i]);
                    $cadena=substr(strrchr($porciones[0], "/"), 1);
                    
                    if ($cadena==='F27' or $cadena==='F194'){
                        #$certList[substr(strrchr($listado[$i], '/'), 1)] = $listado[$i];
                      
                        # Obtenemos la Fecha de Modificación del Certificado
                        $docftp = $this->finder->date('since today');
               
                        $fechadoc = date("Y-m-d H:i:s", $docftp);
                
                        # Almacenamos los Valores obtenidos en Dos Arrays
                        $certOrig[$listado[$i]] = $fechadoc;
                        $certFmod[$listado[$i]] = $fechadoc;
                    } 
                }

            }else{
            //FIN MNN
            $lista_nop_alternativos = ['F','P','C','I', 'C','G','T', 'S','N'];
            # Comprobamos SI el Certificado contiene el Nombre del Operador
            $pos =strpos($listado[$i],$nop,0);
            
            $filename =explode('-',substr ($listado[$i],(int)$pos))[0];
            /*
            * CASO 1
            * %LLNNNL%
            * LLNNNLLL
            * CASO 2
            * %LLNNNLLL%
            * LLNNNL
            * CASO 3
            * %LLNNNLLL%
            * LLNNNLLL
            * o
            * %LLNNNL%
            * LLNNNL
            */
            if ($pos!==false || (
                in_array(substr($filename,strlen($filename)-3,1),$lista_nop_alternativos)
                 && (!is_numeric(substr($filename,strlen($filename)-3,1))) &&
                    substr($filename,strlen($filename)-3,1) == substr($nop,strlen($nop)-2,1))||
                    (   in_array(substr($nop,strlen($nop)-4,1),$lista_nop_alternativos)
                         && !is_numeric(substr($nop,strlen($nop)-4,1)) &&
                            substr($filename,strlen($filename)-1,1) == substr($nop,strlen($nop)-4,1))){
                
                $nop_aux= str_replace('AE','', $nop);
                $nop_aux= substr($nop_aux, 0,-2);
                
                if(strpos($listado[$i],$nop_aux)!==false){
                
                    #$certList[substr(strrchr($listado[$i], '/'), 1)] = $listado[$i];
                    
                    # Obtenemos la Fecha de Modificación del Certificado
                    $docftp = $this->finder->date('since today');
                   
                    $fechadoc = date("Y-m-d H:i:s", $docftp);
                    
                    # Almacenamos los Valores obtenidos en Dos Arrays
                    $certOrig[$listado[$i]] = $fechadoc;
                    $certFmod[$listado[$i]] = $fechadoc;
                }
            } 
        }
    }
        # Ordenamos el Array certFmod ascendentemente por el Valor de la Fecha de Modificación
        
        # Recorremos el Array certFmod Ordenado
        foreach ($certFmod as $fmod) {
            
            # Recorremos el Array certOrig
            foreach ($certOrig as $rutadoc => $valorfmod) {
                
                # Comprobamos SI las Fechas de Modificación de ambos arrays coinciden
                if (strcmp($valorfmod, $fmod) == 0 ) {
                    
                    # Creamos un Nuevo Array Asociativo Ordenado por
                    # la Fecha de Modificación de los Certificados 
                    if($query!=='analisis'){
                        $certList[substr(strrchr($rutadoc, '/'), 1)] = $rutadoc;
                    }else{
                        $certList[substr(strrchr($rutadoc, '/'), 1)] = [directory=>$rutadoc, anFec=>$fmod];                    
                    } 
                }
            }
        }
        
        #dump($certList);
        #krsort($certList);
        #krsort($fechadoc);
        #rsort($fechadoc);
       
        #dump($fechadoc);
       
        /** Confección de la expresión regular para filtrar facturas y/o certificados*/
        if ($query === 'facturas') {

            $current = date("y");
            $previous = date("y", strtotime("-1 year"));
            /* comienza con dos digitos para año en vigor o anterior y termina con - NOP . extension de 3 letras */
            $pattern = '/^(' . $current . '|' . $previous . ')\w+' . $nop . '.[a-z]{3}$/';

            $temp = $this->pregGrepKeys($pattern, $certList);
            $certList = $temp;
        } elseif ($query === 'certificados') {
           
            /** Regex para Certificados
             *
             * Listar Certificados, Nuevos y Antiguos, de un mismo Operador
             */
            if (!empty($certList)) {
                # code...
            
                #$lista = array_keys($certList);
                
                #dump($lista);
                /*$certList= ["F202-03-AR411PAE-01 ingles.pdf" =>"/SITIO2/F202-03-AR411PAE-01 ingles.pdf"
                    , "F201-02-AR411PAE-01 (2).pdf" => "/SITIO2/F201-02-AR411PAE-01 (2).pdf"
                    , "F202-03-AR411PAE-01 FRANCES.pdf" => "/SITIO2/F202-03-AR411PAE-01 FRANCES.pdf"
                    , "F202-03-AR411PAE-01 INGLES.pdf" => "/SITIO2/F202-03-AR411PAE-01 INGLES.pdf"
                    , "F202-03-AR411PAE-02 TRADUCCION INGLES.pdf" => "/SITIO2/F202-03-AR411PAE-02 TRADUCCION INGLES.pdf"
                    , "F202-03-AR411PAfE-3.pdf" => "/SITIO2/F202-03-AR411PAfE-3.pdf"
                    ,"F202-03-AR411PAE-08.3.pdf" => "/SITIO2/F202-03-AR411PAE-08.3.pdf"
                    ,"F202-03-AR411PAE-08.3 Ingles.pdf" => "/SITIO2/F202-03-AR411PAE-08.3 Ingles.pdf"
                    ,"F202-01-AR411PAE-8.7.pdf" => "/SITIO2/F202-01-AR411PAE-8.7.pdf"
                    ,"F201-02-AR411PAE-02.pdf" => "/SITIO2/F201-02-AR411PAE-02.pdf"];*/
                #dump($certList);
                
                # Recorremos los Archivos del Directorio Certificados
                foreach ($certList as $key => $value) {
                   # dump($certList);
                    # Descartamos los Archivos que contengan el texto 'Untitled'
                    if (strpos($value, 'Untitled') === false) {
                       
  
                        # Almacenamos los archivos en un nuevo array
                        $listcert[$key] = $value;

                        # Localizamos la última posicion del guión
                        $posg = strripos($value, '-');

                        #dump($numFile);
                        # Almacenamos en un nuevo array el nombre del archivo resultante
                        
                        $nvlista[$key] = substr($value, 0, $posg);
                    }
                }
                #dump($nvlista);
               # dump($nvlista);
                
                # Almacenamos en nuevo array los nombres de los archivos únicos, no repetitivos
                $cert = array_unique($nvlista);
                #dump($cert);
                # Inicializamos una lista de minimos
                foreach ($cert as $key){
                    $listnummin[$key] = -1;
                }
               
                # Buscar los maximos de los ficheros certificados
                foreach ($certList as $key => $value) {
                        $subtring =strrchr($value, '-');
                        $posg = strripos($value, '-');
                        $key_elem = substr($value, 0, $posg);
                        $subtring = substr($subtring,1,3);
                        $numFile = trim($subtring, ".");
                        $numFile = trim($numFile, " ");
                        if($listnummin[$key_elem] < (int) $numFile){
                            $listnummin[$key_elem]=(int)$numFile;
                        } 
                       
                        
                } 
                #dump($listnummin);
                #dump($listnummin);
                # Recorremos el Nuevo Array con los Nombres de los Archivos Únicos
                foreach ($cert as $keycert => $valuecert) {
                    
                    # Quitamos la extensión '.pdf' a las Claves del array 
                    #$nbcert = trim($keycert, '.pdf');
                    
                    # Localizamos la posición del guión
                    $posg = strripos($keycert, '-');
                    
                    #dump($posg);
                    #exit;
                    $nbcert = substr($keycert, 0,$posg ) . "-0" . $listnummin[$valuecert];
                    $nbcert2 = substr($keycert, 0,$posg ) . "-" . $listnummin[$valuecert];
                    #dump($nbcert);
                    # Recorremos el nuevo array resultante sin los archivos que contienen 'Untitled'
                    foreach ($listcert as $keylistcert => $valuelistcert) {
                        
                        # Obtenemos SÓLO el Nombre del Documento SIN la Extensión del Archivo ni la Ruta
                        $valorlistcert = trim($valuelistcert, '.pdf');
                        $valorlistcert = substr(strrchr($valorlistcert, '/'), 1);
                       # dump($valorlistcert);
                        #dump($valuelistcert , $nbcert);
                        # Comparamos el valor de los certificados filtrados con el valor de las claves únicas filtradas 
                        if (strpos($valorlistcert, $nbcert) !== false || strpos($valorlistcert, $nbcert2) !== false) {
                        #if (strcmp($valuelistcert, $nbcert) == 0) {

                            # Almacenamos en un nuevo array las claves y los valores correspondientes
                            $allcert[$keylistcert] = $valuelistcert; 
                        }
                    }
                }
               
               
                # Devolvemos el Listado de los Certificados
                return $allcert;
            }else{
                
                return $certList;
            }
        }
       
            # Devolvemos el Listado de las Facturas
            return $certList;
            // ...
        }
    }

    /**
     * Dada una expresión regular y un array asociativo.
     *
     * Devuelve un nuevo array asociativo con los clave valor que pasan el match de la regex
     *
     * @param $pattern
     * @param array $input
     * @param int $flags
     * @return array
     */
    private function pregGrepKeys($pattern, array $input, $flags = 0)
    {
        #dump(array_keys($input));
        #dump($pattern);
        #dump(preg_grep($pattern, array_keys($input), $flags));
        #dump(array_flip(preg_grep($pattern, array_keys($input), $flags)));
        return array_intersect_key($input, array_flip(preg_grep($pattern, array_keys($input), $flags)));
    }

    /**
     * Conversor de NOPs antiguos al formato en vigor.
     *
     * Dado un string NOP con formato antiguo lo transforma según las reglas dadas por Sohiscert
     * Así de VA-6/01-RP => troceado por '-' e ignorado el /xx se convierte Rx y se reconstruye
     * Dando así VA6PAE
     * parameter: nop_conversion
     *
     * @param string $nop El NOP del Operador
     * @return string
     */
    private function nopConversion($nop)
    {
        $newNop = '';
        $temp = explode('-', $nop);
        $reg = $temp[2];
        if (array_key_exists($reg, $this->nopConversion)) {
            $newNop .= $temp[0] . substr($temp[1], 0, strpos($temp[1], '/')) . $this->nopConversion[$reg];
        }

        return $newNop;
    }
}