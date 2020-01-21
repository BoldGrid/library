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
	 * @param string $configName Name of the item in the Configs array to return.
	 * @return array
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
	 * Get Notices
	 *
	 * Gets an array of notices by Type, ID, or PluginName from the database.
	 * @param string $noticeValue Specific value you want to filter notices by.
	 * @param string $noticeType Either noticeId, noticeType, noticePlugin
	 * @return array
	 * @access private
	 * @since SINCEVERSION
	 */
	private static function getNotices( $noticeValue, $valueType ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		$notices = [];
		if ( $option ) {
			foreach ( $option as $noticeId => $notice) {
				if ( $valueType == 'NoticeId' && $noticeId == $noticeValue) {
					$notices[] = $notice;
				} else if ( isset( $notice[ $valueType ] ) && $notice[ $valueType ] == $noticeValue) {
					$notices[] = $notice;
				}
			}
		}
		return $notices;
	}

	/**
	 * Get Unread Count.
	 *
	 * @param string $id of the notice count type to return.
	 * @return string.
	 * @since SINCEVERSION
	 */
	public static function getUnreadCount( $noticeValue, $valueType ) {
		$notices = [];
		if ( ! in_array( $valueType, ['noticeType', 'noticeId', 'noticePlugin'] ) ) {
			trigger_error( 'valueType of NoticeCounts::getUnreadCount must be either noticeType, noticeId, or noticePlugin', E_UESR_NOTICE);
		}

		$notices = self::GetNotices( $noticeValue, $valueType );

		$unread_count = 0;
		foreach ( $notices as $notice ) {
			if ( $notice['isUnread'] ) {
				$unread_count++;
			}
		}
		return self::getCountMarkup( $unread_count );
	}

	/**
	 * Unread Count Markup.
	 * generates HTML Markup for unread notice counts
	 *
	 * @param int $count number to use for markup.
	 * @return string the markedup unread count string.
	 * @access private
	 * @since SINCEVERSION
	 */
	private static function getCountMarkup( $count ) {
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
	public static function isUnread( $noticeType, $noticeId ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		if ( $option && isset( $option[ $noticeId ] ) ) {
			if ( $option[ $noticeId ]['isUnread'] ){
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
	public static function setRead( $noticeType, $noticeId = false ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		if ( $noticeId && $option && isset( $option[ $noticeId ] ) && $option[ $noticeId ]['isUnread'] ) {
				$option[ $noticeId ]['isUnread'] = false;
			} elseif ( $option ) {
				foreach ( $option as $notice => $noticeValues ) {
					if ( $noticeValues['noticeType'] == $noticeType && $noticeValues['isUnread'] ) {
						$option[ $notice ]['isUnread'] = false;
					}
				}
			}
		update_option( 'boldgrid-plugin-notice-counts', $option);
	}

	/**
	 * Set Notice from Config
	 * Updates database option based on config file data,
	 * Given Parameter.
	 *
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
			error_log(serialize($option));
			foreach (  $configNotices as $configNotice => $noticeValues ) {
				// If notice does not exist in database, add to $option.
				error_log($configNotice . ' => ' . serialize($noticeValues) );
				if ( ! isset( $option[ $configNotice ] ) ) {
					$option[ $configNotice ] = $noticeValues;
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
