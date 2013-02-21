<?php $this->load->view('common/header'); ?>
<?php echo form_open('profile/location'); ?>
<?php echo validation_errors(); ?>
    <fieldset class="right" id="profile-settings-about">
        <ul>
            <li>Your currently set location is:</li>
	    <li><strong><?php echo (isset($location->locationTitle) ? anchor('group/'.$location->locationID, $location->locationTitle) : '-'); ?></strong> (<?php echo anchor('group/all', 'View all'); ?>)</li>
	    <li>You can remove your location by typing a - (a dash) into each field</li>
	    <li>Note that you will be listed in the City's Colorless Division that is only viewable to Colorless members.</li>
        </ul>
    </fieldset>
    <fieldset id="location-settings">
        <ul>
            <li><label for="locationCountry">Country:</label>
                <?php echo form_input('locationCountry', set_value('locationCountry')); ?>
            </li>
            <li><label for="locationCity">City:</label>
                <?php echo form_input('locationCity', set_value('locationCity')); ?>
            </li>
            <li class="right">
                <?php echo form_submit('', 'Save changes'); ?>
            </li>
	    <li>
		Done?
	    </li>
        </ul>
    </fieldset>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>