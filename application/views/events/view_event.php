<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('events/index/'.date('Y',$event['time']).'/'.date('m',$event['time']));
$logged_in = logged_in();
eval(error_code());

$this->load->view('events/event_info', array('event' => $event));

if(has_level('any')) { ?>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Sign Up</h2>
		<div class="padding-box">
			<a href="<?php echo site_url('signup/new_signup/'.$event['id'])?>" class="jcr-button inline-block" title="Create a sign up for this event">
				<span class="ui-icon ui-icon-plus inline-block"></span>Add
			</a>
		</div>
	</div>
<?php } else if($signups !== FALSE) { ?>
	<h2 class="bold-header">Sign Up</h2>
<?php }
if($signups !== FALSE) {
	$this->load->view('signup/signup', array('signups' => $signups));
} ?>

<div class="jcr-box wotw-outer">
	<h2 class="wotw-day">Event Poster</h2>
	<div class="event-poster-box">
		<?php
			if(is_null($event['event_poster'])){
		?>
				<p>There is no poster for this event.</p>
		<?php
				if(has_level('any')){
		?>
					<p>To add a poster, click the edit button at the top of this page.</p>
		<?php
				}
			}else{
				$image_path = './application/views/events/posters/';
				$sizes = array('_800px', '_300px', '');//300px for when 800px version has been deleted (after event has happened) '' for backwards compatibility
				foreach($sizes as $s){
					$im_name = str_replace('.', $s.'.', $event['event_poster']);
					if(file_exists($image_path.$im_name)){
						$image_link = VIEW_URL.'events/posters/'.$im_name;
						break;
					}
				}
				
				if(!isset($image_link)){
		?>
					<p>There is no poster for this event.</p>
		<?php
					if(has_level('any')){
		?>
						<p>However there is one in the database, please click the delete button to remove it, then you will be able to add a new one.</p>
						<a href="#" class="jcr-button inline-block delete-poster"><span class="ui-icon ui-icon-trash inline-block"></span>Delete</a><br>
		<?php
					}
				}else{
					if(has_level('any')){ ?>
						<a href="#" class="jcr-button inline-block delete-poster"><span class="ui-icon ui-icon-trash inline-block"></span>Delete</a><br>
				<?php } ?>
					<img class="event-poster" src="<?php echo $image_link; ?>">
		<?php	}		
			} ?>
		<span class="event-id" style="display:none;"><?php echo $event['id']; ?></span>
	</div>
</div>
<?php if(is_admin()) { ?>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Questionnaires</h2>
		<div class="padding-box">
			<a href="<?php echo site_url('questionnaire/add/'.$event['id']); ?>" class="jcr-button inline-block" title="Create a questionnaire for this event">
				<span class="ui-icon ui-icon-plus inline-block"></span>Create
			</a>
		</div>
	</div>
<?php }
?>

<?php
if(!empty($questionnaires)) {?>
	<?php if(!is_admin()) { ?>
		<h2 class="bold-header">Questionnaires</h2>
	<?php } ?>
	<?php foreach($questionnaires as $q) { ?>
		<div class="jcr-box wotw-outer">
			<h3 class="wotw-day"><?php echo $q['name']; ?></h3>
			<?php if(time() < $q['questionnaire_opens']) { ?>
				<p>Questionnaire opens at <?php echo date('H:i', $q['questionnaire_opens']); ?> on <?php echo date('l jS F Y',$q['questionnaire_opens']); ?>.</p>
			<?php } elseif(time() >= $q['questionnaire_opens'] && time() <= $q['questionnaire_closes']) { ?>
				<p>Questionnaire closes at <?php echo date('H:i', $q['questionnaire_closes']); ?> on <?php echo date('l jS F Y',$q['questionnaire_closes']); ?>.</p>
				<?php if($q['user_has_answered']) { ?>
					<p>You've answered this questionnaire</p>
				<?php } else { ?>
					<a href="<?php echo site_url('questionnaire/answer/'.$q['id']); ?>" class="jcr-button inline-block" title="Answer <?php echo $q['name']; ?>">
						<span class="ui-icon ui-icon-clipboard inline-block"></span>Answer
					</a>
				<?php } ?>
			<?php } else { ?>
				<p>Questionnaire Closed</p>
			<?php } ?>
			<?php if(is_admin()) { ?>
				<a href="<?php echo site_url('questionnaire/edit/'.$q['id']); ?>" class="jcr-button inline-block" title="Admin: Edit <?php echo $q['name']; ?>">
					<span class="ui-icon ui-icon-pencil inline-block"></span>Edit
				</a>
				<a href="<?php echo site_url('questionnaire/results/'.$q['id']); ?>" class="jcr-button inline-block" title="Admin: Results of <?php echo $q['name']; ?>">
					<span class="ui-icon ui-icon-document inline-block"></span>Results
				</a>
				<a href="<?php echo site_url('questionnaire/cancel/'.$q['id']); ?>" class="jcr-button inline-block" title="Admin: Cancel <?php echo $q['name']; ?>">
					<span class="ui-icon ui-icon-close inline-block"></span>Cancel
				</a>
			<?php } ?>
		</div>
	<?php } ?>
<?php } ?>

<?php
if(!empty($posts)) { ?>
	<h2 class="bold-header">Event News</h2>
	<?php foreach($posts as $post) {
		$this->load->view('events/post', array('post' => $post, 'show_link' => FALSE));
	}
}
if($logged_in) {?>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Add a news post for this event</h2>
		<div class="wotw-box">
			<?php $this->load->view('events/news_post', array('event_id' => $event['id'])); ?>
		</div>
	</div>
<?php } ?>

<?php if($logged_in) { ?>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Files</h2>
		<a href="<?php echo site_url('events/add_file/'.$event['id']);?>" class="jcr-button inline-block" title="Upload a file for this event">
			<span class="ui-icon ui-icon-arrowthick-1-n inline-block"></span>Upload
		</a>
	</div>
<?php }
if(!empty($files)) { ?>
	<?php if(!$logged_in) { ?>
		<h2 class="bold-header">Files</h2>
	<?php } ?>
	<?php foreach($files as $f) {
		$filetype = end(explode(".", strtolower($f['filename'])));
		if(!in_array($filetype, array('doc', 'docx', 'jpg', 'mp3', 'pdf', 'png', 'ppt', 'pptx', 'txt', 'xls', 'xlsx'), TRUE)) $filetype = 'default'; ?>
		<div class="jcr-box wotw-outer">
			<h3 class="wotw-day"><?php echo (empty($f['description']) ? $f['filename'] : $f['description']); ?></h3>
			<div class="wotw-box">
				<h3 class="wotw-time"><?php echo '<img src="'.VIEW_URL.'common/editable/icons/doc_icons/'.$filetype.'.png" alt="'.$filetype.' file">';?></h3>
				<div class="wotw-info">
					<?php echo '<a target="_blank" class="no-jsify" href="'.VIEW_URL.'events/files/event_'.$event['id'].'/'.$f['filename'].'">Download '.(empty($f['description']) ? $f['filename'] : $f['description']).'</a>'; ?>
					<p><?php echo 'File added by '.user_profile_a_open($f['submitted_by']).$this->users_model->get_full_name($f['submitted_by']).'</a> <span title="'.date('l jS F Y \a\t G:i',$f['submitted_time']).'">'.time_elapsed_string($f['submitted_time']).'</span>.'; ?>
					<?php if(is_admin() or $this->session->userdata('id') == $f['submitted_by']) echo '<a href="'.site_url('events/delete_file/'.$event['id'].'/'.$f['id']).'" class="admin-delete-button no-jsify ui-icon ui-icon-trash inline-block" title="Delete this file"></a>'; ?>
					</p>
				</div>
			</div>
		</div>
	<?php }
} ?>

<?php if($logged_in) { ?>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Photos</h2>
		<a href="<?php echo site_url('events/add_photo/'.$event['id']);?>" class="jcr-button inline-block" title="Upload a photo for this event">
			<span class="ui-icon ui-icon-arrowthick-1-n inline-block"></span>Upload
		</a>
	</div>
<?php }
	if(!empty($photos)) { ?>
	<div class="jcr-box">
	<?php if(!$logged_in) { ?>
		<h2 class="wotw-day">Photos</h2>
	<?php } ?>
		<?php $this->load->view('events/photo_thumbs', array('photos' => $photos)); ?>
	</div>
<?php } ?>

<?php if($logged_in) { ?>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Projects</h2>
		<div class="padding-box">
			<a href="<?php echo site_url('projects/add_request/1/'.$event['id']); ?>" class="jcr-button inline-block" title="Submit a request for this event to a college committee such as Pub Comm or Charities Comm">
				<span class="ui-icon ui-icon-mail-closed inline-block"></span>Submit Request
			</a>
			<?php
			$categories = $this->events_model->get_categories();
			$category_names = array();
			foreach($categories as $c) {
				$category_names[$c['id']] = $c['name'];
			}
			if(!empty($requests)) {
				foreach($requests as $request) {
					$this->load->view('projects/view_request', array(
						'request' => $request,
						'category_names' => $category_names
					));
				}
			} ?>
		</div>
	</div>
<?php } ?>
