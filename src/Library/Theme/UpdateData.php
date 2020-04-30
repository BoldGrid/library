<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * BoldGrid Library Update Data.
 *
 * Library package uses different naming convention.
 * phpcs:disable WordPress.NamingConventions.ValidVariableName
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName
 *
 * @package Boldgrid\Theme
 *
 * @since 2.12.2
 *
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Library\Library\Theme;

use Boldgrid\Library\Library\Theme\Theme;

/**
 * Update Data Class.
 *
 * This class stores the update data for a given theme.
 * Boldgrid\Library\Library\Theme\Theme class.
 *
 * @since 2.12.0
 */
class UpdateData {
	/**
	 * Theme.
	 *
	 * @since 2.12.2
	 * @var Theme
	 */
	public $theme;

	/**
	 * Themes.
	 *
	 * @since 2.12.2
	 * @var string
	 */
	public $themes;

	/**
	 * Release Date.
	 *
	 * @since 2.12.2
	 * @var string
	 */
	public $releaseDate;

	/**
	 * Version.
	 *
	 * @since 2.12.2
	 * @var string
	 */
	public $version;

	/**
	 * Days Since Release.
	 *
	 * @since 2.12.2
	 * @var int
	 */
	public $days;

	/**
	 * Response Data.
	 *
	 * @since 2.12.2
	 * @var Response
	 * @access private
	 */
	private $responseData;

	/**
	 * Constructor.
	 *
	 * @since 2.12.2
	 *
	 * @param Theme  $theme The Theme Object.
	 * @param string $stylesheet Theme's Stylesheet.
	 */
	public function __construct( $theme = null, $stylesheet = null ) {
		// If a theme object is passed in constructer, use that, or else create a new one from stylesheet.
		$this->theme = ( null !== $theme ) ? $theme : new Theme( $stylesheet );

		$responseTransient = $this->getInformationTransient();

		if ( false !== $responseTransient ) {
			$this->responseData = (object) $responseTransient;
			$this->version      = $responseTransient['version'];
			$this->downloaded   = $responseTransient['downloaded'];
			$this->releaseDate  = $responseTransient['last_updated'];
		} else {
			$this->responseData = $this->fetchResponseData();
			$this->version      = $this->responseData->version;
			$this->downloaded   = $this->responseData->downloaded;
			$this->releaseDate  = new \DateTime( $this->responseData->last_updated );

			$this->setInformationTransient();
		}

		$now = new \DateTime();

		$this->days = date_diff( $now, $this->releaseDate )->format( '%a' );
	}

	/**
	 * Get Response Data.
	 *
	 * @since 2.12.2
	 *
	 * @return Response
	 */
	public function getResponseData() {
		return $this->responseData;
	}

	/**
	 * Set Response Data.
	 *
	 * @since 2.12.2
	 *
	 * @return Response
	 */
	public function fetchResponseData() {
		include_once ABSPATH . 'wp-admin/includes/theme.php';

		$is_timely_updates = apply_filters( 'boldgrid_backup_is_timely_updates', false );

		if ( $is_timely_updates ) {
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
			);
		} else {
			$theme_information = $this->getGenericInfo( new \WP_Error( 'Timely Updates Not Enabled' ) );
			return (object) $theme_information;
		}
		if ( is_a( $theme_information, 'WP_Error' ) ) {
			$theme_information = $this->getGenericInfo( $theme_information );
			return (object) $theme_information;
		}

		return $theme_information;
	}

	/**
	 * Get Theme Information from Transient.
	 *
	 * @since 2.12.2
	 *
	 * @return array
	 */
	public function getInformationTransient() {
		$transient = get_transient( 'boldgrid_theme_information' );
		if ( false === $transient ) {
			return false;
		}
		if ( array_key_exists( $this->theme->stylesheet, $transient ) ) {
			return $transient[ $this->theme->stylesheet ];
		}
		return false;
	}

	/**
	 * Set Theme Information Transient.
	 *
	 * @since 2.12.2
	 */
	public function setInformationTransient() {
		$transient = get_transient( 'boldgrid_theme_information' );
		if ( false === $transient ) {
			$transient = array();
		}

		$transient[ $this->theme->stylesheet ] = array(
			'version'      => $this->version,
			'downloaded'   => $this->downloaded,
			'last_updated' => $this->releaseDate,
		);

		$is_timely_updates = apply_filters( 'boldgrid_backup_is_timely_updates', false );
		if ( $is_timely_updates ) {
			set_transient( 'boldgrid_theme_information', $transient, 3600 );
		}
	}

	/**
	 * Plugins Api Failed.
	 *
	 * @since 2.12.2
	 *
	 * @param \WP_Error $errors WordPress error returned by themes_api().
	 */
	public function getGenericInfo( \WP_Error $errors ) {
		$current     = get_site_transient( 'update_themes' );
		$new_version = isset( $current->checked[ $this->theme->stylesheet ] ) ? $current->checked[ $this->theme->stylesheet ] : '';

		$theme_information = array(
			'active_installs' => '0',
			'version'         => $new_version,
			'downloaded'      => '000000',
			'last_updated'    => gmdate( 'Y-m-d H:i:s', 1 ),
			'third_party'     => true,
		);

		return $theme_information;
	}
}
