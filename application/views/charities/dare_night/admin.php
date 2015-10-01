<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php     
    if(isset($admin) and $admin){
?>
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Admin</h2>
        <div>
            <h3>Submissions</h3>
            <ol style="padding-left:40px;">
            <?php
                foreach($submissions as $s){
                    echo '<li><a href="'.site_url('/charities/view_submission/'.$s['id']).'">Team '.$s['id'].' - '.($s['prefname']==''?$s['firstname']:$s['prefname']).' '.$s['surname'].' ('.$s['completed'].')</a>'.($s['submitted']?' - Submitted':'').'</li>';
                }
            ?>
            </ol>
        </div>
    </div>
<?php
    }
?>
