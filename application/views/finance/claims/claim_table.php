<?php

function table_of_claims($claims, $admin, $button_text = '', $button_class = '', $status = array(), $checkboxes=FALSE){
    if((gettype($claims) == 'array') and (sizeof($claims) > 0)){?>
        <table class="table-of-claims"><tr>
        <?php
            if($admin){
                echo ($checkboxes?'<th></th>':'').'<th>Claim ID</th>';
            }    
        ?>
        <th>Submitted By</th><th>Payment For</th><th>Payment To</th><th>Amount</th><th>Method</th><th>Budget</th><th>Budget Holder(s)</th><th>Details</th><th>Status</th><th>Files</th><?php
        if($button_text != '' or $admin){?>
            <th>Actions</th><?php
        }?></tr><?php
        foreach($claims as $c){
            $i = 1;?>
            <tr class="claims-row">
                <?php
                    if($admin){
                        echo ($checkboxes?'<td><input type="checkbox" class="select-claims"></td>':'');
                ?>
                        <td><a href="<?php echo site_url('finance/claims/view_claim/'.$c['id']); ?>"><?php echo $c['id']; ?></a></td>
                <?php
                    }    
                ?>
                <td><?php echo ($c['prefname']==''?$c['firstname']:$c['prefname']).' '.$c['surname']; ?></td>
                <td><?php echo $c['item']; ?></td>
                <td><?php echo $c['pay_to']; ?></td>
                <td><?php echo '£'.$c['amount']; ?></td>
                <td><?php echo ($c['payment_method']==1?'Cheque':'Bank Transfer'); ?></td>
                <td><?php echo $c['budget_name']; ?></td>
                <td><?php foreach($c['owners'] as $k=>$u){ echo ($k==0?'':', ').($u['prefname']==''?$u['firstname']:$u['prefname']).' '.$u['surname']; }?></td>
                <td><?php echo str_replace(chr(10), '<br>', $c['details']); ?></td>
                <td class="claim-status">
                    <?php 
                        echo ($c['status']==0?'Waiting For Budget Holder':($c['status']==1?'Waiting For Payment':'Paid')); 
                    ?>
                </td>
                <td  style="width:45px;"><?php
                    $files = explode(',', $c['files'],-1);
                    foreach($files as $f){?>
                        <p style="margin:0px"><a href="<?php echo site_url('application/views/finance/files/'.$f);?>"><?php echo 'File '.$i++;?></a></p><?php
                    } 
                ?></td>
                    <td class="claim-actions">
                <?php
                    if($button_text != '' and in_array($c['status'], $status)){
                ?>
                        <span id="claim-id" style="display:none;"><?php echo $c['id']; ?></span><a href="#" class="<?php echo $button_class; ?>"><?php echo $button_text; ?></a><?php
                        if($admin){
                            echo ', ';
                        }
                    }
                    if($admin){
                ?>
                        <a href="<?php echo site_url('finance/claims/edit_claim/'.$c['id']); ?>">Edit</a>
                <?php
                    }
                ?>
                    </td>
            </tr>
            <?php
        }?></table><?php
        
        
    }else{?>    
        <p>No claims found.</p><?php
    }
}

?>