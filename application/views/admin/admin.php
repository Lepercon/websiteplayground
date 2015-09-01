<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
eval(success_code());?>

<h2>Administration</h2>
<h3>Users:</h3>
<p><a href="<?php echo site_url('admin/user_directory'); ?>">Users list</a> - View a list of all JCR members</p>
<p><a href="<?php echo site_url('admin/levels');?>">Edit Roles</a> - Add, delete or modify JCR roles and the people in them</p>
<p><a href="<?php echo site_url('admin/page_edit_rights');?>">Page Editing</a> - Manage which users can edit page content</p>
<p><a href="<?php echo site_url('admin/change_passwords');?>">Reset Alumni Passwords</a> - Reset passwords for students who's accounts have been dissabled</p>
<p><a href="<?php echo site_url('family_tree/new_family');?>">Add College Family</a> - Add new parents and children to the family tree</p>
<p><a href="<?php echo site_url('admin/user_photos');?>">Upload Profile Photos</a> - Upload a profile photo for a user</p>
<br>
<h3>Asthetic:</h3>
<p><a href="<?php echo site_url('admin/menu');?>">Menu Bar</a> - Edit the Menu Bar</p>
<p><a href="<?php echo site_url('home/banner');?>">Poster Banner</a> - View the poster banner</p>
<p><a href="<?php echo site_url('admin/messages');?>">Set Messages</a> - View and set the messages for the banner page</p>
<br>
<h3>Site Tools:</h3>
<p><a href="<?php echo site_url('admin/sync');?>">Synchronise</a> - Synchronise the JCR site with the University database</p>
<p><a href="<?php echo site_url('admin/categories')?>">Categories</a> - Manage the list of categories used throughout the site</p>
<!--<p><a href="<?php echo site_url('admin/database');?>">Site cleanup</a> - Tidy up files, images and the database</p>-->
<br>
<h3>Help:</h3>
<p><a href="<?php echo site_url('admin/help');?>">Help</a> - Find help with using the JCR Website</p>