<?php
# простой переводчик. Есть набор строк типа:
# значение1 значение2
# выполняет замену значение1 => значение2
# формат строки [значение1]\t[значение2]\n
#
class Translator {
	var $dictionary = array();	

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
        
		if (isset($this->dictionary[$str])){
            return $this->dictionary[$str];
        }else{
            # нет перевода для данного значения
            # автоперевод? или др. действия.
            # по умолчанию просто возвращем то что запросил.
            return $str;
        }
	}
}
?>