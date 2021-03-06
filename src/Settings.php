<?php

/**
 * Title: iDEAL Advanced gateway settings
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.0
 * @since 1.2.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_Settings extends Pronamic_WP_Pay_GatewaySettings {
	public function __construct() {
		// Filters
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );

		// Actions
		add_action( 'admin_init', array( $this, 'maybe_download_private_certificate' ) );
		add_action( 'admin_init', array( $this, 'maybe_download_private_key' ) );
	}

	public function sections( array $sections ) {
		// iDEAL Advanced
		$sections['ideal_advanced'] = array(
			'title'   => __( 'iDEAL Advanced', 'pronamic_ideal' ),
			'methods' => array( 'ideal-advanced' ),
		);

		// Private Key and Certificate
		$sections['ideal_advanced_private'] = array(
			'title'   => __( 'Private Key and Certificate', 'pronamic_ideal' ),
			'methods' => array( 'ideal-advanced' ),
		);

		// Return
		return $sections;
	}

	public function fields( array $fields ) {
		/*
		 * iDEAL Advanced
		 */

		// Private Key Password
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced',
			'meta_key'    => '_pronamic_gateway_ideal_private_key_password',
			'title'       => __( 'Private Key Password', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
		);

		// Private Key
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced',
			'meta_key'    => '_pronamic_gateway_ideal_private_key',
			'title'       => __( 'Private Key', 'pronamic_ideal' ),
			'type'        => 'textarea',
			'callback'    => array( $this, 'field_private_key' ),
			'classes'     => array( 'code' ),
		);

		// Private Certificate
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced',
			'meta_key'    => '_pronamic_gateway_ideal_private_certificate',
			'title'       => __( 'Private Certificate', 'pronamic_ideal' ),
			'type'        => 'textarea',
			'callback'    => array( $this, 'field_private_certificate' ),
			'classes'     => array( 'code' ),
		);

		/*
		 * Private Key and Certificate
		 */

		// Number Days Valid
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced_private',
			'meta_key'    => '_pronamic_gateway_number_days_valid',
			'title'       => __( 'Number Days Valid', 'pronamic_ideal' ),
			'type'        => 'text',
			'description' => __( 'specify the length of time for which the generated certificate will be valid, in days.', 'pronamic_ideal' ),
		);

		// Country
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced_private',
			'meta_key'    => '_pronamic_gateway_country',
			'title'       => __( 'Country', 'pronamic_ideal' ),
			'type'        => 'text',
			'description' => __( '2 letter code [NL]', 'pronamic_ideal' ),
		);

		// State or Province
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced_private',
			'meta_key'    => '_pronamic_gateway_state_or_province',
			'title'       => __( 'State or Province', 'pronamic_ideal' ),
			'type'        => 'text',
			'description' => __( 'full name [Friesland]', 'pronamic_ideal' ),
		);

		// Locality
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced_private',
			'meta_key'    => '_pronamic_gateway_locality',
			'title'       => __( 'Locality', 'pronamic_ideal' ),
			'type'        => 'text',
			'description' => __( 'eg, city', 'pronamic_ideal' ),
		);

		// Organization
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced_private',
			'meta_key'    => '_pronamic_gateway_organization',
			'title'       => __( 'Organization', 'pronamic_ideal' ),
			'type'        => 'text',
			'description' => __( 'eg, company [Pronamic]', 'pronamic_ideal' ),
		);

		// Organization Unit
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced_private',
			'meta_key'    => '_pronamic_gateway_organization_unit',
			'title'       => __( 'Organization Unit', 'pronamic_ideal' ),
			'type'        => 'text',
			'description' => __( 'eg, section', 'pronamic_ideal' ),
		);

		// Common Name
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced_private',
			'meta_key'    => '_pronamic_gateway_common_name',
			'title'       => __( 'Common Name', 'pronamic_ideal' ),
			'type'        => 'text',
			'description' =>
				__( 'eg, YOUR name', 'pronamic_ideal' ) . '<br />' .
				__( 'Do you have an iDEAL subscription with Rabobank or ING Bank, please fill in the domainname of your website.', 'pronamic_ideal' ) . '<br />' .
				__( 'Do you have an iDEAL subscription with ABN AMRO, please fill in "ideal_<strong>company</strong>", where "company" is your company name (as specified in the request for the subscription). The value must not exceed 25 characters.', 'pronamic_ideal' ),
		);

		// Email Address
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'ideal_advanced_private',
			'meta_key'    => '_pronamic_gateway_email',
			'title'       => __( 'Email Address', 'pronamic_ideal' ),
			'type'        => 'text',
		);

		// Return
		return $fields;
	}

	public function field_private_key( $field ) {
		echo '<div>';

		submit_button(
			__( 'Download Private Key', 'pronamic_ideal' ),
			'secondary' , 'download_private_key',
			false
		);

		echo ' ';

		echo '<input type="file" name="_pronamic_gateway_ideal_private_key_file" />';

		echo '</div>';
	}

	public function field_private_certificate( $field ) {
		$certificate = get_post_meta( get_the_ID(), '_pronamic_gateway_ideal_private_certificate', true );

		if ( ! empty( $certificate ) ) {
			$fingerprint = Pronamic_WP_Pay_Gateways_IDealAdvanced_Security::getShaFingerprint( $certificate );
			$fingerprint = str_split( $fingerprint, 2 );
			$fingerprint = implode( ':', $fingerprint );

			echo '<dl>';

			echo '<dt>', esc_html__( 'SHA Fingerprint', 'pronamic_ideal' ), '</dt>';
			echo '<dd>', esc_html( $fingerprint ), '</dd>';

			$info = openssl_x509_parse( $certificate );

			if ( $info ) {
				$date_format = __( 'M j, Y @ G:i', 'pronamic_ideal' );

				if ( isset( $info['validFrom_time_t'] ) ) {
					echo '<dt>', esc_html__( 'Valid From', 'pronamic_ideal' ), '</dt>';
					echo '<dd>', esc_html( date_i18n( $date_format, $info['validFrom_time_t'] ) ), '</dd>';
				}

				if ( isset( $info['validTo_time_t'] ) ) {
					echo '<dt>', esc_html__( 'Valid To', 'pronamic_ideal' ), '</dt>';
					echo '<dd>', esc_html( date_i18n( $date_format, $info['validTo_time_t'] ) ), '</dd>';
				}
			}

			echo '</dl>';
		}

		echo '<div>';

		submit_button(
			__( 'Download Private Certificate', 'pronamic_ideal' ),
			'secondary' , 'download_private_certificate',
			false
		);

		echo ' ';

		echo '<input type="file" name="_pronamic_gateway_ideal_private_certificate_file" />';

		echo '</div>';
	}

	//////////////////////////////////////////////////

	/**
	 * Download private certificate
	 */
	public function maybe_download_private_certificate() {
		if ( filter_has_var( INPUT_POST, 'download_private_certificate' ) ) {
			$post_id = filter_input( INPUT_POST, 'post_ID', FILTER_SANITIZE_STRING );

			$filename = sprintf( 'ideal-private-certificate-%s.cer', $post_id );

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: application/x-x509-ca-cert; charset=' . get_option( 'blog_charset' ), true );

			echo get_post_meta( $post_id, '_pronamic_gateway_ideal_private_certificate', true ); //xss ok

			exit;
		}
	}

	/**
	 * Download private key
	 */
	public function maybe_download_private_key() {
		if ( filter_has_var( INPUT_POST, 'download_private_key' ) ) {
			$post_id = filter_input( INPUT_POST, 'post_ID', FILTER_SANITIZE_STRING );

			$filename = sprintf( 'ideal-private-key-%s.key', $post_id );

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: application/pgp-keys; charset=' . get_option( 'blog_charset' ), true );

			echo get_post_meta( $post_id, '_pronamic_gateway_ideal_private_key', true ); //xss ok

			exit;
		}
	}
}
