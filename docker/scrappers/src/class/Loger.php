<?php

class LOGER_ {
  // initial
  public static  $log_type; # console / file / combo

  public static $log_path_default; #

  public static $log_name;  # путь к  файлу лога, если не консоль
  public static $file_descr; # дескриптор файла

  public static $log_path_full; # полный путь к файлу

  // internal
  protected static $mloger = null;


  public static function getLOGER_() {
    $mloger = LOGER_::$mloger;

    if ($mloger === null) {
      $mloger = LOGER_::$mloger = new Loger();
    }

    return $mloger;
  }

  public static function __callStatic($name, $args) {
    $fn = array(LOGER_::getLOGER_(), $name);
   # if (! is_callable($fn)) {
  #    throw new TIMERException("TIMER does not have a method called $name");
   # }

    return call_user_func_array($fn, $args);
  }


}


class Loger {
	var $log_type="console"; # console / file / combo

    var $log_path_default; #

    var $log_name;  # путь к  файлу лога, если не консоль
    var $file_descr; # дескриптор файла


	/*  start Loger  */
	function __construct() {


	}

    function LogStart($log_name='', $log_type = "console") {

		if ($log_name==''){
            $this->log_name = time().".txt";
        }else{
            $this->log_name = $log_name."_".time().".txt";
        }

        $this->log_type = $log_type;

        try {

            if ($this->log_type == "file" || $this->log_type=="combo"){

               # $this->log_path_default = "/../run-logs/";
                $this->log_path_default = dirname(__FILE__) . "/../run-logs/";

                if (!is_dir($this->log_path_default)){
                    mkdir($this->log_path_default);
                }
                $this->file_descr = fopen($this->log_path_default.$this->log_name,"a");
                if (!$this->file_descr){
                    echo 'can`t create files errors...';
                    exit;
                }
            }else{
                $this->log_path_full ='';
            }
        } catch (Exception $e) {
            echo  "Can't write logs to HDD: ".$e->getMessage();
        }
	}

    function return_log_path(){
         if ($this->log_type == "file" || $this->log_type=="combo"){
            return $this->log_name;
         }else{
            return '';
         }
    }

	/*  add msg to console or file logs..  */
	function msg($msg_add,$type="INFO") {  #info / error

        if ($this->log_type == "combo" || $this->log_type == "console"){
            echo TIMER::get()." [$type] - ".$msg_add."\n";
        }

        if ($this->log_type == "combo" || $this->log_type == "file"){
            fputs($this->file_descr, TIMER::get()." [$type] - ".$msg_add."\r");
        }
	}

    function __destruct() {
        if ($this->log_type == "combo" || $this->log_type == "file"){
            fclose($this->file_descr);
        }
    }


}
?>
