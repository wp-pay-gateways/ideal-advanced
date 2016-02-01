<?php

/**
 * Title: Error XML parser
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_ErrorParser implements Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_Parser {
	/**
	 * Parse iDEAL Advanced error from the specified XML element
	 *
	 * @param SimpleXMLElement $xml
	 * @return Pronamic_WP_Pay_Gateways_IDealAdvanced_Error
	 */
	public static function parse( SimpleXMLElement $xml ) {
		$error = new Pronamic_WP_Pay_Gateways_IDealAdvanced_Error();

		if ( $xml->errorCode ) {
			$error->set_code( Pronamic_WP_Pay_XML_Security::filter( $xml->errorCode ) );
		}

		if ( $xml->errorMessage ) {
			$error->set_message( Pronamic_WP_Pay_XML_Security::filter( $xml->errorMessage ) );
		}

		if ( $xml->errorDetail ) {
			$error->set_detail( Pronamic_WP_Pay_XML_Security::filter( $xml->errorDetail ) );
		}

		if ( $xml->suggestedAction ) {
			$error->set_suggested_action( Pronamic_WP_Pay_XML_Security::filter( $xml->suggestedAction ) );
		}

		if ( $xml->suggestedExpirationPeriod ) {
			$error->set_suggested_expiration_period( Pronamic_WP_Pay_XML_Security::filter( $xml->suggestedExpirationPeriod ) );
		}

		if ( $xml->consumerMessage ) {
			$error->set_consumer_message( Pronamic_WP_Pay_XML_Security::filter( $xml->consumerMessage ) );
		}

		return $error;
	}
}
