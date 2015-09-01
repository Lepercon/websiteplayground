<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php

	echo back_link('finance');

?>
<div class="wotw-outer jcr-box">
	<h2 class="wotw-day">Notifications</h2>
	<div>
		<?php
			if(!empty($notifications)){
		?>
				<p class="delete-read">With Selected: 
					<a class="inline-block delete-selected" href="#"><span class="ui-icon ui-icon-trash"></span>Delete</a> 
					<a class="inline-block read-selected" href="#"><span class="ui-icon ui-icon-mail-open"></span>Mark Read</a> 
					<a class="inline-block unread-selected" href="#"><span class="ui-icon ui-icon-mail-closed"></span>Mark Unread</a>
				</p>
				<table class="notifications-table">
					<?php
						foreach($notifications as $n){
					?>
							<tr class="n-r <?php echo $n['is_read']?'notification-row-read':'notification-row-unread'; echo ($n['user_id']==-1)?' admin-row':''; ?>">
								<td class="n-checkbox">
									<?php echo form_checkbox(array('class'=>'n-check')); ?>
									<span class="n-id" style="display:none;"><?php echo $n['id']; ?></span>
								</td>
								<td class="n-cell n-date"><?php echo date('H:i d/m/Y',$n['time']); ?></td>
								<td class="n-cell n-category"><?php echo $n['category']; ?></td>
								<td class="n-cell"><?php echo $n['message']; ?></td>
								<td class="hidden-info">
									<span class="is-read"><?php echo $n['is_read']; ?></span>
									<?php
										if(!is_null($n['link'])){
									?>
											<a class="click-link" href="<?php echo site_url($n['link']); ?>"></a>
									<?php
										}
									?>
								</td>
							</tr>
					<?php	
						}	
					?>
				</table>
				<p class="select-all"><?php echo form_checkbox(array('class'=>'all-check')); ?> Select All</p>
				<p class="delete-read">With Selected: 
					<a class="inline-block delete-selected" href="#"><span class="ui-icon ui-icon-trash"></span>Delete</a> 
					<a class="inline-block read-selected" href="#"><span class="ui-icon ui-icon-mail-open"></span>Mark Read</a> 
					<a class="inline-block unread-selected" href="#"><span class="ui-icon ui-icon-mail-closed"></span>Mark Unread</a>
				</p>
		<?php
			}else{
		?>
				<p>There are no notifications to view.</p>
		<?php
			}
		?>
	</div>
</div>