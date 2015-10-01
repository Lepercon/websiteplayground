<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('charities/view_album/'.$album['id']);

eval(error_code()); ?>

<div class="jcr-box wotw-outer">
    <h2 class="wotw-day">Edit Album Information</h2>
    <?php echo form_open('charities/edit_album/'.$album['id'], array('class' => 'jcr-form')); ?>
    <ul class="nolist">
    <li><?php
    echo form_label('Album Title', 'charity-album-title');
    echo form_input(array(
        'name' => 'title',
        'id' => 'charity-album-title',
        'value' => $album['title'],
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
        'value' => $album['description'],
        'class' => 'input-help',
        'placeholder' => 'Album Description',
        'title' => 'Optional field. Enter a description for the album.'
    )); ?></li><li><?php
    echo form_label('Album date', 'charity-album-date');
    echo form_input(array(
        'name' => 'event_time',
        'id' => 'charity-album-date',
        'value' => date('d/m/Y', $album['event_time']),
        'required' => 'required',
        'maxlength' => '10',
        'class' => 'datepicker',
        'placeholder' => 'Album date'
    )); ?></li><li><?php
    echo form_label('');
    echo form_submit('update', 'Update');
    echo token_ip('charity_album');
    ?></li>
    </ul>
    <?php echo form_close(); ?>
</div>

<div class="jcr-box wotw-outer">
    <h2 class="wotw-day">Upload files to <?php echo $album['title']; ?></h2>

    <?php echo form_open('charities/upload', array('id' => 'charity-upload-form', 'class' => 'jcr-form no-jsify')); ?>
    <input type="hidden" id="album-id" value="<?php echo $album['id']; ?>">
    <ul>
        <li>
            <label>Watermark</label><input type="radio" name="watermark-radio" value="text" checked="checked"><?php
            echo form_input(array(
                'name' => 'watermark',
                'id' => 'charity-watermark-text',
                'class' => 'input-help',
                'placeholder' => 'Watermark text',
                'title' => 'Enter the text you would like to appear on the image watermark'
            ));?>
        </li><li>
            <label></label><input type="radio" name="watermark-radio" value="image"><input type="file" id="charity-watermark-upload">
        </li><li>
            <label></label><input type="radio" name="watermark-radio" value="butler-jcr"><img class="watermark-image" id="charity-watermark-butler-jcr-logo" src="<?php echo VIEW_URL; ?>charities/img/butler-jcr-logo.png" alt="Butler JCR Logo">
        </li><li>
            <label></label><input type="radio" name="watermark-radio" value="grace-house"><img class="watermark-image" id="charity-watermark-grace-house-logo" src="<?php echo VIEW_URL; ?>charities/img/grace-house-logo.png" alt="Grace House Logo">
        </li><li>
        <label>Images</label><input type="file" id="charity-upload" multiple>
        </li><li>
        <label></label><button id="charity-submit">Upload</button>
        </li>
    </ul>
    <?php echo form_close(); ?>

    <div id="charity-upload-errors" class="nolist validation_errors"></div>
    <div id="charity-upload-success" class="nolist validation_success"></div>
</div>