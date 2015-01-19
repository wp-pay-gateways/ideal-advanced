<?php

/**
 * Title: Issuer XML parser
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_DirectoryParser implements Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_Parser {
	/**
	 * Parse
	 *
	 * @param SimpleXMLElement $xml
	 * @return Pronamic_WP_Pay_Gateways_IDealAdvanced_Directory
	 */
	public static function parse( SimpleXMLElement $xml ) {
		$directory = new Pronamic_WP_Pay_Gateways_IDealAdvanced_Directory();

		$timestamp = Pronamic_XML_Util::filter( $xml->directoryDateTimeStamp );
		$directory->setDate( new DateTime( $timestamp ) );

		foreach ( $xml->Issuer as $element ) {
			$issuer = Pronamic_WP_Pay_Gateways_IDealAdvanced_XML_IssuerParser::parse( $element );

			$directory->addIssuer( $issuer );
		}

		return $directory;
	}
}
