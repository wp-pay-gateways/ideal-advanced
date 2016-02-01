<?php

/**
 * Title: iDEAL Advanced error test
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_IDealAdvanced_ErrorTest extends PHPUnit_Framework_TestCase {
	public function testToStringError() {
		$error = new Pronamic_WP_Pay_Gateways_IDealAdvanced_Error();
		$error->set_code( '1' );
		$error->set_message( 'Error' );

		$string = (string) $error;

		$expected = '1 Error';

		$this->assertEquals( $expected, $string );
	}
}
