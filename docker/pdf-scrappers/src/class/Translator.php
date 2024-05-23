<?php
# простой переводчик. Есть набор строк типа:
# значение1 значение2
# выполняет замену значение1 => значение2
# формат строки [значение1]\t[значение2]\n
#
class Translator {
	var $dictionary = array();	
    var $default_lang = 'en';
	/*  start the timer  */
	function __construct() {

	}

	function TranslatorAdd($file_name) {
        if (is_file($file_name)){
            $tmp_data = file_get_contents($file_name);   
            $tmp_data = explode("\n", $tmp_data);
            $count_add=0;
            foreach ($tmp_data as $one){
                if (trim($one)=='') continue;
                $value = explode("\t",$one);
                $this->dictionary[trim($value[0])] = trim($value[1]);
                $count_add++;
            }
            return $count_add;
        }else{
            return -1; # файл не найден.
        }
	}
	 

	
	function Translate($str) {
        
        if ($this->default_lang=='en') {
            return $str;
        }
                
		if (isset($this->dictionary[$str])){
            return $this->dictionary[$str];
        }else{
            # нет перевода для данного значения
            # автоперевод? или др. действия.
            # по умолчанию просто возвращем то что запросил.
            return $str;
        }        
	}
    
    function TranslateAPI($str) {
        
        if ($this->default_lang=='en') {
            return $str;
        }
                
		if (isset($this->dictionary[$str])){
            return $this->dictionary[$str];
        }else{
            # нет перевода для данного значения
            # автоперевод? или др. действия.
            # по умолчанию просто возвращем то что запросил.
            
            $apiKey = 'AIzaSyCJgQyOXzLS2CeTmEc46xZZXt7Gu6mA51E';

          #  $text = 'Hello world!';
            $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($str) . '&source=en&target=ru';

            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($handle);                 
            $responseDecoded = json_decode($response, true);
            curl_close($handle);
            
            return $responseDecoded['data']['translations'][0]['translatedText'];
            
            /*$curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://text-translator2.p.rapidapi.com/translate",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "source_language=en&target_language=ru&text=".urlencode($str),
                CURLOPT_HTTPHEADER => [
                    "X-RapidAPI-Host: text-translator2.p.rapidapi.com",
                    "X-RapidAPI-Key: d78cd68842msh3c38f600c207dc0p1c828ejsne812f9f5b405",
                    "content-type: application/x-www-form-urlencoded"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                #echo "cURL Error #:" . $err;
                #can;t translate
                return $str;
            } else {
                $response = json_decode($response,true);
                $response =$response["data"]["translatedText"];
                $response = urldecode($response);
                return $response;
            }*/
            
            
            return $str;
        }        
	}
    
}
?>