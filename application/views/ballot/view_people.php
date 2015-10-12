<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

echo back_link('ballot/view_ballot/'.$b['id']);
?>

<h2>Options Totals</h2>
<?php 
    foreach($people['totals'] as $k => $t){
        echo '<h3>'.$people['options'][$k]['title'].'</h3>';
        foreach($t as $kk => $n){
            echo '<p>'.$people['options'][$k]['options'][$kk]['name'].' - <b>'.$n.'</b></p>';
        }
    }
?>
<h2>Special Requirements</h2>
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

<h2>Totals By Table</h2>
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

<h2>All Preferences</h2>
<a href="<?php echo $this->uri->segment(4)?site_url('ballot/view_signups/'.$b['id']):site_url('ballot/view_signups/'.$b['id'].'/1'); ?>"><p>Show by <?php echo $this->uri->segment(4)?'Table Number':'Surname'; ?></p></a>
<table>
<?php 
    echo '<tr><th>Name</th><th>Table Number</th><th>Requirements</th>';
    foreach($people['options'] as $o){
        echo '<th>'.$o['title'].'</th>';
    }
    echo '<th>Price</th></tr>';
    foreach($people['people'] as $k => $p){
        echo '<tr>';
        echo '<td>'.$p['name'].'</td>';
        echo '<td>'.$p['table_num'].'</td>';
        echo '<td>'.$p['requirements'].'</td>';
        $price = $b['price'];
        foreach($p['op_list'] as $o){
            echo '<td>'.$o['name'].'</td>';
            $price += $o['price'];
        }
        echo '<td>£'.number_format($price, 2).'</td>';
        echo '</tr>';
    }
?>
</table>