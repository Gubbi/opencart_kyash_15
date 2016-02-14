<?php 
class ControllerPaymentKyash extends Controller {
	private $error = array(); 
	
	public function install()
	{
		$this->load->model('payment/kyash');
		$this->model_payment_kyash->install();
	}
	
	public function uninstall() 
	{
        $this->load->model('payment/kyash');
		$this->model_payment_kyash->uninstall();
    }

	public function index()
	{
		$this->language->load('payment/kyash');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('kyash', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_payment'] = $this->language->get('text_payment');

		$this->data['entry_public_api_id'] = $this->language->get('entry_public_api_id');
		$this->data['entry_api_secrets'] = $this->language->get('entry_api_secrets');
		$this->data['entry_callback_secret'] = $this->language->get('entry_callback_secret');
		$this->data['entry_callback_url'] = $this->language->get('entry_callback_url');
        $this->data['entry_pg_text'] = $this->language->get('entry_pg_text');
		$this->data['entry_instructions'] = $this->language->get('entry_instructions');

		$this->data['entry_total'] = $this->language->get('entry_total');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['public_api_id'])) {
			$this->data['error_public_api_id'] = $this->error['public_api_id'];
		} else {
			$this->data['error_public_api_id'] = '';
		}

		if (isset($this->error['api_secrets'])) {
			$this->data['error_api_secrets'] = $this->error['api_secrets'];
		} else {
			$this->data['error_api_secrets'] = '';
		}

		if (isset($this->error['callback_secret'])) {
			$this->data['error_callback_secret'] = $this->error['callback_secret'];
		} else {
			$this->data['error_callback_secret'] = '';
		}

		if (isset($this->error['hmac_secret'])) {
			$this->data['error_hmac_secret'] = $this->error['hmac_secret'];
		} else {
			$this->data['error_hmac_secret'] = '';
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/kyash', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('payment/kyash', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['kyash_public_api_id'])) {
			$this->data['public_api_id'] = $this->request->post['kyash_public_api_id'];
		} else {
			$this->data['public_api_id'] = $this->config->get('kyash_public_api_id');
		}

		if (isset($this->request->post['kyash_api_secrets'])) {
			$this->data['api_secrets'] = $this->request->post['kyash_api_secrets'];
		} else {
			$this->data['api_secrets'] = $this->config->get('kyash_api_secrets');
		}


		if (isset($this->request->post['kyash_callback_secret'])) {
			$this->data['callback_secret'] = $this->request->post['kyash_callback_secret'];
		} else {
			$this->data['callback_secret'] = $this->config->get('kyash_callback_secret');
		}

		if (isset($this->request->post['kyash_hmac_secret'])) {
			$this->data['hmac_secret'] = $this->request->post['kyash_hmac_secret'];
		} else {
			$this->data['hmac_secret'] = $this->config->get('kyash_hmac_secret');
		}

		$this->data['callback_url'] = 'URL';

        if (isset($this->request->post['kyash_pg_text'])) {
            $this->data['pg_text'] = $this->request->post['kyash_pg_text'];
        } else {
            $this->data['pg_text'] = $this->config->get('kyash_pg_text');
            if(empty($this->data['pg_text']))
            {
                $this->data['pg_text'] = 'Pay by cash at a shop near me';
            }
        }


		if (isset($this->request->post['kyash_instructions'])) {
			$this->data['instructions'] = $this->request->post['kyash_instructions'];
		} else {
			$this->data['instructions'] = $this->config->get('kyash_instructions');
			if(empty($this->data['instructions']))
			{
				$this->data['instructions'] = 'Please pay at any of the authorized outlets before expiry. You need to mention only the KyashCode and may be asked for your mobile number during payment. No other details needed. Please wait for the confirmation SMS after payment. Remember to take a payment receipt. You can verify the payment status anytime by texting this KyashCode to +91 9243710000';
			}
		}

		if (isset($this->request->post['kyash_total'])) {
			$this->data['kyash_total'] = $this->request->post['kyash_total'];
		} else {
			$this->data['kyash_total'] = $this->config->get('kyash_total'); 
		} 

		if (isset($this->request->post['kyash_geo_zone_id'])) {
			$this->data['kyash_geo_zone_id'] = $this->request->post['kyash_geo_zone_id'];
		} else {
			$this->data['kyash_geo_zone_id'] = $this->config->get('kyash_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['kyash_status'])) {
			$this->data['kyash_status'] = $this->request->post['kyash_status'];
		} else {
			$this->data['kyash_status'] = $this->config->get('kyash_status');
		}

		if (isset($this->request->post['kyash_sort_order'])) {
			$this->data['kyash_sort_order'] = $this->request->post['kyash_sort_order'];
		} else {
			$this->data['kyash_sort_order'] = $this->config->get('kyash_sort_order');
		}

		$this->template = 'payment/kyash.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/kyash')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['kyash_api_secrets']) {
			$this->error['api_secrets'] = $this->language->get('error_api_secrets');
		}
		
		if (!$this->request->post['kyash_public_api_id']) {
			$this->error['public_api_id'] = $this->language->get('error_public_api_id');
		}
	
		if (!$this->request->post['kyash_callback_secret']) {
			$this->error['callback_secret'] = $this->language->get('error_callback_secret');
		}
		
		if (!$this->request->post['kyash_hmac_secret']) {
			$this->error['hmac_secret'] = $this->language->get('error_hmac_secret');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>
