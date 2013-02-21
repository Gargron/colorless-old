<?php $this->load->view('common/header'); ?>
<h3><?php echo ($pm->pmSender !== $this->session->userdata('userID') ? 'From ' .$pm->pmSenderName : 'To ' .$pm->pmReceiverName) .': '.(!empty($pm->pmBlob->subject) ? $pm->pmBlob->subject : "No subject"); ?></h3>
<div id="user-controls">
    <?php echo anchor('direct/compose/'.($pm->pmSender !== $this->session->userdata('userID') ? $pm->pmSenderName . '/'.$pm->pmID : $pm->pmReceiverName), ($pm->pmSender !== $this->session->userdata('userID') ? 'Reply' : 'Retry')); ?>
    <?php echo anchor('direct/inbox', 'Inbox'.($unread > 0 ? ' ('.$unread.')' : false)); ?>
    <?php echo anchor('direct/sent', 'Sent'); ?>
</div>
     <?php $ago = timespan(human_to_unix($pm->pmCreatedAt)); $ago = explode(',', $ago); ?>
<div class="entry-content">
   <?php echo $this->chan->format($pm->pmBlob->body); ?>

   &mdash;
   <span class="pm-controls">
    <?php echo ($pm->pmSender == $this->session->userdata('userID') ? 'you' : anchor('user/'.$pm->pmSenderName, $pm->pmSenderName)); ?>
    sent it to
    <?php echo implode(', ', array_map(function($item) { return anchor('user/'.$item->userName, $item->userName); }, $pm->pmReceivers)); ?>
    <?php echo $ago[0].' ago'; ?>
    </span>
</div>
<?php $this->load->view('common/footer'); ?>