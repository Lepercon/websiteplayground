<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php echo editable_area('liversout', 'content/'.$page, $access_rights); ?>
<h2>Properties in <?php echo ucwords(str_replace("_", " ", $page));?></h2>
<?php $this->load->view('liversout/search_results', array('results' => $results));?>