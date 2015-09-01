<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$token = token_ip('page_edit_rights');
echo back_link('admin');
eval(success_code());

foreach($pages as $page) {
	if($page['editable'] == TRUE) {?>
		<div class="jcr-box wotw-outer">
			<h2 class="wotw-day">Manage user levels allowed to edit "<?php echo $page['title']; ?>"</h2>
			<div>
				<?php echo form_open('admin/page_edit_rights/'.$page['id']); ?>
					<div class="editable-pagerights-selects">
						<?php foreach(array('allowed', 'notallowed') as $k => $type) : ?>
							<div class="editable-pagerights-div">
								<b><?php echo ($k ? 'Not Allowed to edit' : 'Allowed to edit'); ?></b>
								<br />
								<select name="<?php echo $type; ?>[]" multiple="multiple" class="editable-pagerights-select editable-pagerights-<?php echo $type; ?>">
									<?php foreach($page_rights[$page['id']][$type] as $id => $name) : ?>
										<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<?php if(!$k) : ?>
								<div class="editable-pagerights-div">
									<input type="button" class="editable-move-right" value="&gt;&gt;&gt;" /><br />
									<input type="button" class="editable-move-left" value="&lt;&lt;&lt;" />
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php echo form_submit(array(
						'value' => 'Save',
						'class' => 'editable-pagerights-save'
					));
					echo $token;
				echo form_close(); ?>
			</div>
		</div>
	<?php }
} ?>
