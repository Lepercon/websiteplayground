<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
    $split = explode('frame_redirect.html%2F%3A80%2F', $_SERVER['REQUEST_URI']);
    if(sizeof($split) != 2){
        $split = explode('frame_redirect.html%2F', $_SERVER['REQUEST_URI']);
    }
    if(sizeof($split) == 2){
        $link = explode('&redirect_status=404', $split[1]);
        $link = str_replace('%2F', '/', $link[0]);
?>
        <div id="page-not-found" style="text-align: center;">
            <h1>Redirecting...</h1>
            <p>If you are not redirected, please click <a href="<?php echo site_url($link); ?>">here</a>.</p>
        </div>
        <span style="display:none;" id="redirect-link"><?php echo site_url($link); ?></span>
<?php
    }else{
?>
        <span class="page-not-found">
            <h2>Ooops... we can't find that page!</h2>
            <p>If you think that page should exist please <?php echo anchor('contact', 'contact the JCR'); ?>.</p>
            <p>Or, visit a page using the navigation above.</p>
        </span>
<?php
    }
?>