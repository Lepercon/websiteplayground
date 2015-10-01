<?php
    $this->load->model('liversout_model');
    $current_loo = $this->liversout_model->get_contact();
?>

<div class="jcr-box wotw-outer">
    <h2 class="wotw-day">Contact</h2>
    <div>
        If you would like more infomation, please contact the current Livers Out Officer, <?php echo $current_loo; ?>.
    </div>
</div>