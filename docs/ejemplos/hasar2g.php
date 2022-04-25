<?php
    function printHasar2g($comando){
        $headers = array("Content-type: application/json","Content-length: ".strlen($comando),"Accept: application/json","Connection: close");

        //open connection
        $ch = curl_init();

        $host = getenv('CF_2g');
        $pass = ":" . getenv('PASS_2g');

        $url = "http://" . $host . "/fiscal.json";
    
        //set options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERPWD, $pass) ;
        #curl_setopt($ch, CURLOPT_ENCODING, 'ISO-8859-1');
        //curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $comando);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        //error_log($result);
        //close connection
        curl_close($ch);
        //$xml = simplexml_load_string($result);
    //    $json=xml2js($xml);
        return $result;
    }

    $filename = "./comprobanteT.json";
    $fp = fopen($filename, "r");
    $xml_src = fread($fp, filesize($filename));
    fclose($fp);

    $json = json_decode($xml_src);

    foreach( $json as  $key=>$value){
        $jsonToString = null;
        if(is_array($value)){
            foreach ($value as $v){
                $jsonToString[$key] = $v;
                $comando=json_encode($jsonToString);
                $r = printHasar2g($comando);
                echo "****************\n";        
                echo "-----$key------\n";        
                echo "-----$comando------\n";        
                echo "-----$r------\n";                 
            }
        }else{
            $jsonToString[$key] = $value;
            $comando=json_encode($jsonToString);
            $r = printHasar2g($comando);
            echo "****************\n";        
            echo "-----$key------\n";        
            echo "-----$comando------\n";        
            echo "-----$r------\n";            
        }
    }
    
