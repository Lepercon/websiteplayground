<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('liversout');
eval(error_code()); ?>

<div class="jcr-box">
    <?php echo form_open('liversout/add_property', array('class' => 'jcr-form')); ?>
        <h3>Add a Property</h3>
        <ul class="nolist">
            <li>
                <label for="name-label">Property Name/No. *</label><input type="text" name="name" id="name-label" maxlength="30" value="<?php echo set_value('name'); ?>">
            </li>
                <?php for($i=1; $i <= 2; $i++) echo '<li><label for="address'.$i.'-label">Address Line '.$i.($i==1?' *':'').'</label><input type="text" name="address'.$i.'" id="address'.$i.'-label" maxlength="30" value="'.set_value('address'.$i).'"></li>';?>
            <li>
                <label for="postcode-label">Postcode *</label><input type="text" name="postcode" id="postcode-label" maxlength="8" value="<?php echo set_value('postcode'); ?>">
            </li>
            <li>
                <label>Property Type *</label><select name="type">
                    <?php foreach(array('','Bungalow','Flat','Detached','Semi-Detached','Terrace') as $v) {
                        echo '<option value="'.$v.'" '.set_select('type',$v).'>'.$v.'</option>';
                    };?>
                </select>
            </li>
            <li>
                <label>Area *</label><select name="area" id="area-label">
                    <?php foreach(array('','Claypath','Elvet','Gilesgate','Nevilles Cross','Viaduct','Other') as $v) {
                        echo '<option value="'.$v.'" '.set_select('type',$v).'>'.$v.'</option>';
                    }?>
                </select>
                 If Other:
                <input type="text" name="other_area" id="other-label" maxlength="30" value="<?php echo set_value('other_area'); ?>">
            </li>
            <?php foreach(array('bedrooms','bathrooms') as $v) {
                echo '<li><label>Number of '.ucfirst($v).' *</label><select name="'.$v.'"><option></option>';
                for($i=1; $i <= 12; $i++) echo '<option value="'.$i.'" '.set_select($v,$i).'>'.$i.'</option>';
                echo '</select></li>';
            }?>
            <li>
                <label></label><input type="submit" value="Done, now Review it" />
            </li>
            <?php echo token_ip('add_property'); ?>
        </ul>
    <?php echo form_close(); ?>
</div>