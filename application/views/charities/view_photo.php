<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!empty($photo)) {	?>

	<div class="content-left width-33 narrow-full">
		<?php $this->load->view('charities/basket'); ?>
	</div>

	<div class="content-right width-66 narrow-full">
		<div class="jcr-box wotw-outer">
			<h2 class="wotw-day">Photo <?php echo $photo['id']; ?></h2>
			<div class="padding-box">
				<div id="charity-photo-controls">
					<a href="<?php echo site_url('charities/view_photo/'.$prev); ?>" class="jcr-button inline-block" title="Previous image in album">
						<span class="ui-icon ui-icon-arrowthick-1-w inline-block"></span>
					</a>
					<a href="<?php echo site_url('charities/view_album/'.$photo['album_id']); ?>" class="jcr-button inline-block" title="Go back to the album">
						<span class="ui-icon ui-icon-arrowreturnthick-1-w inline-block"></span>
					</a>
					<a href="<?php echo site_url('charities/view_photo/'.$next); ?>" class="jcr-button inline-block" title="Next image in album">
						<span class="ui-icon ui-icon-arrowthick-1-e inline-block"></span>
					</a>
				</div>
				<img class="charity-photo-large" width="500px" alt="Photo <?php echo $photo['id'] ?>" title="Photo <?php echo $photo['id'] ?>" src="<?php echo VIEW_URL.'charities/photos/album_'.$photo['album_id'].'/'.$photo['filename'].'.png'; ?>" >
			</div>
		</div>
		<div class="jcr-box wotw-outer">
			<h2 class="wotw-day">Order Photo <?php echo $photo['id']; ?></h2>
			<?php echo form_open('charities/add_photo/'.$photo['id'], array('class' => 'jcr-form inline-block')); ?>
				<ul class="nolist inline-block">
					<li><?php
						echo form_label('Quantity');
						echo form_input(array(
							'name' => 'quantity',
							'value' => '1',
							'maxlength' => '2',
							'required' => 'required',
							'class' => 'input-help',
							'placeholder' => 'Qty',
							'title' => 'Enter the number of this photo you would like to order.',
							'style' => 'width:40px;'
						));
					?></li><?php foreach($sizes as $s) { ?>
						<li>
							<?php $id = $photo['id'].str_replace(' ', '', strtolower($s['description']));
							echo form_label($s['description'].' - &pound;'.$s['price'], $id);
							echo form_checkbox(array(
								'name' => 'format[]',
								'value' => $s['id'],
								'checked' => FALSE,
								'id' => $id
							)); ?>
						</li><?php } ?><li><?php
						echo form_label();
						echo form_submit('Submit','Add to Basket');
					?></li>
				</ul>
			<?php echo form_close(); ?>
		</div>
	</div>
<?php } ?>