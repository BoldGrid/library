<?php
/**
 * BoldGrid Library Key Prompt Notice
 *
 * @package Boldgrid\Library
 * @subpackage \Util
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Notice;

use Boldgrid\Library\Library;

/**
 * BoldGrid Library Key Prompt Notice.
 *
 * This class is responsible for adding the Key Prompt notice logic
 * to a user's WordPress dashboard.
 *
 * @since 1.0.0
 */
class KeyPrompt {

	/**
	 * @access private
	 *
	 * @var object $key      Key class object.
	 * @var object $messages Messages used for key prompt.
	 */
	private
		$key,
		$messages;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Library\Key $key ) {
		$this->key = $key;
		$this->setMessages();
		Library\Filter::add( $this );
	}

	/**
	 * Sets the messages returned by key prompt.
	 *
	 * @since 1.0.0
	 *
	 * @return object $messages Messages used by key prompt.
	 */
	private function setMessages() {
		$msg = new \stdClass();
		$msg->success = esc_html__( 'Your api key has been saved successfully.', 'boldgrid-inspirations' );
		$msg->error = sprintf( esc_html__( 'Your API key appears to be invalid!%sPlease try to enter your BoldGrid Connect Key again.', 'boldgrid-inspirations' ), '<br />' );
		$msg->nonce = esc_html__( 'Security violation!  An invalid nonce was detected.', 'boldgrid-inspirations' );

		return $this->messages = $msg;
	}

	/**
	 * Adds the required CSS and JS to the WordPress dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @hook: admin_enqueue_scripts
	 */
	public function enqueue() {
		wp_enqueue_style(
			'bglib-api-notice-css',
			Library\Configs::get( 'libraryUrl' ) .  'src/assets/css/api-notice.css'
		);
		wp_enqueue_script(
			'bglib-api-notice-js',
			Library\Configs::get( 'libraryUrl' ) .  'src/assets/js/api-notice.js'
		);
	}

	/**
	 * Displays the notice.
	 *
	 * @since 1.0.0
	 *
	 * @hook: admin_notices
	 */
	public function keyNotice() {
		// Get current user.
		$current_user = wp_get_current_user();
		$email = $current_user->user_email;
		$first_name = empty( $current_user->user_firstname ) ? '' : $current_user->user_firstname;
		$last_name = empty( $current_user->user_lastname ) ? '' : $current_user->user_lastname;
		$api = Library\Configs::get( 'api' ) . '/api/open/generateKey';
		include dirname( __DIR__ ) . '/Views/KeyPrompt.php';
	}

	/**
	 * Key input handling.
	 *
	 * @since 1.0.0
	 *
	 * @hook: wp_ajax_addKey
	 */
	public function addKey() {
		$key = $this->validate();
		$data = $this->key->callCheckVersion( array( 'key' => $key ) );
		$msg = $this->getMessages();

		if ( is_object( $data ) ) {
			$this->key->save( $data, $key );
			wp_send_json_success( array( 'message' => $msg->success ) );
		} else {
			wp_send_json_error( array( 'message' => $msg->error ) );
		}
	}

	/**
	 * Handles validation of user input on API key entry form.
	 *
	 * @since  1.0.0
	 *
	 * @return string The user's validate API key hash.
	 */
	protected function validate() {
		$msg = $this->getMessages();

		// Validate nonce.
		if ( ! isset( $_POST['set_key_auth'] ) || ! check_ajax_referer( 'boldgrid_set_key', 'set_key_auth', false ) ) {
			wp_send_json_error( array( 'message' => $msg->nonce ) );
		}

		// Validate user input.
		if ( empty( $_POST['api_key'] ) ) {
			wp_send_json_error( array( 'message' => $msg->error ) );
		}

		// Validate key.
		$valid = new Library\Key\Validate( $_POST['api_key'] );
		if ( ! $valid->getValid() ) {
			wp_send_json_error( array( 'message' => $msg->error ) );
		}

		return $valid->getHash();
	}

	/**
	 * Gets messages class property.
	 *
	 * @since  1.0.0
	 *
	 * @return object $messages The messages class property.
	 */
	protected function getMessages() {
		return $this->messages;
	}
}
