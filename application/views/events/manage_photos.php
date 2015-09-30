<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('events/view_event/'.$e_id);

$token = token_ip('manage_photos');

if(!empty($photos)) {
    foreach($photos as $p) { ?>
        <div class="jcr-box">
            <a class="photo-thumb photo-view inline-block no-jsify" rel="event_<?php echo $p['event_id']; ?>"<?php if(!empty($p['description'])) echo ' title="'.$p['description'].'"'; ?> href="<?php echo VIEW_URL.'events/photos/event_'.$p['event_id'].'/'.$p['filename'].'_large.jpg'; ?>">
                <img alt="<?php echo $p['description']; ?>" class="inline-block" src="<?php echo VIEW_URL.'events/photos/event_'.$p['event_id'].'/'.$p['filename'].'_thumb.jpg'; ?>" />
            </a>
            <p>Uploaded by <?php echo $this->users_model->get_full_name($p['submitted_by']);?> on <?php echo date('l jS F Y \a\t H:i',$p['submitted_time']); ?></p>
            <?php echo form_open('events/manage_photos/'.$e_id, array('class' => 'jcr-form'));
            echo form_input(array(
                'name' => 'description',
                'value' => $p['description'],
                'maxlength' => '200',
                'class' => 'input-help',
                'title' => 'Optional Field. Give a description of the photo. Maximum 200 characters',
                'placeholder' => 'Photo Description'
            ));
            echo $token;
            echo form_hidden('image_id', $p['id']);
            echo form_submit('manage_photos', 'Save Changes');
            echo form_close();?>
            <a class="inline-block jcr-button admin-delete-button no-jsify" title="Delete Photo" href="<?php echo site_url('events/delete_photo/'.$p['event_id'].'/'.$p['id']); ?>">
                <span class="ui-icon ui-icon-close inline-block"></span>Delete
            </a>
        </div>
    <?php }
}
else { ?>
    <h3>There are no photos for this event</h3>
<?php } ?>