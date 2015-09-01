<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="jcr-box wotw-outer">
	<h2 class="wotw-day">Shopping Basket</h2>
	<div class="padding-box">
		<?php if($this->cart->total_items() > 0) {
			echo form_open('charities/submit_form/'.(isset($album['id']) ? $album['id'] : 'orders'), array('class' => 'jcr-form')); ?>
			<table>
				<?php foreach ($this->cart->contents() as $items) { ?>
					<tr>
						<?php foreach ($this->cart->product_options($items['rowid']) as $v) {
							$photo_db = $this->charities_model->get_photo($items['name']);?>
							<td class="charity-item-description">
								<?php $description = '';
								foreach($sizes as $s) {
									if($s['id'] == $v) {
										$description = $s['description'];
									}
								} ?>
								<p>Photo <?php echo $items['name']; ?> - <?php echo $description; ?></p>
								<a href="<?php echo site_url('charities/view_photo/'.$items['name']); ?>" class="inline-block">
									<img width="60px" alt="Photo <?php echo $items['name']; ?>" title="Photo <?php echo $items['name']; ?>" src=" <?php echo VIEW_URL.'charities/photos/album_'.$photo_db['album_id'].'/'.$photo_db['filename'].'.png'; ?> ">
								</a>
								<a href="<?php echo site_url('charities/view_photo/'.$items['name']); ?>" class="jcr-button inline-block" title="Buying options for this photo">
									<span class="ui-icon ui-icon-cart inline-block"></span>
								</a>
								<a href="<?php echo site_url('charities/remove_from_basket/'.$items['rowid']); ?>" class="jcr-button charity-delete-basket no-jsify inline-block" title="Delete this photo from the basket">
									<span class="ui-icon ui-icon-closethick inline-block"></span>
								</a>
							</td>
							<td class="charity-item-qty">
								<?php echo form_input(array(
									'name' => 'product_'.$items['rowid'],
									'value' => $items['qty'],
									'maxlength' => '2',
									'required' => 'required',
									'class' => 'input-help',
									'placeholder' => 'Qty',
									'title' => 'Enter the number of this photo you would like to order.',
									'style' => 'width:30px;'
								)); ?>
							</td>
							<td class="charity-item-price">
								&pound;<?php echo number_format($items['price'] * $items['qty'],2); ?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
			</table>
			<p id="charity-total-price"></p>
			<div id="charity-basket-options">
			<?php
			echo form_submit('update','Update');
			echo form_submit('checkout','Checkout');
			echo form_submit('clear','Clear Basket'); ?>
			</div>
			<?php echo form_close();
		} else { ?>
			<p>Your shopping basket is empty</p>
		<?php } ?>
	</div>
</div>