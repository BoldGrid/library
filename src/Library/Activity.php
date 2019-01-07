<?php
/**
 * BoldGrid Library Activity Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library\License
 *
 * @version 2.7.7
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

use Boldgrid\Library\Library;

/**
 * BoldGrid Library Activity Class.
 *
 * @since 2.7.7
 */
class Activity {
	/**
	 * Whether or not the filters in the constructor have been added.
	 *
	 * They only need to be added once.
	 *
	 * @since 2.7.7
	 * @var bool
	 */
	private static $filters_added = false;

	/**
	 * The option name where activity data is stored.
	 *
	 * @since 2.7.7
	 * @var string
	 */
	private $option_name = 'bglib_activity';

	/**
	 * The name of the plugin this class represents.
	 *
	 * For example, 'boldgrid-backup'.
	 *
	 * @since 2.7.7
	 * @var string
	 */
	private $plugin;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 2.7.7
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		if ( ! self::$filters_added ) {
			Filter::add( $this );
			self::$filters_added = true;
		}

		// Debug.
		// delete_option( $this->option_name );
	}

	/**
	 * Add / update an activity count.
	 *
	 * @since 2.7.7
	 *
	 * @param string $activity    The name of the activity.
	 * @param int    $count       The amount to increment the activity by.
	 * @param string $config_path The path to the config file for rating prompts. If the path is
	 *                            passed in, this method will also attempt to add a new prompt if
	 *                            the activities threshold has been reached.
	 * @return bool               Whether or not the activity was added.
	 */
	public function add( $activity, $count = 1, $config_path = null ) {
		$added = false;

		$plugin_activities = $this->get_plugin_activities();

		if ( ! isset( $plugin_activities[ $activity ] ) ) {
			$plugin_activities[ $activity ] = $count;
		} else {
			$plugin_activities[ $activity ] += $count;
		}

		$added = $this->save_plugin_activities( $plugin_activities );

		if ( ! empty( $config_path ) ) {
			$this->maybeAddRatingPrompt( $activity, $config_path );
		}

		return $added;
	}

	/**
	 * Get all activities.
	 *
	 * @since 2.7.7
	 *
	 * @return array
	 */
	public function get_activities() {
		return get_option( $this->option_name, array() );
	}

	/**
	 * Get the configs for a particular activity.
	 *
	 * @since 2.7.7
	 *
	 * @var    string $activity    The name of the activity.
	 * @var    string $config_path The full path to the config file.
	 * @return array               An array of configs.
	 */
	public function get_activity_configs( $activity, $config_path ) {
		$configs = array();

		if ( file_exists( $config_path ) ) {
			$configs = require $config_path;
		}

		return isset( $configs[ $activity ] ) ? $configs[ $activity ] : array();
	}

	/**
	 * Get the count for an activity.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $activity The name of the activity.
	 * @return int              The activity's count.
	 */
	public function get_activity_count( $activity ) {
		$plugin_activities = $this->get_plugin_activities();

		return empty( $plugin_activities[ $activity ] ) ? 0 : $plugin_activities[ $activity ];
	}

	/**
	 * Get all activities for a plugin.
	 *
	 * @since 2.7.7
	 *
	 * @return array
	 */
	public function get_plugin_activities() {
		$activities = $this->get_activities();

		if ( ! isset( $activities[ $this->plugin ] ) ) {
			$activities[ $this->plugin ] = array();
		}

		return $activities[ $this->plugin ];
	}

	/**
	 * Maybe add a rating prompt for an activity.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $activity    The name of an activity
	 * @param  string $config_path The path to our plugin's rating prompt configs.
	 * @return bool                Whether or not we added a rating prompt.
	 */
	public function maybeAddRatingPrompt( $activity, $config_path ) {
		$added = false;

		$configs = $this->get_activity_configs( $activity, $config_path );

		if ( isset( $configs['threshold'] ) && $this->get_activity_count( $activity ) >= $configs['threshold'] ) {
			$rating_prompt = new \Boldgrid\Library\Library\RatingPrompt();
			$added = $rating_prompt->add_prompt( $configs['prompt'] );
		}

		return $added;
	}

	/**
	 * Save all activities.
	 *
	 * This overwrites all activites, not just the ones for this plugin.
	 *
	 * @since 2.7.7
	 *
	 * @param  array $activities An array of activities.
	 * @return bool              Whether or not the activities were updated successfully.
	 */
	public function save_activities( $activities ) {
		return update_option( $this->option_name, $activities );
	}

	/**
	 * Save all activities for this plugin.
	 *
	 * @since 2.7.7
	 *
	 * @param  array $plugin_activities An array of activities.
	 * @return bool                     Whether or not the activities were updated successfully.
	 */
	public function save_plugin_activities( $plugin_activities ) {
		$activities = $this->get_activities();

		$activities[ $this->plugin ] = $plugin_activities;

		return $this->save_activities( $activities );
	}
}
