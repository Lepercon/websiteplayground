<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$status = array(
	'0' => 'Academic, Teaching or Management Staff',
	'1' => 'Research Staff',
	'2' => 'Taught Postgraduate',
	'3' => 'Research Postgraduate',
	'4' => 'Administrative Staff',
	'5' => 'Technical Staff',
	'6' => 'Visitor',
	'7' => 'Contract Staff',
	'8' => 'Temporary Staff',
	'9' => 'Manual and Domestic Staff',
	'U' => 'Undergraduate',
	'R' => 'Distance Learning Students',
	'D' => 'Retired Staff',
	'E' => 'Emeritus Staff',
);
?>

<table>
	<tr>
		<th>Photo</th>
		<th>Name</th>
		<th>Email</th>
		<th>Year Group</th>
		<th>Status</th>
		<?php if(is_admin()) echo '<th>Site ID</th>'?>
	</tr>

	<?php foreach($users as $user): ?>
	<tr>
		<td>
			<?php if(file_exists(VIEW_PATH.'details/img/users/'.$user['uid'].'_tiny.jpg')):
			echo user_profile_a_open($user['id']); ?>
				<img src="<?php echo VIEW_URL.'details/img/users/'.$user['uid'].'_tiny.jpg'; ?>" />
			<?php echo '</a>'; ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo user_profile_a_open($user['id']); ?>
				<?php echo $user['firstname'].($user['prefname']==''?'':' ('.$user['prefname'].')').' '.$user['surname']; ?>
			<?php echo '</a>'; ?>
		</td>
		<td>
			<?php echo email_link($user['id'], 'Contact').', <a href="mailto:'.$user['email'].'">Email</a>'; ?>
		</td>
		<td>
			<?php echo $user['year_group']; ?>
		</td>
		<td>
			<?php echo is_null($user['status'])?'Graduate':$status[$user['status']]; ?>
		</td>
		<?php if(is_admin()) echo '<td>'.$user['id'].'</td>'; ?>
	</tr>
	<?php endforeach; ?>
</table>