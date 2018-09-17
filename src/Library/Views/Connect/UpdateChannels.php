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
$pluginReleaseChannel = \Boldgrid\Library\Util\Option::get( 'release_channel' );
$themeReleaseChannel  = \Boldgrid\Library\Util\Option::get( 'theme_release_channel' );

// Validate release channel settings.
$channels = array(
	'stable',
	'edge',
	'candidate',
);

if ( ! in_array( $pluginReleaseChannel, $channels, true ) ) {
	$pluginReleaseChannel = 'stable';
}

if ( ! in_array( $themeReleaseChannel, $channels, true ) ) {
	$themeReleaseChannel = 'stable';
}

$return = '
<div class="bg-box">
	<div class="bg-box-top">
		' . esc_html__( 'Update Channels', 'boldgrid-connect' ) . '
	</div>
	<div class="bg-box-bottom">

<div class="card auto-update-management div-table">
	<div class="auto-upate-settings div-table-body">
		<div class="div-table-row">
			<div class="div-tableCell"><h2>Plugins</h2></div>
			<div class="div-tableCell">
				<div class="div-table"><div class="div-table-body">
					<div class="div-table-row plugin-update-channel">
						<div class="div-tableCell">
							<input type="radio" name="plugin_release_channel" value="stable"';

if ( 'stable' === $pluginReleaseChannel ) {
	$return .= ' checked="checked"';
}

$return .= '> Stable
						</div>
						<div class="div-tableCell">
							<input type="radio" name="plugin_release_channel" value="edge"';

							if ( 'edge' === $pluginReleaseChannel ) {
								$return .= ' checked="checked"';
							}

$return .='> Edge
						</div>
';
							
if ( 'candidate' === $pluginReleaseChannel || ! empty( $_GET['channels'] ) ) {
	$return .='
						<div class="div-tableCell">
							<input type="radio" name="plugin_release_channel" value="candidate"';
							
	if ( 'candidate' === $pluginReleaseChannel ) {
		$return .= ' checked="checked"';
	}

	$return .= '> Candidate
						</div>
';
}

$return .= '
					</div>
				</div></div>
			</div>
		</div>
	</div>

	<div class="auto-upate-settings div-table-body">
		<div class="div-table-row">
			<div class="div-tableCell"><h2>Themes</h2></div>
			<div class="div-tableCell">
				<div class="div-table"><div class="div-table-body">
					<div class="div-table-row theme-update-channel">
						<div class="div-tableCell">
							<input type="radio" name="theme_release_channel" value="stable"';

if ( 'stable' === $themeReleaseChannel ) {
	$return .= ' checked="checked"';
}

$return .= '> Stable
						</div>
						<div class="div-tableCell">
							<input type="radio" name="theme_release_channel" value="edge"';

if ( 'edge' === $themeReleaseChannel ) {
	$return .= ' checked="checked"';
}

$return .= '> Edge
						</div>
';

if ( 'candidate' === $themeReleaseChannel || ! empty( $_GET['channels'] ) ) {
	$return .= '
						<div class="div-tableCell">
							<input type="radio" name="theme_release_channel" value="candidate"';

	if ( 'candidate' === $themeReleaseChannel ) {
		$return .= ' checked="checked"';
	}
	
	$return .= '> Candidate
						</div>
';
}

$return .= '
					</div>
				</div></div>
			</div>
		</div>
	</div>
</div>

	</div>
</div>
';

return $return;
