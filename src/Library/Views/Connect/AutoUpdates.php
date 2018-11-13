<?php
/**
 * File: Connect.php
 *
 * @package    Boldgrid\Library
 * @subpackage Library\Views
 * @version    2.5.0
 * @author     BoldGrid <support@boldgrid.com>
 */

// Get BoldGrid settings.
\Boldgrid\Library\Util\Option::init();
$autoupdateSettings = \Boldgrid\Library\Util\Option::get( 'autoupdate' );
$pluginsDefault     = ! empty( $autoupdateSettings['plugins']['default'] );
$themesDefault      = ! empty( $autoupdateSettings['themes']['default'] );

// Get BoldGrid Backup settings.
$boldgridBackupSettings = get_site_option( 'boldgrid_backup_settings', array() );

// Get deprecated settings.
$pluginAutoupdate = (bool) \Boldgrid\Library\Util\Option::get( 'plugin_autoupdate' );
$themeAutoupdate  = (bool) \Boldgrid\Library\Util\Option::get( 'theme_autoupdate' );

// Get WordPress Core auto-update setting.
$wpcoreAutoupdates = ! empty( $autoupdateSettings['wpcore'] ) ?
	$autoupdateSettings['wpcore'] : array();
$wpcoreMajor       = ! empty( $wpcoreAutoupdates['major'] );
$wpcoreMinor       = ! isset( $wpcoreAutoupdates['minor'] ) || $wpcoreAutoupdates['minor'];
$wpcoreDev         = ! empty( $wpcoreAutoupdates['dev'] );
$wpcoreTranslation = ! empty( $wpcoreAutoupdates['translation'] );
$wpcoreAll         = ! empty( $wpcoreAutoupdates['all'] ) ||
	( $wpcoreMajor && $wpcoreMinor && $wpcoreDev && $wpcoreTranslation );
$translations      = array(
	'active'   => esc_attr__( 'Active', 'boldgrid-connect' ),
	'inactive' => esc_attr__( 'Inactive', 'boldgrid-connect' ),
	'parent'   => esc_attr__( 'Parent', 'boldgrid-connect' ),
);
$return            = '';
$helpMarkup        = sprintf(
	// translators: 1: HTML anchor open tag, 2: HTML anchor close tag.
	esc_html__(
		'Automatically perform WordPress core, plugin, and theme updates. This feature utilizes %1$sWordPress filters%2$s, which enables automatic updates as they become available.',
		'boldgrid-backup'
	),
	'<a target="_blank" href="https://codex.wordpress.org/Configuring_Automatic_Background_Updates">',
	'</a>'
);

if ( empty( $boldgridBackupSettings['auto_backup'] ) ) {
	$bbsLinkOpen  = '';
	$bbsLinkClose = '';

	if ( empty( $_GET['page'] ) || 'boldgrid-backup-settings' !== $_GET['page'] ) {
		$bbsLinkOpen = '<a href="' . admin_url( 'admin.php?page=boldgrid-backup-settings&section=section_auto_rollback' ) . '">';
		$bbsLinkClose = '</a>';
	}

	$return .= '
		<div><p>' .
		sprintf(
			// translators: 1: HTML anchor open tag, 2: HTML anchor close tag, 3: HTML em open tag, 4: HTML em close tag..
			esc_html__(
				'You have %3$sAuto-Backup%4$s disabled in the %1$sBoldGrid Backup Settings%2$s.  Please consider enabling the setting.',
				'boldgrid-backup'
			),
			$bbsLinkOpen,
			$bbsLinkClose,
			'<em>',
			'</em>'
		) .
		'</p></div>' . PHP_EOL;
}

$return .= '
<div class="bg-box">
	<div class="bg-box-top">
		' . esc_html__( 'WordPress Core', 'boldgrid-connect' ) . '
		<span class="dashicons dashicons-editor-help" data-id="core-autoupdate"></span>
	</div>
	<div class="bg-box-bottom">
		<p class="help" data-id="core-autoupdate">' . $helpMarkup . '</p>

<div class="auto-update-management div-table">
	<div class="auto-update-settings div-table-body">
		<div class="div-table-row">
			<div class="div-tableCell">
				<div class="div-table"><div class="div-table-body">
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'All Update Types', 'boldgrid-connect' ) . '</div>
						<div class="toggle toggle-light toggle-group wpcore-toggle"
							data-wp-core="all"
							data-toggle-on="' . ( $wpcoreAll ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[wpcore][all]"
							value="' . ( $wpcoreAll ? 1 : 0 ) . '" />
					</div>
					<div class="div-table-row"><br /></div>
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'Major Updates', 'boldgrid-connect' ) . '</div>
						<div class="toggle toggle-light wpcore-toggle"
							data-wp-core="major"
							data-toggle-on="' . ( $wpcoreMajor ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[wpcore][major]"
							value="' . ( $wpcoreMajor ? 1 : 0 ) . '" />
					</div>
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'Minor Updates', 'boldgrid-connect' ) . '</div>
						<div class="toggle toggle-light wpcore-toggle"
							data-wp-core="minor"
							data-toggle-on="' . ( $wpcoreMinor ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[wpcore][minor]"
							value="' . ( $wpcoreMinor ? 1 : 0 ) . '" />
					</div>
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'Development Updates', 'boldgrid-connect' ) . '</div>
						<div class="toggle toggle-light wpcore-toggle"
							data-wp-core="dev"
							data-toggle-on="' . ( $wpcoreDev ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[wpcore][dev]"
							value="' . ( $wpcoreDev ? 1 : 0 ) . '" />
					</div>
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'Translation Updates', 'boldgrid-connect' ) . '</div>
						<div class="toggle toggle-light wpcore-toggle"
							data-wp-core="translation"
							data-toggle-on="' . ( $wpcoreTranslation ? 'true' : 'false' ) . '">
						</div>
						<input type="hidden" name="autoupdate[wpcore][translation]"
							value="' . ( $wpcoreTranslation ? 1 : 0 ) . '" />
					</div>
				</div></div>
			</div>
		</div>
	</div>
</div>

	</div>
</div>

<div class="bg-box">
	<div class="bg-box-top">
		' . esc_html__( 'Plugins', 'boldgrid-connect' ) . '
		<span class="dashicons dashicons-editor-help" data-id="plugins-autoupdate"></span>
	</div>
	<div class="bg-box-bottom">
		<p class="help" data-id="plugins-autoupdate">' . $helpMarkup . '</p>

<div class="auto-update-management div-table">
	<div class="auto-update-settings div-table-body">
		<div class="div-table-row">
			<div class="div-tableCell">

				<div class="div-table"><div class="div-table-body">
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'Default for New Plugins', 'boldgrid-connect' ) . '</div>
						<div class="div-tableCell">
							<div class="toggle toggle-light" id="toggle-default-plugins"
								data-toggle-on="' . ( $pluginsDefault ? 'true' : 'false' ) . '">
							</div>
						</div>
						<input type="hidden" name="autoupdate[plugins][default]"
							value="' . ( $pluginsDefault ? 1 : 0 ) . '" />
					</div>
					<div class="div-table-row"><br /></div>
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'All Plugins', 'boldgrid-connect' ) . '</div>
						<div class="div-tableCell">
							<div class="toggle toggle-light toggle-group" id="toggle-plugins"></div>
						</div>
					</div>
';

$plugins = get_plugins();

// Split into groups: active/inactive.
$pluginsActive   = array();
$pluginsInactive = array();

foreach ( $plugins as $slug => $pluginData ) {
	if ( is_plugin_active( $slug ) ) {
		$pluginsActive[ $slug ] = $pluginData;
	} else {
		$pluginsInactive[ $slug ] = $pluginData;
	}
}

$statuses = array(
	'Active',
	'Inactive',
);

foreach ( $statuses as $status ) {
	$statusLower = strtolower( $status );

	$return .= '<div class="div-table-contents">
	<div class="div-table-row bglib-collapsible-control' . ( 'Inactive' !== $status ?
		' bglib-collapsible-open' : '' ) . '">
		<div class="div-tableCell"><h3>' . $translations[ $statusLower ] . '</h3></div>
		<div class="div-tableCell">
			<span class="dashicons dashicons-arrow-down-alt2 bglib-collapsible-' . $statusLower . '"></span>
		</div>
	</div>
';

	foreach ( ${ 'plugins' . $status } as $slug => $pluginData ) {
		// Enable if global setting is on, individual settings is on, or not set and default is on.
		$toggle = $pluginAutoupdate || ! empty( $autoupdateSettings['plugins'][ $slug ] ) ||
			( ! isset( $autoupdateSettings['plugins'][ $slug ] ) && $pluginsDefault );

		$return .= '
			<div class="div-table-row plugin-update-setting bglib-collapsible">
				<div class="div-tableCell">' . $pluginData['Name'] . '</div>
				<div class="div-tableCell">
					<div class="toggle toggle-light plugin-toggle"
						data-plugin="' . $slug . '"
						data-toggle-on="' . ( $toggle ? 'true' : 'false' ) . '">
					</div>
				</div>
				<input type="hidden" name="autoupdate[plugins][' . $slug . ']"
					value="' . ( $toggle ? 1 : 0 ) . '" />
			</div>
		';
	}
	$return .= '</div>
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

<div class="bg-box">
	<div class="bg-box-top">
		' . esc_html__( 'Themes', 'boldgrid-connect' ) . '
		<span class="dashicons dashicons-editor-help" data-id="themes-autoupdate"></span>
	</div>
	<div class="bg-box-bottom">
		<p class="help" data-id="themes-autoupdate">' . $helpMarkup . '</p>

<div class="auto-update-management div-table">
	<div class="auto-update-settings div-table-body">
		<div class="div-table-row">
			<div class="div-tableCell">

				<div class="div-table"><div class="div-table-body">
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'Default for New Themes', 'boldgrid-connect' ) . '</div>
						<div class="div-tableCell">
							<div class="toggle toggle-light" id="toggle-default-themes"
								data-toggle-on="' . ( $themesDefault ? 'true' : 'false' ) . '">
							</div>
						</div>
						<input type="hidden" name="autoupdate[themes][default]"
							value="' . ( 'true' === $themesDefault ? 1 : 0 ) . '" />
					</div>
					<div class="div-table-row"><br /></div>
					<div class="div-table-row">
						<div class="div-tableCell">' .
	esc_html__( 'All Themes', 'boldgrid-connect' ) . '</div>
						<div class="div-tableCell">
							<div class="toggle toggle-light toggle-group" id="toggle-themes"></div>
						</div>
					</div>
';

$activeStylesheet = get_option( 'stylesheet' );
$activeTemplate   = get_option( 'template' );
$themes           = wp_get_themes();

// Split into groups: active/inactive.
$themesActive   = array();
$themesInactive = array();

foreach ( $themes as $stylesheet => $theme ) {
	$isActive = $stylesheet === $activeStylesheet;
	$isParent = ( $activeStylesheet !== $activeTemplate && $stylesheet === $activeTemplate );

	if ( $isActive || $isParent ) {
		$themesActive[ $stylesheet ] = $theme;
	} else {
		$themesInactive[ $stylesheet ] = $theme;
	}
}

$statuses = array(
	'Active',
	'Inactive',
);

foreach ( $statuses as $status ) {
	$statusLower = strtolower( $status );

	$return .= '<div class="div-table-contents">
	<div class="div-table-row bglib-collapsible-control' . ( 'Inactive' !== $status ?
		' bglib-collapsible-open' : '' ) . '">
		<div class="div-tableCell"><h3>' . $translations[ $statusLower ] . '</h3></div>
		<div class="div-tableCell">
			<span class="dashicons dashicons-arrow-down-alt2 bglib-collapsible-' . $statusLower .'"></span>
		</div>
	</div>
';

	foreach ( ${ 'themes' . $status } as $stylesheet => $theme ) {
		$isParent = ( $activeStylesheet !== $activeTemplate && $stylesheet === $activeTemplate );

		// Enable if global setting is on, individual settings is on, or not set and default is on.
		$toggle = $themeAutoupdate || ! empty( $autoupdateSettings['themes'][ $stylesheet ] ) ||
			( ! isset( $autoupdateSettings['themes'][ $stylesheet ] ) && $themesDefault );

		$return .= '
			<div class="div-table-row theme-update-setting bglib-collapsible">
				<div class="div-tableCell">' . $theme->get( 'Name' ) .
				( $isParent ? ' (' . $translations['parent'] . ')' : '' ) . '</div>
				<div class="div-tableCell">
					<div class="toggle toggle-light theme-toggle"
						data-stylesheet="' . $stylesheet . '"
						data-toggle-on="' . ( $toggle ? 'true' : 'false' ) . '">
					</div>
				</div>
				<input type="hidden" name="autoupdate[themes][' . $stylesheet . ']"
					value="' . ( $toggle ? 1 : 0 ) . '" />
			</div>
		';
	}

	$return .= '</div>
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
