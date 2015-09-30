<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('finance/');?>

<h1>My Groups</h1>

<?php
if($permissions){
    echo anchor('finance/my_group/all', 'All Groups');
}

if(!empty($groups)) {
    foreach($groups as $g){?>
        <h3><a href="<?php echo site_url('finance/my_group/'.$g['group_id']); ?>"><?php echo $g['budget_name']; ?></a></h3>
    <?php }
} else { ?>
    <p>You have no groups.</p>
<?php } ?>
<a class="jcr-button inline-block" title="Greate A New Group." href="<?php echo site_url('finance/create_group'); ?>">
    <span class="ui-icon inline-block ui-icon-pencil"></span>
    Create New Group
</a>