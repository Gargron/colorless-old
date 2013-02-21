<?php $this->load->view('common/header'); ?>
<?php echo form_open('post/delete/'.$post->postID, array('id'=>'delete-thread')); ?>
    <fieldset>
        <ul>
            <li>You sure, man?</li>
            <li><?php echo form_submit('confirm', 'Yes, delete it'); ?></li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>