<?php $this->load->view('common/header'); ?>
<?php if($this->user->isLoggedIn()) { ?>
<?php echo form_open('post/move/'.$post->postID, array('id'=>'move-thread')); ?>
    <fieldset>
        <ul>
            <li><label for="postBoard">New board:</label>
            <?php echo form_dropdown('postBoard', $this->chan->get_boards(), (isset($post) ? $post->postBoard : false)); ?></li>
            <li><?php echo form_submit('', 'Move'); ?></li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php } ?>
<?php $this->load->view('common/footer'); ?>
