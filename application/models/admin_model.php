<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends CI_Model {

    var $pages;

    function Admin_model() {
        parent::__construct(); // Call the Model constructor
    }

    function get_page_details() {
        if(!isset($this->pages)) {
            include APPPATH.'config/pages.php';
            $this->pages = $pages;
        }
    }

    function get_page_id_from_name($name) {
        $this->get_page_details();
        return $this->pages[$name]['id'];
    }

    function get_level_names($all = TRUE, $full_access = TRUE) {
        if(!$all) {
            $this->db->where('managed', 1);
            if(!$full_access) $this->db->where('full_access', 0);
        }
        $this->db->order_by('full ASC');
        return $this->db->get('levels')->result_array();
    }

    function get_level_details($id, $select) {
        if(!empty($select)) $this->db->select($select);
        $this->db->where('id', $id);
        return $this->db->get('levels')->row_array(0);
    }
    
    function update_level($id, $type, $name, $full_access, $desc){
        $this->db->where('id', $id);
        $data['full'] = $name;
        $data['type'] = $type;
        $data['full_access'] = $full_access;
        $data['description'] = $desc;
        $this->db->update('levels', $data);
    }

    function save_levels() {
        // Save existing levels
        if(isset($_POST['full_id'])) {
            foreach($_POST['full_id'] as $id => $full) {
                $this->db->where('id', $id);
                if(!isset($_POST['full_access_id'][$id]) OR $_POST['full_access_id'][$id] != 1) $_POST['full_access_id'][$id] = 0;
                $this->db->set(array('full' => $full, 'type' => $_POST['type'][$id], 'full_access' => $_POST['full_access_id'][$id]));
                if(!$this->db->update('levels')) return FALSE;

                $this->db->where('level_id', $id);
                $users_in_level = $this->db->get('level_list')->result_array();
                $users_list = array();
                foreach($users_in_level as $u) {
                    $users_list[] = $u['user_id'];
                }
                if(isset($_POST['user_id'][$id]) && !empty($_POST['user_id'][$id])) {
                    foreach($_POST['user_id'][$id] as $user) {
                        if(!in_array($user, $users_list)) {
                            $this->db->set(array('level_id' => $id, 'user_id' => $user));
                            $this->db->insert('level_list');
                        }
                    }
                    $this->db->where('level_id', $id);
                    $this->db->where_not_in('user_id', $_POST['user_id'][$id]);
                    $this->db->delete('level_list');
                }
            }
        }
        // Save newly created levels
        foreach($_POST['full'] as $num => $full) {
            if(empty($full)) continue;
            if(!isset($_POST['full_access'][$num]) OR $_POST['full_access'][$num] != 1) $_POST['full_access'][$num] = 0;
            $this->db->set(array('full' => $full, 'type' => $_POST['type'][$num], 'full_access' => $_POST['full_access'][$num]));
            if(!$this->db->insert('levels')) return FALSE;
        }
        return TRUE;
    }
    
    function add_user_level($level_id, $u_id, $year, $current){
        $data['user_id'] = $u_id;
        $data['level_id'] = $level_id;
        $data['year'] = $year;
        $data['current'] = $current;
        $this->db->insert('level_list', $data); 
    }
    
    function remove_user_level($level_id, $u_id){
        $this->db->where('level_id', $level_id);
        $this->db->where('user_id', $u_id);
        $this->db->delete('level_list');
    }

    function delete_level($id) {
        if(in_array($id, admin_levs())) return FALSE;
        // remove all occurences of this level in users
        $this->db->where('level_id', $id);
        $this->db->delete('level_list');

        // remove the level
        $this->db->where('id', $id);
        $this->db->delete('levels');

        // tidy level edit rights
        $this->db->where('level_id', $id);
        $this->db->delete('page_edit_rights');
        return TRUE;
    }

    // maintenance
    function sync_users() {
        $this->load->library('login');
        $pub = $this->load->database('public', TRUE);
        $pub->where('college', 'Josephine Butler College');
        //$pub->or_where('college', "Trevelyan College");
        $pub->or_where('department', 'Josephine Butler College');
        $all_college = $pub->get('UserDetails')->result_array();
        foreach($all_college as $p) {
            $this->db->where('username', $p['username']);
            if($this->db->get('users')->num_rows() == 0) {
                // add user
                $this->db->set($this->login->gen_temp_details($p));
                $this->db->insert('users');
            }else {
                $this->db->update('users', $this->login->gen_temp_details($p, FALSE), array('username' => $p['username']));
            }
            $unames[] = $p['username'];
        }
        // clear some variables to keep database compact
        $this->db->where_not_in('username', $unames);
        $this->db->set('rand', '');
        $this->db->set('rand_exp', '0');
        $this->db->set('level_desc', '');
        $this->db->set('current', '0');
        $this->db->set('mobile', '');
        $this->db->set('year_group', NULL);
        $this->db->set('status', NULL);
        //$this->db->set('gym', '');
        $this->db->update('users');

        $this->db->select('id, uid');
        $this->db->where_not_in('username', $unames);
        $users_left = $this->db->get('users');
        foreach($users_left as $user) {
            // remove any image of departed user
            $uid = return_array_value('uid', $user);
            foreach(array('large', 'small', 'tiny') as $size) {
                if(file_exists(VIEW_PATH.'details/img/users/'.$uid.'_'.$size.'.jpg')) unlink(VIEW_PATH.'details/img/users/'.$uid.'_'.$size.'.jpg');
            }
            // remove associated level information about user
            $id = return_array_value('id', $user);
            $this->db->where('user_id', $id);
            $this->db->delete('level_list');

            // remove associated edit rights
            $this->db->where('level_id', $id);
            $this->db->delete('page_edit_rights');
        }
    }

    function get_page_edit_rights() {
        return $this->db->get('page_edit_rights')->result_array();
    }

    function save_page_edit_rights($page_id) {
        $this->db->where('page_id', $page_id);
        if(!empty($_POST['allowed'])) $this->db->where_not_in('level_id', $_POST['allowed']);
        $this->db->delete('page_edit_rights');

        if(!empty($_POST['allowed'])){
            $ds = array();
            foreach($_POST['allowed'] as $d) {
                $ds[] = '('.$page_id.', '.$d.')';
            }
            $this->db->query('INSERT INTO page_edit_rights (page_id, level_id) VALUES '.implode(',', $ds).' ON DUPLICATE KEY UPDATE page_id=page_id');
        }
    }
    
    function level_change_current($level_id, $u_id, $new_status){
        $this->db->where('level_id', $level_id);
        $this->db->where('user_id', $u_id);
        $this->db->update('level_list', array('current'=>$new_status));
    }
    
    function update_menu(){
        
        $data = array();
        $i = 0;
        $n = 0;
        while(1){
            if(isset($_POST['menu-'.$i])){
                $data[] = array(
                    'id' => ++$n,
                    'display_name' => $_POST['menu-'.$i],
                    'link' => $_POST['link-'.$i]==''?NULL:$_POST['link-'.$i],
                    'level' => 0,
                    'parent_id' => NULL,
                    'sort_order' => $n*3
                );
                $grandparent_id = $n;
            }else{
                break;
            }
            $ii = 0;
            while(1){
                if(isset($_POST['menu-'.$i.'-'.$ii])){
                    $data[] = array(
                        'id' => ++$n,
                        'display_name' => $_POST['menu-'.$i.'-'.$ii],
                        'link' => $_POST['link-'.$i.'-'.$ii]==''?NULL:$_POST['link-'.$i.'-'.$ii],
                        'level' => 1,
                        'parent_id' => $grandparent_id,
                        'sort_order' => $n*3
                    );
                    $parent_id = $n;
                }else{
                    break;
                }
                $iii = 0;
                while(1){
                    if(isset($_POST['menu-'.$i.'-'.$ii.'-'.$iii])){
                        $data[] = array(
                            'id' => ++$n,
                            'display_name' => $_POST['menu-'.$i.'-'.$ii.'-'.$iii],
                            'link' => $_POST['link-'.$i.'-'.$ii.'-'.$iii]==''?NULL:$_POST['link-'.$i.'-'.$ii.'-'.$iii],
                            'level' => 2,
                            'parent_id' => $parent_id,
                            'sort_order' => $n*3
                        );
                    }else{
                        break;
                    }
                    $iii++;
                }
                $ii++;
            }
            $i++;
        }
        
        $this->db->empty_table('menu_structure');
        $this->db->insert_batch('menu_structure', $data);
        
    }
    
    function get_ex_students(){
        $this->db->where('current', 0);
        $this->db->where('account_disabled', 0);
        $this->db->order_by('surname');
        return $this->db->get('users')->result_array();
    }
    
    function reset_password(){
        $this->db->where('current', 0);
        $this->db->where('id', $_POST['user-id']);
        $charUniverse = 'abcdefghjkmnpqrstuvwxyz23456789';
        $new_password = '';
        for($i=0; $i<8; $i++){
            $new_password .= substr($charUniverse, mt_rand(0, strlen($charUniverse)-1), 1);
        }
        $salt = $this->get_salt();
        $this->db->update('users', array('password_hash'=>crypt($new_password, $salt), 'temporary_password'=>1));
        $this->db->where('id', $_POST['user-id']);
        $data = $this->db->get('users')->row_array(0);
        $data['password'] = $new_password;
        return $data;
    }
    
    function email_user_new_level($level_id, $u_id){
        
    }
    
    function get_salt(){
        $salt = '$2y$08$';
        $charUniverse = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for($i=0; $i<22; $i++){
            $salt .= substr($charUniverse, mt_rand(0, 61), 1);
        }
        return $salt;
    }
}