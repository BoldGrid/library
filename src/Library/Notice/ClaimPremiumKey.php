<?php
/**
 * File: ClaimPremiumKey.php
 *
 * The notice for Envato customers that have purchased the BoldGrid Prime theme.
 * They are eligible to receive a free Premium Connect Key.
 *
 * @package Boldgrid\Library
 *
 * @version 2.1.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Notice;

use Boldgrid\Library\Library;

/**
 * BoldGrid Library Claim Premium Key Notice.
 *
 * This class is responsible for adding the Claim Premium Key notice logic to a user's WordPress
 * admin pages.
 *
 * @since 2.1.0
 */
class ClaimPremiumKey {
	/**
	 * Bool indicating whether or not this key prompt has been dismissed by the current user.
	 *
	 * @since 2.1.0
	 *
	 * @var mixed Null if not set, otherwise bool.
	 */
	public static $isDismissed = null;

	/**
	 * Bool indicating whether or not we are currently showing the notice.
	 *
	 * @since 2.1.0
	 *
	 * @var bool
	 */
	public static $isDisplayed = false;

	/**
	 * @access private
	 *
	 * @var object $key           Key class object.
	 * @var string $userNoticeKey When dismissing this prompt, this is the identifier for the
	 *                            user notice.
	 */
	private
		$key,
		$userNoticeKey;

	/**
	 * Initialize class and set class properties.
	 *
	 * This class is automatically instantiated through the following events:
	 * #. Boldgrid\Library\Util\Load->__construct
	 * #. Boldgrid\Library\Library\Start->__construct
	 * #. Boldgrid\Library\Library\Key->__construct
	 * #. Boldgrid\Library\Library\Notice->__construct
	 * #. Boldgrid\Library\Library\Notice\ClaimPremiumKey->__construct
	 *
	 * This is important to note because if you ever wanted to instantiate
	 * another KeyPrompt class, you should know it's probably already been done
	 * so.
	 *
	 * Some of the class properties (such as isDismissed and isDisplayed) are
	 * static and easily retrievable via filters.
	 *
	 * @since 2.1.0
	 *
	 * @param \Boldgrid\Library\Library\Key Key object.
	 */
	public function __construct( Library\Key $key ) {
		$this->key = $key;
		$this->userNoticeKey = 'bg-claim-premium';
		Library\Filter::add( $this );
	}

	/**
	 * Displays the notice.
	 *
	 * Enabled if the filter "Boldgrid\Library\Library\Notice\ClaimPremiumKey_enable" is true.
	 *
	 * Does not display the notice if the user dismissed it, has a Connect Key,
	 * or a filter says not to.
	 *
	 * @since 2.1.0
	 *
	 * @see \Boldgrid\Library\Library\Notice::isDismissed()
	 * @see \Boldgrid\Library\Library\Configs::get()
	 *
	 * @hook: admin_notices
	 */
	public function displayNotice() {
		/**
		 * Check if the Envato notice to claim a Premium Connect Key should be enabled.
		 *
		 * A theme can add this filter and return true, which will enable this notice.
		 *
		 * @since 2.1.0
		 */
		$enabled = apply_filters(
			'Boldgrid\Library\Library\Notice\ClaimPremiumKey_enable',
			false
		);

		// If a Connect Key is not saved, then skip this notice; it will be in the key prompt.
		$hasConnectKey = (bool) Library\Configs::get( 'key' );

		if ( $enabled && $hasConnectKey ) {
			// If user has dismissed the notice, then do not display the notice.
			$display = ! Library\Notice::isDismissed( $this->userNoticeKey );

			// Do not display if user has an Envato-connected Prime theme.
			$hasEnvatoPrime = $this->key->getLicense()->isPremium( 'envato-prime' );

			if ( $hasEnvatoPrime ) {
				$display = false;
			}

			/**
			 * Check of there are any overrides for displaying this notice.
			 *
			 * @since 2.1.0
			 */
			$display = apply_filters(
				'Boldgrid\Library\Library\Notice\ClaimPremiumKey_display',
				$display
			);

			if ( $display ) {
				include dirname( __DIR__ ) . '/Views/ClaimPremiumKey.php';

				self::$isDisplayed = true;
			}
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
	 * Get static class property isDismissed.
	 *
	 * @since 2.0.1
	 *
	 * @see \Boldgrid\Library\Library\Notice::isDismissed()
	 *
	 * @hook: Boldgrid\Library\Notice\ClaimPremiumKey\getIsDismissed
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
}
