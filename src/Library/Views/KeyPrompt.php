<div id="container_boldgrid_api_key_notice"
	class="boldgrid-notice library error notice is-dismissible"
	data-notice-id="bg-key-prompt">
	<div class="api-notice">
		<h2 class="dashicons-before dashicons-admin-network">
			<?php esc_html_e( 'Enter Your BoldGrid Connect Key', 'boldgrid-inspirations' ); ?>
		</h2>
		<p id="boldgrid_api_key_notice_message">
			<?php printf( esc_html__( 'Please enter your %s32 digit BoldGrid Connect Key%s below and click submit.', 'boldgrid-inspirations' ), '<b>', '</b>' ); ?>
		</p>
		<form id="boldgrid-api-form" autocomplete="off">
			<?php wp_nonce_field( 'boldgrid_set_key', 'set_key_auth' ); ?>
			<div class="tos-box">
				<input id="tos-box" type="checkbox" value="0">
				<?php printf( esc_html__( 'I agree to the %sTerms of Use and Privacy Policy%s.', 'boldgrid-inspirations' ), '<a href="https://www.boldgrid.com/software-privacy-policy/" target="_blank">', '</a>' ); ?>
			</div>
			<br />
			<input type="text" id="boldgrid_api_key" maxlength="37" placeholder="XXXXXXXX - XXXXXXXX - XXXXXXXX - XXXXXXXX" autocomplete="off" />
			<button id="submit_api_key" class="button button-primary">
				<?php esc_html_e( 'Submit', 'boldgrid-inspirations' ); ?>
			</button>
			<span>
				<div id="boldgrid-api-loading" class="boldgrid-wp-spin"></div>
			</span>
		</form>
		<br />
		<?php
// Display either the Envato message or the default signup message.
if ( $enableClaimMessage ) {
	printf(
		esc_html__(
			'Thank you for your Envato Market purchase.%sPlease visit %sBoldGrid Central%s to link your accounts and claim your Premium Connect Key.',
			'boldgrid-inspirations'
		),
		'<br />',
		'<a target="_blank" href="https://www.boldgrid.com/central/code/envato">',
		'</a>'
	);
} else {
		?>
		<a href="#" class="boldgridApiKeyLink">
		<?php
	esc_html_e( 'Don\'t have a Connect Key yet or lost your Key?', 'boldgrid-inspirations' );
		?>
		</a>
		<?php
}
		?>
	</div>
	<div class="new-api-key hidden">
		<h2 class="dashicons-before dashicons-admin-network">
			<?php esc_html_e( 'Request a BoldGrid Connect Key', 'boldgrid-inspirations' ); ?>
		</h2>
		<a href="#" class="enterKeyLink">
			<?php esc_html_e( 'Have a Connect Key to enter?', 'boldgrid-inspirations' ); ?>
		</a>
		<br />
		<br />
		<div class="key-request-content">
			<p id="requestKeyMessage">
				<?php printf(
					esc_html__(
						'There are two types of BoldGrid Connect Keys, a free key or an Official Host Premium Connect Key.
						%sA Premium Connect Key is highly recommended and may already come with your hosting account.%s
						If you do not have a Premium Connect Key, then you may request a free key below.
						Please visit %sour site%s for full details.%s
						If you have lost your key, you can have it resent by entering your information below.',
						'boldgrid-inspirations'
					),
					'<b>',
					'</b>',
					"<a href='https://www.boldgrid.com/get-it-now/' target='_blank'>",
					'</a>',
					'<br /><br />' );
				?>
				<br />
			</p>
			<p class="error-alerts"></p>
			<form id="requestKeyForm">
				<label>
					<?php esc_html_e( 'First Name', 'boldgrid-inspirations' ); ?>:
				</label>
				<input type="text" id="firstName" maxlength="50" placeholder="<?php esc_html_e( 'First Name', 'boldgrid-inspirations' ); ?>" value="<?php echo $first_name; ?>" />
				<label>
					<?php esc_html_e( 'Last Name', 'boldgrid-inspirations' ); ?>:
				</label>
				<input type="text" id="lastName" maxlength="50" placeholder="<?php esc_html_e( 'Last Name', 'boldgrid-inspirations' ); ?>" value="<?php echo $last_name; ?>" />
				<label>
					<?php esc_html_e( 'E-mail', 'boldgrid-inspirations' ); ?>:
				</label>
				<input type="text" id="emailAddr" maxlength="50" placeholder="your@name.com" value="<?php echo $email; ?>" />
				<br />
				<input type="hidden" id="siteUrl" value="<?php echo get_admin_url(); ?>" />
				<br />
				<button id="requestKey" class="button button-primary">
					<?php esc_html_e( 'Submit', 'boldgrid-inspirations' ); ?>
				</button>
				<span class="spinner"></span>
				<input type="hidden" id="generate-api-key" value="<?php echo $api ?>" />
			</form>
		</div>
	</div>
</div>
