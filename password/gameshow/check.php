<?php
if($_GET['check'] == "true") {
    $db = mysql_connect("mysql.dur.ac.uk", "djb8jcr", "VP4GL6dhF3rYdUGB");

    if(!$db) {
        die('Could not connect: ' . mysql_error());
    }

    mysql_select_db("Xdjb8jcr_gameshow");

    function query($query) {
        $result = mysql_query($query);
        if ($result) {
            if(@mysql_num_rows($result)) {
                while($row = mysql_fetch_array($result)) {
                    $output[] = $row;
                }
            } else {
                $output = null;
            }
        } else {
            die('MySQL error : ' . mysql_error());
        }

        if(count($output) == 1) {
            $output = $output[0];
        }
        return $output;
    }

    $result = query("SELECT team, time FROM family ORDER BY id ASC LIMIT 0, 1");

    if($result['time'] > time() - 2) {
        echo "contest".$result['team'];
    }
    
    query("TRUNCATE TABLE family");
}