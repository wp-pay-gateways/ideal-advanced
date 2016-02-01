<?php

/**
 * Title: iDEAL status response XML message
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_StatusResponseMessage extends Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_ResponseMessage {
	/**
	 * The document element name
	 *
	 * @var string
	 */
	const NAME = 'AcquirerStatusRes';

	//////////////////////////////////////////////////

	public $transaction;

	public $signature;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an status response message
	 */
	public function __construct() {
		parent::__construct( self::NAME );
	}

	//////////////////////////////////////////////////

	/**
	 * Parse the specified XML into an directory response message object
	 *
	 * @param SimpleXMLElement $xml
	 */
	public static function parse( SimpleXMLElement $xml ) {
		$message = new self();

		$message->transaction = Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_TransactionParser::parse( $xml->Transaction );
		// $message->transaction = SignatureParser::parse($xml->Signature);

		return $message;
	}
};
