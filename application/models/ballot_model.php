<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ballot_model extends CI_Model {


    function Ballot_model() {
        parent::__construct(); // Call the Model constructor
    }

    function get_ballots(){
        $this->db->select('ballot.*, events.name, events.time');
        $this->db->join('events', 'ballot.event_id=events.id');
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
                
                $this->db->where('ballot_id', $id);
        $this->db->where('created_by', $bal['created_by']);
        
        $ballot['people'] = $this->db->get('ballot_people')->result_array();
        
        return $ballot;
    }
    
    function update_ballot($id, $u_id, $users){
        $this->db->delete('ballot_people', array('created_by' => $u_id, 'ballot_id'=>$id)); 
        $this->db->insert_batch('ballot_people', $users);
        $_POST = array();
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
            foreach($people as $p){
                $tables[$p['created_by']][] = $p;
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
    
    function shuffle_sort2($list) { 
        
        //Sort array into sepeate lists by value
        $sorted_list = array();
        foreach($list as $k => $v){
            $sorted_list[$v][$k] = $v;
        }
        
        //Put them into high to low order
        krsort($sorted_list);
        
        $master = array();
        
        mt_srand(333);
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
                $master[$k][$key] = $v[$key]; 
            }
        }
        
        return $master; 
    } 

    
    function table_assignment2($id){
        
        $this->db->where('id', $id);
        $ballot = $this->db->get('ballot')->row_array(0);
        $t = explode(';', $ballot['tables']);
        asort($t);
        //log_message('error', var_export($t, true));
        
        $data = array();
        $sizes = array();
        
        if($ballot['close_time'] < time()){
            $tables = $this->get_tables($id, true, true);
            
            $num_sizes = array();
            foreach($tables as $key => $table){
                $sizes[$key] = count($table);
                if(isset($num_sizes[$sizes[$key]])){
                    $num_sizes[$sizes[$key]]++;
                }else{
                    $num_sizes[$sizes[$key]] = 1;
                }
            }
            krsort($num_sizes);
            
            $sizes = $this->shuffle_sort2($sizes);
            
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
                    $res[$min] = $A[$min][0];
                    foreach($A[$min][0] as $a){
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

        
}
