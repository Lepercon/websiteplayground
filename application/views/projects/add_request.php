<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

eval(error_code()); ?>

<h2>Add a project request</h2>
<p>For a Publicity Committee request, please confirm the event name, date, time &amp; venue. If applicable, give the price, colour scheme or theme.</p>
<?php echo form_open('projects/add_request', array('class' => 'jcr-form')); ?>
<ul class="nolist">
    <li>
        <label>Request Title *</label><?php echo form_input(array(
            'name' => 'title',
            'value' => ($errors? set_value('title') : ''),
            'max_length' => '50',
            'placeholder' => 'Request Title',
            'class' => 'input-help narrow-full',
            'required' => 'required',
            'title' => 'Required field. Enter a title for your request.'
        )); ?>
    </li>
    <li>
        <label>Request Category *</label><select name="category" class="input-help narrow-full" title="Select the category your request should relate to. When submitting your request, an email will be sent to the category leader to notify them.">
            <option></option>
            <?php foreach($categories as $c) echo '<option value="'.$c['id'].'" '.($c_id == $c['id'] ? 'selected="selected"' : set_select('category', $c['id'])).'>'.$c['name'].'</option>'; ?>
        </select>
    </li>
    <li>
        <label>Description *</label><?php echo form_textarea(array(
            'name' => 'description',
            'value' => set_value('description'),
            'rows' => '6',
            'class' => 'input-help narrow-full',
            'title' => 'Required field. Give a description of your request. If it is a Publicity Committee request, please confirm the event name, date, time & venue. If applicable, give the price, colour scheme or theme.',
            'placeholder' => 'Request Description'
        )); ?>
    </li>
    <li>
        <label></label>
        <?php if(isset($e_id)) echo form_hidden('event', $e_id);
        echo token_ip('projects');
        echo form_submit('add_request', 'Submit Request'); ?>
    </li>
</ul>