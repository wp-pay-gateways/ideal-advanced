<?php

/**
 * Title: Security
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_Security {
	/**
	 * Indicator for the begin of an certificate
	 *
	 * @var string
	 */
	const CERTIFICATE_BEGIN = '-----BEGIN CERTIFICATE-----';

	/**
	 * Indicator for the end of an certificate
	 *
	 * @var string
	 */
	const CERTIFICATE_END = '-----END CERTIFICATE-----';

	//////////////////////////////////////////////////

	/**
	 * Get the sha1 fingerprint from the specified certificate
	 *
	 * @param string $certificate
	 * @return fingerprint or null on failure
	 */
	public static function getShaFingerprint( $certificate ) {
		return self::getFingerprint( $certificate, 'sha1' );
	}

	/**
	 * Get the md5 fingerprint from the specified certificate
	 *
	 * @param string $certificate
	 * @return fingerprint or null on failure
	 */
	public static function getMd5Fingerprint( $certificate ) {
		return self::getFingerprint( $certificate, 'md5' );
	}

	/**
	 * Get the fingerprint from the specified certificate
	 *
	 * @param string $certificate
	 * @return fingerprint or null on failure
	 */
	public static function getFingerprint( $certificate, $hash = null ) {
		$fingerprint = null;

		// The openssl_x509_read() function will throw an warning if the supplied
		// parameter cannot be coerced into an X509 certificate
		$resource = @openssl_x509_read( $certificate );
		if ( false !== $resource ) {
			$output = null;

			$result = openssl_x509_export( $resource, $output );
			if ( false !== $result ) {
				$output = str_replace( self::CERTIFICATE_BEGIN, '', $output );
				$output = str_replace( self::CERTIFICATE_END, '', $output );

				// Base64 decode
				$fingerprint = base64_decode( $output );

				// Hash
				if ( null !== $hash ) {
					$fingerprint = hash( $hash, $fingerprint );
				}
			} // @todo else what to do?
		} // @todo else what to do?

		return $fingerprint;
	}

	//////////////////////////////////////////////////

	/**
	 * Function to sign a message
	 *
	 * @param filename of the private key
	 * @param message to sign
	 * @return signature
	 */
	public static function signMessage( $privateKey, $privateKeyPassword, $data ) {
		$signature = null;

		$resource = openssl_pkey_get_private( $privateKey, $privateKeyPassword );
		if ( false !== $resource ) {
			// Compute signature
			$computed = openssl_sign( $data, $result, $resource );
			if ( $computed ) {
				$signature = $result;
			}

			// Free the key from memory
			openssl_free_key( $resource );
		} // @todo else what to do?

		return $signature;
	}
}
