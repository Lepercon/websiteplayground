<?php

class Questionnaire extends CI_Controller {

    function Questionnaire()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
        $this->questionnaire_model->delete_old_questionnaires();
        $this->page_info = array(
            'id' => 6,
            'title' => 'Questionnaire',
            'big_title' => '<span class="big-text-medium">Questions</span>',
            'description' => 'Answer JCR Questionnaires',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => FALSE,
            'css' => array(),
            'js' => array('questionnaire/questionnaire'),
            'keep_cache' => FALSE,
            'editable' => FALSE
        );
    }

    function index() {
        $this->load->view('questionnaire/list', array(
            'questionnaires' => $this->questionnaire_model->get_questionnaires()
        ));
    }

    function add() {
        $e_id = $this->uri->rsegment(3);
        if($e_id === FALSE OR !is_admin()) {
            $this->index();
            return;
        }
        if(validate_form_token('new_questionnaire')) {
            $this->questionnaire_model->save_questionnaire();
            $this->index();
        } else {
            $this->load->view('questionnaire/new_questionnaire', array('errors' => FALSE, 'e_id' => $e_id));
        }
    }

    function edit() {
        $q_id = $this->uri->rsegment(3);
        if($q_id === FALSE OR !is_admin()) {
            $this->index();
            return;
        }
        $q = $this->questionnaire_model->get_from_id($q_id);
        if($q['secure'] == 0 or $q['created_by'] == $this->session->userdata('id') or has_level(1)) {
            if(validate_form_token('edit_questionnaire')) {
                $this->questionnaire_model->save_questionnaire($q_id);
                $this->index();
            } else {
                $this->load->view('questionnaire/edit_questionnaire', array('q' => $q));
            }
        } else {
            $this->questionnaire_model->alert('edit', $q);
            $this->load->view('questionnaire/alert', array('section' => 'edit'));
        }
    }

    function answer() {
        $q = $this->questionnaire_model->get_from_id($this->uri->rsegment(3));
        if ($q !== FALSE) {
            if ((time() >= $q['questionnaire_opens'] && time() < $q['questionnaire_closes'] && $q['user_has_answered'] === FALSE) && validate_form_token('begin-questionnaire')) {
                $q = array_merge($q, $this->questionnaire_model->init_questionnaire($q['id']));
                $q['user_has_answered'] = FALSE;
                $q['started'] = TRUE;
            } elseif ((time() >= $q['questionnaire_opens'] && time() < $q['questionnaire_closes'] && $q['user_has_answered'] === FALSE) && validate_form_token('questionnaire')) {
                $this->questionnaire_model->save_answers($q['id']);
                $q['user_has_answered'] = TRUE;
            }
            $this->load->view('questionnaire/questionnaire', array('q' => $q));
        } else {
            $this->index();
        }
    }

    function cancel() {
        $q_id = $this->uri->rsegment(3);
        if($q_id === FALSE OR !is_admin()) {
            $this->index();
            return;
        }
        $q = $this->questionnaire_model->get_from_id($q_id);
        if($q['secure'] == 0 or $q['created_by'] == $this->session->userdata('id')) {
            if(validate_form_token('cancel_questionnaire') && $this->input->post('cancel') !== FALSE) {
                $this->questionnaire_model->cancel_questionnaire($q_id);
                $this->index();
                return;
            } elseif (validate_form_token('cancel_questionnaire') && $this->input->post('cancel') === FALSE) {
                $this->index();
            } else {
                $this->load->view('questionnaire/cancel_questionnaire', array('q_id' => $q_id));
            }
        } else {
            $this->questionnaire_model->alert('cancel', $q);
            $this->load->view('questionnaire/alert', array('section' => 'cancel'));
        }
    }

    function results() {
        $q_id = $this->uri->rsegment(3);
        if($q_id === FALSE OR !is_admin()) {
            $this->index();
            return;
        }
        $q = $this->questionnaire_model->get_from_id($q_id);
        if($q['secure'] == 0 or $q['created_by'] == $this->session->userdata('id') or $this->session->userdata('id')==1597) {
            $answers = $this->questionnaire_model->get_answers($q_id);
            if($q['anonymous'] == TRUE) {
                $this->load->view('questionnaire/result_questionnaire', array('q' => $q, 'answer' => $answers));
            } else {
                foreach($answers as &$a) {
                    $a['user_id'] = $this->users_model->get_full_name($a['user_id']);
                }
                $this->load->view('questionnaire/result_questionnaire', array('q' => $q, 'answer' => $answers));
            }
        } else {
            //$this->questionnaire_model->alert('results', $q);
            $this->load->view('questionnaire/alert', array('section' => 'results'));
        }
    }
}
/* End of file questionnaire.php */
/* Location: .application/controllers/questionnaire.php */