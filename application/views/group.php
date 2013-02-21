<?php $this->load->view('common/header'); ?>
<h3><?php echo $location->locationTitle; ?></h3>
<div id="map_canvas" style="width: 650px; height: 400px; margin-bottom: 18px"></div>
<h3>Residents</h3>
<div class="users-grid">
    <?php foreach($users as $u) { ?>
	<?php echo anchor('user/'.$u->userName, '<img src="http://www.gravatar.com/avatar/'.md5(strtolower($u->userEmail)).'?s=64" alt="'.$u->userName.'" class="avatar" />'); ?>
    <?php } ?>
</div>
<?php echo anchor('group/all', 'Return to all locations'); ?> or <?php echo anchor('profile/location', 'set your location'); ?>
<?php $this->load->view('common/footer'); ?>