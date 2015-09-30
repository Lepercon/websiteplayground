<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  ?>

<div id="md-nav" class="<?php echo $tab;?> narrow-hide">
    <ul class="nolist">
        <?php echo '<li id="md-photos">'.anchor('photos', 'Photos').'</li>';
        echo '<li id="md-videos">'.anchor('photos/videos', 'Videos').'</li>';?>
    </ul>
</div>