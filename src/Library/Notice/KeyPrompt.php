<?php
/**
 * BoldGrid Library Key Prompt Notice
 *
 * @package Boldgrid\Library
 * @subpackage \Notice
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Notice;

use Boldgrid\Library\Library;
use Boldgrid\Library\Library\Configs;

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
	 * Bool indicating whether or not this key prompt has been dismissed by the current user.
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
	 *
	 * @param \Boldgrid\Library\Library\Key Key object.
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
		// Allowed html for wp_kses.
		$allowed_html = array(
			'a' => array(
				'href' => array(),
			),
			'strong' => array(),
		);

		$msg = new \stdClass();
		$msg->success = sprintf(
			wp_kses(
				/* translators: The Url to the BoldGrid Connect settings page. */
				__( 'Your api key has been saved. To change, see <strong>Settings &#187; <a href="%1$s">BoldGrid Connect</a></strong>.', 'boldgrid-library' ),
				$allowed_html
			),
			admin_url( 'options-general.php?page=boldgrid-connect.php' )
		);
		$msg->error = sprintf(
			// translators: 1 A br / break tag.
			esc_html__( 'Your API key appears to be invalid!%sPlease try to enter your BoldGrid Connect Key again.', 'boldgrid-library' ),
			'<br />'
		);
		$msg->nonce = esc_html__( 'Security violation!  An invalid nonce was detected.', 'boldgrid-library' );
		$msg->timeout = esc_html__( 'Connection timed out. Please try again.', 'boldgrid-library' );

		return $this->messages = $msg;
	}

	/**
	 * Adds the required CSS and JS to the WordPress dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @hook: admin_enqueue_scripts
	 */
	public static function enqueue() {
		wp_enqueue_style(
			'boldgrid-library-api-notice-css',
			Library\Configs::get( 'libraryUrl' ) .  'src/assets/css/api-notice.css'
		);

		$handle = 'boldgrid-library-api-notice-js';
		wp_register_script(
			$handle,
			Library\Configs::get( 'libraryUrl' ) .  'src/assets/js/api-notice.js'
		);
		wp_localize_script(
			$handle,
			'BoldGridLibraryApiNotice',
			array(
				'clickEnterKey'       => esc_html__( 'Click here to enter your BoldGrid Connect Key.', 'boldgrid-library' ),
				'emailRequired'       => esc_html__( 'Please enter a valid e-mail address.', 'boldgrid-library' ),
				'errorCommunicating'  => esc_html__( 'There was an error communicating with the BoldGrid Connect Key server.  Please try again.', 'boldgrid-library' ),
				'firstRequired'       => esc_html__( 'First name is required.', 'boldgrid-library' ),
				'keyRequired'         => esc_html__( 'You must enter a valid BoldGrid Connect Key.', 'boldgrid-library' ),
				'lastRequired'        => esc_html__( 'Last name is required.', 'boldgrid-library' ),
				'tosRequired'         => esc_html__( 'You must agree to the Terms of Service before continuing.', 'bglig' ),
				'unexpectedError'     => esc_html__( 'An unexpected error occured. Please try again later.', 'boldgrid-library' ),
			)
		);
		wp_enqueue_script( $handle );
	}

	/**
	 * Get the current state of the BoldGrid Connect Key.
	 *
	 * @since 1.0.0
	 *
	 * @return string State, what to display when the prompt loads.
	 */
	public static function getState() {
		$state = 'no-key-added';
		$license = Configs::get( 'start' )->getKey()->getLicense();

		if ( $license ) {
			$isPremium = $license->isPremium( 'boldgrid-inspirations' );
			$license = $isPremium ? 'premium' : 'basic';
			$state = $license . '-key-active';
		}

		return $state;
	}

	/**
	 * Displays the notice.
	 *
	 * @since 1.0.0
	 *
	 * @see \Boldgrid\Library\Library\Notice::isDismissed()
	 *
	 * @hook: admin_notices
	 */
	public function keyNotice() {
		$display_notice = apply_filters(
			'Boldgrid\Library\Library\Notice\KeyPrompt_display',
			( ! Library\Notice::isDismissed( $this->userNoticeKey ) )
		);

		if ( $display_notice ) {
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
			$first_name = empty( $current_user->user_firstname ) ? '' : $current_user->user_firstname;
			$last_name = empty( $current_user->user_lastname ) ? '' : $current_user->user_lastname;
			$api = Library\Configs::get( 'api' ) . '/api/open/generateKey';

			/**
			 * Check if the Envato notice to claim a Premium Connect Key should be enabled.
			 *
			 * A theme can add this filter and return true, which will enable this notice.
			 *
			 * @since 2.1.0
			 */
			$enableClaimMessage = apply_filters(
				'Boldgrid\Library\Library\Notice\ClaimPremiumKey_enable',
				false
			);

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

		// When adding Keys, delete the transient to make sure we get new license info.
		delete_site_transient( 'bg_license_data' );
		delete_site_transient( 'boldgrid_api_data' );

		$key = $this->validate();
		$data = $this->key->callCheckVersion( array( 'key' => $key ) );
		$msg = $this->getMessages();

		if ( is_object( $data ) ) {
			$this->key->save( $data, $key );
			wp_send_json_success( array(
				'message'   => $msg->success,
				'site_hash' => $data->result->data->site_hash,
				'api_key'   => apply_filters( 'Boldgrid\Library\License\getApiKey', false ),
			) );
		} else {
			$is_timeout = false !== strpos( $data, 'cURL error 28:' );
			wp_send_json_error( array( 'message' => $is_timeout ? $msg->timeout : $msg->error ) );
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
		$valid = new Library\Key\Validate( sanitize_text_field( $_POST['api_key'] ) );
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
			self::$isDismissed = Library\Notice::isDismissed( $this->userNoticeKey );
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
}
