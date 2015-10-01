<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('charities/orders'); ?>

<div class="content-left width-33 narrow-full">
    <?php $this->load->view('charities/basket'); ?>
</div>

<div class="content-right width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day"><?php echo $album['title']; ?></h2>
        <div class="padding-box">
            <?php if(!empty($album['description'])) { ?>
                <p><?php echo $album['description']; ?></p>
            <?php }?>
            <p><?php echo date('jS F Y',$album['event_time']); ?></p>
            <?php if(has_level('any')) { ?>
                <a href="<?php echo site_url('charities/upload/'.$album['id']); ?>" class="jcr-button no-jsify inline-block" title="Manage this album">
                    <span class="ui-icon ui-icon-wrench inline-block"></span>
                </a>
            <?php }
            if($access_rights > 0 || $album['created_by'] == $this->session->userdata('id')) { ?>
                <a href="<?php echo site_url('charities/delete_album/'.$album['id']); ?>" class="jcr-button admin-delete-button inline-block" title="Delete this album">
                    <span class="ui-icon ui-icon-trash inline-block"></span>
                </a>
            <?php }
            if(!empty($photos)) { ?>
            <table class="charity-table">
                <?php $i = 0;
                foreach($photos as $p) {
                    if($i % 3 == 0){
                        ?><tr><?php
                    } ?>
                    <td class="charity-table-cell">
                        <a href="<?php echo site_url('charities/view_photo/'.$p['id']); ?>">
                            <div class="charity-photo-block">
                                <img width="200px" alt="photo_<?php echo $p['id']; ?>" title="Photo <?php echo $p['id']; ?>" src="<?php echo VIEW_URL.'charities/photos/album_'.$p['album_id'].'/'.$p['filename'].'.png'; ?>">
                            </div>
                        </a>
                        <a href="<?php echo site_url('charities/view_photo/'.$p['id']); ?>" class="jcr-button no-jsify inline-block" title="Buying options for this photo">
                            <span class="ui-icon ui-icon-cart inline-block"></span>Photo <?php echo $p['id']; ?>
                        </a>
                        <?php if($access_rights > 0 || $p['uploaded_by'] == $this->session->userdata('id') || $album['created_by'] == $this->session->userdata('id')) { ?>
                            <a href="<?php echo site_url('charities/delete_photo/'.$p['id']); ?>" class="jcr-button admin-delete-button no-jsify inline-block" title="Delete this photo">
                                <span class="ui-icon ui-icon-trash inline-block"></span>
                            </a>
                        <?php } ?>
                    </td>
                <?php
                    $i++;
                    if($i % 3 == 0){
                        ?></tr><?php
                    }
                } ?>
            </table>
            <?php } else { ?>
                <p>There are no photos in this album.</p>
            <?php } ?>
        </div>
    </div>
</div>
