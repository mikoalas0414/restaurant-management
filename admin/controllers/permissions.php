<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Permissions extends Admin_Controller {

    public function __construct() {
		parent::__construct(); //  calls the constructor
        $this->user->restrict('Admin.Permissions');
        $this->load->library('pagination');
        $this->load->model('Permissions_model');
    }

	public function index() {
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

		if (is_numeric($this->input->get('filter_status'))) {
			$filter['filter_status'] = $data['filter_status'] = $this->input->get('filter_status');
			$url .= 'filter_status='.$filter['filter_status'].'&';
		} else {
			$filter['filter_status'] = $data['filter_status'] = '';
		}

		if ($this->input->get('sort_by')) {
			$filter['sort_by'] = $data['sort_by'] = $this->input->get('sort_by');
		} else {
			$filter['sort_by'] = $data['sort_by'] = 'permission_id';
		}

		if ($this->input->get('order_by')) {
			$filter['order_by'] = $data['order_by'] = $this->input->get('order_by');
			$data['order_by_active'] = $this->input->get('order_by') .' active';
		} else {
			$filter['order_by'] = $data['order_by'] = 'ASC';
			$data['order_by_active'] = 'ASC';
		}

		$this->template->setTitle('Permissions');
		$this->template->setHeading('Permissions');
		$this->template->setButton('+ New', array('class' => 'btn btn-primary', 'href' => page_url() .'/edit'));
		$this->template->setButton('Delete', array('class' => 'btn btn-danger', 'onclick' => '$(\'#list-form\').submit();'));

		$data['text_empty'] 		= 'There are no permissions available.';

		$order_by = (isset($filter['order_by']) AND $filter['order_by'] == 'ASC') ? 'DESC' : 'ASC';
		$data['sort_name'] 			= site_url('permissions'.$url.'sort_by=name&order_by='.$order_by);
		$data['sort_status'] 		= site_url('permissions'.$url.'sort_by=status&order_by='.$order_by);
		$data['sort_id'] 			= site_url('permissions'.$url.'sort_by=permission_id&order_by='.$order_by);

		$data['permissions'] = array();
		$results = $this->Permissions_model->getList($filter);
		foreach ($results as $result) {
			$data['permissions'][] = array(
				'permission_id'		=> $result['permission_id'],
				'name'		        => $result['name'],
				'description'		=> $result['description'],
				'action'    		=> (!empty($result['action'])) ? ucwords(implode(' | ', unserialize($result['action']))) : '',
				'status'		    => ($result['status'] == '1') ? 'Enabled' : 'Disabled',
				'edit'				=> site_url('permissions/edit?id=' . $result['permission_id'])
			);
		}

		if ($this->input->get('sort_by') AND $this->input->get('order_by')) {
			$url .= 'sort_by='.$filter['sort_by'].'&';
			$url .= 'order_by='.$filter['order_by'].'&';
		}

		$config['base_url'] 		= site_url('permissions'.$url);
		$config['total_rows'] 		= $this->Permissions_model->getCount($filter);
		$config['per_page'] 		= $filter['limit'];

		$this->pagination->initialize($config);

		$data['pagination'] = array(
			'info'		=> $this->pagination->create_infos(),
			'links'		=> $this->pagination->create_links()
		);

		if ($this->input->post('delete') AND $this->_deletePermission() === TRUE) {
			redirect('permissions');
		}

		$this->template->setPartials(array('header', 'footer'));
		$this->template->render('permissions', $data);
	}

	public function edit() {
		$permission_info = $this->Permissions_model->getPermission((int) $this->input->get('id'));

		if ($permission_info) {
			$permission_id = $permission_info['permission_id'];
			$data['_action']	= site_url('permissions/edit?id='. $permission_id);
		} else {
		    $permission_id = 0;
			$data['_action']	= site_url('permissions/edit');
		}

		$title = (isset($permission_info['name'])) ? $permission_info['name'] : 'New';
		$this->template->setTitle('Permission: '. $title);
		$this->template->setHeading('Permission: '. $title);
		$this->template->setButton('Save', array('class' => 'btn btn-primary', 'onclick' => '$(\'#edit-form\').submit();'));
		$this->template->setButton('Save & Close', array('class' => 'btn btn-default', 'onclick' => 'saveClose();'));
		$this->template->setBackButton('btn btn-back', site_url('permissions'));


		$data['permission_id'] 		= $permission_info['permission_id'];
		$data['name'] 		        = $permission_info['name'];
		$data['description'] 		= $permission_info['description'];
		$data['status'] 		    = $permission_info['status'];

        if ($this->input->post('action')) {
            $data['action'] = $this->input->post('action');
        } else if (!empty($permission_info['action'])) {
            $data['action'] = unserialize($permission_info['action']);
        } else {
            $data['action'] = array();
        }

        $data['permission_actions'] = array('access' => 'Access', 'manage' => 'Manage', 'add' => 'Add', 'delete' => 'Delete');

		if ($this->input->post() AND $permission_id = $this->_savePermission()) {
			if ($this->input->post('save_close') === '1') {
				redirect('permissions');
			}

			redirect('permissions/edit?id='. $permission_id);
		}

		$this->template->setPartials(array('header', 'footer'));
		$this->template->render('permissions_edit', $data);
	}

	private function _savePermission() {
    	if ($this->validateForm() === TRUE) {
            $save_type = ( ! is_numeric($this->input->get('id'))) ? 'added' : 'updated';

			if ($permission_id = $this->Permissions_model->savePermission($this->input->get('id'), $this->input->post())) {
				$this->alert->set('success', 'Permission ' . $save_type . ' successfully.');
			} else {
				$this->alert->set('warning', 'An error occurred, nothing ' . $save_type . '.');
			}

			return $permission_id;
		}
	}

	private function _deletePermission() {
        if ($this->input->post('delete')) {
            $deleted_rows = $this->Permissions_model->deletePermission($this->input->post('delete'));

            if ($deleted_rows > 0) {
                $prefix = ($deleted_rows > 1) ? '['.$deleted_rows.'] Permissions': 'Permission';
                $this->alert->set('success', $prefix.' deleted successfully.');
            } else {
                $this->alert->set('warning', 'An error occurred, nothing deleted.');
            }

            return TRUE;
        }
	}

	private function validateForm() {
		$this->form_validation->set_rules('name', 'Name', 'xss_clean|trim|required|min_length[2]|max_length[128]');
		$this->form_validation->set_rules('description', 'Description', 'xss_clean|trim|required|max_length[255]');
		$this->form_validation->set_rules('status', 'Status', 'xss_clean|trim|required|integer');

		if ($this->form_validation->run() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

/* End of file permissions.php */
/* Location: ./admin/controllers/permissions.php */