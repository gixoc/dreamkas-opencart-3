<?php
class ControllerExtensionModuleDreamkas extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/dreamkas');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_dreamkas', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['error_access_token'])) {
			$data['error_access_token'] = $this->error['error_access_token'];
		} else {
			$data['error_access_token'] = '';
		}

		if (isset($this->error['error_device_id'])) {
			$data['error_device_id'] = $this->error['error_device_id'];
		} else {
			$data['error_device_id'] = '';
		}

		if (isset($this->error['error_payments_ids'])) {
			$data['error_payments_ids'] = $this->error['error_payments_ids'];
		} else {
			$data['error_payments_ids'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/dreamkas', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/dreamkas', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_dreamkas_access_token'])) {
			$data['module_dreamkas_access_token'] = $this->request->post['module_dreamkas_access_token'];
		} else {
			$data['module_dreamkas_access_token'] = $this->config->get('module_dreamkas_access_token');
		}

		if (isset($this->request->post['module_dreamkas_device_id'])) {
			$data['module_dreamkas_device_id'] = $this->request->post['module_dreamkas_device_id'];
		} else {
			$data['module_dreamkas_device_id'] = $this->config->get('module_dreamkas_device_id');
		}

		if (isset($this->request->post['module_dreamkas_tax_mode'])) {
			$data['module_dreamkas_tax_mode'] = $this->request->post['module_dreamkas_tax_mode'];
		} else {
			$data['module_dreamkas_tax_mode'] = $this->config->get('module_dreamkas_tax_mode');
		}

		if (isset($this->request->post['module_dreamkas_tax_type'])) {
			$data['module_dreamkas_tax_type'] = $this->request->post['module_dreamkas_tax_type'];
		} else {
			$data['module_dreamkas_tax_type'] = $this->config->get('module_dreamkas_tax_type');
		}

		if (isset($this->request->post['module_dreamkas_payments_ids'])) {
			$data['module_dreamkas_payments_ids'] = $this->request->post['module_dreamkas_payments_ids'];
		} elseif ($this->config->get('module_dreamkas_payments_ids')) {
			$data['module_dreamkas_payments_ids'] = $this->config->get('module_dreamkas_payments_ids');
		} else {
			$data['module_dreamkas_payments_ids'] = array();
		}

		if (isset($this->request->post['module_dreamkas_paid_order'])) {
			$data['module_dreamkas_paid_order'] = $this->request->post['module_dreamkas_paid_order'];
		} else {
			$data['module_dreamkas_paid_order'] = $this->config->get('module_dreamkas_paid_order');
		}

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		//Get payments
		$this->load->model('setting/extension');
		$paymenttypes = $this->model_setting_extension->getInstalled('payment');

		foreach ($paymenttypes as $type) {
			$this->load->language('extension/payment/' . $type, 'extension');

			$data['paymenttypes'][] = array(
				'code' => $type,
				'name' => $this->language->get('extension')->get('heading_title'),
			);
		}

		if (isset($this->request->post['module_dreamkas_status'])) {
			$data['module_dreamkas_status'] = $this->request->post['module_dreamkas_status'];
		} else {
			$data['module_dreamkas_status'] = $this->config->get('module_dreamkas_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/dreamkas', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/dreamkas')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['module_dreamkas_access_token']) {
			$this->error['error_access_token'] = $this->language->get('error_access_token');
		}

		if (!$this->request->post['module_dreamkas_device_id']) {
			$this->error['error_device_id'] = $this->language->get('error_device_id');
		}

		if (!isset($this->request->post['module_dreamkas_payments_ids'])) {
			$this->error['error_payments_ids'] = $this->language->get('error_payments_ids');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('setting/event');
		$this->model_setting_event->addEvent('dreamkas', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/dreamkas');
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dreamkas` (
			  `order_id` int(11) NOT NULL,
			  `dk_id` VARCHAR(30) NOT NULL,
			  `dk_date` VARCHAR(30) NOT NULL,
			  `dk_status`  VARCHAR(30) NOT NULL,
			  PRIMARY KEY (`order_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci
		");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `dk_tax_type` VARCHAR(50) NOT NULL DEFAULT ''");
	}

	public function uninstall() {
		$this->load->model('setting/event');
		//$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP `dk_tax_type`;");
		$this->model_setting_event->deleteEvent('dreamkas');
	}
}