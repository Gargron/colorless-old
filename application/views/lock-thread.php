<?php $this->load->view('common/header'); ?>
<?php echo form_open('post/lock/'.$post->postID, array('id'=>'lock-thread')); ?>
    <fieldset>
        <ul>
            <li>You sure? You are locking it down!</li>
            <li><?php echo form_submit('confirm', 'Yes, lock it'); ?></li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>