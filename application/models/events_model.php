<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class events_model extends CI_Model {

    function events_model() {
        // Call the Model constructor
        parent::__construct();
    }
    
    function event_permissions(){
        return is_admin();
    }

    function get_questionnaire_by_event($e_id) {
        $this->db->where('event_id', $e_id);
        $questionnaire = $this->db->get('questionnaire')->result_array();
        if(empty($questionnaire)) {
            return FALSE;
        } else {
            $this->load->model('questionnaire_model');
            foreach($questionnaire as &$q) {
                $q = $this->questionnaire_model->get_questionnaire($q);
            }
            return $questionnaire;
        }
    }

    function get_categories() {
        $this->db->order_by('name ASC');
        return $this->db->get('categories')->result_array();
    }

    function get_category_id($short) {
        $this->db->select('id');
        $this->db->where('short', $short);
        return return_array_value('id',$this->db->get('categories')->row_array(0));
    }

    function add_event() {
        $start = explode("/", $this->input->post('date'));
        $submit['time'] = mktime($this->input->post('hour'), $this->input->post('minute'), 0, $start[1], $start[0], $start[2]);
        $end = array($start[1], $start[0], $start[2]);
        $end_array = explode("/", $this->input->post('end_date'));
        for($i = 0; $i<=2; $i++) {
            if(isset($end_array[$i])) {
                $end[$i] = $end_array[$i];
            }
        }
        $submit['end'] = mktime($this->input->post('end_hour'), $this->input->post('end_minute'), 0, $end[1], $end[0], $end[2]);
        if($submit['end'] < $submit['time']) {
            $submit['end'] = $submit['time'] + 3600;
        }
        if(isset($_POST['file'])){
            $submit['event_poster'] = $_POST['file']['upload_data']['file_name'];
        }
        foreach(array('name', 'description', 'location', 'facebook_url', 'twitter_handle', 'category', 'hidden', 'event_poster_hidden') as $v) $submit[$v] = $this->input->post($v);
        $submit['created_by'] = $this->session->userdata('id'); // Store user
        $this->db->set($submit);
        $this->db->insert('events');
        return $this->db->insert_id();
    }

    function edit_event($e_id) {
        $start = explode("/", $this->input->post('date'));
        $submit['time'] = mktime($this->input->post('hour'), $this->input->post('minute'), 0, $start[1], $start[0], $start[2]);
        $end = explode("/", $this->input->post('end_date'));
        $submit['end'] = mktime($this->input->post('end_hour'), $this->input->post('end_minute'), 0, $end[1], $end[0], $end[2]);
        if($submit['end'] < $submit['time']) {
            $submit['end'] = $submit['time'] + 3600;
        }
        if(isset($_POST['file'])){
            $submit['event_poster'] = $_POST['file']['upload_data']['file_name'];
        }
        foreach(array('name', 'description', 'location', 'facebook_url', 'twitter_handle', 'category', 'hidden', 'event_poster_hidden') as $v) $submit[$v] = $this->input->post($v);
        $this->db->where('id', $e_id);
        $this->db->update('events', $submit);
    }

    function cancel_event($e_id) {
        // Deletes the event and related news posts
        $this->db->where('id', $e_id);
        $this->db->delete('events');

        $this->db->where('event_id', $e_id);
        $this->db->delete('news');
    }

    function get_events_by_category($category = 'all', $limit = 0, $future = TRUE, $hidden = 0) {

        if($category !== 'all') {
            if(!is_numeric($category)) $category = $this->get_category_id($category);
            $this->db->where('category', $category);
        }
        if($future === TRUE) $this->db->where('time >', time() - 60 * 60 * 24 * 7);
        if($limit > 0) $this->db->limit($limit);
        if(!$hidden) $this->db->where('hidden', 0);
        $this->db->order_by('time ASC');
        return $this->db->get('events')->result_array();
    }

    function get_event($e_id) {
        $this->db->where('id', $e_id);
        $return = $this->db->get('events')->row_array(0);
        if(empty($return)) return FALSE;
        else return $return;

    }

    function add_post() {
        
        $event_id = $this->input->post('event_id');
        
        $submit['created'] = time();
        $submit['created_by'] = $this->session->userdata('id');
        /*$this->db->select('name');
        $this->db->where('id', $event_id);
        $event = $this->db->get('events')->row_array(0);*/
        $submit['content'] = $this->input->post('content');
        $submit['title'] = $this->input->post('title');
        $submit['event_id'] = $event_id;
        $this->db->set($submit);
        $this->db->insert('news');
        
        $this->db->select('news.*, users.firstname, users.prefname, users.surname');
        $this->db->where('news.id', $this->db->insert_id());
        $this->db->join('users', 'users.id=news.created_by');
        $post = $this->db->get('news')->row_array(0);
        $post['created_name'] = ($post['prefname']==''?$post['firstname']:$post['prefname']).' '.$post['surname'];
        return $post;
    }

    function get_posts($event_id = 'all', $limit = 0) {
        $this->load->helper('smiley');
        if($event_id != 'all') $this->db->where('event_id', $event_id);
        if($limit > 0) $this->db->limit($limit);
        $this->db->order_by('created DESC, updated DESC');
        $news = $this->db->get('news')->result_array();
        if(empty($news)) return FALSE;
        foreach($news as &$n) {
            $creator = $this->users_model->get_users($n['created_by'], 'firstname, prefname, surname');
            if(empty($creator)) $n['created_name'] = 'Unknown User';
            else $n['created_name'] = user_pref_name($creator['firstname'], $creator['prefname'], $creator['surname']);
            $n['content'] = parse_smileys($n['content'], VIEW_URL.'common/smileys/');
        }
        return $news;
    }

    function get_post($post_id) {
        $this->db->where('id', $post_id);
        return $this->db->get('news')->row_array(0);
    }

    function delete_post($post_id) {
        $this->db->where('id', $post_id);
        $this->db->delete('news');
    }

    function get_files($event_id = 'all') {
        if($event_id != 'all') $this->db->where('event_id', $event_id);
        $this->db->order_by('submitted_time DESC');
        return $this->db->get('event_files')->result_array();
    }

    function delete_file($event_id, $file_id) {
        $this->db->where('event_id', $event_id);
        $this->db->where('id', $file_id);
        $filename = $this->db->get('event_files')->row_array(0);
        if(!empty($filename)) {
            $this->db->where('event_id', $event_id);
            $this->db->where('id', $file_id);
            $this->db->delete('event_files');
            unlink(VIEW_PATH.'events/files/event_'.$event_id.'/'.$filename['filename']);
        }
    }

    function update_file($event_id, $file_id) {
        $this->db->where('event_id', $event_id);
        $this->db->where('id', $file_id);
        $this->db->set(array('description' => $this->input->post('description')));
        $this->db->update('event_files');
    }

    function add_file($event_id, $filename) {
        $this->db->set(array(
            'filename' => $filename,
            'submitted_time' => time(),
            'submitted_by' => $this->session->userdata('id'),
            'event_id' => $event_id,
            'description' => $this->input->post('description')
        ));
        $this->db->insert('event_files');
    }

    function get_photos($event_id = 'all') {
        if($event_id != 'all') $this->db->where('event_id', $event_id);
        $this->db->order_by('submitted_time DESC');
        return $this->db->get('photos')->result_array();
    }

    function delete_photo($event_id, $image_id) {
        $this->db->where('event_id', $event_id);
        $this->db->where('id', $image_id);
        $this->db->delete('photos');
    }

    function update_photo($event_id, $image_id) {
        $this->db->where('event_id', $event_id);
        $this->db->where('id', $image_id);
        $this->db->set(array('description' => $this->input->post('description')));
        $this->db->update('photos');
    }

    function add_photo($event_id, $filename) {
        $this->db->set(array(
            'filename' => $filename,
            'submitted_time' => time(),
            'submitted_by' => $this->session->userdata('id'),
            'event_id' => $event_id,
            'description' => $this->input->post('description')
        ));
        $this->db->insert('photos');
    }

    function get_month($year, $month) {
        $return['month'] = $month;
        $return['year'] = $year;
        // what day number of the week does this month start on? 1=mon, 7=sun
        $return['day_start'] = date('N', mktime(0,0,0,$month,1,$year));
        // how many days in this month?
        $return['cal_days'] = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        // what day of the week does the month end?
        $return['day_end'] = date('N', mktime(0,0,0,$month,$return['cal_days'],$year));
        // previous month
        $return['prev'] = ($month == 1) ? array('month' => 12, 'year' => $year - 1) : array('month' => $month - 1, 'year' => $year);
        // next month
        $return['next'] = ($month == 12) ? array('month' => 1, 'year' => $year + 1) : array('month' => $month + 1, 'year' => $year);
        // calendar days in previous month.  need to check if previous month is in previous year
        $return['prev_month_cal_days'] = cal_days_in_month(CAL_GREGORIAN,$return['prev']['month'],$return['prev']['year']);

        $date = $return['prev_month_cal_days'] - $return['day_start'] + 1;
        $limit = $return['prev_month_cal_days'];
        $cur_month = $return['prev']['month'];
        $cur_year = $return['prev']['year'];
        $month_counter = 0;

        $start = mktime(0,0,0,$cur_month,$date,$cur_year);
        $end = $start + 3715199;

        $events = $this->get_cal_events($start, $end, 'id, name, description, time, location, hidden, event_poster', $this->event_permissions());
        $signups = $this->get_signup_events($start, $end, 'signup.id, signup.name, signup_opens, meet_location, event_id, event_poster');
        $swaps = $this->get_swap_events($start, $end, 'signup.id, signup.name, swapping_opens, meet_location, event_id, event_poster');

        for($row = 1; $row <= 6; $row++) {
            for($cell = 1; $cell <= 7; $cell++) {
                $date++;
                if($date > $limit)  { // either end of prev month or end of this month, reset date to 1
                    $date = 1;
                    $month_counter++;
                    if($month_counter == 1) {
                        $cur_month = $month;
                        $cur_year = $year;
                        $limit = $return['cal_days'];
                    }
                    else {
                        $cur_month = $return['next']['month'];
                        $cur_year = $return['next']['year'];
                    }
                }
                $c = array(); // cell details array
                $c['date'] = $date;
                $c['month'] = $cur_month;
                $c['year'] = $cur_year;
                if($month_counter !== 1) $c['class'] = 'dim';
                elseif($date == date('j') && $cur_month == date('n') && $cur_year == date('Y')) $c['class'] = 'today';
                else $c['class'] = '';
                $c['appts'] = array();
                $start = mktime(0,0,0,$cur_month,$date,$cur_year);
                $end = mktime(23,59,59,$cur_month,$date,$cur_year);
                if(!empty($events)) {
                    foreach($events as $s) {
                        if($s['time'] >= $start && $s['time'] <= $end) {
                            $c['appts'][] = $s;
                        }
                    }
                }
                if(!empty($signups)) {
                    foreach($signups as $s) {
                        if($s['signup_opens'] >= $start && $s['signup_opens'] <= $end) {
                            $s['time'] = $s['signup_opens'];
                            $s['description'] = 'Signup opens for '.$s['name'];
                            $s['location'] = $s['meet_location'];
                            $s['name'] = 'Signup: '.$s['name'];
                            $s['id'] = $s['event_id'];
                            $c['appts'][] = $s;
                        }
                    }
                }
                if(!empty($swaps)) {
                    foreach($swaps as $s) {
                        if($s['swapping_opens'] >= $start && $s['swapping_opens'] <= $end) {
                            $s['time'] = $s['swapping_opens'];
                            $s['description'] = 'Swapping opens for '.$s['name'];
                            $s['location'] = $s['meet_location'];
                            $s['name'] = 'Swapping: '.$s['name'];
                            $s['id'] = $s['event_id'];
                            $c['appts'][] = $s;
                        }
                    }
                }
                $return['data'][$row][$cell] = $c;
            }
        }
        return $return;
    }

    function get_week() {
        $start = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $week_end = $start + 604800;

        $events = $this->get_cal_events($start, $week_end, 'id, name, description, time, location, facebook_url, twitter_handle, event_poster');
        $signups = $this->get_signup_events($start, $week_end, 'signup.id, signup.name, signup_opens, meet_location, event_id, event_poster');
        $swaps = $this->get_swap_events($start, $week_end, 'signup.id, signup.name, swapping_opens, meet_location, event_id, event_poster');

        $return = array();
        for($cell = 1; $cell <= 7; $cell++) {
            $end = $start + 86400;
            if(!empty($events)) {
                $return[$cell]['appt'] = array();
                foreach($events as $e) {
                    if($e['time'] >= $start && $e['time'] < $end) {
                        $return[$cell]['appt'][] = $e;
                    }
                }
            }
            if(!empty($signups)) {
                $return[$cell]['signup'] = array();
                foreach($signups as $e) {
                    if($e['signup_opens'] >= $start && $e['signup_opens'] < $end) {
                        $return[$cell]['signup'][] = $e;
                    }
                }
            }
            if(!empty($swaps)) {
                $return[$cell]['swapping'] = array();
                foreach($swaps as $e) {
                    if($e['swapping_opens'] >= $start && $e['swapping_opens'] < $end) {
                        $return[$cell]['swapping'][] = $e;
                    }
                }
            }
            if($cell == 1) {
                $return[$cell]['day'] = 'Today';
            } elseif($cell == 2) {
                $return[$cell]['day'] = 'Tomorrow';
            } else {
                $return[$cell]['day'] = date('l', $start);
            }
            $start = $end;
        }
        return $return;
    }

    function get_signup_events($start, $end, $select) {
        // Get signup event openings between the times given
        $this->db->select($select);
        $this->db->where('signup_opens >=', $start);
        $this->db->where('signup_opens <=', $end);
        $this->db->order_by('signup_opens ASC');
        $this->db->join('events', 'events.id=signup.event_id');
        return $this->db->get('signup')->result_array();
    }

    function get_swap_events($start, $end, $select) {
        // Get swap event openings between the times given
        $this->db->select($select);
        $this->db->where('swap_price >', 0);
        $this->db->where('swapping_opens >=', $start);
        $this->db->where('swapping_opens <=', $end);
        $this->db->order_by('swapping_opens ASC');
        $this->db->join('events', 'events.id=signup.event_id');
        return $this->db->get('signup')->result_array();
    }

    function get_cal_events($start, $end, $select, $hidden = 0) {
        // Get calendar events
        $this->db->select($select);
        $this->db->where('time >=', $start);
        $this->db->where('time <=', $end);
        if(!$hidden) $this->db->where('hidden', 0);
        $this->db->order_by('time ASC');

        return $this->db->get('events')->result_array();

    }

    function get_photos_by_academic_year($start = 'now', $hidden = 0) {
        // If no year has been set in controller then get current academic year
        if($start == 'now') {
            $start = date('Y');
            // Determine side of calendar year that current academic year is
            // Use 1st of August as academic year cutoff
            if(time() < mktime(0, 0, 0, 8, 1, date('Y'))) {
                $start = $start - 1;
            }
        }
        $this->db->select('events.id, events.name, events.description, events.time');
        $this->db->join('photos', 'photos.event_id = events.id', 'inner');
        $this->db->where('events.time >=', mktime(0, 0, 0, 8, 1, $start));
        $this->db->where('events.time <', mktime(0, 0, 0, 8, 1, $start + 1));

        if(!$hidden) $this->db->where('events.hidden', 0);
    
    $this->db->order_by('events.time DESC');
        $all_events = $this->db->get('events')->result_array();
        $processed = array();
        foreach($all_events as $k => $v) {
            if(in_array($v['id'], $processed)) {
                unset($all_events[$k]);
            } else {
                $processed[] = $v['id'];
            }
        }
        foreach($all_events as &$event) {
            $photos = $this->get_photos($event['id']);
            if(!empty($photos)) {
                $event['photos'] = $photos;
            }
            else {
                $event['photos'] = NULL;
            }
        }
        return $all_events;
    }

    function get_date_range($hidden = 0) {

        $range = array();

        $this->db->select('MIN(time) as min, MAX(time) as max', TRUE);
        if(!$hidden) $this->db->where('hidden', 0);
        $dates = $this->db->get('events')->row_array(0);
        if(empty($dates)) return FALSE;
        foreach(array('min', 'max') as $m) {
            $year = date('Y', $dates[$m]);
            if($dates[$m] > mktime(0, 0, 0, 8, 1, $year)) {
                $range[$m] = $year;
            }
            else {
                $range[$m] = $year - 1;
            }
        }
        return $range;
    }
    
    function get_event_posters($num = 10, $hidden=FALSE){
        $this->db->select('id, event_poster, name, description');
        if(!$hidden){
            $this->db->where('event_poster_hidden !=', 1);
        }
        $this->db->where('time >', time() - 12 * 60 * 60);
        $this->db->where('event_poster IS NOT NULL');

        $this->db->from('events');
        $this->db->order_by('time');
        $this->db->limit($num);
        $posters = $this->db->get()->result_array();
        
        $sizes = array('_800px', '_300px', '');
        $image_path = './application/views/events/posters/';
        
        for($i=0;$i<sizeof($posters);$i++){
            foreach($sizes as $s){
                $im_name = str_replace('.', $s.'.', $posters[$i]['event_poster']);
                if(file_exists($image_path.$im_name)){
                    $posters[$i]['event_poster'] = $im_name;
                    break;
                }
            }
        }
        
        return $posters;
    }
    
    function remove_poster($event_id){
        $this->db->select('event_poster');
        $this->db->where('id', $event_id);
        $this->db->from('events');
        $file = $this->db->get()->row_array(0);
        if(!is_null($file['event_poster'])){
        
            $sizes = array('_800px', '_300px', '');
            $image_path = './application/views/events/posters/';
        
            foreach($sizes as $s){
                $im_name = str_replace('.', $s.'.', $file['event_poster']);
                if(file_exists($image_path.$im_name)){
                    unlink($image_path.$im_name);
                }
            }
            $this->db->set('event_poster', 'NULL', FALSE);
            $this->db->where('id', $event_id);
            return $this->db->update('events');
        }
    }
}
