<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
echo back_link('welfare');
?>

<p>Thanks for requesting the supplies.  We will get on it as soon as possible.  Your anonymous code is shown below, one last time.  You will not be able to retrieve the code again, so write it down!</p>
<p></p>
<p>If you have left your room number your supplies will be delivered to your postbox.</p>

<h3>You requested:</h3>
<ul style="padding-left:20px;">
    <?php foreach($supplies as $s) : ?>
        <li><?php echo $s['name']; ?></li>
    <?php endforeach; ?>
</ul>

<h3>Anonymous code:</h3>
<div style="font-size: 15pt;border: 1px solid black;display: inline;"><?php echo $code; ?></div>

<?php if(isset($_POST['urgent'])){ ?>
<p>This request has been marked as urgent.</p>
<?php } ?>