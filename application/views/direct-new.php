<?php $this->load->view('common/header'); ?>
<?php echo form_open('direct/compose/'.$userID, array('class'=>'form-awesome form-awesome-wider round-10'), array("prevMessage" => $this->uri->segment(4) ? $this->uri->segment(4) : 0)); ?>
    <ul>
	<li><label for="pmSubject">Subject:</label>
	    <?php echo form_input('pmSubject', set_value('pmSubject', (!empty($re) ? $re : false))); ?>
	</li>
	<li><label for="pmContent">Message:</label>
	    <?php echo form_textarea('pmContent', set_value('pmContent')); ?>
	</li>
	<li>
	    <button type="submit"><span>Send off</span></button>
	</li>
    </ul>
<?php echo form_close(); ?>
<?php $this->load->view('common/footer'); ?>