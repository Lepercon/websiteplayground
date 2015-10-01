<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="ww-nav" value="<?php echo $page; ?>" class="<?php echo $page; ?>">
    <ul class="nolist">
        <li id="ww-exec">
            <?php echo anchor('whoswho/print_profiles/exec', 'Exec', 'class="no-jsify"'); ?>
        </li><li id="ww-assistants">
            <?php echo anchor('whoswho/print_profiles/assistants', 'Exec Officio & Assistants', 'class="nav-two-line no-jsify"'); ?>
        </li><li id="ww-sports">
            <?php echo anchor('whoswho/print_profiles/sports', 'Sports Captains', 'class="nav-two-line no-jsify"'); ?>
        </li><li id="ww-societies">
            <?php echo anchor('whoswho/print_profiles/societies', 'Society Presidents', 'class="nav-two-line no-jsify"'); ?>
        </li><li id="ww-committees">
            <?php echo anchor('whoswho/print_profiles/committees', 'Committee Chairs', 'class="nav-two-line no-jsify"'); ?>
        </li><li id="ww-support">
            <?php echo anchor('whoswho/print_profiles/support', 'Student Support', 'class="nav-two-line no-jsify"'); ?>
        </li><li id="ww-services">
            <?php echo anchor('whoswho/print_profiles/services', 'Services', 'class="no-jsify"'); ?>
        </li><li id="ww-staff">
            <?php echo anchor('whoswho/print_profiles/staff', 'College Staff', 'class="no-jsify"'); ?>
        </li>
    </ul>
</div>
<div>
    <div id="whoswho-icons">
    <?php
        echo '<span class="page" style="display:none;">'.$page.'</span>';
        //var_dump($all_whoswho);
    if($access_rights) { ?>
        <a href="" class="jcr-button inline-block" id="whoswho-print" title="">
            <span class="ui-icon ui-icon-print inline-block"></span>Download Printable
        </a>
    <?php } ?>
    
    <ul id="whoswho" class="nolist">
    <?php foreach($all_whoswho as $who){ ?><li class="inline-block">
        <div class="whoswho-id"><?php echo $who['id']; ?></div>
        <span class="no-jsify whoswho-jsify"><div style="background-image: url(<?php echo get_usr_img_src($who['uid'], array('medium', 'small')); ?>);"></div>
            <p><b><?php echo user_pref_name($who['firstname'], $who['prefname'], $who['surname']); ?></b></p>
            <p><?php echo $who['full']; ?></p>
                        <p style="display:none;"><?php echo $who['email']; ?></p>
        </span>
    </li><?php } ?>
    </ul>
    </div><div id="whoswho-details"><div id="whoswho-mem-content"><?php if(!empty($mem)) $this->load->view('whoswho/users', array('mem' => $mem)); ?></div></div>

</div>
