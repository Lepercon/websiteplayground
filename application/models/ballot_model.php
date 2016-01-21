<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ballot_model extends CI_Model {


    function Ballot_model() {
        parent::__construct(); // Call the Model constructor
    }

    function get_ballots(){
        $this->db->select('ballot.*, events.name, events.time');
        $this->db->join('events', 'ballot.event_id=events.id');
        $this->db->order_by('close_time desc');
        return $this->db->get('ballot')->result_array();
    }
    
    function get_ballot($id, $u_id){
        $this->db->where('ballot.id', $id);
        $this->db->select('ballot.*, events.name, events.time');
        $this->db->join('events', 'ballot.event_id=events.id');
        $ballot = $this->db->get('ballot')->row_array(0);
        
        $this->db->where('ballot_id', $id);
        $this->db->where('created_by', $u_id);
        $this->db->or_where('ballot_id', $id);
        $this->db->where('user_id', $u_id);
        $bal = $this->db->get('ballot_people')->row_array(0);

        $ballot['people'] = array();
        if(!empty($bal)){
            $this->db->where('ballot_id', $id);
            $this->db->where('created_by', $bal['created_by']);

            $ballot['people'] = $this->db->get('ballot_people')->result_array();
        }
        
        return $ballot;
    }
    
    function update_ballot($id, $u_id, $users){
        $this->db->where( array('created_by' => $u_id, 'ballot_id'=>$id));
        $u_ids = $this->db->get('ballot_people')->result_array();
        $this->db->delete('ballot_people', array('created_by' => $u_id, 'ballot_id'=>$id)); 
        $this->db->insert_batch('ballot_people', $users);
        $email_list = array();
        foreach($users as $u){
            $email_list[$u['user_id']] = TRUE;
        }        
        foreach($u_ids as $u){
            if(isset($email_list[$u['user_id']])){
                unset($email_list[$u['user_id']]);
            }
        }
        $_POST = array();
    }
    
    function get_priority($ballot_id, $user_id){
        if($user_id == -1){
            return 0;
        }
        
        $this->db->where('ballot_id !=', $ballot_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('table_num IS NULL');
        $num = $this->db->get('ballot_people')->num_rows();
        $this->db->where('ballot_id !=', $ballot_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('table_num IS NOT NULL');
        $num -= $this->db->get('ballot_people')->num_rows();
        if($num < 0){
            return 0;
        }
        return $num;
    }
    
    function get_tables($id, $not_assigned=true, $group=false){
        $this->db->where('ballot_id', $id);
        if(!$not_assigned){
            $this->db->where('table_num is not null');
        }
        $this->db->order_by('id');
        $this->db->select('ballot_people.*, u1.firstname, u1.prefname, u1.surname, u2.firstname as fn, u2.prefname as pn, u2.surname as sn', $id);
        $this->db->join('users as u1', 'ballot_people.user_id=u1.id', 'left outer');
        $this->db->join('users as u2', 'ballot_people.created_by=u2.id');
        $people = $this->db->get('ballot_people')->result_array();
        $tables = array();
        if($group){
            $totals = array();
            foreach($people as $p){
                $tables[$p['created_by']][] = $p;
                if(isset($tables[$p['created_by']]['score'])){
                    $tables[$p['created_by']]['score'] += $p['priority_score'];
                }else{
                    $tables[$p['created_by']]['score'] = $p['priority_score'];
                }
            }
        }else{
            foreach($people as $p){
                if(!is_null($p['table_num'])){
                    $tables[$p['table_num']][] = $p;
                }else{
                    $tables[-1][] = $p;
                }
            }
        }
        return $tables;
    }
    
    function table_assignment($id){
        
        $this->db->where('id', $id);
        $ballot = $this->db->get('ballot')->row_array(0);
        $t = explode(';', $ballot['tables']);
        asort($t);
        //log_message('error', var_export($t, true));
        
        $data = array();
        $sizes = array();
        
        if($ballot['close_time'] < time()){
            $tables = $this->get_tables($id, true, true);
            
            foreach($tables as $key => $table){
                $sizes[$key] = count($table);
            }
            
            $sizes = $this->shuffle_sort($sizes);
            $i = 0;
            
            foreach($t as $num => $spaces){
                foreach(range(1, min(count($sizes), $spaces)) as $i){
                    //find a set of $i groups from $sizes that combine to have $spaces people in
                    //log_message('error', '##TABLE '.$num.' '.$spaces);
                    $new_set = $this->get_groups_set($i, $sizes, $spaces);
                    if(!is_null($new_set)){
                        foreach($new_set as $n){
                        
                            //log_message('error', 'Removing '.$n);
                            unset($sizes[$n]);

                            //log_message('error', $n.': '.count($tables[$n]));
                            foreach($tables[$n] as $kk => $tab){
                                $tables[$n][$kk]['table_num'] = $num;
                                $data[] = array(
                                    'id' => $tables[$n][$kk]['id'],
                                    'table_num' => $num
                                );
                            }
                        }
                        break;
                    }    
                }                
            }
            
            $this->db->update_batch('ballot_people', $data, 'id');
            
        }else{
            return NULL;
        }
        return $tables;
    }
    
    function shuffle_sort($list) { 
        
        //Sort array into sepeate lists by value
        $sorted_list = array();
        foreach($list as $k => $v){
            $sorted_list[$v][$k] = $v;
        }
        
        //Put them into high to low order
        krsort($sorted_list);
        
        $master = array();
        
        mt_srand(33);
        foreach($sorted_list as $k => $v){
            $keys = array_keys($v); 
            
            $n = count($keys);
            for($i=0; $i<=$n-2; $i++){
                   $j = mt_rand($i, $n-1);
                   $temp = $keys[$i];
                   $keys[$i] = $keys[$j];
                   $keys[$j] = $temp;
            }
            
            foreach ($keys as $key) { 
                $master[$key] = $v[$key]; 
            }
        }
        
        return $master; 
    } 
    
    function get_groups_set($i, &$sizes, $spaces){
        //log_message('error', 'find a set of '.$i.' groups that combine to have '.$spaces.' people in from '.var_export($sizes, true));
    
        if($i == 1){
            foreach($sizes as $k=>$s){
                if($s == $spaces){
                    //log_message('error', 'found '.$k.' to fill remaining '.$spaces.' spaces');
                    return array($k);
                }elseif($s < $spaces){
                    break;
                }
            }
            return NULL;
        }else{
            $attempts = array();
            foreach($sizes as $k=>$s){
                if($spaces > $s && !isset($attempts[$s])){
                    $sub_sizes = $sizes;
                    unset($sub_sizes[$k]);
                    //log_message('error', 'Attempting to use '.$k.' ('.$s.'), looking for '.($i-1).' groups to fill remaining '.($spaces-$s).' spaces');
                    $new_set = $this->get_groups_set($i-1, $sub_sizes, $spaces-$s);
                    if(!is_null($new_set)){
                        //log_message('error', 'Removing '.$k);
                        $new_set[] = $k;
                        return $new_set;
                    }
                    $attempts[$s] = true;
                }
            }
            return NULL;
        }
        
    }
    
    /*function do_assignments($id){
        $this->db->where('ballot_id', $id);
        $people = $this->db->get('ballot_people')->result_array();
        foreach($people as $k=>$p){
            $people[$k]['table_num'] = 6;
        }
        $this->db->update_batch('ballot_people', $people, 'id');
        $this->db->update('ballot', array('calc_token'=>'complete'), array('id' => $id));
    }*/
    
    function shuffle_sort2($seed, $list, $scores){ 
        
        //log_message('error', var_export($list, true));
        //log_message('error', var_export($scores, true));
        //Sort array into sepeate lists by value
        $sorted_list = array();
        foreach($list as $k => $v){
            $sorted_list[$v][$k] = $v;
        }
        
        //Put them into high to low order
        krsort($sorted_list);
        
        $master = array();
        
        mt_srand($seed);
        foreach($sorted_list as $k => $v){
            $keys = array_keys($v); 
            
            $n = count($keys);
            for($i=0; $i<=$n-2; $i++){
                $j = mt_rand($i, $n-1);
                $temp = $keys[$i];
                $keys[$i] = $keys[$j];
                $keys[$j] = $temp;
            }
            
            $done_swap = true;
            while($done_swap){
                $done_swap = false;
                for($i=1; $i<$n; $i++){
                    if($scores[$keys[$i]] > $scores[$keys[$i-1]]){
                        $temp = $keys[$i];
                        $keys[$i] = $keys[$i-1];
                        $keys[$i-1] = $temp;
                        $done_swap = true;
                    }
                }
            }
            
            foreach ($keys as $key) { 
                $master[$k][$key] = $v[$key]; 
            }
        }
        
        return $master; 
    } 
    
    function get_people($id, $printing=FALSE){
        
        if($printing){
            $this->db->order_by('ISNULL(users.surname), users.surname');
        }else{
            $this->db->order_by('ISNULL(table_num), table_num');
        }
        $this->db->select('ballot_people.*, CONCAT(u2.firstname, \' \', u2.surname) as creator_name, users.email', false);
        $this->db->where('table_num IS NOT NULL');
        $this->db->join('users', 'users.id=ballot_people.user_id', 'left outer');
        $this->db->join('users as u2', 'u2.id=ballot_people.created_by', 'left outer');
        $this->db->where('ballot_id', $id);
        $people = $this->db->get('ballot_people')->result_array();
        $this->db->where('id', $id);
        $ballot = $this->db->get('ballot')->row_array();
        
        $options = explode(':', $ballot['options']);
        $op = array();

        foreach($options as $k=>$o){
            $temp = explode(';', $o);
            $op[$k]['title'] = $temp[0];
            $op[$k]['options'] = array();
            foreach(array_slice($temp, 1) as $i => $t){
                $name_price = explode('#', $t);
                if(count($name_price) < 2){
                    $name_price[1] = 0;
                }
                $op[$k]['options'][$i]['name'] = $name_price[0];
                $op[$k]['options'][$i]['price'] = $name_price[1];
            }
        }
        
        $totals = array();
        $table_totals = array();
        
        foreach($people as $k => $p){
            $temp = explode(';', $p['options']);
            $people[$k]['op_list'] = array();
            foreach($temp as $kk => $t){
                $people[$k]['op_list'][] = $op[$kk]['options'][$t];
                if(!isset($totals[$kk][$t])){
                    $totals[$kk][$t] = 0;
                }
                if(!isset($table_totals[$p['table_num']][$kk][$t])){
                    $table_totals[$p['table_num']][$kk][$t] = 0;
                }
                $totals[$kk][$t]++;
                $table_totals[$p['table_num']][$kk][$t]++;
            }
        }
        
        $this->db->where('ballot_id', $id);
        $this->db->where('requirements != ', '');
        $this->db->where('table_num IS NOT NULL');
        $this->db->order_by('table_num');
        $requirements = $this->db->get('ballot_people')->result_array();
        
        foreach($requirements as $k => $p){
            $temp = explode(';', $p['options']);
            $requirements[$k]['op_list'] = array();
            foreach($temp as $kk => $t){
                $requirements[$k]['op_list'][] = $op[$kk]['options'][$t];
            }
        }
        
        $data['people'] = $people;
        $data['options'] = $op;
        $data['totals'] = $totals;
        $data['table_totals'] = $table_totals;
        $data['requirements'] = $requirements;
        
        return $data;
    }

    
    function table_assignment2($id){
        
        $this->db->where('id', $id);
        $ballot = $this->db->get('ballot')->row_array(0);
        if($ballot['done_sorting'])
            return;
        $t = explode(';', $ballot['tables']);
        asort($t);
        //log_message('error', var_export($t, true));
        
        $data = array();
        $sizes = array();
        $scores = array();
        
        if($ballot['close_time'] < time()){
            $tables = $this->get_tables($id, true, true);
            
            $num_sizes = array();
            foreach($tables as $key => $table){
                //log_message('error', $key.': '.var_export($table, true));
                $sizes[$key] = count($table)-1;
                $scores[$key] = $table['score'];
                unset($tables[$key]['score']);
                if(isset($num_sizes[$sizes[$key]])){
                    $num_sizes[$sizes[$key]]++;
                }else{
                    $num_sizes[$sizes[$key]] = 1;
                }
            }
            krsort($num_sizes);
            
            $sizes = $this->shuffle_sort2($ballot['calc_token'], $sizes, $scores);
            mt_srand($ballot['calc_token']);
            
            $t_sub = $t;
            foreach($t as $num => $spaces){
                
                $lim = 1000;
                foreach($t_sub as $n => $sp){
                    $count = 0;
                    //log_message('error', '##Looking for '.$sp.' for table '.$n.' from '.var_export($num_sizes, true));
                    $A[$n] = $this->combinations($sp, $num_sizes, 1, $lim, $count);
                    //log_message('error', 'Found '.var_export($A, true));
                    $lim = min($lim, count($A[$n]));
                    $counts[$n] = count($A[$n]);
                }
                
                $min = array_search(min($counts), $counts);
                if($counts[$min] > 0){
                    
                    $ind = 0;
                    if(mt_rand(0, 10) > 6)
                        $ind = mt_rand(0, count($A[$min])-1);
                    //log_message('error', $ind.' '.var_export($A[$min], true));
                    $res[$min] = $A[$min][$ind];
                    foreach($A[$min][$ind] as $a){
                        //log_message('error', '##Reducing '.$a.' '.var_export($num_sizes, true));
                        $num_sizes[$a]--;
                        //log_message('error', '##Reduced '.$a.' '.var_export($num_sizes, true));
                        reset($sizes[$a]);
                        $group_id = key($sizes[$a]);
                        unset($sizes[$a][$group_id]);
                        foreach($tables[$group_id] as $p){
                            $data[] = array(
                                'id' => $p['id'],
                                'table_num' => $min+1
                            );
                        }
                    }
                }
                
                $A = array();
                $counts = array();
                unset($t_sub[$min]);
                //log_message('error', 't_sub = '.var_export($t_sub, true));
                //log_message('error', 'num_sizes = '.var_export($num_sizes, true));
                                            
            }    
            
            $this->db->trans_start();
            $this->db->where('ballot_id', $id);
            $this->db->where('table_num is not null');
            $this->db->set('table_num', NULL);
            $this->db->update('ballot_people');
            if(!empty($data)){
                $this->db->update_batch('ballot_people', $data, 'id');
            }
            $this->db->where('id', $id);
            $this->db->set('done_sorting', 1);
            $this->db->update('ballot');
            $this->db->trans_complete();
            
        }else{
            return NULL;
        }
        return $tables;
    }
    
    function combinations($size, $sizes, $level, $lim, &$count){
        
        $res = array();
        if($count > $lim){
            return $res;
        }
        
        //log_message('error', '('.$level.') looking for '.$size.' from '.var_export($sizes, true));
        
        foreach($sizes as $k => $s){
            //log_message('error', '('.$level.') checking if we can insert a table of '.$k.' ('.$s.')');
            if($s > 0){
                if($k < $size){
                    $si = $sizes;
                    $si[$k]--;
                    $temp = $this->combinations($size-$k, $si, $level+1, $lim, $count);
                    foreach($temp as $t){
                        $t[] = $k;
                        $res[] = $t;
                    }
                }elseif($k == $size){
                    $res[] = array($k);
                    $count = $count+1;
                    //log_message('error', '('.$level.') just right '.$count.' '.var_export($res, true));
                }else{
                    //log_message('error', '('.$level.') too big');
                }
            }
            if($count > $lim){
                break;
            }
        }

        return $res;
        
    }
    
    function signup_check($id){
        $user_id = $this->session->userdata('id');
        $this->db->where('ballot_id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->where('created_by != ', $user_id);
        $res = $this->db->get('ballot_people')->result_array();
        return empty($res);
    }
    
    function get_events(){
        $this->db->select('id, name, time');
        $this->db->where('time > ', time());
        $this->db->order_by('time');
        $ev = $this->db->get('events')->result_array();
        $events = array();
        foreach($ev as $e){
            $events[$e['id']] = $e['name'].' ('.date('d/m/Y', $e['time']).')';
        }
        return $events;
    }
    
    function create_signup(){
        $inputs = array('event_id', 'signup_name', 'max_group', 'price', 'allow_guests');
        $data = array();
        foreach($inputs as $i){
            $data[$i] = $this->input->post($i, true);
        }
        $re = '/(?P<day>[0-9]{2})\\/(?P<month>[0-9]{2})\\/(?P<year>[0-9]{4})/';
        $data['open_time'] = time();
        $data['close_time'] = time();
        if(preg_match($re, $this->input->post('date-open'), $date)){
            $data['open_time'] = mktime($this->input->post('open-hour'), $this->input->post('open-minute'), 0, $date['month'], $date['day'], $date['year'] );
        }
        if(preg_match($re, $this->input->post('date-close'), $date)){
            $data['close_time'] = mktime($this->input->post('close-hour'), $this->input->post('close-minute'), 0, $date['month'], $date['day'], $date['year'] );
        }
        $data['tables'] = implode(';', $this->input->post('table'));
        $data['created_by'] = $this->session->userdata('id');
        $data['calc_token'] = rand_num(0, 2000);
        $data['allow_guests'] = $this->input->post('allow_guests');
        $data['max_guests'] = $this->input->post('max_guests');
        $data['guest_charge'] = $this->input->post('guest_price');
        
        $data['options'] = '';
        foreach($this->input->post('option') as $option){
            if($data['options'] != ''){
                $data['options'] .= ':';
            }
            foreach($option as $k=>$o){
                $data['options'] .= $o;
                if($k % 2 == 0){
                    $data['options'] .= ';';
                }else{
                    $data['options'] .= '#';
                }
            }
            $data['options'] = rtrim($data['options'], ";");
        }
        
        $this->db->insert('ballot', $data);
        return $this->db->insert_id();
    }

        
}
