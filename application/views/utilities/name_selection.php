<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
    
    if(!isset($users)){
        if(isset($_SESSION['users'])){
            $users = $_SESSION['users'];
        }else{
            $users = $this->users_model->get_all_user_ids_and_names();
            $_SESSION['users'] = $users;
        }
    }
    
    if(!isset($name)){
        $name = 'newmember-id';
    }

?>
<input type="text" name="newmember" value="" placeholder="Name" style="width:150px" id="nameentry"><input type="hidden" name="<?php echo $name; ?>" id="nameentry-id">
<span id="users-list">
<?php
    foreach($users as $u){?>
        <p value="<?php echo $u['id']; ?>"><?php echo $u['name']; ?></p><?php
    }
?>
</span>