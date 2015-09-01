<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('admin/help');

echo editable_area('admin', 'content/'.$page, $access_rights);
?>