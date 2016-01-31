<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed'); 

echo back_link('ballot/view/'.$b['id'].'/'.$b['url_name']);

$methods = array(
    'bank_transfer' => 'Bank Transfer',
    'cash' => 'Cash',
    'cheque' => 'Cheque'
);

echo '<h2>Payments</h2>';

if(!empty($payments['not_sent'])){
    echo '<p>Not all invoices have not yet been sent to attendees. To do so please click '.anchor('ballot/payments/'.$b['id'].'/#', 'here', 'class="click-to-send"').'.</p>';
    echo '<div style="display: none;">';
    echo form_open('', 'class="send-invoices no-jsify"');
    echo form_input('send-invoices', 'send');
    echo form_close();
    echo '</div>';
}else{
    
    echo form_open('', 'class="jcr-form"');
    echo form_label('Search:').form_input('search', '', 'placeholder="Search..." class="search-people"');
    echo form_close();
    
    echo '<table>';
    echo '<tr><th>Name</th><th>Amount</th><th>Table Number</th><th>Paid</th><th>Actions</th></th>';
    foreach($payments['sent'] as $p){
        echo '<tr>';
        echo '<td class="name-search">'.$p['name'].($p['user_id']==-1?' ('.$p['creator_name'].')':'').'</td>';
        echo '<td>£'.$p['amount'].'</td>';
        echo '<td>'.$p['table_num'].'</td>';
        echo '<td>'.($p['paid']?'Yes ('.$methods[$p['payment_method']].')':'No').'</td>';
        echo '<td invoice-id="'.$p['inv_id'].'"><a href="#" class="mark-unpaid jcr-button green-button" style="'.($p['paid']?'':'display:none;').'">Mark Unpaid</a>';
        echo '<a href="#" class="mark-paid bank jcr-button" method="bank" style="'.($p['paid']?'display:none;':'').'">Bank Transfer</a>';
        echo '<a href="#" class="mark-paid cheque jcr-button" method="cheque" style="'.($p['paid']?'display:none;':'').'">Cheque</a>';
        echo '<a href="#" class="mark-paid cash jcr-button" method="cash" style="'.($p['paid']?'display:none;':'').'">Cash</a></td>';
        echo '</tr>';
    }
    echo '</table>';
}

echo '<span style="display:none" class="ajax-url">'.site_url('ballot/payment/'.$b['id']).'</span>';

/*  End of file payments.php  */