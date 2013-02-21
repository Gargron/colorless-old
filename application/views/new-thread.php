<?php $this->load->view('common/header'); ?>
<?php if($this->user->isLoggedIn()) { ?>
<?php $this->load->view('common/new-post'); ?>
<?php } ?>
<?php $this->load->view('common/footer'); ?>