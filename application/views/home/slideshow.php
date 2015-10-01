<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
    $url = VIEW_URL.'photos/images/';
    $images = $this->photos_model->get_slideshow_photos();
?>
<?php if(!empty($images)){ ?>
    <div class="jcr-box">
        <a href="<?php echo site_url('photos'); ?>">
            <div class="slideshow-wrapper">
                <?php
                    foreach($images as $i){
                        echo '<div class="slideshow" style="background-image:url('.$url.$i['thumb_name'].');"></div>';
                    }
                ?>
            </div>
        </a>
    </div>
<?php }elseif(is_admin()){
    echo '<div class="jcr-box">Click <a href="'.site_url('photos').'">here</a> to upload photos to play a slideshow.</div>';
} ?>

