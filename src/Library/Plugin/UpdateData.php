<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * BoldGrid Library Update Data.
 *
 * Library package uses different naming convention.
 * phpcs:disable WordPress.NamingConventions.ValidVariableName
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName
 *
 * @package Boldgrid\Plugin
 *
 * @since SINCEVERSION
 *
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Library\Library\Plugin;

use Boldgrid\Library\Library\Plugin\Plugin;

/**
 * Update Data Class.
 *
 * This class stores the update data for a given plugin.
 * Boldgrid\Library\Library\Plugin\Plugin class.
 *
 * @since 2.12.0
 */
class UpdateData {
	/**
	 * Plugin.
	 *
	 * @since SINCEVERSION
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Active Installs.
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $activeInstalls;

	/**
	 * Minor Version Installs.
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $minorVersionInstalls;

	/**
	 * Release Date.
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $releaseDate;

	/**
	 * Version.
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $version;

	/**
	 * Minor Version.
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $minorVersion;

	/**
	 * Stats.
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $stats;

	/**
	 * Days Since Release.
	 *
	 * @since SINCEVERSION
	 * @var int
	 */
	public $days;

	/**
	 * Response Data.
	 *
	 * @since SINCEVERSION
	 * @var Response
	 * @access private
	 */
	private $responseData;

	/**
	 * Constructor.
	 *
	 * @since SINCEVERSION
	 *
	 * @param Plugin $plugin The plugin we are getting data for.
	 * @param string $slug Optional slug of plugin if plugin object not given.
	 */
	public function __construct( $plugin = null, $slug = null ) {
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		// If a plugin object is passed in constructer, use that, or else create a new one from slug.
		$this->plugin = ( null !== $plugin ) ? $plugin : new Plugin( $slug );

		$responseTransient = $this->getInformationTransient();

		if ( false !== $responseTransient ) {
			$this->responseData   = (object) $responseTransient;
			$this->activeInstalls = $responseTransient['active_installs'];
			$this->version        = $responseTransient['version'];
			$this->downloaded     = $responseTransient['downloaded'];
			$this->releaseDate    = $responseTransient['last_updated'];
			$this->stats          = $responseTransient['stats'];
			$this->thirdParty     = $responseTransient['third_party'];
		} else {
			$this->responseData   = $this->fetchResponseData();
			$this->activeInstalls = isset( $this->responseData->active_installs ) ? $this->responseData->active_installs : '0';
			$this->version        = isset( $this->responseData->version ) ? $this->responseData->version : null;
			$this->downloaded     = isset( $this->responseData->downloaded ) ? $this->responseData->downloaded : '0';
			$this->releaseDate    = isset( $this->responseData->last_updated ) ? new \DateTime( $this->responseData->last_updated ) : new \DateTime( gmdate( 'Y-m-d H:i:s', 1 ) );
			$this->stats          = ( $this->fetchPluginStats() ) ? $this->fetchPluginStats() : array();
			$this->thirdParty     = isset( $this->responseData->third_party ) ? $this->responseData->third_party : false;
		}
		$this->setInformationTransient();

		$now        = new \DateTime();
		$this->days = date_diff( $now, $this->releaseDate )->format( '%a' );
	}

	/**
	 * Get Minor Version Installs.
	 *
	 * @since SINCEVERSON
	 * @return int
	 */
	public function getMinorVersionInstalls() {
		foreach ( $this->stats as $minorVersion => $percentInstalls ) {
			if ( $minorVersion === $this->minorVersion ) {
				$x = $percentInstalls / 100;
				return $this->activeInstalls * $x;
			}
		}
		$now        = new \DateTime();
		$this->days = date_diff( $now, $this->releaseDate )->format( '%a' );
	}

	/**
	 * Get Response Data.
	 *
	 * @since SINCEVERSION
	 *
	 * @return Response
	 */
	public function getResponseData() {
		return $this->responseData;
	}

	/**
	 * Set Response Data.
	 *
	 * @since SINCEVERSION
	 *
	 * @return Response
	 */
	public function fetchResponseData() {
		$plugin_information = plugins_api(
			'plugin_information',
			array(
				'slug'   => $this->plugin->getSlug(),
				'fields' => array(
					'downloaded',
					'last_updated',
					'active_installs',
				),
			)
		);

		if ( is_a( $plugin_information, 'WP_Error' ) ) {
			$plugin_information = $this->getGenericInfo( $plugin_information );
			return (object) $plugin_information;
		}

		return $plugin_information;
	}

	/**
	 * Get Plugin Stats.
	 *
	 * @since SINCEVERSION
	 *
	 * @return Array
	 */
	public function fetchPluginStats() {

		$response = wp_remote_get( 'https://api.wordpress.org/stats/plugin/1.0/' . $this->plugin->getSlug() );

		if ( $response && array_key_exists( 'body', $response ) ) {
			$response = $response['body'];
		} else {
			return false;
		}

		$stats = array();
		if ( false !== $response ) {
			$response = json_decode( $response, true );

			if ( json_last_error() === JSON_ERROR_NONE && is_array( $response ) ) {
				$stats = $response;
			}
		}
		return $stats;
	}

	/**
	 * Get Plugin Information from Transient.
	 *
	 * @since SINCEVERSION
	 *
	 * @return array
	 */
	public function getInformationTransient() {
		$transient = get_transient( 'boldgrid_plugin_information' );
		if ( false === $transient ) {
			return false;
		}

		if ( array_key_exists( $this->plugin->getSlug(), $transient ) ) {
			return $transient[ $this->plugin->getSlug() ];
		}

		return false;
	}

	/**
	 * Set Plugin Information Transient.
	 *
	 * @since SINCEVERSION
	 */
	public function setInformationTransient() {
		$transient = get_transient( 'boldgrid_plugin_information' );
		if ( false === $transient ) {
			$transient = array();
		}

		$transient[ $this->plugin->getSlug() ] = array(
			'active_installs' => $this->activeInstalls,
			'version'         => $this->version,
			'downloaded'      => $this->downloaded,
			'last_updated'    => $this->releaseDate,
			'stats'           => $this->stats,
			'third_party'     => $this->thirdParty,
		);

		set_transient( 'boldgrid_plugin_information', $transient, 60 );
	}

	/**
	 * Plugins Api Failed.
	 *
	 * @since SINCEVERSION
	 *
	 * @param \WP_Error $errors WordPress error returned by plugins_api().
	 */
	public function getGenericInfo( \WP_Error $errors ) {
		$current     = get_site_transient( 'update_plugins' );
		$new_version = isset( $current->response[ $this->plugin->getFile() ] ) ? $current->response[ $this->plugin->getFile() ]->new_version : '';

		$plugin_information = array(
			'active_installs' => '0',
			'version'         => $new_version,
			'downloaded'      => '000000',
			'last_updated'    => gmdate( 'Y-m-d H:i:s', 1 ),
			'third_party'     => true,
		);

		return $plugin_information;
	}
}
