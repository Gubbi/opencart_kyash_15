<?php
require_once(DIR_SYSTEM . 'lib/common.php');

class ModelPaymentKyash extends KyashModel {

    function __construct($params) {
        parent::__construct($params);
        $this->load->model('sale/order');
        $this->model_order = $this->model_sale_order;
    }

	public function install() 
	{
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD kyash_code VARCHAR(50)");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD kyash_status VARCHAR(50)");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD kyash_expires INT(10) UNSIGNED NOT NULL");
	}

	public function uninstall(){}
}
?>
