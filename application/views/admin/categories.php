<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('admin');

eval(error_code());
$token = token_ip('manage_categories');

// prepare the user names dropdown box
$level_dropdown = array();
$level_dropdown[0] = '';
foreach($levels as $l) {
	$level_dropdown[$l['id']] = $l['full'];
}
?>

<div class="jcr-box">
	<h3>Add a Category</h3>
	<p>Both fields are required.</p>
	<div class="inline-block">
		<?php echo form_open('admin/categories', array('class' => 'jcr-form'));
		echo form_input(array(
			'name' => 'name',
			'maxlength' => '50',
			'class' => 'input-help narrow-full',
			'placeholder' => 'Category Name',
			'required' => 'required',
			'title' => 'Required field. Please enter a name for the category, it must be unique and less than 50 characters.'
		));
		echo form_dropdown('leader', $level_dropdown, '', 'class="input-help narrow-full" required="required" title="Required field. Choose a role to associate as the leader of this category."');
		echo $token;
		echo form_submit('add_category', 'Add');
		echo form_close(); ?>
	</div>
</div>

<div class="jcr-box">
	<h3>Rename or Delete Categories</h3>
	<p>For each category, both fields are required.</p>

	<?php foreach($categories as $c) {
		echo '<div class="row inline-block">';
		echo form_open('admin/categories', array('class' => 'jcr-form'));
		echo form_input(array(
			'name' => 'name',
			'value' => $c['name'],
			'maxlength' => '50',
			'class' => 'input-help narrow-full',
			'placeholder' => 'Category Name',
			'title' => 'Required field. Please enter a name for the category, it must be unique and less than 50 characters.'
		));
		echo form_dropdown('leader', $level_dropdown, $c['leader'], 'class="input-help narrow-full" required="required" title="Required field. Choose a role to associate as the leader of this category."');
		echo $token;
		echo form_hidden('category_id', $c['id']);
		echo form_submit('edit_category', 'Update');
		echo form_submit('delete_category', 'Delete');
		echo form_close();
		echo '</div>';
	} ?>
</div>