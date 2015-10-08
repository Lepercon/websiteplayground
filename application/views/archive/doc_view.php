<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if($section == 'finance_decisions'){
    $this->load->view('archive/finance_decisions');
    return;
}

$months = array(1=>'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

function add_ordinal($day) {
    if($day > 3 && $day < 21) return $day.'th';
    $the_num = (string) $day;
    switch(substr($the_num, -1)) {
        case "1":
            $the_num.="st";
            break;
        case "2":
            $the_num.="nd";
            break;
        case "3":
            $the_num.="rd";
            break;
        default:
            $the_num.="th";
    }
    return $the_num;
}

if(is_admin()) { ?>
    <a href="<?php echo site_url('archive/add_new_doc/'.$section); ?>" class="jcr-button inline-block" title="Upload new document">
        <span class="ui-icon ui-icon-arrowthick-1-n inline-block"></span>Upload
    </a>
<?php } ?>

<ul class="nolist">
<?php foreach($years as $y) : ?>
    <li>
        <h3><div class="archive-arrow"></div><?php echo ($y-1).' - '.$y; ?></h3>
        <ul class="nolist">
            <?php foreach($docs[$y] as $d) : ?>
                <li>
                    <?php if(is_admin()) { ?>
                        <a href="<?php echo site_url('archive/delete_doc/'.$d['id']); ?>" class="admin-delete-button no-jsify inline-block jcr-button"><span class="ui-icon ui-icon-close inline-block"></span></a>
                    <?php } ?>
                    <a class="inline-block" target="_blank" href="<?php echo VIEW_URL.'archive/doc/'.$section.'/'.$d['doc_name']; ?>">
                        <p><?php echo $d['name'].(empty($d['month']) ? '' : ' '.add_ordinal($d['day']).' '.$months[$d['month']]); ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </li>
<?php endforeach; ?>
</ul>