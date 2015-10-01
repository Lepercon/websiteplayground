<?php

/* 
    Updating the webpage after a github request
 */

$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");

$txt = var_export($_POST, true);
fwrite($myfile, $txt);
fclose($myfile);