<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$userprefname = user_pref_name($user['firstname'], $user['prefname']); ?>

<div class="wotw-outer jcr-box">
	<h2 class="wotw-day">Who is <?php echo $userprefname.' '.$user['surname']; ?>?</h2>
	<div class="padding-box">
		<img id="usr-profile-img" class="inline-block" src="<?php echo get_usr_img_src($user['uid'], 'large'); ?>" alt="<?php echo $user['firstname'].' '.$user['surname']; ?>'s JCR Profile Picture" />
		<p class="inline-block"><?php echo email_link($user['id'], 'Contact via JCR website').' or <a href="mailto:'.$user['email'].'">Email</a>'; ?>
			<?php if(!empty($user['levels'])) echo '<br/>'.$user['levels'].''; ?>
		</p>
	</div>
</div>
<?php if(!empty($user['level_desc'])) {?>
<div class="wotw-outer jcr-box">
	<h2 class="wotw-day"><?php echo $userprefname; ?>'s role at Butler</h2>
	<div class="padding-box">
		<p><?php echo $user['level_desc']; ?></p>
	</div>
</div>
<?php } ?>
<?php if($user['registeredon'] > 0) { ?>
<div class="wotw-outer jcr-box">
	<h2 class="wotw-day"><?php echo $userprefname; ?>'s Butler stats</h2>
	<div class="padding-box">
		<p>First used the JCR website in <?php echo date('Y', $user['registeredon']); ?></p>
		<p><?php echo $user['visitcount']; ?> website visits</p>
	</div>
</div>
<?php } ?>