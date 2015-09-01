<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('liversout/search/area/all');
eval(error_code());?>

<div class="content-left width-33 narrow-full">
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Property Details</h2>
		<div>
			<?php echo '<h2 id="geoAddress">'.$p['name'].' '.$p['address1'],', '.(empty($p['address2']) ? '' : $p['address2'].', ').'Durham, '.$p['postcode'].'</h2>';?>
			<ul class="nolist">
				<li>Type: <b><?php echo $p['type'];?></b></li>
				<li>Area: <b><?php echo $p['area'];?></b></li>
				<li>Bedrooms: <b><?php echo $p['bedrooms'];?></b></li>
				<li>Bathrooms: <b><?php echo $p['bathrooms'];?></b></li>
	            <h2>Walking Times:</h2>
	            <li>To College: <b><?php echo $p['time_college'];?> minute<?php echo $p['time_college']==1?'':'s'; ?></b></li>
	            <li>To Town: <b><?php echo $p['time_town'];?> minute<?php echo $p['time_town']==1?'':'s'; ?></b></li>
	            <li>To Science Site: <b><?php echo $p['time_science'];?> minute<?php echo $p['time_science']==1?'':'s'; ?></b></li>
	            <li>To Elvet: <b><?php echo $p['time_elvet'];?> minute<?php echo $p['time_elvet']==1?'':'s'; ?></b></li>
			</ul>
	        <br>
			<a href="<?php echo site_url('liversout/add_review/'.$p['id']); ?>" class="jcr-button inline-block" title="Add a review for this property">
				<span class="ui-icon ui-icon-plus inline-block"></span>Add Review
			</a>
			<a href="<?php echo site_url('contact/user/jcr'); ?>" class="jcr-button inline-block" title="Report an error with the details for this property">
				<span class="ui-icon ui-icon-alert inline-block"></span>Report Error
			</a>
		</div>
	</div>
	<div class="jcr-box wotw-outer">
		<?php echo form_open_multipart('liversout/view_property/'.$p['id'], array('class' => 'no-jsify jcr-form')); ?>
			<h2 class="wotw-day">Add a Photo (jpg)</h2>
			<div>
				<ul class="nolist">
					<li><input name="userfile" type="file" /></li>
					<li>Photo Description</li>
					<li><input id="photo-description-label" type="text" placeholder="Photo Description" name="caption" maxlength="100" value="<?php echo set_value('caption'); ?>"></li>
					<li><input type="submit" value="Add Photo" /></li>
					<?php echo token_ip('add_photo'); ?>
				</ul>
			</div>
		<?php echo form_close(); ?>
	</div>
	<div class="property-data noshow" style="display:none;"><?php echo $p['id'].';'.$p['lat'].';'.$p['lng'].';'.$p['name'].' '.$p['address1'].';'.$p['area'];?></div>
	<?php if(!empty($photos)) { ?>
		<div class="jcr-box wotw-outer">
			<h2 class="wotw-day">Photos</h2>
			<div>
				<?php foreach($photos as $q) {
					echo '<a class="photo-thumb photo-view inline-block no-jsify" rel="photos-'.$p['id'].'" title="'.$q['caption'].'" href="'.VIEW_URL.'liversout/img/property/'.$q['img'].'.jpg"><img class="inline-block" src="'.VIEW_URL.'liversout/img/property/'.$q['img'].'_small.jpg" alt="'.$q['caption'].'" /></a>';
				} ?>
			</div>
		</div>
	<?php } ?>
</div>
<div class="content-right width-66 narrow-full">
	<div id="map_container" style="margin:5px;width:100%;height:500px;"></div>
	<?php if(!empty($review)) {
		foreach($review as $i => $r) { ?>
			<div class="jcr-box">
				<h2>Review <?php echo ($i+1).': <span class="star20" style="width:'.(4*($r['property_rating']+$r['living_area_rating']+$r['bedrooms_rating']+$r['bathrooms_rating']+$r['landlord_rating'])).'px;"></span>';?>
					</h2><h3>Key Facts</h3>
						<ul class="nolist">
							<li><label>Recommended:</label><?php echo ($r['recommend'] ? 'Yes' : 'No');?></li>
							<li><label>Rent:</label>&pound;<?php echo $r['rent_cost'];?> per week</li>
							<li><label>Bills:</label><?php echo ($r['bills_included'] ? 'Included' : '&pound;'.$r['bills_cost'].' per week');?></li>
						</ul>
					<h3>Ratings</h3>
						<ul class="nolist">
						<?php foreach (array('property','living_area','bedrooms','bathrooms','landlord') as $a) echo '<li><label>'.ucwords(str_replace("_"," ",$a)).':</label><span class="star16" style="width:'.(16*$r[$a.'_rating']).'px;"></span></li>';?>
						</ul>
					<?php echo '<h3>Landlord: '.$r['landlord'].'</h3>'.$r['landlord_responsive'];
					foreach(array('neighbours' => 'Problems with Neighbours','problems' => 'Problems with the Property', 'comments' => 'Other Comments') as $a => $b) if(!empty($r[$a])) echo '<h3>'.$b.'</h3>'.$r[$a];?>
				<?php if($r['allow_contact']) echo '<br/><a href="mailto:'.$r['email'].'" class="no-jsify">Contact this Reviewer</a>'; ?>
			</div>
		<?php }
	}
	else { ?>
		<a href="<?php echo site_url('liversout/add_review/'.$p['id']); ?>" class="jcr-button inline-block" title="Add a review for this property">
			<span class="ui-icon ui-icon-plus inline-block"></span>Add Review
		</a>
	<?php } ?>
</div>
