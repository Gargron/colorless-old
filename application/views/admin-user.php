<?php $this->load->view('common/header'); ?>
<?php echo form_open('/admin/user/'.$user->userID, array('class'=>'form-awesome form-awesome-wider round-10')); ?>
    <ul>
        <li>
            <label>Username</label>
            <?php echo form_input('username', set_value('username', $user->userName)); ?>
        </li>
        <li>
            <label>Email</label>
            <?php echo form_input('email', set_value('email', $user->userEmail)); ?>
        </li>
        <li>
            <label>Role</label>
            <?php echo form_dropdown('role', array(0 => 'Member', 1 => 'Verified', 2 => 'Moderator', 3 => 'Administrator'), set_value('role', $user->userRole)); ?>
        </li>
        <li>
            <label>Status</label>
            <?php echo form_dropdown('status', array('active' => 'Active', 'questionable' => 'Blocked', 'inactive' => 'Banned', 'deleted' => 'Deleted'), set_value('status', $user->userRole)); ?>
        </li>
        <li>
            <button type="submit"><span>Save</span></button>
        </li>
    </ul>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>