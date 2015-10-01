<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$token = token_ip('welfare_supplies');

if($access_rights > 0) : ?>
    <div class="jcr-box">
        <h2>Admin - Manage Supplies</h2>
        <?php echo form_open('welfare/change_supply', array('class' => 'jcr-form')); ?>
            <ul class="nolist">
                <li>
                    <label for="new-provision">Add supply</label>
                    <input type="text" name="new" id="new-provision" placeholder="Add supply" class="narrow-full" />
                </li>
                <li>
                    <label>Remove supply</label>
                    <select name="remove" class="narrow-full">
                        <option></option>
                        <?php foreach($supplies as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li>
                    <label class="narrow-hide"></label><input type="submit" value="Save" />
                    <?php echo $token; ?>
                </li>
            </ul>
        <?php echo form_close(); ?>
        <h2>Admin - Manage Notifications</h2>
            
            <?php echo anchor('welfare/index/notifications', ($section=='notifications'?'class="selected"':'').'Send Delivery Notifications', 'class="page-link jcr-button"'); ?>
    </div>
<?php endif;

echo editable_area('welfare', 'content/supplies', $access_rights); ?>

<div class="jcr-box">
    <?php echo form_open('welfare/request_supply', array('class' => 'jcr-form')); ?>
        <h3>Supplies:</h3>
        <ul class="nolist">
        <?php foreach($supplies as $s) : ?>
            <li>
                <input type="checkbox" id="<?php echo $s['id']?>" name="supplies[]" value="<?php echo $s['id']; ?>" /> <label for="<?php echo $s['id']?>" class="radio-label"><?php echo $s['name']; ?></label>
            </li>
        <?php endforeach; ?>
        </ul>
        <!--<h3>Urgent: <?php echo form_checkbox('urgent', 'urgent', FALSE); ?></h3>-->
        <!--<p>If this request is urgent please tick the above box.</p>-->
        <h3>Delivery Notifcation:</h3>
        <p><input type="checkbox" id="notifcation" name="notifcation" /> By ticking this you will receive a delivery notification when your welfare supplies are ready to collect. This means your request and details will be stored in the database until delivered. Only webmasters will have access to this information and not even the welfare officer will see your details.</p>
        <h3>Room Number (Optional):</h3>
        <p>If you are not worried about anonymity and live in college please leave your room number and your supplies will be delivered directly to your postbox.</p>
        <ul class="nolist">
            <li>
                <input name="room" placeholder="Room Number" class="narrow-full"></input>
            </li>
        </ul>
        <h3>Comments:</h3>
        <ul class="nolist">
            <li>
                <textarea name="comments" placeholder="Comments" class="narrow-full"></textarea>
            </li>
        </ul>
        <?php
            $anon = rand_uppercase(6);
            echo '<h3>Your anonymous code - write this down: '.$anon.'</h3><input type="hidden" name="anon_code" value="'.$anon.'" />';
        ?>
        <ul class="nolist">
            <li>
                <input id="request-button" type="submit" value="Request"/>
                <?php echo $token; ?>
            </li>
        </ul>
    <?php echo form_close(); ?>
</div>