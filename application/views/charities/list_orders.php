<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('charities/orders'); ?>

<h2>Orders List</h2>

<?php if(!empty($orders)) {
$order_id = 0;
$order_total = 0; ?>
	<div id="accordion">
		<?php foreach($orders as $o) {
			if($o['order_id'] !== $order_id) {
				if($order_id !== 0) {
					echo '<p>Total: &pound;'.number_format($order_total, 2, '.', '').'</p>';
					$order_total = 0; ?>
					</div>
				<?php }
				$order_id = $o['order_id'];
				$user = $this->users_model->get_users($o['order_by'], 'email, firstname, prefname, surname'); ?>
				<h3><?php echo user_pref_name($user['firstname'], $user['prefname'], $user['surname']); ?> - <?php echo $order_id; ?> - <span class="charity-order-status"><?php echo ucfirst($o['status']); ?></span></h3>
				<div>
					<p>Order <span class="charity-order-id"><?php echo $order_id; ?></span> submitted at <?php echo date('H:i \o\n l jS F Y',$o['order_time']); ?> by <?php echo user_pref_name($user['firstname'], $user['prefname'], $user['surname']); ?> (<a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a>)</p>
					<button class="jcr-button inline-block charity-update-printed" title="Change order status to printed">
						<span class="ui-icon ui-icon-print inline-block"></span>
					</button>
					<button class="jcr-button inline-block charity-update-paid" title="Change order status to paid">
						<span class="ui-icon ui-icon-check inline-block"></span>
					</button>
					<button class="jcr-button inline-block charity-update-delete" title="Delete order">
						<span class="ui-icon ui-icon-trash inline-block"></span>
					</button>
			<?php }
			echo '<p>'.$o['item_qty'].' '.$o['item_format'].' of '.anchor('charities/view_photo/'.$o['item_id'], 'Photo '.$o['item_id']).' at &pound;'.$o['item_price'].' each.</p>';
			$order_total += $o['item_qty'] * $o['item_price'];
		} ?>
		<?php echo '<p>Total: &pound;'.number_format($order_total, 2, '.', '').'</p>';
		$order_total = 0; ?>
	</div>
<?php } else { ?>
	<p>There are no orders to show</p>
<?php } ?>