<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
    $url = VIEW_URL.'photos/images/';
    echo back_link('photos');
?>
<h2>View Orders</h2>
<?php 
    if(empty($orders)){
        echo '<h3>No orders have been placed...</h3>';
    }else{
        echo '<div id="accordion">';
        foreach($orders as $o){
            echo '<h3>Order: '.($o[0]['prefname']==''?$o[0]['firstname']:$o[0]['prefname']).' '.$o[0]['surname'].date(' - l jS M H:i', $o[0]['time']).' - <span class="stat">'.$statuses[$o[0]['status']].'</span></h3>';
            echo '<div>';
            echo '<table class="orders-table">';
            echo '<tr><th colspan="2">Photo</th><th>Type</th><th>Price</th><th>Qty</th><th>Total</th></tr>';
            $total = 0;
            foreach($o as $p){
                echo '<tr>';
                echo '<td><a href="'.site_url('photos/photo/'.$p['photo_id']).'"><div style="background-image:url('.$url.$p['thumb_name'].')" class="order-thumb"></div></a></td>';
                echo '<td><a href="'.site_url('photos/photo/'.$p['photo_id']).'">Photo '.$p['photo_id'].'</a></td>';
                echo '<td>'.$p['format'].'</td>';
                echo '<td>£'.number_format($p['price'],2).'</td>';
                echo '<td>'.$p['qty'].'</td>';
                echo '<td>£'.number_format($p['price']*$p['qty'],2).'</td>';
                echo '</tr>';
                $total += ($p['price']*$p['qty']);
            }
            echo '<tr><th></th><th></th><th></th><th></th><td class="normal-height">Total:</td><th>£'.number_format($total,2).'</th></tr>';
            echo '</table>Order Status: '.form_hidden(array('oid'=>$o[0]['order_id'])).form_dropdown('status', $statuses, $o[0]['status'], 'class="status-update"').'</div>';
        }
        echo '</div>';
    }
?>
