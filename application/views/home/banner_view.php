<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
    $this->load->view('home/poster_banner');
?>
<div class="wotw-outer jcr-box outer-box">
    <h2 class="wotw-day">Messages from your JCR:</h2>
    <div class="message-scroll">
        <?php
            foreach($messages as $m){
        ?>
                <span><b><?php echo is_null($m['name'])?'':$m['name']; ?></b><?php echo (is_null($m['name'])?'':' - ').$m['message']; ?></span>
        <?php            
            }
        ?>
    </div>
</div>
<span class="banner-page"></span>
<span class="clock"><span class="hour"><?php echo date('H'); ?></span><span class="colon"></span><span class="minute"><?php echo date('i'); ?></span></span>
