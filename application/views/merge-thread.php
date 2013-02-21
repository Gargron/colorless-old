<?php $this->load->view('common/header'); ?>
<?php echo form_open('post/merge/'.$post->postID, array('id'=>'merge-thread')); ?>
    <fieldset>
        <ul>
            <li><label for="postBoard" style="padding-top: 10px">Merge with:</label>
            <?php $title = array('name'=>'postName', 'value' => $postName, 'id'=>'postName', 'class'=>'input-large', 'placeholder'=>'Post name...');
            echo form_input($title); ?>
            <?php echo form_submit('', 'Search threads...'); ?></li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php if (!empty($postList)) { ?>
<?php echo form_open('post/merge/'.$post->postID, array('id'=>'merge-thread')); ?>
    <fieldset>
        <ul>
            <?php foreach($postList as $post) { ?>
            <input type="radio" name="threadID" value="<?php echo $post->postID; ?>"><strong><?php echo $post->postTitle; ?></strong> by <?php echo $post->userName; ?> (<?php echo anchor('/thread/'.$post->postID, 'view it'); ?>)</input><br />
            <?php } ?>
            <li><?php echo form_submit('', 'Merge now!'); ?></li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php } ?>
<?php $this->load->view('common/footer'); ?>
