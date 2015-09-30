<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo form_open('events/add_post', array('class' => 'jcr-form')); ?>
<ul class="nolist">
    <li>
        <label>Title</label><?php echo form_input(array(
            'name' => 'title',
            'maxlength' => '50',
            'value' => set_value('title'),
            'required' => 'required',
            'placeholder' => 'Post title',
            'class' => 'input-help narrow-full',
            'title' => 'Required Field. A title for your news post, up to 50 characters long.'
        )); ?>
    </li>
    <li>
        <label>Content</label><?php echo form_textarea(array(
            'name' => 'content',
            'value' => set_value('content'),
            'required' => 'required',
            'rows' => '10',
            'placeholder' => 'Post content',
            'class' => 'input-help narrow-full',
            'title' => 'Required Field. The content of your news post.'
        )); ?>
    </li>
    <li>
        <?php if(isset($post_id)) echo form_hidden('post_id', $post_id);
        echo form_hidden('event_id', $event_id);
        echo token_ip('news_post'); ?>
        <label class="narrow-hide"></label><?php echo form_submit('save_post', 'Save Post'); ?>
    </li>
</ul>
<?php echo form_close();