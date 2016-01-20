<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

if($this->ballot_admin){
    echo '<p>'.anchor('ballot/create', '<span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span>&nbsp;Create New', 'class="jcr-button"').'</p><br>';
}

foreach($ballots as $b){
    
    $options = explode(':', $b['options']);
    $op = array();
    
    $min_price = $b['price'];
    $max_price = $b['price'] + $b['guest_charge'];
    
    foreach($options as $k=>$o){
        $temp = explode(';', $o);
        $op[$k]['title'] = $temp[0];
        $op[$k]['options'] = array();
        $op[$k]['options']['price'] = array();
        foreach(array_slice($temp, 1) as $i => $t){
            $name_price = explode('#', $t);
            if(count($name_price) < 2){
                $name_price[1] = 0;
            }
            $op[$k]['options']['price'][$i] = $name_price[1];
        }
        if(!empty($op[$k]['options']['price'])){
            $min_price += min($op[$k]['options']['price']);
            $max_price += max($op[$k]['options']['price']);
        }
    }
    $min_price = number_format($min_price, 2);
    $max_price = number_format($max_price, 2);

?>
    <div class="jcr-box wotw-outer">
        <a href="<?php echo site_url('ballot/view_ballot/'.$b['id'].'/'.(is_null($b['signup_name'])?url_title($b['name']):url_title($b['name'].'-'.$b['signup_name']))); ?>"><h3 class="wotw-day"><?php echo (is_null($b['signup_name'])?$b['name']:$b['name'].' - '.$b['signup_name']); ?></h3></a>
        <div>
            <p><?php echo $b['name']; ?> is at <b><?php echo date('H:i \o\n l, d/m/Y', $b['time']); ?></b></p>
            <?php if($b['open_time'] > time()){ ?>
                <p>Signup Will Open at <b><?php echo date('H:i \o\n l, d/m/Y', $b['open_time']); ?></b></p>
            <?php } if($b['close_time'] > time()){ ?>
                <p>Signup Will Close at <b><?php echo date('H:i \o\n l, d/m/Y', $b['close_time']); ?></b></p>
            <?php }else{ ?>
                <p>Signup Has Closed</p>
            <?php } ?>
            <p>Max Group Size: <b><?php echo $b['max_group']; ?></b></p>
            <p>Price: <b><?php echo '£'.$min_price.($max_price!=$min_price?' - '.'£'.$max_price:''); ?></b></p>
            <p>Spaces: <b><?php $num = 0; foreach(explode(';', $b['tables']) as $t){ $num += $t; } echo $num; ?></b></p>
        </div>
    </div>
<?php } ?>