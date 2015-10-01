<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

    $user = $this->users_model->get_users($post['created_by'], 'uid');

?>

<div class="jcr-box square-box">
    <div class="wotw-top-row">
        <?php echo user_profile_a_open($post['created_by']); ?>
            <div class="wotw-icon" style="background-image:url(<?php echo get_usr_img_src($user['uid'], 'small'); ?>)"></div>
        </a>
        <div class="wotw-name">
            <h2><?php echo user_profile_a_open($post['created_by']); ?><?php echo $post['created_name']; ?></a></h2>
            <p>
                <?php echo time_elapsed_string($post['created']); ?> 
                <?php if($show_link && $post['event_id'] > 0) echo '<a href="'.site_url('events/view_event/'.$post['event_id']).'" class="ui-icon ui-icon-info inline-block" title="View this news item in context"></a>'; ?>
            </p>
        </div>
    </div>
    <div class="wotw-supporting">
        <h3 class=""><?php echo $post['title']; ?></h3>
        <p><?php echo nl2br($post['content']); ?></p>
        <?php if(is_admin() or $this->session->userdata('id') == $post['created_by']) echo '<p><a href="'.site_url('events/delete_post/'.$post['id']).'" class="admin-delete-button no-jsify ui-icon ui-icon-trash inline-block" title="Delete this post"></a></p>'; ?>
    </div>
</div>