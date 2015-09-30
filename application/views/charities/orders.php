<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

eval(error_code()); ?>

<div class="content-left width-33 narrow-full">
    <?php $this->load->view('charities/basket'); ?>
</div>

<div class="content-right width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Charity Photo Orders</h2>
        <div class="padding-box">
            <?php if(has_level('any')) { ?>
                <a href="" id="charity-create-album-button" class="jcr-button no-jsify inline-block" title="Create new photo album">
                    <span class="ui-icon ui-icon-plusthick inline-block"></span>
                </a>
            <?php }
            if($access_rights > 0) { ?>
                <a href="<?php echo site_url('charities/sizes'); ?>" class="jcr-button inline-block" title="Manage available photo formats">
                    <span class="ui-icon ui-icon-wrench inline-block"></span>
                </a>
                <a href="<?php echo site_url('charities/list_orders'); ?>" class="jcr-button inline-block" title="View all orders">
                    <span class="ui-icon ui-icon-document inline-block"></span>
                </a>
            <?php }
            echo editable_area('charities', 'content/orders', $access_rights); ?>
            <ul class="nolist">
            <?php foreach($sizes as $s) { ?>
                <li><?php echo $s['description'].' - &pound;'.$s['price']; ?></li>
            <?php } ?>
            </ul>
        </div>
    </div>
    <?php if(has_level('any')) { ?>
        <div id="charity-create-album" class="jcr-box wotw-outer">
            <h2 class="wotw-day">Create new album</h2>
            <div class="padding-box">
                <?php echo form_open('charities/add_album', array('class' => 'jcr-form')); ?>
                    <ul class="nolist">
                        <li><?php
                        echo form_label('Album Title', 'charity-album-title');
                        echo form_input(array(
                            'name' => 'title',
                            'id' => 'charity-album-title',
                            'class' => 'input-help',
                            'placeholder' => 'Album Title',
                            'title' => 'Required field. Enter the album title.',
                            'required' => 'required',
                            'maxlength' => '50'
                        )); ?></li><li><?php
                        echo form_label('Album Description', 'charity-album-description');
                        echo form_input(array(
                            'name' => 'description',
                            'id' => 'charity-album-description',
                            'class' => 'input-help',
                            'placeholder' => 'Album Description',
                            'title' => 'Optional field. Enter a description for the album.'
                        )); ?></li><li><?php
                        echo form_label('Album Date', 'charity-album-date');
                        echo form_input(array(
                            'name' => 'event_time',
                            'id' => 'charity-album-date',
                            'value' => date('d/m/Y'),
                            'required' => 'required',
                            'maxlength' => '10',
                            'class' => 'datepicker',
                            'placeholder' => 'Album Date'
                        )); ?></li><li><?php
                        echo form_label('');
                        echo form_submit('create', 'Create');
                        ?></li>
                    </ul>
                <?php echo form_close(); ?>
            </div>
        </div>
    <?php } ?>
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Photo Albums</h2>
        <div class="padding-box">
            <?php if(!empty($albums)) {
                foreach($albums as $a) { ?>
                    <div>
                        <a href="<?php echo site_url('charities/view_album/'.$a['id']);?>"><?php echo $a['title'].' - '.date('jS F Y',$a['created_time']);?></a>
                        <p><?php echo $a['description']; ?> </p>
                        <a href="<?php echo site_url('charities/view_album/'.$a['id']);?>"><img src="<?php echo VIEW_URL.'charities/photos/album_'.$a['id'].'/'.$a['thumb']; ?>" width="200px;"/></a>
                        <hr class="separator"/>
                    </div>
                <?php }
            } else { ?>
                <p>There are no albums at the moment.</p>
            <?php } ?>
        </div>
    </div>
</div>