<?php //phpcs:ignore WordPress.Files.FileName
/**
 * BoldGrid Library Notice Counts
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library Notice Counts Class.
 *
 * This class is responsible for adding any unread notice count numbers
 * that are displayed in the WordPress dashboard.
 *
 * @since SINCEVERSION
 */
class NoticeCounts {
	/**
	 * Get Core Configs
	 *
	 * Gets configs array from admin core
	 * exists;
	 *
	 * @since SINCEVERSION
	 * @var array
	 * @access private
	 */
	private static function getConfigNotices( $configName ) {
		$configPath = BOLDGRID_BACKUP_PATH . '/includes/config/config.plugin.php';
		$config = require $configPath;
		if ( isset( $config[ $configName ] ) ) {
			return $config[ $configName ];
		}
	}

	/**
	 * Get Unread Count.
	 *
	 * @param string $id of the notice count type to return.
	 * @since SINCEVERSION
	 * @return string.
	 */
	public static function getUnreadCount( $id ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		if ( $option && isset( $option[ $id ] ) ) {
			$unread_count = 0;
			$notices      = $option[ $id ];
			foreach ( $notices as $notice ) {
				if ( true === $notice ) {
					$unread_count++;
				}
			}
			return self::countMarkup( $unread_count );
		}
	}

	/**
	 * Get Total Unread.
	 *
	 * Determines the total number of unread notices
	 *
	 * @since SINCEVERSION
	 * @return string.
	 */
	public static function getTotalUnread() {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		$totalUnread = 0;
		if ( $option ) {
			$configNotices = self::getConfigNotices( 'boldgrid-plugin-notice-counts' );
			foreach ( array_keys( $configNotices ) as $noticeType ) {
				if ( isset( $option[ $noticeType ] ) ) {
					foreach ( $option[ $noticeType ] as $is_unread ) {
						if ( $is_unread ) {
							$totalUnread++;
						}
					}
				}
			}
		}
		return self::countMarkup( $totalUnread );
	}

	/**
	 * Unread Count Markup.
	 * generates HTML Markup for unread notice counts
	 *
	 * @param int $count number to use for markup.
	 * @return string the marked up unread count string.
	 * @since SINCEVERSION
	 */
	public static function countMarkup( $count ) {
		if ( $count > 0 ) {
			return '<span class="unread-notice-count">' . $count . '</span>';
		} else {
			return '<span class="unread-notice-count hidden"></span>';
		}
	}

	/**
	 * Is a specific notice-id unread?
	 * returns true if a provided notice-id is unread
	 *
	 * @param string $id of the notice count type to return.
	 * @param string $noticeId - $id of individual notice.
	 * @return bool true if notice-id is unread
	 * @since SINCEVERSION
	 */
	public static function isUnread( $id, $noticeId ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		if ( $option && isset( $option[ $id ] ) ) {
			if ( isset( $option[ $id ][ $noticeId ] ) && true === $option[ $id ][ $noticeId ] ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Set Notice to Read.
	 * if $notice is not set, all notices are set to read
	 *
	 * @param string $id of the notice count type to return.
	 * @param string $noticeId - $id of individual notice.
	 * @since SINCEVERSION
	 */
	public static function setRead( $id, $noticeId = false ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		if ( $option && isset( $option[ $id ] ) ) {
			$notices = $option[ $id ];
			foreach ( $notices as $noticeId => $notice_is_unread ) {
				if ( ! $noticeId || isset( $option[ $id ][ $noticeId ] ) ) {
					$option[ $id ][ $noticeId ] = false;
				}
			}
			update_option( 'boldgrid-plugin-notice-counts', $option );
		}
	}

	/**
	 * Set Notice from Config
	 * Updates database option based on config file data,
	 * Given Parameter.
	 *
	 * @param array $notice Optional param to define a notice to set.
	 * @since SINCEVERSION
	 */

	public static function setNoticeConfig() {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		$configNotices = self::getConfigNotices( 'boldgrid-plugin-notice-counts' );
		if ( false === $option ) {
			/**
			* If the option is not set in the database,
			* but is set in the config file, create a new option,
			*/
			$option = $configNotices;
		} else {
			/**
			* Otherwise, if the option already exists in the database,
			* determine which items (if any) in the config are NOT set yet,
			* and set them.
			*/
			foreach ( $configNotices as $noticeType => $noticeList ) {
				// If notice type exists, look for new notice ID's.
				if ( isset( $option[ $noticeType ] ) ) {
					// Check each notice_id in $config_notices against $option.
					// If notice ID does not exist, add it to $option[$noticeType].
					foreach ( $noticeList as $noticeId => $notice_is_unread ) {
						if ( ! isset( $option[ $noticeType ][ $noticeId ] ) ) {
							$option[ $noticeType ][ $noticeId ] = $notice_is_unread;
						}
					}
				} else {
					// If $noticeType does not exist in $option,
					// add $noticeType look for new notice ID's.
					$option[ $noticeType ] = $noticeList;
				}
			}
		} 
		// Push update to database.
		update_option( 'boldgrid-plugin-notice-counts', $option );
	}

	/**
	 * Set Notice Option
	 * Updates database option based on config file data,
	 * Given Parameter.
	 *
	 * @param array $notice Optional param to define a notice to set.
	 * @since SINCEVERSION
	 */
	public static function setNoticeOption( array $notice ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
			if ( isset( $option[ $notice[0] ] ) ) {
				$option[ $notice[0] ][ $notice[1] ] = $notice[2];
			}
		// Push update to database.
		update_option( 'boldgrid-plugin-notice-counts', $option );
	}
}
