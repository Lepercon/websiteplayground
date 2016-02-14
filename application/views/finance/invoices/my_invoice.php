<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('finance/invoices/my_invoices');
$invoice_id = $this->uri->segment(3);
?>
<h1>My Invoice</h1>
<div class="wotw-outer jcr-box">
<h3 class="wotw-day">Group Details:</h3>
<div>
<table class="invoice-details">
    <tr>
        <td>Group Name:</td>
        <td><?php echo $group['budget_name']; ?></td>
    </tr>
    <tr>
        <td>Payment Options:</td>
        <td><?php echo $group['how_to_pay']; ?></td>
    </tr>
</table>
</div>
</div>
<div class="wotw-outer jcr-box">
<h3 class="wotw-day">Details:</h3>
<div>
<table class="invoice-details">
    <tr>
        <td>Name:</td>
        <td><?php echo $invoice['name']; ?></td>
    </tr>
    <tr>
        <td>Status:</td>
        <td><?php echo ($invoice['paid']?'Paid':'Unpaid').((!$invoice['paid'] && $invoice['marked_paid'])?'*':''); ?></td>
    </tr>
    <tr>
        <td>Date:</td>
        <td><?php echo date('jS F Y',$invoice['date']); ?></td>
    </tr>
    <tr>
        <td>Amount:</td>
        <td>£<?php echo $invoice['amount']; ?></td>
    </tr>
    <tr>
        <td>Details:</td>
        <td><?php echo $invoice['details']; ?></td>
    </tr>
    <tr>
        <td>Date Paid:</td>
        <td><?php echo ($invoice['paid'] && !is_null($invoice['date_paid']))?date('d/m/Y', $invoice['date_paid']):''; ?></td>
    </tr>
</table>

<?php 
    if(!$invoice['paid'] && $invoice['marked_paid']){
?>
        <p>* You have marked this invoice as paid, but it has not yet been accepted by the group owner.</p>
<?php
    }
?>
<a class="jcr-button inline-block invoice-mark-paid" title="<?php echo ($invoice['marked_paid']?'Select this to undo.':'Select this once you have sent in the payment for this item.'); ?>" href="#"><?php echo ($invoice['marked_paid']?'Unmark':'Mark'); ?> As Paid</a>
<span style="display:none;" class="invoice-id"><?php echo $invoice['id'] ?></span>
<span style="display:none;" class="invoice-marked-status"><?php echo ($invoice['marked_paid']?'1':'0'); ?></span>
</div>
</div>
