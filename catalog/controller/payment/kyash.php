<?php
class ControllerPaymentKyash extends Controller
{
    function __construct($params)
    {
        parent::__construct($params);
        $this->load->model('setting/setting');
        $this->settings = $this->model_setting_setting->getSetting('kyash');

        $this->load->library('log');
        $this->logger = new Log('kyash.log');

        require_once(DIR_SYSTEM . 'lib/KyashPay.php');
        $this->api = new KyashPay($this->settings['kyash_public_api_id'], $this->settings['kyash_api_secrets'], $this->settings['kyash_callback_secret'], $this->settings['kyash_hmac_secret']);
        $this->api->setLogger($this->logger);
    }

    protected function index()
    {

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/kyash/kyash.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/kyash/kyash.tpl';
        } else {
            $this->template = 'default/template/payment/kyash/kyash.tpl';
        }

        $this->render();
    }

    public function placeorder()
    {

        $this->load->model('checkout/order');
        $this->load->model('payment/kyash');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $params = $this->model_payment_kyash->getOrderParams($order_info);
        $response = $this->api->createKyashCode($params);

        $json = array();
        if (isset($response['status']) && $response['status'] == 'error') {
            $json['error'] = 'Payment error. ' . $response['message'];
        } else {
            $message = '';
            $expires_on = $response['expires_on'];
            $this->model_checkout_order->confirm($this->session->data['order_id'], 1, $message, false);
            $this->model_payment_kyash->updateKyashCode($order_info['order_id'], $response['id'], 'pending', $expires_on, ', KyashCode - ' . $response['id']);
            $json['success'] = $this->url->link('checkout/success') . '&order_id=' . $order_info['order_id'];
        }
        $this->response->setOutput(json_encode($json));
    }

    public function getPaymentPoints()
    {
        $this->displayPaymentPointsList('/template/payment/kyash/payment_points.tpl');
    }

    public function getPaymentPoints2()
    {
        $this->displayPaymentPointsList('/template/payment/kyash/payment_points2.tpl');
    }

    protected function displayPaymentPointsList($success_tpl_path)
    {
        $pincode = $this->request->get['postcode'];
        $response = $this->api->getPaymentPoints($pincode);
        $this->data = array();
        if (isset($response['status']) && $response['status'] === 'error') {
            $this->data['error'] = $response['message'];

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/kyash/error.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/kyash/error.tpl';
            } else {
                $this->template = 'default/template/payment/kyash/error.tpl';
            }
        } else {
            $this->data['payments'] = $response;

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . $success_tpl_path)) {
                $this->template = $this->config->get('config_template') . $success_tpl_path;
            } else {
                $this->template = 'default/template/payment/kyash/payment_points.tpl';
            }
        }

        echo $this->response->setOutput($this->render());
    }

    public function handler()
    {
        $this->load->model('checkout/order');
        $this->load->model('payment/kyash');

        $order_id = trim($this->request->post['order_id']);
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if (!$order_info) {
            $this->logger->write("Order " . $order_id . " is not in our records.");
            header("HTTP/1.1 404 Not Found");
            exit;
        }

        $url = $this->url->link('payment/kyash/handler');
        $updater = new KyashUpdater($this->model_checkout_order, $this->model_payment_kyash, $order_id);
        list($kyash_code, $kyash_status,) = $this->model_payment_kyash->getOrderInfo($order_id);
        $this->api->callback_handler($updater, $kyash_code, $kyash_status, $url);
    }
}

class KyashUpdater {
    public $order = NULL;
    public $kyash = NULL;
    public $order_id = NULL;

    public function __construct($order, $kyash, $order_id) {
        $this->order = $order;
        $this->kyash = $kyash;
        $this->order_id = $order_id;
    }

    public function update($status, $comment) {
        if ($status === 'paid') {
            $this->order->update($this->order_id, 2, $comment);
            $this->kyash->updateKyashStatus($this->order_id, 'paid');
        } else if ($status === 'expired') {
            $this->order->update($this->order_id, 14, $comment);
            $this->kyash->updateKyashStatus($this->order_id, 'expired');
        }
    }
}
?>