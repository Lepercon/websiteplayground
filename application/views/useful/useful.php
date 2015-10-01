<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="inline-block width-33 narrow-full">
<h1>Pages</h1>
<?php foreach($pages as $k=>$p){ ?>
    <a href="<?php echo site_url('useful/index/'.$k); ?>"><p><?php echo ($page==$k?'>>':'').$p; ?></p></a>
<?php } ?>
</div>
<div class="inline-block width-66 narrow-full">
<?php echo editable_area('useful', 'doc/'.$page); ?>
</div>