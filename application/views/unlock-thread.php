<?php $this->load->view('common/header'); ?>
<?php echo form_open('post/unlock/'.$post->postID, array('id'=>'unlock-thread')); ?>
    <fieldset>
        <ul>
            <li>You sure? You are unlocking this thread and everyone will be able to post on it.</li>
            <li><?php echo form_submit('confirm', 'Yes, unlock it'); ?></li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>