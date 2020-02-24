<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * BoldGrid Library Update Data.
 *
 * Library package uses different naming convention
 * phpcs:disable WordPress.NamingConventions.ValidVariableName
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName
 *
 * @package Boldgrid\Theme
 *
 * @since SINCEVERSION
 *
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Library\Library\Theme;

use Boldgrid\Library\Library\Theme\Theme;

/**
 * Update Data Class.
 *
 * This class stores the update data for a given theme
 * Boldgrid\Library\Library\Theme\Theme class.
 *
 * @since 2.12.0
 */
class UpdateData {
	/**
	 * Theme
	 *
	 * @since SINCEVERSION
	 * @var Theme
	 */
	public $theme;

	/**
	 * Themes
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $themes;

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
	 * @param Theme  $theme The Theme Object.
	 * @param string $stylesheet Theme's Stylesheet.
	 */
	public function __construct( $theme = null, $stylesheet = null ) {

		// If a plugin object is passed in constructer, use that, or else create a new one from slug.
		$this->theme = ( null !== $theme ) ? $theme : new Theme( $stylesheet );

		$responseTransient = $this->getInformationTransient();

		if ( false !== $responseTransient ) {
			$this->version     = $responseTransient['version'];
			$this->downloaded  = $responseTransient['downloaded'];
			$this->releaseDate = $responseTransient['releaseDate'];
		} else {
			$responseData      = $this->fetchResponseData();
			$this->version     = $responseData->version;
			$this->downloaded  = $responseData->downloaded;
			$this->releaseDate = new \DateTime( $responseData->last_updated );
		}
		$this->setInformationTransient();

		$now = new \DateTime();

		$this->days = date_diff( $now, $this->releaseDate )->format( '%a' );
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
		include_once ABSPATH . 'wp-admin/includes/theme.php';
		$theme_information = themes_api(
			'theme_information',
			[
				'slug'   => $this->theme->stylesheet,
				'fields' => [
					'downloaded',
					'last_updated',
					'active_installs',
				],
			]
		);

		return $theme_information;
	}

	/**
	 * Get Theme Information from Transient.
	 *
	 * @since SINCEVERSION
	 *
	 * @return array
	 */
	public function getInformationTransient() {
		$transient = get_transient( 'theme_information' );
		if ( false === $transient ) {
			return false;
		}

		if ( array_key_exists( $this->theme->stylesheet, $transient ) ) {
			return $transient[ $this->theme->stylesheet ];
		}

		return false;
	}

	/**
	 * Set Theme Information Transient
	 *
	 * @since SINCEVERSION
	 */
	public function setInformationTransient() {
		$transient = get_transient( 'theme_information' );
		if ( false === $transient ) {
			$transient = [];
		}

		$transient[ $this->theme->stylesheet ] = [
			'version'     => $this->version,
			'downloaded'  => $this->downloaded,
			'releaseDate' => $this->releaseDate,
		];

		set_transient( 'theme_information', $transient, 60 );
	}
}
