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
	 * Bool indicating whether or not this key prompt has been dismissed by the
	 * current user.
	 *
	 * @since 2.0.1
	 *
	 * @var mixed $isDismissed Null if not set, otherwise bool.
	 */
	public static $isDismissed = null;

	/**
	 * Bool indicating whether or not we are currently showing the notice.
	 *
	 * @since 2.0.1
	 *
	 * @var bool $isDisplayed
	 */
	public static $isDisplayed = false;

	/**
	 * @access private
	 *
	 * @var object $key           Key class object.
	 * @var object $messages      Messages used for key prompt.
	 * @var string $userNoticeKey When dismissing this prompt, this is the
	 *                            identifier for the user notice.
	 */
	private
		$key,
		$messages,
		$userNoticeKey;

	/**
	 * Initialize class and set class properties.
	 *
	 * This class is automatically instantiated through the following events:
	 * #. Boldgrid\Library\Util\Load->__construct
	 * #. Boldgrid\Library\Library\Start->__construct
	 * #. Boldgrid\Library\Library\Key->__construct
	 * #. Boldgrid\Library\Library\Notice->__construct
	 * #. Boldgrid\Library\Library\Notice\KeyPrompt->__construct
	 *
	 * This is important to note because if you ever wanted to instantiate
	 * another KeyPrompt class, you should know it's probably already been done
	 * so.
	 *
	 * Some of the class properties (such as isDismissed and isDisplayed) are
	 * static and easily retrievable via filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Library\Key $key ) {
		$this->key = $key;
		$this->setMessages();
		$this->userNoticeKey = 'bg-key-prompt';
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
		$display_notice = apply_filters(
			'Boldgrid\Library\Library\Notice\KeyPrompt_display',
			( ! $this->isDismissed( $this->userNoticeKey ) )
		);

		if ( $display_notice ) {
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
			$first_name = empty( $current_user->user_firstname ) ? '' : $current_user->user_firstname;
			$last_name = empty( $current_user->user_lastname ) ? '' : $current_user->user_lastname;
			$api = Library\Configs::get( 'api' ) . '/api/open/generateKey';
			include dirname( __DIR__ ) . '/Views/KeyPrompt.php';

			self::$isDisplayed = true;
		}
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
	 * Get static class property isDismissed.
	 *
	 * @since 2.0.1
	 *
	 * @hook: Boldgrid\Library\Notice\KeyPrompt\getIsDismissed
	 */
	public function getIsDismissed() {
		if( is_null( self::$isDismissed ) ) {
			self::$isDismissed = $this->isDismissed( $this->userNoticeKey );
		}

		return self::$isDismissed;
	}

	/**
	 * Get static class property isDisplayed.
	 *
	 * @since 2.0.1
	 *
	 * @hook: Boldgrid\Library\Notice\KeyPrompt\getIsDisplayed
	 */
	public function getIsDisplayed() {
		return self::$isDisplayed;
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

	/**
	 * Handle dimissal of the key prompt notice.
	 *
	 * @since 1.1.6
	 *
	 * @see \Boldgrid\Library\Library\Notice\KeyPrompt::isDismissed()
	 *
	 * @hook: wp_ajax_dismissBoldgridNotice
	 */
	public function dismiss() {
		// Validate nonce.
		if ( isset( $_POST['set_key_auth'] ) && check_ajax_referer( 'boldgrid_set_key', 'set_key_auth', false ) ) {

			// Mark the notice as dismissed, if not already done so.
			$dismissal = array(
				'id' => $this->userNoticeKey,
				'timestamp' => time(),
			);

			if ( ! $this->isDismissed( $this->userNoticeKey ) ) {
				add_user_meta( get_current_user_id(), 'boldgrid_dismissed_admin_notices', $dismissal );
			}
		}
	}

	/**
	 * Handle undimissal of the key prompt notice.
	 *
	 * @since 1.1.7
	 *
	 * @hook: wp_ajax_undismissBoldgridNotice
	 */
	public function undismiss() {
		// Validate nonce.
		if ( isset( $_POST['set_key_auth'] ) && ! empty( $_POST['notice'] ) && check_ajax_referer( 'boldgrid_set_key', 'set_key_auth', false ) ) {
			$id = sanitize_key( $_POST['notice'] );

			// Get all of the notices this user has dismissed.
			$dismissals = get_user_meta( get_current_user_id(), 'boldgrid_dismissed_admin_notices' );

			// Loop through all of the dismissed notices. If we find the dismissal, then remove it.
			foreach ( $dismissals as $dismissal ) {
				if ( $id === $dismissal['id'] ) {
					delete_user_meta( get_current_user_id(), 'boldgrid_dismissed_admin_notices', $dismissal );
					break;
				}
			}
		}
	}

	/**
	 * Is there a user dismissal record for a particular admin notice id?
	 *
	 * @todo This method seems like a more generalized method that should belong
	 * in maybe Library\Notice rather than Library\Notice\KeyPrompt.
	 *
	 * @since 1.1.6
	 *
	 * @param  string $id An admin notice id.
	 * @return bool
	 */
	public function isDismissed( $id ) {
		$dismissed = false;
		$id = sanitize_key( $id );

		// Get all of the notices this user has dismissed.
		$dismissals = get_user_meta( get_current_user_id(), 'boldgrid_dismissed_admin_notices' );

		// Loop through all of the dismissed notices. If we find our $id, then mark bool and break.
		foreach ( $dismissals as $dismissal ) {
			if ( $id === $dismissal['id'] ) {
				$dismissed = true;
				break;
			}
		}

		// We did not find our notice dismissed above, so return false.
		return $dismissed;
	}
}
