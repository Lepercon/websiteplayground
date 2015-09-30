<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class signup_model extends CI_Model {


    function signup_model() {
        parent::__construct();
    }

    function get_signups($event_id = NULL) {
        if(!is_null($event_id)) $this->db->where('event_id', $event_id);
        $this->db->order_by('event_time', 'desc');
        $signups = $this->db->get('signup')->result_array();
        if(empty($signups)) return FALSE;
        foreach($signups as &$s) {
            $s['user_has_booking'] = $this->user_has_booking($s['id']);
        }
        return $signups;
    }

    function get_signup($id, $add_tables = TRUE) {
        $this->db->where('id', $id);
        $signup = $this->db->get('signup')->row_array(0);
        if(empty($signup)) return FALSE;
        // check for user booking
        $signup['user_has_booking'] = $this->user_has_booking($signup['id']);

        $this->db->where(array(
            'signup_id' => $signup['id'],
            'reserved_by' => $this->session->userdata('id')
        ));
        $signup['num_user_reservations'] = $this->db->get('signup_people')->num_rows();

        if($add_tables) $signup = $this->add_people($signup);
        return $signup;
    }

    function add_people($signup) {
        $this->db->where('signup_id', $signup['id']);
        $this->db->order_by('sort_order ASC, last_update ASC');
        $people = $this->db->get('signup_people')->result_array();
        $signup['tables'] = array_fill(1, substr_count($signup['seats'], ",") + 1, array('seats' => array()));
        foreach($people as $b) {
            $table =& $signup['tables'][$b['table_num']];
            $table['seats'][] = array(
                'id' => $b['id'],
                'name' => (isset($b['user_id']) && !empty($b['user_id']) ? $this->users_model->get_full_name($b['user_id']) : $b['name']),
                'reserved_by' => $b['reserved_by']
            );
        }
        return $signup;
    }

    function user_has_booking($signup_id) {
        // For the given signup, determine if a user already has a booking.
        $this->db->where(array('signup_id' => $signup_id, 'user_id' => $this->session->userdata('id')));
        if($this->db->get('signup_people')->num_rows() > 0) return TRUE;
        else return FALSE;
    }

    function check_signup($e = null) {
        // Validate the signup editor and prepare some fields for database entry.
        $times_array = array('event_time', 'signup_opens', 'signup_closes');
        if($_POST['type']==2) {
            $times_array = array_merge($times_array, array('swapping_opens', 'swapping_closes'));
            $this->form_validation->set_rules('swap_price', 'Swap Price', 'trim|max_length[50]|xss_clean');
        }

        $this->form_validation->set_rules('event_id', 'Event ID (Hidden Field, please contact the administrator)', 'trim|required|max_length[11]|integer');
        $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[50]|xss_clean');
        $this->form_validation->set_rules('dress_code', 'Dress Code', 'trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('price', 'Price', 'trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('pickup', 'Pickup Locations', 'trim|max_length[200]|xss_clean');
        $this->form_validation->set_rules('meet_hour', 'Meet Hour', 'trim|less_than[24]|integer|max_length[2]');
        $this->form_validation->set_rules('meet_min', 'Meet min', 'trim|less_than[60]|integer|max_length[2]');
        $this->form_validation->set_rules('meet_location', 'Meet location', 'trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('sets', 'Signup Sets', 'trim|required|integer|max_length[1]');
        $this->form_validation->set_rules('notes', 'Notes', 'trim|max_length[1000]|xss_clean');
        $this->form_validation->set_rules('permission', 'Permissions', 'trim|xss_clean');

        foreach($times_array as $v) {
            $this->form_validation->set_rules($v.'_date', ucwords(str_replace('_', ' ', $v)).' Date', 'trim|max_length[10]|required');
            $this->form_validation->set_rules($v.'_hour', ucwords(str_replace('_', ' ', $v)).' Hour', 'trim|less_than[24]|required|integer|max_length[2]');
            $this->form_validation->set_rules($v.'_minute', ucwords(str_replace('_', ' ', $v)).' Minute', 'trim|less_than[60]|required|integer|max_length[2]');
        }
        
        if($_POST['type']==1 || $_POST['type']==2) {
            foreach(array('starters', 'mains', 'desserts', 'drinks') as $v) {
                $this->form_validation->set_rules($v, ucfirst($v), 'trim|max_length[200]|xss_clean');
            }
            
        }
        if($this->form_validation->run()) {
            $errors = array();
            // check dates
            foreach($times_array as $v) {
                $var = explode("/", $this->input->post($v.'_date'));
                if(!checkdate($var[1], $var[0], $var[2])) $errors[] = ucfirst(str_replace('_', ' ', $v)).' date is invalid';
                $_POST[$v] = mktime($_POST[$v.'_hour'], $_POST[$v.'_minute'], 0, $var[1], $var[0], $var[2]);
            }

            if($_POST['event_time'] < $_POST['signup_closes']) $errors[] = 'Signup closes after the event';
            if($_POST['signup_opens'] >= $_POST['signup_closes']) $errors[] = 'Signup open is after signup close';
            if(!empty($_POST['swap_price'])) {
                if($_POST['event_time'] < $_POST['swapping_closes']) $errors[] = 'Swapping closes after the event';
                if($_POST['swapping_opens'] >= $_POST['swapping_closes']) $errors[] = 'Swapping open is after swapping close';
            }

            return $errors;
        }
        else return FALSE;
    }

    function save_signup($e_id = null, $e = null) {
        // set empty text fields to an empty text string
        foreach(array('meet_location', 'notes', 'starters', 'mains', 'desserts', 'drinks', 'pickup', 'permission') as $v) {
            if(empty($_POST[$v])) $_POST[$v] = '';
        }

        $seat_total = 0;
        foreach($_POST['seats'] as $s) {
            $seat_total = $seat_total + $s;
        }
        $seats = implode(",", $_POST['seats']);
        $_POST['seats'] = $seats;

        $tables = implode(",", $_POST['table_names']);
        $_POST['table_names'] = $tables;

        // convert html chars, add line breaks
        $_POST['notes'] = textarea_to_db($_POST['notes']);

        foreach(array($_POST['price'], $_POST['swap_price']) as $v) {
            // remove pound symbol if one has been entered
            if(strpos($v, '£') === 0) {
                $price = substr($v, strlen('£'));
                if(is_numeric($price)) $v = $price;
            }
            // convert html chars
            $v = htmlentities($v, ENT_QUOTES,'UTF-8');
        }

        // create submit array
        foreach($_POST as $k => $v) if(in_array($k, array('event_id', 'type', 'name', 'event_time', 'meet_hour', 'meet_min', 'meet_location', 'dress_code', 'price', 'notes', 'starters', 'mains', 'desserts', 'drinks', 'table_names', 'seats', 'pickup', 'sets', 'signup_opens', 'signup_closes', 'swapping_opens', 'swapping_closes', 'swap_price', 'permission'))) $submit[$k] = $v;
        
        foreach($_POST as $k => $v) if(in_array($k, array('event_id', 'table_names', 'seats'))) $table_submit[$k] = $v;

        if(!empty($submit)) {
            if(is_null($e)) {
                $submit['created_on'] = time();
                $submit['created_by'] = $this->session->userdata('id');
                $submit['seats_remain'] = $seat_total;
            }
            else {
                $this->db->where('signup_id', $e_id);
                $submit['seats_remain'] = $seat_total - $this->db->get('signup_people')->num_rows();
            }
            $this->db->set($submit);
            if(is_null($e)) {
                $this->db->insert('signup');                
                return $this->db->insert_id();
            }
            else {
                $this->db->where('id', $e_id);
                $this->db->update('signup');
                return $e_id;
            }
        }
    }

    function cancel_signup($e_id) {
        $this->db->where('id', $e_id);
        $this->db->delete('signup');
        $this->db->where('signup_id', $e_id);
        $this->db->delete('signup_people');
        $this->db->where('signup_id', $e_id);
        $this->db->delete('signup_swaps');
    }

    // signup stuff

    function check_reservation($signup) {
        if(!empty($signup['swap_price'])) {
            if($_POST['reserve'] > 2) $errors[] = 'Too many reservations';
            if($_POST['reserve'] < 2) $errors[] = 'Too few reservations';
        } else {
            if($_POST['reserve'] > 4) $errors[] = 'Too many reservations';
            if($_POST['reserve'] < 1) $errors[] = 'Too few reservations';
        }

        if(empty($signup['swap_price']) && !is_numeric($_POST['table']) && $_POST['table'] != 'any') $errors[] = 'Invalid table value';
        // return now as next part requires these to be valid
        if(!empty($errors)) return $errors;

        if(!empty($signup['swap_price'])) $_POST['table'] = 'any';
        if($_POST['table'] == 'any') {
            $_POST['table'] = $this->get_table_number($signup, $_POST['reserve']);
            if($_POST['table'] === FALSE) {
                $_POST['table'] = 'any'; // for select value
                $errors[] = 'Not enough spaces on any table, please reserve a smaller group';
            }
        }
        else {
            $seats = explode(",", $signup['seats']);
            if($_POST['table'] <= 0 OR $_POST['table'] > substr_count($signup['seats'], ",") + 1) $errors[] = 'Not a valid table';
            if($seats[$_POST['table'] - 1] - count($signup['tables'][$_POST['table']]['seats']) < $_POST['reserve']) $errors[] = 'Not enough spaces on this table';
        }
        if(empty($errors)) return TRUE;
        else return $errors;
    }

    function get_table_number($signup, $size) {
        //log_message('error', var_export($signup, true));
        $seats = explode(",", $signup['seats']);
        $keys = array_keys($signup['tables']);
        //log_message('error', var_export($keys, true));
        //shuffle($keys);
        //log_message('error', var_export($keys, true));
        foreach($keys as $num) {
            $t = $signup['tables'][$num];
            if($seats[$num - 1] - count($t['seats']) >= $size) 
                return $num;
        }
        return FALSE;
    }

    function add_reservation($e_id, $signup) {
        //insert entries
        $vals = array('signup_id' => $e_id, 'table_num' => $_POST['table'], 'reservation' => 1, 'reserved_until' => time() + 15*60, 'reserved_by' => $this->session->userdata('id'), 'last_update' => time(), 'sort_order'=>$this->session->userdata('id')); // reserved for 15 mins
        $ids = array();
        for($i=0; $i<$_POST['reserve'];$i++){
            $this->db->insert('signup_people', $vals);
            $ids[] = $this->db->insert_id();
        }
        log_message('error', 'User: '.$this->session->userdata('id').' made '.$_POST['reserve'].' reservations on table '.$_POST['table'].' - '.var_export($ids, true));
        
        
        //check that somebody else didn't make a reservation after we checked if there were spaces
        $this->db->where('signup_id', $e_id);
        $this->db->where('table_num', $_POST['table']); //find all current reservations on our table 
        $this->db->order_by('id', 'asc'); 
        $this->db->from('signup_people');
        $reservations = $this->db->get()->result_array();
        $seats = explode(",", $signup['seats']);
        if(sizeof($reservations) > $seats[$_POST['table'] - 1]){
            log_message('error', 'overbooked');
            for($i=0;$i<sizeof($reservations);$i++){
                if(($i+1)>$seats[$_POST['table'] - 1]){
                    if(in_array($reservations[$i]['id'], $ids)){
                        log_message('error', 'overbooked - detected after');
                        foreach($ids as $id){
                            $this->db->delete('signup_people', array('id' => $id)); 
                        }
                        return false;
                    }
                }
            }
        }
        
        
        // update remaining
        $this->db->where('id', $e_id);
        $this->db->set('seats_remain', 'seats_remain - '.($_POST['reserve']), FALSE);
        $this->db->update('signup');
        return true;
    }

    function cancel_reservations($e_id, $swapping, $admin=false) {
        if($swapping && !$admin) {
            $this->db->where(array('signup_id' => $e_id, 'reserved_by' => $this->session->userdata('id')));
        }else{
            $this->db->where(array('signup_id' => $e_id, 'reserved_by' => $this->session->userdata('id'), 'reservation' => 1));
        }
        $this->db->select('reserved_by, signup_id, table_num, reserved_until, count(id) as num');
        $this->db->group_by('reserved_by, signup_id, table_num');
        $removals = $this->db->get('signup_people')->result_array();
        $data = array();
        foreach($removals as $k => $r){
            $data[$k]['timestamp'] = time();
            $data[$k]['signup_id'] = $r['signup_id'];
            $data[$k]['user_id'] = $r['reserved_by'];
            $data[$k]['table_num'] = $r['table_num'];
            $data[$k]['num_seats'] = -$r['num'];
        }
        if(sizeof($data) > 0){
            $this->db->insert_batch('signup_attempts', $data);
        }

        if($swapping && !$admin) {
            $this->db->where(array('signup_id' => $e_id, 'reserved_by' => $this->session->userdata('id')));
        }else{
            $this->db->where(array('signup_id' => $e_id, 'reserved_by' => $this->session->userdata('id'), 'reservation' => 1));
        }
        $this->db->delete('signup_people');
        $num = $this->db->affected_rows();
        $this->db->where('id', $e_id);
        $this->db->set('seats_remain', 'seats_remain + '.$num, FALSE);
        $this->db->update('signup');
    }

    function get_first_reservation($e_id) {
        $this->db->where(array('signup_id' => $e_id, 'reserved_by' => $this->session->userdata('id'), 'reservation' => 1));
        return $this->db->get('signup_people')->row_array(0);
    }

    function get_num_reservations($e_id) {
        $this->db->select('id');
        $this->db->where(array('signup_id' => $e_id, 'reserved_by' => $this->session->userdata('id'), 'reservation' => 1));
        return $this->db->get('signup_people')->num_rows();
    }

    function check_reservation_details($signup) {
        foreach(array('starter', 'main', 'dessert', 'drink') as $course) if(!empty($signup[$course.'s']) && !empty($_POST[$course]) && strpos($signup[$course.'s'], $_POST[$course]) === FALSE) $errors[] = 'Invalid '.$course;

        // check if user is already booked into signup
        if($this->session->userdata('skip_name_search') == FALSE) {
            if(!isset($_POST['user_id']) OR $_POST['user_id'] === '') {
                $this->db->select('name');
                $this->db->like('name', str_replace(' ', '% %', trim($_POST['name'])), null, FALSE); // wrap with wildcards and search
            }
            else {
                $this->db->select('user_id');
                $this->db->where('user_id', $_POST['user_id']);
            }
            $this->db->where(array('signup_id' => $signup['id']));
            $people = $this->db->get('signup_people')->result_array();
            if(count($people) > 0) {
                if(!(isset($_POST['user_id']) && $_POST['user_id'] !== '')) {
                    foreach($people as $p) $err[] = $p['name'];
                    $errors[] = 'The name you entered matches a booking for the following people: '.implode(', ', $err).'. Submit again if you are entering someone else not listed';
                    $this->session->set_userdata('skip_name_search', TRUE);
                }
                else $errors[] = 'The user you entered already has a booking';
            }
        }
        else $this->session->unset_userdata('skip_name_search'); // remove old var
        if(empty($errors)) return TRUE;
        else return $errors;
    }

    function add_reservation_details($e_id) {
        $data['reservation'] = 0;
        $data['last_update'] = time();
        foreach(array('starter', 'main', 'dessert', 'drink', 'special', 'pickup') as $v) if(isset($_POST[$v])) $data[$v] = $_POST[$v];
        if(empty($_POST['name']) && !empty($_POST['user_id'])) $_POST['name'] = $this->users_model->get_full_name($_POST['user_id']);
        foreach(array('name', 'user_id') as $w) if(isset($_POST[$w]) && $_POST[$w] !== '') $data[$w] = $_POST[$w];
        $res = $this->get_first_reservation($e_id);
        $this->db->where('id', $res['id']);
        $this->db->set($data);
        $this->db->update('signup_people');
        log_message('error', $this->session->userdata('id').' - '.var_export($res, true).' - '.var_export($data, true));
    }

    function get_reservations($e_id) {
        $this->db->select('signup_people.*, CONCAT(u1.surname, \', \', u1.firstname) AS uname, CONCAT(u2.surname, \', \', u2.firstname) AS booked_by, u1.email', FALSE);
        $this->db->where('signup_id', $e_id);
        $this->db->from('signup_people');
        $this->db->join('users AS u1', 'u1.id = signup_people.user_id', 'left');
        $this->db->join('users AS u2', 'u2.id = signup_people.reserved_by', 'left');
        $this->db->order_by('table_num ASC, uname ASC');
        return $this->db->get()->result_array();
    }

    function get_catering($e_id) {
        $this->db->where('signup_id', $e_id);
        $this->db->order_by('table_num ASC, drink ASC');
        return $this->db->get('signup_people')->result_array();
    }

    function delete_booking($e_id, $b_id) {
        $this->db->where('id', $b_id);
        $this->db->delete('signup_people');
        $this->db->where('id', $e_id);
        $this->db->set('seats_remain', 'seats_remain + 1', FALSE);
        $this->db->update('signup');
    }
    
    //function get_group_reservation($e_id, $signup) {
    //    $this->db->where('signup_id', $e_id, 'reserved_by', $_POST['user_id']);
    //    $this->db->order_by('table_num ASC, drink ASC');
    //    return $this->db->get('signup_people')->result_array();
    //}

// Swapping Functions

    function get_group_reservations($e_id) {
        if($e_id == 158){
            $this->db->select('u1.id AS id1, u1.table_num AS table1, u1.name AS name1, u2.id AS id2, u2.name AS name2', FALSE);
            $this->db->where('u1.signup_id', $e_id);
            $this->db->from('signup_people AS u1');
            $this->db->join('signup_people AS u2', 'u1.reserved_by = u2.reserved_by AND u1.reserved_until = u2.reserved_until AND u1.table_num = u2.table_num', 'inner');
            $this->db->order_by('name1 ASC');
        }else{
            $this->db->select('u1.id AS id1, u1.table_num AS table1, u1.name AS name1, u2.id AS id2, u2.name AS name2', FALSE);
            $this->db->where('u1.signup_id', $e_id);
            $this->db->from('signup_people AS u1');
            $this->db->join('signup_people AS u2', 'u1.reserved_by = u2.reserved_by AND u1.reserved_until = u2.reserved_until AND u1.table_num = u2.table_num AND u1.name != u2.name', 'inner');
            $this->db->order_by('name1 ASC');
        }
        return $this->db->get()->result_array();
    }

    function get_num_swaps($e_id) {
        $this->db->where(array('movement_by' => $this->session->userdata('id'), 'signup_id' => $e_id));
        $this->db->from('signup_swaps');
        return $this->db->count_all_results();
    }

    function get_swap_totals($e_id) {
        $this->db->where('signup_id', $e_id);
        $this->db->order_by('time ASC');
        return $this->db->get('signup_swaps')->result_array();
    }

    function get_movements($e_id) {
        $ret = array();
        foreach($this->get_swap_totals($e_id) as $s) {
            $pair1 = explode(";", $s['pair1']);
            $pair2 = explode(";", $s['pair2']);
            $this->db->where('id',$pair1[0]);
            $a = $this->db->get('signup_people')->row_array();
            $this->db->where('id',$pair1[1]);
            $b = $this->db->get('signup_people')->row_array();
            $this->db->where('id',$pair2[0]);
            $c = $this->db->get('signup_people')->row_array();
            $this->db->where('id',$pair2[1]);
            $d = $this->db->get('signup_people')->row_array();
            $ret[] = array('movement_by' => $this->users_model->get_full_name($s['movement_by']), 'movement_of' => '<b>'.$a['name'].'</b> & <b>'.$b['name'].'</b> ('.$pair1[2].') with <b>'.$c['name'].'</b> & <b>'.$d['name'].'</b> ('.$pair2[2].') at '.date("G:i:s M j", $s['time']));
        }
        return $ret;
    }

    function check_swap($signup) {
        if(empty($signup['swap_price'])) $errors[] = 'Swapping is not allowed in this signup, please contact the administrator';

        $pair1 = explode(";", $_POST['pair1']);
        $pair2 = explode(";", $_POST['pair2']);
        $this->db->where(array('id' => $pair1[0], 'table_num' => $pair1[2], 'signup_id' => $signup['id']));
        $a = $this->db->get('signup_people')->num_rows();
        $this->db->where(array('id' => $pair1[1], 'table_num' => $pair1[2], 'signup_id' => $signup['id']));
        $b = $this->db->get('signup_people')->num_rows();
        $this->db->where(array('id' => $pair2[0], 'table_num' => $pair2[2], 'signup_id' => $signup['id']));
        $c = $this->db->get('signup_people')->num_rows();
        $this->db->where(array('id' => $pair2[1], 'table_num' => $pair2[2], 'signup_id' => $signup['id']));
        $d = $this->db->get('signup_people')->num_rows();
        if($a != 1 OR $b != 1 OR $c != 1 OR $d != 1) $errors[] = 'Someone else has just swapped one of these pairs.';
        //if($pair1[2] == $pair2[2]) $errors[] = 'The selected pairs are already on the same table so cannot be swapped.';
        if(empty($errors)) return TRUE;
        else return $errors;
    }

    function add_swap($e_id) {
        $pair1 = explode(";", $_POST['pair1']);
        $res1 = $this->db->get_where('signup_people', array('id'=>$pair1[0]))->row_array(0);
        $pair1['table'] = $res1['table_num'];
        $pair2 = explode(";", $_POST['pair2']);
        $res2 = $this->db->get_where('signup_people', array('id'=>$pair2[0]))->row_array(0);
        $pair2['table'] = $res2['table_num'];
        foreach(array($pair1[0], $pair1[1]) as $p) {
            $this->db->where('id', $p);
            $this->db->update('signup_people',array('table_num' => $pair2['table'], 'last_update' => time(), 'sort_order'=>$res2['sort_order']));
        }
        foreach(array($pair2[0], $pair2[1]) as $p) {
            $this->db->where('id', $p);
            $this->db->update('signup_people',array('table_num' => $pair1['table'], 'last_update' => time(), 'sort_order'=>$res1['sort_order']));
        }
        $this->db->set('signup_id', $e_id);
        $this->db->set('movement_by', $this->session->userdata('id'));
        $this->db->set('pair1', $_POST['pair1']);
        $this->db->set('pair2', $_POST['pair2']);
        $this->db->set('time',time());
        $this->db->insert('signup_swaps');
    }

// Maintenance Functions

    function delete_old_signups() {
        /*
        // delete old signups and associated bookings
        $old_signup_cutoff = (time() - 10*24*60*60);
        $this->db->select('id');
        $this->db->where('event_time <', $old_signup_cutoff);
        $to_delete = $this->db->get('signup')->result_array();
        foreach($to_delete as $e) $this->cancel_signup($e['id']);

        // delete expired reservations
        $this->db->where(array('reserved_until < ' => time(), 'reservation' => 1));
        $old = $this->db->get('signup_people')->result_array();
        foreach($old as $o) {
            $this->db->where('id', $o['signup_id']);
            $this->db->set('seats_remain', 'seats_remain + 1', FALSE);
            $this->db->update('signup');
            $this->db->where('id', $o['id']);
            $this->db->delete('signup_people');
        }

        // optimize database tables following large deletion
        if(!empty($to_delete)) {
            $this->load->dbutil();
            foreach(array('signup','signup_people') as $del) $this->dbutil->optimize_table($del);
        }*/
    }

// Admin Function

    function check_permission($signup) {
        // check user has a level which allows full access to current signup
        if($this->session->userdata('level_list') != FALSE) {
            $levels = explode(',',$this->session->userdata('level_list'));
            foreach($levels as $l) {
                if($l == 1 OR $l == 2 OR $l == 16) {
                    // Webmaster = 1, president = 2, vice-president = 3, social chair = 16
                    return TRUE;
                }
                if(!empty($signup['permission']) && $signup['permission'] == $l) {
                    // Matches individual signup permission
                    return TRUE;
                }
            }
            return FALSE;
        }
        else return FALSE;
    }
    
    function check_permissions(){
        if($this->session->userdata('level_list') != FALSE) {
            $levels = explode(',',$this->session->userdata('level_list'));
            $admin_levels = array('1', '2', '3', '16');// Webmaster = 1, president = 2, vice-president = 3, social chair = 16
            foreach($levels as $l) {
                if(in_array($l, $admin_levels)) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    
    function remove_expired(){
        //record the ones being removed
        $time = time();
        $this->db->select('reserved_by, signup_id, table_num, reserved_until, count(id) as num');
        $this->db->where('reserved_until <', $time);
        $this->db->where('reservation', 1);
        $this->db->group_by('reserved_by, signup_id, table_num');
        $removals = $this->db->get('signup_people')->result_array();
        
        //remove
        $this->db->where('reserved_until <', $time);
        $this->db->where('reservation', 1);
        $this->db->delete('signup_people');
        
        //record
        
        $data = array();
        foreach($removals as $k => $r){
            $data[$k]['timestamp'] = $r['reserved_until'];
            $data[$k]['signup_id'] = $r['signup_id'];
            $data[$k]['user_id'] = $r['reserved_by'];
            $data[$k]['table_num'] = $r['table_num'];
            $data[$k]['num_seats'] = -$r['num'];
        }
        if(sizeof($data) > 0){
            $this->db->insert_batch('signup_attempts', $data);
        }
    }
    
    function get_attempts($e_id){
        $this->db->select('signup_attempts.*, users.firstname, users.prefname, users.surname, count(signup_people.id) as reservations');
        $this->db->where('signup_attempts.signup_id', $e_id);
        $this->db->join('users', 'users.id=signup_attempts.user_id');
        $this->db->join('signup_people', 'signup_people.signup_id=signup_attempts.signup_id and signup_people.reserved_by=signup_attempts.user_id', 'left outer');
        $this->db->order_by('reservations, signup_attempts.user_id, signup_attempts.timestamp');
        $this->db->group_by('signup_attempts.id');
        return $this->db->get('signup_attempts')->result_array();
    }
    
    function add_attempt($e_id){
        $this->db->where('signup_id', $e_id);
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->where('table_num', NULL);
        $attempt = $this->db->get('signup_attempts')->row_array(0);
        if(isset($_POST['reserve'])){
            if(sizeof($attempt) > 0){
                $data['table_num'] = $this->input->post('table', true);
                $data['num_seats'] = $this->input->post('reserve', true);
                $data['timestamp'] = time();
                $this->db->update('signup_attempts', $data, array('id' => $attempt['id']));
                return;
            }else{
                $data['signup_id'] = $e_id;
                $data['user_id'] = $this->session->userdata('id');
                $data['table_num'] = $this->input->post('table', true);
                $data['num_seats'] = $this->input->post('reserve', true);
                $data['timestamp'] = time();
            }
        }else{
            if(sizeof($attempt) == 0){
                $data['signup_id'] = $e_id;
                $data['user_id'] = $this->session->userdata('id');
                $data['timestamp'] = time();
            }else{
                return;
            }
        }
        $this->db->insert('signup_attempts', $data);
    }

}
