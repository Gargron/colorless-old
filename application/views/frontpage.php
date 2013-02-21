<?php $this->load->view('common/header'); ?>
<?php if(!$this->uri->segment(2)) { ?>
    <h3 class="dash-head">Popular
        <span style="float: right">
            <a id="next_popular" href="#"><span class="icon spin_12" style="width:12px;height:12px;position:relative;bottom:-2px;"></span>next</a>
        </span>
    </h3>
    <dl class="thread-item" id="featured-popular">
    <?php $this->load->view('common/popular-widget.php'); ?>
    </dl>
    <!--Begin control panel-->
    <div class="black-box">
        <?php if($this->user->isLoggedIn()): ?>
        <button onclick="window.location.href='/post/create'" style="width:49%"><span style="font-size:18px;letter-spacing:-1px;font-weight:normal;">Create new thread</span></button>
        <button onclick="window.location.href='/image/upload'" style="width:50%"><span style="font-size:18px;letter-spacing:-1px;font-weight:normal;">Upload new image</span></button>
        <?php else: ?>
        <?php endif; ?>
    </div>
    <!--End control panel-->
    <h3 class="dash-head">Active</h3>
<?php } ?>
<div class="threads-loop" style="margin-bottom: 20px">
<?php $this->load->view('common/threads-loop', array('posts'=>$posts)); ?>
</div>
<?php echo $this->pagination->create_links(); ?>
<?php $this->load->view('common/footer'); ?>
