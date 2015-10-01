<?php
include('database.php');

if(isset($_POST['id'])) {
    $search = query("select * from people where id = '". $_POST['id']."'");

    echo $search['name']." ".str_replace("'", "", $search['surname']);
}
?>