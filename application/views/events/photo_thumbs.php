<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!empty($photos)) {
    $event_id = '';
    foreach($photos as $p) { ?>
        <a class="photo-thumb photo-view inline-block no-jsify" rel="event_<?php echo $p['event_id']; ?>"<?php if(!empty($p['description'])) echo ' title="'.$p['description'].'"'; ?> href="<?php echo VIEW_URL.'events/photos/event_'.$p['event_id'].'/'.$p['filename'].'_large.jpg'; ?>">
            <img alt="<?php echo $p['description']; ?>" class="inline-block" src="<?php echo VIEW_URL.'events/photos/event_'.$p['event_id'].'/'.$p['filename'].'_thumb.jpg'; ?>" />
        </a>
        <?php if(empty($event_id)) {
            $event_id = $p['event_id'];
        }
    } ?>
    <div>
    <a href="<?php echo site_url('events/download_photos/'.$event_id); ?>" class="jcr-button inline-block no-jsify" title="Download this photo album">
        <span class="ui-icon ui-icon-arrowthick-1-s inline-block"></span>Download
    </a>
    <?php if(is_admin()) { ?>
    <a href="<?php echo site_url('events/manage_photos/'.$event_id); ?>" class="jcr-button inline-block" title="Manage this photo album">
        <span class="ui-icon ui-icon-gear inline-block"></span>Manage
    </a>
    <?php } ?>
    </div>
<?php } else { ?>
    <p>There are no photos for this event.</p>
<?php } ?>