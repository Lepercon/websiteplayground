<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!empty($e['table_names'])){
    $f = str_replace(',', '', $e['table_names']);
    $c = explode(',',$e['table_names']);
}
$seats = explode(",", $e['seats']);
$permission = $this->signup_model->check_permission($e);
$u_id = $this->session->userdata('id');

foreach($e['tables'] as $num => $t):?>
    <div class="signup-table inline-block">
        <h3><?php 
        if($e['type']==1 || $e['type']==2){
            echo 'Table '.$num;
            echo (empty($f)) ? '' : ': '.trim($c[($num)-1]); 
        }
        elseif($e['type']==3){
            echo 'Coach '.$num;
            echo (empty($f)) ? '' : ': '.trim($c[($num)-1]); 
        }
        elseif($e['type']==4){
            echo 'Shirt Size '.$num;
            echo (empty($f)) ? '' : ': '.trim($c[($num)-1]); 
        }
        else{
            echo 'Group '.$num;
            echo (empty($f)) ? '' : ': '.trim($c[($num)-1]); 
        }
        ?></h3>
        <?php $free = $seats[$num - 1] - count($t['seats']);
        if($free == 0) echo '<div class="red">Full</div>';
        elseif($free < 0) echo '<div class="red">'.($free * -1).' space'.($free == -1 ? '' : 's').' overbooked</div>';
        else echo '<div class="green">'.$free.' space'.($free == 1 ? '' : 's').'</div>'; ?>
        <ol class="<?php echo empty($e['swap_price'])?'nolist':'nolist'; ?> inline-block">
        <?php foreach($t['seats'] as $s) { ?>
            <li style="width:130px;" class="<?php echo $u_id==$s['reserved_by']?'my-reservation':'reservation'; ?>">
                <?php echo (empty($s['name']) ? 'Reserved' : $s['name']);
                if($permission && empty($e['swap_price'])) echo '<a href="'.site_url('signup/delete_booking/'.$e['id'].'/'.$s['id']).'" class="signup-delete-button no-jsify"></a>'; ?>
            </li>
        <?php } ?>
        </ol>
    </div>
<?php endforeach; ?>