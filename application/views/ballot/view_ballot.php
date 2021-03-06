<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
echo back_link('ballot');


if($b['id'] == 7){
    echo '<span id="green-formal-alert" style="">';
    echo '<div>'.editable_area('ballot', 'content/popup_'.$b['id'], is_admin()).'</div>';
    echo '</span>';
}
?>
<div class="no-print">
<?php

echo editable_area('ballot', 'content/top_desc_'.$b['id'], is_admin());

    function get_value($b, $name, $num, $option_num=NULL){
        if(isset($b['people'][$num])){
            if(strrpos($name, 'person') !== FALSE){
                if($b['people'][$num]['user_id'] == -1){
                    return 'Guest';
                }
                return $b['people'][$num]['name'];
            }elseif(strrpos($name, 'id') !== FALSE){
                return $b['people'][$num]['user_id'];
            }elseif(strrpos($name, 'requirements') !== FALSE){
                return $b['people'][$num]['requirements'];
            }elseif(strrpos($name, 'guestname') !== FALSE){
                if($b['people'][$num]['user_id'] == -1){
                    return $b['people'][$num]['name'];
                }
            }else{
                $options = explode(';', $b['people'][$num]['options']);
                return $options[$option_num];
            }
        }elseif(isset($_POST[$name])){
            return $_POST[$name];
        }
        return '';
    }
    $options = array();
    if($b['options'] != ''){
        $options = explode(':', $b['options']);
    }
    $op = array();
    
    $min_price = $b['price'];
    $max_price = $b['price'] + $b['guest_charge'];
    
    foreach($options as $k=>$o){
        $temp = explode(';', $o);
        $op[$k]['title'] = $temp[0];
        $op[$k]['options'] = array();
        foreach(array_slice($temp, 1) as $i => $t){
            $name_price = explode('#', $t);
            if(count($name_price) < 2){
                $name_price[1] = 0;
            }
            $op[$k]['options']['names'][$i] = $name_price[0].' (£'.number_format($name_price[1], 2).')';
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
<div id="ballot_accordion">
    <h3><?php echo $b['full_name']; ?></h3>
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
        <p>Guests Allowed: <b><?php echo $b['allow_guests']?'Yes (Max '.$b['max_guests'].')':'No'; ?></b></p>
        <p>Price: <b>£<?php echo ($min_price==$max_price)?$min_price:$min_price.' - £'.$max_price; ?></b></p>
        <p>Total Spaces: <b><?php $num = 0; foreach(explode(';', $b['tables']) as $t){ $num += $t; } echo $num; ?></b></p>
    </div>

    <h3>Tables</h3>
    <div>
        <?php foreach(explode(';', $b['tables']) as $k => $v){ ?>
            <p>Table <?php echo ($k+1); ?>: <b><?php echo ($v); ?> spaces</b></p>
        <?php } ?>
    </div>

    <h3>Options</h3>
    <div>
        <p>Base Price: £<span class="base-price"><?php echo number_format($b['price'], 2); ?></span></p>
        <p>Guest Charge: £<span class="guest-price"><?php echo number_format($b['guest_charge'], 2); ?></span></p>
        <ul class="options-list">
        <?php foreach($op as $o){ ?>
            <h3><?php echo $o['title']; ?></h3>
            <?php foreach($o['options']['names'] as $k => $p){ 
                echo '<p value="'.$o['options']['price'][$k].'">'.($k+1).'. '.$p.'</p>'; ?>
            <?php }?>
        <?php } ?>
        </ul>
    </div>

    <h3>Signup</h3>
    <div>
        <?php if($b['open_time'] > time()){ ?>
            <p>Signup Will Open at <b><?php echo date('H:i \o\n l, d/m/Y', $b['open_time']); ?></b></p>
        <?php }elseif($b['close_time'] > time()){ ?>
            <p>Please enter your signup details below, you do not need to fill all of the availible places.</p>
            <p>For each person you must type a few letters of the first name or surname, then select them from the list.</p>
        <?php
            if($b['allow_guests']){
                echo '<p>If you are trying to sign up a guest, please type "Guest" in the box.</p>';
            }
            echo '<p>If the box glows red around a name then you have done something wrong and they <b>won\'t</b> be entered into the ballot</p>';
            if(!empty($b['people'])){
                if($b['people'][0]['created_by'] == $user_id){
                    echo '<p class="validation_success"><span class="ui-icon ui-icon-check inline-block green-icon"></span>You have signed up '.sizeof($b['people']).(sizeof($b['people'])==1?' person':' people').'. The result will be published after the ballot has closed.</p>';
                }else{
                    echo '<p class="validation_success">Since the sign ups below were not created by yourself, you will not be able to make edits to them.</p>';
                }
            }
            foreach($_SESSION['errors'] as $e){
                echo '<p class="validation_errors"><span class="ui-icon ui-icon-close inline-block"></span>'.$e.'</p>';
            }
            echo form_open('', 'class="jcr-form"');
            
            echo form_label('Split Group:').form_dropdown('split-group', array(0=>'No',1=>'Yes'), isset($b['people'][0])?$b['people'][0]['split_group']:'').' Would you like your group to be split if you can not fit as a full group?<br><br>';
            foreach(range(1,$b['max_group']) as $r){
                echo '<span class="person-option">';
                echo '<p>'.form_label('Person '.$r.':');
                echo form_input(array('name'=>'person-'.$r, 'placeholder'=>'Name', 'value'=>get_value($b, 'person-'.$r, $r-1), 'class'=>'name-selection')).' ';
                echo form_input(array('name'=>'guestname-'.$r, 'type'=>'hidden', 'value'=>get_value($b, 'guestname-'.$r, $r-1), 'class'=>'guest-name', 'placeholder'=>'Guest Full Name', 'title'=>'Please enter the full name of your guest.', 'pattern'=>''));
                echo form_input(array('name'=>'id-'.$r, 'type'=>'hidden', 'value'=>get_value($b, 'id-'.$r, $r-1), 'class'=>'user-id')).'</p>';
                
                foreach($op as $k=>$o){
                    echo '<p class="options">'.form_label().form_label($o['title'].':').form_dropdown('option-'.$r.'-'.$k, $o['options']['names'], get_value($b, 'option-'.$r.'-'.$k, $r-1, $k), 'style="min-width:167px"').'</p>';
                }
                
                echo '<p>'.form_label().form_label('Special Requirements:').form_input('requirements-'.$r, get_value($b, 'requirements-'.$r, $r-1), 'placeholder="Special Requirements"').'</p>';
                echo '<p>'.form_label().form_label('Price:').'<b>£<span class="user-price">0.00</span></b></p>';
                
                echo '<br></span>';
            }
            if(!isset($b['people'][0]) || $b['people'][0]['created_by'] == $user_id){
                echo form_label().form_label().form_submit('', 'Submit');
            }
            
            echo form_close();
        }else{ ?>
            <p>Signup Has Closed.</p>
            <?php if(!empty($b['people'])){
                echo 'You have chosen to'.($b['people'][0]['split_group']?'':' not').' split your group if there is not space for your whole group.';
            }?>
        <table><tr><th>Name</th>
        <?php
            foreach($op as $o){
                echo '<th>'.$o['title'].'</th>';
            }
        ?><th>Cost</th></tr>
        <?php foreach($b['people'] as $p){
                echo '<tr><td>'.$p['name'].'</td>';
                $options = explode(';', $p['options']);
                $price = 0;
                if($p['user_id'] == -1)
                    $price += $b['guest_charge'];
                foreach($options as $k=>$o){
                    echo '<td>'.$op[$k]['options']['names'][$o].'</td>';
                    $price += $op[$k]['options']['price'][$o];
                }
                echo '<td>£'.number_format($price+$b['price'], 2).'</td></tr>';
            } ?>
        <?php } ?>
        </table>
    </div>
    <?php if($this->ballot_admin){ ?>
        <h3>Admin</h3>
        <div>
            <a href="<?php echo site_url('ballot/view_signups/'.$b['id']); ?>"><p>View Sign Ups</p></a>
            <a href="<?php echo site_url('ballot/payments/'.$b['id']); ?>"><p>Payments</p></a>
        </div>
    <?php } ?>
</div>

<span id="users-list">
<?php
    foreach($users as $u){
    ?><p value="<?php echo $u['id']; ?>"><?php echo $u['name']; ?></p><?php
    }
    if($b['allow_guests']){
        echo '<p value="-1">Guest</p>';
    }
?>
</span>
<span id="active-tab" style="display: none;"><?php echo (empty($b['people']) && empty($_SESSION['errors']))?0:3; ?></span>
</div>