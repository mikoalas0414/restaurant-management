<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Countries_model extends TI_Model {

    public function getCount($filter = array()) {
		if (!empty($filter['filter_search'])) {
			$this->db->like('country_name', $filter['filter_search']);
		}

		if (isset($filter['filter_status']) AND is_numeric($filter['filter_status'])) {
			$this->db->where('status', $filter['filter_status']);
		}

		$this->db->from('countries');
		return $this->db->count_all_results();
    }

	public function getList($filter = array()) {
		if (!empty($filter['page']) AND $filter['page'] !== 0) {
			$filter['page'] = ($filter['page'] - 1) * $filter['limit'];
		}

		if ($this->db->limit($filter['limit'], $filter['page'])) {
			$this->db->from('countries');

			if (!empty($filter['filter_search'])) {
				$this->db->like('country_name', $filter['filter_search']);
			}

			if (isset($filter['filter_status']) AND is_numeric($filter['filter_status'])) {
				$this->db->where('status', $filter['filter_status']);
			}

			if (!empty($filter['sort_by']) AND !empty($filter['order_by'])) {
				$this->db->order_by($filter['sort_by'], $filter['order_by']);
			}

			$query = $this->db->get();
			$result = array();

			if ($query->num_rows() > 0) {
				$result = $query->result_array();
			}

			return $result;
		}
	}

	public function getCountries() {
		$this->db->from('countries');
		$this->db->order_by('country_name', 'ASC');

		$query = $this->db->get();
		$result = array();

		if ($query->num_rows() > 0) {
			$result = $query->result_array();
		}

		return $result;
	}

	public function getCountry($country_id) {
		$this->db->from('countries');
		$this->db->where('country_id', $country_id);

		$query = $this->db->get();

		if ($this->db->affected_rows() > 0) {
			return $query->row_array();
		}
	}

	public function updateCountry($update = array()) {
		$query = FALSE;

		if (!empty($update['country_name'])) {
			$this->db->set('country_name', $update['country_name']);
		}

		if (!empty($update['iso_code_2'])) {
			$this->db->set('iso_code_2', $update['iso_code_2']);
		}

		if (!empty($update['iso_code_3'])) {
			$this->db->set('iso_code_3', $update['iso_code_3']);
		}

		if (!empty($update['flag'])) {
			$this->db->set('flag', $update['flag']);
		}

		if (!empty($update['format'])) {
			$this->db->set('format', $update['format']);
		}

		if ($update['status'] === '1') {
			$this->db->set('status', $update['status']);
		} else {
			$this->db->set('status', '0');
		}

		if (!empty($update['country_id'])) {
			$this->db->where('country_id', $update['country_id']);
			$query = $this->db->update('countries');
		}

		return $query;
	}

	public function addCountry($add = array()) {
		$query = FALSE;

		if (!empty($add['country_name'])) {
			$this->db->set('country_name', $add['country_name']);
		}

		if (!empty($add['iso_code_2'])) {
			$this->db->set('iso_code_2', $add['iso_code_2']);
		}

		if (!empty($add['iso_code_3'])) {
			$this->db->set('iso_code_3', $add['iso_code_3']);
		}

		if (!empty($add['flag'])) {
			$this->db->set('flag', $add['flag']);
		}

		if (!empty($add['format'])) {
			$this->db->set('format', $add['format']);
		}

		if ($add['status'] === '1') {
			$this->db->set('status', $add['status']);
		} else {
			$this->db->set('status', '0');
		}

		if (!empty($add)) {
			if ($this->db->insert('countries')) {
				$query = $this->db->insert_id();
			}
		}

		return $query;
	}

	public function deleteCountry($country_id) {
		if (is_numeric($country_id)) {
			$this->db->where('country_id', $country_id);
			$this->db->delete('countries');

			if ($this->db->affected_rows() > 0) {
				return TRUE;
			}
		}
	}
}

/* End of file countries_model.php */
/* Location: ./system/tastyigniter/models/countries_model.php */