<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configurations for USBuilder
 */

/**
 * Configuring fields for the page settings screen
 *
 * @var array
 */
$page_fields = array(
	'params' => array(
		'post_title' => array(
			'title' => us_translate( 'Title' ),
			'type' => 'text',
			'std' => '',
		), // post_status, post_name etc.
	),
);

/**
 * Different templates that are required for the USBuilder to work on the frontend side
 *
 * @var array
 */
$templates = array(
	'vc_row' => '[vc_row usbid="{%vc_row%}"][vc_column usbid="{%vc_column%}"]{%content%}[/vc_column][/vc_row]',
);

// VC TTA (Tabs/Tour/Accordion) Section ( The sections that are created with a new element )
$vc_tta_section = '[vc_tta_section title="{%title_1%}" usbid="{%vc_tta_section_1%}"]';
$vc_tta_section .= '[vc_column_text usbid="{%vc_column_text%}"]{%vc_column_text_content%}[/vc_column_text]';
$vc_tta_section .= '[/vc_tta_section]';
$vc_tta_section .= '[vc_tta_section title="{%title_2%}" usbid="{%vc_tta_section_2%}"][/vc_tta_section]';
$templates['vc_tta_section'] = $vc_tta_section;

/**
 * Deferred assets for the admin part of the builder
 *
 * @var array
 */
$deferred_assets = array(
	// A set of minimal assets for initializing a code editor (Order is important here)
	'codeEditor' => array(
		'wp-codemirror',
		'csslint',
		'esprima',
		'code-editor',
	),
);

/**
 * List of usof field types for which to use throttle
 * Note: Types of fields for which a large interval of recording changes in history is used,
 * this is necessary for fields that have a high frequency of changes, for example,
 * when entering text in a text field.
 *
 * @var array
 */
$use_throttle_for_fields = array(
	'editor', 'color', 'text', 'textarea',
);

/**
 * List of usof field types for which the update interval is used
 * Note: Field types that use spacing when the preview refreshes are required
 * for fields that have a high rate of change, such as when choosing a color.
 *
 * @var array
 */
$use_long_update_for_fields = array(
	'color', 'design_options',
);

/**
 * Elements outside of the container, such as the header, footer, or sidebar.
 *
 * @var array
 */
$outside_elms = array(
	'header', 'footer',
);

/**
 * @var array
 */
return array(
	'deferred_assets' => $deferred_assets,
	'outside_elms' => $outside_elms,
	'page_fields' => $page_fields,
	'templates' => $templates,

	// Undo/Redo settings
	'use_long_update_for_fields' => $use_long_update_for_fields,
	'use_throttle_for_fields' => $use_throttle_for_fields,

	// Maximum size of changes in the data history
	'max_data_history' => 100,

	// Since we introduced a new type of root `container` at the level of shortcodes and builder,
	// then we will add a rule for it that should be ignored when adding a new element
	'as_parent_container_only' => 'vc_row,import_template',

	// Elements with more than one node in the result must have a common wrap
	// Example: `<div class="one">...</div><div class="two">...</div>`
	'with_wrappers' => array( 'us_grid', 'us_carousel' ),
);
