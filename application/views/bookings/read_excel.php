<?php
    $path = 'application/views/bookings/files/';
    $file = '4 Nov 15 KX.xlsx';
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load($path.$file);
    /*
    $sheetNames = $objPHPExcel->getSheetNames();
    echo '<pre>';
    foreach($sheetNames as $sheetIndex => $sheetName) {
            echo 'WorkSheet #',$sheetIndex,' is named "',$sheetName,'"<br />';
            $objPHPExcel->setActiveSheetIndexByName($sheetName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            var_dump($sheetData);
    }
    echo '</pre>';
     */
    $objPHPExcel->setActiveSheetIndexByName("Conference Room Plan");
    var_dump($objPHPExcel->getActiveSheet()->getComment('D4')->getText()->getPlainText());
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->Save($path.'TTest.xlsx');
    