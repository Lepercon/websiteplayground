<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('admin');
eval(success_code().error_code());

$level_types = array('exec', 'assistants', 'sports', 'societies', 'committees', 'support', 'services', 'staff');

echo form_open('admin/levels', array('class' => 'jcr-form')); ?>
    <h2>New Admin Level</h2>
    <ul class="nolist">
        <li>
            <label for="full">Level Name</label><input type="text" class="full-level input-help" placeholder="Level Name" title="Required Field when creating a new level. The full name of the level. Maximum 50 characters" name="full" value="<?php echo ($errors ? set_value('full') : '');?>" />
        </li>
        <li>
            <label for="full_access">Full Site Access</label><input type="checkbox" name="full_access" value="1" <?php echo $errors ? set_checkbox('full_access', '1') : ''; ?> />
        </li>
        <li>
            <label>Level Type</label><select name="type">
                <?php foreach($level_types as $type) echo '<option value="'.$type.'" '.($errors && $_POST['type'] == $type ? 'selected="selected"' : '').'>'.ucfirst($type).'</option>'; ?>
            </select>
        </li>
        <li>
            <label></label><?php echo form_submit('add', 'Add'); ?>
        </li>
    </ul>
<?php echo form_close();?>

<h2>Admin Levels</h2>
    <span id="list-of-users" style="display:none">
    <?php 
        foreach($users as $id => $name) {
            echo '<p value="'.$id.'">'.$name.'</p>';
        }
    ?>
    </span>
    <div id="accordion">
        <?php foreach($levels as $l):
            $val['full'] = $l['full'];
            $val['check'] = ($l['full_access'] ? 'checked="checked"' : '');
            $val['type'] = $l['type'];
            ?>
            <h2 class="level-<?php echo $l['id']; ?>"><?php echo $val['full']; ?></h2>
            <div class="levels level-<?php echo $l['id']; ?>">
                
                    <table>
                        <tr>
                            <th>Level Options</th>
                            <td>
                                <?php echo form_open('admin/levels', array('class' => 'jcr-form admin-levels-form no-jsify')); ?>
                                <ul class="nolist">
                                    <li>
                                        <label>Level Name</label>
                                        <?php echo form_input(array(
                                            'class' => 'full-level input-help',
                                            'name' => 'name',
                                            'value' => $val['full'],
                                            'placeholder' => 'Level Name',
                                            'maxlength' => '50',
                                            'title' => 'Required Field. The full name of the level. Maximum 50 characters.'
                                        )); ?>
                                    </li>
                                    <li>
                                        <label class="top">Level Description</label>
                                        <?php echo form_textarea(array(
                                            'class' => 'level-desc input-help',
                                            'name' => 'desc',
                                            'value' => db_to_textarea($l['description']),
                                            'placeholder' => 'Level Description'
                                        )); ?>
                                    </li>
                                    <li>
                                        <?php echo '<label for="full_access_id">Full Site Access</label><input type="checkbox" id="full_access_id" name="full_access" value="1" '.$val['check'].'/>'; ?>
                                    </li>
                                    <li>
                                        <label>Level Type</label><select name="type">
                                        <?php foreach($level_types as $type) {
                                            echo '<option value="'.$type.'" '.($type == $val['type'] ? 'selected="selected"' : '').'>'.ucfirst($type).'</option>';
                                        } ?>
                                        </select>
                                    </li>
                                </ul>
                                <?php 
                                    echo form_hidden('id', $l['id']);
                                    echo form_label().form_submit('save', 'Save');
                                ?>
                                    <button class="jcr-button inline-block admin-level-delete" data-delete="<?php echo $l['id']; ?>" title="Delete Level">
                                        <span class="ui-icon ui-icon-trash inline-block"></span>
                                    </button> 
                                <?php echo form_close(); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Users In Level</th>
                            <td>
                                <table class="in-level-table">
                                    <tr><th>Name</th><th>Year</th><th>Current</th><th>Options</th></tr>
                                    <?php if(!empty($l['user'])) {
                                        foreach($l['user'] as $id => $u) {
                                        //var_dump($u);
                                            $remove = form_open('', 'class="jcr-form no-jsify remove-user"').form_hidden(array('id'=>$u['id'], 'l_id'=>$l['id'])).form_submit('remove', 'Remove').form_close();
                                            $current = form_open('', 'class="jcr-form no-jsify current"').form_hidden(array('id'=>$u['id'], 'l_id'=>$l['id'], 'new'=>$u['current']?0:1)).form_close();
                                            echo '<tr><td><span value="'.$u['id'].'" class="user-id">'.$u['name'].'</td><td>'.$u['year'].'/'.($u['year']+1).'</td><td><a href="#" class="current-link">'.($u['current']?'Yes':'No').'</a>'.$current.'</td><td>'.$remove.'</td></tr>';
                                        }
                                    } ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th>Add New User</th>
                            <td>
                                <?php
                                    echo form_open('', 'class="jcr-form no-jsify add-new-user"');
                                    echo form_label('Name').form_input(array('class'=>'user-list-select level-notuser full-level', 'name'=>'notuser_name_id['.$l['id'].'][]'));
                                    echo form_input(array('type'=>'hidden', 'class'=>'user_id', 'name'=>'notuser_id['.$l['id'].'][]')).'<br>'; 
                                    foreach(range(date('Y'), 2006, -1) as $y){
                                        $year[$y] = $y.'/'.($y+1);
                                    }
                                    echo form_label('Year').form_dropdown('year', $year).'<br>';
                                    echo form_label('Current').form_checkbox('current', 'yes', TRUE).'<br>';
                                    echo form_hidden('level_id', $l['id']);
                                    echo form_label('').form_submit('add-new', 'Add');
                                    echo form_close();
                                ?>
                            </td>
                        </tr>
                    </table>
                <?php ?>
            </div>
        <?php endforeach; ?>
    </div>