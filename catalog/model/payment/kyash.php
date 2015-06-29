<?php
require_once(DIR_SYSTEM . 'lib/common.php');

class ModelPaymentKyash extends Model {
    use KyashModel;

    function __construct($params) {
        parent::__construct($params);
        $this->load->model('checkout/order');
        $this->model_order = $this->model_checkout_order;
        $this->init();
    }

	public function getMethod($address, $total) {
		$this->language->load('payment/kyash');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('kyash_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('kyash_total') > 0 && $this->config->get('kyash_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('kyash_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}	

		$method_data = array();

		if ($status) { 
			$additional = $this->getShopsLink($address['postcode']);
		
			$method_data = array(
				'code'       => 'kyash',
				'title'      => sprintf($this->language->get('text_title').$additional, $this->config->get('kyash_pg_text')?: 'Kyash - Pay at a nearby Shop'),
				'sort_order' => $this->config->get('kyash_sort_order')
			);
		}

		return $method_data;
	}
	
	public function getShopsLink($postcode)
	{
		$this->language->load('payment/kyash');
		if(empty($postcode))
		{
			$postcode = 'Enter Pincode';
		}
		$url = $this->url->link('payment/kyash/getPaymentPoints');
        $css = '<link href="catalog/view/theme/default/stylesheet/kyash.css" rel="stylesheet">';

		$html = '
		<span id="kyash_postcode_payment">
			<a href="javascript:void(0);" onclick=\'openShops("'.$url.'","")\' id="kyash_open">
			See nearby shops
			</a>
		   
			<span id="kyash_postcode_payment_sub">
				<input type="text" class="input-text" id="kyash_postcode" value="'.$postcode.'" maxlength="12" />
				<input type="button" class="button" id="kyash_postcode_button" value="See nearby shops" onclick=\'pullNearByShops("'.$url.'","")\'>
				<a href="javascript:void(0);" onclick="closeShops()" id="kyash_close" style="float:right">X</a>
			</span>
		</span>
		<div style="display: none" id="see_nearby_shops_container" class="content">
		</div>';

        $js = '<script src="catalog/view/javascript/kyash.js" type="text/javascript"></script>';

        return $css.$html.$js;
	}

	public function getOrderParams($order_info)
	{
		$address1 = $order_info['payment_address_1'];
		if ($order_info['payment_address_2']) {
			$address1 .= ','.$order_info['payment_address_2'];
		}
		
		$address2 = $order_info['shipping_address_1'];
		if ($order_info['shipping_address_2']) {
			$address2 .= ','.$order_info['shipping_address_2'];
		}
		
        $params = array (
            'order_id' => $order_info['order_id'],
			'amount' => $order_info['total'],
			'billing_contact.first_name' => $order_info['payment_firstname'],
			'billing_contact.last_name' => $order_info['payment_lastname'],
			'billing_contact.email' => $order_info['email'],
			'billing_contact.address' => $address1,
			'billing_contact.city' => $order_info['payment_city'],
			'billing_contact.state' => $order_info['payment_zone'],
			'billing_contact.pincode' => $order_info['payment_postcode'],
            'billing_contact.phone' => $order_info['telephone'],
            'shipping_contact.first_name' => $order_info['shipping_firstname'],
			'shipping_contact.last_name' => $order_info['shipping_lastname'],
			'shipping_contact.address' => $address2,
			'shipping_contact.city' => $order_info['shipping_city'],
			'shipping_contact.state' => $order_info['shipping_zone'],
            'shipping_contact.pincode' => $order_info['shipping_postcode'],
            'shipping_contact.phone' => $order_info['telephone']
        );

		return http_build_query($params);
    }

    public function updateKyashCode($order_id, $code, $status, $kyash_expires, $additional) {
        $this->language->load('payment/kyash');
        $method = $this->language->get('text_title') . $additional;
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET kyash_code = '" . $code . "', kyash_status = '" . $status ."', kyash_expires = '" . $kyash_expires . "', payment_method = '" . $method . "' WHERE order_id = '" . (int)$order_id . "'");
    }
	
	public function updateKyashStatus($order_id,$status)
    {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET kyash_status = '".$status."' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getSuccessContent($order_id)
	{
		if($order_id > 0)
		{
            list($kyash_code, ,$expires_on) = $this->getOrderInfo($order_id);
			if(empty($kyash_code))
				return '';

            $order_info = $this->model_checkout_order->getOrder($order_id);
            $postcode = $order_info['payment_postcode'];

            $dateTime = new DateTime("@".$expires_on);
            $dateTime->setTimeZone(new DateTimeZone('Asia/Kolkata'));
            $kc_expires_on = $dateTime->format("j M Y, g:i A");
            if (empty($postcode)) {
                $postcode = 'Enter Pincode';
            }

			$url = $this->url->link('payment/kyash/getPaymentPoints2');
            $css = '<link href="catalog/view/theme/default/stylesheet/kyash.css" rel="stylesheet">';
		
			$html = '
			<div class="kyash_succcess_instructions" style="border-top:1px solid #ededed;">
				<h1>KyashCode: '.$kyash_code.'</h1>
				<p><span>KyashCode expires on ' . $kc_expires_on . '</span></p>
				<p>' . nl2br(html_entity_decode($this->settings['kyash_instructions'])) . '</p>
			</div>
			<div class="kyash_succcess_instructions">
				<input type="text" class="input-text" id="postcode" value="'.$postcode.'" maxlength="12" style="width:120px; text-align:center" 
				onblur="if(this.value ==\'\'){this.value=\'Enter Pincode\';}" 
				onclick="if(this.value == \'Enter Pincode\'){this.value=\'\';}" />
				<input type="button" class="button" id="kyash_postcode_button" value="See nearby shops" onclick="preparePullShops()">
			</div>
			<div style="display: none" id="see_nearby_shops_container" class="content">
			</div>
			';

            $js = '
			<script src="catalog/view/javascript/kyash_success.js" type="text/javascript"></script>
			<script>preparePullShops("' . $url . '");</script>
			';
        	return $css.$html.$js;
		}
	}
}
?>