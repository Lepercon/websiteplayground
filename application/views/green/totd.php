<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

    $user = $this->users_model->get_users_with_level(58, 'users.id, uid');

?>

<div class="jcr-box square-box">
    <div class="">
        <a href="<?php echo site_url('green'); ?>" class="wotw-poster-link">
            <img class="wotw-poster wotw-green" src="<?php echo VIEW_URL.'home/img/green.png'; ?>">
        </a>
        <div class="wotw-name wotw-remain">
            <h2><a href="<?php echo site_url('green'); ?>">Green Tip of the Day</a></h2>
            <p>
                <?php echo date('l jS F Y'); ?> 
                <a href="<?php echo site_url('green'); ?>" class="ui-icon ui-icon-info inline-block"></a>
            </p>
            <p><?php echo $tip; ?></p>
        </div>
    </div>
</div>