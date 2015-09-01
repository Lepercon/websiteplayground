<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
	'permitted_methods' => array(
		'editable' => array(
			'get_exec_contact' => array(
				'desc' => 'Add exec contact details',
				'min_params' => 1,
				'max_params' => 2,
				'params' => array(
					'level' => array(
						'desc' => 'Role title',
						'req' => TRUE,
						'type' => 'select',
						'options' => 'func:get_exec_contact_options'
					),
					'add_title' => array(
						'desc' => 'Show role title',
						'req' => FALSE,
						'type' => 'bool'
					),
					'add_email' => array(
						'desc' => 'Show email',
						'req' => FALSE,
						'type' => 'bool'
					)
				)
			)
		)
	)
);

/* End of file editable.php */
/* Location: ./application/config/editable.php */