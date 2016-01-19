<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

echo back_link('ballot/view_ballot/'.$b['id']);
?>
<div id="tabs">
    <ul class="no-print">
        <li><a href="#tabs-1">Options Totals</a></li>
        <li><a href="#tabs-2">Special Requirements</a></li>
        <li><a href="#tabs-3">Totals By Table</a></li>
        <li><a href="#tabs-4">All Preferences</a></li>
    </ul>
<h2></h2>
<div id="tabs-1">
    <?php 
        foreach($people['totals'] as $k => $t){
            echo '<h3>'.$people['options'][$k]['title'].'</h3>';
            foreach($t as $kk => $n){
                echo '<p>'.$people['options'][$k]['options'][$kk]['name'].' - <b>'.$n.'</b></p>';
            }
        }
    ?>
</div>
<div id="tabs-2">
<table>
<?php 
    echo '<tr><th>Name</th><th>Table Number</th><th>Requirements</th>';
    foreach($people['options'] as $o){
        echo '<th>'.$o['title'].'</th>';
    }
    echo '</tr>';
    foreach($people['requirements'] as $k => $p){
        echo '<tr>';
        echo '<td>'.$p['name'].'</td>';
        echo '<td>'.$p['table_num'].'</td>';
        echo '<td>'.$p['requirements'].'</td>';
        foreach($p['op_list'] as $o){
            echo '<td>'.$o['name'].'</td>';
        }
        echo '</tr>';
    }
?>
</table>
</div>
<div id="tabs-3">
<table>
<?php 
    echo '<tr><th>Table Number</th>';
    foreach($people['options'] as $o){
        foreach($o['options'] as $oo){
            echo '<th>'.$oo['name'].'</th>';
        }
    }
    echo '</tr>';
    foreach($people['table_totals'] as $k => $t){
        echo '<tr>';
        echo '<td>'.$k.'</td>';
        foreach($t as $kk => $o){
            foreach($people['options'][$kk]['options'] as $kkk => $oo){
                echo '<td>'.(isset($o[$kkk])?$o[$kkk]:0).'</td>';
            }
        }
        echo '</tr>';
    }
?>
</table>
</div>
<div id="tabs-4">
<a class="no-print" href="<?php echo $this->uri->segment(4)?site_url('ballot/view_signups/'.$b['id']):site_url('ballot/view_signups/'.$b['id'].'/1'); ?>"><p>Show by <?php echo $this->uri->segment(4)?'Table Number':'Surname'; ?></p></a>
<table class="user-list">
<?php 
    echo '<tr><th>Name</th><th>Email</th><th>Price</th><th>Table Number</th><th>Requirements</th>';
    foreach($people['options'] as $o){
        echo '<th>'.$o['title'].'</th>';
    }
    echo '<th>Signed Up By</th></tr>';
    foreach($people['people'] as $k => $p){
        echo '<tr>';
        echo '<td>'.($p['user_id']==-1?'Guest: '.$p['name'].' ('.$p['creator_name'].')':$p['name']).'</td>';
        echo '<td>'.$p['email'].'</td>';
        $price = $b['price'];
        if($p['user_id'] == -1)
            $price += $b['guest_charge'];
        $ops = '';
        foreach($p['op_list'] as $o){
            $ops .= '<td>'.$o['name'].'</td>';
            $price += $o['price'];
        }
        echo '<td>£'.number_format($price, 2).'</td>';
        echo '<td>'.$p['table_num'].'</td>';
        echo '<td>'.$p['requirements'].'</td>';
        echo $ops;
        echo '<td>'.$p['creator_name'].'</td>';
        echo '</tr>';
    }
?>
</table>
</div>
</div>