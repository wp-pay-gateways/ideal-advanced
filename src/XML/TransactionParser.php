<?php

/**
 * Title: Transaction XML parser
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_TransactionParser implements Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_Parser {
	/**
	 * Parse the specified XML element into an iDEAL transaction object
	 *
	 * @param SimpleXMLElement $xml
	 */
	public static function parse( SimpleXMLElement $xml ) {
		$transaction = new Pronamic_WP_Pay_Gateways_IDealAdvanced_Transaction();

		if ( $xml->transactionID ) {
			$transaction->setId( Pronamic_WP_Pay_XML_Security::filter( $xml->transactionID ) );
		}

		if ( $xml->purchaseID ) {
			$transaction->setPurchaseId( Pronamic_WP_Pay_XML_Security::filter( $xml->purchaseID ) );
		}

		if ( $xml->status ) {
			$transaction->setStatus( Pronamic_WP_Pay_XML_Security::filter( $xml->status ) );
		}

		if ( $xml->consumerName ) {
			$transaction->setConsumerName( Pronamic_WP_Pay_XML_Security::filter( $xml->consumerName ) );
		}

		if ( $xml->consumerAccountNumber ) {
			$transaction->setConsumerAccountNumber( Pronamic_WP_Pay_XML_Security::filter( $xml->consumerAccountNumber ) );
		}

		if ( $xml->consumerCity ) {
			$transaction->setConsumerCity( Pronamic_WP_Pay_XML_Security::filter( $xml->consumerCity ) );
		}

		return $transaction;
	}
}
