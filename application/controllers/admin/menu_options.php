<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Menu_options extends CI_Controller {

	public function __construct() {
		parent::__construct(); //  calls the constructor
		$this->load->library('user');
		$this->load->library('pagination');
		$this->load->model('Menus_model'); // load the menus model
	}

	public function index() {
		$this->load->library('currency'); // load the currency library
			
		if (!$this->user->islogged()) {  
  			redirect(ADMIN_URI.'/login');
		}

    	if (!$this->user->hasPermissions('access', ADMIN_URI.'/menu_options')) {
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
		
		if ($this->input->get('filter_display_type')) {
			$filter['filter_display_type'] = $data['filter_display_type'] = $this->input->get('filter_display_type');
			$url .= 'filter_display_type='.$filter['filter_display_type'].'&';
		} else {
			$filter['filter_display_type'] = $data['filter_display_type'] = '';
		}
		
		if ($this->input->get('sort_by')) {
			$filter['sort_by'] = $data['sort_by'] = $this->input->get('sort_by');
		} else {
			$filter['sort_by'] = $data['sort_by'] = 'priority';
		}
		
		if ($this->input->get('order_by')) {
			$filter['order_by'] = $data['order_by'] = $this->input->get('order_by');
			$data['order_by_active'] = $this->input->get('order_by') .' active';
		} else {
			$filter['order_by'] = $data['order_by'] = 'ASC';
			$data['order_by_active'] = 'ASC';
		}
		
		$this->template->setTitle('Menu Options');
		$this->template->setHeading('Menu Options');
		$this->template->setButton('+ New', array('class' => 'btn btn-success', 'href' => page_url() .'/edit'));
		$this->template->setButton('Delete', array('class' => 'btn btn-default', 'onclick' => '$(\'#list-form\').submit();'));

		$data['text_empty'] 		= 'There are no menu options, please add!.';

		$order_by = (isset($filter['order_by']) AND $filter['order_by'] == 'ASC') ? 'DESC' : 'ASC';
		$data['sort_name'] 			= site_url(ADMIN_URI.'/menu_options'.$url.'sort_by=option_name&order_by='.$order_by);
		$data['sort_priority'] 		= site_url(ADMIN_URI.'/menu_options'.$url.'sort_by=priority&order_by='.$order_by);
		$data['sort_display_type'] 	= site_url(ADMIN_URI.'/menu_options'.$url.'sort_by=display_type&order_by='.$order_by);
		$data['sort_id'] 			= site_url(ADMIN_URI.'/menu_options'.$url.'sort_by=option_id&order_by='.$order_by);

		$data['menu_options'] = array();
		$results = $this->Menus_model->getOptionsList($filter);
		foreach ($results as $result) {
			$data['menu_options'][] = array(
				'option_id' 	=> $result['option_id'],
				'option_name' 	=> $result['option_name'],
				'priority' 		=> $result['priority'],
				'display_type' 	=> ucwords($result['display_type']),
				'edit' 			=> site_url(ADMIN_URI.'/menu_options/edit?id=' . $result['option_id'])
			);
		}

		if ($this->input->get('sort_by') AND $this->input->get('order_by')) {
			$url .= 'sort_by='.$filter['sort_by'].'&';
			$url .= 'order_by='.$filter['order_by'].'&';
		}
		
		$config['base_url'] 		= site_url(ADMIN_URI.'/menu_options').$url;
		$config['total_rows'] 		= $this->Menus_model->getAdminOptionsListCount($filter);
		$config['per_page'] 		= $filter['limit'];
		
		$this->pagination->initialize($config);

		$data['pagination'] = array(
			'info'		=> $this->pagination->create_infos(),
			'links'		=> $this->pagination->create_links()
		);

		if ($this->input->post('delete') AND $this->_deleteMenuOption() === TRUE) {
			
			redirect(ADMIN_URI.'/menu_options');
		}	

		$this->template->regions(array('header', 'footer'));
		if (file_exists(APPPATH .'views/themes/'.ADMIN_URI.'/'.$this->config->item('admin_theme').'menu_options.php')) {
			$this->template->render('themes/'.ADMIN_URI.'/'.$this->config->item('admin_theme'), 'menu_options', $data);
		} else {
			$this->template->render('themes/'.ADMIN_URI.'/default/', 'menu_options', $data);
		}
	}
	
	public function edit() {
		if (!$this->user->islogged()) {  
  			redirect(ADMIN_URI.'/login');
		}

    	if (!$this->user->hasPermissions('access', ADMIN_URI.'/menu_options')) {
  			redirect(ADMIN_URI.'/permission');
		}
		
		if ($this->session->flashdata('alert')) {
			$data['alert'] = $this->session->flashdata('alert');  // retrieve session flashdata variable if available
		} else { 
			$data['alert'] = '';
		}		
		
		$option_info = $this->Menus_model->getAdminOption((int) $this->input->get('id'));
		
		if ($option_info) {
			$option_id = $option_info['option_id'];
			$data['action']	= site_url(ADMIN_URI.'/menu_options/edit?id='. $option_id);
		} else {
			$option_id = 0;
			$data['action']	= site_url(ADMIN_URI.'/menu_options/edit');
		}
		
		$title = (isset($option_info['option_name'])) ? $option_info['option_name'] : 'New';	
		$this->template->setTitle('Menu Option: '. $title);
		$this->template->setHeading('Menu Option: '. $title);
		$this->template->setButton('Save', array('class' => 'btn btn-success', 'onclick' => '$(\'#edit-form\').submit();'));
		$this->template->setButton('Save & Close', array('class' => 'btn btn-default', 'onclick' => 'saveClose();'));
		$this->template->setBackButton('btn-back', site_url(ADMIN_URI.'/menu_options'));

		$data['option_id'] 			= $option_info['option_id'];
		$data['option_name'] 		= $option_info['option_name'];
		$data['display_type'] 		= $option_info['display_type'];
		$data['priority'] 			= $option_info['priority'];

		if ($this->input->post('values')) {
			$values = $this->input->post('values');
		} else {
			$values = $this->Menus_model->getOptionValues($option_id);
		}
		
		$data['values'] = array();
		foreach ($values as $value) {					
			$data['values'][] = array(
				'option_value_id'	=> $value['option_value_id'],
				'value'				=> $value['value'],
				'price'				=> $value['price']
			);
		}

		if ($this->input->post() AND $this->_addMenuOpiton() === TRUE) {
			if ($this->input->post('save_close') !== '1' AND is_numeric($this->input->post('insert_id'))) {	
				redirect(ADMIN_URI.'/menu_options/edit?id='. $this->input->post('insert_id'));
			} else {
				redirect(ADMIN_URI.'/menu_options');
			}
		}

		if ($this->input->post() AND $this->_updateMenuOption() === TRUE){
			if ($this->input->post('save_close') === '1') {
				redirect(ADMIN_URI.'/menu_options');
			}
			
			redirect(ADMIN_URI.'/menu_options/edit?id='. $option_id);
		}
					
		$this->template->regions(array('header', 'footer'));
		if (file_exists(APPPATH .'views/themes/'.ADMIN_URI.'/'.$this->config->item('admin_theme').'menu_options_edit.php')) {
			$this->template->render('themes/'.ADMIN_URI.'/'.$this->config->item('admin_theme'), 'menu_options_edit', $data);
		} else {
			$this->template->render('themes/'.ADMIN_URI.'/default/', 'menu_options_edit', $data);
		}
	}

	public function autocomplete() {
		$json = array();
		
		if ($this->input->get('term')) {
			$filter = array(
				'option_name' => $this->input->get('term')
			);

			$results = $this->Menus_model->getOptionsAutoComplete($filter);
			if ($results) {
				foreach ($results as $result) {
					$json['results'][] = array(
						'id' 				=> $result['option_id'],
						'text' 				=> utf8_encode($result['option_name']),
						'display_type' 		=> utf8_encode($result['display_type']),
						'priority' 			=> $result['priority'],
						'option_values' 	=> $result['option_values']
					);
				}
			} else {
				$json['results'] = array('id' => '0', 'text' => 'No Matches Found');
			}
		}
		
		$this->output->set_output(json_encode($json));
	}
	
	public function _addMenuOpiton() {
    	if (!$this->user->hasPermissions('modify', ADMIN_URI.'/menu_options')) {
			$this->session->set_flashdata('alert', '<p class="alert-warning">Warning: You do not have permission to add!</p>');
  			return TRUE;
    	} else if ( ! is_numeric($this->input->get('id')) AND $this->validateForm() === TRUE) { 
			$add = array();
	
			$add['option_name'] 	= $this->input->post('option_name');
			$add['display_type'] 	= $this->input->post('display_type');	
			$add['priority'] 		= $this->input->post('priority');	
			$add['option_values'] 	= $this->input->post('values');	
										
			if ($_POST['insert_id'] = $this->Menus_model->addOption($add)) {
				$this->session->set_flashdata('alert', '<p class="alert-success">Menu option added sucessfully.</p>');
			} else {
				$this->session->set_flashdata('alert', '<p class="alert-warning">An error occured, nothing added.</p>');				
			}
			
			return TRUE;
		}
	}

	public function _updateMenuOption() {
    	if (!$this->user->hasPermissions('modify', ADMIN_URI.'/menu_options')) {
			$this->session->set_flashdata('alert', '<p class="alert-warning">Warning: You do not have permission to update!</p>');
  			return TRUE;
    	} else if (is_numeric($this->input->get('id')) AND $this->validateForm() === TRUE) { 
			$update = array();
		
			$update['option_id'] 		= $this->input->get('id');
			$update['option_name'] 		= $this->input->post('option_name');
			$update['display_type'] 	= $this->input->post('display_type');	
			$update['priority'] 		= $this->input->post('priority');	
			$update['option_values'] 	= $this->input->post('values');	

			if ($this->Menus_model->updateOption($update)) {						
				$this->session->set_flashdata('alert', '<p class="alert-success">Menu option updated sucessfully.</p>');
			} else {
				$this->session->set_flashdata('alert', '<p class="alert-warning">An error occured, nothing updated.</p>');				
			}
		
			return TRUE;
		}
	}

	public function _deleteMenuOption() {
    	if (!$this->user->hasPermissions('modify', ADMIN_URI.'/menu_options')) {
			$this->session->set_flashdata('alert', '<p class="alert-warning">Warning: You do not have permission to delete!</p>');
    	} else if (is_array($this->input->post('delete'))) {
			foreach ($this->input->post('delete') as $key => $value) {
				$this->Menus_model->deleteMenuOption($value);
			}			
		
			$this->session->set_flashdata('alert', '<p class="alert-success">Menu option(s) deleted sucessfully!</p>');
		}
				
		return TRUE;
	}
	
	public function validateForm() {
		$this->form_validation->set_rules('option_name', 'Option Name', 'xss_clean|trim|required|min_length[2]|max_length[32]');
		$this->form_validation->set_rules('display_type', 'Display Type', 'xss_clean|trim|required|alpha');
		$this->form_validation->set_rules('priority', 'Priority', 'xss_clean|trim|required|integer');

		if ($this->input->post('values')) {
			foreach ($this->input->post('values') as $key => $value) {
				$this->form_validation->set_rules('values['.$key.'][value]', 'Value', 'xss_clean|trim|required|min_length[2]|max_length[128]');
				$this->form_validation->set_rules('values['.$key.'][price]', 'Price', 'xss_clean|trim|required|numeric');
			}
		}
					
		if ($this->form_validation->run() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

/* End of file menu_options.php */
/* Location: ./application/controllers/admin/menu_options.php */