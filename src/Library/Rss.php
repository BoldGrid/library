<?php
/**
 * BoldGrid Library RSS Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 2.9.0
 * @author BoldGrid <support@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library RSS Class.
 *
 * This class is responsible for rendering the BOldGrid RSS feed on the WordPress Dashboard.
 *
 * @since 2.9.0
 */
class Rss {
	/**
	 * Initialize class and set class properties.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Add the widget to the WordPress dashboard.
	 *
	 * @since 1.11.0
	 *
	 * @hook: wp_dashboard_setup
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'render_widget',
			esc_html__( 'BoldGrid News', 'boldgrid-library' ),
			[
				$this,
				'render_widget',
			]
		);
	}

	/**
	 * Render the RSS feed widget.
	 *
	 * @since 1.11.0
	 */
	public function render_widget() {
		// Get a SimplePie feed object from the specified feed source.
		$rss      = fetch_feed( 'https://www.boldgrid.com/tag/dashboard/feed/' );
		$maxItems = 0;

		if ( ! is_wp_error( $rss ) ) {
			// Figure out how many total items there are, but limit it to 5.
			$maxItems = $rss->get_item_quantity( 3 );

			// Build an array of all the items, starting with element 0 (first element).
			$rssItems = $rss->get_items( 0, $maxItems );
		}
		?>
		<ul>
			<?php
			if ( ! $maxItems ) {
				?>
				<li><?php esc_html_e( 'There are no updates to show right now.', 'boldgrid-library' ); ?></li>
				<?php
			} else {
				$dateFormat  = get_option( 'date_format' );
				$timeFormat  = get_option( 'time_format' );
				$allowedTags = [
					'a' => [
						'href'   => [],
						'target' => [],
						'title'  => [],
					],
				];

				foreach ( $rssItems as $item ) {
					?>
					<div id="boldgrid_rss_widget"><li>
						<?php
						wp_kses(
							// Translators: 1: Anchored URL address, 2: Hover text, 3: Itmm title.
							printf(
								'<span class="rss-title">
									<a class="rsswidget" target="_blank" href="%1$s" target="_blank">%2$s</a>
								</span>
								<span class="rss-date">%3$s</span>
								<div class="rssSummary">%4$s</div>',
								esc_url( $item->get_permalink() ),
								esc_html( $item->get_title() ),
								esc_attr( $item->get_date( 'l, ' . $dateFormat . ' ' . $timeFormat ) ),
								esc_html( wp_html_excerpt( $item->get_content(), 250 ) . ' ...' )
							),
							$allowedTags
						);
						?>
					</li></div>
					<?php
				}
			}
			?>
		</ul>
		<?php
	}
}
