<?php $this->load->view('common/header'); ?>
<?php echo form_open('/admin/thread/'.$thread->threadID, array('class'=>'form-awesome form-awesome-wider round-10')); ?>
    <ul>
        <li>Administrating: "<?php echo $thread->threadTitle; ?>"</li>
        <li>
            <label>Role</label>
            <?php echo form_dropdown('role', array('normal' => 'normal', 'stickyboard' => 'stickyboard', 'sticky' => 'sticky'), set_value('role', $thread->threadStatus)); ?>
        </li>
        <li>
            <button type="submit"><span>Save</span></button>
        </li>
    </ul>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>