<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Image element
 */

$_atts['class'] = 'w-image';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( ! empty( $style ) ) {
	$_atts['class'] .= ' style_' . $style;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

if ( has_filter( 'us_tr_object_id' ) ) {
	$image = apply_filters( 'us_tr_object_id', $image );
}

// Set Aspect Ratio values
$ratio_helper_html = '';
if ( $has_ratio ) {
	$ratio_array = us_get_aspect_ratio_values( $ratio, $ratio_width, $ratio_height );
	$ratio_helper_html = '<div style="padding-bottom:' . round( $ratio_array[1] / $ratio_array[0] * 100, 4 ) . '%"></div>';
	$_atts['class'] .= ' has_ratio';
}

// Classes & inline styles
if ( $us_elm_context == 'shortcode' ) {

	// Get image ID from shortcode
	$img = $image;

	$_atts['class'] .= ' align_' . $align;
	$_atts['class'] .= ( $meta ) ? ' meta_' . $meta_style : '';
}

// Fallback for the old "animate" attribute (for versions before 8.0)
if ( ! us_amp() AND ! us_design_options_has_property( $css, 'animation-name' ) AND ! empty( $atts['animate'] ) ) {
	$_atts['class'] .= ' us_animate_' . $atts['animate'];
	if ( ! empty( $atts['animate_delay'] ) ) {
		$_atts['style'] = 'animation-delay:' . (float) $atts['animate_delay'] . 's';
	}
}

// Get the image
$img_src = '';
$img_loading_attr = array();

// Disable lazy loading
if ( $disable_lazy_loading ) {
	$img_loading_attr['loading'] = FALSE;
}

$img_html = wp_get_attachment_image( $img, $size, FALSE, $img_loading_attr );

if ( empty( $img_html ) ) {
	// check if image ID is URL
	if ( strpos( $img, 'http' ) !== FALSE ) {
		$img_src = $img;
		$img_url_atts = array(
			'src' => esc_url( $img ),
			'loading' => 'lazy',
			'alt' => '',
		);
		if ( $disable_lazy_loading ) {
			unset( $img_url_atts['loading'] );
		}
		$img_html = '<img ' . us_implode_atts( $img_url_atts ) . '>';

		// if no use placeholder
	} else {
		$img_html = us_get_img_placeholder( $size );
	}
}

// Add the different image for transparent header if the image exists
if (
	$us_elm_context == 'header'
	AND ! empty( $img_transparent )
	AND $img_transparent_html = wp_get_attachment_image( $img_transparent, $size, FALSE, $img_loading_attr )
) {
	$_atts['class'] .= ' with_transparent';
	$img_html .= $img_transparent_html;
}

// Title and description
$img_meta_html = '';
if (
	$us_elm_context == 'shortcode'
	AND $img
	AND $img_html
	AND $meta
) {

	if ( $attachment = get_post( $img ) ) {
		// Use Caption
		$title = trim( strip_tags( $attachment->post_excerpt ) );

		// Use Title if no Caption
		if ( empty( $title ) ) {
			$title = trim( strip_tags( $attachment->post_title ) );
		}

		// Use ALT if no Caption
		if ( empty( $title ) ) {
			$title = trim( strip_tags( get_post_meta( $attachment->ID, '_wp_attachment_image_alt', TRUE ) ) );
		}
	} else {
		// Set fallback title
		$title = us_translate( 'Title' );
	}

	$img_meta_html .= '<div class="w-image-meta">';
	$img_meta_html .= ( ! empty( $title ) ) ? '<div class="w-image-title">' . $title . '</div>' : '';
	$img_meta_html .= ( ! empty( $attachment->post_content ) ) ? '<div class="w-image-description">' . $attachment->post_content . '</div>' : '';
	$img_meta_html .= '</div>';

	// When colors is set in Design settings, add the specific class
	if ( us_design_options_has_property( $css, array( 'background-color', 'background-image' ) ) ) {
		$_atts['class'] .= ' has_bg_color';
	}
}

// Get url to the image to imitate shadow
$img_shadow_html = '';
if ( $style == 'shadow-2' ) {
	$img_src = empty( $img_src ) ? wp_get_attachment_image_url( $img, $size ) : $img_src;
	$img_src = empty( $img_src ) ? us_get_img_placeholder( $size, TRUE ) : $img_src;

	$img_shadow_html = '<div class="w-image-shadow" style="background-image:url(' . $img_src . ');"></div>';
}

// Link
if ( $onclick === 'none' ) {
	$link_atts = array();

} elseif ( $onclick === 'post' ) {

	// Terms of selected taxonomy in Grid
	global $us_grid_item_type;
	if ( $us_elm_context == 'grid' AND $us_grid_item_type == 'term' ) {
		global $us_grid_term;
		$link_atts['href'] = get_term_link( $us_grid_term );
	} else {
		$link_atts['href'] = apply_filters( 'the_permalink', get_permalink() );
	}

} elseif ( $onclick === 'lightbox' AND ! empty( $img ) ) {
	$link_atts['href'] = wp_get_attachment_image_url( $img, 'full' );
	if ( ! us_amp() ) {
		$link_atts['ref'] = 'magnificPopup';
	}
} elseif ( $onclick === 'custom_link' ) {
	$link_atts = us_generate_link_atts( $link );
} elseif ( $onclick === 'onclick' ) {
	$onclick_code = ! empty( $onclick_code ) ? $onclick_code : 'return false';
	$link_atts['href'] = '#';
	$link_atts['onclick'] = esc_js( trim( $onclick_code ) );
} else {
	$link_atts = us_generate_link_atts( 'url:{{' . $onclick . '}}|||' );
}

if ( ! empty( $link_atts['href'] ) ) {
	$tag = 'a';

	// Force "Open in a new tab" attributes
	if ( empty( $link_atts['target'] ) AND $link_new_tab ) {
		$link_atts['target'] = '_blank';
		$link_atts['rel'] = 'noopener nofollow';
	}

	// Add placeholder aria-label for Accessibility
	$link_atts['aria-label'] = ! empty( $title ) ? $title : us_translate( 'Link' );

} else {
	$tag = 'div';
}

$link_atts['class'] = 'w-image-h';

// Add extra mockup as backround for Phone Styles
global $us_template_directory_uri;
if ( $style == 'phone6-1' ) {
	$link_atts['style'] = 'background-image: url(' . esc_url( $us_template_directory_uri ) . '/img/phone-6-black-real.png)';
}
if ( $style == 'phone6-2' ) {
	$link_atts['style'] = 'background-image: url(' . esc_url( $us_template_directory_uri ) . '/img/phone-6-white-real.png)';
}
if ( $style == 'phone6-3' ) {
	$link_atts['style'] = 'background-image: url(' . esc_url( $us_template_directory_uri ) . '/img/phone-6-black-flat.png)';
}
if ( $style == 'phone6-4' ) {
	$link_atts['style'] = 'background-image: url(' . esc_url( $us_template_directory_uri ) . '/img/phone-6-white-flat.png)';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<' . $tag . us_implode_atts( $link_atts ) . '>';
$output .= $ratio_helper_html;
$output .= $img_shadow_html;
$output .= $img_html;
$output .= ( $meta_style == 'modern' OR strpos( $style, 'phone' ) === 0 ) ? $img_meta_html : '';
$output .= '</' . $tag . '>';
$output .= ( $meta_style == 'modern' OR strpos( $style, 'phone' ) === 0 ) ? '' : $img_meta_html;
$output .= '</div>';

echo $output;
