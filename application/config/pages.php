<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$pages = array(
    'default' => array(
        'id' => 0,
        'title' => 'ERROR', // page title
        'big_title' => NULL, // big title at the bottom of the page (title will be used if not provided)
        'description' => 'Butler JCR',
        'requires_login' => FALSE, // does page require login
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array(), // page css
        'js' => array(), // page js
        'keep_cache' => FALSE, // keep the page cache in javascript
        'editable' => FALSE // is this page editable?
    ),
    'home' => array(
        'id' => 1,
        'title' => 'Home',
        'big_title' => NULL,
        'description' => 'The Josephine Butler college JCR is the student run body of the newest college in Durham University, taking in roughly 230 new students each year.',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('home/home'),
        'js' => array('home/home'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'admin' => array(
        'id' => 2,
        'title' => 'Administration Panel',
        'big_title' => '<span class="big-text-medium">Admin Panel</span>',
        'description' => 'Admin panel',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => FALSE,
        'css' => array('admin/admin', 'details/details', 'jcrop'),
        'js' => array('admin/admin', 'details/details', 'jcrop'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'events' => array(
        'id' => 3,
        'title' => 'Calendar',
        'big_title' => NULL,
        'description' => 'Butler JCR Event Calendar',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('events/events'),
        'js' => array('events/events'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'involved' => array(
        'id' => 4,
        'title' => 'Get Involved',
        'big_title' => '<span class="big-text-medium">get involved</span>',
        'description' => 'How you can get involved in the JCR',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('involved/involved'),
        'js' => array('involved/involved'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'photos' => array(
        'id' => 5,
        'title' => 'Media',
        'big_title' => '<span class="big-text-tiny">Photos & Videos</span>',
        'description' => 'Photos of us!',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('photos/photos'),
        'js' => array('photos/photos'),
        'keep_cache' => array('photos', 'photos/videos'),
        'editable' => FALSE
    ),
    'questionnaire' => array(
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
    ),
    'contact' => array(
        'id' => 7,
        'title' => 'Contact Us',
        'big_title' => '<span class="big-text-medium">Contact Us</span>',
        'description' => 'How to contact us',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('contact/contact'),
        'js' => array('contact/contact'),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'prospective' => array(
        'id' => 8,
        'title' => 'Prospective',
        'big_title' => '<span class="big-text-tiny">Prospective students</span>',
        'description' => 'Information for prespective students',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('prospective/prospective'),
        'js' => array('prospective/prospective'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'alumni' => array(
        'id' => 9,
        'title' => 'Alumni',
        'big_title' => NULL,
        'description' => 'Information for Alumni',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('alumni/alumni'),
        'js' => array('alumni/alumni'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'signup' => array(
        'id' => 10,
        'title' => 'Sign Up',
        'big_title' => NULL,
        'description' => 'Signup to upcoming Butler events',
        'requires_login' => TRUE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('signup/signup'),
        'js' => array('signup/signup'),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'whoswho' => array(
        'id' => 11,
        'title' => 'Who\'s Who',
        'big_title' => '<span class="big-text-medium">who\'s who</span>',
        'description' => 'Find out who does what around college',
        'requires_login' => FALSE,
                'allow_non-butler' => TRUE,
                'require-secure' => FALSE,
        'css' => array('whoswho/whoswho'),
        'js' => array('whoswho/whoswho', 'whoswho/jsPDF/jspdf', 'whoswho/jsPDF/jspdf.plugin.addimage', 'whoswho/jsPDF/libs/FileSaver.js/FileSaver.min', 'whoswho/jsPDF/jspdf.plugin.from_html', 'whoswho/jsPDF/jspdf.plugin.split_text_to_size', 'whoswho/jsPDF/jspdf.plugin.standard_fonts_metrics'),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'welfare' => array(
        'id' => 12,
        'title' => 'Welfare',
        'big_title' => '<span class="big-text-medium">welfare</span>',
        'description' => 'Get help and support on all matters through the welfare committee',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => TRUE,
        'css' => array('welfare/welfare'),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'liversout' => array(
        'id' => 13,
        'title' => 'Livers Out',
        'big_title' => '<span class="big-text-small">livers </span><span class="big-text-medium">out</span>',
        'description' => 'Information for livers out',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => FALSE,
        'css' => array('liversout/liversout'),
        'js' => array('liversout/liversout'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'archive' => array(
        'id' => 14,
        'title' => 'Archive',
        'big_title' => NULL,
        'description' => 'Committee Documents and JCR Awards',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => FALSE,
        'css' => array('archive/archive'),
        'js' => array('archive/archive'),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'details' => array(
        'id' => 15,
        'title' => 'User Details',
        'big_title' => '<span class="big-text-small">User </span><span class="big-text-medium">Details</span>',
        'description' => 'View and edit user details',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => FALSE,
        'css' => array('details/details', 'jcrop'),
        'js' => array('details/details', 'jcrop'),
        'keep_cache' => array('details'),
        'editable' => FALSE
    ),
    'bar' => array(
        'id' => 16,
        'title' => 'Bar',
        'big_title' => NULL,
        'description' => 'Information about the bar',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array(),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'services' => array(
        'id' => 17,
        'title' => 'JBs',
        'big_title' => '<span class="big-text-tiny">JBs</span>',
        'description' => 'JBs Info',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array(),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'jcr' => array(
        'id' => 18,
        'title' => 'The JCR',
        'big_title' => '<span class="big-text-small">What is the JCR?</span>',
        'description' => 'What is Butler College JCR?',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array(),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'green' => array(
        'id' => 19,
        'title' => 'Green JCR',
        'big_title' => '<span class="big-text-medium">Green JCR</span>',
        'description' => 'Butler JCR is a green JCR, find out how to help keep it that way.',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array(),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'international' => array(
        'id' => 20,
        'title' => 'International',
        'big_title' => '<span class="big-text-tiny">International Committee</span>',
        'description' => 'Butler College has a strong international community',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array(),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'voting' => array(
        'id' => 21,
        'title' => 'Voting',
        'big_title' => NULL,
        'description' => 'Vote in Butler JCR or Durham University elections',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('voting/voting'),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'projects' => array(
        'id' => 22,
        'title' => 'Projects',
        'big_title' => '<span class="big-text-medium">Projects</span>',
        'description' => 'A hub for requests and documentation within the JCR',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => FALSE,
        'css' => array(),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'markets' => array(
        'id' => 23,
        'title' => 'Markets',
        'big_title' => '<span class="big-text-small">Durham Markets</span>',
        'description' => 'Request deliveries from Durham Market',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array(),
        'js' => array('markets/markets'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'game' => array(
        'id' => 24,
        'title' => 'Games',
        'big_title' => NULL,
        'description' => 'Play a game against the JCR',
        'requires_login' => TRUE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array(),
        'js' => array('game/game'),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'charities' => array(
        'id' => 25,
        'title' => 'Charities',
        'big_title' => NULL,
        'description' => 'All things charitable, in the JCR',
        'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
        'css' => array('charities/charities'),
        'js' => array('charities/charities', 'charities/dare_night/dare_night'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'family_tree' => array(
        'id' => 26,
        'title' => 'Family Tree',
        'big_title' => '<span class="big-text-small">Family Tree</span>',
        'description' => 'JCR, Family Tree',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => FALSE,
        'css' => array('family_tree/family_tree'),
        'js' => array('family_tree/family_tree'),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'finance' => array(
        'id' => 27,
        'title' => 'Finance',
        'big_title' => '<span class="big-text-small">My Finances</span>',
        'description' => 'JCR, Sports and Society finances',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => TRUE,
        'css' => array('finance/finance', 'finance/notifications/notifications'),
        'js' => array('finance/finance', 'finance/invoices/invoices', 'finance/notifications/notifications'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'faults' => array(
        'id' => 28,
        'title' => 'Faults',
        'big_title' => NULL,
        'description' => 'Report missing or damaged items',
        'requires_login' => TRUE,
        'allow_non-butler' => FALSE,
        'require-secure' => FALSE,
<<<<<<< HEAD
        'css' => array(''),
        'js' => array('faults/faults'),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'scheduling' => array(
        'id' => 29,
        'title' => 'Scheduling',
        'big_title' => NULL,
        'description' => '',
        'requires_login' => FALSE,
                'allow_non-butler' => TRUE,
                'require-secure' => FALSE,
        'css' => array(''),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => FALSE
    ),
    'useful' => array(
        'id' => 30,
        'title' => 'Useful Info',
        'big_title' => NULL,
        'description' => 'Just some generally useful information',
        'requires_login' => FALSE,
                'allow_non-butler' => FALSE,
                'require-secure' => FALSE,
        'css' => array(),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'bookings' => array(
        'id' => 31,
        'title' => '<span class="big-text-small">Room Bookings</span>',
                'description' => '',
        'requires_login' => TRUE,
                'allow_non-butler' => FALSE,
                'require-secure' => FALSE,
        'css' => array(),
        'js' => array('bookings/bookings'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'ballot' => array(
        'id' => 32,
        'title' => 'Ballot',
        'big_title' => NULL,
        'description' => '',
        'requires_login' => TRUE,
                'allow_non-butler' => TRUE,
                'require-secure' => FALSE,
        'css' => array('ballot/ballot'),
        'js' => array('ballot/ballot'),
        'keep_cache' => FALSE,
        'editable' => TRUE
    ),
    'volunteering' => array(
        'id' => 33,
        'title' => 'Volunteering Projects',
        'big_title' => '<span class="big-text-medium">Projects</span>',
        'description' => 'Butler JCR is involved in many volunteering project, find out more.',
        'requires_login' => FALSE,
                'allow_non-butler' => TRUE,
                'require-secure' => FALSE,
        'css' => array(),
        'js' => array(),
        'keep_cache' => FALSE,
        'editable' => TRUE
    )

=======
		'css' => array(''),
		'js' => array('faults/faults'),
		'keep_cache' => FALSE,
		'editable' => FALSE
	),
	'scheduling' => array(
		'id' => 29,
		'title' => 'Scheduling',
		'big_title' => NULL,
		'description' => '',
		'requires_login' => FALSE,
        'allow_non-butler' => TRUE,
        'require-secure' => FALSE,
		'css' => array(''),
		'js' => array(),
		'keep_cache' => FALSE,
		'editable' => FALSE
	),
	'useful' => array(
		'id' => 30,
		'title' => 'Useful Info',
		'big_title' => NULL,
		'description' => 'Just some generally useful infomation',
		'requires_login' => FALSE,
	    'allow_non-butler' => FALSE,
        'require-secure' => FALSE,
		'css' => array(),
		'js' => array(),
		'keep_cache' => FALSE,
		'editable' => TRUE
	)
>>>>>>> parent of d723000... Ballot Updates v1.0
);