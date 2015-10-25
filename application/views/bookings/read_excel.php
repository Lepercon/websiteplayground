<?php

    $file = 'application/views/bookings/files/test.xlsx';
    $objPHPExcel = PHPExcel_IOFactory::load($file);
    
    echo '<pre>';
    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    var_dump($sheetData);
    echo '</pre>';