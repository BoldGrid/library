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
$settings = \Boldgrid\Library\Util\Option::get( 'autoupdate' );

// Get deprecated settings.
$pluginAutoupdate = (bool) \Boldgrid\Library\Util\Option::get( 'plugin_autoupdate' );
$themeAutoupdate  = (bool) \Boldgrid\Library\Util\Option::get( 'theme_autoupdate' );

$return = '
<div class="bg-box">
	<div class="bg-box-top">
		' . esc_html__( 'Auto-Updates', 'boldgrid-connect' ) . '
	</div>
	<div class="bg-box-bottom">

<div class="card auto-update-management div-table">
	<div class="auto-upate-settings div-table-body">
		<div class="div-table-row">
			<div class="div-tableCell"><h2>Plugins</h2></div>
			<div class="div-tableCell">
				<div class="div-table"><div class="div-table-body">
					<div class="div-table-row">
						<div class="div-tableCell">All Plugins</div>
						<div class="toggle toggle-modern toggle-group" id="toggle-plugins"></div>
					</div>
					<div class="div-table-row"><br /></div>
';

$plugins = get_plugins();

foreach ( $plugins as $slug => $plugin_data ) {
	$toggle = $pluginAutoupdate || ! empty( $settings['plugins'][ $slug ] ) ?
		'true' : 'false';
	
	$return .= '
					<div class="div-table-row plugin-update-setting">
						<div class="div-tableCell">' . $plugin_data['Name'] . '</div>
						<div class="toggle toggle-modern plugin-toggle"
							 data-plugin="' . $slug .'"
							 data-toggle-on="' . $toggle . '"></div>
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
						<div class="div-tableCell">All Themes</div>
						<div class="toggle toggle-modern toggle-group" id="toggle-themes"></div>
					</div>
					<div class="div-table-row"><br /></div>
';

$themes = wp_get_themes();

foreach ( $themes as $stylesheet => $theme_data ) {
	$toggle = $themeAutoupdate || ! empty( $settings['themes'][ $stylesheet ] ) ?
		'true' : 'false';
	
	$return .= '
					<div class="div-table-row theme-update-setting">
						<div class="div-tableCell">' . $theme_data['Name'] . '</div>
						<div class="toggle toggle-modern theme-toggle"
							data-stylesheet="' . $stylesheet . '"
							data-toggle-on="' . $toggle . '"></div>
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
