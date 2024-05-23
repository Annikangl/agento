<?php
        ini_set('memory_limit', '1024M');

    # переделать на add cron job  или удаленный вызов скрипта CURL с низким timeout
    if (isset($_GET["run"])){
        if ($_GET["run"]=='dubizzle.com'){            
            include("dubizzle.com.php");
        }
        if ($_GET["run"]=='encar.com'){            
            include("encar.com.php");
        }
        
        echo "----- END ------";
        exit;
    }
    
    
    
    
    include 'config.php';
    include 'class/meekrodb.2.4.class.php';    
    
    try {
            DB::$dbName = DB_NAME;
            DB::$user = DB_USER;
            DB::$password = DB_PWD;
            DB::$host = DB_HOST;
            
            DB::query("SET CHARACTER SET utf8;");
        } catch (Exception $e) {
            echo 'Error message ',  $e->getMessage();                        
            exit;
        }
    
    if (isset($_GET["stop"]) && $_GET["id"]){
        # stopped scraping process..        
        DB::update('scrapper_tasks', ['task_status' =>0], "task_id=%i", intval($_GET["id"]));                                        
        echo "<p><b>Stopped process - ".intval($_GET["id"])."</b></p>";
        echo "<p>Redirect in 2 sek..</p>";
        $path_redirect = explode("?", $_SERVER['REQUEST_URI']);
        $path_redirect =$path_redirect[0];
        echo '<script>
            setTimeout(redirectHandler, 2000); 
            function redirectHandler() {
                window.location = "'.$path_redirect.'";
            } 
        </script>';
        
        
      #  header("Location: ".$_SERVER['REQUEST_URI']);
        exit;
    }
   
 
    
    echo "<html>";
    echo "<head>";
    echo "<title>Process list...</title>";
    echo "</head>";
    
    
    
    echo "<body>";
    
    
    echo "<h1>Active task</h1>";
    $active_task = DB::query("SELECT * FROM scrapper_tasks WHERE task_status=1");
    
    if ($active_task){
        foreach ($active_task as $one){
            echo "<table border=1 cellpadding=2 cellspacing=0>";
        echo "<thead style='font-weight:bold;'>
                    <tr>
                        <td>id</td>
                        <td>Task name</td>
                        <td>Started</td>
                        <td>Last update/end</td>
                        <td>Time work</td>
                        <td>Progress</td>
                        <td>Last msg</td>
                        <td>View logs</td>
                      
                        <td>Action</td>
                    </tr>
             </thead>";
        foreach ($active_task as $one){
            echo "<tr>";
            echo "<td>".$one["task_id"]."</td>";
            echo "<td>".$one["task_type"]."</td>";
            echo "<td>".date("Y-m-d H:i:s",$one["task_start"])."</td>";
            echo "<td>".date("Y-m-d H:i:s",$one["task_last_update"])."</td>";
            echo "<td>". ($one["task_last_update"]-$one["task_start"])." sek</td>";
            echo "<td>".$one["task_progress"]."%</td>";
            echo "<td>".$one["task_last_msg"]."</td>";
            echo "<td><a href='".$_SERVER['REQUEST_URI']."run-logs/".$one["task_log_path"]."' target=_blank>Log</a></td>";
            
            echo "<td><a href='?stop&id=".$one["task_id"]."'>Stop</td>";
            echo "</tr>";
        }
        echo "</table>";
        }
    }else{
        echo "<p>No active task</p>";
    }
    
    echo "<h1>Action</h1>";
 

   # echo " | <a href='?run=encar.com' target='_blank'>RUN encar.com</a>";
    
    $count_in_DB = @DB::queryFirstRow("SELECT count(*) as num FROM catalog_property WHERE active_flag=1 LIMIT 1");
    if ($count_in_DB>0){
     #   echo " -  <a href='?export=encar.com' target='_blank'>(CSV - ".$count_in_DB["num"]." records)</a>";
    }
    
    echo "<h1>Last 20 completed tasks</h1>";
    $active_task = DB::query("SELECT * FROM scrapper_tasks WHERE task_status=0 ORDER BY task_id DESC");
    
    if ($active_task){
        echo "<table border=1 cellpadding=2 cellspacing=0>";
        echo "<thead style='font-weight:bold;'>
                    <tr>
                        <td>id</td>
                        <td>Task name</td>
                        <td>Started</td>
                        <td>Last update/end</td>
                        <td>Time work</td>
                        <td>Task progress</td>
                        <td>Last msg</td>
                        <td>View logs</td>
                    </tr>
             </thead>";
        foreach ($active_task as $one){
            echo "<tr>";
            echo "<td>".$one["task_id"]."</td>";
            echo "<td>".$one["task_type"]."</td>";
            echo "<td>".date("Y-m-d H:i:s",$one["task_start"])."</td>";
            echo "<td>".date("Y-m-d H:i:s",$one["task_last_update"])."</td>";
            echo "<td>". ($one["task_last_update"]-$one["task_start"])." sek</td>";
            echo "<td>".$one["task_progress"]."%</td>";
            echo "<td>".$one["task_last_msg"]."</td>";
            echo "<td><a href='".$_SERVER['REQUEST_URI']."run-logs/".$one["task_log_path"]."' target=_blank>Log</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "</body>";
    echo "</html>";
    
function csv_line(array $fields, $delimiter = ',', $enclosure = '"', $mysql_null = false) { 
    $delimiter_esc = preg_quote($delimiter, '/'); 
    $enclosure_esc = preg_quote($enclosure, '/'); 

    $output = array(); 
    foreach ($fields as $field) { 
        if ($field === null && $mysql_null) { 
            $output[] = 'NULL'; 
            continue; 
        } 

        $output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? ( 
            $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure 
        ) : $field; 
    } 

    return join($delimiter, $output) . "\n"; 
}      
?>