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
    )
);