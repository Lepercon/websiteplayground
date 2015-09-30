<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('admin'); ?>

<h2>Get help with sections of the website</h2>
<?php $groups = array(
    'Media' => array(
        'photos' => 'Photos and Videos'
    ),
    'Admin' => array(
        'admin' => 'Site admin'
    ),
    'Page Management' => array(
        'editor' => 'Page content editor'
    ),
    'Events' => array(
        'events' => 'Events Management',
        'signup' => 'Signup',
        'news' => 'News Posts',
        'posters' => 'Posters'
    ),
    'Other' => array(
        'requests' => 'JCR requests'
    )
);
foreach($groups as $k => $sections) {
    echo heading($k, 3).'<ul style="padding-left:40px;">';
    foreach($sections as $key => $val){
        echo '<li>'.anchor('admin/help/'.$key, $val, array('style'=>'font-weight:normal;')).'</li>';
    }
    echo '</ul>';
}
?>