<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
	$url = VIEW_URL.'photos/images/';
	echo back_link('photos');
	$this->load->view('photos/validation');
?>
<h2>Your Shopping Basket</h2>
<?php echo editable_area('photos', 'content/checkout', is_admin()); ?>
<p><i>To remove an item, set its quantity to 0, then click update.</i></p>
<table class="basket-table large-photo">
	<tr><th colspan="2">Photo</th><th>Price</th><th>Qty</th><th>Total</th></tr>
	<?php 
		$contents = $this->cart->contents(); 
		$price = 0;
		foreach($contents as $photo){
			$price += $photo['subtotal'];
			echo '<tr>';
			echo '<td><a href="'.site_url('photos/photo/'.$photo['options']['photo_id']).'"><div style="background-image:url('.$url.$photo['options']['thumb-name'].')" class="basket-thumb"></div></a></td>';
			echo '<td><a href="'.site_url('photos/photo/'.$photo['options']['photo_id']).'">'.$photo['name'].'</a></td>';
			echo '<td>£'.number_format($photo['price'],2).'</td>';
			echo '<td><span class="row-id">'.$photo['rowid'].'</span>'.form_input(array('value'=>$photo['qty'], 'placeholder'=>'Qty', 'class'=>'basket-qty')).'</td>';
			echo '<td>£'.number_format($photo['subtotal'],2).'</td>';
			echo '</tr>';
		}	
	?>
	<td></td><td></td><td></td><td>Total:</td><td><b>£<?php echo number_format($price,2); ?></b></td>
</table>
<a href="#" class="jcr-button update-cart no-jsify">Update Shopping Basket</a>
<a href="<?php echo site_url('photos/order'); ?>" class="jcr-button">Place Order</a>