<?php if(!empty($docs)) { ?>
<div class="editable-doc-create">
    <ul>
        <?php foreach($docs as $doc):
            $doc_img = VIEW_URL.'common/editable/icons/doc_icons/'.(in_array($doc['ext'], array('doc', 'docx', 'jpg', 'mp3', 'pdf', 'png', 'ppt', 'pptx', 'txt', 'xls', 'xlsx')) ? $doc['ext'] : 'default').'.png';
            // reduce file name to fit in box
            $file = pathinfo($doc['name']);
            $length = strlen($file['filename']);
            $doc_name = ($length > 17 ? substr($file['filename'], 0, 11).'...'.substr($file['filename'], -5) : $file['filename']).'.'.$file['extension'];
            ?>
            <li>
                <img class="editable-doc-create-icon" src="<?php echo $doc_img; ?>" title="<?php echo $doc['ext']; ?>" />
                <div class="editable-doc-create-details">
                    <div class="editable-doc-create-url"><?php echo $doc_url.$doc['name']; ?></div>
                    <div class="editable-doc-create-filename"><?php echo $doc_name; ?></div>
                    <div class="editable-doc-create-full-filename"><?php echo $doc['name']; ?></div>
                    <div class="editable-doc-create-modified"><?php echo $doc['modified']; ?></div>
                    <div class="editable-doc-create-size"><?php echo $doc['size']; ?></div>
                </div>
                <div class="editable-doc-create-remove"></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php } else { ?>
<h3>No documents exist</h3>
<?php } ?>