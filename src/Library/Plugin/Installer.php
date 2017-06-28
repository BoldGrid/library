<?php
/**
 * BoldGrid Library Plugin Installer
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Plugin
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin;

use Boldgrid\Library\Library;
use Boldgrid\Library\Util;

/**
 * BoldGrid Library Plugin Installer Class.
 *
 * This class is responsible for installing BoldGrid plugins in the WordPress
 * admin's "Plugins > Add New" section.
 *
 * @since 1.0.0
 */
class Installer {

	/**
	 * @access protected
	 *
	 * @var array $configs   Configuration options for the plugin installer.
	 * @var array $transient Data from boldgrid_plugins transient.
	 */
	protected
		$configs,
		$transient,
		$updates;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param array $configs Array of configuration options for the BG Library.
	 */
	public function __construct( $configs ) {
		$this->setConfigs( $configs );
		if ( $this->configs['enabled'] ) {
			$this->setTransient();
			$license = new Library\License;
			$this->license = Library\Configs::get( 'licenseData' );
			$this->setUpdates();
			$this->ajax();
			Library\Filter::add( $this );
		}
	}

	/**
	 * Set the configs class property.
	 *
	 * @since 1.0.0
	 *
	 * @param array $configs Array of configuration options for plugin installer.
	 *
	 * @return object $configs The configs class property.
	 */
	protected function setConfigs( $configs ) {
		return $this->configs = $configs;
	}

	/**
	 * Set the updates class property.
	 *
	 * @since 1.0.0
	 *
	 * @param array $updates Available updates for plugin installer.
	 *
	 * @return object $updates The updates class property.
	 */
	protected function setUpdates() {
		if ( ! function_exists( 'get_plugin_updates' ) ) {
			require_once ABSPATH . 'wp-admin/includes/update.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return $this->updates = get_plugin_updates();
	}

	/**
	 * Get available plugin updates.
	 *
	 * @since 1.0.0
	 *
	 * @return [type] [description]
	 */
	protected function getUpdates() {
		return $this->updates;
	}

	/**
	 * Set the transient class property.
	 *
	 * @since 1.0.0
	 *
	 * @return object $transient The transient class property.
	 */
	protected function setTransient() {
		return $this->transient = get_site_transient( 'boldgrid_plugins', null ) ? get_site_transient( 'boldgrid_plugins' ) : $this->getPluginInformation( $this->configs['plugins'] );
	}

	/**
	 * Add "BoldGrid" tab to the plugins install filter bar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Tabs in the WordPress "Plugins > Add New" filter bar.
	 *
	 * @hook: install_plugins_tabs
	 *
	 * @return array $tabs Tabs in the WordPress "Plugins > Add New" filter bar.
	 */
	public function addTab( $tabs ) {
		$boldgrid = array( 'boldgrid' => __( 'BoldGrid', 'boldgrid-library' ) );
		$tabs = array_merge( $boldgrid, $tabs );
		return $tabs;
	}

	/**
	 * Filter the Plugin API arguments.
	 *
	 * We use this filter to add the custom "boldgrid" field for the
	 * "browse" arg to the WordPress Plugin API.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args   WordPress Plugins API arguments.
	 * @param string $action The type of information being requested from the Plugin Install API.
	 *
	 * @hook: plugins_api_args
	 *
	 * @return array $args WordPress Plugins API arguments.
	 */
	public function pluginsApiArgs( $args, $action ) {
		$boldgrid_plugins = get_site_transient( 'boldgrid_plugins' );
		if ( isset( $args->slug ) ) {
			$plugin = $args->slug;
			if ( isset( $boldgrid_plugins->$plugin ) ) {
				$args->browse = 'boldgrid';
			}
		}

		return $args;
	}

	/**
	 * Filter the WordPress Plugins API information before we display it.
	 *
	 * We use this filter to add data to the WordPress Plugins API containing
	 * the information necessary for our outside sources to be installed.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $results The results object or array. Default is false false.
	 * @param string $action  The type of information being requested from the Plugin Install API.
	 * @param array  $args    WordPress Plugins API arguments.
	 *
	 * @hook: plugins_api
	 *
	 * @return array $args WordPress Plugins API arguments.
	 */
	public function pluginsApi( $results, $action, $args ) {
		$boldgrid_plugins = get_site_transient( 'boldgrid_plugins' );

		// Check if we are hooked to query_plugins and browsing 'boldgrid' sorted plugins.
		if ( isset( $args->browse ) && $args->browse === 'boldgrid' ) {

			// Query plugins action.
			if ( $action === 'query_plugins' ) {
				$results = new stdClass();

				// Set the results for query.
				$results->plugins = array_values( ( array ) $boldgrid_plugins );
				$results->info = array( 'results' => count( $results->plugins ) );

			// The plugin-information tab expects a different format.
			} elseif ( $action === 'plugin_information' ) {
				if ( ! empty( $boldgrid_plugins->{$args->slug} ) ) {
					$results = $boldgrid_plugins->{$args->slug};
				}
			}
		}

		return $results;
	}

	/**
	 * Filter the Plugin API results.
	 *
	 * We use this filter to add our plugins to the plugins_api results.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $result Plugins API result.
	 * @param array  $args   WordPress Plugins API arguments.
	 * @param string $action The type of information being requested from the Plugin Install API.
	 *
	 * @hook: plugins_api_result
	 *
	 * @return array $result WordPress Plugins API result.
	 */
	public function result( $result, $action, $args ) {
		$boldgrid_plugins = get_site_transient( 'boldgrid_plugins' );

		// Add data for plugin info tabs in results.
		if ( $action === 'plugin_information' ) {
			if ( ! empty( $boldgrid_plugins->{$args->slug} ) ) {
				$result = $boldgrid_plugins->{$args->slug};
			}

		// Allow BoldGrid ( + variations of ) as a search option to show all plugins.
		} else if ( $action === 'query_plugins' ) {
			if ( strpos( strtolower( $args->search ), 'boldgrid' ) !== false ) {

				// Add all boldgrid plugins.
				$result->plugins = ( object ) array_merge( ( array ) $boldgrid_plugins, ( array ) $result->plugins );

				// Count results found.
				$result->info = array( 'results' => count( $result->plugins ) );
			} else if ( ! empty( $args->search ) ) {
				$found = array();
				foreach ( $boldgrid_plugins as $plugin ) {
					if ( strpos( strtolower( implode( ' ', $plugin->tags ) ), trim( strtolower( $args->search ) ) ) !== false ) {
						$found[] = $plugin;
					}
				}

				// Merge found results.
				$result->plugins = ( object ) array_merge( ( array ) $found, ( array ) $result->plugins );

				// Recount the results found.
				$result->info = array( 'results' => count( $result->plugins ) );
			}
		}

		return $result;
	}

	/**
	 * Initialize the display of the plugins.
	 *
	 * @since 1.0.0
	 *
	 * @hook: install_plugins_boldgrid
	 */
	public function init() {

		// Abort if we don't have any plugins to list.
		if ( ! $this->configs['plugins'] ) {
			return;
		}

		?>
		<div class="bglib-plugin-installer">
		<?php
			foreach ( $this->configs['plugins'] as $plugin => $details ) {
				$button_classes = 'install button';
				$button_text = __( 'Install Now', 'boldgrid-library' );

				$api = plugins_api(
					'plugin_information',
					array(
						'slug' => $plugin,
						'fields' => array(
							'short_description' => true,
							'sections' => false,
							'requires' => false,
							'downloaded' => true,
							'last_updated' => false,
							'added' => false,
							'tags' => false,
							'compatibility' => false,
							'homepage' => false,
							'donate_link' => false,
							'icons' => true,
							'banners' => true,
						),
					)
				);

				if ( ! is_wp_error( $api ) ) {

					// Main plugin file.
					$file = $this->configs['plugins'][ $plugin ]['file'];
					if ( $this->get_plugin_file( $plugin ) ) {
						// Has activation already occured? Disable button if so.
						if ( is_plugin_active( $file ) ) {
							$button_classes = 'button disabled';
							$button_text = __( 'Activated', 'boldgrid-library' );

						// Otherwise allow activation button.
						} else {
							$button_classes = 'activate button button-primary';
							$button_text = __( 'Activate', 'boldgrid-library' );
						}
					}

					$button_link = add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => $api->slug,
							'_wpnonce' => wp_create_nonce( "install-plugin_{$api->slug}" ),
						),
						self_admin_url( 'update.php' )
					);

					$button = array(
						'link' => $button_link,
						'classes' => $button_classes,
						'text' => $button_text,
					);

					$modal = add_query_arg(
						array(
							'tab' => 'plugin-information',
							'plugin' => $api->slug,
							'TB_iframe' => 'true',
						),
						self_admin_url( 'plugin-install.php' )
					);

					// Plugin Name ( Consists of name plus version number e.g. BoldGrid Inspirations 1.4 ).
					$name = "$api->name $api->version";

					$premiumSlug = $api->slug . '-premium';
					$pluginClasses = $api->slug;
					if ( isset( $this->license->{$premiumSlug} ) || isset( $this->license->{$api->slug} ) ) {
						$pluginClasses = "plugin-card-{$api->slug} premium";
					}
					$messageClasses = 'installer-messages';
					$message = '';

					if ( isset( $this->updates[ $file ] ) && version_compare( $this->updates[ $file ]->Version, $this->updates[ $file ]->update->new_version, '<' ) ) {
						$messageClasses = "{$messageClasses} update-now update-message notice inline notice-warning notice-alt";
						$updateUrl = add_query_arg(
							array(
								'action' => 'upgrade-plugin',
								'plugin' => urlencode( $file ),
								'slug' => $api->slug,
								'_wpnonce' => wp_create_nonce( "upgrade-plugin_{$api->slug}" ),
							),
							self_admin_url( 'update.php' )
						);
						$updateLink = '<a href="' . $updateUrl . '" class="update-link" aria-label="' . sprintf( __( 'Update %s now', 'boldgrid-library' ), $api->name ) . '" data-plugin="' . $file . '" data-slug="' . $api->slug . '"> ' . __( 'Update now' ) . '</a>';
						$message = sprintf( __( 'New version available. %s' ), $updateLink );
					}

					// Send plugin data to template.
					$this->renderTemplate( $plugin, $pluginClasses, $message, $messageClasses, $api, $name, $button, $modal );
				}
			}
			?>
		</div>
		<?php
	}


	/**
	 * Adds ajax actions for plugin installer page.
	 *
	 * @since 1.0.0
	 */
	protected function ajax() {
		$activate = new Installer\Activate( $this->configs );
		$activate->init();
		$upgrade = new Installer\Upgrade( $this->configs );
		$upgrade->init();
		$install = new Installer\Install( $this->configs );
		$install->init();
	}

	/**
	 * Helper to get and verify the plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $plugin_slug Slug of the plugin to get main file for.
	 *
	 * @return mixed  $plugin_file Main plugin file of slug or null if not found.
	 */
	public function get_plugin_file( $plugin_slug ) {

		// Load plugin.php if not already included by core.
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		foreach ( $plugins as $plugin_file => $plugin_info ) {

			// Get the basename of the plugin.
			$slug = dirname( plugin_basename( $plugin_file ) );
			if ( $slug ) {
				if ( $slug == $plugin_slug ) {
					return $plugin_file;
				}
			}
		}

		return null;
	}

	/**
	 * Renders template for each plugin card.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $plugin         Original data passed in for plugin list.
	 * @param string $pluginClasses  Classes to add for plugin card.
	 * @param string $message        The message to display if any.
	 * @param string $messageClasses Classes to apply to the message box.
	 * @param array  $api            Results from plugins_api
	 * @param string $name           Plugin name/version used for aria labels.
	 * @param array  $button         Contains button link, text and classes.
	 * @param string $modal          Modal link for thickbox plugin-information tabs.
	 */
	public function renderTemplate( $plugin, $pluginClasses, $message, $messageClasses, $api, $name, $button, $modal ) {
		include Library\Configs::get( 'libraryDir' ) . 'src/Library/Views/PluginInstaller.php';
	}

	/**
	 * Enqueue required CSS, JS, and localization for installer.
	 *
	 * @since 1.0.0
	 *
	 * @hook: admin_enqueue_scripts
	 */
	public function enqueue( $filter ) {

		// Check that we are on the plugin install page and in the BoldGrid tab before loading scripts.
		if ( $filter === 'plugin-install.php' && ( ! isset( $_GET['tab'] ) || isset( $_GET['tab'] ) && $_GET['tab'] === 'boldgrid' ) ) {

			// Enqueue Javascript.
			wp_enqueue_script(
				'bglib-plugin-installer',
				Library\Configs::get( 'libraryUrl' ) . 'src/assets/js/plugin-installer.js',
				array(
					'jquery',
					'plugin-install',
					'updates',
				)
			);

			// Add localized variables.
			wp_localize_script(
				'bglib-plugin-installer',
				'_bglibPluginInstaller',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'bglibPluginInstallerNonce' ),
					'install' => __( 'Install Now', 'boldgrid-library' ),
					'installing' => __( 'Installing', 'boldgrid-library' ),
					'installed' => __( 'Activated', 'boldgrid-library' ),
					'activate' => __( 'Activate', 'boldgrid-library' ),
					'activating' => __( 'Activating', 'boldgrid-library' ),
				)
			);

			// Enqueue CSS.
			wp_enqueue_style(
				'bglib-plugin-installer',
				Library\Configs::get( 'libraryUrl' ) .  'src/assets/css/plugin-installer.css'
			);
		}
	}

	/**
	 * Hides inaccurate info on plugin search results loaded from
	 * external sources.
	 *
	 * @since 1.0.0
	 *
	 * @hook: admin_head-plugin-install.php
	 */
	public function hideInfo() {
		$boldgrid_plugins = get_site_transient( 'boldgrid_plugins' );

		$css = '<style>';

		foreach ( $boldgrid_plugins as $plugin => $details ) {

			// Feedback/rating details.
			$css .= ".plugin-card-{$plugin} .vers.column-rating{display:none;}";

			// Active install counts.
			$css .= ".plugin-card-{$plugin} .column-downloaded{display:none;}";
		}

		$css .= '</style>';

		echo $css;
	}

	/**
	 * Prepares the data for the WordPress Plugins API.
	 *
	 * @return [type] [description]
	 */
	public function getPluginInformation( $plugins ) {
		global $wp_version;

		// If we don't find any data in our transient storage, make remote request.
		if ( ! $this->getTransient() ) {

			// Load plugin.php if necessary so method doesn't cause errors.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			// Get the API URL and Endpoint to call for requests.
			$api = Library\Configs::get( 'api' );
			$endpoint = '/api/open/getPluginVersion';

			// Each call will be stored in the $responses object before saving transient.
			$responses = new \stdClass();

			// Loop through plugins and save the data to transient.
			foreach ( $plugins as $plugin => $details ) {

				// Default data to use if plugin isn't loaded locally.
				$data = array(
					'Author' => 'BoldGrid.com',
					'Version' => null,
				);

				// Check that file exists and is readable before getting plugin data.
				$file = trailingslashit( WP_PLUGIN_DIR ) . $details['file'];
				if ( file_exists( $file ) && is_readable( $file ) ) {
					$data = get_plugin_data( $file, false );
				}

				$releaseChannel = new Library\ReleaseChannel;

				// Params to pass to remote API call.
				$params = array(
					'key' => $details['key'],
					'channel' => $releaseChannel->getPluginChannel(),
					'installed_' . $details['key'] . '_version' => $data['Version'],
					'installed_wp_version' => $wp_version,
				);

				// Make API Call.
				$call = new Library\Api\Call( $api . $endpoint, $params );

				// ** NOTE ** The next 95 lines are only here because we don't have the data
				// being returned that the WordPress Plugins API expects.
				// No errors when making remote request so begin preparing data for save.
				if ( ! $call->getError() ) {

					// Add the actual data to our $responses object.
					$responses->{$plugin} = $call->getResponse()->result->data;

					// Name is the expected value for the plugins_api, but we are returned title.
					$responses->{$plugin}->name = $responses->{$plugin}->title;

					// Slug is never returned from remote call, so we will create slug based on the title.
					$responses->{$plugin}->slug = sanitize_title( $responses->{$plugin}->title );

					// This was in the update class, but with/without it - it seemed to work.  Not sure if this
					// is really necessary to do as the data should be prepared on the asset server first.
					$responses->{$plugin}->sections = preg_replace(
						'/\s+/', ' ',
						trim( $responses->{$plugin}->sections )
					);

					// This part is json encoded on the asset server.  It might be better to just json_encode the
					// entire response, because it's hard to work with parts of this response.
					$responses->{$plugin}->sections = json_decode( $responses->{$plugin}->sections, true );

					// Loop through the decoded sections and then add to sections array.
					foreach ( $responses->{$plugin}->sections as $section => $section_data ) {
						$responses->{$plugin}->sections[ $section ] = html_entity_decode(
							$section_data,
							ENT_QUOTES
						);
					}

					// Set the short_description as the description before preparing.
					$responses->{$plugin}->short_description = $responses->{$plugin}->sections['description'];

					// Now generate the short_description from the standard description automatically.
					if ( mb_strlen( $responses->{$plugin}->short_description ) > 150 ) {
						$responses->{$plugin}->short_description = mb_substr( $responses->{$plugin}->short_description, 0, 150 ) . ' &hellip;';

						// If not a full sentence, and one ends within 20% of the end, trim it to that.
						if ( function_exists( 'mb_strrpos' ) ) {
							$pos = mb_strrpos( $responses->{$plugin}->short_description, '.' );
						} else {
							$pos = strrpos( $responses->{$plugin}->short_description, '.' );
						}
						if ( '.' !== mb_substr( $responses->{$plugin}->short_description, - 1 ) && $pos > ( 0.8 * 150 ) ) {
							$responses->{$plugin}->short_description = mb_substr( $responses->{$plugin}->short_description, 0, $pos + 1 );
						}
					}

					// Remove any remaining markup from short_description.
					$responses->{$plugin}->short_description = wp_strip_all_tags( $responses->{$plugin}->short_description );

					// Returned release date === last_updated (?)
					$responses->{$plugin}->last_updated = $responses->{$plugin}->release_date;

					// Create the author URL based on the siteurl and author name of plugin library is being ran in.
					// @todo: This should be handled by the API call as well.
					$responses->{$plugin}->author = '<a href="' . $responses->{$plugin}->siteurl . '">' . $data['Author'] . '</a>';

					// The filtering on the plugins add new expects download count to be present and errors out without it.
					$responses->{$plugin}->active_installs = $responses->{$plugin}->downloads;

					// This has to be json decoded since this array is json encoded for whatever reason.
					$responses->{$plugin}->banners = json_decode( $responses->{$plugin}->banners, true );

					// Just creating the links by having the file naming standardized.  WordPress also expects this same
					// format for these files.  The same approach can be taken for the banners as well.
					// @todo: imgs were uploaded to repo-dev. temporarily for testing. This should be updated to repo.
					$responses->{$plugin}->icons = array(
						'1x' => "https://repo.boldgrid.com/assets/icon-{$responses->{$plugin}->slug}-128x128.png",
						'2x' => "https://repo.boldgrid.com/assets/icon-{$responses->{$plugin}->slug}-256x256.png",
						'svg' => "https://repo.boldgrid.com/assets/icon-{$responses->{$plugin}->slug}-128x128.svg",
					);

					// This seems hardcoded in based on looking at our plugins in the update class.
					$responses->{$plugin}->added = '2015-03-19';

					// Newest version of plugin (?).
					$responses->{$plugin}->new_version = $responses->{$plugin}->version;

					// Setting the returned siteurl as the expected url param.
					$responses->{$plugin}->url = $responses->{$plugin}->siteurl;

					// Build the URL for the plugin download from the asset server.
					$responses->{$plugin}->download_link = add_query_arg(
						array(
							'key' => Library\Configs::get( 'key' ),
							'id' => $responses->{$plugin}->asset_id,
							'installed_plugin_version' => $data['Version'],
							'installed_wp_version' => $wp_version,
						),
						$api . '/api/asset/get'
					);
				}
			}

			// Now we have the correct data so save it in the boldgrid_plugins transient.  Expiry set to 1 week.
			if ( ! empty( $responses ) ) {
				set_site_transient( 'boldgrid_plugins', $responses, 7 * DAY_IN_SECONDS );
			}
		}
	}

	/**
	 * Gets configs class property.
	 *
	 * @since  1.0.0
	 *
	 * @return array $configs The configs class property.
	 */
	protected function getConfigs() {
		return $this->configs;
	}

	/**
	 * Gets the transient class property.
	 *
	 * @since  1.0.0
	 *
	 * @nohook
	 *
	 * @return mixed $transient The transient class property.
	 */
	public function getTransient() {
		return $this->transient;
	}
}
