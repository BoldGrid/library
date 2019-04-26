<?php
/**
 * BoldGrid Library News Widget Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library\License
 *
 * @version 2.9.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library News Widget Class.
 *
 * @since 2.9.0
 */
class NewsWidget {
	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.9.0
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_register_style(
			'bglib-news-widget-css',
			Configs::get( 'libraryUrl' ) . 'src/assets/css/news-widget.css'
		);

		switch( $hook ) {
			case 'index.php':
				wp_enqueue_style( 'bglib-news-widget-css' );
				break;
		}
	}

	/**
	 * Display the inner contents of our news widgget.
	 *
	 * @since 2.9.0
	 */
	public function display() {
		$postsToShow = 2;
		$postsShown  = 0;

		// Get the id of the "dashboard" tag.
		$dashboardId = $this->getDashboardId();

		// Get all posts tagged, "dashboard".
		$request = wp_remote_get( 'https://www.boldgrid.com/wp-json/wp/v2/posts/?tags=' . $dashboardId );

		// If we have an error, abort.
		if( is_wp_error( $request ) ) {
			if ( is_admin() || current_user_can( 'manage_options' ) ) {
				echo '<p>' . sprintf(
					// translators: An RSS error message.
					__( '<strong>RSS Error</strong>: %s', 'boldgrid-inspirations' ),
					esc_html( $request->get_error_message() )
				) . '</p>';
			}

			return;
		}

		// Get our "dashboard" posts.
		$body  = wp_remote_retrieve_body( $request );
		$posts = json_decode( $body );

		// If we have no news posts, abort.
		if( empty( $posts ) ) {
			echo '<p>' . esc_html__( 'There are no updates to show right now.', 'boldgrid-library' ) . '</p>';

			return;
		}

		echo '<ul>';
		foreach ( $posts as $post ) {
			$featuredImage = '';
			if ( ! empty( $post->_links->{'wp:featuredmedia'}[0]->href ) ) {
				$featuredImage = $this->getMediaUrl( $post->_links->{'wp:featuredmedia'}[0]->href );
			}
?>			<li title="<?php echo esc_attr( $post->excerpt->rendered ); ?>">
				<?php if ( ! empty( $featuredImage ) ) {
					echo '<img class="bglib-featured" src="' . esc_url( $featuredImage ) . '" />';
				} ?>
				<p class="bglib-date">
					<?php echo date( 'M jS, Y', strtotime( $post->date ) ); ?>
				</p>
				<p class="bglib-title">
					<a href='<?php echo $post->link ?>' target='_blank'><?php echo $post->title->rendered; ?></a>
				</p>
			</li>
<?php		$postsShown++;
			if ( $postsShown >= $postsToShow ) {
				break;
			}
 		}
		echo '</ul><div style="clear:both;"></div>';
	}

	/**
	 * Get the id of the "dashboard" tag.
	 *
	 * @since 2.9.0
	 *
	 * @return int
	 */
	public function getDashboardId() {
		$id = 0;

		$request = wp_remote_get( 'https://www.boldgrid.com/wp-json/wp/v2/tags/?slug=dashboard' );

		if( is_wp_error( $request ) ) {
			return $id;
		}

		$body = wp_remote_retrieve_body( $request );
		$body = json_decode( $body );

		$id = ! empty( $body[0]->id ) ? $body[0]->id : $id;

		return $id;
	}

	/**
	 * Get the url to an image.
	 *
	 * This method assumes you have a media post, as retrieved via a wp-json call.
	 *
	 * @since 2.9.0
	 *
	 * @param  string $url  The url to a media page, such as http://domain.com/wp-json/wp/v2/media/1234
	 * @param  string $size The image size to get.
	 * @return string
	 */
	public function getMediaUrl( $url, $size = 'medium' ) {
		$mediaUrl = '';

		$request = wp_remote_get( $url );

		if( is_wp_error( $request ) ) {
			return $mediaUrl;
		}

		$body = wp_remote_retrieve_body( $request );
		$body = json_decode( $body );

		$mediaUrl = ! empty( $body->media_details->sizes->$size->source_url ) ? $body->media_details->sizes->$size->source_url : $mediaUrl;

		return $mediaUrl;
	}

	/**
	 * Add our news widget to the dashboard.
	 *
	 * Priority 5 given for backwards compatibility with BoldGrid Inspirations. If Inspirations is
	 * trying to add the news widget, this method will go first and take precedence.
	 *
	 * @since 2.9.0
	 *
	 * @priority 5
	 */
	public function wp_dashboard_setup() {
		wp_add_dashboard_widget(
			'boldgrid_news_widget',
			esc_html__( 'BoldGrid News', 'boldgrid-library' ),
			array(
				$this,
				'display',
			)
		);
	}
}
