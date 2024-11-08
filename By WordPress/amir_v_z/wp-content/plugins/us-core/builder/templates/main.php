<?php defined( 'ABSPATH' ) or die( 'This script cannot be accessed directly.' );
/**
 * @var array $fieldsets
 * @var array $usb_setting
 * @var array $elms_categories
 * @var string $ajaxurl
 * @var string $body_class
 * @var string $edit_page_link
 * @var string $page_link
 */

// Checking required variables
$breakpoints = us_arr_path( $usb_settings, 'breakpoints', array() );
$elms_categories = isset( $elms_categories ) ? $elms_categories : array();
$fieldsets = isset( $fieldsets ) ? $fieldsets : array();
$section_templates_included = us_get_option( 'section_templates', /* default */1 );
$post_id = (int) USBuilder::get_post_id();
$post_type = isset( $post_type ) ? $post_type : '';
$usb_settings = isset( $usb_settings ) ? $usb_settings : array();

?>
<!DOCTYPE HTML>
<html dir="<?php echo( is_rtl() ? 'rtl' : 'ltr' ) ?>" <?php language_attributes( 'html' ) ?>>
<head>
	<title><?php echo $title ?></title>
	<meta charset="<?php bloginfo( 'charset' ) ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php wp_print_styles() ?>
	<script type="text/javascript">
		// Link to get data via AJAX for USOF
		var ajaxurl = '<?php esc_attr_e( $ajaxurl ) ?>';
		// Text translations for USBuilder
		window.$usbdata = window.$usbdata || {}; // Single space for data
		window.$usbdata.textTranslations = <?php echo json_encode( $text_translations ) ?>;
	</script>
</head>
<body class="<?php echo $body_class ?>">
<div id="us-builder-wrapper" class="us-builder-wrapper"<?php echo us_pass_data_to_js( $usb_settings ) ?>>
	<aside id="us-builder-panel" class="us-builder-panel wp-core-ui">
		<div class="us-builder-panel-switcher ui-icon_left" title="<?php _e( 'Hide/Show panel', 'us' ) ?>">
		</div>
		<header class="us-builder-panel-header">
			<!-- Builder Menu -->
			<div class="us-builder-panel-header-menu">
				<div class="us-builder-panel-header-btn icon_menu" title="<?php esc_attr_e( us_translate( 'Menu' ) )?>">
					<span></span>
				</div>
				<div class="us-builder-panel-header-menu-list">
					<?php if ( ! in_array( $post_type, array( 'us_page_block', 'us_content_template' ) ) ) { ?>
					<a class="us-builder-panel-header-menu-item" href="<?php esc_attr_e( $page_link ) ?>" target="_blank">
						<span><?php esc_attr_e( us_translate( 'View Page' ) ) ?></span>
					</a>
					<?php } else { ?>
					<a class="us-builder-panel-header-menu-item" href="<?php echo home_url( '/' ) ?>" target="_blank">
						<span><?php esc_attr_e( us_translate( 'Visit Site' ) ) ?></span>
					</a>
					<?php } ?>
					<a class="us-builder-panel-header-menu-item" href="<?php esc_attr_e( $edit_page_link ) ?>" target="_blank">
						<span><?php esc_attr_e( __( 'Edit page in Backend', 'us' ) ) ?></span>
					</a>
					<a class="us-builder-panel-header-menu-item" href="<?php echo admin_url( 'admin.php?page=us-theme-options' ) ?>" target="_blank">
						<span><?php esc_attr_e( __( 'Go to Theme Options', 'us' ) ) ?></span>
					</a>
					<a class="us-builder-panel-header-menu-item usb_action_show_import_content" href="javascript:void(0)">
						<span><?php esc_attr_e( __( 'Paste Row/Section', 'us' ) ) ?></span>
					</a>
					<a class="us-builder-panel-header-menu-item" href="<?php echo admin_url() ?>">
						<span><?php esc_attr_e( __( 'Exit to dashboard', 'us' ) ) ?></span>
					</a>
				</div>
			</div>
			<div class="us-builder-panel-header-title"></div>
			<a href="javascript:void(0)" class="us-builder-panel-header-btn ui-icon_add usb_action_elm_add" title="<?php esc_attr_e( __( 'Add element', 'us' ) )?>"></a>
		</header>
		<div class="us-builder-panel-body">
			<!-- Add Items -->
			<div class="us-builder-panel-add-items usof-tabs">
				<?php if ( $section_templates_included ): ?>
				<div class="usof-tabs-list">
					<div class="usof-tabs-item active"><?php esc_attr_e( __( 'Elements', 'us' ) ) ?></div>
					<div class="usof-tabs-item usb-templates-loaded"><?php esc_attr_e( us_translate( 'Templates' ) ) ?></div>
				</div>
				<?php endif; ?>
				<div class="usof-tabs-sections">
					<div class="usof-tabs-section active">
						<!-- Begin Add Element List -->
						<div class="us-builder-panel-elms">
							<div class="us-builder-panel-elms-search">
								<input type="text" name="search" autocomplete="off" placeholder="<?php esc_attr_e( us_translate( 'Search' ) ) ?>">
								<i class="ui-icon_close usb_action_reset_search hidden" title="<?php esc_attr_e( __( 'Reset', 'us' ) ) ?>"></i>
							</div>
							<div class="us-builder-panel-elms-search-noresult hidden"><?php esc_attr_e( us_translate( 'No results found.' ) ) ?></div>
							<?php foreach ( $elms_categories as $category => $elms ): ?>
								<?php
								// Category title
								$title = ! empty( $category ) ? $category : us_translate( 'General', 'js_composer' );
								echo '<h2 class="us-builder-panel-elms-header">' . strip_tags( $title ) . '</h2>';

								// Category elements
								$output = '<div class="us-builder-panel-elms-list">';
								foreach ( $elms as $type => $elm ) {
									$elm_atts = array(
										'class' => 'us-builder-panel-elms-item',
										'data-title' => strip_tags( $elm['title'] ),
										'data-type' => (string) $type,
									);
									if ( ! empty( $elm['is_container'] ) ) {
										$elm_atts['data-isContainer'] = TRUE;
									}

									// Hide specific elements
									if (
										! empty( $elm['hide_on_adding_list'] )
										OR (
											! empty( $elm['show_for_post_types'] )
											AND ! in_array( $post_type, (array) $elm['show_for_post_types'] )
										)
										OR (
											! empty( $elm[ 'hide_for_post_ids' ] )
											AND in_array( $post_id, (array) $elm['hide_for_post_ids'] )
										)
									) {
										$elm_atts['class'] .= ' hidden';

									} elseif( ! empty( $elm_atts['data-title'] ) ) {
										$elm_atts['data-search-text'] = us_strtolower( $elm_atts['data-title'] );
									}
									$output .= '<div' . us_implode_atts( $elm_atts ) . '>';
									$output .= '<i class="' . $elm['icon'] . '"></i>';
									$output .= '</div>';
								}
								$output .= '</div>';
								echo $output;
								?>
							<?php endforeach; ?>
						</div>
						<!-- End Add Element List -->
					</div>
					<?php if ( $section_templates_included ): ?>
					<div class="usof-tabs-section">
						<!-- Begin Templates List -->
						<div class="usb-templates">
							<span class="usof-preloader usb-templates-preloader"></span>
							<div class="usb-templates-error"><?php echo strip_tags( us_translate( 'No results found.' ) ) ?></div>
						</div>
						<!-- End Templates List -->
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Elements Fieldsets -->
			<div class="us-builder-panel-fieldsets">
				<?php foreach ( $fieldsets as $fieldset_name => $fieldset ): ?>
					<form class="us-builder-panel-fieldset" data-name="<?php esc_attr_e( $fieldset_name ) ?>">
						<?php us_load_template(
							'usof/templates/edit_form', array(
								'type' => $fieldset_name,
								'params' => isset( $fieldset['params'] ) ? $fieldset['params'] : array(),
								'context' => 'shortcode'
							)
						) ?>
					</form>
				<?php endforeach; ?>
			</div>

			<!-- Messages Panel -->
			<div class="us-builder-panel-messages hidden"></div>

			<!-- Paste Row/Section -->
			<div class="us-builder-panel-import-content usof-container inited hidden">
				<textarea placeholder="[vc_row][vc_column] ... [/vc_column][/vc_row]"></textarea>
				<button class="usof-button usb_action_save_pasted_content disabled" disabled>
					<span><?php esc_attr_e( __( 'Append Section', 'us' ) ) ?></span>
					<span class="usof-preloader"></span>
				</button>
			</div>

			<!-- Page Custom CSS -->
			<div class="us-builder-panel-page-custom-css usof-container inited hidden">
				<div class="type_css" data-name="<?php esc_attr_e( USBuilder::KEY_CUSTOM_CSS ) ?>">
					<?php us_load_template(
						'usof/templates/fields/css', array(
							'name' => USBuilder::KEY_CUSTOM_CSS, // Meta key for post custom css
							'value' => '', // NOTE: The value is empty because the data should be loaded from the preview frame.
						)
					) ?>
				</div>
			</div>

			<!-- Templates Transit -->
			<div class="usb-template-transit hidden">
				<i class="fas fa-border-all"></i>
				<span>Template section</span>
			</div>

			<!-- Page Settings -->
			<div class="us-builder-panel-page-settings usof-container inited hidden">
				<!-- Begin page fields -->
				<?php us_load_template(
					'usof/templates/edit_form', array(
						'context' => 'us_builder',
						'params' => us_config( 'us-builder.page_fields.params', array() ),
						'type' => 'page_fields',
						'values' => array(), // Values will be set on the JS side after loading the iframe.
					)
				) ?>
				<!-- End page fields -->
				<!-- Begin page metadata -->
				<div class="us-builder-panel-page-meta">
					<?php foreach ( (array) us_config( 'meta-boxes', array() ) as $metabox_config ): ?>
						<?php
						if (
							! us_arr_path( $metabox_config, 'usb_context' )
							OR ! in_array( $post_type, (array) us_arr_path( $metabox_config, 'post_types', array() ) )
						) {
							continue;
						}
						?>
						<div class="us-builder-panel-page-meta-title"><?php esc_html_e( $metabox_config['title'] ) ?></div>
						<?php us_load_template(
							'usof/templates/edit_form', array(
								'context' => 'usb_metabox',
								'params' => us_arr_path( $metabox_config, 'fields', array() ),
								'type' => us_arr_path( $metabox_config, 'id', '' ),
								'values' => array(), // Values will be set on the JS side after loading the iframe.
							)
						) ?>
					<?php endforeach; ?>
				</div>
				<!-- End page metadata -->
			</div>
		</div>
		<footer class="us-builder-panel-footer">
			<div class="us-builder-panel-footer-btn ui-icon_settings usb_action_show_page_settings" title="<?php esc_attr_e( __( 'Page Settings', 'us' ) ) ?>"></div>
			<div class="us-builder-panel-footer-btn ui-icon_css3 usb_action_show_page_custom_css" title="<?php esc_attr_e( __( 'Page Custom CSS', 'us' ) ) ?>"></div>
			<div class="us-builder-panel-footer-btn ui-icon_devices usb_action_toggle_responsive_mode" title="<?php esc_attr_e( __( 'Responsive', 'us' ) ) ?>"></div>
			<div class="us-builder-panel-footer-btn ui-icon_undo usb_action_undo disabled" title="<?php esc_attr_e( us_translate( 'Undo' ) ) ?>"></div>
			<div class="us-builder-panel-footer-btn ui-icon_redo usb_action_redo disabled" title="<?php esc_attr_e( us_translate( 'Redo' ) ) ?>"></div>
			<?php if ( ! in_array( $post_type, array( 'us_page_block', 'us_content_template' ) ) ): ?>
			<!-- Begin data for create revision and show a preview page -->
			<form action="<?php echo admin_url( 'post.php' ) ?>" method="post" id="wp-preview" target="wp-preview-<?php echo (int) $post_id ?>">
				<textarea class="hidden" name="post_content"></textarea>
				<input type="hidden" name="post_ID" value="<?php echo (int) $post_id ?>">
				<input type="hidden" name="wp-preview" value="dopreview">
				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'update-post_' . $post_id ) ?>">
				<!-- Begin post meta data -->
				<textarea class="hidden" name="<?php esc_attr_e( USBuilder::KEY_CUSTOM_CSS ) ?>"></textarea>
				<!-- End post meta data -->
				<button type="submit" class="us-builder-panel-footer-btn ui-icon_eye" title="<?php esc_attr_e( us_translate( 'Preview Changes' ) ) ?>"></button>
			</form>
			<!-- End data for create revision and show a preview page -->
			<?php endif; ?>
			<button class="us-builder-panel-footer-btn type_save usb_action_save_changes disabled" disabled>
				<span><?php echo strip_tags( us_translate( 'Update' ) ) ?></span>
				<span class="usof-preloader"></span>
			</button>
		</footer>
		<!-- Notification Prototype -->
		<div class="us-builder-notification hidden">
			<span></span>
			<i class="ui-icon_close usb_action_notification_close" title="<?php esc_attr_e( us_translate( 'Close' ) ) ?>"></i>
		</div>
		<!-- Panel Preloader -->
		<span class="usof-preloader us-builder-panel-preloader"></span>

	</aside>
	<main id="us-builder-preview" class="us-builder-preview">
		<!-- Responsive Toolbar -->
		<div class="us-builder-preview-toolbar">
			<?php echo usof_get_responsive_buttons() ?>
			<a href="javascript:void(0)" class="ui-icon_close usb_action_hide_states_toolbar" title="<?php esc_attr_e( us_translate( 'Close' ) ) ?>"></a>
		</div>
		<!-- Preview Wrapper -->
		<div class="us-builder-preview-iframe-wrapper">
			<iframe data-src="<?php esc_attr_e( us_arr_path( $usb_settings, 'previewUrl', '' ) ) ?>"></iframe>
		</div>
	</main>
</div>
<!-- Begin scritps -->
<?php do_action( 'usb_admin_footer_scripts' ) ?>
</body>
</html>
