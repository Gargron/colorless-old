<?php $this->load->view('common/header'); ?>
<?php echo form_open('profile/settings', array('class'=>'form-awesome round-10')); ?>
    <fieldset>
	<legend>About yourself</legend>
	<ul>
            <li><label for="userCountry">Country:</label>
                <?php $countries = $this->user->getCountries(); ?>
                <?php echo form_dropdown('userCountry', $countries, set_value('userCountry', (isset($user->userBlob->userCountry) ? $user->userBlob->userCountry : false))); ?>
            </li>
            <li><label for="userGender">Gender:</label>
                <?php echo form_dropdown('userGender', array("unknown"=>"Unknown", "male"=>"Male", "female"=>"Female"), set_value('userGender', (isset($user->userBlob->userGender) ? $user->userBlob->userGender : false))); ?>
            </li>
            <li><label for="userYear">Date of birth:</label>
                <?php echo form_input('userYear', set_value('userYear', (isset($user->userBlob->userYear) ? $user->userBlob->userYear : false))); ?>
            </li>
            <li><label for="userHP">Your homepage URL:</label>
                <?php echo form_input('userHP', set_value('userHP', (isset($user->userBlob->userHP) ? $user->userBlob->userHP : false))); ?>
            </li>
            <li>
                <label for="userAbout">About yourself in 160 characters:</label>
                <?php echo form_textarea(array('name'=>'userAbout', 'value'=>set_value('userAbout', (isset($user->userBlob->userAbout) ? $user->userBlob->userAbout : false)), 'id'=>'userAbout', 'rows'=>'5')); ?>
            </li>
	</ul>
    </fieldset>
    <fieldset>
	<legend>Avatar</legend>
	<ul>
	    <li><img src="http://www.gravatar.com/avatar/<?php echo md5(strtolower($user->userEmail)) ?>?s=64&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="64" height="64" class="avatar left" style="margin-right:10px" />Your avatars are powered by <a href="http://en.gravatar.com" rel="nofollow" target="_blank">Gravatar</a>. To manage them, create an account at Gravatar using the same e-mail address as you are using here.</li>
	</ul>
    </fieldset>
    <fieldset>
	<legend>Social profiles</legend>
	<ul>
            <li><label for="socialTwitter">Twitter username:</label>
                <?php echo form_input('socialTwitter', set_value('socialTwitter', (isset($user->userBlob->socialTwitter) ? $user->userBlob->socialTwitter : false))); ?>
            </li>
            <li><label for="socialTumblr">Tumblr username:</label>
                <?php echo form_input('socialTumblr', set_value('socialTumblr', (isset($user->userBlob->socialTumblr) ? $user->userBlob->socialTumblr : false))); ?>
            </li>
            <li><label for="socialFacebook">Facebook username:</label>
                <?php echo form_input('socialFacebook', set_value('socialFacebook', (isset($user->userBlob->socialFacebook) ? $user->userBlob->socialFacebook : false))); ?>
            </li>
            <li><label for="socialLastfm">Last.fm username:</label>
                <?php echo form_input('socialLastfm', set_value('socialLastfm', (isset($user->userBlob->socialLastfm) ? $user->userBlob->socialLastfm : false))); ?>
            </li>
	</ul>
    </fieldset>
    <ul>
	<li>
	    <button type="submit"><span>Save changes</span></button>
	</li>
    </ul>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>