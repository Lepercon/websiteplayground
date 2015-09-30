<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

eval(error_code());

echo back_link('charities/orders'); ?>

<div class="jcr-box wotw-outer">
    <h2 class="wotw-day">Add New Photo Size</h2>
    <?php echo form_open('charities/sizes', array('class' => 'jcr-form')); ?>
        <ul class="nolist">
            <li>
                <?php echo form_label('Size');
                echo form_input(array(
                    'name' => 'description',
                    'value' => '',
                    'maxlength' => '50',
                    'required' => 'required',
                    'class' => 'input-help',
                    'placeholder' => 'Size',
                    'title' => 'Required field. Enter photo dimensions.'
                )); ?>
            </li>
            <li>
                <?php echo form_label('Price');
                echo form_input(array(
                    'name' => 'price',
                    'value' => '0.00',
                    'maxlength' => '7',
                    'required' => 'required',
                    'class' => 'input-help',
                    'placeholder' => 'Price',
                    'title' => 'Required field. If the size is free, enter 0.'
                )); ?>
            </li>
            <li>
                <?php echo form_label('');
                echo form_submit('add', 'Add'); ?>
            </li>
        </ul>
    <?php echo form_close(); ?>
</div>

<h2 class="bold-header">Current Photo Sizes</h2>

<?php foreach($sizes as $s) { ?>
    <div class="jcr-box">
        <?php echo form_open('charities/sizes', array('class' => 'jcr-form')); ?>
            <ul class="nolist">
                <li>
                    <?php echo form_label('Size');
                    echo form_input(array(
                        'name' => 'description',
                        'value' => $s['description'],
                        'maxlength' => '50',
                        'required' => 'required',
                        'class' => 'input-help',
                        'placeholder' => 'Size',
                        'title' => 'Required field. Enter photo dimensions.'
                    )); ?>
                </li>
                <li>
                    <?php echo form_label('Price');
                    echo form_input(array(
                        'name' => 'price',
                        'value' => $s['price'],
                        'maxlength' => '7',
                        'required' => 'required',
                        'class' => 'input-help',
                        'placeholder' => 'Price',
                        'title' => 'Required field. If the size is free, enter 0.'
                    )); ?>
                </li>
                <li>
                    <?php echo form_hidden('id', $s['id']);
                    echo form_label('');
                    echo form_submit('update', 'Update');
                    if($s['id'] !== '1') {
                        echo form_submit('delete', 'Delete');
                    } ?>
                </li>
            </ul>
        <?php echo form_close(); ?>
    </div>
<?php } ?>