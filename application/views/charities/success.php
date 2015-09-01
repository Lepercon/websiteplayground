<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo editable_area('charities', 'content/success', $access_rights);?>

<a href="<?php echo site_url('charities/orders');?>">Return to the charities home page</a>