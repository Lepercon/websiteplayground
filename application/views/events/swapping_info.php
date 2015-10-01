<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
if(is_null($swap['event_poster'])){
    $poster = '';
}else{
    $poster = explode('.',$swap['event_poster']);
    $poster[0] .= '_800px';
    $poster = VIEW_URL.'events/posters/'.implode('.', $poster);
}
?>

<div class="jcr-box square-box">
    <div class="">
        <a href="<?php echo site_url('signup/event/'.$swap['id']); ?>" class="wotw-poster-link">
            <?php if($poster != ''){ ?>
                <img class="wotw-poster" src="<?php echo $poster; ?>">
            <?php }else{ ?>
                <div class="wotw-poster"></div>
            <?php } ?>
        </a>
        <div class="wotw-name wotw-remain">
            <h2>Seat Swapping: <?php echo $swap['name']; ?></h2>
            <p>
                <?php echo date('l jS F Y',$swap['swapping_opens']).' from '.date('G:i', $swap['swapping_opens']); ?> 
                <a href="<?php echo site_url('events/view_event/'.$swap['event_id']); ?>" class="ui-icon ui-icon-info inline-block" title="View this news item in context"></a>
            </p>                
            <a href="<?php echo site_url('signup/event/'.$swap['id']); ?>" class="jcr-button inline-block">
                <span class="ui-icon ui-icon-pencil inline-block"></span> Seat Swapping
            </a>
            <?php if(isset($extra_text)) { 
                echo $extra_text;
            } ?>
        </div>
    </div>
</div>
