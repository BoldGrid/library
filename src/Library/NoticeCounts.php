<?php
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
     * @param string $id 
	 * @since 1.0
	 * @return string.
	 */
	public function get_unread_count( $id ) {
        $option = get_option( 'boldgrid-plugin-notice-counts' );
        if ( $option && isset( $option[$id] ) ) {
            $unread_count = 0;
            $notices = $option[$id];
            foreach($notices as $notice) {
                if ( $notice === true) {
                    $unread_count++;
                }
            }
            if ( $unread_count > 0 ) {
                return '<span class="unread-notice-count">' . $unread_count . '</span>';
            } else {
                return '<span class="unread-notice-count hidden"></span>';
            }
        }
    }
    
    /**
	 * Set Notice to Read.
     * if $notice is not set, all notices are set to read
	 *
     * @param string $id 
     * @param string $notice_id - $id of individual notice
	 * @since 1.0
	 */
    public function set_read( $id, $notice_id = false) {
        $option = get_option( 'boldgrid-plugin-notice-counts' );
        if ( $option && isset( $option[$id] ) ) {
            $notices = $option[$id];
            foreach ($notices as $notice_id => $notice_is_unread) {
                if ( ! $notice_id || isset( $option[$id][$notice_id] )) {
                    $option[$id][$notice_id] = false;
                }
            }
            update_option( 'boldgrid-plugin-notice-counts' , $option);
        }
    }
  
    /**
	 * set_notice_option
     * Updates database option based on config file data.
	 *
     * @param string $id 
     * @param string $notice_id - $id of individual notice
	 * @since 1.0
	 */
    private function set_notice_option() {
        $option = get_option( $this->option_name );
        $config_notices = $this->configs[$this->option_name];
        /**
         * if the option is not set in the database,
         * but is set in the config file, create a new option,
         * and update with config values
         */
        if ( $option === false && isset( $config_notices ) ) {
            update_option( $this->option_name, $config_notices );
        /**
         * Otherwise, if the option already exists in the database,
         * determine which items (if any) in the config are NOT set yet,
         * and set them.
         */
        } else if ( isset( $config_notices ) ) {
            foreach( $config_notices as $notice_type => $notice_list) {
                //If notice type exists, look for new notice ID's
                if ( isset( $option[$notice_type] ) ) {
                    //Check each notice_id in $config_notices against $option
                    //If notice ID does not exist, add it to $option[$notice_type]
                    foreach ($notice_list as $notice_id => $notice_is_unread ) {
                        if ( ! isset( $option[$notice_type][$notice_id] ) ) {
                            $option[$notice_type][$notice_id] = $notice_is_unread;
                        }
                    }
                    //Check each notice_id in $option against $config_notices
                    //If notice ID does not exist in $config_notices, del it from $option[$notice_type]
                    foreach ( $option[$notice_type] as $notice_id => $notice_is_unread ) {
                        if ( ! isset( $config_notices[$notice_type][$notice_id] ) ) {
                            unset($option[$notice_type][$notice_id]);
                        }
                    }
                //If $notice_type does not exist in $option,
                //add $notice_type look for new notice ID's
                } else {
                    $option[$notice_type] = $notice_list;
                }
            }
            //Push update to database
            update_option( $this->option_name, $option );
        }
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
