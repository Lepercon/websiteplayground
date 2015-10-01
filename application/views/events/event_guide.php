<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
    echo back_link('events');
    echo editable_area('events', 'content/event_guide', $access_rights);
?>