<div id="container_boldgrid_connection_notice" class="error">
	<h2 class="dashicons-before dashicons-admin-network">
		<?php esc_html_e( 'BoldGrid Connection Issue', 'boldgrid-inspirations' ); ?>
	</h2>
	<p>
		<?php esc_html_e( 'There was an issue reaching the BoldGrid Connect server. Some BoldGrid features may be temporarily unavailable. Please try again in a moment.', 'boldgrid-inspirations' ); ?>
	</p>
	<p>
		<?php printf(
			esc_html__( 'If the issue persists, then please feel free to check our %sBoldGrid Status%s page.', 'boldgrid-inspirations' ),
			'<a target="_blank" href="https://www.boldgrid.com/">',
			'</a>'
		); ?>
	</p>
</div>
