<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Customers extends CI_Controller {

	public function __construct() {
		parent::__construct(); //  calls the constructor
		$this->load->library('user');
		$this->load->library('pagination');
		$this->load->model('Customers_model');
		$this->load->model('Addresses_model');
		$this->load->model('Countries_model');
		$this->load->model('Security_questions_model');
		$this->load->model('Locations_model');
		$this->load->model('Activity_model');
		$this->load->model('Orders_model');
	}

	public function index() {
		if (!$this->user->islogged()) {  
  			redirect(ADMIN_URI.'/login');
		}

    	if (!$this->user->hasPermissions('access', ADMIN_URI.'/customers')) {
  			redirect(ADMIN_URI.'/permission');
		}
		
		if ($this->session->flashdata('alert')) {
			$data['alert'] = $this->session->flashdata('alert');  // retrieve session flashdata variable if available
		} else {
			$data['alert'] = '';
		}

		$url = '?';
		$filter = array();
		if ($this->input->get('page')) {
			$filter['page'] = (int) $this->input->get('page');
		} else {
			$filter['page'] = '';
		}
		
		if ($this->config->item('page_limit')) {
			$filter['limit'] = $this->config->item('page_limit');
		}
				
		if ($this->input->get('filter_search')) {
			$filter['filter_search'] = $data['filter_search'] = $this->input->get('filter_search');
			$url .= 'filter_search='.$filter['filter_search'].'&';
		} else {
			$data['filter_search'] = '';
		}
		
		if ($this->input->get('filter_date')) {
			$filter['filter_date'] = $data['filter_date'] = $this->input->get('filter_date');
			$url .= 'filter_date='.$filter['filter_date'].'&';
		} else {
			$filter['filter_date'] = $data['filter_date'] = '';
		}
		
		if (is_numeric($this->input->get('filter_status'))) {
			$filter['filter_status'] = $data['filter_status'] = $this->input->get('filter_status');
			$url .= 'filter_status='.$filter['filter_status'].'&';
		} else {
			$filter['filter_status'] = $data['filter_status'] = '';
		}
		
		if ($this->input->get('sort_by')) {
			$filter['sort_by'] = $data['sort_by'] = $this->input->get('sort_by');
		} else {
			$filter['sort_by'] = $data['sort_by'] = 'date_added';
		}
		
		if ($this->input->get('order_by')) {
			$filter['order_by'] = $data['order_by'] = $this->input->get('order_by');
			$data['order_by_active'] = $this->input->get('order_by') .' active';
		} else {
			$filter['order_by'] = $data['order_by'] = 'DESC';
			$data['order_by_active'] = 'DESC';
		}
		
		$this->template->setTitle('Customers');
		$this->template->setHeading('Customers');
		$this->template->setButton('+ New', array('class' => 'btn btn-success', 'href' => page_url() .'/edit'));
		$this->template->setButton('Delete', array('class' => 'btn btn-default', 'onclick' => '$(\'#list-form\').submit();'));

		$data['text_empty'] 		= 'There are no customers available.';

		$order_by = (isset($filter['order_by']) AND $filter['order_by'] == 'ASC') ? 'DESC' : 'ASC';
		$data['sort_first'] 		= site_url(ADMIN_URI.'/customers'.$url.'sort_by=first_name&order_by='.$order_by);
		$data['sort_last'] 			= site_url(ADMIN_URI.'/customers'.$url.'sort_by=last_name&order_by='.$order_by);
		$data['sort_email'] 		= site_url(ADMIN_URI.'/customers'.$url.'sort_by=email&order_by='.$order_by);
		$data['sort_date'] 			= site_url(ADMIN_URI.'/customers'.$url.'sort_by=date_added&order_by='.$order_by);
		$data['sort_id'] 			= site_url(ADMIN_URI.'/customers'.$url.'sort_by=customer_id&order_by='.$order_by);

		$data['customers'] = array();
		$results = $this->Customers_model->getList($filter);
		foreach ($results as $result) {
			
			$data['customers'][] = array(
				'customer_id' 		=> $result['customer_id'],
				'first_name' 		=> $result['first_name'],
				'last_name'			=> $result['last_name'],
				'email' 			=> $result['email'],
				'telephone' 		=> $result['telephone'],
				'date_added' 		=> mdate('%d %M %y', strtotime($result['date_added'])),
				'status' 			=> ($result['status'] === '1') ? 'Enabled' : 'Disabled',
				'edit' 				=> site_url(ADMIN_URI.'/customers/edit?id=' . $result['customer_id'])
			);
		}

		$data['questions'] = array();
		$results = $this->Security_questions_model->getQuestions();
		foreach ($results as $result) {
			$data['questions'][] = array(
				'id'	=> $result['question_id'],
				'text'	=> $result['text']
			);
		}

		$data['country_id'] = $this->config->item('country_id');
		$data['countries'] = array();
		$results = $this->Countries_model->getCountries(); 										// retrieve countries array from getCountries method in locations model
		foreach ($results as $result) {															// loop through crountries array
			$data['countries'][] = array( 														// create array of countries data to pass to view
				'country_id'	=>	$result['country_id'],
				'name'			=>	$result['country_name'],
			);
		}

		$data['customer_dates'] = array();
		$customer_dates = $this->Customers_model->getCustomerDates();
		foreach ($customer_dates as $customer_date) {
			$month_year = '';
			$month_year = $customer_date['year'].'-'.$customer_date['month'];
			$data['customer_dates'][$month_year] = mdate('%F %Y', strtotime($customer_date['date_added']));
		}

		if ($this->input->get('sort_by') AND $this->input->get('order_by')) {
			$url .= 'sort_by='.$filter['sort_by'].'&';
			$url .= 'order_by='.$filter['order_by'].'&';
		}
		
		$config['base_url'] 		= site_url(ADMIN_URI.'/customers').$url;
		$config['total_rows'] 		= $this->Customers_model->getAdminListCount($filter);
		$config['per_page'] 		= $filter['limit'];
		
		$this->pagination->initialize($config);
		
		$data['pagination'] = array(
			'info'		=> $this->pagination->create_infos(),
			'links'		=> $this->pagination->create_links()
		);

		if ($this->input->post('delete') AND $this->_deleteCustomer() === TRUE) {
			
			redirect(ADMIN_URI.'/customers');  			
		}	

		$this->template->regions(array('header', 'footer'));
		if (file_exists(APPPATH .'views/themes/'.ADMIN_URI.'/'.$this->config->item('admin_theme').'customers.php')) {
			$this->template->render('themes/'.ADMIN_URI.'/'.$this->config->item('admin_theme'), 'customers', $data);
		} else {
			$this->template->render('themes/'.ADMIN_URI.'/default/', 'customers', $data);
		}
	}
	
	public function edit() {
		if (!$this->user->islogged()) {  
  			redirect(ADMIN_URI.'/login');
		}

    	if (!$this->user->hasPermissions('access', ADMIN_URI.'/customers')) {
  			redirect(ADMIN_URI.'/permission');
		}
		
		$customer_info = $this->Customers_model->getCustomer((int)$this->input->get('id'));
		
		if ($customer_info) {
		    $customer_id = $customer_info['customer_id'];
			$data['action']	= site_url(ADMIN_URI.'/customers/edit?id='. $customer_id);
		} else {
		    $customer_id = 0;
			$data['action']	= site_url(ADMIN_URI.'/customers/edit');
		}

		if ($this->session->flashdata('alert')) {
			$data['alert'] = $this->session->flashdata('alert');  // retrieve session flashdata variable if available
		} else {
			$data['alert'] = '';
		}

		$orders_filter = array();
		$orders_filter['customer_id'] = $customer_id;

		$title = (isset($customer_info['first_name']) AND isset($customer_info['last_name'])) ? $customer_info['first_name'] .' '. $customer_info['last_name'] : 'New';	
		$this->template->setTitle('Customer: '. $title);
		$this->template->setHeading('Customer: '. $title);
		$this->template->setButton('Save', array('class' => 'btn btn-success', 'onclick' => '$(\'#edit-form\').submit();'));
		$this->template->setButton('Save & Close', array('class' => 'btn btn-default', 'onclick' => 'saveClose();'));
		$this->template->setBackButton('btn-back', site_url(ADMIN_URI.'/customers'));

		$data['text_empty'] 		= 'There are no order available for this customer.';
		$data['text_empty_activity'] = 'This customer has no recent activity.';
	
		$data['first_name'] 		= $customer_info['first_name'];
		$data['last_name'] 			= $customer_info['last_name'];
		$data['email'] 				= $customer_info['email'];
		$data['telephone'] 			= $customer_info['telephone'];
		$data['security_question'] 	= $customer_info['security_question_id'];
		$data['security_answer'] 	= $customer_info['security_answer'];
		$data['newsletter'] 		= $customer_info['newsletter'];
		$data['customer_group_id'] 	= (!empty($customer_info['customer_group_id'])) ? $customer_info['customer_group_id'] : $this->config->item('customer_group_id');
		$data['status'] 			= $customer_info['status'];
		
		if ($this->input->post('address')) {
			$data['addresses'] 			= $this->input->post('address');
		} else {
			$data['addresses'] 			= $this->Addresses_model->getCustomerAddresses($customer_id);
		}
		
		$data['questions'] = array();
		$results = $this->Security_questions_model->getQuestions();
		foreach ($results as $result) {
			$data['questions'][] = array(
				'id'	=> $result['question_id'],
				'text'	=> $result['text']
			);
		}

		$this->load->model('Customer_groups_model');
		$data['customer_groups'] = array();
		$results = $this->Customer_groups_model->getCustomerGroups();
		foreach ($results as $result) {					
			$data['customer_groups'][] = array(
				'customer_group_id'	=>	$result['customer_group_id'],
				'group_name'		=>	$result['group_name']
			);
		}

		$data['country_id'] = $this->config->item('country_id');
		$data['countries'] = array();
		$results = $this->Countries_model->getCountries(); 										// retrieve countries array from getCountries method in locations model
		foreach ($results as $result) {															// loop through crountries array
			$data['countries'][] = array( 														// create array of countries data to pass to view
				'country_id'	=>	$result['country_id'],
				'name'			=>	$result['country_name'],
			);
		}

		if ($this->input->get('page')) {
			$orders_filter['page'] = (int) $this->input->get('page');
		} else {
			$orders_filter['page'] = 1;
		}
		
		if ($this->config->item('page_limit')) {
			$orders_filter['limit'] = $this->config->item('page_limit');
		} else {
			$orders_filter['limit'] = '';
		}
				
		$data['orders'] = array();
		$results = $this->Orders_model->getCustomerOrders($orders_filter);
		foreach ($results as $result) {					
			$current_date = mdate('%d-%m-%Y', time());
			$date_added = mdate('%d-%m-%Y', strtotime($result['date_added']));
			
			if ($current_date === $date_added) {
				$date_added = 'Today';
			} else {
				$date_added = mdate('%d %M %y', strtotime($date_added));
			}
			
			$data['orders'][] = array(
				'order_id'			=> $result['order_id'],
				'location_name'		=> $result['location_name'],
				'first_name'		=> $result['first_name'],
				'last_name'			=> $result['last_name'],
				'order_type' 		=> ($result['order_type'] === '1') ? 'Delivery' : 'Collection',
				'order_time'		=> mdate('%H:%i', strtotime($result['order_time'])),
				'order_status'		=> $result['status_name'],
				'date_added'		=> $date_added,
				'edit' 				=> site_url(ADMIN_URI.'/orders/edit?id=' . $result['order_id'])
			);
		}
			
		$config['base_url'] 		= site_url(ADMIN_URI.'/customers/edit'.'?id='. $customer_id .'&');
		$config['total_rows'] 		= $this->Orders_model->getAdminCustomerListCount($orders_filter);
		$config['per_page'] 		= $orders_filter['limit'];
		
		$this->pagination->initialize($config);
				
		$data['pagination'] = array(
			'info'		=> $this->pagination->create_infos(),
			'links'		=> $this->pagination->create_links()
		);

		$activities = $this->Activity_model->getCustomerActivities($customer_id);
		$data['activities'] = array();
		foreach ($activities as $activity) {
			$data['activities'][] = array(
				'activity_id'		=> $activity['activity_id'],
				'ip_address' 		=> $activity['ip_address'],
				'customer_name'		=> ($activity['customer_id']) ? $activity['first_name'] .' '. $activity['last_name'] : 'Guest',
				'access_type'		=> ucwords($activity['access_type']),
				'browser'			=> $activity['browser'],
				'request_uri'		=> (!empty($activity['request_uri'])) ? $activity['request_uri'] : '--',
				'referrer_uri'		=> (!empty($activity['referrer_uri'])) ? $activity['referrer_uri'] : '--',
				'date_added'		=> mdate('%d %M %y - %H:%i', strtotime($activity['date_added'])),
				'blacklist' 		=> site_url(ADMIN_URI.'/customers_activity/blacklist?ip=' . $activity['ip_address'])
			);
		}
			
		if ($this->input->post() AND $this->_addCustomer() === TRUE) {
			if ($this->input->post('save_close') !== '1' AND is_numeric($this->input->post('insert_id'))) {	
				redirect(ADMIN_URI.'/customers/edit?id='. $this->input->post('insert_id'));
			} else {
				redirect(ADMIN_URI.'/customers');
			}
		}

		if ($this->input->post() AND $this->_updateCustomer($data['email']) === TRUE) {
			if ($this->input->post('save_close') === '1') {
				redirect(ADMIN_URI.'/customers');
			}
			
			redirect(ADMIN_URI.'/customers/edit?id='. $customer_id);
		}
		
		$this->template->regions(array('header', 'footer'));
		if (file_exists(APPPATH .'views/themes/'.ADMIN_URI.'/'.$this->config->item('admin_theme').'customers_edit.php')) {
			$this->template->render('themes/'.ADMIN_URI.'/'.$this->config->item('admin_theme'), 'customers_edit', $data);
		} else {
			$this->template->render('themes/'.ADMIN_URI.'/default/', 'customers_edit', $data);
		}
	}

	public function autocomplete() {
		$json = array();
		
		if ($this->input->get('term') OR $this->input->get('customer_id')) {
			$filter['customer_name'] = $this->input->get('term');
			$filter['customer_id'] = $this->input->get('customer_id');

			$results = $this->Customers_model->getAutoComplete($filter);
		
			if ($results) {
				foreach ($results as $result) {
					$json['results'][] = array(
						'id' 	=> $result['customer_id'],
						'text' 	=> utf8_encode($result['first_name'] .' '. $result['last_name'])
					);
				}
			} else {
				$json['results'] = array('id' => '0', 'text' => 'No Matches Found');
			}
		}
		
		$this->output->set_output(json_encode($json));
	}
	
	public function _addCustomer() {
    	if ( ! $this->user->hasPermissions('modify', ADMIN_URI.'/customers')) {
			$this->session->set_flashdata('alert', '<p class="alert-warning">Warning: You do not have permission to add!</p>');
  			return TRUE;
    	} else if ( ! is_numeric($this->input->get('id')) AND $this->validateForm() === TRUE) {
			$add = array();
			
			$add['first_name'] 				= $this->input->post('first_name');
			$add['last_name'] 				= $this->input->post('last_name');
			$add['email'] 					= $this->input->post('email');
			$add['password'] 				= $this->input->post('password');
			$add['telephone'] 				= $this->input->post('telephone');
			$add['security_question_id'] 	= $this->input->post('security_question');
			$add['security_answer'] 		= $this->input->post('security_answer');
			$add['newsletter'] 				= $this->input->post('newsletter');
			$add['customer_group_id'] 		= $this->input->post('customer_group_id');
			$add['date_added'] 				= mdate('%Y-%m-%d', time());
			$add['status']					= $this->input->post('status');
			$add['address']					= $this->input->post('address');

			if ($_POST['insert_id'] = $this->Customers_model->addCustomer($add)) {	
				$this->session->set_flashdata('alert', '<p class="alert-success">Customer added sucessfully.</p>');
			} else {
				$this->session->set_flashdata('alert', '<p class="alert-warning">An error occured, nothing added.</p>');
			}
			
			return TRUE;		
		}
	}

	public function _updateCustomer($customer_email) {
    	if ( ! $this->user->hasPermissions('modify', ADMIN_URI.'/customers')) {
			$this->session->set_flashdata('alert', '<p class="alert-warning">Warning: You do not have permission to update!</p>');
  			return TRUE;
    	} else if (is_numeric($this->input->get('id')) AND $this->validateForm($customer_email) === TRUE) {
			$update = array();
			
			$update['customer_id'] 			= $this->input->get('id');
			$update['first_name'] 			= $this->input->post('first_name');
			$update['last_name'] 			= $this->input->post('last_name');
			$update['telephone'] 			= $this->input->post('telephone');
			$update['security_question_id'] = $this->input->post('security_question');
			$update['security_answer']		= $this->input->post('security_answer');
			$update['newsletter'] 			= $this->input->post('newsletter');
			$update['customer_group_id'] 	= $this->input->post('customer_group_id');
			$update['status']				= $this->input->post('status');
			$update['address']				= $this->input->post('address');
			
			if ($customer_email !== $this->input->post('email')) {
				$update['email']	= $this->input->post('email');
			}

			if ($this->input->post('password')) {
				$update['password'] = $this->input->post('password');
			}

			if ($this->Customers_model->updateCustomer($update)) {
				$this->session->set_flashdata('alert', '<p class="alert-success">Customer updated sucessfully.</p>');
			} else {
				$this->session->set_flashdata('alert', '<p class="alert-warning">An error occured, nothing updated.</p>');	
			}
			
			return TRUE;
		}
	}

	public function _deleteCustomer($customer_id = FALSE) {
    	if (!$this->user->hasPermissions('modify', ADMIN_URI.'/menus')) {
			$this->session->set_flashdata('alert', '<p class="alert-warning">Warning: You do not have permission to delete!</p>');
    	} else if (is_array($this->input->post('delete'))) {
			foreach ($this->input->post('delete') as $key => $value) {
				$this->Customers_model->deleteCustomer($value);
			}			
		
			$this->session->set_flashdata('alert', '<p class="alert-success">Customer(s) deleted sucessfully!</p>');
		}
				
		return TRUE;
	}
	
	public function validateForm($customer_email = FALSE) {
		$this->form_validation->set_rules('first_name', 'First Name', 'xss_clean|trim|required|min_length[2]|max_length[12]');
		$this->form_validation->set_rules('last_name', 'First Name', 'xss_clean|trim|required|min_length[2]|max_length[12]');

		if ($customer_email !== $this->input->post('email')) {
			$this->form_validation->set_rules('email', 'Email', 'xss_clean|trim|required|valid_email|max_length[96]|is_unique[customers.email]');
		}

		if ($this->input->post('password')) {
			$this->form_validation->set_rules('password', 'Password', 'xss_clean|trim|required|min_length[6]|max_length[32]|matches[confirm_password]');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'xss_clean|trim|required|md5');
		}
				
		if (!$this->input->get('id')) {
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required');
		}

		$this->form_validation->set_rules('telephone', 'Telephone', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('security_question', 'Security Question', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('security_answer', 'Security Answer', 'xss_clean|trim|required|min_length[2]');
		$this->form_validation->set_rules('newsletter', 'Newsletter', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('customer_group_id', 'Customer Group', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('status', 'Status', 'xss_clean|trim|required|integer');

		if ($this->input->post('address')) {
			foreach ($this->input->post('address') as $key => $value) {
				$this->form_validation->set_rules('address['.$key.'][address_1]', '['.$key.'] Address 1', 'xss_clean|trim|required|min_length[3]|max_length[128]');
				$this->form_validation->set_rules('address['.$key.'][city]', '['.$key.'] City', 'xss_clean|trim|required|min_length[2]|max_length[128]');
				$this->form_validation->set_rules('address['.$key.'][postcode]', '['.$key.'] Postcode', 'xss_clean|trim|required|min_length[2]|max_length[10]');
				$this->form_validation->set_rules('address['.$key.'][country]', '['.$key.'] Country', 'xss_clean|trim|required|integer');
			}
		}

		if ($this->form_validation->run() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}		
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/customers.php */