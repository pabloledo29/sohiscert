<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Ftp;


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
    //const FTP_BILLING = "/test";
    const FTP_BILLING = "/DEPARTAMENTO DE CONTABILIDAD/FACTURAS 2016"; # Directorio anterior: "/RAIZ/SOHISCERT-GERENCIA/DEPARTAMENTO DE CONTABILIDAD/FACTURAS 2016/";
    const FTP_DOC = "/Documentos/Documentos/";
    const FTP_GENERAL = "/Documentos/General/";
    //const FTP_CERTIFICADOS = "/test";
    //const FTP_CERTIFICADOS = "/DEPARTAMENTO ADMINISTRACION/ARCHIVO DIGITAL";
    const FTP_CERTIFICADOS = "/DEPARTAMENTO CERTIFICACION/1 CERTIFICADOS/3 FIRMADOS";
    //const FTP_ANALISIS = "/test/acceso directo";
    const FTP_ANALISIS = "/DEPARTAMENTO DE CONTROL/RESULTADOS ANALÍTICOS";
    const FTP_CARTAS = "/DEPARTAMENTO CERTIFICACION/DECISIÓN DE CERTIFICACIÓN/COMUNICACION DE LA COMISION DE CERTIFICACION";
    //const FTP_CARTAS = "/test";
    const FTP_UPLOADS = "/subidas";
    // MNN Nueva ruta para conclusiones
    //const FTP_CONCLUSIONES = "/test/acceso directo";
    const FTP_CONCLUSIONES = "/DEPARTAMENTO DE CONTROL/03. CONCLUSIONES/AREA PRIVADA";  
    

    protected $ftp;
    protected $nopConversion;

    /**
     * Ftp constructor.
     * @param array $nopConversion Array von valores parametrizados para conversión de NOPs antiguos.
     */
    public function __construct($nop_conversion, $ftp_server, $ftp_user_name, $ftp_user_pass)
    { 
        $nop_conversion = ['RP'=> 'PAE', 'RI' => 'IAE','RC' => 'CAE', 'RG' => 'GAE', 'RF' => 'FAE', 'RT'=> 'TAE', 'RS' => 'SAE', 'RN' => 'NAE' ];
        
       
        $this->nopConversion = $nop_conversion;
         # Datos Conexión FTP para poder Obtener Fecha Modificación de los Archivos
        $this->ftp_server = $ftp_server;
        $this->ftp_user_name = $ftp_user_name;
        $this->ftp_user_pass = $ftp_user_pass;
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
    public function retrieveFilesFromClients($conn_id ,array $clients, $query)
    {
        $path = $this->determineQueryType($query);
        $clientList = [];
        foreach ($clients as $client) {
            if (is_dir("ftp://$this->ftp_user_name:$this->ftp_user_password@$this->ftp_server/".$path . $client['codigo'])) {
                array_push($clientList, $client['codigo']);
            }
        }

        $operatorList = [];
        foreach ($clientList as $dir) {
            $tempList = ftp_nlist($conn_id,$path . $dir);
            foreach ($tempList as $item) {
                $fileList = ftp_nlist($conn_id, $item);
                
                $list = [];
                foreach ($fileList as $file) {
                    $list[substr(strrchr($file, '/'), 1)] = $file;
                }
                $operatorList[substr(strrchr($item, '/'), 1)] = $list;
            }
        }
        ftp_close($conn_id);
        return $operatorList;
    }

    /**
     * Genera dinámicamente la lista de documentos generales
     *
     * @param $query
     * @return array
     */
    public function retrieveGeneralDocuments($conn_id,$query)
    {
        $ftpWrapper = $this->ftpWrapper;
        $path = $this->determineQueryType($query);


        $fileList = ftp_nlist($conn_id, $path);
        $list = [];
        foreach ($fileList as $file) {
            $list[substr(strrchr($file, '/'), 1)] = $file;
        }
        ftp_close($conn_id);
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
    public function validPath($conn_id, $path, $query)
    {
        $valid = false;
        $dir = $this->determineQueryType($query);


        $fileList = ftp_nlist($conn_id, $dir);

        if (in_array($path, $fileList)) {
            $valid = true;
        }
        ftp_close($conn_id);
        
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
        # Establecemos Conexión 
        $conn_id = ftp_connect($this->ftp_server); 

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $this->ftp_user_name, $this->ftp_user_pass); 
        
        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
            /*echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $this->ftp_server por el usuario $this->ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } /*else {
            echo "\n Conexión a $this->ftp_server realizada con éxito, por el usuario " . $this->ftp_user_name . " \n";
        }*/

        ftp_pasv($conn_id, true);
        # Inciamos Sesión
        /** @var array|false $listado */
        $listado = ftp_nlist($conn_id, $path);
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
            $nopcarta =$nop;
            $nop = 'F155-03-' . $nop . '-';
           
            #$nop = 'F155-02-' . $nop . '-';
        } elseif ($query === 'certificados') {
            $nop .= '-';
        } elseif ($query === 'analisis'){
            $nop = $nop;
        // MNN. añadimos la nueva nomemclatura 
        } elseif ($query === "conclusiones"){  
            $nop = $nop; 
            
            
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
//                                          //        if (strpos($listado[$i], $nop2) or strpos($listado[$i], $nop3)) {
             
                if (strpos($listado[$i], $nop)) {
                //if (strpos($listado[$i], strtoupper($nop))) { 
                    $porciones = explode("-", $listado[$i]);
                    $cadena=substr(strrchr($porciones[0], "/"), 1);               
                  
                    if ($cadena==='F27' or $cadena==='F194'){
                        #$certList[substr(strrchr($listado[$i], '/'), 1)] = $listado[$i];
                      
                        # Obtenemos la Fecha de Modificación del Certificado
                        $docftp = ftp_mdtm($conn_id, $listado[$i]);
               
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
                # B13053467
                # CM17CoC
               
                $filename = explode("-", $listado[$i]);   
              
                $filename_aux = substr(strrchr($filename[0], "/"),1);
                $strlen_filename = strlen($filename_aux);
                if($strlen_filename <=4 ){                    
                    if(count($filename)>2){
                        if(!is_null($filename[2])){
                            $filename_aux=$filename[2];
                            if ($query === 'cartas')
                            {
                               
                                $nop_aux=$nopcarta;
                            }
                        }
                    }
                } else if($strlen_filename==12 && is_numeric($filename_aux)){
                    if(count($filename)>1){
                        if(!is_null($filename[1])){
                            $filename_aux=$filename[1];
                            $filename_aux= str_replace('.pdf','',$filename_aux);
                        }
                    }
                }
                
                if ($query !== 'cartas'){
                    $nop_aux= str_replace('-','',$nop);
                    
                }else{
                    $nop_aux= str_replace('-','',$nopcarta);
                    
                }
                $pos= -1;
                if(false === strpos($nop,'SHC') && strpos($listado[$i], 'SHC')===false || $query=="cartas"){

                    $nop_aux= str_replace('AE','', $nop_aux);
                    $filename_aux= str_replace('.pdf','',$filename_aux);
                    
                    if ($query === 'facturas') {
                        if(strpos($filename_aux, ".") !== false || (strlen($filename_aux)==7) ){
                            //if(strpos($filename_aux, ".") !== false){
                                if(isset($filename[1])){
                                    $filename_aux=$filename[1];
                                    
                                }
                            }
                    } else {
                        if(strpos($filename_aux, ".") !== false || (strlen($filename_aux)==7 && is_numeric($filename_aux)) ){
                            //if(strpos($filename_aux, ".") !== false){
                                if(isset($filename[1])){
                                    $filename_aux=$filename[1];
                                    
                                }
                            }
                    }
                   
                    $filename_aux = str_replace('AE','', $filename_aux);
                    $filename_aux= str_replace('.pdf','',$filename_aux);
           
                    //$pos = strcmp(strtoupper($filename_aux),strtoupper($nop_aux));
                    $pos = strcmp($filename_aux,$nop_aux);
                    
                }else{ 
                    
                    if(strpos($listado[$i],substr($nop,strpos($nop,'SHC'), strlen($nop)-1))!==false ){
                        $pos = 0;
                    }
                }

             
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
            
                if ($pos==0){
                  /*if($query=="certificados" && false===strpos($listado[$i], 'F157')){ //NUEVA NORMATIVA
                        unset($listado[$i]);
                        continue;
                    
                    }else if($query=="cartas" && false===strpos($listado[$i], 'F155')){ //NUEVA NORMATIVA
                        unset($listado[$i]);
                        continue;
                    }else if($query=="analisis" && false===strpos($listado[$i], 'F156')){ //NUEVA NORMATIVA
                        unset($listado[$i]);                    
                        continue;
                    }*/
                        #$certList[substr(strrchr($listado[$i], '/'), 1)] = $listado[$i];
                       
                        # Obtenemos la Fecha de Modificación del Certificado
                        $docftp = ftp_mdtm($conn_id, $listado[$i]);
                       
                        $fechadoc = date("Y-m-d H:i:s", $docftp);
                        
                        # Almacenamos los Valores obtenidos en Dos Arrays
                        $certOrig[$listado[$i]] = $fechadoc;
                        $certFmod[$listado[$i]] = $fechadoc;
                    
                } 
            }
        }
    ftp_close($conn_id);
        
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
                        $certList[substr(strrchr($rutadoc, '/'), 1)] = ["directory"=>$rutadoc, "anFec"=>$fmod];                    
                    } 
                }
            }
        }
        if($query==='cartas'){
            if (!empty($certList)){
                $listAux = [];
               
                foreach($certList as $line){
                    $listAux[] = str_replace('AE', '', $line);
                }
                sort($listAux,SORT_STRING);
                
                $cont_i = 0;
                $encontrado = false;
                   $values_cerList = array_values($certList); 

                    $listAux_count = count($listAux); 
                    while($cont_i < $listAux_count && !$encontrado){
                            if(strcmp(str_replace('AE', '', $values_cerList[$cont_i]), 
                                array_values($listAux)[count($listAux)-1])===0){
                                $position=$cont_i;
                                $encontrado = true;
                            }else{
                                $cont_i++;
                            }
                        }
                    
                
                //TODO : CERTLIST estará vacío
                $certList[]= array_values($certList)[$cont_i];
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
           // $pattern = '/^( [F202]-' . $current . '|' . $previous . ')\w+' . $nop . '.[a-z]{3}$/';
            //MNN. Cquitamos la comparacion de arrays. 13/04/2021
            /*$pattern = '/^('. $current . '|' . $previous . ')\w+' . $nop . '.[a-z]{3}$/';
         

            $temp = $this->pregGrepKeys($pattern, $certList);

            $certList = $temp;*/
            /* FIN MNN */
            $certList=$certList;
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
                    $optimizacion_string1 =substr($keycert, 0,$posg );
                    $listnummin_value = $listnummin[$valuecert];
                    $nbcert = $optimizacion_string1 . "-0" . $listnummin_value;
                    $nbcert2 = $optimizacion_string1 . "-" . $listnummin_value;
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
        //dump(array_keys($input));
        //dump($pattern);
        //dump(preg_grep($pattern, array_keys($input), $flags));
        //dump(array_flip(preg_grep($pattern, array_keys($input), $flags)));
    
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