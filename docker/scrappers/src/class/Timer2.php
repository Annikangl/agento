<?php
# простой таймер для контроля времени работы скрипта.

class TIMER {
  // initial 
  public static $start = '';
  public static $pause_time = '';

  
  // internal
  protected static $mtimer = null;
 
  
  public static function getMTIMER() {
    $mtimer = TIMER::$mtimer;
    
    if ($mtimer === null) {
      $mtimer = TIMER::$mtimer = new timer_run();
    }
    
    return $mtimer;
  }

  public static function __callStatic($name, $args) {
    $fn = array(TIMER::getMTIMER(), $name);
   # if (! is_callable($fn)) {
  #    throw new TIMERException("TIMER does not have a method called $name");
   # }

    return call_user_func_array($fn, $args);
  }

 
}


class timer_run {
	public $start;
	public $pause_time;

	/*  start the timer  */
	public function __construct($start = 0) {
		if($start) { $this->start(); }
	}

	/*  start the timer  */
	public function start() {
		$this->start = $this->get_time();
		$this->pause_time = 0;
	}

	/*  pause the timer  */
	public function pause() {
		$this->pause_time = $this->get_time();
	}

	/*  unpause the timer  */
	public function unpause() {
		$this->start += ($this->get_time() - $this->pause_time);
		$this->pause_time = 0;
	}

	/*  get the current timer value  */
	public function get($decimals = 8) {
		return round(($this->get_time() - $this->start),$decimals);
	}

	/*  format the time in seconds  */
	public function get_time() {
		list($usec,$sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>