<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="jcr-box wotw-outer">
    <h2 class="wotw-day">Report Problems</h2>
    <div class="padding-box">
        <p>Report a problem such as a missing or damaged item</p>
        <?php echo form_open('faults/report', array('class' => 'jcr-form'));?>
            <ul class="nolist">
                <li><label>Description</label><?php echo form_textarea(array(
                    'name' => 'description',
                    'value' => ($errors ? $this->input->post('description') : ''),
                    'required' => 'required',
                    'class' => 'input-help',
                    'placeholder' => 'Description',
                    'title' => 'Required field. Give a description of the problem',
                    'rows' => '6'
                )); ?></li>
                <li><label>Location</label><?php echo form_input(array(
                    'name' => 'location',
                    'value' => ($errors ? $this->input->post('location') : ''),
                    'class' => 'input-help',
                    'placeholder' => 'Location',
                    'title' => 'If relevant, give the location of the problem'
                )); ?></li>
                <li><label></label><?php echo form_submit('report', 'Report Problem'); ?></li>
            </ul>
        <?php echo form_close(); ?>
    </div>
</div>

<?php if(is_admin()) { ?>
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">View reports</h2>
        <div class="padding-box">
            <?php if(!empty($faults)) { ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Report By</th>
                        <th>Report On</th>
                        <th>Delete</th>
                    </tr>
                    <?php foreach($faults as $f) { ?>
                        <tr>
                            <td><?php echo $f['id']; ?></td>
                            <td><?php echo $f['location']; ?></td>
                            <td><?php echo $f['description']; ?></td>
                            <td><?php echo user_pref_name($f['firstname'], $f['prefname'], $f['surname']); ?></td>
                            <td><?php echo date('d/m/Y', $f['reported']); ?></td>
                            <td><a href="<?php echo site_url('faults/delete/'.$f['id']); ?>" class="faults-delete jcr-button inline-block no-jsify" title="Delete this report"><span class="ui-icon ui-icon-trash inline-block"></span></a></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>There are no problem reports at the moment.
            <?php } ?>
        </div>
    </div>
<?php } ?>