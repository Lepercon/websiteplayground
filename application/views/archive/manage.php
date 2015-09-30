<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$token = token_ip('archive_manage_sections');

echo back_link('archive');
eval(error_code().success_code());
?>
<div class="jcr-box">
    <h3>Add new</h3>
    <?php echo form_open('archive/manage', array('class' => 'jcr-form')); ?>
        <ul>
            <li>
                <input type="text" class="input-help" name="new-section" maxlength="50" placeholder="Section Name" required="required" title="Under 50 characters, A-Z or 0-9 only"/>
            </li><li>
                <input type="submit" value="Create new section" />
            </li>
        </ul>
        <?php echo $token;
    echo form_close(); ?>
</div>

<?php if(!empty($sections)) : ?>
<div class="jcr-box">
    <h3>Delete existing</h3>
    <p>Deleting a section will delete all associated documents</p>
    <?php echo form_open('archive/manage', array('class' => 'jcr-form')); ?>
        <ul class="nolist">
        <?php foreach($sections as $s) : ?>
            <li>
                <input type="radio" name="delete" value="<?php echo $s['id']; ?>" /><?php echo ' '.$s['full']; ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <input type="submit" value="Delete" />
        <?php echo $token;
    echo form_close(); ?>
</div>
<?php endif; ?>