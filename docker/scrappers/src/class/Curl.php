<?php
 
class Curl
{

  
  
    # proxy format = array("ip"=>1.1.1.1, "port"=>1111, "pwd"=>"login:pwd" )
    static function exec($url, $options = [], $headers= [], $post='', $proxy=[]) {

        $ch = curl_init($url);        
  
        if (empty($options[CURLOPT_USERAGENT])) {
            $options[CURLOPT_USERAGENT] = self::getRandomUserAgent();
        } else {
            $options[CURLOPT_USERAGENT] = $options[CURLOPT_USERAGENT];
        }                        

        if ($post != ''){
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($post));
            curl_setopt($ch, CURLOPT_POST, 1);
            #curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($post)));
            
            $headers = array_merge($headers, array('Content-Length: ' . strlen($post)));
        }
        
        if ($headers != [] && $headers!=''){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);   
        }
        
        if ($proxy != [] && $proxy!=''){
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy["port"]);
            curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            curl_setopt($ch, CURLOPT_PROXY, $proxy["ip"]);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy["pwd"]);            
        }
        
        
       curl_setopt_array($ch, $options);
 

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
                
        curl_close($ch);
        
        
        return ['response' => $response, 'info' => $info];
    }

#
    # Multitrhread
    #
    static function Get_data_multi($urls, $options = [], $proxy= []){
        global $dir_tmp;
        $hhh =array();
        $data = array();


        if (empty($options[CURLOPT_USERAGENT])) {
            $options[CURLOPT_USERAGENT] = self::getRandomUserAgent();
        } else {
            $options[CURLOPT_USERAGENT] = $options[CURLOPT_USERAGENT];
        }  
    
        $mh = curl_multi_init();

        foreach ($urls as $i => $url) {
            $conn[$i] = curl_init($url);
            $hhh[$i] = $url;
            
            if (isset($proxy[$i])){
                curl_setopt($conn[$i], CURLOPT_PROXYPORT, $proxy[$i]["port"]);
                curl_setopt($conn[$i], CURLOPT_PROXYTYPE, 'HTTP');
                curl_setopt($conn[$i], CURLOPT_PROXY, $proxy[$i]["ip"]);
                curl_setopt($conn[$i], CURLOPT_PROXYUSERPWD, $proxy[$i]["pwd"]);            
            }
            
            curl_setopt_array($conn[$i], $options);
            
            curl_multi_add_handle($mh, $conn[$i]);
        }

        do {
            $status = curl_multi_exec($mh, $active);
            $info = curl_multi_info_read($mh);
            if (false !== $info) {
               // var_dump($info);
            }
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

        foreach ($urls as $i => $url) {
            $html = curl_multi_getcontent($conn[$i]);				
             
             
     
            $data[$i] = $html;
            
    #        $info = curl_getinfo($ch);
    #        print_r($info);
                
            curl_close($conn[$i]);		
        }	
        return $data;
    } 
 

    public static function getRandomUserAgent() {
        $user_agents = array(             
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
            
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0',
        );
        $current_useragent = array_rand(array_flip($user_agents), 1);
        return $current_useragent;
    }
}
