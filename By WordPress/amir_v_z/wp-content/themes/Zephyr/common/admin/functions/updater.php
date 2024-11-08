<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Updater of the plugins from UpSolution Help Portal
 */

if ( ! function_exists( 'us_api_addons' ) ) {
	add_filter( 'us_config_addons', 'us_api_addons' );
	/**
	 * @param array $plugins The plugins
	 * @param bool $force_request The force request
	 * @return array
	 */
	function us_api_addons( $plugins, $force_request = FALSE ) {
		$addons_version = get_option( 'us_addons_version' );
		$license_activated = get_option( 'us_license_activated', 0 );
		$license_dev_activated = get_option( 'us_license_dev_activated', 0 );
		$license_secret = get_option( 'us_license_secret' );

		if (
			$license_secret != ''
			AND (
				$license_activated
				OR $license_dev_activated
			)
		) {
			// Get transient data
			$transient = 'us_update_addons_data_' . US_THEMENAME;
			if ( ! empty( $addons_version )
				AND $addons_version == US_THEMEVERSION
				AND ( $data = get_transient( $transient ) ) !== FALSE
			) {
				$update_addons_data = $data;
			}

			if ( empty( $update_addons_data ) OR $force_request ) {
				/**
				 * @var array HTTP GET variables
				 */
				$get_variables = array(
					'secret' => urlencode( $license_secret ),
					'domain' => parse_url( site_url(), PHP_URL_HOST ),
					'current_version' => urlencode( US_THEMEVERSION ),
				);

				// Note: We do not need to check this often, since after theme update addons data is reset
				$us_api_response = us_api( '/check_addons_update/:us_themename', $get_variables );
				if ( ! empty( $us_api_response ) ) {
					set_transient( $transient, $us_api_response, 6 * HOUR_IN_SECONDS );
					$update_addons_data = $us_api_response;
					update_option( 'us_addons_version', US_THEMEVERSION );
				}
			}

			if ( ! empty( $update_addons_data->data ) ) {
				foreach ( $plugins as $i => $plugin ) {
					$slug = $plugin['slug'];
					if ( isset( $update_addons_data->data->$slug ) ) {
						// Do not update version and source from Help Portal if config has higher version
						if ( isset( $plugins[ $i ]['version'] )
							AND version_compare( $update_addons_data->data->$slug->new_version, $plugins[ $i ]['version'], '<' ) ) {
							continue;
						}
						$plugins[ $i ]['version'] = $update_addons_data->data->$slug->new_version;
						$plugins[ $i ]['source'] = $update_addons_data->data->$slug->package;
					}
				}
			}
		}

		return $plugins;
	}
}

$addons = us_config( 'addons', array() );

foreach ( $addons as $i => $addon ) {
	if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
		unset( $addons[$i] );
	}
}

if ( empty( $addons ) ) {
	return;
}

// Transient hook for automatic updates of bundled plugins
add_action( 'site_transient_update_plugins', 'us_addons_transient_update' );
function us_addons_transient_update( $transient ) {

	// Exit if no response is set
	if ( ! isset( $transient->response ) ) {
		return $transient;
	}

	$installed_plugins = get_plugins();

	$addons = us_config( 'addons', array() );

	foreach ( $addons as $i => $addon ) {
		if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
			unset( $addons[$i] );
		}
	}

	foreach ( $addons as $addon ) {
		$folder = ( ! empty( $addon['folder'] ) ) ? $addon['folder'] : $addon['slug'];
		$file = $addon['slug'];
		if ( strpos( $addon['slug'], '/' ) !== FALSE ) {
			$folder = substr(
				$addon['slug'],
				0,
				strpos( $addon['slug'], '/' )
			);
			$file = substr(
				$addon['slug'],
				strpos( $addon['slug'], '/' ) + 1
			);
		}
		$plugin_basename = sprintf( '%s/%s.php', $folder, $file );

		if ( ! isset( $installed_plugins[$plugin_basename] ) ) {
			continue;
		}

		if ( version_compare( $installed_plugins[$plugin_basename]['Version'], $addon['version'], '<' ) ) {
			$transient->response[$plugin_basename] = new StdClass();
			$transient->response[$plugin_basename]->plugin = $plugin_basename;
			$transient->response[$plugin_basename]->url = $addon['changelog_url'];
			$transient->response[$plugin_basename]->slug = $addon['slug'];
			$transient->response[$plugin_basename]->package = $addon['source'];
			$transient->response[$plugin_basename]->new_version = $addon['version'];
			$transient->response[$plugin_basename]->id = '0';
		}
	}

	return $transient;
}

// Seen when user clicks "view details" on the plugin listing page
add_action( 'install_plugins_pre_plugin-information', 'us_addons_update_popup' );
function us_addons_update_popup() {

	if ( ! isset( $_GET['plugin'] ) ) {
		return;
	}

	$plugin_slug = sanitize_file_name( $_GET['plugin'] );

	$addons = us_config( 'addons', array() );

	foreach ( $addons as $i => $addon ) {
		if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
			unset( $addons[$i] );
		}
	}

	foreach ( $addons as $addon ) {
		if ( $addon['slug'] == $plugin_slug ) {
			$changelog_url = $addon['changelog_url'];

			echo '<html><body style="height: 90%; background: #fcfcfc"><p>See the <a href="' . $changelog_url . '" ' . 'target="_blank" rel="noopener">' . $changelog_url . '</a> for the detailed changelog</p></body></html>';

			exit;
		}
	}
}

add_action( 'admin_head', 'us_remove_bsf_update_counter' );
function us_remove_bsf_update_counter() {
	remove_action( 'admin_head', 'bsf_update_counter', 999 );
}
