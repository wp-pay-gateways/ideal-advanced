<?php

/**
 * Title: Issuer XML parser
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_IssuerParser implements Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_Parser {
	/**
	 * Parse
	 *
	 * @param SimpleXMLElement $xml
	 */
	public static function parse( SimpleXMLElement $xml ) {
		$issuer = new Pronamic_WP_Pay_Gateways_IDealAdvanced_Issuer();

		if ( $xml->issuerID ) {
			$issuer->setId( Pronamic_WP_Pay_XML_Security::filter( $xml->issuerID ) );
		}

		if ( $xml->issuerName ) {
			$issuer->setName( Pronamic_WP_Pay_XML_Security::filter( $xml->issuerName ) );
		}

		if ( $xml->issuerList ) {
			$issuer->setList( Pronamic_WP_Pay_XML_Security::filter( $xml->issuerList ) );
		}

		if ( $xml->issuerAuthenticationURL ) {
			$issuer->authenticationUrl = Pronamic_WP_Pay_XML_Security::filter( $xml->issuerAuthenticationURL );
		}

		return $issuer;
	}
}
