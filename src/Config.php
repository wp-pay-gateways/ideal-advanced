<?php

/**
 * Title: iDEAL Advanced config
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_Config extends Pronamic_WP_Pay_GatewayConfig {
	public $merchant_id;

	public $sub_id;

	public $private_key_password;

	public $private_key;

	public $private_certificate;

	public function get_payment_server_url() {
		return $this->payment_server_url;
	}

	public function get_directory_request_url() {
		return $this->get_payment_server_url();
	}

	public function get_transaction_request_url() {
		return $this->get_payment_server_url();
	}

	public function get_status_request_url() {
		return $this->get_payment_server_url();
	}

	public function get_certificates() {
		return array();
	}
}
