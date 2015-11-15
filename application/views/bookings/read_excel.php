<?php
    $path = 'application/views/bookings/files/';
    $file = '4 Nov 15 KX.xlsx';
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load($path.$file);
    
    $room = '';
    $date = '';
    $instances = array();
    
    $months = array('Jan'=>1, 'Feb'=>2, 'Mar'=>3, 'Apr'=>4, 'May'=>5, 'Jun'=>6, 'Jul'=>7, 'Aug'=>8, 'Sep'=>9, 'Oct'=>10, 'Nov'=>11, 'Dec'=>12);
    
    $sheetNames = $objPHPExcel->getSheetNames();
    echo '<pre>';
    foreach($sheetNames as $sheetIndex => $sheetName) {
        if ($sheetName == "Conference Room Plan"){
            $objPHPExcel->setActiveSheetIndexByName($sheetName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            foreach($sheetData as $row => $innerArray){
                foreach($innerArray as $col => $value){
                    if($row > 3){
                        if(!is_null($sheetData[3][$col])) //May want to make 3 a variable then calculate it in case it ever changes
                            $date = $sheetData[3][$col];
                        if($col == 'A' && !is_null($value)){
                            $room = $value;
                        }elseif(!is_null($value)){
                            $read_time = "/(?P<s_hour>[0-9]{2}):(?P<s_min>[0-9]{2}) to (?P<e_hour>[0-9]{2}):(?P<e_min>[0-9]{2})/";
                            $read_date = "/(?P<day>[0-9]+)-(?P<month>[a-zA-Z]+)-(?P<year>[0-9]+)/";
                            $time = $objPHPExcel->getActiveSheet()->getComment($col.$row)->getText()->getPlainText();
                            if(preg_match($read_time, $time, $times) && preg_match($read_date, $date, $dates)){
                                $instances[] = array(
                                    'room' => $room,
                                    'event' => $value,
                                    'starttime' => mktime($times['s_hour'], $times['s_min'], 0, $months[$dates['month']], $dates['day'], $dates['year']),
                                    'endtime' => mktime($times['e_hour'], $times['e_min'], 0, $months[$dates['month']], $dates['day'], $dates['year']),
                                    'cell' => $col.$row
                                );
                            }
                        }
                    }                    
                   /* $cell = (string)$col.(string)$row;
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
                        //echo $room.': '.$date[$col_str].': '.$value.': '.$times['s_hour'].':'.$times['s_min'].' - '.$times['e_hour'].':'.$times['e_min'].'<br><br>';

                        /*$instance[] = array('time_start' => $t,
                                                    'time_end' => $end_t,
                                                    'booking_id'=> $id,
                                                    'room_id' => $details['room_id']
                                                    );
                        $this->db->insert_batch('bookings_instances', $instance);
                    }*/
                    
                }
            }
            var_dump($instances);
        }
    }
    echo '</pre>';