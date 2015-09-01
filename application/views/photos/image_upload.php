<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('photos/album/'.$album['id']);?>
<h3>Image Upload - <?php echo $album['name']; ?></h3>
<p><?php echo $album['description']; ?></p>
<?php 
echo form_open('', 'id="file-upload" class="jcr-form no-jsify"');
echo form_upload('file', '', 'multiple id="file-select"');
echo '<p>'.form_checkbox('watermark', 'Watermark', FALSE).'Watermark Photos</p>';
echo '<div class="watermark-options">';
echo '<p>'.form_radio('watermark-options', 'name-logo', TRUE).'Album Name</p>';
echo '<p>'.form_radio('watermark-options', 'gracehouse', FALSE).'Gracehouse Logo</p>';
echo '<p>'.form_radio('watermark-options', 'custom-watermark-option', FALSE).'Custom Text:'.form_input(array('placeholder'=>'Custom Text', 'id'=>'custom-text')).'</p>';
echo '</div>';
echo form_submit('s', 'Submit');
echo form_close();
?>
<a id="album-link" href="<?php echo site_url('photos/unpublished/'.$album['id']); ?>"></a>

<div id="notifier"><div id="spinning"></div><div id="notifier-text"></div></div>
<div id="image-previews"></div><div id="album-id"><?php echo $album['id']; ?></div>