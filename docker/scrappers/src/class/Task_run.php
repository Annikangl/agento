<?php
# простой таскер.
# 
#
class Task_run {
	var $task_name='';
	var $task_id = null;
	var $task_status;    

	
	function __construct($name, $path="") {
		 $this->task_name = $name;
         
         # check if we have running task now
         
         $row = DB::queryFirstRow("SELECT * FROM scrapper_tasks WHERE task_type=%s and task_status=1 LIMIT 1", $this->task_name);

         # add new task 
         if (!$row){
             $now = time();
             DB::insert('scrapper_tasks', [
              'task_start' => $now,
              'task_last_update' => $now,
              'task_status' => 1,
              'task_type' =>$this->task_name,
              'task_progress' =>0,
              'task_last_msg'=>'Starting for the '. $this->task_name,
              'task_log_path'=>$path
            ]);
            $this->task_id = DB::insertId();            
            $this->task_status = true;
            
            return true;
         }else{
            $this->task_status = false;
            return false;
         }
        # DB::query("SET CHARACTER SET utf8;");
	}
    
    function Task_update($progress, $last_msg='') {
            
            DB::update('scrapper_tasks', [              
              'task_last_update' => time(),                
              'task_progress' =>$progress,
              'task_last_msg'=>$last_msg
            ], "task_id=%i", $this->task_id);
            
            return 1;            
    }
    
    
    function Task_finish_error( $last_msg='') {
            if ($this->task_id !==null){
                DB::update('scrapper_tasks', [              
                  'task_last_update' => time(),    
                  'task_status' => 0,                            
#                  'task_progress' =>-1,
                  'task_last_msg'=>$last_msg
                ], "task_id=%i", $this->task_id);
            }
            $this->task_status = false;
            
            return 1;            
    }
    
	function Task_end($msg = 'Task ended') {
        # update or delete...
        
        $this->task_status = false;
        
        DB::update('scrapper_tasks', [              
              'task_last_update' => time(),
              'task_status' => 0,              
              'task_progress' =>100,
              'task_last_msg'=>$msg
            ], "task_id=%i", $this->task_id);
        return 1;
    }
    
    function Task_checker() {
        # update or delete...
        
        $row = DB::queryFirstRow("SELECT * FROM scrapper_tasks WHERE task_id=%i  LIMIT 1", $this->task_id);
        
        # task stopped...
        if ($row["task_status"] ==0){
            $this->task_status = false;            
            return false;
        }        
        return true;        
    }
    
}
?>