<?php
class ControllerExtensionModuleDreamkas extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/dreamkas');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dreamkas', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			//$this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			$this->response->redirect($this->url->link('extension/module/dreamkas', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_access_token'] = $this->language->get('entry_access_token');
		$data['entry_device_id'] = $this->language->get('entry_device_id');
		$data['entry_tax_mode'] = $this->language->get('entry_tax_mode');
		$data['entry_tax_type'] = $this->language->get('entry_tax_type');
		$data['entry_payments_ids'] = $this->language->get('entry_payments_ids');
		$data['entry_paid_order'] = $this->language->get('entry_paid_order');
		$data['text_tax_default'] = $this->language->get('text_tax_default');
		$data['text_tax_simple'] = $this->language->get('text_tax_simple');
		$data['text_tax_simple_wo'] = $this->language->get('text_tax_simple_wo');
		$data['text_tax_envd'] = $this->language->get('text_tax_envd');
		$data['text_tax_agricult'] = $this->language->get('text_tax_agricult');
		$data['text_tax_patent'] = $this->language->get('text_tax_patent');
		$data['text_tax_nds_no_tax'] = $this->language->get('text_tax_nds_no_tax');
		$data['text_tax_nds_0'] = $this->language->get('text_tax_nds_0');
		$data['text_tax_nds_10'] = $this->language->get('text_tax_nds_10');
		$data['text_tax_nds_18'] = $this->language->get('text_tax_nds_18');
		$data['text_tax_nds_10_calculated'] = $this->language->get('text_tax_nds_10_calculated');
		$data['text_tax_nds_18_calculated'] = $this->language->get('text_tax_nds_18_calculated');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['access_token'])) {
			$data['error_access_token'] = $this->error['access_token'];
		} else {
			$data['error_access_token'] = '';
		}

		if (isset($this->error['device_id'])) {
			$data['error_device_id'] = $this->error['device_id'];
		} else {
			$data['error_device_id'] = '';
		}

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/dreamkas', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['action'] = $this->url->link('extension/module/dreamkas', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['dreamkas_access_token'])) {
			$data['access_token'] = $this->request->post['dreamkas_access_token'];
		} else {
			$data['access_token'] = $this->config->get('dreamkas_access_token');
		}

		if (isset($this->request->post['dreamkas_device_id'])) {
			$data['device_id'] = $this->request->post['dreamkas_device_id'];
		} else {
			$data['device_id'] = $this->config->get('dreamkas_device_id');
		}

		if (isset($this->request->post['dreamkas_tax_mode'])) {
			$data['tax_mode'] = $this->request->post['dreamkas_tax_mode'];
		} else {
			$data['tax_mode'] = $this->config->get('dreamkas_tax_mode');
		}

		if (isset($this->request->post['dreamkas_tax_type'])) {
			$data['tax_type'] = $this->request->post['dreamkas_tax_type'];
		} else {
			$data['tax_type'] = $this->config->get('dreamkas_tax_type');
		}

		if (isset($this->request->post['dreamkas_payments_ids'])) {
			$data['payments_ids'] = $this->request->post['dreamkas_payments_ids'];
		} else {
			$data['payments_ids'] = $this->config->get('dreamkas_payments_ids');
		}

		if (isset($this->request->post['dreamkas_paid_order'])) {
			$data['paid_order'] = $this->request->post['dreamkas_paid_order'];
		} else {
			$data['paid_order'] = $this->config->get('dreamkas_paid_order');
		}

		if (!isset($data['payments_ids'])) {
			$data['payments_ids'] = array();
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

		if (isset($this->request->post['dreamkas_status'])) {
			$data['status'] = $this->request->post['dreamkas_status'];
		} else {
			$data['status'] = $this->config->get('dreamkas_status');
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
		if (!$this->request->post['dreamkas_access_token']) {
			$this->error['error_access_token'] = $this->language->get('error_access_token');
		}
		if (!$this->request->post['dreamkas_device_id']) {
			$this->error['error_device_id'] = $this->language->get('error_device_id');
		}
		if (!isset($this->request->post['dreamkas_payments_ids'])) {
			//$this->error['error_payments_ids'] = $this->language->get('error_payments_ids');
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
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP `dk_tax_type`;");
		$this->model_setting_event->deleteEvent('dreamkas');
	}
}