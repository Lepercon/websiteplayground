<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('photos'); ?>
<div class="basket-wrapper">
    <div class="width-50 inline-block content-left">
        <?php echo '<h3>'.$album['name'].' ('.(isset($album['photos'])? sizeof($album['photos']) :0).')</h3><p>'.$album['description'].'</p><p><i>'.date('jS M Y', $album['date']).'</i></p>'; ?>
    </div>
    <div class="width-50 inline-block content-right basket-preview">
        <h2>Loading Basket...</h2>
    </div>
    <div class="spacer" style="clear: both;"></div>
</div>

<?php echo editable_area('photos', 'content/album_'.$album['id'], is_admin()); ?>
<?php if(is_admin()){ ?>
    <p class="padded">
        <a href="<?php echo site_url('photos/upload/'.$album['id']); ?>" class="jcr-button"><span class="ui-icon ui-icon-plus"></span>Upload Photos</a>
        <a href="<?php echo site_url('photos/edit/'.$album['id']); ?>" class="jcr-button"><span class="ui-icon ui-icon-pencil"></span>Edit Album</a>
        <a href="<?php echo site_url('photos/unpublished/'.$album['id']); ?>" class="jcr-button"><span class="ui-icon ui-icon-document"></span>Publish Photos</a>
        <a href="<?php echo site_url('photos'); ?>" class="jcr-button delete-album no-jsify"><span class="ui-icon ui-icon-trash"></span>Delete Album</a>
    </p>
<?php } ?>

<div id="photo-view">
<?php 
    foreach($album['photos'] as $p){ 
        $dims = getimagesize($path.$p['photo_name']);
?>
        <div class="photo-container">
            <a href="<?php echo site_url('photos/photo/'.$p['id']); ?>" class="photo-link no-jsify">
                <div class="thumb photo-thumb" image="<?php echo $url.$p['photo_name']; ?>" style="background-image:url(<?php echo $url.$p['thumb_name']; ?>)">
                    <span class="image-info uploader-name"><?php echo ($p['prefname']=''?$p['firstname']:$p['prefname']).' '.$p['surname']; ?></span>
                    <span class="image-info uploader-icon"><?php echo get_usr_img_src($p['uid'], 'small'); ?></span>
                    <span class="image-info date"><?php echo date('l jS F Y', $p['timestamp']); ?></span>
                    <span class="image-info p-id"><?php echo $p['id']; ?></span>
                    <span class="image-info uploader-tagged">
                        <h3>People in this photo:</h3>
                        <div class="list-of-tagged">
                            <?php
                                if(isset($p['tags'])){
                                    foreach($p['tags'] as $t){
                                        echo '<p value="'.$t['u_id'].'" x="'.$t['x'].'" y="'.$t['y'].'">'.($t['prefname']==''?$t['firstname']:$t['prefname']).' '.$t['surname'].'</p>';
                                    }
                                }
                            ?>
                        </div>
                        <h2 class="basket-add"><span class="ui-icon inline-block ui-icon-cart gold-icon"></span>Add To Basket</h2>
                    </span>
                    <span class="image-info width"><?php echo $dims[0]; ?></span>
                    <span class="image-info height"><?php echo $dims[1]; ?></span>
                </div>
            </a>
        </div>
<?php
    }
?>
</div>
<span id="album-name"><?php echo $album['name']; ?></span>
<span id="album-id"><?php echo $album['id']; ?></span>
<div id="list-of-users">
    <?php 
        foreach($users as $u){
            echo '<p value="'.$u['id'].'">'.($u['prefname']==''?$u['firstname']:$u['prefname']).' '.$u['surname'].'</p>';
        }
    ?>
</div>
<div id="type-selection"><p>What size of photo would you like?</p>
<?php
    foreach($sizes as $s){
        $types[$s['id']] = $s['description'].' (£'.$s['price'].')';
    }
    echo form_dropdown('type-select', $types, '', 'class="type-select"');
?></div>

