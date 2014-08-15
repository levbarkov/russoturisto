<?php
/**
//Всякие побочные функции
 *
 * @author dmitry
 */
class bankir
{
// Приводит строку к нормальному денежному виду "RRRR.KK"
	function money($amount)
	{
		if(strlen($amount) == 0) throw new Exception("Сумма не заполнена");
                $amount = preg_replace('/,/','.', $amount); // меняем запятую на точку
		$amount = preg_replace('/ /','',$amount); // режем пробелы
		list($rub, $kop) = explode(".",$amount); //
		if(!is_numeric($rub)) throw new Exception('Неверно указана сумма');
		if($kop != "")
		{
			if(!is_numeric($kop)) return false;
			if(strlen($kop) > 2) {$kop = substr($kop,0,2); } //если остатка больше чем 99, режем
			if(strlen($kop) == 1) $kop = $kop."0";
		}
		else $kop = "00";
		$amount  = $rub.".".$kop;
		return $amount;
	}
        
        /** Прописной вариант суммы
         *
         * @param <FLOAT> $amount
         * @return <STRING>
         */
	public function toWords($amount)
	{
            return num::num2str ( $amount );
	}	

	  
        function postSender($url, $text)
        {
            $opts = array('http' =>
            	    array(
            	        'method'  => 'POST',
		        'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
                       ."Content-Length: " . strlen($text) . "\r\n",
            	        'content' => $text
		    )
		);

                $context  = stream_context_create($opts);                
                $result = @file_get_contents($url, FALSE, $context);
                return $result;
        }

        function curlPostSender($url, $text)
        {
             $s = curl_init();
             curl_setopt($s,CURLOPT_URL,$url);
             curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:'));
             curl_setopt($s,CURLOPT_TIMEOUT,30);
             curl_setopt($s,CURLOPT_RETURNTRANSFER,true);

         /*  if($this->authentication == 1){
           curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
         }
          * */

             curl_setopt($s,CURLOPT_POST,true);
             curl_setopt($s,CURLOPT_POSTFIELDS,$text);

             $result = curl_exec($s);
             curl_close($s);
             return $result;
        }

        function httpPostSender($url, $text)
        {
                $credentials = 'user@example.com:password';
                $header_array = array("Content-type" => "application/x-www-form-urlencoded" );
                $ssl_array = array('version' => SSL_VERSION_SSLv3);
                $options = array(headers => $header_array,
                    httpauth => $credentials,
                    httpauthtype => HTTP_AUTH_BASIC,
                    protocol => HTTP_VERSION_1_1,
                    ssl => $ssl_array);
            //create the httprequest object
            try {
                $httpRequest_OBJ = new httpRequest($url, HTTP_METH_POST, $options);
            //add the content type
                $httpRequest_OBJ->setContentType = 'Content-Type: text/xml';
            //add the raw post data
                $httpRequest_OBJ->setRawPostData ($text);
            //send the http request
                $result = $httpRequest_OBJ->send();
            //print out the result
                return  $result->getBody();
            }
            catch(Exception $e)
            {
                return false;
            }
        }
        
}


?>
