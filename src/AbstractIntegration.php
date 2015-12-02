<?php

/**
 * Title: iDEAL Advanced abstract integration
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
abstract class Pronamic_WP_Pay_Gateways_IDealAdvanced_AbstractIntegration extends Pronamic_WP_Pay_Gateways_AbstractIntegration {
	public function get_config_factory_class() {
		return 'Pronamic_WP_Pay_Gateways_IDealAdvanced_ConfigFactory';
	}

	public function get_config_class() {
		return 'Pronamic_WP_Pay_Gateways_IDealAdvanced_Config';
	}

	public function get_settings_class() {
		return array(
			'Pronamic_WP_Pay_Gateways_IDeal_Settings',
			'Pronamic_WP_Pay_Gateways_IDealBasic_Settings',
		);
	}

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_IDealAdvanced_Gateway';
	}
}
