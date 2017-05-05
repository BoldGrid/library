<?php
/**
 * BoldGrid Library License Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library\License
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

use Boldgrid\Library\Library\Configs;
use Boldgrid\Library\Library\Api\Call;

/**
 * BoldGrid Library License Class.
 *
 * This class is responsible for calling the API and retrieving the
 * remote license data required for licensed plugins to function.
 *
 * @since 1.0.0
 */
class License {

	/**
	 * @access private
	 *
	 * @var array  $configs Configuration array.
	 * @var object $license BoldGrid license information.
	 * @var object $data    BoldGrid license data.
	 */
	private
		$key,
		$license,
		$data;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->key = $this->setKey();
		$this->license = $this->setLicense();
		$this->data = $this->setData();
		$this->setTransient( $this->getData() );
		$this->checkLicense();
	}

	/**
	 * Set key class property.
	 *
	 * This is the key used to access the transient data stored.
	 *
	 * @since  1.0.0
	 *
	 * @return string $key The key class property.
	 */
	public function setKey() {
		return $this->key = sanitize_key( 'bg_license_data' );
	}

	/**
	 * Sets the transient for the license data.
	 *
	 * @since  1.0.0
	 *
	 * @return bool  Was the transient successfully set?
	 */
	public function setTransient() {
		return ! $this->getTransient() && set_site_transient( $this->getKey(), $this->getLicense(), $this->getExpiration( $this->getData() ) );
	}

	/**
	 * Set the license class property.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed $license The response object or error string of call.
	 */
	private function setLicense() {
		if ( ! $license = $this->getTransient() ) {
			$license = $this->getRemoteLicense();
		}

		return $this->license = $license;
	}

	/**
	 * Check if the current license is valid.
	 *
	 * Is valid if the refresh-by timestamp is not more than 1 day past due.
	 *
	 * @since  1.0.0
	 *
	 * @return bool  Checks if the license data is expired.
	 */
	public function isValid() {
		return ( bool ) $this->getTransient();
	}

	/**
	 * Set the data class property.
	 *
	 * @since  1.0.0
	 *
	 * @return object $data The license data class property.
	 */
	private function setData() {
		if ( $license = $this->getLicense() ) {
			$data = json_decode(
				openssl_decrypt(
					$license->data,
					$license->cipher,
					$license->key,
					0,
					base64_decode( $license->iv )
				)
			);
		}

		return $this->data = $data;
	}

	/**
	 * Checks the license data.
	 *
	 * @since 1.0.0
	 */
	public function checkLicense() {
		if ( ! $this->isValid() ) {
			deactivate_plugins( Configs::get( 'pluginFile' ) );
			if ( is_admin() ) {
				add_action( 'admin_notices', array( $this, 'licenseNotice' ) );
			}
		}
	}

	/**
	 * Displays the license notice in the WordPress admin.
	 *
	 * @since 1.0.0
	 */
	public function licenseNotice() {
		include dirname( __DIR__ ) . '/Views/License.php';
	}

	/**
	 * Get the latest license data from the API server.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed $response The remote license data object or error string.
	 */
	private function getRemoteLicense() {
		$call = new Call( Configs::get( 'api' ) . '/api/plugin/get-license' );

		if ( ! $response = $call->getError() ) {
			$response = $call->getResponse()->result->data;
		}

		return $response;
	}

	/**
	 * Get the key class property.
	 *
	 * @since  1.0.0
	 *
	 * @return string $key The key class property.
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Get the transient containing license data.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed The API license data from the WordPress transient.
	 */
	public function getTransient() {
		return get_site_transient( $this->getKey() );
	}

	/**
	 * Gets the expiration date from data passed.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $data The data object from API license request.
	 *
	 * @return string       The expiration of the data stored.
	 */
	private function getExpiration( $data ) {
		return strtotime( $data->refreshBy ) - strtotime( 'now' );
	}

	/**
	 * Get the license class property.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed $license The license class property.
	 */
	public function getLicense() {
		return $this->license;
	}

	/**
	 * Get the data class property.
	 *
	 * @since  1.0.0
	 *
	 * @return $data The data class property.
	 */
	public function getData() {
		return $this->data;
	}
}
