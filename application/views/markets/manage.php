<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('markets');

$token = token_ip('add_item');

eval(error_code()); ?>

<h2>Add meals</h2>
<?php echo form_open('markets/manage', array('class' => 'jcr-form')); ?>
<ul class="nolist">
    <li>
    <?php echo form_input(array(
        'name' => 'meal',
        'value' => ($errors ? set_value('meal') : ''),
        'maxlength' => '200',
        'required' => 'required',
        'class' => 'input-help',
        'placeholder' => 'Meal',
        'title' => 'Required field. Enter the meal name.'
    )); ?>
    </li>
    <li>
    <?php echo $token;
    echo form_submit('add_meal', 'Add Meal'); ?>
    </li>
</ul>
<?php echo form_close();?>

<h2>Meal list</h2>
<p>Recipe uploads must be pdf and less than 8MB.</p>
<?php
foreach($meals as $m) { ?>
    <div class="jcr-box">
    <ul class="nolist">
        <li><?php echo $m['name']; ?></li>
        <?php if(file_exists(VIEW_PATH.'markets/recipes/'.$m['id'].'.pdf')) { ?>
            <li><a href="<?php echo VIEW_URL.'markets/recipes/'.$m['id'].'.pdf'; ?>" class="no-jsify jcr-button inline-block" title="View the uploaded recipe">
                <span class="ui-icon ui-icon-document inline-block"></span>View Recipe
            </a></li>
        <?php } ?>
        <li><a href="<?php echo site_url('markets/delete_meal/'.$m['id']); ?>" class="jcr-button inline-block" title="Delete this meal from the list">
            <span class="ui-icon ui-icon-trash inline-block"></span>Delete meal
        </a></li>
        <li>
            <?php echo form_open_multipart('markets/add_recipe/'.$m['id'], array('class' => 'no-jsify jcr-form'));
            echo form_upload('userfile');
            echo form_submit('add_recipe', (file_exists(VIEW_PATH.'markets/recipes/'.$m['id'].'.pdf') ? 'Change' : 'Upload').' Recipe');
            echo $token;
            echo form_close(); ?>
        </li>
    </ul>
    </div>
<?php } ?>

<h2>Add groceries</h2>
<p>Use a category which already exists where possible</p>

<?php echo form_open('markets/manage', array('class' => 'jcr-form')); ?>
<ul class="nolist">
    <li>
    <?php echo form_input(array(
        'name' => 'item',
        'value' => ($errors ? set_value('item') : ''),
        'maxlength' => '200',
        'required' => 'required',
        'class' => 'input-help',
        'placeholder' => 'Item',
        'title' => 'Required field. Enter the item name. Maximum 200 characters.'
    )); ?>
    </li>
    <li>
    <?php echo form_input(array(
        'name' => 'unit',
        'value' => ($errors ? set_value('unit') : ''),
        'maxlength' => '20',
        'required' => 'required',
        'class' => 'input-help',
        'placeholder' => 'Item order units',
        'title' => 'Required field. Enter the units which the item is sold in. Maximum 20 characters.'
    )); ?>
    </li>
    <li>
    <?php echo form_input(array(
        'name' => 'category',
        'value' => ($errors ? set_value('category') : ''),
        'maxlength' => '100',
        'required' => 'required',
        'class' => 'input-help',
        'placeholder' => 'Category',
        'title' => 'Required field. Enter a category name for the item. Maximum 100 characters.'
    )); ?>
    </li>
    <li>
    <?php echo form_submit('add_item', 'Add Item');
    echo $token; ?>
    </li>
</ul>
<?php echo form_close(); ?>


<h2>Groceries list</h2>
<p>Click the cross next to an item to remove it from the list.</p>
<ul class="nolist">
<?php
foreach($items as $t) { ?>
    <li>
        <a href="<?php echo site_url('markets/delete_item/'.$t['id']); ?>" class="jcr-button inline-block no-jsify admin-delete-button" title="Delete Item"><span class="ui-icon ui-icon-close"></span></a><?php echo $t['category'].': '.$t['name'].' ('.$t['unit'].')'; ?>
    </li>
<?php } ?>
</ul>