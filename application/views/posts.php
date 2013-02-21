<?php $this->load->view('common/header'); ?>
<div class="black-box lone">
    <?php if(isset($tag)): ?>
        <?php $creator = $this->user->getUsers(array('userID'=>$tag->tagCreatorID)); ?>
        <p>This tag "<?php echo $tag->tagSlug; ?>" was created by <?php echo anchor('user/'.$tag->tagCreatorID, $creator->userName); ?> and has <strong><?php echo $tag->tagCount; ?></strong> posts tagged with it.</p>
    <?php endif; ?>
    <!-- BuySellAds.com Zone Code -->
    <div id="bsap_1262243" class="bsarocks bsap_6185bec2da7bb340a6a5e59e4e0e5995"></div>
    <!-- End BuySellAds.com Zone Code -->
</div>
<?php //echo $this->pagination->create_links(); ?>
<?php $this->load->view('common/threads-loop'); ?>
<?php echo $this->pagination->create_links(); ?>
<?php $this->load->view('common/footer'); ?>