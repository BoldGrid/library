<?php
/**
 * File: Connect.php
 *
 * @package    Boldgrid\Library
 * @subpackage Library\Views
 * @version    2.4.0
 * @author     BoldGrid <support@boldgrid.com>
 */

$settings = \Boldgrid\Library\Util\Option::get( 'connect_settings' );

wp_nonce_field( 'boldgrid_library_connect_settings_save' );

?>
<div class="wrap">
	<h1>BoldGrid Connect</h1>
	<div class="card connect-key-management">
		<div class="connect-key-prompt"></div>
	</div>
	<div class="card auto-update-management div-table">
		<div class="auto-upate-settings div-table-body">
			<div class="div-table-row">
				<div class="div-tableCell"><h2>Plugin Auto-Updates</h2></div>
				<div class="div-tableCell">
					<div class="div-table"><div class="div-table-body">
						<div class="div-table-row">
							<div class="div-tableCell">Toggle All Plugins</div>
							<div class="toggle toggle-modern toggle-group" id="toggle-plugins"></div>
						</div>
						<div class="div-table-row"><br /></div>
					<?php
					$plugins = get_plugins();
					foreach ( $plugins as $slug => $plugin_data ) {
						$toggle = ! empty( $settings['autoupdate']['plugins'][ $slug ] ) ?
							'true' : 'false';
						?>
						<div class="div-table-row plugin-update-setting">
							<div class="div-tableCell"><?php echo $plugin_data['Name']; ?></div>
							<div class="toggle toggle-modern plugin-toggle"
								 data-plugin="<?php echo $slug; ?>"
								 data-toggle-on="<?php echo $toggle; ?>"></div>
						</div>
						<?php
					}
					?>
					</div></div>
				</div>
			</div>
		</div>
	</div>
	<div class="card auto-update-management div-table">
		<div class="auto-upate-settings div-table-body">
			<div class="div-table-row">
				<div class="div-tableCell"><h2>Theme Auto-Updates</h2></div>
				<div class="div-tableCell">
					<div class="div-table"><div class="div-table-body">
						<div class="div-table-row">
							<div class="div-tableCell">Toggle All Themes</div>
							<div class="toggle toggle-modern toggle-group" id="toggle-themes"></div>
						</div>
						<div class="div-table-row"><br /></div>
					<?php
					$themes = wp_get_themes();
					foreach ( $themes as $stylesheet => $theme_data ) {
						$toggle = ! empty( $settings['autoupdate']['themes'][ $stylesheet ] ) ?
							'true' : 'false';
						?>
						<div class="div-table-row theme-update-setting">
							<div class="div-tableCell">
								<?php echo $theme_data['Name']; ?>
							</div>
							<div class="toggle toggle-modern theme-toggle"
								data-stylesheet="<?php echo $stylesheet; ?>"
								data-toggle-on="<?php echo $toggle; ?>"></div>
						</div>
						<?php
					}
					?>
					</div></div>
				</div>
			</div>
		</div>
	</div>
</div>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit">
	<span class='spinner'></span>
</p>

<div id="settings-notice" class="notice notice-success inline"></div>
