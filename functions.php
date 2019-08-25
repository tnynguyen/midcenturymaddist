<?php
/**
 * Filename: functions.php
 * Author: Mid Century Maddist
 * Platform: WordPress Woocommerce
 * Theme: storefront-child 
 * 
 * This file contains new and overridden store functions for the store's customized theme 
 *
 */

/**
 * Admin Backend: replace the WordPress logo with the Mid Century Maddist logo on the login page
 *
 */
add_action( 'login_enqueue_scripts', 'maddist_logo_swap_admin_login' );
function maddist_logo_swap_admin_login() { 
    ?> 
    <style type="text/css"> 
        body.login div#login h1 a {
            background-image: url('/wp-content/uploads/logo.png');
        }
    </style>
    <?php 
}

/**
 * Admin Backend: add the customized theme javascript and css files for the admin backend area
 *
 */
add_action( 'admin_enqueue_scripts', 'maddist_custom_wp_admin_style_enqueue' );
function maddist_custom_wp_admin_style_enqueue() {
    wp_enqueue_style( 'custom_wp_admin_css', get_stylesheet_directory_uri() . '/admin-style.css', false, '1.0.0' );

    wp_register_script( 'custom_wp_admin_js', get_stylesheet_directory_uri() . '/js/maddist-admin.js', '', '', true );
    wp_enqueue_script( 'custom_wp_admin_js' );

    wp_register_script( 'custom_wp_admin_2_js', get_stylesheet_directory_uri() . '/js/maddist-admin-2.js', '', '', true );
    wp_enqueue_script( 'custom_wp_admin_2_js' );
}

/**
 * Add and define the product image sizes
 *
 */
add_action( 'init', 'custom_define_image_sizes' );
function custom_define_image_sizes() {
    remove_image_size( 'shop_catalog' );
    remove_image_size( 'woocommerce_single' );
    remove_image_size( 'shop_single' );

    add_image_size( 'woocommerce_single', '2048', '0', false );
    add_image_size( 'shop_single', '2048', '0', false );
    add_image_size( 'medium_large', '1800', '0', false );
    add_image_size( 'woocommerce_thumbnail', '1200', '900', true );
    add_image_size( 'shop_catalog', '1200', '900', true );
    add_filter( 'woocommerce_gallery_image_size', function( $size ) {
        return 'large';
    } );
    add_image_size( 'woocommerce_gallery_thumbnail', '100', '100', true );
}

/**
 * Override any theme settings that define the thumbnail size
 *
 * @param array|object $args Theme settings with the old default thumbnail size
 * @return array|object $args Theme settings with the new thumbnail size
 */
add_filter( 'storefront_woocommerce_args', 'override_storefront_woocommerce_args' );
function override_storefront_woocommerce_args( $args ) {
	$args['thumbnail_image_width'] = 1200;
	return $args;
}

/**
 * Add the black header banner to the store
 *
 * The text content is pulled from page id 2706 and titled 'Header Banner'
 *
 */
add_action( 'storefront_before_header', 'add_header_banner', 10);
function add_header_banner() {
    $post_id = 2706;
    $post = get_post($post_id);
    $post_content = $post->post_content;
    ?>
    <div id="header-banner"><?php echo $post_content; ?></div>
    <?php
}

/**
 * Move the product search bar in the header so it is inline with the menu
 *
 */
add_action( 'init', 'custom_storefront_product_search' );
function custom_storefront_product_search() {
    remove_action('storefront_header', 'storefront_product_search', 40);
    add_action('storefront_header', 'aws_product_search', 25);
}
function aws_product_search() {
    if ( storefront_is_woocommerce_activated() ) { ?>
        <div class="site-search">
            <?php echo do_shortcode("[aws_search_form id='1']"); ?>
        </div>
    <?php
    }
}

/**
 * Remove the Woocommerce footer message and use our own
 *
 */
add_action( 'init', 'custom_remove_footer_credit', 10 );
function custom_remove_footer_credit() {
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    add_action( 'storefront_footer', 'custom_storefront_credit', 20 );
}
function custom_storefront_credit() {
    ?>
    <div class="site-info">
        &copy; <?php echo get_bloginfo( 'name' ) . ' ' . date('Y'); ?>
    </div><!-- .site-info -->
    <?php
}

/**
 * Show empty categories on the shop page
 *
 * Even if there aren't any products in a category always show:
 * Storage, Seating, Tables, Accessories, Lighting, and Sale
 *
 * @return bool Unset the hide empty category option
 */
add_filter( 'woocommerce_product_subcategories_hide_empty', 'hide_empty_categories', 10, 1 );
function hide_empty_categories ( $hide_empty ) {
    $hide_empty  =  FALSE;
    return $hide_empty;
}

/**
 * Hide the Uncategorized category from the shop page
 *
 * Products that aren't assigned to a category show up in Uncategorized category
 *
 * @param array|object $args The old arguments for the store categories
 * @return array|object $args The new arguments for the store categories with those to exclude
 */
add_filter( 'woocommerce_product_subcategories_args', 'remove_uncategorized_category' );
function remove_uncategorized_category( $args ) {
  $uncategorized = get_option( 'default_product_cat' );
  $args['exclude'] = $uncategorized;
  return $args;
}

/**
 * Remove the sidebar from the shop page
 *
 */
add_action( 'get_header', 'hide_sidebar' );
function hide_sidebar() {
    if ( is_woocommerce() || is_checkout() ) {
        remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
    }
}

/**
 * Remove the page title from the shop page
 *
 */
add_filter( 'woocommerce_show_page_title' , 'custom_woocommerce_hide_page_title' );
function custom_woocommerce_hide_page_title() {
    return false;
}

/**
 * Remove the category description from displaying
 *
 */
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );


/**
 * Move the Sale tag location on the single product page
 *
 */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_sale_flash', 0 );

/**
 * Remove the data tabs from the single product page
 *
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

/**
 * Add the product description to the single product page
 *
 */
add_action ( 'woocommerce_single_product_summary', 'custom_woocommerce_product_description', 20 );
function custom_woocommerce_product_description() {
    the_content();
}

/**
 * Add the product dimensions to the single product page
 *
 * Note: the length notation is listed as depth in the front end
 * The order is width, depth(length), height
 *
 * @global array|object $product The product object on the single product page
 */
add_action ( 'woocommerce_single_product_summary', 'custom_woocommerce_product_dimensions', 21 );
function custom_woocommerce_product_dimensions() {
    global $product;

    ?>
        <?php if ( $product->has_dimensions() ) : ?>
        <?php $dimensions = $product->get_dimensions(false); ?>
            <p><strong>Dimensions:</strong> <?php if ( !empty($dimensions['width']) ) { echo esc_html( $dimensions['width'] ) . '&#8243; W,'; } ?>
                           <?php if ( !empty($dimensions['length']) ) { echo esc_html( $dimensions['length'] ); if ( !empty($dimensions['height']) ) { echo '&#8243; D,'; } else { echo '&#8243; D'; } } ?>
                           <?php if ( !empty($dimensions['height']) ) { echo esc_html( $dimensions['height'] ) . '&#8243; H'; } ?>
            </p>
        <?php endif; ?>
    <?php
}

/**
 * Replace the 'Add to Cart' button on the single product page if the product is on hold
 *
 * @global array|object $product The product object on the single product page
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'maddist_template_single_add_to_cart', 30 );
function maddist_template_single_add_to_cart() {
    global $product;

    $on_hold = get_post_meta($product->get_id(), '_on_hold', true);

    if ( $on_hold == 'yes' ) {
        echo '<div class="cart"><p class="on-hold button alt">On Hold</p></div>';
    }
    else {
        do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
    }

}

/**
 * Add the Social media sharing icons to the single product page in the summary section
 *
 */
add_action ( 'woocommerce_single_product_summary', 'custom_social_share', 34 );
function custom_social_share() {
    echo do_shortcode('[TheChamp-Sharing type="horizontal"]');
}

/**
 * Remove the upsell and related product loops from the single product page
 * We will be adding our own custom upsell/related product loop
 *
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'init', 'custom_storefront_upsell_display', 15 );
function custom_storefront_upsell_display() {
    remove_action( 'woocommerce_after_single_product_summary', 'storefront_upsell_display', 15 );
}

/**
 * Filter out products with zero stock inventory from list of products in stock
 *
 */
function wc_products_array_filter_in_stock( $product ) {
    if ($product->get_stock_quantity() == 0) {
         return false;
    }
    return $product && is_a( $product, 'WC_Product' );
}

/**
 * Get the products listed in the upsells and related fields for the product listed
 *
 * The maximum of upsells and related items shown on the single product page is 6
 * If the limit is reached with the number of upsells then no related items are added to the loop
 * If the number of upsells and related items is short of the limit, then the loop is filled out
 * with items of the same category
 * 
 * @global array|object $product The featured item listed on the single product page
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
add_action( 'woocommerce_after_single_product_summary', 'custom_storefrontchild_upsell_related_products', 15 );
function custom_storefrontchild_upsell_related_products() {
    global $product;

    if ( ! $product ) {
        return;
    }

    $limit = 6;
    $orderby = 'menu_order';
    $columns = 6;
    $order = 'asc';

    // Handle the legacy filter which controlled posts per page etc.
    $args = apply_filters( 'woocommerce_upsell_display_args', array(
        'posts_per_page' => $limit,
        'orderby'        => $orderby,
        'columns'        => $columns,
    ) );
    wc_set_loop_prop( 'name', 'up-sells' );
    wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_upsells_columns', isset( $args['columns'] ) ? $args['columns'] : $columns ) );

    $upsell_cross_sell_ids = array_merge($product->get_upsell_ids(), $product->get_cross_sell_ids());

    // Get in stock upsells then sort them at random, then limit result set.
    $upsells = wc_products_array_orderby( array_filter( array_map( 'wc_get_product', $upsell_cross_sell_ids ), 'wc_products_array_filter_in_stock' ), $orderby, $order );
    $upsells = $limit > 0 ? array_slice( $upsells, 0, $limit ) : $upsells;

    /* start of related products handling */
    $upsellcount = count($upsells);
    $relatedcount = $limit - $upsellcount;

    $defaults = array(
        'posts_per_page' => 6,
        'columns'        => 6,
        'orderby'        => 'rand',
        'order'          => 'asc',
    );

    $args = wp_parse_args( $args, $defaults );

    // Get visible related products then sort them at random.
    $args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $relatedcount, $upsell_cross_sell_ids ) ), 'wc_products_array_filter_in_stock' );

    // Handle orderby.
    $args['related_products'] = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

    // Set global loop values.
    wc_set_loop_prop( 'name', 'related' );
    wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_related_products_columns', $args['columns'] ) );

    wc_get_template( 'single-product/up-sells-related.php', array(
        'upsells'        => $upsells,
        'related_products' => $args['related_products'],

        // Not used now, but used in previous version of up-sells.php.
        'posts_per_page' => $limit,
        'orderby'        => $orderby,
        'columns'        => $columns,
    ) );

}

/**
 * Remove out of stock items from the product search
 *
 * @param array|object $products Complete list of products found in the search
 * @return array|object $products List of products found in the search sans those out of stock
 */
add_filter( 'woocommerce_json_search_found_products', 'filter_woocommerce_json_search_found_products', 10, 1 );
function filter_woocommerce_json_search_found_products( $products ) {

    foreach($products as $product_id => $product_name) {
	$product = wc_get_product($product_id);
	if ($product->get_stock_quantity() == 0) {
		unset($products[$product_id]);
	}
    }

    return $products;
}

/**
 * Remove the Add to Cart button from the product loops
 *
 */
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

/**
 * Remove cross-sell items from the Shopping Cart page
 *
 */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

/**
 * Add and change the order of the drop down sort by options on the Category pages
 *
 * @param array|object $orderby The initial drop down sort options
 * @return array|object $orderby The new modified drop down sort options
 */
add_filter('woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby', 102 );
function custom_woocommerce_catalog_orderby($orderby) {
	$orderby['date'] = __('Sort by most recent', 'woocommerce');
        $orderby['onsale'] = __('Sort by On Sale', 'woocommerce-product-sort-and-display' );
        $orderby['featured'] = __('Sort by Featured', 'woocommerce-product-sort-and-display' );

	return $orderby;
}

/**
 * Remove extra wrapper displaying pagination count on the Category pages
 *
 */
add_action ( 'init', 'custom_storefront_after_shop_loop', 15);
function custom_storefront_after_shop_loop() {
    remove_action( 'woocommerce_before_shop_loop',       'woocommerce_result_count',                 20 );

    remove_action( 'woocommerce_after_shop_loop',        'woocommerce_catalog_ordering',             10 );
    remove_action( 'woocommerce_after_shop_loop',        'woocommerce_result_count',                 20 );
}

/**
 * Remove the zoom feature in the product image gallery
 *
 */
add_action ( 'init', 'custom_woocommerce_product_gallery_zoom' );
function custom_woocommerce_product_gallery_zoom() {
    remove_theme_support( 'wc-product-gallery-zoom' );
}


/* add js for ajax add to cart, zip code check, and hide img title */
/**
 * Add custom javascript files to the storesite
 *
 * ajax-add-to-cart.js: Handles adding (and removing) the product to the user's shopping cart
 * zipcode-validation.js: In the shipping calculator, performs validation on California zipcodes
 * hide-img-title.js: Remove the title attribute from the product images. They were previously showing when hovering
 *                    the mouse over the images
 */
add_action( 'wp_enqueue_scripts', 'wp_woo_js_enqueue' );
function wp_woo_js_enqueue() {
    wp_register_script( 'ajax-add-to-cart', get_stylesheet_directory_uri() . '/js/ajax-add-to-cart.js', '', '', true );
    wp_enqueue_script( 'ajax-add-to-cart' );

    wp_register_script( 'zipcode-validation', get_stylesheet_directory_uri() . '/js/zipcode-validation.js', '', '', true );
    wp_enqueue_script( 'zipcode-validation' );

    wp_register_script( 'hide-img-title', get_stylesheet_directory_uri() . '/js/hide-img-title.js', '', '', true );
    wp_enqueue_script( 'hide-img-title' );
}

/**
 * Move the location of added to cart messages to after the summary on single product page
 *
 */
add_action( 'wp_head', 'reorder_shop_messages' );
function reorder_shop_messages() {
    if( is_product() )
        remove_action( 'storefront_content_top', 'storefront_shop_messages', 15 ); 
    remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
    add_action('woocommerce_product_meta_end', 'storefront_shop_messages', 1 );
}

/**
 * Show the subcategory title in the product loop.
 *
 * Overwritten WooCommerce core function
 *
 * @param object $category Category object.
 */
function woocommerce_template_loop_category_title( $category ) {
    ?>
    <h2 class="woocommerce-loop-category__title">
        <?php
        echo esc_html( $category->name );
        ?>
    </h2>
    <?php
}

/**
 * Show the product thumbnail in the product loop.
 *
 * Overwritten WooCommerce core function
 * Adding a div container to the product thumbnail
 * 
 * @param thumbnail size, default is 'woocommerce_thumbnail'.
 * @return string HTML element of the product thumbnail
 */
function woocommerce_get_product_thumbnail( $size = 'woocommerce_thumbnail', $deprecated1 = 0, $deprecated2 = 0 ) {
    global $product;

    $image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

    return $product ? '<div class="product-loop-thumbnail">' . $product->get_image( $image_size ) . '</div>' : '';
}

/**
 * Display Header Cart
 *
 * Overwritten WooCommerce core function
 *
 * @since  1.0.0
 * @uses  storefront_is_woocommerce_activated() check if WooCommerce is activated
 * @return void
 */
function storefront_header_cart() {
    if ( storefront_is_woocommerce_activated() ) {
        if ( is_cart() ) {
            $class = 'current-menu-item';
        } else {
            $class = '';
        }
    ?>
    <ul id="site-header-cart" class="site-header-cart menu">
        <li class="site-header-cart-topmenu <?php echo esc_attr( $class ); ?>">
            <?php storefront_cart_link(); ?>
        </li>
        <li>
            <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
        </li>
    </ul>
    <?php
    }
}

/**
 * Display Primary Navigation
 *
 * Overwritten WooCommerce core function
 *
 * @since  1.0.0
 * @return void
 */
function storefront_primary_navigation() {
    ?>
    <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'storefront' ); ?>">
    <button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><?php echo esc_attr( apply_filters( 'storefront_menu_toggle_text', __( '', 'storefront' ) ) ); ?></span></button>
        <?php
            wp_nav_menu(
                array(
                    'theme_location'        => 'primary',
                    'container_class'       => 'primary-navigation',
                )
            );

            wp_nav_menu(
                array(
                    'theme_location'        => 'handheld',
                    'container_class'       => 'handheld-navigation',
                )
            );
        ?>
    </nav><!-- #site-navigation -->
    <?php
}

/**
 * Sell only in specific states
 *
 * Remove the states not eligible to purchase
 * Final list is of states that CAN purchase
 *
 * @param object $states Initial object of eligible states to sell to
 * @return object $states Modified object of eligible states to sell to
 */
add_filter( 'woocommerce_states', 'wc_sell_only_states' );
function wc_sell_only_states( $states ) {

	$states['US'] = array(
		'AL' => __( 'Alabama', 'woocommerce' ),
		//'AK' => __( 'Alaska', 'woocommerce' ),
		'AZ' => __( 'Arizona', 'woocommerce' ),
		'AR' => __( 'Arkansas', 'woocommerce' ),
		'CA' => __( 'California', 'woocommerce' ),
		'CO' => __( 'Colorado', 'woocommerce' ),
		'CT' => __( 'Connecticut', 'woocommerce' ),
		'DE' => __( 'Delaware', 'woocommerce' ),
		'DC' => __( 'District Of Columbia', 'woocommerce' ),
		'FL' => __( 'Florida', 'woocommerce' ),
		'GA' => __( 'Georgia', 'US state of Georgia', 'woocommerce' ),
		//'HI' => __( 'Hawaii', 'woocommerce' ),
		'ID' => __( 'Idaho', 'woocommerce' ),
		'IL' => __( 'Illinois', 'woocommerce' ),
		'IN' => __( 'Indiana', 'woocommerce' ),
		'IA' => __( 'Iowa', 'woocommerce' ),
		'KS' => __( 'Kansas', 'woocommerce' ),
		'KY' => __( 'Kentucky', 'woocommerce' ),
		'LA' => __( 'Louisiana', 'woocommerce' ),
		'ME' => __( 'Maine', 'woocommerce' ),
		'MD' => __( 'Maryland', 'woocommerce' ),
		'MA' => __( 'Massachusetts', 'woocommerce' ),
		'MI' => __( 'Michigan', 'woocommerce' ),
		'MN' => __( 'Minnesota', 'woocommerce' ),
		'MS' => __( 'Mississippi', 'woocommerce' ),
		'MO' => __( 'Missouri', 'woocommerce' ),
		'MT' => __( 'Montana', 'woocommerce' ),
		'NE' => __( 'Nebraska', 'woocommerce' ),
		'NV' => __( 'Nevada', 'woocommerce' ),
		'NH' => __( 'New Hampshire', 'woocommerce' ),
		'NJ' => __( 'New Jersey', 'woocommerce' ),
		'NM' => __( 'New Mexico', 'woocommerce' ),
		'NY' => __( 'New York', 'woocommerce' ),
		'NC' => __( 'North Carolina', 'woocommerce' ),
		'ND' => __( 'North Dakota', 'woocommerce' ),
		'OH' => __( 'Ohio', 'woocommerce' ),
		'OK' => __( 'Oklahoma', 'woocommerce' ),
		'OR' => __( 'Oregon', 'woocommerce' ),
		'PA' => __( 'Pennsylvania', 'woocommerce' ),
		'RI' => __( 'Rhode Island', 'woocommerce' ),
		'SC' => __( 'South Carolina', 'woocommerce' ),
		'SD' => __( 'South Dakota', 'woocommerce' ),
		'TN' => __( 'Tennessee', 'woocommerce' ),
		'TX' => __( 'Texas', 'woocommerce' ),
		'UT' => __( 'Utah', 'woocommerce' ),
		'VT' => __( 'Vermont', 'woocommerce' ),
		'VA' => __( 'Virginia', 'woocommerce' ),
		'WA' => __( 'Washington', 'woocommerce' ),
		'WV' => __( 'West Virginia', 'woocommerce' ),
		'WI' => __( 'Wisconsin', 'woocommerce' ),
		'WY' => __( 'Wyoming', 'woocommerce' ),
		//'AA' => __( 'Armed Forces (AA)', 'woocommerce' ),
		//'AE' => __( 'Armed Forces (AE)', 'woocommerce' ),
		//'AP' => __( 'Armed Forces (AP)', 'woocommerce' ),
		//'AS' => __( 'American Samoa', 'woocommerce' ),
		//'GU' => __( 'Guam', 'woocommerce' ),
		//'MP' => __( 'Northern Mariana Islands', 'woocommerce' ),
		//'PR' => __( 'Puerto Rico', 'woocommerce' ),
		//'UM' => __( 'US Minor Outlying Islands', 'woocommerce' ),
		//'VI' => __( 'US Virgin Islands', 'woocommerce' ),
	);

	return $states;

}

/**
 * Remove the Actions column from My Account - Orders page
 *
 * @param array|object $order Customer orders with Actions
 * @return array|object $order Customer orders without Actions
 */
add_filter( 'woocommerce_account_orders_columns', 'woocommerce_account_orders_remove_column', 10);
function woocommerce_account_orders_remove_column($order) {
    unset($order['order-actions']);
    return $order;
}

/**
 * Remove the 'Order Again' button from the Order Details page
 *
 */
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );

/**
 * Add product images to all emails where the ordered items are listed
 *
 * @param array $table Array of ordered items
 * @param array $order Order details
 * @return array Output buffer
 */
add_filter( 'woocommerce_email_order_items_table', 'add_wc_order_email_images', 10, 2 );
function add_wc_order_email_images( $table, $order ) {
  
	ob_start();
	
	$template = $plain_text ? 'emails/plain/email-order-items.php' : 'emails/email-order-items.php';
	wc_get_template( $template, array(
		'order'                 => $order,
		'items'                 => $order->get_items(),
		'show_purchase_note'    => $show_purchase_note,
		'show_image'            => true,
		'image_size'            => array( 60, 60) 
	) );
   
	return ob_get_clean();
}

/**
 * Bcc info@midcenturymaddist.com to the Processing Order, Completed Order, and Refunded emails
 *
 * @param array $headers Email header info
 * @param array $object Email being sent
 * @return array $headers Modified email header with new bcc info
 */
add_filter( 'woocommerce_email_headers', 'add_bcc_to_certain_emails', 10, 2 );
function add_bcc_to_certain_emails( $headers, $object ) {
	$add_bcc_to = array(
                'customer_processing_order',    // woocommerce processing order emails
		'customer_completed_order',	// woocommerce completed order emails
		'customer_refunded_order'	// woocommerce refunded order emails
		);

	if ( in_array( $object, $add_bcc_to ) ) {
		$headers = array( 
			$headers,
			'Bcc: info@midcenturymaddist.com' ."\r\n",
			);
	}
	return $headers;
}

/**
 * Add the customer's billing email to recipient list for Cancelled and Failed Order emails
 *
 * @param string $recipient List of recipients for the email
 * @param array $order Object array with order details
 * @return string The list of recipients with the billing email added
 */ 
add_filter( 'woocommerce_email_recipient_cancelled_order', 'wc_cancelled_order_add_customer_email', 10, 2 );
add_filter( 'woocommerce_email_recipient_failed_order', 'wc_cancelled_order_add_customer_email', 10, 2 );
function wc_cancelled_order_add_customer_email( $recipient, $order ){
    return $recipient . ',' . $order->billing_email;
}

/**
 * Hide per product shipping if local shipping exists
 *
 * Customer should not be able to select or see per product shipping if the cheaper local shipping
 * option is available on the Shopping Cart and Checkout pages
 *
 * @param array $rates Complete array of shipping rates initially available
 * @return array $rates Revised array of shipping rates
 */
add_filter( 'woocommerce_package_rates', 'hide_shipping_when_flatrate_is_available', 100 );
function hide_shipping_when_flatrate_is_available( $rates ) {
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'flat_rate' === $rate->method_id ) {
			unset( $rates['per_product'] );
			break;
		}
	}
	return array_reverse($rates);
}

/**
 * Remove the Woocommerce coupon field
 *
 * We've added our own coupon field right after the list of products purchasing in the checkout page
 * so remove the field that displays at the top of the page
 */
remove_action( 'wpmc-woocommerce_checkout_coupon_form', 'woocommerce_checkout_coupon_form', 10 );

/**
 * Add 'Ask a Question' link to the single product page summary
 *
 * Relevant id for the element is 'ask_question'
 */
add_action( 'woocommerce_single_product_summary', 'ask_a_question_form', 30 );
function ask_a_question_form() {
    ?>
	<p><a class="ask_question" href="">Ask a Question</a></p>
    <?php
}

/**
 * Display sold products as out of stock
 *
 * @param array $availability Array with label for a product's availability
 * @param array $_product Product data object with info on wether in stock or not
 */
add_filter( 'woocommerce_get_availability', 'maddist_get_availability', 1, 2);
function maddist_get_availability( $availability, $_product ) {

    if ( !$_product->is_in_stock() ) {
        $availability['availability'] = __('Out of Stock', 'woocommerce');
    }
    return $availability;
}

/**
 * Discount logic for Small Business Saturday Sale
 *
 * Code is 'local' and discount for the product is dependant on the category
 * Sale category takes an additional 10% off
 * Storage, Seating, and Tables categories take 15% off
 * Accessories and Lighting categories take 20% off
 *
 * @return float $round Total discount amount
 */
add_filter( 'woocommerce_coupon_get_discount_amount', 'alter_shop_coupon_data', 20, 5 );
function alter_shop_coupon_data( $round, $discounting_amount, $cart_item, $single, $coupon ){

    // Related coupons codes to be defined in this array 
    $coupon_codes = array('local');

    // Product categories
    $product_category10 = array('sale'); // for 10% discount
    $product_category15 = array('storage', 'seating', 'tables'); // for 15% discount
    $product_category20 = array('accessories', 'lighting'); // for 20% discount

    if ( $coupon->is_type('percent') && in_array( $coupon->get_code(), $coupon_codes ) ) {
        if ( has_term( $product_category10, 'product_cat', $cart_item['product_id'] ) ){
            $discount = (float) $coupon->get_amount() * ( 1 * $discounting_amount / 100 );
            $round = round( min( $discount, $discounting_amount ), wc_get_rounding_precision() );
        } elseif ( has_term( $product_category15, 'product_cat', $cart_item['product_id'] ) ){
            $discount = (float) $coupon->get_amount() * ( 1.5 * $discounting_amount / 100 );
            $round = round( min( $discount, $discounting_amount ), wc_get_rounding_precision() );
        } elseif ( has_term( $product_category20, 'product_cat', $cart_item['product_id'] ) ){
            $discount = (float) $coupon->get_amount() * ( 2 * $discounting_amount / 100 );
            $round = round( min( $discount, $discounting_amount ), wc_get_rounding_precision() );
        }
    }

    return $round;
}

/**
 * Check if the current user is allowed to use coupon
 *
 * Fix for WooCommerce coupon code email restriction/whitelist
 *
 * @param bool $result True but can be overriden
 * @param array $coupon Coupon code data object
 * @return bool True if current user is in whitelisted email array
 */
//add_filter( 'woocommerce_coupon_is_valid', 'maddist_coupon_is_valid', 10, 2 );
if ( ! function_exists( 'maddist_coupon_is_valid' ) ) {

        function maddist_coupon_is_valid( $result, $coupon ) {
                $user = wp_get_current_user();

                $restricted_emails = $coupon->get_email_restrictions();

                return ( in_array( $user->user_email, $restricted_emails ) ? $result : false );
        }
}

/**
 * Remove the generated product schema markup from Product Category and Shop pages.
 *
 * Fix for Google Search console errors
 */
add_action( 'woocommerce_init', 'wc_remove_product_schema_product_archive' );
function wc_remove_product_schema_product_archive() {
        remove_action( 'woocommerce_shop_loop', array( WC()->structured_data, 'generate_product_data' ), 10, 0 );
}

/**
 * Remove the demo store banner from the site
 *
 * This is an overridden core function
 * @return null
 */
if ( ! function_exists( 'woocommerce_demo_store' ) ) {
        function woocommerce_demo_store() {
                if ( ! is_store_notice_showing() ) {
                        return;
                }

                $notice = get_option( 'woocommerce_demo_store_notice' );

                if ( empty( $notice ) ) {
                        $notice = __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'woocommerce' );
                }

                echo apply_filters( 'woocommerce_demo_store', '<p class="woocommerce-store-notice demo_store">' . wp_kses_post( $notice ) . ' <a href="#" class="woocommerce-store-notice__dismiss-link">' . esc_html__( 'Accept', 'woocommerce' ) . '</a></p>', $notice ); // WPCS: XSS ok.
        }
}

/* on product updates check to make sure the stock is managed, inventory count, and hold status is correct */
/**
 * Admin Backend: Cleanup the product settings when an edit is made in the product view page
 *
 * The 'Manage Stock' checkbox should always be checked
 * If the product is in stock then inventory should be set to 1, otherwise 0
 * Make sure the 'On Hold' button is unchecked if the product is out of stock or inventory is 0
 *
 * @param int $post_id The id of the product being edited
 */
add_action( 'woocommerce_process_product_meta_simple', 'manage_stock_default');
function manage_stock_default( $post_id ) {
    if ( empty($_POST['_manage_stock']) ) {
      update_post_meta($post_id, '_manage_stock', 'yes');
      if ( $_POST['_stock_status'] == 'instock' ) {
        update_post_meta($post_id, '_stock', '1');
      }
      if ( $_POST['_stock_status'] == 'outofstock' ) {
        update_post_meta($post_id, '_stock', '0');
      }
    }

    //if inventory count is 0 or out of stock for an item, uncheck the on hold button if it is checked
    if ( (($_POST['_stock'] == '0') || ($_POST['_stock_status'] == 'outofstock')) && ($_POST['_on_hold'] == 'yes') ) {
      update_post_meta($post_id, '_on_hold', null);
    }
}

/**
 * Admin Backend: Add a 'Place item on hold' checkbox to the edit product view
 *
 */
add_action( 'woocommerce_product_options_inventory_product_data', 'maddist_add_on_hold' );
function maddist_add_on_hold() {

    woocommerce_wp_checkbox(
        array(
            'id'            => '_on_hold',
            'wrapper_class' => 'show_if_simple show_if_variable',
            'label'         => __( 'Place item on hold', 'woocommerce' ),
            'description'   => __( 'Enable this to replace the add to cart button with a "On Hold" message on the product page', 'woocommerce' ),
        )
    );

}

/**
 * Admin Backend: Save the contents of the 'On Hold' checkbox if it is checked
 *
 * @param int $post_id Id of the product being edited
 */
add_action( 'woocommerce_process_product_meta', 'save_on_hold_field' );
function save_on_hold_field( $post_id ) {
  
    $on_hold_field_value = isset( $_POST['_on_hold'] ) ? $_POST['_on_hold'] : '';
  
    $product = wc_get_product( $post_id );
    $product->update_meta_data( '_on_hold', $on_hold_field_value );
    $product->save();

}

/**
 * Admin Backend: Add product to Sale category if Sale price entered
 *
 * Before publishing a product in the edit product view, if there is a Sale price > 0
 * then add the product to the Sale category
 * 
 * @param int $post_id The id of the product being edited
 */
add_action( 'save_post', 'update_product_set_sale_category', 1, 2);
function update_product_set_sale_category( $post_id ) {

    $post = get_post( $post_id );

    if($post->post_type == 'product' && $post->post_status == 'publish') {
        $sale_price = get_post_meta($post_id, '_sale_price', true);
        if(!empty($sale_price) || $sale_price > 0)
            wp_set_object_terms($post_id, 'sale', 'product_cat', true );
    }

}

/**
 * Admin Backend: Remove unused columns and add custom columns to product grid
 *
 * Unused columns to remove: Sku
 * New custom columns to add: On Hold, Shipping Price
 * 
 * @param array $columns The columns array for the product grid
 */
add_filter( 'manage_edit-product_columns', 'add_product_custom_columns', 4 );
function add_product_custom_columns( $columns ){

   //remove columns
   unset( $columns['sku'] );

   //add custom columns
    return array_slice( $columns, 0, 4, true )
        + array( 'on_hold' => 'On Hold' )
        + array( 'shipping_per_product' => 'Shipping Price' )
        + array_slice( $columns, 4, NULL, true );

}

/**
 * Admin Backend: Add product values to the custom columns in the product grid
 *
 * For the 'On Hold' column, values are 'Yes' and an empty string if not on hold
 * For the 'Shipping Price' column, values are the per product shipping price manually entered
 * 
 * @param string $column The column id 
 * @param int $productid The product id
 */
add_action( 'manage_product_posts_custom_column', 'populate_product_custom_columns', 4, 2 );
function populate_product_custom_columns( $column, $productid ) {

    global $wpdb;

    switch ( $column ) {
        case 'on_hold':
            if ( get_post_meta($productid, '_on_hold', true) == 'yes' ) {
                echo 'Yes';
            }
            break;
        case 'shipping_per_product':
            $rules = $wpdb->get_results( $wpdb->prepare( "SELECT rule_cost, rule_item_cost FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules WHERE product_id = %d ORDER BY rule_order;", $productid ) );
            foreach ( $rules as $rule ) {
                if ( $rule->rule_cost > 0 ) {
                    echo '$' . $rule->rule_cost . ' (line)<br>';
                }
                if ( $rule->rule_item_cost > 0 ) {
                    echo '$' . $rule->rule_item_cost . '<br>';
                }
            }
            break;
    }

}

/**
 * Admin Backend: Make 'On Hold' column selectable for filtering and sorting
 *
 * @param array $columns Array of columns that are filterable and sortable
 * @return array Array with 'On Hold' column added
 */
add_filter( 'manage_edit-product_sortable_columns', 'maddist_sortable_product_column' );
function maddist_sortable_product_column( $columns ) {
    return wp_parse_args( array( 'on_hold' => '_on_hold' ), $columns );
}

/**
 * Admin Backend: Filter and sort the products by the 'On Hold' column in the product grid
 *
 * Only products that are on hold will show
 * 
 * @param array $query The current query on the product grid
 * @return array $query The new query request on the product grid
 */
add_action( 'pre_get_posts', 'maddist_do_sort_product_column' );
function maddist_do_sort_product_column( $query ) {
    if( !is_admin() || empty( $_GET['orderby']) || empty( $_GET['order'] ) )
        return;
 
    if( $_GET['orderby'] == '_on_hold' ) {
        $query->set('meta_key', '_on_hold' );
        $query->set('orderby', 'meta_value');
        $query->set('order', $_GET['order'] );
        $query->set('meta_query', array(
            array(
                'key'       => '_on_hold',
                'value'     => 'yes'
            )
        ));
    }
 
    return $query;
}

/**
 * Admin Backend: Add the 'On Hold' checkbox in the Quick Edit view
 *
 */
add_action( 'woocommerce_product_quick_edit_end', function(){
    ?>
    <div class="inline-edit-group on_hold_field">
        <label class="on_hold">
            <input type="checkbox" name="_on_hold" value="">
	    <span class="checkbox-title">Place item on hold</span>
        </label>
    </div>
    <?php

} );

/**
 * Admin Backend: Saves the 'On Hold' value in the Quick Edit view
 *
 * The 'On Hold' valus is unset if the product's stock Qty is 0
 *
 * @param array $product The product data object
 */
add_action('woocommerce_product_quick_edit_save', function($product){

    if ( $product->is_type('simple') ) {
        $post_id = $product->id;

            if ( isset( $_REQUEST['_on_hold'] ) ) {
                $onHold = 'yes';
            } else {
                $onHold = ( trim( esc_attr( $_REQUEST['_on_hold'] ) ) ? 'yes' : null ) ;
            }

            if ( $_REQUEST['_stock'] == 0 ) {
                $onHold = null;
            }

            update_post_meta( $post_id, '_on_hold', $onHold );
    }

}, 10, 1);

/**
 * Admin Backend: Insert hidden value HTML element for 'On Hold' attribute column in product grid
 *
 * @param string $column The column id of the admin product grid
 * @param int $post_id The post id of the product
 */
add_action( 'manage_product_posts_custom_column', function( $column, $post_id ) {

    switch ( $column ) {
        case 'name' :
            ?>
            <div class="hidden on_hold_inline" id="on_hold_inline_<?php echo $post_id; ?>">
                <div id="_on_hold"><?php echo get_post_meta( $post_id, '_on_hold', true ); ?></div>
            </div>
            <?php
            break;

        default :
            break;
    }

}, 99, 2);

