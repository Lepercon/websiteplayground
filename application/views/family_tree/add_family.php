<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

eval(error_code());
if(!isset($errors)) $errors = FALSE;

if(isset($success)) echo "<p class='validation_success'>Family Added</p>";

$parent[0]='';
foreach($parents as $p){
    $parent[$p['id']]=$p['name'].' '.$p['surname'];
}
                
$year = range(date('Y'), 2006);
foreach($year as $y){
    $years[$y]=$y;
}

?>
<div class="jcr-box">
    <h3>Add Family</h3>
    <p>Please use full names, not nick names.<br />
    Type the first few letters of the name, then select it from the drop-down list.<br /></p>
    <?php echo form_open('family_tree/new_family/', array('class' => 'jcr-form')); ?>
    <ul class="nolist">
        <li id="partent-names">
            <label class="narrow-full">Parent 1</label>
            <?php echo form_dropdown('parent_1', $parent, '0'); ?></br>
            <label class="narrow-full">Parent 2</label>
            <?php echo form_dropdown('parent_2', $parent, '0'); ?></br>
            <label class="narrow-full">Parent 3</label>
            <?php echo form_dropdown('parent_3', $parent, '0'); ?></br>
            <label class="narrow-full">Parent 4</label>
            <?php echo form_dropdown('parent_4', $parent, '0'); ?>
        </li>
        
        <li id="child-names">
            <ul id="children">
                <li id="child1">
                    <label class="narrow-full">Child</label>
                    <?php echo form_input(array(
                        'name' => 'first_name[]',
                        'placeholder' => 'First Name',
                        'title' => 'Optional field'
                    )); ?>
                    <?php echo form_input(array(
                        'name' => 'last_name[]',
                        'placeholder' => 'Surname',
                        'title' => 'Optional field'
                    )); ?>
                    <?php echo form_dropdown('year[]', $years, date('Y')); ?>
                </li>
            </ul>
        </li>
        <span class="child-add ui-icon ui-icon-plusthick inline-block"></span>
    </ul>
    <?php 
    echo form_submit('new_family', 'Create Family');
    echo token_ip('new_family');
    echo form_close();
    ?>
</div>