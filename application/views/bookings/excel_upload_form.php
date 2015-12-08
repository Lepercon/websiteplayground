<?php
echo 'Please upload the excel copy of KX in the most recent type of excel on your computer. <br>To do this select save as, and under type select the most recent type of excel you have.';
?>


<?php echo $this->upload->display_errors('<p>', '</p>'); ?>

<?php 

    echo form_open_multipart('', 'class="jcr-form no-jsify"');
    echo '<p>'.form_upload('userfile').'</p>';
    echo '<p>'.form_submit('upload', 'Upload').'</p>';
    echo form_close();
    
?>



