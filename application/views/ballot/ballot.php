<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 



foreach($ballots as $b){
    
    $options = explode(':', $b['options']);
    $op = array();
    
    $min_price = $b['price'];
    $max_price = $b['price'];
    
    foreach($options as $k=>$o){
        $temp = explode(';', $o);
        $op[$k]['title'] = $temp[0];
        $op[$k]['options'] = array();
        foreach(array_slice($temp, 1) as $i => $t){
            $name_price = explode('#', $t);
            if(count($name_price) < 2){
                $name_price[1] = 0;
            }
            $op[$k]['options']['names'][$i] = $name_price[0].' (Â£'.number_format($name_price[1], 2).')';
            $op[$k]['options']['price'][$i] = $name_price[1];
        }
        $min_price += min($op[$k]['options']['price']);
        $max_price += max($op[$k]['options']['price']);
    }
    $min_price = number_format($min_price, 2);
    $max_price = number_format($max_price, 2);

?>
    <div class="jcr-box wotw-outer">
        <a href="<?php echo site_url('ballot/view_ballot/'.$b['id']); ?>"><h3 class="wotw-day"><?php echo $b['name'].' - '.$b['signup_name']; ?></h3></a>
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
            <p>Price: <b><?php echo '£'.$min_price.' - '.'£'.$max_price; ?></b></p>
            <p>Spaces: <b><?php $num = 0; foreach(explode(';', $b['tables']) as $t){ $num += $t; } echo $num; ?></b></p>
        </div>
    </div>
<?php } ?>