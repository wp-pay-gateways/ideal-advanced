<?php

/**
 * Title: iDEAL Advanced gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.0
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_Gateway extends Pronamic_WP_Pay_Gateway {
	/**
	 * Constructs and initializes an iDEAL Advanced gateway
	 *
	 * @param Pronamic_WP_Pay_Gateways_IDealAdvanced_Config $config
	 */
	public function __construct( Pronamic_WP_Pay_Gateways_IDealAdvanced_Config $config ) {
		parent::__construct( $config );

		$this->set_method( Pronamic_WP_Pay_Gateway::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );

		// Client
		$client = new Pronamic_WP_Pay_Gateways_IDealAdvanced_Client();
		$client->setAcquirerUrl( $config->get_payment_server_url() );
		$client->merchant_id = $config->merchant_id;
		$client->sub_id = $config->sub_id;
		$client->setPrivateKey( $config->private_key );
		$client->setPrivateKeyPassword( $config->private_key_password );
		$client->setPrivateCertificate( $config->private_certificate );

		$client->directory_request_url   = $config->get_directory_request_url();
		$client->transaction_request_url = $config->get_transaction_request_url();
		$client->status_request_url      = $config->get_status_request_url();

		foreach ( $config->get_certificates() as $certificate ) {
			$client->addPublicCertificate( $certificate );
		}

		$this->client = $client;
	}

	/////////////////////////////////////////////////

	/**
	 * Get issuers
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$result = $this->client->getIssuerLists();

		$lists = null;

		if ( null !== $result ) {
			$lists = $result;
		} elseif ( $error = $this->client->get_error() ) {
			$this->error = $error;
		}

		return $lists;
	}

	/////////////////////////////////////////////////

	/**
	 * Get issuer field
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_issuer_field()
	 */
	public function get_issuer_field() {
		if ( Pronamic_WP_Pay_PaymentMethods::IDEAL === $this->get_payment_method() ) {
			$choices = array();

			$list = $this->get_transient_issuers();

			if ( $list ) {
				foreach ( $list as $name => $issuers ) {
					$options = array();

					foreach ( $issuers as $issuer ) {
						$options[ $issuer->getId() ] = $issuer->getName();
					}

					$choices[] = array(
						'name'    => ( Pronamic_WP_Pay_Gateways_IDealAdvanced_Issuer::LIST_LONG === $name ) ? __( '&mdash; Other banks &mdash;', 'pronamic_ideal' ) : false,
						'options' => $options,
					);
				}
			}

			return array(
				'id'       => 'pronamic_ideal_issuer_id',
				'name'     => 'pronamic_ideal_issuer_id',
				'label'    => __( 'Choose your bank', 'pronamic_ideal' ),
				'required' => true,
				'type'     => 'select',
				'choices'  => $choices,
			);
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Get payment methods
	 *
	 * @return mixed an array or null
	 */
	public function get_payment_methods() {
		return Pronamic_WP_Pay_PaymentMethods::IDEAL;
	}

	/////////////////////////////////////////////////

	/**
	 * Get supported payment methods
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			Pronamic_WP_Pay_PaymentMethods::IDEAL => Pronamic_WP_Pay_PaymentMethods::IDEAL,
		);
	}

	/////////////////////////////////////////////////

	/**
	 * Start transaction with the specified date
	 *
	 * @see Pronamic_WP_Pay_Gateway::start()
	 */
	public function start( Pronamic_Pay_PaymentDataInterface $data, Pronamic_Pay_Payment $payment, $payment_method = null ) {
		// Purchase ID
		$purchase_id = Pronamic_WP_Pay_Gateways_IDeal_Util::get_purchase_id( $this->config->purchase_id, $data, $payment );

		$payment->set_meta( 'purchase_id', $purchase_id );

		// Transaction request message
		$transaction = new Pronamic_WP_Pay_Gateways_IDealAdvanced_Transaction();
		$transaction->setPurchaseId( $purchase_id );
		$transaction->setAmount( $data->get_amount() );
		$transaction->setCurrency( $data->get_currency() );
		$transaction->setExpirationPeriod( 'PT30M' );
		$transaction->setLanguage( $data->get_language() );
		$transaction->setDescription( $data->get_description() );
		$transaction->setEntranceCode( $data->get_entrance_code() );

		$result = $this->client->create_transaction( $transaction, $payment->get_return_url(), $data->get_issuer_id() );

		$error = $this->client->get_error();

		if ( null !== $error ) {
			$this->error = $error;
		} else {
			$payment->set_action_url( $result->issuer->authenticationUrl );
			$payment->set_transaction_id( $result->transaction->getId() );
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Payment
	 *
	 * @see Pronamic_WP_Pay_Gateway::payment()
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function payment( Pronamic_Pay_Payment $payment ) {
		/*
		 * Schedule status requests
		 * http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Advanced_PHP_EN_V2.2.pdf (page 19)
		 *
		 * @todo
		 * Considering the number of status requests per transaction:
		 * - Maximum of five times per transaction;
		 * - Maximum of two times during the expirationPeriod;
		 * - After the expirationPeriod not more often than once per 60 minutes;
		 * - No status request after a final status has been received for a transaction;
		 * - No status request for transactions older than 7 days.
		 */

		/*
		 * The function wp_schedule_single_event() uses the arguments array as an key for the event,
		 * that's why we also add the time to this array, besides that it's also much clearer on
		 * the Cron View (http://wordpress.org/extend/plugins/cron-view/) page
		 */

		$time = time();

		// Examples of possible times when a status request can be executed:

		// 30 seconds after a transaction request is sent
		wp_schedule_single_event( $time + 30, 'pronamic_ideal_check_transaction_status', array( 'payment_id' => $payment->get_id(), 'seconds' => 30 ) );
	}

	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment
	 *
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function update_status( Pronamic_Pay_Payment $payment ) {
		$result = $this->client->get_status( $payment->get_transaction_id() );

		$error = $this->client->get_error();

		if ( null !== $error ) {
			$this->set_error( $error );
		} else {
			$transaction = $result->transaction;

			$payment->set_status( $transaction->getStatus() );
			$payment->set_consumer_name( $transaction->getConsumerName() );
			$payment->set_consumer_account_number( $transaction->getConsumerAccountNumber() );
			$payment->set_consumer_city( $transaction->getConsumerCity() );
		}
	}
}
