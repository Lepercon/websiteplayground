<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>

<div id="archive-left" class="content-left width-33 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">JCR Documents</h2>
        <p>Select a menu option from the list below:</p>
        <ul class="nolist">
            <?php foreach($sections as $s): ?>
            <li>
                <?php echo anchor('archive/index/'.$s['short'], '<h3'.($s['short']==$section?' class="selected"':'').'>'.$s['full'].'</h3>'); ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php if(is_admin()) { ?>
            <a href="<?php echo site_url('archive/manage'); ?>" class="jcr-button inline-block" title="Manage sections on this page">
                <span class="ui-icon ui-icon-gear inline-block"></span>Manage
            </a>
        <?php } ?>
    </div>
    <hr />
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">JCR Awards</h2>
        <?php foreach(array('2014-2015','2013-2014', '2012-2013', '2011-2012','2010-2011','2009-2010') as $y) echo '<h3 style="margin-left: 1em;"><a class="no-jsify" href="'.VIEW_URL.'archive/doc/awards/'.$y.'.pdf">'.$y.'</a></h3>';?>
    </div>
</div>
<div id="archive-right" class="content-right width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Archive</h2>
        <div>
            <?php
                if(is_null($section)){
                    $section = 'intro';
                }else{
                    echo editable_area('archive', 'content/intro', $access_rights);
                }
                echo editable_area('archive', 'content/'.$section, $access_rights);
            ?>
        </div>
        <?php if($section != 'intro') { ?>
            <div id="archive-right">
                <div id="archive-docs">
                    <?php $this->load->view('archive/doc_view'); ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
