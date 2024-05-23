<?php
# простой прокси ротатор
# берем прокси из внешнего источника
# и выдаем по требованию...
# 
class Proxy_webshare {
	var $all_proxy = [];	
    var $current_proxy = [];

	/*  start the timer  */
	function __construct($url, $rnd=true) {
        $curl_opt = [
            CURLOPT_ENCODING => "gzip",
            CURLOPT_HEADER =>0,
            CURLOPT_FAILONERROR =>0,
            CURLOPT_FOLLOWLOCATION =>1,
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER =>1,
            CURLOPT_TIMEOUT=>30,
            CURLOPT_CONNECTTIMEOUT=>30,
            
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36'
        ];        
    
        $proxy_list = Curl::exec($url,  $curl_opt, [], '', []);
 
        $proxy_list_tmp = explode("\n", $proxy_list["response"]);
        
        
        
        foreach ($proxy_list_tmp as $one){
            if ($one=="") continue;
            $one_proxy = explode(":",$one);
            $this->all_proxy[] = array(
                "ip"=>$one_proxy[0],
                "port"=>$one_proxy[1],
                "pwd"=>$one_proxy[2].":".$one_proxy[3],
            );
            #$limit--;
            #if ($limit<=0) break;
        }  
        
       # $limit=15;
        
        
        if ($rnd) shuffle($this->all_proxy);
	}    
    
    function Proxy_shuffle(){
        shuffle($this->all_proxy);
    }
    
    function GetRandomProxy(){
        return $this->all_proxy[array_rand($this->all_proxy)];
    }
    
    function Proxy_next(){
        # no proxy...
        if ($this->all_proxy==[]){            
            $this->current_proxy =[];
            
            #throw new ParserException("Need more proxy. Can't pasring more...",$new_task);            
            return false;
        }
        $this->current_proxy = array_pop($this->all_proxy);
        return true;
    }
	
}


?>