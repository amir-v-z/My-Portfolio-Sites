<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Closing part of Grid output
 */

// Reset grid items counter
global $us_grid_item_counter;
$us_grid_item_counter = 0;

// Reset grid outputing items
global $us_grid_outputs_items;
$us_grid_outputs_items = FALSE;

$us_grid_index = isset( $us_grid_index )
	? (int) $us_grid_index
	: 0;
$items_count = isset( $items_count )
	? $items_count
	: 0;
$filter_html = isset( $filter_html )
	? $filter_html
	: '';
$is_widget = isset( $is_widget )
	? $is_widget
	: FALSE;

// Check for filters in query parameters
$filter_url_prefix = us_get_grid_url_prefix( 'filter' );
$isset_url_filters = strpos( implode( ',', array_keys( $_GET ) ), $filter_url_prefix );

// Check Grid params and use default values from config, if its not set
$default_grid_params = us_shortcode_atts( array(), 'us_grid' );
foreach ( $default_grid_params as $param => $value ) {
	if ( ! isset( $$param ) ) {
		$$param = $value;
	}
}

// Check Carousel params and use default values from config, if its not set
if ( $type == 'carousel' ) {
	$default_carousel_params = us_shortcode_atts( array(), 'us_carousel' );
	foreach ( $default_carousel_params as $param => $value ) {
		if ( ! isset( $$param ) ) {
			$$param = $value;
		}
	}
}

// TODO: check if we need this here (already have same code in listing.php)
if (
	! $is_widget
	AND ! empty( $post_id )
	AND $type != 'carousel'
) {
	$us_grid_ajax_indexes[ $post_id ] = isset( $us_grid_ajax_indexes[ $post_id ] ) ? ( $us_grid_ajax_indexes[ $post_id ] ) : 1;
} else {
	$us_grid_ajax_indexes = NULL;
}

// Global preloader type
$preloader_type = us_get_option( 'preloader' );
if ( ! in_array( $preloader_type, array_merge( us_get_preloader_numeric_types(), array( 'custom' ) ) ) ) {
	$preloader_type = 1;
}

if ( $preloader_type == 'custom' AND $preloader_image = us_get_option( 'preloader_image', '' ) ) {
	$img_arr = explode( '|', $preloader_image );
	$preloader_image_html = wp_get_attachment_image( $img_arr[0], 'medium' );
	if ( empty( $preloader_image_html ) ) {
		$preloader_image_html = us_get_img_placeholder( 'medium' );
	}
} else {
	$preloader_image_html = '';
}
echo '</div>';

// Output custom styles from Design settings of every post in the Grid, if it has Post Content with Full Content
if ( $items_custom_css = us_jsoncss_compile( $items_jsoncss_collection ) ) {
	echo '<style id="grid-post-content-css">' . $items_custom_css . '</style>';
}

// Output preloader for Carousel and Filter
if ( $filter_html != '' OR $type !== 'carousel' ) {
	echo '<div class="w-grid-preloader">';
}
?>
<div class="g-preloader type_<?php echo $preloader_type; ?>">
	<div><?php echo $preloader_image_html ?></div>
</div>
<?php
if ( $filter_html != '' OR $type !== 'carousel' ) {
	echo '</div>';
}

// Output pagination for not Carousel type
if (
	(
		$wp_query->max_num_pages > 1
		AND $type != 'carousel'
	)
	OR $isset_url_filters !== FALSE
) {

	// Next page elements may have sliders, so we preloading the needed assets now
	if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
		wp_enqueue_script( 'us-royalslider' );
	}

	if ( $pagination == 'infinite' ) {
		$is_infinite = TRUE;
		$pagination = 'ajax';
	}

	if ( $pagination == 'regular' ) {

		// The main parameters for the formation of pagination
		$paginate_args = array(
			'after_page_number' => '</span>',
			'before_page_number' => '<span>',
			'mid_size' => 3,
			'next_text' => '<span>' . us_translate( 'Next' ) . '</span>',
			'prev_text' => '<span>' . us_translate( 'Previous' ) . '</span>',
		);

		// Adding filters to pagination, this will allow you to create pagination
		// based on filters for AJAX requests
		if ( wp_doing_ajax() AND ! empty( $us_grid_filter_params ) ) {
			parse_str( $us_grid_filter_params, $paginate_args['add_args'] );
		}

		// Adding order to pagination
		if ( ! empty( $grid_orderby ) ) {
			$paginate_args['add_args'][ us_get_grid_url_prefix( 'order' ) ] = $grid_orderby;
		}

		// Removes from `admin-ajax.php` links
		$paginate_links = paginate_links( $paginate_args );
		$paginate_home_url = ( has_filter( 'us_tr_home_url' ) )
			? trailingslashit( apply_filters( 'us_tr_home_url', NULL ) )
			: trailingslashit( home_url() );
		$paginate_links = str_replace( $paginate_home_url . 'wp-admin/admin-ajax.php', '', $paginate_links );

		if ( ! empty( $pagination_style ) ) {
			$paginate_class = ' custom us-nav-style_' . (int) $pagination_style;
		} else {
			$paginate_class = '';
		}

		?>
		<nav class="pagination navigation" role="navigation">
			<div class="nav-links<?php echo $paginate_class ?>">
				<?php echo $paginate_links ?>
			</div>
		</nav>
		<?php

	} elseif ( $pagination == 'ajax' ) {
		$pagination_btn_css = us_prepare_inline_css( array( 'font-size' => $pagination_btn_size ) );

		$loadmore_classes = $pagination_btn_fullwidth
			? ' width_full'
			: '';

		if ( $wp_query->max_num_pages <= 1 ) {
			$loadmore_classes .= ' done';
		}
		?>
		<div class="g-loadmore <?php echo $loadmore_classes ?>">
			<div class="g-preloader type_<?php echo ( $preloader_type == 'custom' ) ? '1' : $preloader_type; ?>">
				<div></div>
			</div>
			<button class="w-btn us-btn-style_<?php echo us_maybe_get_button_style( $pagination_btn_style ) ?>"<?php echo $pagination_btn_css ?>>
				<span class="w-btn-label"><?php echo $pagination_btn_text ?></span>
			</button>
		</div>
		<?php
	}
}

// Fix for multi-filter ajax pagination
if ( isset( $paged ) ) {
	$query_args['posts_per_page'] = $paged;
}

if ( $filter_html AND isset( $query_args['tax_query']['relation'] ) ) {
	unset( $query_args['tax_query']['relation'] );
}

/**
 * Recursively removing filters from meta_key by key
 *
 * @param array $items The items
 * @param string $skip_key The skip key
 *
 * @return array
 */
$func_remove_filters_in_meta_query = function( $items, $skip_key ) use( &$func_remove_filters_in_meta_query ) {
	$results = array();
	foreach ( $items as $index => $item ) {
		if ( ! empty( $item[0] ) AND is_array( $item[0] ) ) {
			$results[ $index ] = $func_remove_filters_in_meta_query( $item, $skip_key );
		} else if ( us_arr_path( $item, 'key' ) === $skip_key ) {
			continue;
		} else {
			$results[ $index ] = $item;
		}
	}
	return $results;
};

// Remove Grid Filters params from $query_args
if ( ! wp_doing_ajax() ) {
	$_filter_taxonomies = array();
	foreach ( us_get_filter_taxonomies( $filter_url_prefix, $us_grid_filter_params ) as $item_name => $item_value ) {
		// Get param_name
		$param = us_grid_filter_parse_param( $item_name );
		$item_source = us_arr_path( $param, 'source' );
		$item_name = us_arr_path( $param, 'param_name', $item_name );

		// Remove filters from tax_query
		if ( $item_source === 'tax' ) {
			$_filter_taxonomies[ us_get_grid_url_prefix( 'filter' ) . '_' . $item_name ] = implode( ',', $item_value );
			if ( ! empty( $query_args['tax_query'] ) ) {
				foreach ( $query_args['tax_query'] as $index => $tax ) {
					if ( us_arr_path( $tax, 'taxonomy' ) === $item_name ) {
						$tax_terms = us_arr_path( $tax, 'terms' );
						if ( ! is_array( $tax_terms ) ) {
							$tax_terms = array( $tax_terms );
						}
						foreach ( $item_value as $term_name ) {
							if (
								in_array( $term_name, $tax_terms )
								AND isset( $tax_terms[ array_search( $term_name, $tax_terms ) ] )
							) {
								unset( $tax_terms[ array_search( $term_name, $tax_terms ) ] );
							}
						}
						if ( empty( $tax_terms ) ) {
							unset( $query_args['tax_query'][ $index ] );
						}
					}
				}
			}

			// Remove filters from meta_query
		} elseif ( ! empty( $query_args['meta_query'] ) ) {
			$meta_query = us_arr_path( $query_args, 'meta_query', array() );
			$query_args['meta_query'] = $func_remove_filters_in_meta_query( $meta_query, $item_name );
		}
	}
	if ( class_exists( 'woocommerce' ) AND is_object( wc() ) ) {
		foreach ( us_arr_path( $query_args, 'tax_query', array() ) as $index => $tax ) {
			if ( us_arr_path( $tax, 'taxonomy' ) === 'product_visibility' ) {
				unset( $query_args['tax_query'][ $index ] );
			}
		}
	}
	if ( is_null( $us_grid_filter_params ) AND ! empty( $_filter_taxonomies ) ) {
		$us_grid_filter_params = build_query( $_filter_taxonomies );
	}

	// Added default query_args created from grid settings
	if ( ! empty( $_default_query_args ) ) {
		$query_args = array_merge( $query_args, $_default_query_args );
	}

	// Remove price range from `query_args`
	if ( isset( $query_args['_us_product_meta_lookup_prices'] ) ) {
		unset( $query_args['_us_product_meta_lookup_prices'] );
	}

	// Add attributes from the default WooCommerce filter
	if (
		class_exists( 'woocommerce' )
		AND function_exists( 'is_filtered' )
		AND is_filtered()
	) {
		// For attributes
		if ( $wc_filter_get = WC_Query::get_layered_nav_chosen_attributes() ) {
			foreach ( $wc_filter_get as $wc_filter_key => $wc_filter_value ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => $wc_filter_key,
					'terms' => $wc_filter_value['terms'],
					'field' => 'slug',
					'operator' => 'IN'
				);
			}
		}

		// For price
		if ( isset( $_GET['min_price'] ) AND isset( $_GET['max_price'] ) ) {
			$query_args['meta_query'][] = array(
				'key' => '_price',
				'value' => array($_GET['min_price'], $_GET['max_price']),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC'
			);
		}
	}
}

global $wp, $us_page_args;

// Define and output all JSON data
$json_data = array(
	// Controller options
	'action' => 'us_ajax_grid',
	'ajax_url' => admin_url( 'admin-ajax.php' ),
	'infinite_scroll' => ( isset( $is_infinite ) ? $is_infinite : 0 ),
	'max_num_pages' => $wp_query->max_num_pages,
	'pagination' => $pagination,
	'permalink_url' => is_singular()
		? get_permalink()
		: home_url( add_query_arg( array(), $wp->request ) ),
	// Grid listing template variables that will be passed to this file in the next call
	'template_vars' => array(
		'columns' => $columns,
		'exclude_items' => $exclude_items,
		'img_size' => $img_size,
		'ignore_items_size' => $ignore_items_size,
		'items_layout' => $items_layout,
		'items_offset' => $items_offset,
		'load_animation' => $load_animation,
		'overriding_link' => $overriding_link,
		'post_id' => $post_id,
		'query_args' => $query_args,
		'orderby_query_args' => $orderby_query_args,
		'type' => $type,
		'us_grid_ajax_index' => ! empty( $us_grid_ajax_indexes[ $post_id ] )
			? $us_grid_ajax_indexes[ $post_id ]
			: $us_grid_index,
		'us_grid_filter_params' => $us_grid_filter_params,
		'us_grid_index' => $us_grid_index,
		'_us_grid_post_type' => $_us_grid_post_type,
		'page_args' => $us_page_args,
	),
);

global $us_get_orderby;
if ( $grid_orderby = us_arr_path( $_GET, us_get_grid_url_prefix( 'order' ), $us_get_orderby ) ) {
	$json_data['template_vars']['grid_orderby'] = trim( $grid_orderby );
}

// Carousel settings
if ( $type == 'carousel' ) {

	$carousel_autoplay = ( $carousel_autoplay AND ( $items_count > $columns ) );
	$carousel_settings = array(
		'autoHeight' => ( $columns == 1 ) ? (int) $carousel_autoheight : 0,
		'autoplay' => $carousel_autoplay ? 1 : 0,
		'carousel_fade' => (int) !! $carousel_fade,
		'center' => (int) !! $carousel_center,
		'dots' => (int) !! $carousel_dots,
		'items' => $columns,
		'loop' => ( ( $carousel_autoplay AND $columns == 1 ) OR $carousel_center )
			? TRUE
			: !! $carousel_loop,
		'nav' => (int) !! $carousel_arrows,
		'slideby' => $carousel_slideby ? 'page' : '1',
		'smooth_play' => (int) !! $carousel_autoplay_smooth,
		'speed' => (int) $carousel_speed,
		'timeout' => (int) $carousel_interval * 1000,
		'transition' => strip_tags( $carousel_transition ),
		'aria_labels' => array(
			'prev' => us_translate( 'Previous' ),
			'next' => us_translate( 'Next' ),
		),
	);

	// Responsive options as on https://owlcarousel2.github.io/OwlCarousel2/demos/responsive.html
	$carousel_breakpoints = array(
		0 => array(
			'autoHeight' => ( min( (int) $breakpoint_3_cols, $columns ) == 1 ) ? (int) $carousel_autoheight : 0,
			'autoplay' => (int) !! $breakpoint_3_autoplay,
			'autoplayHoverPause' => (int) !! $breakpoint_3_autoplay,
			'items' => min( (int) $breakpoint_3_cols, $columns ),
			'loop' => ( (int) !! $breakpoint_3_autoplay AND ( min( (int) $breakpoint_3_cols, $columns ) == 1 ) )
				? TRUE
				: $carousel_settings['loop'],
			'stagePadding' => (int) $breakpoint_3_offset,
		),
		(int) $breakpoint_3_width => array(
			'autoHeight' => ( min( (int) $breakpoint_2_cols, $columns ) == 1 ) ? (int) $carousel_autoheight : 0,
			'autoplay' => (int) !! $breakpoint_2_autoplay,
			'autoplayHoverPause' => (int) ! ! $breakpoint_2_autoplay,
			'items' => min( (int) $breakpoint_2_cols, $columns ),
			'loop' => ( (int) !! $breakpoint_2_autoplay AND ( min( (int) $breakpoint_2_cols, $columns ) == 1 ) )
				? TRUE
				: $carousel_settings['loop'],
			'stagePadding' => (int) $breakpoint_2_offset,
		),
		(int) $breakpoint_2_width => array(
			'autoHeight' => ( min( (int) $breakpoint_1_cols, $columns ) == 1 ) ? (int) $carousel_autoheight : 0,
			'autoplay' => (int) !! $breakpoint_1_autoplay,
			'autoplayHoverPause' => (int) !! $breakpoint_1_autoplay,
			'items' => min( (int) $breakpoint_1_cols, $columns ),
			'loop' => ( (int) !! $breakpoint_1_autoplay AND ( min( (int) $breakpoint_1_cols, $columns ) == 1 ) )
				? TRUE
				: $carousel_settings['loop'],
			'stagePadding' => (int) $breakpoint_1_offset,
		),
		(int) $breakpoint_1_width => array(
			'items' => (int) $columns,
			'stagePadding' => (int) $carousel_items_offset,
		),
	);

	$json_data = array_merge( $json_data, array(
		'carousel_settings' => $carousel_settings,
		'carousel_breakpoints' => $carousel_breakpoints,
	) );
}

// Add lang variable if WPML is active
if ( class_exists( 'SitePress' ) ) {
	global $sitepress;
	if ( apply_filters( 'us_tr_default_language', NULL ) != apply_filters( 'us_tr_current_language', NULL ) ) {
		$json_data['template_vars']['lang'] = apply_filters( 'us_tr_current_language', NULL );
	}
}

if ( ! us_amp() ) {
	?>
	<div class="w-grid-json hidden"<?= us_pass_data_to_js( $json_data ) ?>></div>
	<?php
}

// Output popup semantics
if ( ! us_amp() AND $overriding_link == 'popup_post' ) {
	?>
	<div class="l-popup">
		<div class="l-popup-overlay"></div>
		<div class="l-popup-wrap">
			<div class="l-popup-box">
				<div class="l-popup-box-content"<?php echo us_prepare_inline_css( array( 'max-width' => $popup_width ) ); ?>>
					<div class="g-preloader type_<?php echo $preloader_type; ?>">
						<div><?php echo $preloader_image_html ?></div>
					</div>
					<iframe class="l-popup-box-content-frame" allowfullscreen></iframe>
				</div>
			</div>
			<?php if ( ! empty( $popup_arrows ) ) { ?>
				<div class="l-popup-arrow to_next" title="<?php echo us_translate( 'Next' ) ?>"></div>
				<div class="l-popup-arrow to_prev" title="<?php echo us_translate( 'Previous' ) ?>"></div>
			<?php } ?>
			<div class="l-popup-closer"></div>
		</div>
	</div>
	<?php
}

echo '</div>';

// Output the "No results" block after the "w-grid" div container
if ( $no_results ) {

	us_grid_shows_no_results();

	if ( $use_custom_query ) {
		us_close_wp_query_context();
	}
}
