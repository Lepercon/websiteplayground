<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
	$url = VIEW_URL.'photos/images/';
	echo back_link('photos');
	$this->load->view('photos/validation');
?>
<h2>My Orders</h2>
<?php 
	echo editable_area('photos', 'content/orders', is_admin());
	if(empty($orders)){
		echo '<h3>You have not placed any orders yet...</h3>';
	}else{
		echo '<div id="accordion">';
		foreach($orders as $o){
			echo '<h3>Order : '.date('l jS M H:i', $o[0]['time']).' - '.$statuses[$o[0]['status']].'</h3>';
			echo '<div>';
			echo '<table class="orders-table">';
			echo '<tr><th colspan="2">Photo</th><th>Type</th><th>Price</th><th>Qty</th><th>Total</th></tr>';
			$total = 0;
			foreach($o as $p){
				echo '<tr>';
				echo '<td><a href="'.site_url('photos/photo/'.$p['photo_id']).'"><div style="background-image:url('.$url.$p['thumb_name'].')" class="order-thumb"></div></a></td>';
				echo '<td><a href="'.site_url('photos/photo/'.$p['photo_id']).'">Photo '.$p['photo_id'].'</a></td>';
				echo '<td>'.$p['format'].'</td>';
				echo '<td>£'.number_format($p['price'],2).'</td>';
				echo '<td>'.$p['qty'].'</td>';
				echo '<td>£'.number_format($p['price']*$p['qty'],2).'</td>';
				echo '</tr>';
				$total += ($p['price']*$p['qty']);
			}
			echo '<tr><th></th><th></th><th></th><th></th><td class="normal-height">Total:</td><th>£'.number_format($total,2).'</th></tr>';
			echo '</table>Order Status: <b>'.$statuses[$o[0]['status']].'</b></div>';
		}
		echo '</div>';
	}
?>
