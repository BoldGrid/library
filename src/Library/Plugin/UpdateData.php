<?php
/**
 * BoldGrid Library Update Data.
 *
 * @package Boldgrid\Plugin
 *
 * @since SINCEVERSION
 *
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Library\Library\Plugin;

/**
 * Update Data Class.
 *
 * This class stores the update data for a given plugin
 * Boldgrid\Library\Library\Plugin\Plugin class.
 *
 * @since 2.12.0
 */
class UpdateData {
	/**
	 * Plugin
	 *
	 * @since SINCEVERSION
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Active Installs
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $activeInstalls;

	/**
	 * Minor Version Installs
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $minorVersionInstalls;

	/**
	 * Release Date
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $releaseDate;

	/**
	 * Version
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $version;

	/**
	 * Minor Version
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $minorVersion;

	/**
	 * Stats
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $stats;

	/**
	 * Days Since Release
	 *
	 * @since SINCEVERSION
	 * @var int
	 */
	public $days;

	/**
	 * Response Data
	 *
	 * @since SINCEVERSION
	 * @var Response
	 * @access private
	 */
	private $responseData;

	/**
	 * Constructor
	 *
	 * @since SINCEVERSION
	 *
	 * @param Plugin $plugin
	 * @param string $slug
	 */
	public function __construct( $plugin = null, $slug = null ) {
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		// If a plugin object is passed in constructer, use that, or else create a new one from slug.
		$this->plugin = ( null !== $plugin ) ? $plugin : new Plugin( $slug );

		$responseTransient = $this->getInformationTransient();

		if ( false !== $responseTransient ) {
			$this->activeInstalls = $responseTransient['activeInstalls'];
			$this->version        = $responseTransient['version'];
			$this->downloaded     = $responseTransient['downloaded'];
			$this->releaseDate    = $responseTransient['releaseDate'];
			$this->stats          = $responseTransient['stats'];
		}  else {
			$responseData = $this->fetchResponseData();
			$this->activeInstalls = $responseData->active_installs;
			$this->version        = $responseData->version;
			$this->downloaded     = $responseData->downloaded;
			$this->releaseDate    = new \DateTime( $responseData->last_updated );
			$this->stats          = $this->fetchPluginStats();
		}
		$this->setInformationTransient();

		$version_array      = explode( '.', $this->version );
		
		$this->minorVersion = implode( '.', [ $version_array[0], $version_array[1] ] );

		$this->minorVersionInstalls = $this->getMinorVersionInstalls();

		$now = new \DateTime();

		$this->days = date_diff( $now, $this->releaseDate )->format('%a');
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
	}
	/**
	 * Get Response Data
	 *
	 * @since SINCEVERSION
	 *
	 * @return Response
	 */
	public function getResponseData() {
		return $this->responseData;
	}

	/**
	 * Set Response Data
	 * 
	 * @since SINCEVERSION
	 * 
	 * @return Response
	 */
	public function fetchResponseData() {
		$plugin_information = plugins_api(
			'plugin_information',
			[
				'slug' => $this->plugin->getSlug(),
				'fields' => [
					'downloaded',
					'last_updated',
					'active_installs',
				],
			]
		);
		if ( is_a( $plugin_information, 'WP_Error' ) ) {
			$plugin_information = [
				'active_installs' => '40000',
				'version'        => '1.13.1',
				'downloaded'     => '123456789',
				'last_updated'   => date('Y-m-d H:i:s'),
			];
			return (object) $plugin_information;
		}

		return $plugin_information;
	}

	/**
	 * Get Plugin Stats
	 * 
	 * @since SINCEVERSION
	 * 
	 * @return Array
	 */
	public function fetchPluginStats() {

		$response = wp_remote_get('https://api.wordpress.org/stats/plugin/1.0/' . $this->plugin->getSlug() );

		if ( array_key_exists('body', $response) ) {
			$response = $response['body'];
		} else {
			$response = false;
		}
		
		$stats = [];
		if (false !== $response) {
			$response = json_decode($response, true);

			if (json_last_error() === JSON_ERROR_NONE && is_array($response)) {
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
		$transient = get_transient('plugin_information');
		if ( false === $transient ) {
			return false;
		}

		if ( array_key_exists( $this->plugin->getSlug(), $transient ) ) {
			return $transient[$this->plugin->getSlug()];
		}

		return false;
	}

	/**
	 * Set Plugin Information Transient
	 * 
	 * @since SINCEVERSION
	 */
	public function setInformationTransient() {
		$transient = get_transient('plugin_information');
		if ( false === $transient ) {
			$transient = [];
		}

		$transient[$this->plugin->getSlug()] = [
			'activeInstalls' => $this->activeInstalls,
			'version'        => $this->version,
			'downloaded'     => $this->downloaded,
			'releaseDate'    => $this->releaseDate,
			'stats'          => $this->stats,
		];

		set_transient( 'plugin_information', $transient, 60);
	}
}