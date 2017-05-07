<?php
/**
 * BoldGrid Library Key Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library Key Class.
 *
 * This class is responsible for verifying BoldGrid connect key information
 * stored in a user's WordPress.
 *
 * @since 1.0.0
 */
class Key {

	/**
	 * @access protected
	 *
	 * @since 1.0.0
	 *
	 * @var mixed $license License object.
	 */
	protected $license;

	/**
	 * @access protected
	 *
	 * @since 1.0.0
	 *
	 * @var bool $valid Is key valid?
	 */
	protected static $valid;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param array $configs Plugin configuration array.
	 */
	public function __construct() {
		$this->setValid();
		$this->setLicense();
		$this->setNotice();
	}

	/**
	 * Set the license class property.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed $license The license object or false.
	 */
	public function setLicense() {
		return $this->license = self::$valid ? new License() : false;
	}

	/**
	 * Set $valid class property.
	 *
	 * @since  1.0.0
	 *
	 * @return bool  $valid Is transient/key valid?
	 */
	public function setValid() {

		// The API Transient data.
		$data = Configs::get( 'apiData' );

		// The API Key.
		$key = Configs::get( 'key' );

		// Flag.
		$valid = false;

		// If the API data is available in the transient.
		if ( $data && ! empty( $data->license_status ) ) {

			// Let the transient set it's own reported status.
			$valid = $data->license_status;
		}

		// If we're still not valid, have no transient data, and have a key stored already.
		if ( ! $valid && ! $data && $key ) {

			// Make a call to get data.
			$data = $this->verify();

			// Did data come back in the response?
			if ( $data ) {

				// Errors come back as strings and success comes back as an object we can use.
				if ( is_object( $data ) ) {

					// Update the data since we know
					$this->save( $data, $key );

					// Set our flag.
					$valid = true;
				}
			}
		}

		return self::$valid = $valid;
	}

	/**
	 * Add the appropriate notice to the WordPress dashboard.
	 *
	 * This method will set the connection issue notice or the prompt key
	 * notice to the user's WordPress dashboard.
	 *
	 * @since 1.0.0
	 */
	public function setNotice() {

		// If we already have transient data saying the API is not available.
		if ( '0' === get_site_transient( 'boldgrid_available' ) ) {
			return new Notice( 'ConnectionIssue' );
		}

		// If we don't have a key stored, or this is not a valid response when calling.
		if ( ! Configs::get( 'key' ) || ! self::$valid ) {
			return new Notice( 'keyPrompt', $this );
		}
	}

	/**
	 * Call the API check-version endpoint for API data.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args     API arguments to pass.
	 *
	 * @return mixed $response Object for valid response, or error message as string.
	 */
	public function callCheckVersion( $args ) {
		$call = new Api\Call( Configs::get( 'api' ) . '/api/plugin/checkVersion', $args );

		// If there's an error set that as the response.
		if ( ! $response = $call->getError() ) {

			// If the response is successful, then retrieve the response.
			$response = $call->getResponse()->result->data;
		}

		// Return the API response.
		return $response;
	}

	/**
	 * Validates the API key and returns details on if it is valid as well as version.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed The BoldGrid API Data object or a message string on failure.
	 */
	public function verify( $key = null ) {
		$key = $key ? $key : Configs::get( 'key' );

		// Make an API call for API data.
		$data = $this->callCheckVersion( array( 'key' => $key ) );

		// Handle the response.
		if ( is_object( $data ) ) {

			// Let the transient data set it's own validity.
			$data->license_status = true;
		}

		// Return back the transient data object.
		return $data;
	}

	/**
	 * Saves data based on API response.
	 *
	 * @since  1.0.0
	 *
	 * @param  Object $data API response data.
	 * @param  string $key  The API key being used for call.
	 *
	 * @return Object $data API response data.
	 */
	public function save( $data, $key ) {
		$data->license_status = true;

		// We can update the available status transient since we know the server was reached.
		// @todo move this into the Call class since it's the earliest point of access.
		set_site_transient( 'boldgrid_available', 1, HOUR_IN_SECONDS );

		// We update transient with the response of the API check version call.
		set_site_transient( 'boldgrid_api_data', $data, 8 * HOUR_IN_SECONDS );

		// Update the key option in case key is being overridden by something else so we have the new one next time.
		update_site_option( 'boldgrid_api_key', $key );

		// Updates the site hash identifier being stored.
		update_site_option( 'boldgrid_site_hash', $data->site_hash );

		// This looks for any reseller entries that exist and saves the wp option.
		$this->saveReseller( $data );


		// Returns back the transient data object.
		return $data;
	}

	/**
	 * Saves data based on API response.
	 *
	 * @since  1.0.0
	 *
	 * @param  Object $data API response data.
	 *
	 * @return Object $data API response data.
	 */
	protected function saveReseller( $data ) {
		$reseller = array();

		// @todo Check if a known key exists before deleting and skip reading/writing option.

		// Clear out any previous reseller entries.
		delete_option( 'boldgrid_reseller' );

		// Loop through the transient data being saved.
		foreach ( $data as $key => $value ) {

			// Check the data for matching keys.
			if ( 1 === preg_match( '/^reseller_/', $key ) ) {

				// Set the reseller transient data in the reseller array.
				$reseller[ $key ] = $data->$key;
			}
		}

		// Check if reseller data was extracted from the transient being saved.
		if ( ! empty( $reseller ) ) {
			update_site_option( 'boldgrid_reseller', $reseller );
		}

		// Return back the transient data object.
		return $data;
	}
}
