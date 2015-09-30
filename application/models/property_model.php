<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class property_model extends CI_Model {
    
    function property_model() {
        parent::__construct();
    }
    
    function add_property() {
        $submit['created'] = time();
        $submit['created_by'] = $this->session->userdata('id');
        foreach($_POST as $k => $v) if(in_array($k, array('name', 'type', 'area', 'address1', 'address2', 'bedrooms', 'bathrooms', 'postcode'))) $submit[$k] = $v;            
        $this->db->set($submit);
        $this->db->insert('property');
    }
    
    function insert_coords($coords, $p_id) {
        $this->db->set($coords);
        $this->db->where('id', $p_id);
        $this->db->update('property');
    }
        
    function get_reviews_for_property($property_id) {
        $this->db->select('property_surveys.*, users.email');
        $this->db->where('property_id', $property_id);
        $this->db->join('users', 'users.id=property_surveys.created_by');
        return $this->db->get('property_surveys')->result_array();
    }
    
    function get_photos_for_property($property_id) {
        $this->db->where('property_id', $property_id);
        $this->db->order_by('id ASC');
        return $this->db->get('property_photos')->result_array();
    }
    
    function get_property_from_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('property')->row_array(0);
    }
    
    function get_property_from_codes($name, $postcode) {
        $this->db->where(array('name'=>$name,'postcode'=>$postcode));
        $ret = $this->db->get('property')->row_array();
        if(empty($ret)) return FALSE;
        else return $ret;
    }
    
    function save_review($property) {
        // Update information on property first
        if($_POST['area']=='Other') $_POST['area'] = $_POST['other_area'];
        foreach($_POST as $k => $v) if(in_array($k, array('type', 'area', 'bedrooms', 'bathrooms'))) $update[$k] = $v;            
        $this->db->set($update);
        $this->db->where('id', $property['id']);
        $this->db->update('property');
        
        // Add review
        foreach(array('landlord_responsive','neighbours','problems','comments') as $a) $_POST[$a] = textarea_to_db($_POST[$a]);
        foreach($_POST as $k => $v) if(in_array($k, array('rent_cost','bills_included','bills_cost','time_college','time_town','property_rating','living_area_rating','bedrooms_rating','bathrooms_rating','landlord_rating','landlord','landlord_responsive','neighbours','problems','comments','recommend','allow_contact'))) $submit[$k] = $v;
        $submit['property_id'] = $property['id'];
        $submit['created'] = time();
        $submit['created_by'] = $this->session->userdata('id');
        $this->db->set($submit);
        $this->db->insert('property_surveys');
    }
    
    function save_photo($id, $name) {
        $submit['img'] = $name;
        $submit['created'] = time();
        $submit['created_by'] = $this->session->userdata('id');
        $submit['property_id'] = $id;
        $submit['caption'] = $_POST['caption'];
        $this->db->set($submit);
        $this->db->insert('property_photos');
    }
    
    function count_reviews($p_id) {
        $this->db->where('property_id',$p_id);
        return $this->db->get('property_surveys')->num_rows();
    }
    
    function property_rating($p_id) {
        $this->db->select('property_rating, bathrooms_rating, bedrooms_rating, landlord_rating, living_area_rating');
        $this->db->where('property_id',$p_id);
        $reviews = $this->db->get('property_surveys')->result_array();
        $j = 0;
        foreach($reviews as $r) $j = $j + (($r['property_rating'] + $r['bathrooms_rating'] + $r['bedrooms_rating'] + $r['landlord_rating'] + $r['living_area_rating'])/5);
        return $j;
    }
    
    function process_results($results) {
        foreach($results as &$r) {
            $r['count'] = $this->count_reviews($r['id']);
            if($r['count'] > 0) $r['rating'] = ($this->property_rating($r['id']))*20/$r['count'];
            else $r['rating'] = 0;
        }
        $sort = array();
        foreach($results as $k=>$v) {
            $sort['rating'][$k] = $v['rating'];
            $sort['name'][$k] = $v['name'];
        }
        if(count($results) > 0) {
            array_multisort($sort['rating'], SORT_DESC, $sort['name'], SORT_ASC, $results);
        }
        return $results;
    }
    
    function search_by($code, $field, $sort_by='time_science ASC', $bedrooms = NULL) {
        $this->db->select('property.*, SUM(property_surveys.living_area_rating + property_surveys.landlord_rating + property_surveys.bedrooms_rating + property_surveys.bathrooms_rating + property_surveys.property_rating)/(5 * COUNT(property_surveys.id)) AS rating, COUNT(property_surveys.id) as count, SUM(property_surveys.rent_cost + IFNULL(property_surveys.bills_cost, 0))/(COUNT(property_surveys.id)) AS price');
        $this->db->like('property.'.$field, $code, 'both');
        if(!is_null($bedrooms)){
            if(is_numeric($bedrooms)){
                $this->db->where('bedrooms', $bedrooms);
            }else{
                $this->db->where('bedrooms >=', 8);
            }
        }
        $this->db->order_by($sort_by);
        $this->db->join('property_surveys', 'property.id=property_surveys.property_id', 'left outer');
        $this->db->group_by('property.id');
        return $this->db->get('property')->result_array();
    }
        
    function search_address($street, $sort_by='time_science ASC', $bedrooms = NULL) {
        $this->db->select('property.*, SUM(property_surveys.living_area_rating + property_surveys.landlord_rating + property_surveys.bedrooms_rating + property_surveys.bathrooms_rating + property_surveys.property_rating)/(5 * COUNT(property_surveys.id)) AS rating, COUNT(property_surveys.id) as count, SUM(property_surveys.rent_cost + IFNULL(property_surveys.bills_cost, 0))/(COUNT(property_surveys.id)) AS price');
        $this->db->like('name',$street,'both');
        $this->db->or_like('address1',$street,'both');
        $this->db->or_like('address2',$street,'both');
        $this->db->or_like('address2',$street,'before');
        if(!is_null($bedrooms)){
            $this->db->where('bedrooms', $bedrooms);
        }
        $this->db->order_by($sort_by);
        $this->db->join('property_surveys', 'property.id=property_surveys.property_id', 'left outer');
        $this->db->group_by('property.id');
        return $this->db->get('property')->result_array();
    }
    
    function get_all_locations(){
        $this->db->where('lat is null');
        $p = $this->db->get('property')->result_array();
        for($i=0;$i<sizeof($p);$i++){
            $url = str_replace(' ', '+', 'http://maps.google.com/maps/api/geocode/json?address='.$p[$i]['name'].'+'.$p[$i]['address1'].'+'.$p[$i]['postcode']);
            $data = json_decode(file_get_contents($url), true);
            if(isset($data['results'][0])){
                $location = $data['results'][0]['geometry']['location'];
                $p[$i]['lat'] = $location['lat'];
                $p[$i]['lng'] = $location['lng'];
            }
        }
        if(!empty($p)){
            $this->db->update_batch('property', $p, 'id'); 
        }
        return $p;
    }
    
    function get_all_times(){
        $locations = array(
            'time_college' => 'Josephine+Butler+College+DH13DF',
            'time_town' => 'DH1+3NJ',
            'time_science' => 'The+Palatine+Centre,+Durham,+DH1+3LE',
            'time_elvet' => 'Elvet+Riverside+DH1+3JT'            
        );
        foreach($locations as $key => $loc){
            $this->db->where($key.' is null');
            $p = $this->db->get('property')->result_array();
            for($i=0;$i<sizeof($p);$i++){
                $p[$i][$key] = $this->get_time($loc, $p[$i]);
            }
            if(!empty($p)){
                $this->db->update_batch('property', $p, 'id'); 
            }
        }
    }
    
    function get_time($dest, $poperty){
        $url = str_replace(' ', '+', 'https://maps.googleapis.com/maps/api/distancematrix/json?mode=walking&destinations='.$dest.'&origins='.$poperty['name'].'+'.$poperty['address1'].'+'.$poperty['postcode']);
        $data = json_decode(file_get_contents($url), true);
        if(isset($data['rows'][0])){
            return round($data['rows'][0]['elements'][0]['duration']['value']/60);
        }
        return NULL;
    }
}
