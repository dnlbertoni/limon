<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hasar250{
    private $host;
    private $pass;
    private $url;
    private $Comprobante;

    public function __construct(){
    }

    public function Imprimir(){
        $rs = array();
        foreach( $this->getComprobante as  $key=>$value){
            $jsonToString = null;
            if(is_array($value)){
                foreach ($value as $v){
                    $jsonToString[$key] = $v;
                    $comando=json_encode($jsonToString);
                    $rs[$key] = $this->ImprimirComando($comando);               
                }
            }else{
                $jsonToString[$key] = $value;
                $comando=json_encode($jsonToString);
                $rs[$key] = $this->ImprimirComando($comando);           
            }
        }
        return $rs;
    }

    private function ImprimirComando($comando=null){
        $status = 10;
        ///*** controlo el comando */
        $status = ($comando===null)?11:$status;  //que no sea null

        if ($status != 10){
            return false;
        }else{
            $headers = array(    "Content-type: application/json"
                                ,"Content-length: ".strlen($comando)
                                ,"Accept: application/json"
                                ,"Connection: close"
                            );

            //open connection
            $ch = curl_init();
            $url = $this->getUrl();
        
            //set options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_PROXY, '');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERPWD, $this->getPass());
            #curl_setopt($ch, CURLOPT_ENCODING, 'ISO-8859-1');
            //curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $comando);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            //controlo errores.....

            return $result;    
        }
    }

    public function getPass() {
    	return $this->pass;
    }

    /**
    * @param $pass
    */
    public function setPass($pass) {
        $pass = explode(":",$pass);
    	$this->pass = ":" . $pass[1];
    }

    public function getHost() {
    	return $this->host;
    }

    /**
    * @param $host
    */
    public function setHost($host) {
        $host = explode(":",$host);
    	$this->host = $host[0];
        $this->setUrl();        
    }

    public function getUrl() {
    	return $this->url;
    }

    /**
    * @param $url
    */
    public function setUrl() {
        $url = "http://".$this->getHost()."/fiscal.json";
    	$this->url = $url;
    }


    public function getComprobante() {
    	return $this->Comprobante;
    }

    /**
    * @param $Comprobante
    */
    public function setComprobante($Comprobante) {
    	$this->Comprobante = $Comprobante;
    }
}

/*
 * Libreria para controlador Hasar 250
 * Location: application/libraries/Hasar250.php 
 */
