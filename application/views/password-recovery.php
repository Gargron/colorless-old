<?php $this->load->view('common/header'); ?>
<?php if(!isset($fulfilled)): ?>
<p>If you forgot your password, we can reset it for you. Enter your e-mail address and the new password will be sent to you. You can change the password later.</p>
<?php echo form_open('login/recover'); ?>
<fieldset>
    <ul>
        <li><label for="userEmail">E-mail:</label><?php echo form_input('userEmail'); ?></li>
        <li><?php echo form_submit('', 'Reset password'); ?></li>
    </ul>
</fieldset>
<?php echo form_close(); ?>
<?php else: ?>
<p>Your new password has been sent to your e-mail.</p>
<?php endif; ?>
<?php $this->load->view('common/footer'); ?>