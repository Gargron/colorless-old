<?php

/**
 *
 */

class Privatum extends Model {

    function Privatum() {
	parent::Model();
    }

    function sendPM($senderID, $receiverID, $blob, $prev) {
	//Message
	$this->db->set('pmBlob', json_encode($blob));
	$this->db->set('pmPrevious', $prev);
	$this->db->set('pmCreatorID', $senderID);
	$this->db->insert('pms');

	$new_id = $this->db->insert_id();
	//Sender
	$this->db->set('boxOwnerID', $senderID);
	$this->db->set('boxModel', 'sender');
	$this->db->set('boxStatus', 'read');
	$this->db->set('boxPMID', $new_id);
	$this->db->insert('pms_box');
	//Receiver
	if(is_integer($receiverID)) {
	    $this->db->set('boxOwnerID', $receiverID);
	    $this->db->set('boxModel', 'receiver');
	    $this->db->set('boxStatus', 'unread');
	    $this->db->set('boxPMID', $new_id);
	    $this->db->insert('pms_box');
	} else if(is_array($receiverID)) {
	    $receive = array();
	    foreach($receiverID as $rid) {
		$this->db->insert('pms_box', array(
		    'boxOwnerID' => $rid,
		    'boxModel' => 'receiver',
		    'boxStatus' => 'unread',
		    'boxPMID' => $new_id
		));
		/*$receive[] = array(
		    'boxOwnerID' => $rid,
		    'boxModel' => 'receiver',
		    'boxStatus' => 'unread',
		    'boxPMID' => $new_id
		);*/
	    }
	    //$this->db->insert_batch('pms_box', $receive);
	}

	return $new_id;
    }

    function getPMCount($userID, $model) {
	$this->db->select('count(*) as cnt');
	$this->db->where(array('boxOwnerID' => $userID, 'boxModel' => $model));
	$query = $this->db->get('pms_box');

	return $query->row()->cnt;
    }

    function getPM($options = array()) {
	if(isset($options['pmSender']) || isset($options['pmReceiver'])) {
	    if(isset($options['pmSender'])) {
		$this->db->select('box1.boxOwnerID as pmSender, pms.pmID, pms.pmBlob, pms.pmCreatedAt, box2.boxOwnerID as pmReceiver, box2.boxStatus as pmStatus, users.userName as pmReceiverName, users.userEmail as pmReceiverEmail');
		$this->db->where('box1.boxOwnerID', $options['pmSender']);
		$this->db->where('box1.boxModel', 'sender');
		$this->db->where('box2.boxModel', 'receiver');
	    }
	    elseif(isset($options['pmReceiver'])) {
		$this->db->select('box1.boxOwnerID as pmReceiver, box1.boxStatus as pmStatus, pms.pmID, pms.pmBlob, pms.pmCreatedAt, box2.boxOwnerID as pmSender, users.userName as pmSenderName, users.userEmail as pmSenderEmail');

		if(isset($options['pmID'])) {
		    $this->db->where('pms.pmID', $options['pmID']);
		} else {
		    $this->db->where('box1.boxOwnerID', $options['pmReceiver']);
		}

		$this->db->where('box1.boxModel', 'receiver');
		$this->db->where('box2.boxModel', 'sender');
	    }

	    $this->db->join('pms_box as box2', 'box1.boxPMID=box2.boxPMID', 'left');
	    $this->db->where('box2.boxPMID', 'box1.boxPMID', FALSE);

	    $this->db->from('pms_box as box1');

	    $this->db->join('pms', 'box1.boxPMID = pms.pmID', 'left');
	    $this->db->join('users', 'users.userID=box2.boxOwnerID', 'left outer');
	} elseif(isset($options['pmID'])) {
	    $this->db->select('sebox.*, pms.*, sebox.boxOwnerID as pmSender, se.userID, se.userName as pmSenderName, se.userEmail as pmSenderEmail');
	    $this->db->where('pms.pmID', $options['pmID']);
	    $this->db->where('sebox.boxModel', 'sender');
	    $this->db->join('pms_box as sebox', 'sebox.boxPMID = pms.pmID', 'left outer');
	    $this->db->join('users as se', 'se.userID=sebox.boxOwnerID', 'left outer');
	    $this->db->from('pms');
	}

	    if(isset($options['limit']) && isset($options['offset']))
		    $this->db->limit($options['limit'], $options['offset']);
	    elseif(isset($options['limit']))
		    $this->db->limit($options['limit']);

	    if(isset($options['sortBy']) && isset($options['sortDirection']))
		    $this->db->order_by($options['sortBy'], $options['sortDirection']);

	    $query = $this->db->get();

	    if(isset($options['pmID'])) {

		$pm = $query->row();
		$this->db->select('pms_box.*, users.userName, users.userEmail');
		$this->db->where(array('boxPMID' => $options['pmID'], 'boxModel' => 'receiver'));
		$this->db->join('users', 'users.userID = pms_box.boxOwnerID');
		$recs = $this->db->get('pms_box')->result();

		$pm->pmReceivers = $recs;

		return $pm;
	    }

	    return $query->result();
    }

    function numUnreadPM($userID) {
	$this->db->select('count(boxID) as unread');
	$this->db->where('boxStatus', 'unread');
	$this->db->where('boxOwnerID', $userID);
	$this->db->where('boxModel', 'receiver');

	$query = $this->db->get('pms_box');
	return $query->row()->unread;
    }

    function sendInboxNotification($reName, $reEmail, $seName, $subject, $content, $new_id) {
	$newPMmessage = <<<EOT
Hey $reName,

you just received a new private message on the Colorless. It goes like this:

{$this->format_pm_quote($content)}

View it and reply to it here: http://thecolorless.net/direct/view/$new_id?ref=email

========================================

This e-mail was intended for $reEmail.
You can manage your notification settings here: http://thecolorless.net/settings?ref=email

========================================

E-mail notifications kindly sponsored by J-List: http://moe.jlist.com/click/3341

--
With utter love from everywhere and nowhere,
The Colorless' Herald
EOT;

	if($this->user->forceWant($reName, 'settingReceivePMs')) {
	    $this->load->library('postmark');
	    $this->postmark->to($reEmail, $reName);
	    $this->postmark->subject($seName.' sent you a new private message on the Colorless');
	    $this->postmark->message_plain($newPMmessage);
	    $this->postmark->send();
	    return true;
	} else {
	    return false;
	}
    }

    function format_pm_quote($s) {
	$s = wordwrap($s, 70);
	$s = explode("\n", $s);
	return '>'.implode("\n>", $s);
    }

    function markPM($userID, $pmID, $status) {
	$this->db->set('boxStatus', $status);

	$this->db->where('boxOwnerID', $userID);
	$this->db->where('boxPMID', $pmID);

	$this->db->update('pms_box');

	return $this->db->affected_rows();
    }

}
?>