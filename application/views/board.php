<?php $this->load->view('common/header'); ?>
<?php //echo $this->pagination->create_links(); ?>
    <!--Begin control panel-->
    <div class="black-box">
    <?php if($this->user->isLoggedIn()): ?>
    <button onclick="window.location.href='/post/create?board=<?php echo $boardID; ?>'" style="width:100%;margin-bottom:20px"><span style="font-size:18px;letter-spacing:-1px;font-weight:normal;">Create new thread</span></button>
    <?php endif; ?>
    <br />
    <!-- BuySellAds.com Zone Code -->
    <div id="bsap_1262243" class="bsarocks bsap_6185bec2da7bb340a6a5e59e4e0e5995"></div>
    <!-- End BuySellAds.com Zone Code -->
    </div>
    <!--End control panel-->
<div class="threads-loop" style="margin-bottom: 20px">
<?php $this->load->view('common/threads-loop'); ?>
</div>
<?php echo $this->pagination->create_links(); ?>
<?php if (false) { ?>
<?php if($this->user->isLoggedIn()) {
    if($this->user->isQuestionable()) {
?>
<div id="please-sign-up">
    <h4 id="please-sign-up-head">Stop!</h4>
    <p id="please-sign-up-tag">Your e-mail address could not be reached</p>
    <p id="please-sign-up-expl">We could not send you an e-mail. That might mean your address is a fake, or has a typo. Please update it.</p>
</div>
<?php
    } else {
        if($boardID == 8 && (!$this->user->isMod() && !$this->user->isAdmin())) {

        } else {
            $this->load->view('common/new-post');
        }
    }
} else { ?>
<div id="please-sign-up">
    <h4 id="please-sign-up-head">Wait!</h4>
    <p id="please-sign-up-tag">You need to be one of us to post, create threads, friend others and upload pictures</p>
    <p id="please-sign-up-expl"><strong>Good news</strong>: That is easily fixable in less than a minute. <?php echo anchor('login/register', 'Sign up'); ?> or <?php echo anchor('login', 'Login', array('class'=>'login-link')); ?>.</p>
</div>
<?php } ?>

<?php } ?>

<?php $this->load->view('common/footer'); ?>
