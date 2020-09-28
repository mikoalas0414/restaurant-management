<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Account_module extends MX_Controller {

	public function __construct() {
		parent::__construct(); 																	// calls the constructor
		$this->load->library('customer');  														// loads language file
		$this->load->library('language');
		$this->lang->load('account_module/account_module', $this->language->folder());
	}

	public function index() {
		if ( ! file_exists(EXTPATH .'modules/account_module/views/main/account_module.php')) { 								//check if file exists in views folder
			show_404(); 																		// Whoops, show 404 error page!
		}
			
		if ($this->session->flashdata('alert')) {
			$data['alert'] = $this->session->flashdata('alert');  								// retrieve session flashdata variable if available
		} else {
			$data['alert'] = '';
		}

		if ($this->uri->segment(2)) {
			$data['page'] = $this->uri->segment(2, FALSE); 	
		} else {
			$data['page'] = 'account';			
		}

		$this->load->model('Messages_model');													// load the customers model
		$inbox_total = $this->Messages_model->getMainInboxTotal($this->customer->getId());					// retrieve total number of customer messages from getMainInboxTotal method in Messages model

		// START of retrieving lines from language file to pass to view.
		$data['text_account'] 			= $this->lang->line('text_account');
		$data['text_edit_details'] 		= $this->lang->line('text_edit_details');
		$data['text_address'] 			= $this->lang->line('text_address');
		$data['text_orders'] 			= $this->lang->line('text_orders');
		$data['text_reservations'] 		= $this->lang->line('text_reservations');
		$data['text_reviews'] 			= $this->lang->line('text_reviews');
		$data['text_inbox'] 			= sprintf($this->lang->line('text_inbox'), $inbox_total);
		$data['text_logout'] 			= $this->lang->line('text_logout');

		// END of retrieving lines from language file to send to view.
		
		// pass array $data and load view files
		$this->load->view('account_module/main/account_module', $data);
	}		
}

/* End of file account_module.php */
/* Location: ./application/extensions/modules/account_module/controllers/main/account_module.php */