<?php //phpcs:ignore WordPress.Files.FileName
/**
 * BoldGrid Library Notice Counts
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library Notice Counts Class.
 *
 * This class is responsible for adding any unread notice count numbers
 * that are displayed in the WordPress dashboard.
 *
 * @since 1.0.0
 */
class NoticeCounts {
	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */

	public $option_name = 'boldgrid-plugin-notice-counts';

	/**
	 * Get Core Configs
	 * gets configs array from admin core
	 * exists;
	 *
	 * @since 1.0
	 * @var array
	 */
	private $configs;

	/**
	 * Get Unread Count.
	 *
	 * @param string $id of the notice count type to return.
	 * @since 1.0
	 * @return string.
	 */
	public function get_unread_count( $id ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		if ( $option && isset( $option[ $id ] ) ) {
			$unread_count = 0;
			$notices      = $option[ $id ];
			foreach ( $notices as $notice ) {
				if ( true === $notice ) {
					$unread_count++;
				}
			}
			return self::count_markup( $unread_count );
		}
	}

	/**
	 * Get Total Unread.
	 *
	 * Determines the total number of unread notices
	 *
	 * @since 1.0
	 * @return string.
	 */
	public function get_total_unread() {
		$option       = get_option( 'boldgrid-plugin-notice-counts' );
		$total_unread = 0;
		if ( $option ) {
			$config_notices = $this->configs[ $this->option_name ];
			foreach ( array_keys( $config_notices ) as $notice_type ) {
				if ( isset( $option[ $notice_type ] ) ) {
					foreach ( $option[ $notice_type ] as $is_unread ) {
						if ( $is_unread ) {
							$total_unread++;
						}
					}
				}
			}
		}
		return $this->count_markup( $total_unread );
	}

	/**
	 * Unread Count Markup.
	 * generates HTML Markup for unread notice counts
	 *
	 * @param int $count number to use for markup.
	 * @return string the marked up unread count string.
	 * @since 1.0
	 */
	private function count_markup( $count ) {
		if ( $count > 0 ) {
			return '<span class="unread-notice-count">' . $count . '</span>';
		} else {
			return '<span class="unread-notice-count hidden"></span>';
		}
	}
	/**
	 * Set Notice to Read.
	 * if $notice is not set, all notices are set to read
	 *
	 * @param string $id of the notice count type to return.
	 * @param string $notice_id - $id of individual notice.
	 * @since 1.0
	 */
	public function set_read( $id, $notice_id = false ) {
		$option = get_option( 'boldgrid-plugin-notice-counts' );
		if ( $option && isset( $option[ $id ] ) ) {
			$notices = $option[ $id ];
			foreach ( $notices as $notice_id => $notice_is_unread ) {
				if ( ! $notice_id || isset( $option[ $id ][ $notice_id ] ) ) {
					$option[ $id ][ $notice_id ] = false;
				}
			}
			update_option( 'boldgrid-plugin-notice-counts', $option );
		}
	}

	/**
	 * Set Notice Option
	 * Updates database option based on config file data,
	 * Given Parameter.
	 *
	 * @param array $notice Optional param to define a notice to set.
	 * @since 1.0
	 */
	public function set_notice_option( $notice = null ) {
		$option = get_option( $this->option_name );
		if ( ! $notice ) {
			$config_notices = $this->configs[ $this->option_name ];
			if ( false === $option ) {
				/**
				* If the option is not set in the database,
				* but is set in the config file, create a new option,
				*/
				$option = $config_notices;
			} else {
				/**
				* Otherwise, if the option already exists in the database,
				* determine which items (if any) in the config are NOT set yet,
				* and set them.
				*/
				foreach ( $config_notices as $notice_type => $notice_list ) {
					// If notice type exists, look for new notice ID's.
					if ( isset( $option[ $notice_type ] ) ) {
						// Check each notice_id in $config_notices against $option.
						// If notice ID does not exist, add it to $option[$notice_type].
						foreach ( $notice_list as $notice_id => $notice_is_unread ) {
							if ( ! isset( $option[ $notice_type ][ $notice_id ] ) ) {
								$option[ $notice_type ][ $notice_id ] = $notice_is_unread;
							}
						}
					} else {
						// If $notice_type does not exist in $option,
						// add $notice_type look for new notice ID's.
						$option[ $notice_type ] = $notice_list;
					}
				}
			}
		} else {
			if ( isset( $option[ $notice[0] ] ) ) {
				$option[ $notice[0] ][ $notice[1] ] = $notice[2];
			}
		}
		// Push update to database.
		update_option( $this->option_name, $option );
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 *
	 * @param array $configs The configs array for this plugin.
	 */
	public function __construct( $configs ) {
		$this->configs = $configs;
		$this->set_notice_option();
	}
}
