<?php $this->load->view('common/header'); ?>
<?php echo form_open('direct/compose', array('class'=>'form-awesome form-awesome-wider round-10'), array("pmFollowup" => array_key_exists("in_reply_to", $_GET) ? $_GET["in_reply_to"] : 0)); ?>
    <ul>
        <li>
            <label for="pmRec">Recepient(s): <em>Username1, Username2, etc</em></label>
            <?php echo form_input('pmRec', set_value('pmRec', (!empty($rec) ? $rec : false))); ?>
        </li>
	<li>
            <label for="pmSubject">Subject: <em>optional, but recommended</em></label>
	    <?php echo form_input('pmSubject', set_value('pmSubject', (!empty($re) ? $re : false))); ?>
	</li>
	<li>
            <label for="pmContent">Message:<em>Markdown supported</em></label>
	    <?php echo form_textarea('pmContent', set_value('pmContent')); ?>
	</li>
	<li>
	    <button type="submit"><span>Send off</span></button>
	</li>
    </ul>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>