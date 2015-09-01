<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('liversout');?>
<div class="content-left width-33 narrow-full">
<?php $this->load->view('liversout/search_form');?>
<?php $this->load->view('liversout/liversout_contact'); ?>
</div>
<div class="content-right width-66 narrow-full">
<div id="map_container" style="margin:0px auto;width:100%;height:500px;"></div>
<p><?php echo form_open('', 'class="jcr-form"').form_label('Sort By:', 'sort-by').form_dropdown('sort-by', $sorting, isset($_POST['sort-by'])?$_POST['sort-by']:'', 'id="sort-by"'); ?></p>
    <p><?php echo form_label('Bedrooms:', 'bedrooms').form_dropdown('bedrooms', $bedrooms, isset($_POST['bedrooms'])?$_POST['bedrooms']:'', 'id="bedrooms"'); ?></p>
    <p><?php echo form_label().form_submit('submit', 'Filter & Sort').form_close(); ?></p>
<?php if(empty($results)) { ?>
	 <p>No Results Found</p>
<?php } else { 
?>
    <?php foreach($results as $r) { ?>
		<div class="jcr-box">
			<?php echo anchor('liversout/view_property/'.$r['id'], '<h3>'.$r['name'].' '.$r['address1'].', '.(empty($r['address2']) ? '' : $r['address2'].', ').'Durham, '.$r['postcode'].'<span class="star20" style="width:'.round(20*$r['rating']).'px;"></span></h3>');
			echo '<p>'.(empty($r['bedrooms']) ? '' : $r['bedrooms'].' Bedrooms, ').(empty($r['bathrooms']) ? '' : $r['bathrooms'].' Bathrooms, ').(empty($r['area']) ? '' : $r['area'].', ').(empty($r['type']) ? '' : $r['type'].', ').(empty($r['count']) ? 'No' : $r['count']).' Review'.($r['count'] == 1 ? '' : 's').'</p>'; ?>
			<div class="property-data noshow" style="display:none;">
				<?php echo $r['id'].';'.$r['lat'].';'.$r['lng'].';'.$r['name'].' '.$r['address1'].';'.$r['area']; ?>
			</div>
			<a href="<?php echo site_url('liversout/view_property/'.$r['id']); ?>" class="jcr-button inline-block" title="View property details and reviews">
				<span class="ui-icon ui-icon-info inline-block"></span>View
			</a>
			<a href="<?php echo site_url('liversout/add_review/'.$r['id']); ?>" class="jcr-button inline-block" title="Add a review for this property">
				<span class="ui-icon ui-icon-plus inline-block"></span>Review
			</a>
			<a href="<?php echo site_url('contact/user/jcr'); ?>" class="jcr-button inline-block" title="Report an error with the details for thsi property">
				<span class="ui-icon ui-icon-alert inline-block"></span>Report Error
			</a>
		</div>
	<?php } ?>
<?php }?>
</div>