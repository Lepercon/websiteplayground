<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>
<div class="content-left width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Family Tree</h2>
        <div style="padding:5px">
            <h3>Children</h3>
            <?php echo str_replace(' ', '&nbsp;&nbsp;&nbsp;', str_replace(chr(10), '<br>', var_export($children, true))); ?>
            <h3>Parents</h3>
            <?php echo str_replace(' ', '&nbsp;&nbsp;&nbsp;', str_replace(chr(10), '<br>', var_export($parents, true))); ?>
            <svg></svg>
        </div>
    </div>
</div>
<?php $this->load->view('family_tree/options'); ?>