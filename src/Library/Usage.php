<?php
/**
 * BoldGrid Library Usage Class
 *
 * @package Boldgrid\Library
 *
 * @version SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

use Boldgrid\Library\Library\Usage\Helper;

/**
 * BoldGrid Library Usage Class.
 *
 * @since SINCEVERSION
 */
class Usage {
	/**
	 * Whether or not we've already added the filters in the constructor.
	 *
	 * Filters only need to be added once.
	 *
	 * @since SINCEVERSION
	 * @access private
	 * @var bool
	 */
	private static $filtersAdded = false;

	/**
	 * GA ID.
	 *
	 * @since SINCEVERSION
	 * @access private
	 * @var string
	 */
	private $gaId = 'UA-1501988-42';

	/**
	 * An array of page prefixes to load usage tracking on.
	 *
	 * @since SINCEVERSION
	 * @access private
	 * @var array
	 */
	private $prefixes;

	/**
	 * Construct.
	 *
	 * Do not instantiate this class unless you're sure you want to collect usage data.
	 *
	 * @since SINCEVERSION
	 */
	public function __construct() {
		// Only add the filters once.
		if ( ! self::$filtersAdded ) {
			Filter::add( $this );
			self::$filtersAdded = true;
		}
	}

	/**
	 * Add tag.
	 *
	 * @since SINCEVERSION
	 */
	public function addTag() {
		?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $this->gaId; ?>"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){ dataLayer.push( arguments ); }
			gtag( 'js', new Date() );
			gtag( 'config', '<?php echo $this->gaId; ?>', { send_page_view: false } );
		</script>
		<?php
	}

	/**
	 * Admin enqueue scripts.
	 *
	 * @since SINCEVERSION
	 */
	public function admin_enqueue_scripts() {
		if ( $this->maybeLoad() ) {
			// Include license data.
			if ( ! apply_filters( 'Boldgrid\Library\Key\hasKey', false ) ) {
				$licenseData = [ 'no-key' ];
			} else {
				$licenseData = $this->getLicenseData();
			}

			$handle = 'bglib-usage';

			wp_register_script(
				$handle,
				Configs::get( 'libraryUrl' ) . 'src/assets/js/usage.js',
				'jquery',
				date( 'Ymd' )
			);

			$translation = [
				'page'    => ! empty ( $_GET['page'] ) ? $_GET['page'] : '',
				'ga_id'   => $this->gaId,
				'license' => json_encode( $licenseData ),
			];

			wp_localize_script( $handle, 'BglibUsage', $translation );

			wp_enqueue_script( $handle );
		}
	}

	/**
	 * Admin head.
	 *
	 * @since SINCEVERSION
	 */
	public function admin_head() {
		if ( $this->maybeLoad() ) {
			$this->addTag();
		}
	}

	/**
	 * Get license data.
	 *
	 * @since SINCEVERSION
	 *
	 * @return array
	 */
	private function getLicenseData() {
		$licenseData = apply_filters( 'Boldgrid\Library\License\getData', false );
		$licenseData = is_object( $licenseData ) ? get_object_vars( $licenseData ) : [];

		// Remove things we don't need.
		unset( $licenseData['issuedAt'] );
		unset( $licenseData['refreshBy'] );

		return array_keys( $licenseData );
	}

	/**
	 * Determine whether or not we should load and track usage.
	 *
	 * @since SINCEVERSION
	 *
	 * @return bool
	 */
	private function maybeLoad() {
		$hasKey     = apply_filters( 'Boldgrid\Library\Key\hasKey', false );
		$userAgreed = \Boldgrid\Library\Library\Usage\Settings::hasAgreed();

		/*
		 * In this class' initial implementation, determining whether or not to track usage is done
		 * based on whether the page begins with boldgrid-backup. In the future, this logic may need
		 * to be expanded upon.
		 */
		return ( $hasKey || $userAgreed ) && Helper::hasPagePrefix();
	}
}