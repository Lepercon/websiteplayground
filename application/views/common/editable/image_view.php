<?php if(!empty($images)) { ?>
<div class="editable-image-create-addexisting">
        <ul>
            <?php foreach($images as $image): ?>
            <li>
                <div><div class="editable-image-create-dummy-div"></div><img src="<?php echo $image_url.$image; ?>" />
                    <div class="editable-image-create-remove"></div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
</div>
<?php } else { ?>
<h3>No images exist</h3>
<?php }?>