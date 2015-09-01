<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
	$url = VIEW_URL.'photos/images/';
?>
<h2 class="shopping-head"><span class="ui-icon inline-block ui-icon-cart gold-icon large-icon"></span>Shopping Basket (<span class="basket-num"><?php echo $this->cart->total_items(); ?></span>)</h2>
<div class="basket-contents">
	<table class="basket-table">
		<tr><th colspan="2">Photo</th><th>Price</th><th>Qty</th><th>Total</th></tr>
		<?php 
			$contents = $this->cart->contents(); 
			$price = 0;
			foreach($contents as $photo){
				$price += $photo['subtotal'];
				echo '<tr>';
				echo '<td><a href="'.site_url('photos/photo/'.$photo['options']['photo_id']).'"><div style="background-image:url('.$url.$photo['options']['thumb-name'].')" class="basket-thumb"/></a></td>';
				echo '<td><a href="'.site_url('photos/photo/'.$photo['options']['photo_id']).'">'.$photo['name'].' - '.$photo['options']['type'].'</a></td>';
				echo '<td>£'.number_format($photo['price'],2).'</td>';
				echo '<td><span class="row-id">'.$photo['rowid'].'</span>'.form_input(array('value'=>$photo['qty'], 'placeholder'=>'Qty', 'class'=>'basket-qty')).'</td>';
				echo '<td>£'.number_format($photo['subtotal'],2).'</td>';
				echo '</tr>';
			}	
		?>
		<td></td><td></td><td></td><td>Total:</td><td><b>£<?php echo number_format($price,2); ?></b></td>
	</table>
	<a href="<?php echo site_url('photos/checkout'); ?>" class="jcr-button">Checkout</a>
	<a href="#" class="jcr-button update-cart no-jsify">Update Shopping Basket</a>
	<div class="spacer" style="clear: both;"></div>
</div>
