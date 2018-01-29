<div id="boldgrid_claim_premium_notice"
	class="boldgrid-notice library notice notice-warning is-dismissible"
	data-notice-id="bg-claim-premium">
	<p>
<?php
printf(
	esc_html__(
		'Thank you for your Envato Market purchase.%sPlease visit %sBoldGrid Central%s to link your accounts and claim your Premium Connect Key.',
		'boldgrid-inspirations'
	),
	'<br />',
	'<a target="_blank" href="https://www.boldgrid.com/central/code/envato">',
	'</a>'
);

wp_nonce_field( 'boldgrid_set_key', 'set_key_auth' );
?>
	</p>
</div>
