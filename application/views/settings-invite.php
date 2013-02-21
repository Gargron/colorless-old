<?php $this->load->view('common/header'); ?>
<div class="form-tabbed">
    <?php echo validation_errors(); ?>
    <?php echo $result; ?>
    <ul class="form-tab-navigation">
        <li><a href="javascript:;" class="form-tab-navigation-link active">Invitations</a></li>
        <li><a href="javascript:;" class="form-tab-navigation-link">Your invitations</a></li>
    </ul>
    <?php echo form_open('profile/invite', array('class'=>'form-awesome round-10')); ?>
        <div class="form-tab active">
            <fieldset>
                <legend>Invite a Friend</legend>
                <ul>
                    <?php if ($invites_left > 0) { ?>
                    <li><label for="userAlias">His/her email:</label>
                        <?php echo form_input('inviteeEmail', set_value('inviteeEmail', '')); ?>
                    </li>
                    <li>You have <?php echo $invites_left; ?> invitations left.</li>
                    <?php } else { ?>
                    <li>You have no invitations left. But don't worry! They reload on monthly cycles.</li>
                    <?php } ?>
                </ul>
            </fieldset>
        </div><!--.form-tab-->
        <div class="form-tab">
            <fieldset>
                <legend>Your Invitations</legend>
                <ul>
                    <?php foreach($invites as $invitation) { ?>
                        <li><?php echo $invitation->invitationStatus == 'created'
                            ? anchor('/user/' . $invitation->userName, $invitation->userName) . ' (accepted)' 
                            : $invitation->invitationFriendEmail . ' (pending)'; ?></li>
                    <?php } ?>
                </ul>
            </fieldset>
        </div>
        <ul>
            <li>
                <button type="submit"><span>Send it!</span></button>
            </li>
        </ul>
    <?php echo form_close(); ?>
</div><!--.form-tabbed-->
<?php $this->load->view('common/footer'); ?>