<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Charities_model extends CI_Model {

    function Charities_model() {
        parent::__construct();
    }

    function get_album($album_id) {
        $this->db->where('id', $album_id);
        return $this->db->get('charity_albums')->row_array(0);
    }

    function get_albums() {
        $this->db->order_by('id DESC');
        return $this->db->get('charity_albums')->result_array();
    }

    function get_photos($album_id) {
        $this->db->where('album_id', $album_id);
        $this->db->order_by('id ASC');
        return $this->db->get('charity_photos')->result_array();
    }

    function get_photo($photo_id) {
        $this->db->where('id',$photo_id);
        return $this->db->get('charity_photos')->row_array(0);
    }

    function delete_photo($photo_id) {
        $this->db->where('id', $photo_id);
        $this->db->delete('charity_photos');
    }

    function delete_album($album_id) {
        $this->db->where('id', $album_id);
        $this->db->delete('charity_albums');
    }

    function get_sizes() {
        $this->db->order_by('description ASC');
        return $this->db->get('charity_sizes')->result_array();
    }

    function get_price($sizes, $id) {
        foreach($sizes as $s) {
            if($s['id'] == $id) {
                return $s['price'];
            }
        }
        return 0.00;
    }

    function update_size() {
        $this->db->where('id', $this->input->post('id'));
        $this->db->set(array(
            'description' => $this->input->post('description'),
            'price' => $this->input->post('price'),
        ));
        $this->db->update('charity_sizes');
    }

    function delete_size() {
        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('charity_sizes');
    }

    function add_size() {
        $this->db->set(array(
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
        ));
        $this->db->insert('charity_sizes');
    }

    function get_orders() {
        $this->db->order_by('order_time DESC');
        $this->db->order_by('order_id ASC');
        return $this->db->get('charity_orders')->result_array();
    }
    
    function charities_permissions(){
        return has_level(array(57,99)) or is_admin();
    }
    
    function get_dare_night_events(){
        return $this->db->get('charity_darenight_events')->result_array();
    }
    
    function get_dare_night($id){
        $this->db->where('id', $id);
        return $this->db->get('charity_darenight_events')->row_array(0);
    }
    
    function get_dare_team($dare_id, $user_id){
        $this->db->where('(`team_member_1`', "'".$user_id."'", false);
        $this->db->or_where('team_member_2', $user_id);
        $this->db->or_where('team_member_3', $user_id);
        $this->db->or_where('team_member_4', $user_id);
        $this->db->or_where('team_member_5', $user_id);
        $this->db->or_where('`team_member_6`', "'".$user_id."')", false); 
        $this->db->where('darenight_event_id', $dare_id);
        return $this->db->get('charity_darenight_teaminfo')->result_array();
    }
    
    function get_dare_night_entry($dare_id, $user_id){
        
        $team = $this->get_dare_team($dare_id, $user_id);
        if(empty($team)){
            $this->db->insert('charity_darenight_teaminfo', array('darenight_event_id'=>$dare_id, 'team_member_1'=>$user_id));
            $team = $this->get_dare_team($dare_id, $user_id);
        }
        
        for($i=0;$i<sizeof($team);$i++){
            $photos = $this->db->get_where('charity_darenight_evidence', array('team_num' => $team[$i]['id']))->result_array();
            $team[$i]['photos'] = array();
            foreach($photos as $p){
                $team[$i]['photos'][$p['dare_num']] = $p;
            }
        }
        if(sizeof($team) == 1){
            $team = $team[0];
        }        
        return $team;    
    }
    
    function dare_update_team($id, $name, $member1, $member2, $member3, $member4, $member5, $member6){
        $this->db->where('id', $id);
        $this->db->update('charity_darenight_teaminfo', array('team_name'=>$name, 'team_member_1'=>$member1, 'team_member_2'=>$member2, 'team_member_3'=>$member3, 'team_member_4'=>$member4, 'team_member_5'=>$member5, 'team_member_6'=>$member6)); 
    }
    
    function dare_add_file($team_id, $dare_num, $file_name, $details){
        $data = array(
            'team_num'=>$team_id,
            'dare_num'=>$dare_num,
            'file_name'=>$file_name,
            'details'=>$details
        );
        $this->db->insert('charity_darenight_evidence', $data);
    }
    
    function remove_photo($team_id, $dare_num){
        $this->db->where('team_num', $team_id);
        $this->db->where('dare_num', $dare_num);
        $record = $this->db->get('charity_darenight_evidence')->row_array(0);
        if(!empty($record)){
            unlink(VIEW_PATH.'charities/dare_night/images/'.$record['file_name']);
            $this->db->where('team_num', $team_id);
            $this->db->where('dare_num', $dare_num);
            $this->db->delete('charity_darenight_evidence'); 
        }
    }
    
    function dare_update_details($team_id, $dare_num, $details){
        $this->db->where('team_num', $team_id);
        $this->db->where('dare_num', $dare_num);
        $this->db->update('charity_darenight_evidence', array('details'=>$details)); 
    }
    
    function confirm_dare($team_id, $new_status=1){
        $this->db->where('id', $team_id);
        $this->db->update('charity_darenight_teaminfo', array('submitted'=>$new_status)); 
    }
    
    function get_dare_submissions($event_id, $count_photos = false){
        $this->db->select('charity_darenight_teaminfo.id, charity_darenight_teaminfo.team_name, charity_darenight_teaminfo.team_member_1, charity_darenight_teaminfo.team_member_2, charity_darenight_teaminfo.team_member_3, charity_darenight_teaminfo.team_member_4, charity_darenight_teaminfo.team_member_5, charity_darenight_teaminfo.team_member_6, charity_darenight_teaminfo.submitted, users.firstname, users.prefname, users.surname');
        $this->db->where('darenight_event_id', $event_id);
        $this->db->order_by('submitted desc, charity_darenight_teaminfo.id asc');
        $this->db->join('users', 'users.id=charity_darenight_teaminfo.team_member_1');
        $submissions = $this->db->get('charity_darenight_teaminfo')->result_array();
        if($count_photos){
            for($i=0;$i<sizeof($submissions);$i++){
                $this->db->where('team_num', $submissions[$i]['id']);
                $this->db->from('charity_darenight_evidence');
                $submissions[$i]['completed'] = $this->db->count_all_results();
            } 
        }
        return $submissions;
    }
    
    function get_darenight_team($team_no, $photos = false){
        $this->db->where('id', $team_no);
        $submissions = $this->db->get('charity_darenight_teaminfo')->row_array(0);
        if($photos){
            $photos = $this->db->get_where('charity_darenight_evidence', array('team_num' => $team_no))->result_array();
            $submissions['photos'] = array();
            foreach($photos as $p){
                $submissions['photos'][$p['dare_num']] = $p;
            }
        }
        return $submissions;
    }
    
}

