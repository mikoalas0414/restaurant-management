<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Cart_module extends Main_Controller {

	public function __construct() {
		parent::__construct(); 																	// calls the constructor

        $this->load->model('Cart_model'); 														// load the cart model
        $this->load->model('Image_tool_model'); 														// load the Image tool model

        $this->load->library('cart'); 															// load the cart library
        $this->load->library('currency'); 														// load the currency library

        $this->lang->load('cart_module/cart_module');
	}

	public function index($ext_data = array()) {
		if ( ! file_exists(EXTPATH .'cart_module/views/cart_module.php')) { 								//check if file exists in views folder
			show_404(); 																		// Whoops, show 404 error page!
		}

        $referrer_uri = explode('/', str_replace(site_url(), '', $this->agent->referrer()));
        $data['rsegment'] = $rsegment = ($this->uri->rsegment(1) === 'cart_module' AND !empty($referrer_uri[0])) ? $referrer_uri[0] : $this->uri->rsegment(1);

        if (empty($ext_data)) {
            $extension = $this->extension->getModule('cart_module');
            if (!empty($extension['ext_data'])) {
                $ext_data = $extension['ext_data'];
            };
        }

        $this->template->setStyleTag(extension_url('cart_module/views/stylesheet.css'), 'cart-module-css', '144000');

        $order_data = $this->session->userdata('order_data');
        if ($rsegment === 'checkout' AND isset($order_data['checkout_step']) AND $order_data['checkout_step'] === 'two') {
            $data['button_order'] = '<a class="btn btn-primary btn-block btn-lg" onclick="$(\'#checkout-form\').submit();">' . $this->lang->line('button_confirm') . '</a>';
        } else if ($rsegment == 'checkout') {
            $data['button_order'] = '<a class="btn btn-primary btn-block btn-lg" onclick="$(\'#checkout-form\').submit();">' . $this->lang->line('button_payment') . '</a>';
        } else {
            $data['button_order'] = '<a class="btn btn-primary btn-block btn-lg" href="' . site_url('checkout') . '">' . $this->lang->line('button_order') . '</a>';
        }

        $data['is_opened']                  = $this->location->isOpened();
        $data['order_type']                 = $this->location->orderType();
        $data['search_query'] 		        = $this->location->searchQuery();
        $data['delivery_time'] 		        = $this->location->deliveryTime();
        $data['collection_time'] 	        = $this->location->collectionTime();
        $data['has_delivery']               = $this->location->hasDelivery();
        $data['has_collection']             = $this->location->hasCollection();
		$data['show_cart_images'] 	        = isset($ext_data['show_cart_images']) ? $ext_data['show_cart_images'] : '';
        $data['cart_images_h'] 		        = isset($ext_data['cart_images_h']) ? $ext_data['cart_images_h'] : '';
        $data['cart_images_w'] 		        = isset($ext_data['cart_images_w']) ? $ext_data['cart_images_w'] :'';

		$data['fixed_cart'] = '';
		$fixed_cart = isset($ext_data['fixed_cart']) ? $ext_data['fixed_cart'] : '1';
		if ($fixed_cart === '1') {
			$fixed_top_offset = isset($ext_data['fixed_top_offset']) ? $ext_data['fixed_top_offset'] : '250';
			$fixed_bottom_offset = isset($ext_data['fixed_bottom_offset']) ? $ext_data['fixed_bottom_offset'] : '120';
			$data['fixed_cart'] = 'data-spy="affix" data-offset-top="'.$fixed_top_offset.'" data-offset-bottom="'.$fixed_bottom_offset.'"';
		}

        $menus = $this->Cart_model->getMenus();

        $data['cart_items'] = array();
        if ($cart_contents = $this->cart->contents()) {															// checks if cart contents is not empty
            foreach ($cart_contents as $row_id => $cart_item) {								// loop through items in cart
	            $menu_data = isset($menus[$cart_item['id']]) ? $menus[$cart_item['id']] : FALSE;				// get menu data based on cart item id from getMenu method in Menus model

	            if (($alert_msg = $this->validateCartMenu($menu_data, $cart_item)) === TRUE) {
		            $cart_image = '';
		            if (isset($data['show_cart_images']) AND $data['show_cart_images'] === '1') {
			            $menu_photo = (!empty($menu_data['menu_photo'])) ? $menu_data['menu_photo'] : 'data/no_photo.png';
			            $cart_image = $this->Image_tool_model->resize($menu_photo, $data['cart_images_h'], $data['cart_images_w']);
		            }

		            // load menu data into array
		            $data['cart_items'][] = array(
			            'rowid'				=> $cart_item['rowid'],
			            'menu_id' 			=> $cart_item['id'],
			            'name' 				=> (strlen($cart_item['name']) > 25) ? strtolower(substr($cart_item['name'], 0, 25)) .'...' : strtolower($cart_item['name']),
			            //add currency symbol and format item price to two decimal places
			            'price' 			=> $this->currency->format($cart_item['price']),
			            'qty' 				=> $cart_item['qty'],
			            'image' 			=> $cart_image,
			            //add currency symbol and format item subtotal to two decimal places
			            'sub_total' 		=> $this->currency->format($cart_item['subtotal']),
			            'comment'           => isset($cart_item['comment']) ? $cart_item['comment'] : '',
			            'options' 			=> ($this->cart->has_options($row_id) == TRUE) ? $this->cart->product_options_string($row_id) : ''
		            );

	            } else {
		            $this->alert->set('custom_now', $alert_msg, 'cart_module');
		            $this->cart->update(array('rowid' => $cart_item['rowid'], 'qty' => '0'));										// pass the cart_data array to add item to cart, if successful
	            }
			}

			if ($this->location->orderType() === '1' AND $this->cart->set_delivery($this->location->deliveryCharge())) {
                $data['delivery'] = $this->currency->format($this->cart->delivery());
			} else {
				$this->cart->set_delivery(0);
			}

			$data['coupon'] = array();
			if ($this->cart->coupon_code()) {
				if (($response = $this->validateCoupon($this->cart->coupon_code())) !== TRUE) {
					$this->alert->set('custom', $response, 'cart_module');
                }

                $data['coupon'] = array(
                    'code' 		=> $this->cart->coupon_code(),
                    'discount' 	=> $this->currency->format($this->cart->coupon_discount())
                );
            }

	        $data['taxes'] = array();
	        if ($taxes = $this->cart->calculate_tax()) {
		        $data['taxes'] = array(
			        'title'     => $taxes['title'],
			        'percent'   => $taxes['percent'],
			        'amount'    => $this->currency->format($taxes['amount']),
		        );
	        }

            $data['sub_total'] 	= $this->currency->format($this->cart->total());
            $data['order_total'] = $this->currency->format($this->cart->order_total());
		}

        $data['cart_alert'] = $this->alert->display('cart_module');

        $this->load->view('cart_module/cart_module', $data);
	}

	public function add() {																		// add() method to add item to cart
		$json = array();

		if ( ! $this->input->is_ajax_request()) {

			$json['error'] = $this->lang->line('alert_bad_request');

		} else if ( ! $this->location->hasSearchQuery() AND $this->config->item('location_order') === '1') { 														// if local restaurant is not selected

			$json['error'] = $this->lang->line('alert_no_search_query');

		} else if ( ! $this->location->isOpened() AND $this->config->item('future_orders') !== '1') { 											// else if local restaurant is not open

			$json['error'] = $this->lang->line('alert_location_closed');

		} else if ( ! $this->input->post('menu_id')) {

			$json['error'] = $this->lang->line('alert_no_menu_selected');

		} else if ($menu_data = $this->Cart_model->getMenu($this->input->post('menu_id'))) {

			$quantity = (is_numeric($this->input->post('quantity'))) ? $this->input->post('quantity') : 0;

			$alert_msg = $this->validateCartMenu($menu_data, array('qty' => $quantity));
			if (!empty($alert_msg) AND is_string($alert_msg)) {
				$json['error'] = $alert_msg;
			}

			$menu_options = $this->Cart_model->getMenuOptions($menu_data['menu_id']);                        // get menu option data based on menu option id from getMenuOption method in Menus model

			$cart_options = $this->validateCartMenuOption($menu_data, $menu_options);
			if (!empty($cart_options) AND is_string($cart_options)) {
				$json['option_error'] = $cart_options;
				$cart_options = array();
			}

			if ($cart_item = $this->cart->get_item($this->input->post('row_id'))) {
				$quantity = ($quantity <= 0) ? $cart_item['qty'] + $quantity : $quantity;
			}

			$cart_data = array(																// create an array of item to be added to cart with id, name, qty, price and options as keys
				'rowid'         => !empty($cart_item['rowid']) ? $cart_item['rowid'] : NULL,
				'id'     		=> $menu_data['menu_id'],
				'name'   		=> $menu_data['menu_name'],
				'qty'    		=> $quantity,
				'price'  		=> $this->cart->format_number(($menu_data['is_special'] === '1') ? $menu_data['special_price'] : $menu_data['menu_price']),
				'comment'       => $this->input->post('comment') ? substr(htmlspecialchars(trim($this->input->post('comment'))), 0, 50) : '',
				'options' 		=> $cart_options
			);
		}

		if (!$json AND !empty($cart_data)) {
			if ($cart_data['rowid'] !== NULL AND $this->cart->update($cart_data)) {
				$json['success'] = $this->lang->line('alert_menu_updated');					// display success message
			} else if ($this->cart->insert($cart_data)) {
				$json['success'] = $this->lang->line('alert_menu_added');					// display success message
			}

			if (!isset($json['success'])) {
				$json['error'] = $this->lang->line('alert_unknown_error');							// display error message
			}
		}

		$this->output->set_output(json_encode($json));											// encode the json array and set final out to be sent to jQuery AJAX
	}

	public function options() {																	// _updateModule() method to update cart
		if ( ! file_exists(EXTPATH .'cart_module/views/cart_options.php')) { 								//check if file exists in views folder
			show_404(); 																		// Whoops, show 404 error page!
		}

		$menu_data = $this->Cart_model->getMenu($this->input->get('menu_id'));

		if ($cart_item = $this->cart->get_item($this->input->get('row_id'))) {
			$data['text_heading'] = $this->lang->line('text_update_heading');
			$quantity = $cart_item['qty'];
		} else {
			$data['text_heading'] = $this->lang->line('text_add_heading');
		}

		$data['menu_id'] 				= $this->input->get('menu_id');
		$data['row_id'] 				= $this->input->get('row_id');
		$data['menu_name'] 				= $menu_data['menu_name'];
		$data['menu_name'] 				= $menu_data['menu_name'];
		$data['menu_price'] 			= $this->currency->format($menu_data['menu_price']);
		$data['description'] 			= $menu_data['menu_description'];
		$data['quantities'] 			= array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
		$data['quantity']               = (isset($quantity)) ? $quantity : 1;
		$data['comment']                = isset($cart_item['comment']) ? $cart_item['comment'] : '';

		$menu_photo = (!empty($menu_data['menu_photo'])) ? $menu_data['menu_photo'] : 'data/no_photo.png';
		$data['menu_image'] = $this->Image_tool_model->resize($menu_photo, '154', '154');

		$data['cart_option_value_ids'] = (!empty($cart_item['options'])) ?
			$this->cart->product_options_ids($this->input->get('row_id')) : array();

		// get menu option data based on menu option id from getMenuOption method in Menus model
		$data['menu_options'] = array();
		if ($menu_options = $this->Cart_model->getMenuOptions($this->input->get('menu_id'))) {
			foreach ($menu_options as $menu_id => $option) {
				$option_values_data = array();

				$option_values = $this->Cart_model->getMenuOptionValues($option['menu_option_id'], $option['option_id']);
				foreach ($option_values as $value) {
					$option_values_data[] = array(
						'option_value_id'		=> $value['option_value_id'],
						'menu_option_value_id'	=> $value['menu_option_value_id'],
						'value'					=> $value['value'],
						'price'					=> (empty($value['new_price']) OR $value['new_price'] == '0.00') ? $this->currency->format($value['price']) : $this->currency->format($value['new_price']),
					);
				}

				$data['menu_options'][$option['menu_option_id']] = array(
					'menu_option_id'	=> $option['menu_option_id'],
					'menu_id'			=> $option['menu_id'],
					'option_id'			=> $option['option_id'],
					'option_name'		=> $option['option_name'],
					'display_type'		=> $option['display_type'],
					'priority'			=> $option['priority'],
					'option_values'		=> $option_values_data
				);
			}
		}

		$data['cart_option_alert'] = $this->alert->display('cart_option_alert');

		$this->load->view('cart_module/cart_options', $data);
	}

	public function order_type() {																// _updateModule() method to update cart
        $json = array();

        if (!$json) {
            $this->load->library('location');
            $this->load->library('cart');

            $order_type = (is_numeric($this->input->post('order_type'))) ? $this->input->post('order_type') : '1';

            if ($order_type === '1') {
                if ( ! $this->location->hasDelivery()) {
                    $json['error'] = $this->lang->line('alert_delivery_unavailable');
                } else if ($this->location->hasSearchQuery() AND $this->location->hasDelivery() AND ! $this->location->checkDeliveryCoverage()) {
                    $json['error'] = $this->lang->line('alert_delivery_coverage');
                } else if ($this->cart->contents() AND ! $this->location->checkMinimumOrder($this->cart->total())) {                            // checks if cart contents is empty
                    $json['error'] = sprintf($this->lang->line('alert_min_delivery_order_total'), $this->currency->format($this->location->minimumOrder()));
                }

            } else if ($order_type === '2') {
                if ( ! $this->location->hasCollection()) {
                    $json['error'] = $this->lang->line('alert_collection_unavailable');
                }
            }

            $this->location->setOrderType($order_type);
        }

        $this->output->set_output(json_encode($json));	// encode the json array and set final out to be sent to jQuery AJAX
    }

	public function coupon() {																	// _updateModule() method to update cart
        $json = array();

        if (!$json AND $this->cart->contents() AND is_string($this->input->post('code'))) {
            switch ($this->input->post('action')) {
                case 'remove':
                    $this->cart->remove_coupon($this->input->post('code'));
                    $json['success'] = $this->lang->line('alert_coupon_removed');						// display success message
                    break;

                case 'add':
                    if (($response = $this->validateCoupon($this->input->post('code'))) === TRUE) {
                        $json['success'] = $this->lang->line('alert_coupon_applied');						// display success message
                    } else {
                        $json['error'] = $response;
                    }
                    break;
                default:
                    $json['redirect'] = site_url(referrer_url());
                    break;
            }
        }

        $this->output->set_output(json_encode($json));											// encode the json array and set final out to be sent to jQuery AJAX
    }

	public function remove() {																	// remove() method to update cart
        $json = array();

        if (!$json) {
            if ($this->cart->update(array ('rowid' => $this->input->post('row_id'), 'qty' => $this->input->post('quantity')))) {											// pass the cart_data array to add item to cart, if successful
                $json['success'] = $this->lang->line('alert_menu_updated');						// display success message
            } else {																			// else redirect to menus page
                $json['redirect'] = site_url(referrer_url());
            }
        }

        $this->output->set_output(json_encode($json));	// encode the json array and set final out to be sent to jQuery AJAX
    }

	public function validateCartMenu($menu_data = array(), $cart_item = array()) {
		// if no menu found in database
		if (empty($menu_data)) {
			return sprintf($this->lang->line('alert_menu_not_found'), $cart_item['name']);
		}

		// if cart quantity is less than minimum quantity
		if ($cart_item['qty'] < $menu_data['minimum_qty']) {
			return sprintf($this->lang->line('alert_qty_is_below_min_qty'), $menu_data['minimum_qty']);
		}

		if ($this->config->item('show_stock_warning') === '1' AND $menu_data['subtract_stock'] === '1') {
			// checks if stock quantity is less than or equal to zero
			if ($menu_data['stock_qty'] <= 0) {
				$stock_warning = sprintf($this->lang->line('alert_out_of_stock'), $menu_data['menu_name']);
			}

			// checks if stock quantity is less than the cart quantity
			if ($menu_data['stock_qty'] < $cart_item['qty']) {
				$stock_warning = sprintf($this->lang->line('alert_low_on_stock'), $menu_data['menu_name'], $menu_data['stock_qty']);
			}

			// Return warning if stock checkout is disabled, else skip
			if (!empty($stock_warning)) {
				return ($this->config->item('stock_checkout') !== '1') ? $stock_warning : TRUE;
			}
		}

		return TRUE;
	}

	public function validateCartMenuOption(&$menu_data, $menu_options) {
		$cart_option_required = FALSE;

		$cart_options = array();
		if ($this->input->post('menu_options') AND is_array($this->input->post('menu_options'))) {
			$option_price = 0;
			foreach ($this->input->post('menu_options') as $menu_option_id => $menu_option) {
				if ($cart_option_required === FALSE AND isset($menu_options[$menu_option_id])) {
					if ($menu_options[$menu_option_id]['required'] === '1'
						AND (empty($menu_option['option_values']) OR ! is_array($menu_option['option_values']))
					) {
						$cart_option_required = $menu_options[$menu_option_id]['option_name'];
						break;
					} else if ( ! empty($menu_option['option_values'])) {
						$option_values = $this->Cart_model->getMenuOptionValues($menu_option['menu_option_id'], $menu_option['option_id']);

						foreach ($menu_option['option_values'] as $key => $value) {
							if (isset($option_values[$value], $option_values[$value]['menu_option_value_id'], $option_values[$value]['value'], $option_values[$value]['price'])) {
								$cart_options[$menu_option_id][] = array(
									'value_id'    => $option_values[$value]['menu_option_value_id'],
									'value_name'  => $option_values[$value]['value'],
									'value_price' => $option_values[$value]['price'],
								);

								$option_price += $option_values[$value]['price'];
							}
						}
					}
				}
			}

			$menu_data['menu_price'] = ( ! empty($option_price)) ? $option_price + $menu_data['menu_price'] : $menu_data['menu_price'];
		}

		if ($cart_option_required !== FALSE OR ($menu_options AND ! $this->input->post('menu_options'))) {
			return sprintf($this->lang->line('alert_option_required'), $cart_option_required);
		}

		return $cart_options;
	}

	private function validateCoupon($code = '') {
		$error = '';

        if (empty($code)) {
            $error = $this->lang->line('alert_coupon_invalid');						// display error message
        } else if (!$coupon = $this->Cart_model->checkCoupon($code)) {
			$error = $this->lang->line('alert_coupon_expired');								// display error message
		} else {
            if (!empty($coupon['order_restriction']) AND $coupon['order_restriction'] !== $this->location->orderType()) {
                $order_type = ($coupon['order_restriction'] === '1') ? $this->lang->line('text_delivery') : $this->lang->line('text_collection');
                $error = sprintf($this->lang->line('alert_coupon_order_restriction'), strtolower($order_type));
            }

            if ($coupon['min_total'] > $this->cart->total()) {
				$error = sprintf($this->lang->line('alert_coupon_not_applied'), $this->currency->format($coupon['min_total']));
			}

			$used = $this->Cart_model->checkCouponHistory($coupon['coupon_id']);

			if (!empty($coupon['redemptions']) AND ($coupon['redemptions']) <= ($used)) {
				$error = $this->lang->line('alert_coupon_maximum_reached');
			}

			if ($coupon['customer_redemptions'] === '1' AND $this->customer->getId()) {
				$customer_used = $this->Cart_model->checkCustomerCouponHistory($coupon['coupon_id'], $this->customer->getId());

				if ($coupon['customer_redemptions'] <= $customer_used) {
					$error = $this->lang->line('alert_coupon_maximum_reached');
				}
			}

            if ($error === '') {
                $this->cart->add_coupon(array('code' => $coupon['code'], 'type' => $coupon['type'], 'discount' => $coupon['discount']));
                return TRUE;
            } else {
                $this->cart->remove_coupon($coupon['code']);
            }
        }


        return $error;
    }
}

/* End of file cart_module.php */
/* Location: ./extensions/cart_module/controllers/cart_module.php */