<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('admin');
?>

<h2>Password Has Been Reset</h2>
<p>User: <b><?php echo ($user['prefname']==''?$user['firstname']:$user['prefname']).' '.$user['surname']; ?></b></p>
<p>Username: <b><?php echo $user['username']; ?></b></p>
<p>Durham Email: <b><?php echo $user['email']; ?></b></p>
<?php if($_POST['reset-type'] == 'display'){ ?>
    <p>New Password: <b><?php echo $user['password']; ?></b></p>
<?php }?>