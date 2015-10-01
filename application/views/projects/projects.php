<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h2>Project Management</h2>
<p>View, contribute and request projects for committees or individuals within the JCR.</p>
<div class="jcr-box">
    <h3>Add a request</h3>
    <p>If you're sure that there isn't already a request like yours, then add it <?php echo anchor('projects/add_request', 'here'); ?>. If your request is associated with an event, you should add it from the event page.</p>
</div>
<div class="jcr-box">
    <h3>Refine List by Category</h3>
    <?php foreach($category_list as $k => $v) {
        echo '<p>'.ucwords($category_names[$k]).' ('.$v.' Project'.($v == 1 ? '' : 's').'): '.anchor('projects/show_category/'.$k, 'Show all projects').', '.anchor('projects/show_incomplete/'.$k, 'Show incomplete projects').'</p>';
    } ?>
</div>
<div class="jcr-box">
    <h3>Project List</h3>
    <?php foreach($requests as $request) $this->load->view('projects/view_request', array('category_names' => $category_names, 'request' => $request)); ?>
</div>