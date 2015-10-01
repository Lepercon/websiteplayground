<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// Information on all of these functions is available in the developer documentation

if ( ! function_exists('logged_in'))
{
    function logged_in() {
        $ci =& get_instance();
        return $ci->session->userdata('logged_in');
    }
}

if ( ! function_exists('has_level'))
{
    function has_level($level) {
        $ci =& get_instance();
        if(is_array($level)) {
            foreach($level as $l) {
                if(has_level($l)) return TRUE;
            }
        }
        else {
            if(is_string($level) && !is_numeric($level)) {
                // logged in isn't really a level, but this is useful for css and js includes.
                if($level == 'logged_in') return logged_in();
                if($level == 'admin') return is_admin();
                // is any exec?
                if($level == 'any') {
                    if($ci->session->userdata('level_list') != FALSE) return TRUE;
                    else return FALSE;
                }
                // is string list of exec
                if(strpos($level, ',') !== FALSE) {
                    $level = explode(',', $level);
                    foreach($level as $l) {
                        if(has_level($l)) return TRUE;
                    }
                    return FALSE;
                }
                // get level id
                $ci->db->select('id');
                $ci->db->where('full', $level);
                $level = $ci->db->get('levels')->row_array(0);
                if(!empty($level)) $level = $level['id'];
                else return FALSE;
            }
            return (($ci->session->userdata('level_list') != FALSE && in_array($level, explode(',',$ci->session->userdata('level_list')))));
        }
    }
}

if ( ! function_exists('is_admin')) {
    function is_admin() {
        $ci =& get_instance();
        return $ci->session->userdata('full_access');
    }
}


if ( ! function_exists('https_url')) {
    function https_url($seg) {
        $url = site_url($seg);
        return str_replace('http://', 'https://', $url);
    }
}

if ( ! function_exists('admin_levs')) {
    function admin_levs() {
        static $levels = array();
        if(empty($levels)) {
            $ci =& get_instance();
            // get level id
            $ci->db->select('id');
            $ci->db->where('full_access', 1);
            $ls = $ci->db->get('levels')->result_array();
            foreach($ls as $l) $levels[] = $l['id'];
        }
        return $levels;
    }
}

if( ! function_exists('back_link')) {
    function back_link($page, $generate = FALSE) {
        $ci =& get_instance();
        // generate back link based on last location
        if($generate && $ci->session->userdata('last_location') != FALSE) $page = BASE_URL.$ci->session->userdata('last_location');
        // full url passed
        else if(strpos($page, 'http://') === FALSE) $page = BASE_URL.''.$page;
        // return back link
        return '<a id="back_link" class="no-print" href="'.$page.'">&#171; Back</a>';
    }
}

if( ! function_exists('print_link')) {
    function print_link() {
        return '<a href="#" id="print-link" class="no-print">Print</a>';
    }
}

if( ! function_exists('get_usr_img_src')) {
    function get_usr_img_src($uid, $type) {
        $path = 'details/img/users/';
        if(!is_array($type)){
            $type = array($type);
        }
        foreach($type as $t){
            if(file_exists(VIEW_PATH.$path.$uid.'_'.$t.'.jpg')){
                return VIEW_URL.$path.$uid.'_'.$t.'.jpg';;
            }
        }
        return VIEW_URL.'common/img/default_'.$type[0].'.png';
    }
}

if( ! function_exists('user_profile_a_open')) {
    function user_profile_a_open($id) {
        return '<a href="'.BASE_URL.'details/profile/'.$id.'">';
    }
}

if(!function_exists('build_options')) {
    function build_options($levels, $user_level = '') {
        $options = '<option value=""></option>';
        foreach($levels as $level) {
            $options .= '<option value="'.$level['id'].'"';
            if($user_level == $level['id']) $options .= ' selected="selected" ';
            $options .= '>'.$level['full'].'</option>';
        }
        return $options;
    }
}

if ( ! function_exists('cshow_error')) {
    function cshow_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
    {
        $ci =& get_instance();
        if(!$ci->input->is_ajax_request()) set_status_header($status_code);

        ob_start();
        include(APPPATH.'errors/error_general.php');
        $ci->output->append_output(ob_get_clean());
    }
}

if ( ! function_exists('user_pref_name')) {
    function user_pref_name($first, $pref = '', $last = '') {
        $first = (empty($pref) ? $first : $pref);
        if(!empty($last)) $last = ' '.$last;
        return $first.$last;
    }
}

if ( ! function_exists('contact_wm')) {
    function contact_wm($inner_text = 'contact the administrator') {
        return '<a class="no-jsify" href="'.BASE_URL.'contact">'.$inner_text.'</a>';
    }
}

if ( ! function_exists('email_link')) {
    function email_link($id, $inner_text = 'contact') {
        return '<a href="'.BASE_URL.'contact/'.$id.'">'.$inner_text.'</a>';
    }
}

if ( ! function_exists('success_code')) {
    function success_code() {
        return 'if(isset($success) && !empty($success)) echo \'<div class="validation_success">\'.tick_img().\'<p>\'.$success.\'</p></div>\';';
    }
}

if ( ! function_exists('error_code')) {
    function error_code() {
        return
        'if(isset($errors) && $errors === TRUE) {
            echo \'<div class="validation_errors">\'.cross_img();
            echo validation_errors();
            if(!empty($other_errors)) foreach($other_errors as $error) echo \'<p>\'.$error.\'</p>\';
            echo \'</div>\';
        }';
    }
}

if ( ! function_exists('token_ip')) {
    function token_ip($page) {
        return '<input type="hidden" name="form_token" value="'.generate_form_token($page).'" />';
    }
}

if ( ! function_exists('validate_form_token')) {
    function validate_form_token($page = '') {
        $ci =& get_instance();
        return ($ci->input->post('form_token') != FALSE && $ci->input->post('form_token') == md5($page.$ci->session->userdata('form_token')));
    }
}

if ( ! function_exists('generate_form_token')) {
    function generate_form_token($page = '') {
        $ci =& get_instance();
        $rand = rand_alphanumeric(20);
        $ci->session->set_userdata('form_token', $rand);
        return md5($page.$rand);
    }
}

if ( ! function_exists('rand_alphanumeric')) {
    function rand_alphanumeric($quantity) {
        $subsets[0] = array('min' => 48, 'max' => 57); // ascii digits
        $subsets[1] = array('min' => 65, 'max' => 90); // ascii uppercase English letters
        $subsets[2] = array('min' => 97, 'max' => 122); // ascii lowercase English letters

        $string = '';
        for($i=0; $i<$quantity; $i++) {
            // random choice between lowercase, uppercase, and digits
            $s = mt_rand(0, 2);
            $ascii_code = rand_num($subsets[$s]['min'], $subsets[$s]['max']);
            $string .= chr( $ascii_code );
        }
        return $string;
    }
}

if ( ! function_exists('rand_num')) {
    function rand_num($min = 0, $max = null) {
        if($max == null) $max = mt_getrandmax();
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);
        return mt_rand($min, $max);
    }
}

if ( ! function_exists('rand_uppercase')) {
    function rand_uppercase($quantity) {
        $string = '';
        for($i=0; $i<$quantity; $i++) {
            // random choice between all chars
            $string .= chr(rand_num(65, 90));
        }
        return $string;
    }
}

if ( ! function_exists('return_array_value')) {
    function return_array_value($key, $array) {
        if(isset($array[$key])) return $array[$key];
        else return FALSE;
    }
}

if ( ! function_exists('deleteAll')) {
    function deleteAll($directory, $empty = false) {
        if(substr($directory,-1) == "/") {
            $directory = substr($directory,0,-1);
        }

        if(!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif(!is_readable($directory)) {
            return false;
        } else {
            $directoryHandle = opendir($directory);

            while ($contents = readdir($directoryHandle)) {
                if($contents != '.' && $contents != '..') {
                    $path = $directory . "/" . $contents;

                    if(is_dir($path)) {
                        deleteAll($path);
                    } else {
                        unlink($path);
                    }
                }
            }

            closedir($directoryHandle);

            if($empty == false) {
                if(!rmdir($directory)) {
                    return false;
                }
            }

            return true;
        }
    }
}

if(!function_exists('get_last_location')) {
    function get_last_location() {
        $ci =& get_instance();
        return str_replace('https://', 'http://', BASE_URL).($ci->session->userdata('last_location') != FALSE ? $ci->session->userdata('last_location') : '');
    }
}

if(!function_exists('tick_img')) {
    function tick_img() {
        return '<span class="ui-icon ui-icon-check inline-block"></span>';
    }
}

if(!function_exists('cross_img')) {
    function cross_img() {
        return '<span class="ui-icon ui-icon-notice inline-block"></span>';
    }
}

if(!function_exists('textarea_to_db')) {
    function textarea_to_db($v) {
        return nl2br(htmlentities($v, ENT_QUOTES,'UTF-8'));
    }
}

if(!function_exists('db_to_textarea')) {
    function db_to_textarea($v) {
        return preg_replace('/<br[\s]*\/?>/i','\n',html_entity_decode($v, ENT_QUOTES,'UTF-8'));
    }
}

if(!function_exists('time_elapsed_string')) {
    function time_elapsed_string($ptime) {
        $etime = time() - $ptime;
        if ($etime < 1) {
            return 'now';
        }

        $a = array( 31104000  =>  'year',
                    2592000   =>  'month',
                    86400     =>  'day',
                    3600      =>  'hour',
                    60        =>  'minute',
                    1         =>  'second'
                    );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
            }
        }
    }
}

if(!function_exists('wotw_box')) {
    function wotw_box($colour, $uri, $uri_text, $time, $description = NULL, $facebook = NULL, $twitter = NULL) {
        $wotw = '<div class="wotw-box">';
        $wotw .= '<h3 class="wotw-time" style="background-color: '.$colour.'">'.$time.'</h3>';
        $wotw .= '<div class="wotw-info">';
        $wotw .= '<p>'.anchor($uri, $uri_text).(empty($description) ? '' : '<br/>'.$description ).'</p>';
        $wotw .= '<a class="sprite-container" href="'.site_url($uri).'"><div class="common-sprite" id="sprite-butler"></div></a>';
        if(!empty($twitter)) $wotw .= '<a class="sprite-container" href="http://twitter.com/'.$twitter.'" target="_blank"><div class="common-sprite" id="sprite-twitter"></div></a>';
        if(!empty($facebook)) $wotw .= '<a class="sprite-container" href="'.$facebook.'" target="_blank"><div class="common-sprite" id="sprite-facebook"></div></a>';
        $wotw .= '</div>';
        $wotw .= '</div>';
        return $wotw;
    }
}

if(!function_exists('wotw_open')) {
    function wotw_open($heading) {
        return '<div class="jcr-box wotw-outer"><h3 class="wotw-day">'.$heading.'</h3>';
    }
}

if ( ! function_exists('editable_area')) {
    function editable_area($page, $path, $access_rights=NULL) {
        $ci =& get_instance();
        $ci->load->library('page_edit_auth');
        if(is_null($access_rights) || !$access_rights){
            $access_rights = $ci->page_edit_auth->authenticate($page);
        }
        $auth = md5($ci->session->userdata('id').date('Y').$page.$path.'50j05t9jk5-f59gk9fkfj8');
        $return = '';
        if($access_rights > 0) {
            $return .= '<input class="page-rights" type="hidden" value="'.$access_rights.'" />';
            $return .= '<input class="page-name" type="hidden" value="'.$page.'" />';
            $return .= '<input class="page-path" type="hidden" value="'.$path.'" />';
            $return .= '<input class="page-auth" type="hidden" value="'.$auth.'" />';
        }
        $return .= '<div class="page-content-area">';
        if(file_exists(VIEW_PATH.$page.'/'.$path.'.php')) {
            $return .= file_get_contents(VIEW_PATH.$page.'/'.$path.'.php');
        }
        $return .= '</div>';
        return $return;
    }
}