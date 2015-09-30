<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$name = user_pref_name($mem['firstname'], $mem['prefname'], $mem['surname']); ?>

<div id="whoswho-mem">
    <img class="whoswho-image" src="<?php echo get_usr_img_src($mem['uid'], 'large'); ?>" alt="JCR Profile Picture" />
    <div class="whoswho-mem-details">
        <h2><?php echo $name; ?></h2>
        <?php
            echo '<div class="whoswho-desc">'.$mem['level_desc'].'</div>';
            foreach($mem['roles'] as $l){ 
                echo '<h3>'.$l['full'].' ('.$l['year'].'/'.($l['year']+1).')</h3>';
                echo '<p>'.$l['description'].'</p>';
            }
            ?>
        <br />
        <?php if($mem['id'] == $this->session->userdata('id')) { ?>
            <a href="<?php echo site_url('details/profile'); ?>" class="jcr-button inline-block" title="Edit my Butler JCR profile">
                <span class="ui-icon ui-icon-wrench inline-block"></span>Edit profile
            </a>
        <?php } else { ?>
            <a href="<?php echo site_url('contact/'.$mem['id']); ?>" class="jcr-button inline-block" title="Contact <?php echo $name; ?>">
                <span class="ui-icon ui-icon-mail-closed inline-block"></span>Contact
            </a>
        <?php } ?>
    </div>
    <div class="spacer" style="clear: both;"></div>
</div>