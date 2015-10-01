<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('photos/album/'.$album['id']);?>

<?php
    $prev = NULL;
    $next = NULL;
    $found = FALSE;
    $i = 0;
    foreach($album['photos'] as $p){
        $i++;
        if($p['id'] == $photo['id']){
            $found = $i;
        }elseif($found){
            $next = $p;
            break;
        }else{
            $prev = $p;
        }
    }
?>

<div class="basket-wrapper">
    <div class="width-50 inline-block content-left">
        <h2><?php echo $album['name'].' ('.sizeof($album['photos']).')'; ?></h2>
        <p><?php echo $album['description']; ?></p>
        <p><i><?php echo date('jS M Y', $album['date']); ?></i></p>
    </div>
    <div class="width-50 inline-block content-right basket-preview">
        <h2>Loading Basket...</h2>
    </div>
    <div class="spacer" style="clear: both;"></div>
</div>


<?php if(is_admin()){ ?>
    <p class="padded">
        <a href="#" class="jcr-button rotate-photo no-jsify alt">Rotate<span class="ui-icon ui-icon-arrowreturnthick-1-w flip-icon"></span></a>
        <a href="#" class="jcr-button rotate-photo no-jsify"><span class="ui-icon ui-icon-arrowreturnthick-1-w"></span>Rotate</a>
        <a href="#" class="jcr-button delete-photo no-jsify"><span class="ui-icon ui-icon-trash"></span>Delete</a>
    </p>
<?php } ?>
<h2 class="basket-add middle"><span class="ui-icon inline-block ui-icon-cart gold-icon"></span>Add To Basket</h2>
<div id="next-prev">
    <?php if(is_null($prev)){ ?>
        <div class="prev-photo no-link"></div>
    <?php }else{ ?>
        <a href="<?php echo site_url('photos/photo/'.$prev['id']); ?>"><div class="prev-photo"></div></a>
    <?php } ?>
    <div class="text">Photo <?php echo $found; ?> of <?php echo sizeof($album['photos']); ?></div>
    <?php if(is_null($next)){ ?>
        <div class="next-photo no-link"></div>
    <?php }else{ ?>
        <a href="<?php echo site_url('photos/photo/'.$next['id']); ?>"><div class="next-photo"></div></a>
    <?php } ?>
</div>

<div id="image-container"><img src="<?php echo $path.$photo['photo_name']; ?>" value="<?php echo $path.$photo['photo_name']; ?>"></div>

<span id="spinner-link"><?php echo VIEW_URL.'photos/spinner.gif'; ?></span>
<span id="album-name"><?php echo $album['name']; ?></span>
<span id="photo-id"><?php echo $photo['id']; ?></span>
<div id="type-selection"><p>What size of photo would you like?</p>
<?php
    foreach($sizes as $s){
        $types[$s['id']] = $s['description'].' (£'.$s['price'].')';
    }
    echo form_dropdown('type-select', $types, '', 'class="type-select"');
?></div>

