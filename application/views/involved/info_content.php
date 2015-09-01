<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="in-nav" class="<?php echo $page;?> narrow-hide">
<ul class="nolist"><?php foreach($this->inv_pages as $p) echo '<li id="in-'.$p.'">'.anchor('involved/index/'.$p, ucfirst($p)).'</li>'; ?></ul>
</div>

<?php echo editable_area('involved', 'content/information/information', $access_rights); ?>