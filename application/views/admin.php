<?php $this->load->view('common/header'); ?>

<div style="width: 650px; height: 300px; margin-bottom: 10px; background: url('/images/cl_banner.jpg') no-repeat top left; border: 1px solid #666; -webkit-border-radius: 10px; -moz-border-radius: 10px; border-radius: 10px;">
    <div style="padding: 20px; font-size: 24px; line-height: 28px; width: 300px; text-shadow: #000 1px 1px 1px;">
        <h2>Good morning, moderator-sama. What would you like to do today? ;)</h2>

        <button onclick="window.location.href='/admin/statistics'" id="big-button" style="width:100%;margin-bottom:5px; margin-top: 5px;"><span style="font-size:18px;letter-spacing:-1px;font-weight:normal;">View Statistics</span></button>
        <button onclick="window.location.href='/admin/flags'" id="big-button" style="width:100%;margin-bottom:5px"><span style="font-size:18px;letter-spacing:-1px;font-weight:normal;">View Flagged Threads</span></button>
        <button onclick="window.location.href='/admin/actions'" id="big-button" style="width:100%;margin-bottom:5px"><span style="font-size:18px;letter-spacing:-1px;font-weight:normal;">View Moderator Actions</span></button>

    </div>
</div>

<?php $this->load->view('common/footer'); ?>
