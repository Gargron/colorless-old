<?php $this->load->view('common/header'); ?>
<h3>Unbanning user #<?php echo $user->userID; ?>: <?php echo $user->userName; ?></h3>
<?php echo form_open('profile/unban/'.$user->userID, array('id'=>'ban-user')); ?>
    <fieldset>
        <ul>
            <li>You wanna forgive? That's cool, bro.</li>
            <li><?php echo form_submit('confirm', 'Yup, forgive them'); ?></li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>