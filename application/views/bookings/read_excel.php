<?php
    $path = 'application/views/bookings/files/';
    $file = '4 Nov 15 KX.xlsx';
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load($path.$file);
    
    $sheetNames = $objPHPExcel->getSheetNames();
    echo '<pre>';
    foreach($sheetNames as $sheetIndex => $sheetName) {
        if ($sheetName == "Conference Room Plan"){
            $objPHPExcel->setActiveSheetIndexByName($sheetName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            foreach($sheetData as $row => $innerArray){
                foreach($innerArray as $col => $value){
                    $cell = (string)$col.(string)$row;
                    $col_str = (string)$col;
                    $row_str = (string)$row;
                    if ($col_str == 'A' && $value != ''){
                        $room = ($value);
                    }
                    if ($row == 3 && $col_str != 'A' && $col_str != 'B'){
                        if ($value == ''){
                            $date = array ($col_str => $prev_date);
                            echo $date[$col_str].'<br>';
                        }
                        else{
                            $date = array ($col_str => strtotime($value));
                        }
                        $prev_date = $date[$col_str];
                    }
                    if ($value != null && $row >= 4){
                        $read_time = "/(?P<s_hour>[0-9]{2}):(?P<s_min>[0-9]{2}) to (?P<e_hour>[0-9]{2}):(?P<e_min>[0-9]{2})/";
                        preg_match($read_time, $objPHPExcel->getActiveSheet()->getComment($cell)->getText()->getPlainText(), $times);
                        echo $room.': '.$date[$col_str].': '.$value.': '.$times['s_hour'].':'.$times['s_min'].' - '.$times['e_hour'].':'.$times['e_min'].'<br><br>';

                        /*$instance[] = array('time_start' => $t,
                                                    'time_end' => $end_t,
                                                    'booking_id'=> $id,
                                                    'room_id' => $details['room_id']
                                                    );
                        $this->db->insert_batch('bookings_instances', $instance);*/
                    }
                    
                }
            }
            //var_dump($sheetData);
        }
    }
    echo '</pre>';
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->Save($path.'TTest.xlsx');