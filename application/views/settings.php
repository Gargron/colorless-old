<?php $this->load->view('common/header'); ?>
<div class="form-tabbed">
    <?php echo validation_errors(); ?>
    <!--<ul class="form-tab-navigation">
        <li><a href="javascript:;" class="form-tab-navigation-link active">Account settings</a></li>
        <li><a href="javascript:;" class="form-tab-navigation-link">Profile settings</a></li>
    </ul>-->
    <?php echo form_open('profile/settings', array('class'=>'form-awesome round-10')); ?>
        <!--<div class="form-tab active">
            <fieldset>
                <legend>Account information</legend>
                <ul>
                    <li><label for="userAlias">Name:</label>
                        <?php echo form_input('userAlias', set_value('userAlias', (isset($user->userBlob->userAlias) ? $user->userBlob->userAlias : false))); ?>
                    </li>
                    <li>
                        <label for="userEmail">E-mail:</label>
                        <?php echo form_input(array('name'=>'userEmail', 'value'=>set_value('userEmail', $user->userEmail), 'readonly'=>'readonly')); ?>
                    </li>
                    <li>
                        <label for="userPassword">New pass:</label>
                        <?php echo form_password(array('name'=>'userPassword', 'autocomplete'=>'off')); ?>
                    </li>
                    <li>
                        <label for="userPasswordConfirm">New pass again:</label>
                        <?php echo form_password(array('name'=>'userPasswordConfirm', 'autocomplete'=>'off')); ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Site settings</legend>
                <table>
                    <tr>
                        <td>
                            <label for="settingReceiveMNs">E-mail on new mentions:</label>
                        </td>
                        <td>
                            <?php echo form_checkbox('settingReceiveMNs', true, set_value('settingReceiveMNs', (isset($user->userBlob->settingReceiveMNs) ? $user->userBlob->settingReceiveMNs : true))); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="settingReceivePMs">E-mail on new PMs:</label>
                        </td>
                        <td>
                            <?php echo form_checkbox('settingReceivePMs', true, set_value('settingReceivePMs', (isset($user->userBlob->settingReceivePMs) ? $user->userBlob->settingReceivePMs : true))); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="settingReceiveFRs">E-mail on friend requests:</label>
                        </td>
                        <td>
                            <?php echo form_checkbox('settingReceiveFRs', true, set_value('settingReceiveFRs', (isset($user->userBlob->settingReceiveFRs) ? $user->userBlob->settingReceiveFRs : true))); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="settingReceiveFRs">Show the Spam board on menu:</label>
                        </td>
                        <td>
                            <?php echo form_checkbox('settingShowSpam', true, set_value('settingShowSpam', (isset($user->userBlob->settingShowSpam) ? $user->userBlob->settingShowSpam : false))); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="settingPrivacy">Enable privacy for Dashboard:</label>
                        </td>
                        <td>
                            <?php echo form_checkbox('userPrivacy', true, set_value('userPrivacy', (isset($user->userPrivacy) ? $user->userPrivacy : false))); ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div><!--.form-tab-->
        <!--<div class="form-tab">
            <fieldset>
                <legend>About yourself</legend>
                <ul>
                    <li><label for="userCountry">Country:</label>
                        <?php $countries = $this->user->getCountries();
                              array_unshift($countries, ""); ?>
                        <?php echo form_dropdown('userCountry', $countries, set_value('userCountry', (isset($user->userBlob->userCountry) ? $user->userBlob->userCountry : false))); ?>
                    </li>
                    <li><label for="userGender">Gender:</label>
                        <?php echo form_dropdown('userGender', array("unknown"=>"Unknown", "male"=>"Male", "female"=>"Female"), set_value('userGender', (isset($user->userBlob->userGender) ? $user->userBlob->userGender : false))); ?>
                    </li>
                    <li><label for="userYear">Date of birth:</label>
                        <?php echo form_input(array('name'=>'userYear', 'id'=>'userYear', 'value'=>set_value('userYear', (isset($user->userBlob->userYear) ? $user->userBlob->userYear : false)), 'placeholder'=>'dd.mm.yyyy')); ?>
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
                    <li><label for="socialLastfm">YouTube username:</label>
                        <?php echo form_input('socialYoutube', set_value('socialYoutube', (isset($user->userBlob->socialYoutube) ? $user->userBlob->socialYoutube : false))); ?>
                    </li>
                    <li><label for="socialLastfm">Forrst username:</label>
                        <?php echo form_input('socialForrst', set_value('socialForrst', (isset($user->userBlob->socialForrst) ? $user->userBlob->socialForrst : false))); ?>
                    </li>
                    <li><label for="socialLastfm">deviantArt username:</label>
                        <?php echo form_input('socialDeviantart', set_value('socialDeviantart', (isset($user->userBlob->socialDeviantart) ? $user->userBlob->socialDeviantart : false))); ?>
                    </li>
                    <li><label for="socialLastfm">Steam username:</label>
                        <?php echo form_input('socialSteam', set_value('socialSteam', (isset($user->userBlob->socialSteam) ? $user->userBlob->socialSteam : false))); ?>
                    </li>
                    <li><label for="socialLastfm">Skype username:</label>
                        <?php echo form_input('socialSkype', set_value('socialSkype', (isset($user->userBlob->socialSkype) ? $user->userBlob->socialSkype : false))); ?>
                    </li>
                    <li><label for="socialLastfm">Flickr username:</label>
                        <?php echo form_input('socialFlickr', set_value('socialFlickr', (isset($user->userBlob->socialFlickr) ? $user->userBlob->socialFlickr : false))); ?>
                    </li>
                </ul>
            </fieldset>
        </div><!--.form-tab-->
        <!--<ul>
            <li>
                <button type="submit"><span>Save changes</span></button>
            </li>
        </ul>-->
        <p>
            Hey, we're going to have settings locked until 0:00AM EST on Christmas Day. 

            Sorry for the troubles!
            <a href="http://thecolorless.net/thread/557589#p557589">More Information</a>
        </p>
    <?php echo form_close(); ?>
</div><!--.form-tabbed-->

<?php $this->load->view('common/footer'); ?>
