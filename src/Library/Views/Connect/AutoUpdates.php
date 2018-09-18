<?php
/**
 * File: Connect.php
 *
 * @package    Boldgrid\Library
 * @subpackage Library\Views
 * @version    2.5.0
 * @author     BoldGrid <support@boldgrid.com>
 */

// Get settings.
\Boldgrid\Library\Util\Option::init();
$autoupdateSettings = \Boldgrid\Library\Util\Option::get( 'autoupdate' );
$pluginsDefault     = ! empty( $autoupdateSettings['plugins']['default'] );
$themesDefault      = ! empty( $autoupdateSettings['themes']['default'] );

// Get deprecated settings.
$pluginAutoupdate = (bool) \Boldgrid\Library\Util\Option::get( 'plugin_autoupdate' );
$themeAutoupdate  = (bool) \Boldgrid\Library\Util\Option::get( 'theme_autoupdate' );

$return = '
<div class="bg-box">
	<div class="bg-box-top">
		' . esc_html__( 'Auto-Updates', 'boldgrid-connect' ) . '
		<span class="dashicons dashicons-editor-help" data-id="plugin-autoupdate"></span>
	</div>
	<div class="bg-box-bottom">

		<p class="help" data-id="plugin-autoupdate">' .
			sprintf(
				// translators: 1: HTML anchor open tag, 2: HTML anchor close tag.
				esc_html__(
					'Automatically perform plugin and theme updates. This feature utilizes the %1$sauto_update_plugin%2$s and %1$sauto_update_theme%2$s WordPress filters, which enables automatic updates as they become available.',
					'boldgrid-backup'
				),
				'<a target="_blank" href="https://codex.wordpress.org/Configuring_Automatic_Background_Updates#Plugin_.26_Theme_Updates_via_Filter">',
				'</a>'
			) .
		'</p>

<div class="card auto-update-management div-table">
	<div class="auto-upate-settings div-table-body">
		<div class="div-table-row">
			<div class="div-tableCell"><h2>Plugins</h2></div>
			<div class="div-tableCell">
				<div class="div-table"><div class="div-table-body">
					<div class="div-table-row">
						<div class="div-tableCell">Default for New Plugins</div>
						<div class="toggle toggle-modern" id="toggle-default-plugins"
							data-toggle-on="' . ( $pluginsDefault ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[plugins][default]"
							value="' . ( $pluginsDefault ? 1 : 0 ) . '" />
					</div>
					<div class="div-table-row"><br /></div>
					<div class="div-table-row">
						<div class="div-tableCell">All Plugins</div>
						<div class="toggle toggle-modern toggle-group" id="toggle-plugins"></div>
					</div>
					<div class="div-table-row"><br /></div>
';

$plugins = get_plugins();

foreach ( $plugins as $slug => $plugin_data ) {
	// Enable if global setting is on, individual settings is on, or not set and default is on.
	$toggle = $pluginAutoupdate || ! empty( $autoupdateSettings['plugins'][ $slug ] ) ||
		( ! isset( $autoupdateSettings['plugins'][ $slug ] ) && $pluginsDefault );

	$return .= '
					<div class="div-table-row plugin-update-setting">
						<div class="div-tableCell">' . $plugin_data['Name'] . '</div>
						<div class="toggle toggle-modern plugin-toggle"
							data-plugin="' . $slug . '"
							data-toggle-on="' . ( $toggle ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[plugins][' . $slug . ']"
							value="' . ( $toggle ? 1 : 0 ) . '" />
					</div>
';
}

$return .= '
				</div></div>
			</div>
		</div>
	</div>
</div>
<div class="card auto-update-management div-table">
	<div class="auto-upate-settings div-table-body">
		<div class="div-table-row">
			<div class="div-tableCell"><h2>Themes</h2></div>
			<div class="div-tableCell">
				<div class="div-table"><div class="div-table-body">
					<div class="div-table-row">
						<div class="div-tableCell">Default for New Themes</div>
						<div class="toggle toggle-modern" id="toggle-default-themes"
							data-toggle-on="' . ( $themesDefault ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[themes][default]"
							value="' . ( 'true' === $themesDefault ? 1 : 0 ) . '" />
					</div>
					<div class="div-table-row"><br /></div>
					<div class="div-table-row">
						<div class="div-tableCell">All Themes</div>
						<div class="toggle toggle-modern toggle-group" id="toggle-themes"></div>
					</div>
					<div class="div-table-row"><br /></div>
';

$themes = wp_get_themes();

foreach ( $themes as $stylesheet => $theme_data ) {
	// Enable if global setting is on, individual settings is on, or not set and default is on.
	$toggle = $themeAutoupdate || ! empty( $autoupdateSettings['themes'][ $stylesheet ] ) ||
		( ! isset( $autoupdateSettings['themes'][ $stylesheet ] ) && $themesDefault );

	$return .= '
					<div class="div-table-row theme-update-setting">
						<div class="div-tableCell">' . $theme_data['Name'] . '</div>
						<div class="toggle toggle-modern theme-toggle"
							data-stylesheet="' . $stylesheet . '"
							data-toggle-on="' . ( $toggle ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[themes][' . $stylesheet . ']"
							value="' . ( $toggle ? 1 : 0 ) . '" />
					</div>
';
}

$return .= '
				</div></div>
			</div>
		</div>
	</div>
</div>

	</div>
</div>
';

return $return;
