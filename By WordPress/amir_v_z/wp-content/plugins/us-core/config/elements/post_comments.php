<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: post_comments
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Post Comments', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-comments',
	'params' => us_set_params_weight(

		// General section
		array(
			'layout' => array(
				'title' => us_translate( 'Show' ),
				'type' => 'select',
				'options' => array(
					'comments_template' => __( 'List of comments with response form', 'us' ),
					'amount' => __( 'Comments amount', 'us' ),
				),
				'std' => 'comments_template',
				'admin_label' => TRUE,
				'context' => array( 'shortcode' ),
				'usb_preview' => array(
					'mod' => 'layout',
				),
			),
			'hide_zero' => array(
				'type' => 'switch',
				'switch_text' => __( 'Hide this element if no comments', 'us' ),
				'std' => 0,
				'show_if' => array( 'layout', '=', 'amount' ),
				'usb_preview' => TRUE,
			),
			'number' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show only number', 'us' ),
				'std' => 0,
				'show_if' => array( 'layout', '=', 'amount' ),
				'usb_preview' => TRUE,
			),
			'link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'select',
				'options' => array(
					'post' => __( 'To a Post comments', 'us' ),
					'custom' => __( 'Custom', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'post',
				'show_if' => array( 'layout', '=', 'amount' ),
				'usb_preview' => TRUE,
			),
			'custom_link' => array(
				'placeholder' => us_translate( 'Enter the URL' ),
				'type' => 'link',
				'std' => array(),
				'shortcode_std' => '',
				'grid_classes' => 'desc_3',
				'show_if' => array( 'link', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'color_link' => array(
				'title' => __( 'Link Color', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Inherit from text color', 'us' ),
				'std' => 1,
				'show_if' => array( 'link', '!=', 'none' ),
				'usb_preview' => array(
					'toggle_class' => 'color_link_inherit',
				),
			),
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'show_if' => array( 'layout', '=', 'amount' ),
				'usb_preview' => TRUE,
			),
		),

		$design_options_params,
		$hover_options_params
	),
);
