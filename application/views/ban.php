<?php $this->load->view('common/header'); ?>
<h3>Banning user #<?php echo $user->userID; ?>: <?php echo $user->userName; ?></h3>
<?php echo form_open('profile/ban/'.$user->userID, array('id'=>'ban-user')); ?>
    <fieldset>
        <ul>
            <li>Are you sure? They won't be able to access the site anymore.</li>
            <li><?php echo form_submit('confirm', 'Sure, send\'em to hell'); ?></li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>