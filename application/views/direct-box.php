<?php $this->load->view('common/header'); ?>
<div id="user-controls">
    <?php echo anchor('direct/compose', 'New'); ?>
    <?php echo anchor('direct/inbox', 'Inbox'.($unread > 0 ? ' ('.$unread.')' : false)); ?>
    <?php echo anchor('direct/sent', 'Sent'); ?>
</div>
<ul id="timeline-snippet">
    <?php if(!empty($pms)): foreach($pms as $pm) { $ago = timespan(human_to_unix($pm->pmCreatedAt)); $ago = explode(',', $ago); ?>
    <li class="<?php echo $pm->pmStatus; ?>"><img src="http://gravatar.com/avatar/<?php echo md5(strtolower(($pm->pmSender !== $this->session->userdata('userID') ? $pm->pmSenderEmail : $pm->pmReceiverEmail))); ?>?s=16&amp;d=http%3A%2F%2Fthecolorless.net%2Fimages%2Favatars%2Fblack.png" width="16" height="16" class="avatar left" /><div class="c"><?php $pmBlob = json_decode($pm->pmBlob); echo '<span class="mel-title">'.anchor('direct/view/'.$pm->pmID, (!empty($pmBlob->subject) ? $pmBlob->subject : 'No subject')).'</span> '; ?><span class="pm-controls"><?php echo ($pm->pmSender == $this->session->userdata('userID') ? 'you' : anchor('user/'.$pm->pmSenderName, $pm->pmSenderName)); ?> sent it to <?php echo ($pm->pmReceiver == $this->session->userdata('userID') ? 'you' : anchor('user/'.$pm->pmReceiverName, $pm->pmReceiverName)); ?> <?php echo $ago[0]; ?> ago</span></div></li>
    <?php } else: ?>
    <li>No messages [yet]</li>
    <?php endif; ?>
</ul><!--#timeline-snippet-->
<?php echo $this->pagination->create_links(); ?>
<?php $this->load->view('common/footer'); ?>