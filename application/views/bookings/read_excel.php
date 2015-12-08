<?php
    //$path = 'application/views/bookings/files/4 Nov 15 KX.xlsx';
    //$file = '';
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load($path);
    
    $room = '';
    $date = '';
    $instances = array();
    
    $months = array('Jan'=>1, 'Feb'=>2, 'Mar'=>3, 'Apr'=>4, 'May'=>5, 'Jun'=>6, 'Jul'=>7, 'Aug'=>8, 'Sep'=>9, 'Oct'=>10, 'Nov'=>11, 'Dec'=>12);
    
    $sheetNames = $objPHPExcel->getSheetNames();
    //echo '<pre>';
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
                            $room_id = $this->bookings_model->room_name2id($room);
                            if(preg_match($read_time, $time, $times) && preg_match($read_date, $date, $dates) && !is_null($room_id)){
                                $reservation = array(
                                    'room_id' => $room_id,
                                    'user_id' => NULL,
                                    'booking_start' => mktime($times['s_hour'], $times['s_min'], 0, $months[$dates['month']], $dates['day'], $dates['year']),
                                    'booking_end' => mktime($times['e_hour'], $times['e_min'], 0, $months[$dates['month']], $dates['day'], $dates['year']),
                                    'frequency' => 0,
                                    'number_of_people' => 0,
                                    'Phone_number' => NULL,
                                    'Title' => $value
                                );
                                $this->db->insert('bookings_reservations', $reservation);
                                $instances[] = array(
                                    'booking_id' => $this->db->insert_id(),
                                    'room_id' => $room_id,
                                    'time_start' => mktime($times['s_hour'], $times['s_min'], 0, $months[$dates['month']], $dates['day'], $dates['year']),
                                    'time_end' => mktime($times['e_hour'], $times['e_min'], 0, $months[$dates['month']], $dates['day'], $dates['year']),
                                );
                                
                            }
                        }
                    }
                }
            }
            $this->db->insert_batch('bookings_instances', $instances);
            /*foreach ($instances as $num => $instance){
                $instance = $this->bookings_model->input_excel($instance);
                //var_dump($instance);
            }*/
            //var_dump($instances);
        }
    }
    //echo '</pre>';
    if(count($instances) > 0){
        
        $date = $instances[0]['time_start'];
        $year = date('Y', $date);
        $month = date('m', $date);
        $day = date('d', $date);
        
        echo count($instances).' Bookings uploaded to the database<br><br>';
        echo '<br>'.anchor('bookings/calender/'.$year.'/'.$month.'/'.$day, 'View uploaded data', 'class="jcr-button"');
    }else{
        echo 'No bookings were found. Please contact the JCR president for help.';
    }