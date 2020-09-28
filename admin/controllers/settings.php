<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Settings extends Admin_Controller {

	public function __construct() {
		parent::__construct(); //  calls the constructor
		$this->load->library('user');
		$this->load->model('Locations_model');
		$this->load->model('Settings_model');
		$this->load->model('Countries_model');
		$this->load->model('Currencies_model');
		$this->load->model('Statuses_model');
		$this->load->model('Menus_model');
	}

	public function index() {
		if (!$this->user->islogged()) {
  			redirect('login');
		}

    	if (!$this->user->hasPermissions('access', 'settings')) {
  			redirect('permission');
		}

		$this->template->setTitle('Settings');
		$this->template->setHeading('Settings');
		$this->template->setButton('Save', array('class' => 'btn btn-primary', 'onclick' => '$(\'#edit-form\').submit();'));

		if ($this->input->post('site_name')) {
			$data['site_name'] = $this->input->post('site_name');
		} else {
			$data['site_name'] = $this->config->item('site_name');
		}

		if ($this->input->post('site_email')) {
			$data['site_email'] = $this->input->post('site_email');
		} else {
			$data['site_email'] = $this->config->item('site_email');
		}

		if ($this->input->post('meta_description')) {
			$data['meta_description'] = $this->input->post('meta_description');
		} else {
			$data['meta_description'] = $this->config->item('meta_description');
		}

		if ($this->input->post('meta_keywords')) {
			$data['meta_keywords'] = $this->input->post('meta_keywords');
		} else {
			$data['meta_keywords'] = $this->config->item('meta_keywords');
		}

		$this->load->model('Image_tool_model');
		$data['no_photo'] = $this->Image_tool_model->resize('data/no_photo.png');

		if ($this->input->post('site_logo')) {
			$data['site_logo'] = $this->Image_tool_model->resize($this->input->post('site_logo'));
			$data['logo_name'] = basename($this->input->post('site_logo'));
			$data['logo_val'] = $this->input->post('site_logo');
		} else if ($this->config->item('site_logo')) {
			$data['site_logo'] = $this->Image_tool_model->resize($this->config->item('site_logo'));
			$data['logo_name'] = basename($this->config->item('site_logo'));
			$data['logo_val'] = $this->config->item('site_logo');
		} else {
			$data['site_logo'] = $this->Image_tool_model->resize('data/no_photo.png');
			$data['logo_name'] = 'no_photo.png';
			$data['logo_val'] = 'data/no_photo.png';
		}

		if ($this->input->post('country_id')) {
			$data['country_id'] = $this->input->post('country_id');
		} else {
			$data['country_id'] = $this->config->item('country_id');
		}

		$data['current_time'] = mdate('%d-%m-%Y %H:%i:%s', time());
		if ($this->input->post('timezone')) {
			$data['timezone'] = $this->input->post('timezone');
		} else {
			$data['timezone'] = $this->config->item('timezone');
		}

		if ($this->input->post('currency_id')) {
			$data['currency_id'] = $this->input->post('currency_id');
		} else {
			$data['currency_id'] = $this->config->item('currency_id');
		}

		if ($this->input->post('language_id')) {
			$data['language_id'] = $this->input->post('language_id');
		} else {
			$data['language_id'] = $this->config->item('language_id');
		}

		if ($this->input->post('customer_group_id')) {
			$data['customer_group_id'] = $this->input->post('customer_group_id');
		} else {
			$data['customer_group_id'] = $this->config->item('customer_group_id');
		}

		if ($this->input->post('page_limit')) {
			$data['page_limit'] = $this->input->post('page_limit');
		} else {
			$data['page_limit'] = $this->config->item('page_limit');
		}

		if ($this->input->post('menus_page_limit')) {
			$data['menus_page_limit'] = $this->input->post('menus_page_limit');
		} else {
			$data['menus_page_limit'] = $this->config->item('menus_page_limit');
		}

		if ($this->input->post('show_menu_images')) {
			$data['show_menu_images'] = $this->input->post('show_menu_images');
		} else {
			$data['show_menu_images'] = $this->config->item('show_menu_images');
		}

		if ($this->input->post('menu_images_h')) {
			$data['menu_images_h'] = $this->input->post('menu_images_h');
		} else {
			$data['menu_images_h'] = $this->config->item('menu_images_h');
		}

		if ($this->input->post('menu_images_w')) {
			$data['menu_images_w'] = $this->input->post('menu_images_w');
		} else {
			$data['menu_images_w'] = $this->config->item('menu_images_w');
		}

		if ($this->input->post('special_category_id')) {
			$data['special_category_id'] = $this->input->post('special_category_id');
		} else {
			$data['special_category_id'] = $this->config->item('special_category_id');
		}

		if ($this->input->post('registration_terms')) {
			$data['registration_terms'] = $this->input->post('registration_terms');
		} else {
			$data['registration_terms'] = $this->config->item('registration_terms');
		}

		if ($this->input->post('checkout_terms')) {
			$data['checkout_terms'] = $this->input->post('checkout_terms');
		} else {
			$data['checkout_terms'] = $this->config->item('checkout_terms');
		}

		if ($this->input->post('stock_warning')) {
			$data['stock_warning'] = $this->input->post('stock_warning');
		} else {
			$data['stock_warning'] = $this->config->item('stock_warning');
		}

		if ($this->input->post('stock_qty_warning')) {
			$data['stock_qty_warning'] = $this->input->post('stock_qty_warning');
		} else {
			$data['stock_qty_warning'] = $this->config->item('stock_qty_warning');
		}

		if ($this->input->post('registration_email')) {
			$data['registration_email'] = $this->input->post('registration_email');
		} else {
			$data['registration_email'] = $this->config->item('registration_email');
		}

		if ($this->input->post('customer_order_email')) {
			$data['customer_order_email'] = $this->input->post('customer_order_email');
		} else {
			$data['customer_order_email'] = $this->config->item('customer_order_email');
		}

		if ($this->input->post('customer_reserve_email')) {
			$data['customer_reserve_email'] = $this->input->post('customer_reserve_email');
		} else {
			$data['customer_reserve_email'] = $this->config->item('customer_reserve_email');
		}

		if ($this->input->post('main_address') AND is_array($this->input->post('main_address'))) {
			$main_address = $this->input->post('main_address');
		} else if (is_array($this->config->item('main_address'))) {
			$main_address = $this->config->item('main_address');
		} else {
			$main_address = array();
		}

		$data['main_address'] = array(
			'location_id' 	=> (isset($main_address['location_id'])) ? $main_address['location_id'] : '',
			'address_1' 	=> (isset($main_address['address_1'])) ? $main_address['address_1'] : '',
			'address_2' 	=> (isset($main_address['address_2'])) ? $main_address['address_2'] : '',
			'city' 			=> (isset($main_address['city'])) ? $main_address['city'] : '',
			'postcode' 		=> (isset($main_address['postcode'])) ? $main_address['postcode'] : '',
			'country_id' 	=> (isset($main_address['country_id'])) ? $main_address['country_id'] : ''
		);

		if ($this->input->post('maps_api_key')) {
			$data['maps_api_key'] = $this->input->post('maps_api_key');
		} else {
			$data['maps_api_key'] = $this->config->item('maps_api_key');
		}

		if ($this->input->post('search_by')) {
			$data['search_by'] = $this->input->post('search_by');
		} else {
			$data['search_by'] = $this->config->item('search_by');
		}

		if ($this->input->post('distance_unit')) {
			$data['distance_unit'] = $this->input->post('distance_unit');
		} else {
			$data['distance_unit'] = $this->config->item('distance_unit');
		}

		if ($this->input->post('location_order_email')) {
			$data['location_order_email'] = $this->input->post('location_order_email');
		} else {
			$data['location_order_email'] = $this->config->item('location_order_email');
		}

		if ($this->input->post('location_reserve_email')) {
			$data['location_reserve_email'] = $this->input->post('location_reserve_email');
		} else {
			$data['location_reserve_email'] = $this->config->item('location_reserve_email');
		}

		if ($this->input->post('future_orders')) {
			$data['future_orders'] = $this->input->post('future_orders');
		} else {
			$data['future_orders'] = $this->config->item('future_orders');
		}

		if ($this->input->post('location_order')) {
			$data['location_order'] = $this->input->post('location_order');
		} else {
			$data['location_order'] = $this->config->item('location_order');
		}

		if ($this->input->post('approve_reviews')) {
			$data['approve_reviews'] = $this->input->post('approve_reviews');
		} else {
			$data['approve_reviews'] = $this->config->item('approve_reviews');
		}

		if ($this->input->post('order_status_new')) {
			$data['order_status_new'] = $this->input->post('order_status_new');
		} else {
			$data['order_status_new'] = $this->config->item('order_status_new');
		}

		if ($this->input->post('order_status_complete')) {
			$data['order_status_complete'] = $this->input->post('order_status_complete');
		} else {
			$data['order_status_complete'] = $this->config->item('order_status_complete');
		}

		if ($this->input->post('guest_order')) {
			$data['guest_order'] = $this->input->post('guest_order');
		} else {
			$data['guest_order'] = $this->config->item('guest_order');
		}

		if ($this->input->post('delivery_time')) {
			$data['delivery_time'] = $this->input->post('delivery_time');
		} else {
			$data['delivery_time'] = $this->config->item('delivery_time');
		}

		if ($this->input->post('collection_time')) {
			$data['collection_time'] = $this->input->post('collection_time');
		} else {
			$data['collection_time'] = $this->config->item('collection_time');
		}

		if ($this->input->post('reservation_mode')) {
			$data['reservation_mode'] = $this->input->post('reservation_mode');
		} else {
			$data['reservation_mode'] = $this->config->item('reservation_mode');
		}

		if ($this->input->post('reservation_status')) {
			$data['reservation_status'] = $this->input->post('reservation_status');
		} else {
			$data['reservation_status'] = $this->config->item('reservation_status');
		}

		if ($this->input->post('reservation_interval')) {
			$data['reservation_interval'] = $this->input->post('reservation_interval');
		} else {
			$data['reservation_interval'] = $this->config->item('reservation_interval');
		}

		if ($this->input->post('reservation_turn')) {
			$data['reservation_turn'] = $this->input->post('reservation_turn');
		} else {
			$data['reservation_turn'] = $this->config->item('reservation_turn');
		}

		if ($this->input->post('themes_allowed_img')) {
			$data['themes_allowed_img'] = strtolower($this->input->post('themes_allowed_img'));
		} else {
			$data['themes_allowed_img'] = strtolower($this->config->item('themes_allowed_img'));
		}

		if ($this->input->post('themes_allowed_file')) {
			$data['themes_allowed_file'] = strtolower($this->input->post('themes_allowed_file'));
		} else {
			$data['themes_allowed_file'] = strtolower($this->config->item('themes_allowed_file'));
		}

		if ($this->input->post('themes_hidden_files')) {
			$data['themes_hidden_files'] = strtolower($this->input->post('themes_hidden_files'));
		} else {
			$data['themes_hidden_files'] = strtolower($this->config->item('themes_hidden_files'));
		}

		if ($this->input->post('themes_hidden_folders')) {
			$data['themes_hidden_folders'] = strtolower($this->input->post('themes_hidden_folders'));
		} else {
			$data['themes_hidden_folders'] = strtolower($this->config->item('themes_hidden_folders'));
		}

		if ($this->input->post('image_manager') AND is_array($this->input->post('image_manager'))) {
			$image_manager = $this->input->post('image_manager');
		} else if (is_array($this->config->item('image_manager'))) {
			$image_manager = $this->config->item('image_manager');
		} else {
			$image_manager = array();
		}

		$data['image_manager'] = array(
			'root_folder' 			=> (isset($image_manager['root_folder'])) ? $image_manager['root_folder'] : '',
			'max_size' 				=> (isset($image_manager['max_size'])) ? $image_manager['max_size'] : '',
			'thumb_height' 			=> (isset($image_manager['thumb_height'])) ? $image_manager['thumb_height'] : '',
			'thumb_width' 			=> (isset($image_manager['thumb_width'])) ? $image_manager['thumb_width'] : '',
			'thumb_height_mini' 	=> (isset($image_manager['thumb_height_mini'])) ? $image_manager['thumb_height_mini'] : '',
			'thumb_width_mini' 		=> (isset($image_manager['thumb_width_mini'])) ? $image_manager['thumb_width_mini'] : '',
			'show_mini' 			=> (isset($image_manager['show_mini'])) ? $image_manager['show_mini'] : '',
			'show_ext' 				=> (isset($image_manager['show_ext'])) ? $image_manager['show_ext'] : '',
			'uploads' 				=> (isset($image_manager['uploads'])) ? $image_manager['uploads'] : '',
			'new_folder' 			=> (isset($image_manager['new_folder'])) ? $image_manager['new_folder'] : '',
			'copy' 					=> (isset($image_manager['copy'])) ? $image_manager['copy'] : '',
			'move' 					=> (isset($image_manager['move'])) ? $image_manager['move'] : '',
			'rename' 				=> (isset($image_manager['rename'])) ? $image_manager['rename'] : '',
			'delete' 				=> (isset($image_manager['delete'])) ? $image_manager['delete'] : '',
			'allowed_ext' 			=> (isset($image_manager['allowed_ext'])) ? $image_manager['allowed_ext'] : '',
			'hidden_files' 			=> (isset($image_manager['hidden_files'])) ? $image_manager['hidden_files'] : '',
			'hidden_folders' 		=> (isset($image_manager['hidden_folders'])) ? $image_manager['hidden_folders'] : '',
			'transliteration' 		=> (isset($image_manager['transliteration'])) ? $image_manager['transliteration'] : '',
			'remember_days' 		=> (isset($image_manager['remember_days'])) ? $image_manager['remember_days'] : '',
			'delete_thumbs'			=> site_url('settings/delete_thumbs'),
		);

		if ($this->input->post('protocol')) {
			$data['protocol'] = strtolower($this->input->post('protocol'));
		} else {
			$data['protocol'] = strtolower($this->config->item('protocol'));
		}

		if ($this->input->post('mailtype')) {
			$data['mailtype'] = strtolower($this->input->post('mailtype'));
		} else {
			$data['mailtype'] = strtolower($this->config->item('mailtype'));
		}

		if ($this->input->post('smtp_host')) {
			$data['smtp_host'] = $this->input->post('smtp_host');
		} else {
			$data['smtp_host'] = $this->config->item('smtp_host');
		}

		if ($this->input->post('smtp_port')) {
			$data['smtp_port'] = $this->input->post('smtp_port');
		} else {
			$data['smtp_port'] = $this->config->item('smtp_port');
		}

		if ($this->input->post('smtp_user')) {
			$data['smtp_user'] = $this->input->post('smtp_user');
		} else {
			$data['smtp_user'] = $this->config->item('smtp_user');
		}

		if ($this->input->post('smtp_pass')) {
			$data['smtp_pass'] = $this->input->post('smtp_pass');
		} else {
			$data['smtp_pass'] = $this->config->item('smtp_pass');
		}

		if ($this->input->post('log_threshold')) {
			$data['log_threshold'] = $this->input->post('log_threshold');
		} else {
			$data['log_threshold'] = $this->config->item('log_threshold');
		}

		if ($this->input->post('encryption_key')) {
			$data['encryption_key'] = $this->input->post('encryption_key');
		} else {
			$data['encryption_key'] = $this->config->item('encryption_key');
		}

		if ($this->input->post('activity_timeout')) {
			$data['activity_timeout'] = $this->input->post('activity_timeout');
		} else {
			$data['activity_timeout'] = $this->config->item('activity_timeout');
		}

		if ($this->input->post('activity_delete')) {
			$data['activity_delete'] = $this->input->post('activity_delete');
		} else {
			$data['activity_delete'] = $this->config->item('activity_delete');
		}

		if ($this->input->post('permalink')) {
			$data['permalink'] = $this->input->post('permalink');
		} else {
			$data['permalink'] = $this->config->item('permalink');
		}

		if ($this->input->post('maintenance_mode')) {
			$data['maintenance_mode'] = $this->input->post('maintenance_mode');
		} else {
			$data['maintenance_mode'] = $this->config->item('maintenance_mode');
		}

		if ($this->input->post('maintenance_page')) {
			$data['maintenance_page'] = $this->input->post('maintenance_page');
		} else {
			$data['maintenance_page'] = $this->config->item('maintenance_page');
		}

		if ($this->input->post('cache_mode')) {
			$data['cache_mode'] = $this->input->post('cache_mode');
		} else {
			$data['cache_mode'] = $this->config->item('cache_mode');
		}

		if ($this->input->post('cache_time')) {
			$data['cache_time'] = $this->input->post('cache_time');
		} else if (isset($this->config->item['cache_time'])) {
			$data['cache_time'] = $this->config->item('cache_time');
		} else {
			$data['cache_time'] = '0';
		}

		$data['page_limits'] = array('10', '20', '50', '75', '100');

		$timezones = $this->getTimezones();
		foreach ($timezones as $key => $value) {
			$data['timezones'][$key] = $value;
		}

		$data['countries'] = array();
		$results = $this->Countries_model->getCountries();
		foreach ($results as $result) {
			$data['countries'][] = array(
				'country_id'	=>	$result['country_id'],
				'name'			=>	$result['country_name'],
			);
		}

		$data['currencies'] = array();
		$currencies = $this->Currencies_model->getCurrencies();
		foreach ($currencies as $currency) {
			$data['currencies'][] = array(
				'currency_id'		=>	$currency['currency_id'],
				'currency_name'		=>	$currency['currency_name'],
				'currency_status'	=>	$currency['currency_status']
			);
		}

		$this->load->model('Languages_model');
		$data['languages'] = array();
		$results = $this->Languages_model->getLanguages();
		foreach ($results as $result) {
			$data['languages'][] = array(
				'language_id'	=>	$result['language_id'],
				'name'			=>	$result['name'],
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

		$data['categories'] = array();
		$categories = $this->Menus_model->getCategories();
		foreach ($categories as $category) {
			$data['categories'][] = array(
				'category_id'	=>	$category['category_id'],
				'category_name'	=>	$category['name']
			);
		}

		$data['search_by_array'] = array('postcode' => 'Postcode Only', 'address' => 'Postcode & Address');

		$data['statuses'] = array();
		$results = $this->Statuses_model->getStatuses();
		foreach ($results as $result) {
			$data['statuses'][] = array(
				'status_id'		=> $result['status_id'],
				'status_name'	=> $result['status_name'],
				'status_for'	=> $result['status_for']
			);
		}

		$data['protocals'] 	= array('mail', 'sendmail', 'smtp');
		$data['mailtypes'] 	= array('text', 'html');
		$data['thresholds'] = array('Disable', 'Error Only', 'Debug Only', 'Info Only', 'All');

		$this->load->model('Pages_model');
		$data['pages'] = array();
		$results = $this->Pages_model->getPages();
		foreach ($results as $result) {
			$data['pages'][] = array(
				'page_id'		=>	$result['page_id'],
				'name'			=>	$result['name'],
			);
		}

		if ($this->input->post() AND $this->_updateSettings() === TRUE) {
			redirect('settings');
		}

		$this->template->setPartials(array('header', 'footer'));
		$this->template->render('settings', $data);
	}

	public function delete_thumbs() {
		if (!$this->user->islogged()) {
  			redirect('login');
		}

    	if (!$this->user->hasPermissions('access', 'settings')) {
  			redirect('permission');
		}

    	if (!$this->user->hasPermissions('modify', 'settings')) {
			$this->alert->set('warning', 'Warning: You do not have permission to delete!');
    	} else {
			if (file_exists(IMAGEPATH . 'thumbs')) {
				$this->_delete_thumbs(IMAGEPATH . 'thumbs/*');
				$this->alert->set('success', 'Thumbs deleted sucessfully!');
			}
		}

		redirect('settings');
	}

	public function _updateSettings() {
    	if (!$this->user->hasPermissions('modify', 'settings')) {
			$this->alert->set('warning', 'Warning: You do not have permission to update!');
  			return TRUE;
    	} else if ($this->validateForm() === TRUE) {
			$update = array(
				'site_name' 				=> $this->input->post('site_name'),
				'site_email' 				=> $this->input->post('site_email'),
				'site_logo' 				=> $this->input->post('site_logo'),
				'country_id' 				=> $this->input->post('country_id'),
				'timezone' 					=> $this->input->post('timezone'),
				'currency_id' 				=> $this->input->post('currency_id'),
				'language_id' 				=> $this->input->post('language_id'),
				'customer_group_id' 		=> $this->input->post('customer_group_id'),
				'page_limit' 				=> $this->input->post('page_limit'),
				'meta_description' 			=> $this->input->post('meta_description'),
				'meta_keywords' 			=> $this->input->post('meta_keywords'),
				'menus_page_limit' 			=> $this->input->post('menus_page_limit'),
				'show_menu_images' 			=> $this->input->post('show_menu_images'),
				'menu_images_h' 			=> $this->input->post('menu_images_h'),
				'menu_images_w' 			=> $this->input->post('menu_images_w'),
				'special_category_id' 		=> $this->input->post('special_category_id'),
				'registration_terms' 		=> $this->input->post('registration_terms'),
				'checkout_terms' 			=> $this->input->post('checkout_terms'),
				'stock_warning' 			=> $this->input->post('stock_warning'),
				'stock_qty_warning' 		=> $this->input->post('stock_warning'),
				'registration_email'		=> $this->input->post('registration_email'),
				'customer_order_email'		=> $this->input->post('customer_order_email'),
				'customer_reserve_email'	=> $this->input->post('customer_reserve_email'),
				'main_address'				=> $this->input->post('main_address'),
				'maps_api_key'				=> $this->input->post('maps_api_key'),
				'search_by'					=> $this->input->post('search_by'),
				'distance_unit'				=> $this->input->post('distance_unit'),
				'future_orders' 			=> $this->input->post('future_orders'),
				'location_order'			=> $this->input->post('location_order'),
				'location_order_email'		=> $this->input->post('location_order_email'),
				'location_reserve_email'	=> $this->input->post('location_reserve_email'),
				'approve_reviews'			=> $this->input->post('approve_reviews'),
				'order_status_new'			=> $this->input->post('order_status_new'),
				'order_status_complete'		=> $this->input->post('order_status_complete'),
				'guest_order'				=> $this->input->post('guest_order'),
				'delivery_time'				=> $this->input->post('delivery_time'),
				'collection_time'			=> $this->input->post('collection_time'),
				'reservation_mode'			=> $this->input->post('reservation_mode'),
				'reservation_status'		=> $this->input->post('reservation_status'),
				'reservation_interval'		=> $this->input->post('reservation_interval'),
				'reservation_turn'			=> $this->input->post('reservation_turn'),
				'themes_allowed_img'		=> $this->input->post('themes_allowed_img'),
				'themes_allowed_file'		=> $this->input->post('themes_allowed_file'),
				'themes_hidden_files'		=> $this->input->post('themes_hidden_files'),
				'themes_hidden_folders'		=> $this->input->post('themes_hidden_folders'),
				'image_manager'				=> $this->input->post('image_manager'),
				'protocol'	 				=> strtolower($this->input->post('protocol')),
				'mailtype' 					=> strtolower($this->input->post('mailtype')),
				'smtp_host' 				=> $this->input->post('smtp_host'),
				'smtp_port' 				=> $this->input->post('smtp_port'),
				'smtp_user' 				=> $this->input->post('smtp_user'),
				'smtp_pass' 				=> $this->input->post('smtp_pass'),
				'log_threshold' 			=> $this->input->post('log_threshold'),
				'activity_timeout'	 		=> $this->input->post('activity_timeout'),
				'activity_delete' 			=> $this->input->post('activity_delete'),
				'encryption_key' 			=> $this->input->post('encryption_key'),
				'permalink' 				=> $this->input->post('permalink'),
				'maintenance_mode' 			=> $this->input->post('maintenance_mode'),
				'maintenance_page' 			=> $this->input->post('maintenance_page'),
				'cache_mode' 				=> $this->input->post('cache_mode'),
				'cache_time' 				=> $this->input->post('cache_time')
			);


			if (!empty($update['main_address']) AND is_array($update['main_address'])) {
				$this->load->model('Locations_model');
				$this->Locations_model->updateDefault($update['main_address']);
			}

			if ($this->Settings_model->updateSettings('config', $update)) {
				$this->alert->set('success', 'Settings updated sucessfully.');
			} else {
				$this->alert->set('warning', 'An error occured, nothing updated.');
			}

			return TRUE;
		}
	}

	public function validateForm() {
		$this->form_validation->set_rules('site_name', 'Restaurant Name', 'xss_clean|trim|required|min_length[2]|max_length[128]');
		$this->form_validation->set_rules('site_email', 'Restaurant Email', 'xss_clean|trim|required|valid_email');
		$this->form_validation->set_rules('site_logo', 'Site Logo', 'xss_clean|trim|required');
		$this->form_validation->set_rules('country_id', 'Restaurant Country', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('timezone', 'Timezones', 'xss_clean|trim|required');
		$this->form_validation->set_rules('currency_id', 'Restaurant Currency', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('language_id', 'Default Language', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('customer_group_id', 'Customer Group', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('page_limit', 'Items Per Page', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('meta_description', 'Meta Description', 'xss_clean|trim');
		$this->form_validation->set_rules('meta_keywords', 'Meta Keywords', 'xss_clean|trim');
		$this->form_validation->set_rules('menus_page_limit', 'Menus Per Page', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('show_menu_images', 'Show Menu Images', 'xss_clean|trim|required|integer');

		if ($this->input->post('show_menu_images') == '1') {
			$this->form_validation->set_rules('menu_images_h', 'Menu Images Height', 'xss_clean|trim|required|numeric');
			$this->form_validation->set_rules('menu_images_w', 'Menu Images Width', 'xss_clean|trim|required|numeric');
		}

		$this->form_validation->set_rules('special_category_id', 'Specials Category', 'xss_clean|trim|numeric');
		$this->form_validation->set_rules('registration_terms', 'Registration Terms', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('checkout_terms', 'Checkout Terms', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('stock_warning', 'Stock Warning', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('stock_qty_warning', 'Stock Quantity Warning', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('registration_email', 'Registration Email', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('customer_order_email', 'Customer Order Email', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('customer_reserve_email', 'Customer Reservation Email', 'xss_clean|trim|required|numeric');

		$this->form_validation->set_rules('main_address[address_1]', 'Address 1', 'xss_clean|trim|required|min_length[2]|max_length[128]');
		$this->form_validation->set_rules('main_address[address_2]', 'Address 2', 'xss_clean|trim|max_length[128]');
		$this->form_validation->set_rules('main_address[city]', 'City', 'xss_clean|trim|required|min_length[2]|max_length[128]');
		$this->form_validation->set_rules('main_address[postcode]', 'Postcode', 'xss_clean|trim|required|min_length[2]|max_length[10]|callback_get_lat_lag');
		$this->form_validation->set_rules('main_address[country_id]', 'Country', 'xss_clean|trim|required|integer');

		$this->form_validation->set_rules('maps_api_key', 'Google Maps API Key', 'xss_clean|trim');
		$this->form_validation->set_rules('search_by', 'Search By', 'xss_clean|trim|required|alpha');
		$this->form_validation->set_rules('distance_unit', 'Distance Unit', 'xss_clean|trim|required');
		$this->form_validation->set_rules('future_orders', 'Future Orders', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('location_order', 'Allow Order', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('location_order_email', 'Send Order Email', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('location_reserve_email', 'Send Reservation Email', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('approve_reviews', 'Approve Reviews', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('order_status_new', 'New Order Status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('order_status_complete', 'Complete Order Status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('guest_order', 'Guest Order', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('delivery_time', 'Delivery Time', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('collection_time', 'Collection Time', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('reservation_mode', 'Reservation Mode', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('reservation_status', 'Reservation Status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('reservation_interval', 'Reservation Interval', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('reservation_turn', 'Reservations Turn', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('themes_allowed_img', 'Themes Allowed Images', 'xss_clean|trim');
		$this->form_validation->set_rules('themes_allowed_file', 'Themes Allowed Files', 'xss_clean|trim');
		$this->form_validation->set_rules('themes_hidden_files', 'Themes Hidden Files', 'xss_clean|trim');
		$this->form_validation->set_rules('themes_hidden_folders', 'Themes Hidden Folders', 'xss_clean|trim');
		$this->form_validation->set_rules('image_manager[root_folder]', 'Root Folder', 'xss_clean|trim|required|callback_validate_path');
		$this->form_validation->set_rules('image_manager[max_size]', 'Maximum File Size', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('image_manager[thumb_height]', 'Thumbnail Height', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('image_manager[thumb_width]', 'Thumbnail Width', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('image_manager[thumb_height_mini]', 'Mini Thumbnail Height', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('image_manager[thumb_width_mini]', 'Mini Thumbnail Width', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('image_manager[show_mini]', 'Mini Thumbnail', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('image_manager[show_ext]', 'Show Extension', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('image_manager[uploads]', 'Uploads', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[new_folder]', 'New Folder', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[copy]', 'Copy', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[move]', 'Move', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[rename]', 'Rename', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[delete]', 'Delete', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[allowed_ext]', 'Allowed Extension', 'xss_clean|trim|required');
		$this->form_validation->set_rules('image_manager[hidden_files]', 'Hidden Files', 'xss_clean|trim');
		$this->form_validation->set_rules('image_manager[hidden_folders]', 'Hidden Folders', 'xss_clean|trim');
		$this->form_validation->set_rules('image_manager[transliteration]', 'Transliteration', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[remember_days]', 'Remember Last Folder', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('protocol', 'Mail Protocol', 'xss_clean|trim|required');
		$this->form_validation->set_rules('mailtype', 'Mail Type Format', 'xss_clean|trim|required');
		$this->form_validation->set_rules('smtp_host', 'SMTP Host', 'xss_clean|trim');
		$this->form_validation->set_rules('smtp_port', 'SMTP Port', 'xss_clean|trim');
		$this->form_validation->set_rules('smtp_user', 'SMTP Username', 'xss_clean|trim');
		$this->form_validation->set_rules('smtp_pass', 'SMTP Password', 'xss_clean|trim');
		$this->form_validation->set_rules('log_threshold', 'Threshold Options', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('encryption_key', 'Encryption Key', 'xss_clean|trim|required');
		$this->form_validation->set_rules('activity_timeout', 'Activity Timeout', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('activity_delete', 'Activity Delete', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('permalink', 'Permalink', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('maintenance_mode', 'Maintenance Mode', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('maintenance_page', 'Maintenance Page', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('cache_mode', 'Cache Mode', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('cache_time', 'Cache Time', 'xss_clean|trim|integer');

		if ($this->form_validation->run() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}

	}

	public function getTimezones() {
		$timezone_identifiers = DateTimeZone::listIdentifiers();
		$utc_time = new DateTime('now', new DateTimeZone('UTC'));

		$temp_timezones = array();
		foreach ($timezone_identifiers as $timezone_identifier) {
			$current_timezone = new DateTimeZone($timezone_identifier);

			$temp_timezones[] = array(
				'offset' => (int)$current_timezone->getOffset($utc_time),
				'identifier' => $timezone_identifier
			);
		}

		usort($temp_timezones, function($a, $b) {
			return ($a['offset'] == $b['offset']) ? strcmp($a['identifier'], $b['identifier']) : $a['offset'] - $b['offset'];
		});

		$timezoneList = array();
		foreach ($temp_timezones as $tz) {
			$sign = ($tz['offset'] > 0) ? '+' : '-';
			$offset = gmdate('H:i', abs($tz['offset']));
			$timezone_list[$tz['identifier']] = $tz['identifier'] .' (UTC ' . $sign . $offset .')';
		}

		return $timezone_list;
	}

	public function get_lat_lag() {
		if (isset($_POST['main_address']) AND is_array($_POST['main_address']) AND !empty($_POST['main_address']['postcode'])) {
			$address_string =  implode(", ", $_POST['main_address']);
			$address = urlencode($address_string);
			$geocode = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='. $address .'&sensor=false&region=GB');
    		$output = json_decode($geocode);
    		$status = $output->status;

    		if ($status === 'OK') {
				$_POST['main_address']['location_lat'] = $output->results[0]->geometry->location->lat;
				$_POST['main_address']['location_lng'] = $output->results[0]->geometry->location->lng;
			    return TRUE;
    		} else {
        		$this->form_validation->set_message('get_lat_lag', 'The Address you entered failed Geocoding, please enter a different address!');
        		return FALSE;
    		}
        }
	}

	public function validate_path($str) {
		if (strpos($str, '/') !== FALSE OR strpos($str, './') !== FALSE) {
			$this->form_validation->set_message('validate_path', 'Root Folder must have NO SLASH!');
			return FALSE;
		}

		return TRUE;
	}

	public function _delete_thumbs($thumb_path) {
		foreach (glob($thumb_path) as $path) {

			if (file_exists($path) AND is_file($path) AND basename($path) === "index.html") {
				continue;
			}

			if (file_exists($path) AND is_file($path)) {
				unlink($path);
				continue;
			}

			foreach (scandir($path) as $item) {
				if ($item != '.' AND $item != '..') {
					if ( ! is_dir($path .'/'. $item)) {
						unlink($path .'/'. $item);
					} else {
						$this->_delete_thumbs($path .'/'. $item);
					}
				}
			}

			if (is_dir($path)) {
				rmdir($path);
				continue;
			}
		}
	}
}

/* End of file settings.php */
/* Location: ./admin/controllers/settings.php */