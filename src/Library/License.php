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
	 * @since 1.0.0
	 *
	 * @var array  $key     Transient data key.
	 * @var object $license BoldGrid license details.
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
		if ( is_object( $this->getLicense() ) ) {
			$this->data = $this->setData();
			$this->setTransient( $this->getData() );
			$licenseData = array( 'licenseData' => $this->getData() );
			Configs::set( $licenseData, Configs::get() );
		} else {
			if ( Configs::get( 'licenseActivate' ) ) {
				Filter::add( $this );
				$this->licenseNotice();
			}
		}
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
	private function setKey() {
		return $this->key = sanitize_key( 'bg_license_data' );
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
	 * Sets the transient for the license data.
	 *
	 * @since  1.0.0
	 *
	 * @return bool  Was the transient successfully set?
	 */
	private function setTransient() {
		return ! $this->getTransient() && set_site_transient( $this->getKey(), $this->getLicense(), $this->getExpiration( $this->getData() ) );
	}

	/**
	 * Check if the current license is valid.
	 *
	 * Is valid if the refresh-by timestamp is not more than 1 day past due.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed  Checks if the license data is available.
	 */
	private function isValid() {
		$data = $this->getTransient();
		$valid = array( 'key', 'cipher', 'iv', 'data' );
		if ( is_object( $data ) ) {
			$props = array_keys( get_object_vars( $data ) );
			$valid = array_diff( $valid, $props );
		}

		return empty( $valid );
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
	 * Displays the license notice in the WordPress admin.
	 *
	 * @since 1.0.0
	 */
	protected function licenseNotice() {
		if ( ! $this->isValid() ) {
			new Notice( 'invalidLicense' );
			// Disables the activate message.
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}

	/**
	 * Runs plugin deactivation when deactivate_plugins is available.
	 *
	 * @since 1.0.0
	 *
	 * @hook: admin_init
	 */
	public function deactivate() {
		if ( ! $this->isValid() ) {
			delete_site_transient( $this->getKey() );
			deactivate_plugins( Configs::get( 'file' ) );
		}
	}

	/**
	 * Get the latest license data from the API server.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed $response The remote license data object or error string.
	 */
	private function getRemoteLicense() {
		$call = new Api\Call( Configs::get( 'api' ) . '/api/plugin/getLicense' );
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
	protected function getKey() {
		return $this->key;
	}

	/**
	 * Get the transient containing license data.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed The API license data from the WordPress transient.
	 */
	protected function getTransient() {
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
	protected function getLicense() {
		return $this->license;
	}

	/**
	 * Get the data class property.
	 *
	 * @since  1.0.0
	 *
	 * @return $data The data class property.
	 */
	protected function getData() {
		return $this->data;
	}

	/**
	 * Get license validation.
	 *
	 * @nohook
	 *
	 * @return [type] [description]
	 */
	public function getValid() {
		return $this->isValid();
	}
}
