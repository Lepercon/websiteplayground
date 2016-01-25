<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php

    $page_admin = $this->finance_model->finance_permissions();
    $u_id = $this->session->userdata('id');

    $notifications = $this->finance_model->get_notification_totals($u_id, $page_admin);

?>
<div class="jcr-box wotw-outer">
    <h2  class="wotw-day">Notifications</h2>
    <div>
        <p><span class="inline-block ui-icon ui-icon-document"></span>You have <b><?php echo $notifications['unread']; ?></b>/<?php echo $notifications['total']; ?> unread <a href="<?php echo site_url('finance/notifications'); ?>">notification<?php echo $notifications['unread']===1?'':'s'; ?>.</a></p>
    </div>
</div>
