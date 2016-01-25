<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); echo back_link('finance/view_claims');?>
<h1>Edit Budgets</h1>

<div id="accordion" class="edit-budgets-accordion" role="tablist">
<?php
    $view_budget = $this->uri->segment(3);
    $i = -1;
    $index = -1;
    $all_levels[0] = '';
    asort($all_levels);
    foreach($budgets as $b){        
?>
        <h3><?php echo $b['budget_name']; ?></h3>
        <div>
            <?php 
                if($b['budget_name'] == 'Other'){
                    echo 'This budget cannot be removed or edited.';
                }else{
            ?>
                    <?php
                        $i++;
                        $this_tab = $view_budget == $b['id'];
                        if($this_tab){
                            $index = $i;
                            echo validation_errors('<div class="validation_errors"><span class="ui-icon ui-icon-cross inline-block"></span>', '</div>');
                            if($success){
                                echo '<div class="validation_success"><span class="ui-icon ui-icon-check inline-block"></span>Budget Infomation Updated.</div>';
                            }
                        }        
                        echo form_open(site_url('finance/claims/edit_budgets/'.$b['id']), array('class'=>'budget-edit jcr-form'), array('budget_id'=>$b['id']));
                        echo '<p>'.form_label('Budget Name: ');
                        echo form_input(array('name' => 'budget_name', 'id' => 'budgetname', 'value' => $b['budget_name'])).'</p>';
                        
                        echo '<p>'.form_label('New Admin Level: ').'</td><td>';
                        echo form_dropdown('holder', $all_levels, '').'</p>';
                
                        echo form_label('');        
                        echo form_submit('edit-budget-submit', 'Submit').'</td></tr>';
                        echo form_close();
                        
                        echo form_open(site_url('finance/claims/edit_budgets/'.$b['id']), array('class'=>'budget-edit jcr-form'), array('budget_id'=>$b['id']));
                    ?><p>Use the options below to remove levels:</p><?php
                        foreach($b['levels'] as $id => $name){
                            echo '<p>'.form_label($name).form_checkbox('remove_'.$id, $id, FALSE).'</p>';
                        }
                        echo form_label('').form_submit('remove-user-submit', 'Remove Levels').'</td></tr>';
                        echo form_close();
                    ?>
            <?php
                }
            ?>
        </div>
<?php
    }
?>
</div>
<?php    
    if($index != -1){
        echo '<span id="index_select" style="display:none">'.$index.'</span>';
    }?>