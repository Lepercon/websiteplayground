<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('liversout/view_property/'.$p['id']);
eval(error_code());?>
<h3>Add a Review for <?php echo $p['name'].' '.$p['address1'].(empty($p['address2']) ? '' : ', ').$p['address2'].', Durham, '.$p['postcode'];?></h3>
<p>If you think there are any errors in the existing details for this property, please <?php echo contact_wm();?></p>
<?php echo form_open('liversout/add_review/'.$p['id'], array('class' => 'jcr-form'));
	if(empty($p['type']) || empty($p['area']) || empty($p['bedrooms']) || empty($p['bathrooms'])) {?>
		<div class="jcr-box">
			<h3>Please Add this Missing Information First</h3>
			<ul class="nolist">
				<?php if(empty($p['type'])) {?>
					<li>
						<label>Property Type *</label><select name="type">
						<?php foreach(array('','Bungalow','Flat','Detached','Semi-Detached','Terrace') as $v) {
							echo '<option value="'.$v.'" '.set_select('type',$v).'>'.$v.'</option>';
						};?>
						</select>
					</li>
				<?php }
				else echo '<input type="hidden" name="type" value="'.$p['type'].'">';?>
				<?php if(empty($p['area']) OR $p['area'] == 'Other') {?>
					<li>
						<label>Area *</label><select name="area" id="area-label">
							<?php foreach(array('','Claypath','Elvet','Gilesgate','Nevilles Cross','Viaduct','Other') as $v) {
								echo '<option value="'.$v.'" '.set_select('area',$v).'>'.$v.'</option>';
							}?>
						</select>
						 If Other:
						<input type="text" name="other_area" id="other-label" maxlength="30" value="<?php echo set_value('other_area'); ?>">
					</li>
				<?php }
				else echo '<input type="hidden" name="area" value="'.$p['area'].'"><input type="hidden" name="other_area" value="'.$p['area'].'">';?>
				<?php if(empty($p['bedrooms'])) {?>
					<li>
						<label>Number of Bedrooms *</label><select name="bedrooms"><option></option>
						<?php for($i=1; $i <= 12; $i++) echo '<option value="'.$i.'" '.set_select('bedrooms',$i).'>'.$i.'</option>';?>
						</select>
					</li>
				<?php }
				else echo '<input type="hidden" name="bedrooms" value="'.$p['bedrooms'].'">';?>
				<?php if(empty($p['bathrooms'])) {?>
					<li>
						<label>Number of Bathrooms *</label><select name="bathrooms"><option></option>
						<?php for($i=1; $i <= 12; $i++) echo '<option value="'.$i.'" '.set_select('bathrooms',$i).'>'.$i.'</option>';?>
						</select>
					</li>
				<?php }
				else echo '<input type="hidden" name="bathrooms" value="'.$p['bathrooms'].'">';?>
			</ul>
		</div>
	<?php }
	else echo '<input type="hidden" name="type" value="'.$p['type'].'"><input type="hidden" name="area" value="'.$p['area'].'"><input type="hidden" name="other_area" value="'.$p['area'].'"><input type="hidden" name="bedrooms" value="'.$p['bedrooms'].'"><input type="hidden" name="bathrooms" value="'.$p['bathrooms'].'">'; ?>
	<div class="jcr-box">
		<h3>Give your Review</h3>
		<ul class="nolist">
			<li>
				<label for="rent-cost-label">Weekly rent *</label>&pound;<input type="text" name="rent_cost" id="rent-cost-label" maxlength="10" value="<?php echo set_value('rent_cost'); ?>">
			</li>
			<li>
				<label>Weekly bills *</label>Included?<select id="bills" name="bills_included">
					<option></option>
					<option value="1" <?php echo set_select('bills_included','1');?>>Yes</option>
					<option value="0" <?php echo set_select('bills_included','0');?>>No</option>
				</select>
			</li>
			<li>
				<label></label>If no, how much? &pound;<input type="text" name="bills_cost" id="bills-cost-label" maxlength="10" value="<?php echo set_value('bills_cost'); ?>">
			</li>
			<li>
				<label></label>For the following aspects, 5 is excellent &amp; 1 is very poor.
			</li>
			<?php foreach(array('property','living_area','bedrooms','bathrooms','landlord') as $a) {
				echo '<li><label>How would you rate the '.str_replace('_',' ',$a).'? *</label><select name="'.$a.'_rating"><option></option>';
				for($i=1; $i <= 5; $i++) echo '<option value="'.$i.'" '.set_select($a.'_rating',$i).'>'.$i.'</option>';
				echo '</select></li>';
			}?>
			<li>
				<label for="landlord-label">Who is your Landlord? *</label><input type="text" name="landlord" id="landlord-label" maxlength="50" value="<?php echo set_value('landlord'); ?>">
			</li>
			<li>
				<label for="landlord-responsive-label">Is your landlord responsive to requests/questions?</label><textarea name="landlord_responsive" id="landlord-responsive-label" rows="4"><?php echo set_value('landlord_responsive'); ?></textarea>
			</li>
			<li>
				<label for="neighbours-label">What (if any) problems have you had with neighbours?</label><textarea name="neighbours" id="neighbours-label" rows="4"><?php echo set_value('neighbours'); ?></textarea>
			</li>
			<li>
				<label for="problems-label">What (if any) problems have you had with the property?</label><textarea name="problems" id="problems-label" rows="4"><?php echo set_value('problems'); ?></textarea>
			</li>
			<li>
				<label>Would you recommend the property to a friend? *</label><select name="recommend">
					<option></option>
					<option value="1" <?php echo set_select('recommend','1');?>>Yes</option>
					<option value="0" <?php echo set_select('recommend','0');?>>No</option>
				</select>
			</li>
			<li>
				<label for="comments-label">Is there anything else you would like to say about the property?</label><textarea name="comments" id="comments-label" rows="4"><?php echo set_value('comments'); ?></textarea>
			</li>
			<li>
				<label>Are you happy to be contacted by Butler students interested in the property? *</label><select name="allow_contact">
					<option></option>
					<option value="1" <?php echo set_select('allow_contact','1');?>>Yes</option>
					<option value="0" <?php echo set_select('allow_contact','0');?>>No</option>
				</select>
			</li>
			<li>
				<label></label><input type="submit" value="Submit Review" />
			</li>
			<?php echo token_ip('add_review'); ?>
		</ul>
	</div>
<?php echo form_close(); ?>
