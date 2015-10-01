<?php

    if ( ! defined('BASEPATH')) 
        exit('No direct script access allowed');
                
?>
<div class="width-33 narrow-full inline-block content-left">
    <div id="column-spacer"></div>
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Prospective Students</h2>
        <div id="prospective-left">
            <p>Click a section below to find out more:</p>
            <?php
                 foreach($sections as $key => $val){
            ?>
                    <a class="no-jsify <?php echo ($key==$section?'anchor-selected':''); ?>" href="<?php echo site_url('prospective/page/'.$key); ?>"><p><?php echo $val; ?></p></a>
            <?php    
                 }
            ?>
        </div>
    </div>
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Get In Contact</h3>
        <p>If you would like more help or information then please get in contact:</p>
    <?php $this->load->view('utilities/users_contact', array('level_ids'=>array(2))); ?>
</div>

</div>
<div class="width-66 narrow-full inline-block content-right" id="prospective-right">
    <div class="jcr-box wotw-outer" id="prospective-content-area">
        <h2 class="wotw-day section-name"><?php echo $sections[$section]; ?></h2>
        <div id="prospective-content">
            <?php
            
                echo editable_area('prospective', 'content/'.$section, $access_rights); 
                if($section == 'welcome'){
            ?>    
                        
            <?php
                }
            ?>
        </div>
    </div>
</div>