<?php
class Members extends Controller {

        function __construct() {
            parent::Controller();
	    $this->load->library('pagination');
        }

	function index() {
            redirect('members/recent');
        }

	function recent() {

	    $config['base_url'] = base_url() . '/members/recent/offset/';
	    $config['total_rows'] = $this->user->numActiveUsers();
	    $config['per_page'] = '50';
	    $config['uri_segment'] = '4';
	    $config['full_tag_open'] = '<div class="pagination">';
	    $config['full_tag_close'] = '</div>';

	    $this->pagination->initialize($config);

	    $data['page_title'] = "Latest members";
	    if($this->uri->segment(4) && $this->uri->segment(4) !== 0)
		$data['page_number'] = ceil($this->uri->segment(4) / 50)+1;

	    $data['members'] = $this->user->getUsers(array('sortBy'=>'userID', 'sortDirection'=>'desc', 'offset'=>$this->uri->segment(4), 'limit'=>'50', 'userStatus'=>'active'));
	    $this->load->view('members-list', $data);
	}

	function active() {
		show_error("This page no longer exists", 410);
	}

}
?>