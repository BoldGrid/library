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
<<<<<<< HEAD
	 * @param Theme  $theme The Theme Object.
	 * @param string $stylesheet Theme's Stylesheet.
	 */
	public function __construct( $theme = null, $stylesheet = null ) {

		// If a plugin object is passed in constructer, use that, or else create a new one from slug.
		$this->theme = ( null !== $theme ) ? $theme : new Theme( $stylesheet );
=======
	 * @param Theme $theme
	 * @param string $slug
	 */
	public function __construct( $theme = null, $stylesheet = null ) {

		// If a plugin object is passed in constructer, use that, or else create a new one from slug.
<<<<<<< HEAD
		$this->theme = ( null !== $theme ) ? $theme : new Theme( $slug );
>>>>>>> added theme functionality
=======
		$this->theme = ( null !== $theme ) ? $theme : new Theme( $stylesheet );
>>>>>>> added additional theme update functionality

		$responseTransient = $this->getInformationTransient();

		if ( false !== $responseTransient ) {
<<<<<<< HEAD
			$this->version     = $responseTransient['version'];
			$this->downloaded  = $responseTransient['downloaded'];
			$this->releaseDate = $responseTransient['releaseDate'];
		} else {
			$responseData      = $this->fetchResponseData();
			$this->version     = $responseData->version;
			$this->downloaded  = $responseData->downloaded;
			$this->releaseDate = new \DateTime( $responseData->last_updated );
=======
			$this->version        = $responseTransient['version'];
			$this->downloaded     = $responseTransient['downloaded'];
			$this->releaseDate    = $responseTransient['releaseDate'];
		}  else {
			$responseData = $this->fetchResponseData();
			$this->version        = $responseData->version;
			$this->downloaded     = $responseData->downloaded;
			$this->releaseDate    = new \DateTime( $responseData->last_updated );
>>>>>>> added theme functionality
		}
		$this->setInformationTransient();

		$now = new \DateTime();

<<<<<<< HEAD
		$this->days = date_diff( $now, $this->releaseDate )->format( '%a' );
=======
		$this->days = date_diff( $now, $this->releaseDate )->format('%a');
>>>>>>> added theme functionality
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
<<<<<<< HEAD
	 *
	 * @since SINCEVERSION
	 *
	 * @return Response
	 */
	public function fetchResponseData() {
		include_once ABSPATH . 'wp-admin/includes/theme.php';
		$theme_information = themes_api(
			'theme_information',
			array(
				'slug'   => $this->theme->stylesheet,
				'fields' => array(
					'downloaded',
					'last_updated',
					'active_installs',
				),
			)
=======
	 *
	 * @since SINCEVERSION
	 *
	 * @return Response
	 */
	public function fetchResponseData() {
		include_once( ABSPATH . 'wp-admin/includes/theme.php' );
		$theme_information = themes_api(
			'theme_information',
			[
				'slug' => $this->theme->stylesheet,
				'fields' => [
					'downloaded',
					'last_updated',
					'active_installs',
				],
			]
>>>>>>> added theme functionality
		);

		return $theme_information;
	}

	/**
	 * Get Theme Information from Transient.
<<<<<<< HEAD
	 *
	 * @since SINCEVERSION
	 *
	 * @return array
	 */
	public function getInformationTransient() {
		$transient = get_transient( 'boldgrid_theme_information' );
=======
	 *
	 * @since SINCEVERSION
	 *
	 * @return array
	 */
	public function getInformationTransient() {
		$transient = get_transient('theme_information');
>>>>>>> added theme functionality
		if ( false === $transient ) {
			return false;
		}

<<<<<<< HEAD
<<<<<<< HEAD
		if ( array_key_exists( $this->theme->stylesheet, $transient ) ) {
			return $transient[ $this->theme->stylesheet ];
=======
		if ( array_key_exists( $this->theme->getSlug(), $transient ) ) {
			return $transient[$this->theme->getSlug()];
>>>>>>> added theme functionality
=======
		if ( array_key_exists( $this->theme->stylesheet, $transient ) ) {
			return $transient[$this->theme->stylesheet];
>>>>>>> added additional theme update functionality
		}

		return false;
	}

	/**
	 * Set Theme Information Transient
<<<<<<< HEAD
	 *
	 * @since SINCEVERSION
	 */
	public function setInformationTransient() {
		$transient = get_transient( 'boldgrid_theme_information' );
		if ( false === $transient ) {
			$transient = array();
		}

		$transient[ $this->theme->stylesheet ] = array(
			'version'     => $this->version,
			'downloaded'  => $this->downloaded,
			'releaseDate' => $this->releaseDate,
		);

		set_transient( 'boldgrid_theme_information', $transient, 60 );
	}
}
=======
	 *
	 * @since SINCEVERSION
	 */
	public function setInformationTransient() {
		$transient = get_transient('theme_information');
		if ( false === $transient ) {
			$transient = [];
		}

		$transient[$this->theme->stylesheet] = [
			'version'        => $this->version,
			'downloaded'     => $this->downloaded,
			'releaseDate'    => $this->releaseDate,
		];

		set_transient( 'theme_information', $transient, 60);
	}
}
>>>>>>> added theme functionality
