<?php
/**
 * File: Connect.php
 *
 * @package    Boldgrid\Library
 * @subpackage Library\Views
 * @version    2.4.0
 * @author     BoldGrid <support@boldgrid.com>
 */

// Build markup container.
$sections = array(
	'sections' => array(
		array(
			'id'      => 'section_connect_key',
			'title'   => __( 'BoldGrid Connect Key', 'boldgrid-connect' ),
			'content' => include __DIR__ . '/Connect/ConnectKey.php',
		),
		array(
			'id'      => 'section_auto_updates',
			'title'   => __( 'Auto-Updates', 'boldgrid-connect' ),
			'content' => include __DIR__ . '/Connect/AutoUpdates.php',
		),
		array(
			'id'      => 'section_update_channels',
			'title'   => __( 'Update Channels', 'boldgrid-connect' ),
			'content' => include __DIR__ . '/Connect/UpdateChannels.php',
		),
	),
	'post_col_right' => '
		<p class="submit">
			<input name="submit" id="submit" class="button button-primary" value="' .
		esc_attr( 'Save Changes', 'boldgrid-connect' ) . '" type="submit">
			<span class="spinner"></span>
		</p>
		<div id="settings-notice" class="notice notice-success inline"></div>
	',
);

/**
 * Render sections into markup.
 *
 * @since 2.5.0
 *
 * @param array $sections
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName
 */
$container = apply_filters( 'Boldgrid\Library\Ui\render_col_container', $sections );

if ( is_array( $container ) ) {
	$container = $this->core->lang['icon_warning'] . ' ' . __( 'Unable to display settings page. Unknown BoldGrid Library error.', 'boldgrid-connect' );
}

// Enqueue styles and scripts (registered in "\Boldgrid\Library\Ui::enqueue()").
wp_enqueue_style( 'bglib-ui-css' );
wp_enqueue_script( 'bglib-ui-js' );
wp_enqueue_script( 'bglib-sticky' );

// Display page.
?>
<div class="wrap">
	<h1>BoldGrid Connect</h1>
<?php
wp_nonce_field( 'boldgrid_library_connect_settings_save' );

echo $container; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
?>
</div>
