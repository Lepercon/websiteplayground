<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Questionnaire_model extends CI_Model {

    function Questionnaire_model() {
        parent::__construct();
    }

    function get_questionnaires() {
        $questionnaires = $this->db->get('questionnaire')->result_array();
        if(!empty($questionnaires)) {
            foreach($questionnaires as &$q) {
                $q = $this->get_questionnaire($q);
            }
        }
        return $questionnaires;
    }

    function get_from_id($id) {
        if($id == NULL) return FALSE;
        $this->db->where('id', $id);
        $questionnaire = $this->db->get('questionnaire')->row_array(0);

        if(empty($questionnaire)) {
            return FALSE;
        } else {
            return $this->get_questionnaire($questionnaire);
        }
    }

    function get_questionnaire($q) {
        $this->db->where('q_id', $q['id']);
        $this->db->order_by('q_order', 'asc');
        $questions = $this->db->get('questionnaire_questions')->result_array();

        if(!empty($questions)) {
            $q['question'] = array();
            $q['options'] = array();
            foreach($questions as $question) {
                $q['question'][] = $question['question'];
                $q['options'][] = $question['options'];
            }
        } else {
            return FALSE;
        }

        $this->db->where('questionnaire_id', $q['id']);
        $this->db->where('user_id', $this->session->userdata('id'));
        $response = $this->db->get('questionnaire_answers')->row_array(0);
        if(empty($response)) {
            $q['user_has_answered'] = FALSE;
            $q['started'] = FALSE;
        } else {
            $q['started'] = TRUE;
            $q['user_has_answered'] = $response['submitted'] > 0 ? TRUE : FALSE;
        }
        return $q;
    }

    function get_answers($id) {
        // Generates results of questionnaire
        $this->db->where('q_id', $id);
        $answer = $this->db->get('questionnaire_responses')->result_array();
        if(empty($answer)) return FALSE;
        return $answer;
    }

    function init_questionnaire($q_id) {
        $submit['questionnaire_id'] = $q_id;
        $submit['user_id'] = $this->session->userdata('id');
        $submit['started'] = time();
        $this->db->set($submit);
        $this->db->insert('questionnaire_answers');
        return $submit;
    }

    function save_answers($q_id) {
        $answers = array();
        foreach($_POST['answer'] as $k => $v) {
            $answers[] = array(
                'q_id' => $q_id,
                'q_order' => $k,
                'u_id' => $this->session->userdata('id'),
                'answer' => $v
            );
        }
        $this->db->insert_batch('questionnaire_responses', $answers);

        $this->db->set('submitted', time());
        $this->db->where('questionnaire_id', $q_id);
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->update('questionnaire_answers');
    }

    function save_questionnaire($q_id = null) {
        foreach(array('questionnaire_opens', 'questionnaire_closes') as $v) {
            $var = explode("/", $this->input->post($v.'_date'));
            $_POST[$v] = mktime($_POST[$v.'_hour'], $_POST[$v.'_minute'], 0, $var[1], $var[0], $var[2]);
        }
        if($_POST['questionnaire_closes'] < $_POST['questionnaire_opens']) $_POST['questionnaire_closes'] = $_POST['questionnaire_opens'] + 3600;
        if(empty($_POST['notes'])) $_POST['notes'] = '';
        $_POST['notes'] = textarea_to_db($_POST['notes']); //convert html chars and add line breaks
        foreach($_POST as $k => $v) {
            if(in_array($k, array('name', 'questionnaire_opens', 'questionnaire_closes', 'anonymous', 'secure', 'notes', 'event_id'))) $submit[$k] = $v;
        }
        if(!empty($submit)) {
            if(is_null($q_id)) {
                $submit['created_on'] = time();
                $submit['created_by'] = $this->session->userdata('id');
            }
            $this->db->set($submit);
            if(is_null($q_id)) {
                $this->db->insert('questionnaire');
                $questionnaire_id = $this->db->insert_id();
            }
            else {
                $this->db->where('id', $q_id);
                $this->db->update('questionnaire');
                $questionnaire_id = $q_id;

                $this->db->where('q_id', $q_id);
                $this->db->delete('questionnaire_questions');
            }
            unset($submit);
            $submit['q_id'] = $questionnaire_id;
            foreach($_POST['question'] as $k => $v) {
                $submit['q_order'] = $k;
                $submit['question'] = $_POST['question'][$k];
                $submit['options'] = $_POST['options'][$k];
                $this->db->set($submit);
                $this->db->insert('questionnaire_questions');
            }
        }
    }

    function alert($section, $q) {
        if(ENVIRONMENT !== 'development') {
            $this->db->select('email');
            $this->db->where('id', $q['created_by']);
            $email = $this->db->get('users')->row_array(0);

            $name = user_pref_name($this->session->userdata('firstname'),$this->session->userdata('prefname'),$this->session->userdata('surname'));
            $this->load->library('email');
            $this->email->from($this->session->userdata('email'), $name);
            $this->email->to($email['email']);
            $this->email->subject('Questionnaire permissions');
            $this->email->message($name.' tried to view the '.$section.' section of your questionnaire '.$q['name'].'.');
            $this->email->send();
        }
    }

    function cancel_questionnaire($q_id) {
        $this->db->where('id', $q_id);
        $this->db->delete('questionnaire');
        $this->db->where('questionnaire_id', $q_id);
        $this->db->delete('questionnaire_answers');
        $this->db->where('q_id', $q_id);
        $this->db->delete('questionnaire_questions');
        $this->db->where('q_id', $q_id);
        $this->db->delete('questionnaire_responses');
    }

    function delete_old_questionnaires() {
        $this->db->select('id');
        $this->db->where('questionnaire_closes <', time() - 14*24*60*60);
        $to_delete = $this->db->get('questionnaire')->result_array();
        foreach($to_delete as $q) {
            $this->cancel_questionnaire($q['id']);
        }
        if(!empty($to_delete)) {
            $this->load->dbutil();
            foreach(array('questionnaire','questionnaire_answers','questionnaire_questions','questionnaire_responses') as $del) {
                $this->dbutil->optimize_table($del);
            }
        }
    }
}
