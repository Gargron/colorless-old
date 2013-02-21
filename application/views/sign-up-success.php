<?php $this->load->view('common/header'); ?>
<h3>You successfully joined us!</h3>
<p>We have sent you an e-mail so you don't forget your login credentials. You can turn e-mail notifications on and off for different tasks in your settings, so we will never e-mail you without your permission.</p>
<p>Note: In case our first e-mail will not reach your postbox because the address was fake, your account will be marked as questionable.</p>
<p>We recommend you to fill out your profile settings right after <?php echo anchor('login', 'logging in'); ?> now, and then you can start communicating with everyone!</p>
<?php $this->load->view('common/footer'); ?>