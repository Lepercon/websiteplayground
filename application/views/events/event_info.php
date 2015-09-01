<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$ci = get_instance();
$ci->load->model('alumni_model');
if(is_null($event['event_poster'])){
	$poster = '';
}else{
	$poster = explode('.',$event['event_poster']);
	$poster[0] .= '_800px';
	$poster = VIEW_URL.'events/posters/'.implode('.', $poster);
}
?>

<div class="jcr-box square-box">
	<div class="">
		<a href="<?php echo site_url('events/view_event/'.$event['id']); ?>" class="wotw-poster-link">
			<?php if($poster != ''){ ?>
				<img class="wotw-poster" src="<?php echo $poster; ?>">
			<?php }else{ ?>
				<div class="wotw-poster"></div>
			<?php } ?>
		</a>
		<div class="wotw-name wotw-remain">
			<h2><?php echo $event['name']; ?></h2>
			<p>
				<?php echo date('l jS F Y',$event['time']).(date('H:i', $event['time']) !== '00:00' ? ' from '.date('G:i', $event['time']) : '').(empty($event['location']) ? '' : ' in '.$event['location']); ?> 
				<a href="<?php echo site_url('events/view_event/'.$event['id']); ?>" class="ui-icon ui-icon-info inline-block" title="View this news item in context"></a>
			</p>
			<?php if(!empty($event['description'])) {
				$ci->load->helper('smiley');
				echo '<p>'.parse_smileys(nl2br($event['description']), VIEW_URL.'common/smileys/').'</p>';
			}?>
			<?php if(uri_string() !== 'events/view_event/'.$event['id']) echo '<a class="sprite-container" title="View related event information" href="'.site_url('events/view_event/'.$event['id']).'"><div class="common-sprite" id="sprite-butler"></div></a>';?>
			<?php if(!empty($event['twitter_handle'])) echo '<a class="sprite-container" href="http://twitter.com/'.$event['twitter_handle'].'" title="View this event on Twitter" target="_blank"><div class="common-sprite" id="sprite-twitter"></div></a>'; ?>
			<?php if(!empty($event['facebook_url'])) echo '<a class="sprite-container" href="'.$event['facebook_url'].'" title="View this event on Facebook" target="_blank"><div class="common-sprite" id="sprite-facebook"></div></a>'; ?>
			<?php if(has_level('any')) { ?>
				<a href="<?php echo site_url('events/edit_event/'.$event['id']); ?>" class="jcr-button inline-block" title="Edit this event">
					<span class="ui-icon ui-icon-pencil inline-block"></span>Edit
				</a>
			<?php } ?>
			<?php if(is_admin()) { ?>
				<a href="<?php echo site_url('events/cancel_event/'.$event['id']); ?>" class="jcr-button admin-delete-button no-jsify inline-block" title="Delete this event">
					<span class="ui-icon ui-icon-trash inline-block"></span>Delete
				</a>
			<?php } ?>
			<?php if($ci->alumni_model->has_open_signup($event['id'])) { ?>
				<a href="<?php echo site_url('alumni/sign_up/'.$event['id']); ?>" class="jcr-button no-jsify inline-block" title="Signup Is Currently Open For This Event">
					<span class="ui-icon ui-icon-pencil inline-block"></span>Sign Up
				</a>
			<?php } ?>				
			<?php if(isset($extra_text)) { 
				echo $extra_text;
			} ?>
		</div>
	</div>
</div>
